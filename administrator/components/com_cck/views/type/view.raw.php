<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: view.raw.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
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
			default:
				break;
		}
		
		if ( JCck::on() ) {
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
		} else {
			$this->css	=	array( '_'=>'',
								   'panel_height'=>'105px',
								   'w30'=>'width-30',
								   'w70'=>'width-70 fltlft',
								   'wrapper'=>'sebwrapper',
								   'wrapper2'=>'seb-wrapper workshop',
								   'wrapper_tmpl'=>'width-100 bg-dark fltlft'
							);
			$this->js	=	array( '_'=>'',
								   'tooltip'=>''
							);
		}
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
			JError::raiseError( 500, implode( "\n", $errors ) );
			return false;
		}
		
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
		$featured	=	$this->state->get( 'skeleton_id', 0 );
		
		// Fields
		$objects				=	'';
		$pos					=	isset( $this->style->positions[0]->value ) ? $this->style->positions[0]->value : 'mainbody';
		$this->fields			=	Helper_Workshop::getFields( 'type', $this->item, 'a.folder = '.(int)$featured, false, false, $pos  );
		$this->fieldsAv			=	Helper_Workshop::getFieldsAv( 'type', $this->item, $objects, 'a.folder != '.(int)$featured );
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
		$max_width				=	( JCck::on() ) ? '' : ' style="max-width:180px;"';
		$default_f				=	( $this->item->id > 0 ) ? $this->item->folder : '';
		$options				=	Helper_Admin::getPluginOptions( 'field', 'cck_', true, false, true );
		$this->lists['af_t']	=	JHtml::_( 'select.genericlist', $options, 'filter_type', 'class="inputbox filter input-medium" prefix="t-"'.$max_width, 'value', 'text', '', 'filter1' );
		$options				=	Helper_Admin::getAlphaOptions( true );
		$this->lists['af_a']	=	JHtml::_( 'select.genericlist', $options, 'filter_alpha', 'class="inputbox filter input-medium" prefix="a-"', 'value', 'text', '', 'filter3' );
		$options				=	Helper_Admin::getTypeOptions( true, false );
		$this->lists['af_c']	=	JHtml::_( 'select.genericlist', $options, 'filter_type', 'class="inputbox filter input-medium" prefix="c-"'.$max_width, 'value', 'text', '', 'filter4' );
		$options				=	Helper_Admin::getFolderOptions( true, true, false, true, 'field' );
		$this->lists['af_f']	=	JHtml::_( 'select.genericlist', $options, 'filter_folder', 'class="inputbox filter input-medium" prefix="f-"'.$max_width, 'value', 'text', $default_f, 'filter2' );
	}
	
	// setPosition
	function setPosition( $name, $title = '' )
	{
		$title	=	( !empty( $title ) ) ? $title : $name;
		$legend	=	'<input class="thin blue" type="text" name="ffp[pos-'.$name.'][legend]" value="'.htmlspecialchars( @$this->positions[$name]->legend ).'" size="22" />';
		$variat	=	Jhtml::_( 'select.genericlist', $this->variations, 'ffp[pos-'.$name.'][variation]', 'size="1" class="thin blue c_var_ck"', 'value', 'text', $this->positions[$name]->variation, 'pos-'.$name.'_variation' );
		$variat	.=	'<input type="hidden" id="pos-'.$name.'_variation_options" name="ffp[pos-'.$name.'][variation_options]" value="'.htmlspecialchars( @$this->positions[$name]->variation_options ).'" />';
		$width	=	'<input class="thin blue" type="text" name="ffp[pos-'.$name.'][width]" value="'.@$this->positions[$name]->width.'" size="8" style="text-align:center;" /> x&nbsp;';
		$height	=	'<input class="thin blue" type="text" name="ffp[pos-'.$name.'][height]" value="'.@$this->positions[$name]->height.'" size="8" style="text-align:center;" />';
		$css	=	'';
		
		Helper_Workshop::displayPosition( $this->p, $name, '# '.$title, $legend, $variat, @$this->positions[$name]->variation, $width, $height, $css );
		$this->p++;
		
		return $name;
	}
}
?>