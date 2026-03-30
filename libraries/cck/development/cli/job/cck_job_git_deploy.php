#!/usr/bin/env php
<?php
use Joomla\Registry\Registry;

require_once __DIR__.'/cck_job.php';

// Cli
class CckJobCli_git_deploy extends CckJobCli
{
	// doExecute
	public function doExecute()
	{
		$event	=	'onCckGitDeploy';

		if ( JCckToolbox::getConfig()->get( 'processing', 0 ) ) {
			$processing =	JCckDatabaseCache::loadObjectListArray( 'SELECT type, scriptfile, options FROM #__cck_more_processings WHERE published = 1 AND type IN ("'.$event.'") ORDER BY ordering', 'type' );

			if ( isset( $processing[$event] ) ) {
				foreach ( $processing[$event] as $p ) {
					if ( is_file( JPATH_SITE.$p->scriptfile ) ) {
						$options	=	new Registry( $p->options );

						include_once JPATH_SITE.$p->scriptfile;
					}	
				}
			}
		}
	}
}

$classname	=	'CckJobCli_git_deploy';
$job		=	new $classname;
$job->doExecute();
?>