<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: default_menu.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

if ( !$app->input->getBool( 'hidemainmenu' ) ) {
	$href	=	'#';
	$layout	=	JPATH_ADMINISTRATOR.'/components/com_cck/views/form/tmpl/modal_'.$modal_layout.'.php';
	JCckDevIntegration::appendModal( $layout, 'collapseModal3', '.cck-quickadd > button', array( 'quickadd'=>1 ) );
	?>
	<ul id="menu<?php echo $module->id; ?>" class="nav">
        <li class="cck-quickadd">
            <a href="<?php echo $href; ?>" data-toggle="modal" data-target="#collapseModal3">
            	<?php echo $label; ?>
            </a>
        </li>
	</ul>
<?php } ?>