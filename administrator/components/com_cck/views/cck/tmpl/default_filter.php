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

if ( JCck::on( '4.0' ) ) {
	echo '<ul class="hidden"><li class="master-filter">'
	 .	 JCckDev::getFormFromHelper( array( 'component'=>'com_cck', 'function'=>'getFolderFilterApp', 'name'=>'core_folder_filter_app' ), $this->state->get( 'filter.folder_app' ), $config, array( 'storage_field'=>'filter_folder_app' ) )
	 .	 '</li></ul>'
	 .	 '<script>var $b4 = $("#sidebar ul"); $("li.master-filter").appendTo($b4); $("ul.master-filter").remove();</script>'
	 ;
}
?>
<div class="<?php echo $this->css['filter_search']; ?>">
	<?php
	$location_filter	=	JCckDev::getFormFromHelper( array( 'component'=>'com_cck', 'function'=>'getLocationFilter', 'name'=>'core_location_filter' ), $this->state->get( 'filter.location' ), $config, array( 'storage_field'=>'filter_location' ) );

	if ( JCck::on( '4.0' ) ) {
		echo '<div class="btn-group"><div class="input-group">';
	} else {
		echo $location_filter;
	}
	echo JCckDev::getForm( $cck['core_filter_input'], $this->escape( $this->state->get( 'filter.search' ) ), $config, array( 'attributes'=>'placeholder="'.JText::_( 'COM_CCK_ITEMS_SEARCH_FILTER' ).'"' ), array( 'after'=>"\n" ) );
	echo JCckDev::getForm( $cck['core_filter_go'], '', $config, array( 'css'=>$this->css['filter_search_button'] ), array( 'after'=>"\n" ) );
	echo JCckDev::getForm( $cck['core_filter_search'], '', $config, array( 'css'=>$this->css['filter_search_button'], 'attributes'=>'onclick="'.$clear.'this.form.submit();"' ), array( 'after'=>"\n" ) );
	echo JCckDev::getForm( $cck['core_filter_clear'], '', $config, array( 'css'=>$this->css['filter_search_button'], 'attributes'=>'onclick="document.getElementById(\'filter_search\').value=\'\';document.getElementById(\'filter_location\').value=\'title\';'.$clear.'this.form.submit();"' ) );

	if ( JCck::on( '4.0' ) ) {
		echo '</div>'.$location_filter.'</div>';
	}
	?>
</div>
<div class="<?php echo $this->css['filter_search_list']; ?>">
	<?php
	if ( JCck::on( '4.0' ) ) {

		if ( isset( $status_filter ) ) {
			echo $status_filter;

			$status_filter	=	'';
		}

		echo '<div class="hidden">';
	} ?>
	<label for="limit" class="element-invisible"><?php echo JText::_( 'JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC' ); ?></label>
	<select name="sortTable" id="sortTable" class="inputbox select input-medium form-select" onchange="Joomla.orderTable()">
		<option value=""><?php echo JText::_( 'JGLOBAL_SORT_BY' );?></option>
		<?php echo JHtml::_( 'select.options', $this->getSortFields(), 'value', 'text', $listOrder ); ?>
	</select>
	<select name="directionTable" id="directionTable" class="inputbox select input-medium form-select form-select-auto" onchange="Joomla.orderTable()">
		<option value="asc"<?php echo $selected_asc; ?>><?php echo JText::_( 'JGLOBAL_ORDER_ASCENDING' ); ?></option>
		<option value="desc"<?php echo $selected_desc; ?>><?php echo JText::_( 'JGLOBAL_ORDER_DESCENDING' );  ?></option>
	</select>
	<?php
	echo $this->pagination->getLimitBox();

	if ( JCck::on( '4.0' ) ) {
		echo '</div>';
	}
	?>
</div>
<?php
if ( JCck::on( '4.0' ) ) {
	echo '<div class="filter-search2">';
}
?>