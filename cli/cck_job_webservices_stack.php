<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

require_once __DIR__ . '/cck_job.php';

// Cli
class CckJobCli_webservices_stack extends CckJobCli
{
	// doExecute
	public function doExecute()
	{
		JCckWebservice::run();
	}
}

JApplicationCli::getInstance( 'CckJobCli_webservices_stack' )->execute();
?>