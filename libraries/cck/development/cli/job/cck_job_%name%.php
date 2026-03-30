#!/usr/bin/env php
<?php
require_once __DIR__.'/cck_job.php';

// Cli
class CckJobCli_%name% extends CckJobCli
{
}

$classname	=	'CckJobCli_%name%';
$job		=	new $classname;
$job->doExecute();
?>