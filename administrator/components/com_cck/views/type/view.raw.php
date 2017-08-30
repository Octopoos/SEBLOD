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
class CCKViewType extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;
	protected $p		=	1;
	protected $vName	=	'type';
	protected $vTitle	=	_C2_TEXT;
	
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
							   'panel_height'=>'132px',
							   'w30'=>'span4',
							   'w70'=>'span8',
							   'wrapper'=>'container',
							   'wrapper2'=>'row-fluid',
							   'wrapper_tmpl'=>'span'
						);
		$this->js	=	array( '_'=>'',
							   'tooltip'=>'$(".hasTooltip").tooltip({});'
						);
		$this->uix	=	JCck::getUIX();
		
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
		
		if ( isset( $this->item->id ) && $this->item->id > 0 ) {
			$this->isNew		=	0;
			$this->panel_class	=	'closed';
			$this->panel_style	=	'display:none; ';
			$name				=	$this->item->name;
		} else {
			$this->isNew		=	1;
			$this->item->locked	=	1;
			$this->panel_class	=	'open';
			$this->panel_style	=	'';
			$name				=	'';
		}
		$this->item->folder		=	Helper_Admin::getSelected( $this->vName, 'folder', $this->item->folder, 1 );
		$this->item->published	=	Helper_Admin::getSelected( $this->vName, 'state', $this->item->published, 1 );
		$this->item->client		=	( $this->isNew ) ? 'admin' : $app->input->getString( 'client', $app->input->cookie->getString( 'cck_type'.$name.'_client', 'admin' ) );
		$this->item->master		=	( $this->item->client == 'content' || $this->item->client == 'intro' ) ? 'content' : 'form';
		$this->item->layer		=	$app->input->getString( 'layer', 'fields' );
		$P						=	'template_'.$this->item->client;
		$this->style			=	Helper_Workshop::getTemplateStyle( $this->vName, $this->item->$P, $this->state->get( 'tpl.'.$this->item->client, Helper_Workshop::getDefaultTemplate() ) );
		$this->item->template	=	$this->style->template;
		
		Helper_Admin::addToolbarEdit( $this->vName, 'COM_CCK_'._C2_TEXT, array( 'isNew'=>$this->isNew, 'folder'=>$this->state->get( 'filter.folder' ), 'checked_out'=>$this->item->checked_out ), array( 'template' => $this->style->template ) );
	}
	
	// prepareDisplay_Ajax
	function prepareDisplay_Ajax()
	{
		$featured	=	(int)$this->state->get( 'skeleton_id', 0 );
		$folder		=	( $featured ) ? 0 : $this->item->folder;
		
		// Fields
		$pos					=	isset( $this->style->positions[0]->value ) ? $this->style->positions[0]->value : 'mainbody';
		$this->fields			=	Helper_Workshop::getFields( 'type', $this->item, 'a.folder = '.(int)$featured, false, false, $pos  );
		
		if ( $this->item->parent != '' ) {
			$names				=	JCckDatabase::loadColumn( 'SELECT a.name FROM #__cck_core_fields AS a WHERE a.storage_table = "#__cck_store_form_'.$this->item->parent.'"' );

			if ( count( $names ) ) {
				$names			=	'"'.implode( '","', $names ).'"';
			} else {
				$names			=	'';
			}
			$this->fieldsAv		=	Helper_Workshop::getFieldsAv( 'type', $this->item, '', 'a.folder = '.(int)$folder, ( $names != '' ? 'a.name IN ('.$names.')' : '' ) );
		} else {
			$this->fieldsAv		=	Helper_Workshop::getFieldsAv( 'type', $this->item, '', 'a.folder = '.(int)$folder );
		}
		$this->type_fields		=	JCckDatabase::loadObjectList( 'SELECT fieldid, GROUP_CONCAT(DISTINCT typeid separator " c-") AS cc FROM #__cck_core_type_field group by fieldid', 'fieldid' );
		
		// Positions
		$positions				=	Helper_Workshop::getPositions( 'type', $this->item );
		if ( count( $this->style->positions ) ) {
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
				}
			}
		}
		$this->positions_nb	=	count( $this->positions );
		$this->variations	=	Helper_Workshop::getPositionVariations( $this->style->template );
		
		// Filters
		$options				=	Helper_Admin::getPluginOptions( 'field', 'cck_', true, false, true );
		$this->lists['af_t']	=	JHtml::_( 'select.genericlist', $options, 'filter_type', 'class="inputbox filter input-medium" prefix="t-"', 'value', 'text', '', 'filter1' );
		$options				=	Helper_Admin::getAlphaOptions( true );
		$this->lists['af_a']	=	JHtml::_( 'select.genericlist', $options, 'filter_alpha', 'class="inputbox filter input-medium" prefix="a-"', 'value', 'text', '', 'filter3' );
		$options				=	Helper_Admin::getTypeOptions( true, false );
		$this->lists['af_c']	=	JHtml::_( 'select.genericlist', $options, 'filter_type', 'class="inputbox filter input-medium" prefix="c-"', 'value', 'text', '', 'filter4' );
		$options				=	Helper_Admin::getFolderOptions( true, true, false, true, 'field' );
		$this->lists['af_f']	=	JHtml::_( 'select.genericlist', $options, 'filter_folder', 'class="inputbox filter input-medium" prefix="f-"', 'value', 'text', ( $this->item->id > 0 ? $this->item->folder : 1 ), 'filter2' );
	}

	// prepareDisplay_Ajax2
	function prepareDisplay_Ajax2( $isScoped )
	{
		$and		=	'';
		$featured	=	(int)$this->state->get( 'skeleton_id', 0 );
		$folder		=	$this->state->get( 'skeleton_id', $this->item->folder );

		if ( $featured == 11 ) { // TODO: dynamic mapping
			$this->item->storage_location	=	'joomla_category';
		} elseif ( $featured == 13 ) {
			$this->item->storage_location	=	'joomla_user';
		} elseif ( $featured == 14 ) {
			$this->item->storage_location	=	'joomla_user_group';
		}	
		$location	=	( $this->item->storage_location == '' ) ? 'joomla_article' : $this->item->storage_location;
		$or			=	'';

		// Fields
		if ( !$isScoped ) {
			$and	=	'(a.storage_location != "'.$location.'" AND a.storage != "none")';
		} else {
			if ( $this->item->parent != '' ) {
				$names		=	JCckDatabase::loadColumn( 'SELECT a.name FROM #__cck_core_fields AS a WHERE a.storage_table = "#__cck_store_form_'.$this->item->parent.'"' );

				if ( count( $names ) ) {
					$names	=	'"'.implode( '","', $names ).'"';
				}
				if ( $names != '' ) {
					$or	=	'a.name IN ('.$names.')';	
				}
			}
			$and	=	'(a.storage_location = "'.$location.'" OR a.storage = "none")';
		}
		$this->fieldsAv			=	Helper_Workshop::getFieldsAv( 'type', $this->item, $and, 'a.folder != '.(int)$folder, $or );
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
		$variat	=	Jhtml::_( 'select.genericlist', $this->variations, 'ffp[pos-'.$name.'][variation]', 'size="1" class="thin blue c_var_ck"', 'value', 'text', $this->positions[$name]->variation, 'pos-'.$name.'_variation' );
		$variat	.=	'<input type="hidden" id="pos-'.$name.'_variation_options" name="ffp[pos-'.$name.'][variation_options]" value="'.htmlspecialchars( @$this->positions[$name]->variation_options ).'" />';
		$width	=	'<input class="thin blue" type="text" name="ffp[pos-'.$name.'][width]" value="'.@$this->positions[$name]->width.'" size="8" style="text-align:center;" />&nbsp;×&nbsp;';
		$height	=	'<input class="thin blue" type="text" name="ffp[pos-'.$name.'][height]" value="'.@$this->positions[$name]->height.'" size="8" style="text-align:center;" />';
		$css	=	'';
		
		Helper_Workshop::displayPosition( $this->p, $name, $title, $legend, $variat, @$this->positions[$name]->variation, $width, $height, $css, array( 'template'=>$this->item->template, 'name'=>$this->item->name, 'view'=>$this->item->client ) );
		$this->p++;
		
		return $name;
	}
}
?>