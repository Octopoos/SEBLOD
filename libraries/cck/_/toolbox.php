<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: toolbox.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

// JCckToolbox
abstract class JCckToolbox
{
	public static $_me		=	'cck_toolbox';
	public static $_config	=	null;
	public static $_urls	=	array();
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Config

	// getConfig
	public static function getConfig()
	{
		if ( ! self::$_config ) {
			if ( JCckDatabaseCache::loadResult( 'SELECT extension_id FROM #__extensions WHERE type = "component" AND element = "'.'com_'.self::$_me.'"' ) > 0 ) {
				self::$_config	=	ComponentHelper::getParams( 'com_'.self::$_me );

				if ( self::$_config->get( 'processing' ) != '0' ) {
					self::$_config->set( 'processing', 1 );
				}
			} else {
				self::$_config	=	new Registry;
				self::$_config->set( 'KO', true );
				self::$_config->set( 'processing', 1 );
			}
		}
		
		return self::$_config;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Head

	// setHead
	public static function setHead( &$head )
	{
		$app	=	Factory::getApplication();
		$doc	=	Factory::getDocument();
		
		if ( isset( $app->cck_document ) ) {
			if ( isset( $app->cck_document['styleSheets'] ) && count( $app->cck_document['styleSheets'] ) ) {
				foreach ( $app->cck_document['styleSheets'] as $k=>$v ) {
					$head['styleSheets'][$k]	=	$v;
					unset( $app->cck_document['styleSheets'][$k] );
				}
				$doc->setHeadData( array( 'styleSheets'=>$head['styleSheets'] ) );
			}
			if ( isset( $app->cck_document['scripts'] ) && count( $app->cck_document['scripts'] ) ) {
				foreach ( $app->cck_document['scripts'] as $k=>$v ) {
					$head['scripts'][$k]		=	$v;
					unset( $app->cck_document['scripts'][$k] );

					$start	=	strpos( $k, '//' );
					
					$k		=	substr( $k, $start );
					$end	=	strpos( $k, '?' );
					$k		=	substr( $k, 0, $end+1 );

					self::$_urls[$k]			=	true;
				}
				$doc->setHeadData( array( 'scripts'=>$head['scripts'] ) );
			}
		}
	}

	// setHeadAfterRender
	public static function setHeadAfterRender()
	{
		$app	=	Factory::getApplication();

		if ( isset( $app->cck_document ) ) {
			$countCss	=	0;
			$countJs	=	0;

			if ( isset( $app->cck_document['styleSheets'] ) ) {
				$countCss	=	count( $app->cck_document['styleSheets'] );
			}
			if ( isset( $app->cck_document['scripts'] ) ) {
				$countJs	=	count( $app->cck_document['scripts'] );
			}

			if ( $countCss || $countJs ) {
				$body	=	$app->getBody();

				if ( $countCss ) {
					foreach ( $app->cck_document['styleSheets'] as $k=>$v ) {
						$html	=	'<link rel="stylesheet" href="'.$k.'" />';
						$body	=	str_replace( '</head>', $html.'</head>', $body );

						unset( $app->cck_document['styleSheets'][$k] );
					}
				}
				if ( $countJs ) {
					$i	=	0;

					foreach ( $app->cck_document['scripts'] as $k=>$v ) {
						$k2	=	JCckDev::getMergedScript( $k );
						
						if ( $k2 == '' ) {
							continue;
						}
						$replace	=	'';

						if ( count( self::$_urls ) ) {
							foreach ( self::$_urls as $k=>$v ) {
								$match	=	'';
								$search	=	'#<script(.*)src="(.*)'.addslashes( $k ).'(.*)"(.*)></script>#';
								
								preg_match( $search, $body, $match );

								if ( count( $match ) && $match[0] != '' ) {
									$replace	=	$match[0];
								}
							}
						}
						$html		=	'<script src="'.$k2.'"';

						if ( $v['async'] ) {
							$html	.=	' async="true"';
						}
						if ( $v['defer'] ) {
							$html	.=	' defer="true"';
						}
						$html		.=	'></script>';

						if ( $replace != '' ) {
							$body	=	str_replace( $replace, $html, $body );
						} else {
							$cck_js	=	'<script src="'.Uri::base( true ).'/media/cck/js/cck.core-3';

							if ( strpos( $body, $cck_js ) !== false ) {
								$search		=	$cck_js;
								$replace	=	$html."\n\t".$cck_js;
							} else {
								$search		=	'</head>';
								$replace	=	$html.'</head>';
							}
							$body	=	str_replace( $search, $replace, $body );
						}
						$i++;

						unset( $app->cck_document['scripts'][$k] );
					}
				}

				$app->setBody( $body );
			}
		}
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Jobs
	
	// run
	public static function run( $name )
	{
		$host	=	'';
		$job	=	JCckDatabase::loadObject( 'SELECT id, run_as, run_url, run_url_custom FROM #__cck_more_jobs WHERE name = "'.$name.'" AND published = 1' );

		if ( !is_object( $job ) ) {
			return;
		}
		if ( $job->run_url != '' ) {
			if ( $job->run_url == -1 ) {
				$url	=	$job->run_url_custom;
			} else {
				$url	=	JCckDatabase::loadResult( 'SELECT name FROM #__cck_core_sites WHERE id = '.(int)$job->run_url );
			}

			$pos	=	strpos( $url, '/' );

			if ( $pos !== false ) {
				$host	=	substr( $url, 0, $pos );
				$url	=	substr( $url, $pos );
			} else {
				$host	=	$url;
				$url	=	'';
			}

			if ( $url != '' ) {
				if ( $url[0] != '/' ) {
					$url	=	'/'.$url;
				}
				$length		=	strlen( $url );

				if ( $url[$length - 1] != '/' ) {
					$url	.=	'/';
				}

				$url	.=	'index.php';

				$_SERVER['PHP_SELF']	=	$url;
				$_SERVER['REQUEST_URI']	=	$url;
				$_SERVER['SCRIPT_NAME']	=	$url;
			}
		}
		
		// Fool the system into thinking we are running as JSite, from the front-end.
		$_SERVER['HTTP_HOST']	=	$host;
		$_SERVER['HTTPS']	=	'on';
		
		Factory::getApplication( 'site' );
		
		// Force identity
		if ( !$job->run_as ) {
			$job->run_as	=	(int)JCck::getConfig_Param( 'integration_user_default_author' );
		}

		if ( $job->run_as ) {
			$previous	=	Factory::getUser()->id;

			Factory::getSession()->set( 'user', Factory::getUser( $job->run_as ) );
			JCck::getUser( array( $job->run_as, true ) );
		}

		
		$processings	=	JCckDatabase::loadObjectList( 'SELECT a.id, a.type, a.scriptfile, a.options'
														. ' FROM #__cck_more_processings AS a'
														. ' LEFT JOIN #__cck_more_job_processing AS b ON b.processing_id = a.id'
														. ' LEFT JOIN #__cck_more_jobs AS c ON c.id = b.job_id'
														. ' WHERE c.name = "'.$name.'" AND c.published = 1 AND a.published = 1'
														. ' ORDER BY b.id' );

		$count	=	count( $processings );
		$now	=	Factory::getDate()->toSql();

		if ( $count ) {
			foreach ( $processings as $p ) {
				if ( is_object( $p ) && is_file( JPATH_SITE.$p->scriptfile ) ) {
					$options	=	new Registry( $p->options );

					try {
						include_once JPATH_SITE.$p->scriptfile;

					} catch ( Exception $e ) {
						// Do Nothing
					}
				}
			}
		}

		if ( $job->run_as ) {
			Factory::getSession()->set( 'user', $previous );
			JCck::getUser( array( $previous, true ) );
		}
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Processings
	
	// process
	public static function process( $event, $config = array() )
	{
		$legacy		=	1;	/* TODO#SEBLOD4 */
		$processing	=	JCckDatabaseCache::loadObjectListArray( 'SELECT type, scriptfile, options FROM #__cck_more_processings WHERE published = 1 ORDER BY ordering', 'type' );

		if ( isset( $processing[$event] ) ) {
			foreach ( $processing[$event] as $p ) {

				if ( $legacy ) {
					if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
						$options	=	new Registry( $p->options );

						include JPATH_SITE.$p->scriptfile;
					}
				} else {
					$process	=	new JCckProcessing( $event, JPATH_SITE.$p->scriptfile, $p->options );

					call_user_func_array( array( $process, 'execute' ), array( &$config ) ); /* ! &$fields */
				}
			}
		}
	}

	// processById
	public static function processById( $id = 0 )
	{
		$processing	=	JCckDatabase::loadObject( 'SELECT type, scriptfile, options FROM #__cck_more_processings WHERE published = 1 AND id = '.(int)$id );

		if ( is_object( $processing ) && is_file( JPATH_SITE.$processing->scriptfile ) ) {
			$options	=	new Registry( $processing->options );

			include_once JPATH_SITE.$processing->scriptfile;
		}
	}
}
?>