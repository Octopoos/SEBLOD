<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: _debug.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Init
jimport( 'joomla.error.profiler' );
$db			=	JFactory::getDbo();
$config		=	JCckDev::init();
$cck		=	JCckDev::preload( array( '' ) );
$doc		=	JFactory::getDocument();
$profiler	=	new JProfiler();

// -- DEBUG:START -- //
$js			=	'';
$js2		=	'';
$js_script	=	'';
$js_script	=	JURI::root( true ).$js_script;
echo '<br /><div style="color: #999999; font-size: 10px; font-weight: bold;">-------- Debug --------<br />';
// -------- -------- -------- -------- -------- -------- -------- -------- //

$n				=	10000;
$profiler		=	new JProfiler();

for ( $i = 0; $i < $n; $i++ ) {
	//
}
$profiler_res	=	$profiler->mark( 'afterDebug' );

// -------- -------- -------- -------- -------- -------- -------- -------- //
if ( $js_script ) {
	$doc->addScript( $js_script );
}
if ( $js || $js2 ) {
	echo '<input text id="toto" name="toto" value="" />'.'<input text id="toto2" name="toto2" value="" />';
	$doc->addScriptDeclaration( 'jQuery(document).ready(function($) {' . $js . '});' . $js2 );
}
echo '<br />'. $profiler_res . '<br />-------- Debug --------<br /></div>';
// -- DEBUG:END -- //

/*
$log	=	JLog::addLogger( array( 'format'=>'{DATE}\t{TIME}\t{LEVEL}\t{CODE}\t{MESSAGE}', 'text_file'=>'com_cck.php' ) );
*/