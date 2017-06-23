<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: view.raw.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

ini_set( 'memory_limit', '512M' );
require_once JPATH_ADMINISTRATOR.'/components/'.CCK_COM.'/helpers/helper_workshop.php';

// View
class CCKViewSearch extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;
	protected $p		=	1;
	protected $vName	=	'search';
	protected $vTitle	=	_C4_TEXT;
	
	// display
	function display( $tpl = null )
	{
		switch ( $this->getlayout() ) {
			case 'delete':
				$this->prepareDelete();
				break;
			case 'edit':
			case 'error':
				$this->prepareDisplay();
				break;
			case 'edit2':
				$this->prepareDisplay();
				$this->prepareDisplay_Ajax();
				break;
			case 'edit3':
				$this->prepareDisplay();
				$this->prepareDisplay_Ajax2( true );
				break;
			case 'edit4':
				$this->prepareDisplay();
				$this->prepareDisplay_Ajax2( false );
				break;
			default:
				break;
		}
		
		$this->css	=	array( '_'=>'',
							   'panel_height'=>'80px',
							   'w30'=>'span4',
							   'w70'=>'span8',
							   'wrapper'=>'container',
							   'wrapper2'=>'row-fluid',
							   'wrapper_tmpl'=>'span'
						);
		$this->js	=	array( '_'=>'',
							   'tooltip'=>'$(".hasTooltip").tooltip({});'
						);

		$this->uix	=	'full';
		
		parent::display( $tpl );
	}
	
	// prepareDelete
	function prepareDelete()
	{		
		Helper_Admin::addToolbarDelete( $this->vName, 'COM_CCK_'.$this->vTitle );
	}
	
	// prepareDisplay
	function prepareDisplay()
	{
		$app			=	JFactory::getApplication();
		$this->form		=	$this->get( 'Form' );
		$this->item		=	$this->get( 'Item' );
		$this->option	=	$app->input->get( 'option', '' );
		$this->state	=	$this->get( 'State' );
		
		// Check Errors
		if ( count( $errors	= $this->get( 'Errors' ) ) ) {
			throw new Exception( implode( "\n", $errors ), 500 );
		}
		
		$this->item->cck_type	=	$this->state->get( 'content_type', '' );
		$this->item->skip		=	$this->state->get( 'skip' );
		if ( @$this->item->id > 0 ) {
			$this->isNew		=	0;
			$this->panel_class	=	'closed';
			$this->panel_style	=	'display:none; ';
			$name				=	$this->item->name;
		} else {
			$this->isNew		=	1;
			$this->panel_class	=	'open';
			$this->panel_style	=	'';
			$name				=	'';
		}
		$this->item->folder		=	Helper_Admin::getSelected( $this->vName, 'folder', $this->item->folder, 1 );
		$this->item->published	=	Helper_Admin::getSelected( $this->vName, 'state', $this->item->published, 1 );
		if ( $this->item->skip != '' ) {
			$this->item->client	=	$this->item->skip;
			$this->item->master	=	( $this->item->client == 'list' || $this->item->client == 'item' ) ? 'content' : ( ( $this->item->client == 'order' ) ? 'order' : 'search' );
			$this->item->layer	=	$app->input->getString( 'layer', 'fields' );
			$P					=	'template_'.$this->item->client;
			$force_template		=	( $this->item->client == 'list' ) ? $this->state->get( 'tpl.list' ) : Helper_Workshop::getDefaultTemplate();
		} else {
			$this->item->client		=	( $this->isNew ) ? 'search' : $app->input->getString( 'client', $app->input->cookie->getString( 'cck_search'.$name.'_client', 'search' ) );
			$this->item->master		=	( $this->item->client == 'list' || $this->item->client == 'item' ) ? 'content' : ( ( $this->item->client == 'order' ) ? 'order' : 'search' );
			$this->item->layer		=	$app->input->getString( 'layer', 'fields' );
			$P						=	'template_'.$this->item->client;
			$force_template			=	( $this->item->client == 'list' ) ? '' : Helper_Workshop::getDefaultTemplate();
		}
		$this->style			=	( $this->item->client != 'order' ) ? Helper_Workshop::getTemplateStyle( $this->vName, $this->item->$P, $this->state->get( 'tpl.'.$this->item->client, $force_template ) ) : '';
		$this->item->template	=	( isset( $this->style->template ) ) ? $this->style->template : '';
		
		Helper_Admin::addToolbarEdit( $this->vName, 'COM_CCK_'._C4_TEXT, array( 'isNew'=>$this->isNew, 'folder'=>$this->state->get( 'filter.folder' ), 'checked_out'=>$this->item->checked_out ), array( 'template' => $this->item->template ) );
	}
	
	// prepareDisplay_Ajax
	function prepareDisplay_Ajax()
	{
		$folder		=	( $this->item->id > 0 ) ? $this->item->folder : 1;

		// Fields
		if ( $this->item->cck_type != '' && !$this->item->skip ) {
			$pos								=	isset( $this->style->positions[0]->value ) ? $this->style->positions[0]->value : 'mainbody';
			$this->fields						=	Helper_Workshop::getFields( 'search', $this->item, 'a.name = "cck"', false, false, $pos );
			$this->fields[$pos][0]->variation	=	'hidden';
			$this->fields[$pos][0]->match_mode	=	'exact';
			$this->fields[$pos][0]->live_value	=	$this->item->cck_type;
			$this->fieldsAv		=	Helper_Workshop::getFieldsAv( 'search', $this->item, '', 'a.name != "cck" AND a.folder = '.(int)$folder );
		} else {
			$this->fields		=	Helper_Workshop::getFields( 'search', $this->item );
			$this->fieldsAv		=	Helper_Workshop::getFieldsAv( 'search', $this->item, '', 'a.folder = '.(int)$folder );
		}
		$this->type_fields		=	JCckDatabase::loadObjectList( 'SELECT fieldid, GROUP_CONCAT(DISTINCT typeid separator " c-") AS cc FROM #__cck_core_type_field group by fieldid', 'fieldid' );
		
		// Positions
		$positions				=	Helper_Workshop::getPositions( 'search', $this->item );
		if ( is_object( $this->style ) && count( $this->style->positions ) ) {
			$this->positions	=	array();
			foreach ( $this->style->positions as $p ) {
				if ( $p->value ) {
					$this->positions[$p->value]						=	new stdClass;
					$this->positions[$p->value]->title				=	$p->text;
					$this->positions[$p->value]->name				=	$p->value;
					$this->positions[$p->value]->disable			=	false;
					$this->positions[$p->value]->legend				=	@$positions[$p->value]->legend;
					$this->positions[$p->value]->variation			=	@$positions[$p->value]->variation;
					$this->positions[$p->value]->variation_options	=	@$positions[$p->value]->variation_options;
					$this->positions[$p->value]->width				=	@$positions[$p->value]->width;
					$this->positions[$p->value]->height				=	@$positions[$p->value]->height;
					$this->positions[$p->value]->css				=	@$positions[$p->value]->css;
				}
			}
		} else {
			$this->positions	=	array( 'mainbody' => (object)array( 'title'=>'(mainbody)', 'name'=>'mainbody', 'disable'=>false, 'legend'=>'',
																		'variation'=>'', 'variation_options'=>'', 'width'=>'', 'height'=>'' ) );
		}
		$this->positions_nb		=	count( $this->positions );
		$this->variations		=	Helper_Workshop::getPositionVariations( $this->item->template );
		
		// Filters
		$options				=	Helper_Admin::getPluginOptions( 'field', 'cck_', true, false, true );
		$this->lists['af_t']	=	JHtml::_( 'select.genericlist', $options, 'filter_type', 'class="inputbox filter input-medium" prefix="t-"', 'value', 'text', '', 'filter1' );
		$options				=	Helper_Admin::getAlphaOptions( true );
		$this->lists['af_a']	=	JHtml::_( 'select.genericlist', $options, 'filter_alpha', 'class="inputbox filter input-medium" prefix="a-"', 'value', 'text', '', 'filter3' );
		$options				=	Helper_Admin::getTypeOptions( true, false );
		$this->lists['af_c']	=	JHtml::_( 'select.genericlist', $options, 'filter_type', 'class="inputbox filter input-medium" prefix="c-"', 'value', 'text', '', 'filter4' );
		$options				=	Helper_Admin::getFolderOptions( true, true, false, true, 'field' );
		$this->lists['af_f']	=	JHtml::_( 'select.genericlist', $options, 'filter_folder', 'class="inputbox filter input-medium" prefix="f-"', 'value', 'text', $folder, 'filter2' );
	}
	
	// prepareDisplay_Ajax2
	function prepareDisplay_Ajax2( $isScoped )
	{
		$and		=	'';
		$folder		=	( $this->item->id > 0 ) ? $this->item->folder : 1;
		if ( $this->item->cck_type != '' ) {
			$this->item->storage_location	=	JCckDatabase::loadResult( 'SELECT storage_location FROM #__cck_core_types WHERE name = "'.$this->item->cck_type.'"' );
		}
		$location	=	( $this->item->storage_location == '' ) ? 'joomla_article' : $this->item->storage_location;
		
		// Fields
		if ( !$isScoped ) {
			$and	=	'(a.storage_location != "'.$location.'" AND a.storage != "none")';
		} else {
			$and	=	'(a.storage_location = "'.$location.'" OR a.storage = "none")';
		}
		$this->fieldsAv			=	Helper_Workshop::getFieldsAv( 'search', $this->item, $and, 'a.folder != '.(int)$folder );
		$this->type_fields		=	JCckDatabase::loadObjectList( 'SELECT fieldid, GROUP_CONCAT(DISTINCT typeid separator " c-") AS cc FROM #__cck_core_type_field group by fieldid', 'fieldid' );
		
		// Languages (todo: optimize)
		Helper_Admin::getPluginOptions( 'field', 'cck_', true, false, true );
		JPluginHelper::importPlugin( 'cck_field' );
	}

	// setPosition
	function setPosition( $name, $title = '' )
	{
		$title	=	( !empty( $title ) ) ? $title : $name;
		$legend	=	'<input class="thin blue" type="text" name="ffp[pos-'.$name.'][legend]" value="'.htmlspecialchars( @$this->positions[$name]->legend ).'" size="22" />';
		$variat	=	Jhtml::_( 'select.genericlist', $this->variations, 'ffp[pos-'.$name.'][variation]', 'size="1" class="thin blue c_var_ck"', 'value', 'text', @$this->positions[$name]->variation, 'pos-'.$name.'_variation' );
		$variat	.=	'<input type="hidden" id="pos-'.$name.'_variation_options" name="ffp[pos-'.$name.'][variation_options]" value="'.htmlspecialchars( @$this->positions[$name]->variation_options ).'" />';
		$width	=	'<input class="thin blue" type="text" name="ffp[pos-'.$name.'][width]" value="'.@$this->positions[$name]->width.'" size="8" style="text-align:center;" />&nbsp;×&nbsp;';
		$height	=	'<input class="thin blue" type="text" name="ffp[pos-'.$name.'][height]" value="'.@$this->positions[$name]->height.'" size="8" style="text-align:center;" />';
		$css	=	'<input class="thin blue" type="text" name="ffp[pos-'.$name.'][css]" value="'.@$this->positions[$name]->css.'" size="22" />';
		
		Helper_Workshop::displayPosition( $this->p, $name, $title, $legend, $variat, @$this->positions[$name]->variation, $width, $height, $css, array( 'template'=>$this->item->template, 'name'=>$this->item->name, 'view'=>$this->item->client ) );
		$this->p++;
		
		return $name;
	}
}
?>