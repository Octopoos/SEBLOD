<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: default_filter.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
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
?>

<div class="<?php echo $this->css['filter']; ?>" id="filter-bar">
	<div class="<?php echo $this->css['filter_search']; ?>">
        <?php
		echo JCckDev::getForm( $cck['core_location_filter'], $this->state->get( 'filter.location' ), $config );
		echo JCckDev::getForm( $cck['core_filter_input'], $this->escape( $this->state->get( 'filter.search' ) ), $config, array( 'attributes'=>'placeholder="'.JText::_( 'COM_CCK_ITEMS_SEARCH_FILTER' ).'" style="text-align:center;"' ), array( 'after'=>"\n" ) );
		echo JCckDev::getForm( $cck['core_filter_go'], '', $config, array( 'css'=>$this->css['filter_search_button'] ), array( 'after'=>"\n" ) );
		echo JCckDev::getForm( $cck['core_filter_search'], '', $config, array( 'css'=>$this->css['filter_search_button'], 'attributes'=>'onclick="'.$clear.'this.form.submit();"' ), array( 'after'=>"\n" ) );
		echo JCckDev::getForm( $cck['core_filter_clear'], '', $config, array( 'css'=>$this->css['filter_search_button'], 'attributes'=>'onclick="document.getElementById(\'filter_search\').value=\'\';document.getElementById(\'filter_location\').value=\'title\';'.$clear.'this.form.submit();"' ) );
		?>
	</div>
	<div class="<?php echo $this->css['filter_search_list']; ?>">
		<label for="limit" class="element-invisible"><?php echo JText::_( 'JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC' ); ?></label>
		<select name="sortTable" id="sortTable" class="inputbox select input-medium" onchange="Joomla.orderTable()">
			<option value=""><?php echo JText::_( 'JGLOBAL_SORT_BY' );?></option>
			<?php echo JHtml::_( 'select.options', $this->getSortFields(), 'value', 'text', $listOrder ); ?>
		</select>
		<select name="directionTable" id="directionTable" class="inputbox select input-medium" onchange="Joomla.orderTable()">
			<option value="asc"<?php echo $selected_asc; ?>><?php echo JText::_( 'JGLOBAL_ORDER_ASCENDING' ); ?></option>
			<option value="desc"<?php echo $selected_desc; ?>><?php echo JText::_( 'JGLOBAL_ORDER_DESCENDING' );  ?></option>
		</select>
		<?php echo $this->pagination->getLimitBox(); ?>
	</div>
	<div class="<?php echo $this->css['filter_select']; ?>">
        <?php
        echo $this->html['filter_select_header'];
		echo JCckDev::getForm( $cck['core_depth_filter'], $this->state->get( 'filter.depth' ), $config, array( 'css'=>'small span12' ) );
		echo $this->html['filter_select_separator'];
		echo JCckDev::getForm( $cck['core_folder_filter'], $this->state->get( 'filter.folder' ), $config, array( 'css'=>'small span12' ) );
		echo $this->html['filter_select_separator'];
		echo JCckDev::getForm( $cck['core_state_filter'], $this->state->get( 'filter.state' ), $config, array( 'css'=>'small span12' ) );
		echo $this->html['filter_select_separator'];
        ?>
	</div>
</div>