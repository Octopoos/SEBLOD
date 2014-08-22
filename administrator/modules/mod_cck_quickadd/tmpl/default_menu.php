<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: default_menu.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

if ( !$app->input->getBool( 'hidemainmenu' ) ) {
	if ( JCck::on() ) {
		$href	=	'#';
		$layout	=	JPATH_ADMINISTRATOR.'/components/com_cck/views/form/tmpl/modal_'.$modal_layout.'.php';
		JCckDevIntegration::appendModal( $layout, 'collapseModal3', '.cck-quickadd > button', array( 'quickadd'=>1 ) );
		?>
		<ul id="menu<?php echo $module->id; ?>" class="nav">
	        <li class="cck-quickadd">
	            <a href="<?php echo $href; ?>" data-toggle="modal" data-target="#collapseModal3">
	            	<!--<i class="icon-plus"></i>-->
	            	<?php echo $label; ?>
	            </a>
	        </li>
		</ul>
	<?php
	} else {
		$elem	=	'li.cck-quickadd a';
		$css	=	'li.cck-quickadd a{cursor:pointer;}';
		?>
		<ul class="cck_menu">
	        <li class="cck-quickadd">
	            <a class="icon-16-newarticle root-icon-position" href="<?php echo $href; ?>" target="_self" style="color:#000000; text-decoration:none;"></a>
	        </li>
		</ul>
	<?php
	}
}
?>