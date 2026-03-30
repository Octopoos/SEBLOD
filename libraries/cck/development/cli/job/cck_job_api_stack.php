#!/usr/bin/env php
<?php
require_once __DIR__.'/cck_job.php';

// Cli
class CckJobCli_api_stack extends CckJobCli
{
	// doExecute
	public function doExecute()
	{
		JCckWebservice::run();
	}
}

$job    =  new CckJobCli_api_stack;
$job->doExecute();
?>