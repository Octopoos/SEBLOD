<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: toolbox.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckToolbox
abstract class JCckToolbox
{
	public static $_me			=	'cck_toolbox';
	public static $_config		=	NULL;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Config

	// getConfig
	public static function getConfig()
	{
		if ( ! self::$_config ) {
			if ( JCckDatabaseCache::loadResult( 'SELECT extension_id FROM #__extensions WHERE type = "component" AND element = "'.'com_'.self::$_me.'"' ) > 0 ) {
				self::$_config	=	JComponentHelper::getParams( 'com_'.self::$_me );

				if ( self::$_config->get( 'processing' ) != '0' ) {
					self::$_config->set( 'processing', 1 );
				}
			} else {
				self::$_config	=	new JRegistry;
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
		$app	=	JFactory::getApplication();
		$doc	=	JFactory::getDocument();

		if ( isset( $app->cck_document ) ) {
			if ( isset( $app->cck_document['styleSheets'] ) && count( $app->cck_document['styleSheets'] ) ) {
				foreach ( $app->cck_document['styleSheets'] as $k=>$v ) {
					$head['styleSheets'][$k]	=	$v;
				}
				if ( JCck::on() ) {
					$doc->setHeadData( array( 'styleSheets'=>$head['styleSheets'] ) );
				} else {
					$doc->_styleSheets			=	$head['styleSheets'];
				}
			}
			if ( isset( $app->cck_document['scripts'] ) && count( $app->cck_document['scripts'] ) ) {
				foreach ( $app->cck_document['scripts'] as $k=>$v ) {
					$head['scripts'][$k]		=	$v;
				}
				if ( JCck::on() ) {
					$doc->setHeadData( array( 'scripts'=>$head['scripts'] ) );
				} else {
					$doc->_scripts				=	$head['scripts'];
				}
			}
		}
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Processing
	
	// process
	public static function process( $event )
	{
		$processing	=	JCckDatabaseCache::loadObjectListArray( 'SELECT type, scriptfile, options FROM #__cck_more_processings WHERE published = 1 ORDER BY ordering', 'type' );

		if ( isset( $processing[$event] ) ) {
			foreach ( $processing[$event] as $p ) {
				if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
					$options	=	new JRegistry( $p->options );
					
					include_once JPATH_SITE.$p->scriptfile;
				}
			}
		}
	}

	// processById
	public static function processById( $id = 0 )
	{
		$processing	=	JCckDatabase::loadObject( 'SELECT type, scriptfile, options FROM #__cck_more_processings WHERE published = 1 AND id = '.(int)$id );

		if ( is_object( $processing ) && is_file( JPATH_SITE.$processing->scriptfile ) ) {
			$options	=	new JRegistry( $p->options );

			include_once JPATH_SITE.$processing->scriptfile;
		}
	}
}
?>