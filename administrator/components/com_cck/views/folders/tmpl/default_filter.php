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

$clear	=	"document.getElementById('filter_folder').value='';document.getElementById('filter_state').value='';document.getElementById('filter_depth').value='';";
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

$status_filter	=	JCckDev::getForm( $cck['core_state_filter'], $this->state->get( 'filter.state' ), $config, array( 'css'=>'span12' ) );
?>

<div class="<?php echo $this->css['filter']; ?>" id="filter-bar">
	<?php include_once dirname( __DIR__, 2 ).'/cck/tmpl/default_filter.php'; ?>
	<div class="<?php echo $this->css['filter_select']; ?>">
        <?php
        echo $this->html['filter_select_header'];
		echo JCckDev::getForm( $cck['core_depth_filter'], $this->state->get( 'filter.depth' ), $config, array( 'css'=>'span12' ) );
		echo $this->html['filter_select_separator'];
		echo $this->html['filter_select_divider'];
		echo JCckDev::getFormFromHelper( array( 'component'=>'com_cck', 'function'=>'getFolderFilter', 'name'=>'core_folder_filter' ), $this->state->get( 'filter.folder' ), $config, array( 'storage_field'=>'filter_folder' ) );
		echo $this->html['filter_select_separator'];
		echo $status_filter;
		echo $this->html['filter_select_separator'];
        ?>
	</div></div>
</div>