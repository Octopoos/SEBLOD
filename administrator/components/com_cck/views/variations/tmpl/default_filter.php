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

$clear	=	"";
if ( $this->js['filter'] ) {
	$doc->addScriptDeclaration( $this->js['filter'] );
}
?>

<div class="<?php echo $this->css['filter']; ?>" id="filter-bar">
	<div class="<?php echo $this->css['filter_search']; ?>">
        <?php
		echo JCckDev::getForm( $cck['core_location_filter'], $this->state->get( 'filter.location' ), $config, array( 'type'=>'select_simple', 'options'=>'Title=title||Templates=optgroup||Template Name=template_name' ) );
		echo JCckDev::getForm( $cck['core_filter_input'], $this->escape( $this->state->get( 'filter.search' ) ), $config, array( 'attributes'=>'placeholder="'.JText::_( 'COM_CCK_ITEMS_SEARCH_FILTER' ).'" style="text-align:center;"' ), array( 'after'=>"\n" ) );
		echo JCckDev::getForm( $cck['core_filter_go'], '', $config, array( 'css'=>$this->css['filter_search_button'] ), array( 'after'=>"\n" ) );
		echo JCckDev::getForm( $cck['core_filter_search'], '', $config, array( 'css'=>$this->css['filter_search_button'], 'attributes'=>'onclick="'.$clear.'this.form.submit();"' ), array( 'after'=>"\n" ) );
		echo JCckDev::getForm( $cck['core_filter_clear'], '', $config, array( 'css'=>$this->css['filter_search_button'], 'attributes'=>'onclick="document.getElementById(\'filter_search\').value=\'\';document.getElementById(\'filter_location\').value=\'title\';'.$clear.'this.form.submit();"' ) );
		?>
	</div>
	<!--
	<div class="<?php echo $this->css['filter_search_list']; ?>">
		<label for="limit" class="element-invisible"><?php echo JText::_( 'JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC' ); ?></label>
		<?php echo $this->pagination->getLimitBox(); ?>
	</div>
	-->
</div>