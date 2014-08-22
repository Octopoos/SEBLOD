<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: default_status.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

if ( JCck::on() ) {
	$class	=	'';
	$href	=	'#';
	if ( !$app->input->getBool( 'hidemainmenu' ) ) {
		$layout	=	JPATH_ADMINISTRATOR.'/components/com_cck/views/form/tmpl/modal_'.$modal_layout.'.php';
		JCckDevIntegration::appendModal( $layout, 'collapseModal3', '.cck-quickadd > button', array( 'quickadd'=>1 ) );
		$class	=	' btn-success';
	}
	?>
	<div class="btn-group cck-quickadd">
		<button href="<?php echo $href; ?>" class="btn btn-small<?php echo $class; ?>" data-toggle="modal" data-target="#collapseModal3">
			<i class="icon-plus"></i>
			<?php echo $label; ?>
		</button>
	</div>
<?php
} else {
	$css	=	'span.icon-16-newarticle{background-attachment:scroll;background-clip:border-box;background-color:transparent;'
			.	'background-origin:padding-box;background-position: 3px 3px;background-repeat:no-repeat;background-size:auto;'
			.	'padding-left:23px!important;}';
	if ( !$app->input->getBool( 'hidemainmenu' ) ) {
		$elem	=	'span.cck-quickadd a';
		$style	=	'';
	} else {
		$href	=	'#';
		$style	=	'color:#808080; text-decoration:none;';
	}
	?>
	<span class="icon-16-newarticle cck-quickadd">
		<a href="<?php echo $href; ?>" target="_self" style="<?php echo $style; ?>;">
			<?php echo $label; ?>
		</a>
	</span>
<?php } ?>