<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: default_filter.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$clear	=	"document.getElementById('filter_folder').value='';document.getElementById('filter_state').value='1';document.getElementById('filter_type').value='';";
if ( $this->js['filter'] ) {
	$doc->addScriptDeclaration( $this->js['filter'] );
}
if ( $listDir == 'asc' ) {
	$selected_asc	=	' selected="selected"';
	$selected_desc	=	'';
} else {
	$selected_asc	=	'';
	$selected_desc	=	' selected="selected"';
}
?>

<div class="<?php echo $this->css['filter']; ?>" id="filter-bar">
	<?php include_once dirname( __DIR__, 2 ).'/cck/tmpl/default_filter.php'; ?>
	<div class="<?php echo $this->css['filter_select']; ?>">
        <?php
        echo $this->html['filter_select_header'];
        echo JCckDev::getFormFromHelper( array( 'component'=>'com_cck', 'function'=>'getClientFilter', 'name'=>'core_client_filter' ), $this->state->get( 'filter.client' ), $config, array( 'storage_field'=>'filter_client' ) );
		echo $this->html['filter_select_separator'];
		echo JCckDev::getFormFromHelper( array( 'component'=>'com_cck', 'function'=>'getStorageLocation2', 'name'=>'core_storage_location2' ), $this->state->get( 'filter.type' ), $config,
							   			 array( 'defaultvalue'=>'', 'selectlabel'=>'All Content Objects', 'storage_field'=>'filter_type', 'attributes'=>'onchange="this.form.submit()"', 'css'=>'span12' ) );
		echo $this->html['filter_select_separator'];
		echo JCckDev::getFormFromHelper( array( 'component'=>'com_cck', 'function'=>'getFolderFilter', 'name'=>'core_folder_filter' ), $this->state->get( 'filter.folder' ), $config, array( 'storage_field'=>'filter_folder' ) );
		echo $this->html['filter_select_separator'];
		echo JCckDev::getForm( $cck['core_state_filter'], $this->state->get( 'filter.state' ), $config, array( 'css'=>'span12' ) );
		echo $this->html['filter_select_separator'];
        ?>
	</div>
</div>