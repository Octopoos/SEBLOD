<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: helper_workshop.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Helper
class Helper_Workshop
{
	protected static $template	=	'';
	
	// displayField
	public static function displayField( &$field, $type_field = '', $attr = array() )
	{
		static $hasMb = -1;

		if ( $hasMb < 0 ) {
			$hasMb	=	( function_exists( 'mb_convert_case' ) ) ? 1 : 0;
		}
		$name	=	( $hasMb ) ? mb_convert_case( substr( $field->title, 0, 1 ), MB_CASE_LOWER, 'UTF-8' ) : strtolower( $field->title );
		$link	=	'index.php?option=com_cck&task=field.edit&id='.$field->id.'&tmpl=component';
		?><li class="field <?php echo 't-'.$field->type.' f-'.$field->folder.' a-'.$name.$type_field; ?>" id="<?php echo $field->id; ?>"><a class="cbox<?php echo $attr['class']; ?>" href="<?php echo $link; ?>"><?php echo $attr['span']; ?></a><span class="title" onDblClick="JCck.DevHelper.move('<?php echo $field->id; ?>');"><?php echo $field->title; ?><span class="subtitle">(<?php echo JText::_( 'PLG_CCK_FIELD_'.$field->type.'_LABEL2' ); ?>)</span></span><input type="hidden" id="k<?php echo $field->id; ?>" name="ff[<?php echo $field->name; ?>]" value="<?php echo $field->id; ?>" /><?php echo '<div class="move" onClick="JCck.DevHelper.move('.$field->id.');"></div>'; ?><div class="drag"></div><?php echo @$field->params; ?></li><?php
	}
	
	// displayHeader
	public static function displayHeader( $element, $master )
	{
		if ( $element == 'type' ) {
		?>
			<div id="divtop">
            <ul class="sortable_header" myid="1">
                <li>
                <?php
                if ( $master == 'content' ) { ?>
                    <div class="pane ph4 hide"><div><?php echo JText::_( 'COM_CCK_RESTRICTION' ); ?></div><div><?php echo JText::_( 'COM_CCK_ACCESS' ); ?></div></div>
                    <div class="pane ph3 hide"><div><?php echo JText::_( 'COM_CCK_MARKUP_CLASS' ); ?></div><div><?php echo JText::_( 'COM_CCK_MARKUP' ); ?></div></div>
                    <div class="pane ph2 hide"><div><?php echo JText::_( 'COM_CCK_TYPO' ); ?></div><div><?php echo JText::_( 'COM_CCK_LINK' ); ?></div></div>
                    <div class="pane ph1"><div><?php echo JText::_( 'COM_CCK_VARIATION' ); ?></div><div><?php echo JText::_( 'COM_CCK_LABEL' ); ?></div></div>
					<div class="pane ph11 hide"><div><?php echo JText::_( 'COM_CCK_WIDTH_HEIGHT_PX_PC' ); ?></div><div><?php echo '-'; ?></div></div>
                <?php } else { ?>
                	<div class="pane ph6 hide"><div><?php echo JText::_( 'COM_CCK_MARKUP_CLASS' ); ?></div><div><?php echo JText::_( 'COM_CCK_MARKUP' ); ?></div></div>
                    <div class="pane ph5 hide"><div><?php echo JText::_( 'COM_CCK_COMPUTATION_RULES' ); ?></div><div><?php echo JText::_( 'COM_CCK_CONDITIONAL_STATES' ); ?></div></div>
                    <div class="pane ph4 hide"><div><?php echo JText::_( 'COM_CCK_RESTRICTION' ); ?></div><div><?php echo JText::_( 'COM_CCK_ACCESS' ); ?></div></div>
                    <div class="pane ph3 hide"><div><?php echo JText::_( 'COM_CCK_STAGE' ); ?></div><div><?php echo JText::_( 'COM_CCK_REQUIRED_VALIDATION' ); ?></div></div>
                    <div class="pane ph2 hide"><div><?php echo JText::_( 'COM_CCK_LIVE_VALUE' ); ?></div><div><?php echo JText::_( 'COM_CCK_LIVE' ); ?></div></div>
                    <div class="pane ph1"><div><?php echo JText::_( 'COM_CCK_VARIATION' ); ?></div><div><?php echo JText::_( 'COM_CCK_LABEL' ); ?></div></div> 
					<div class="pane ph11 hide"><div><?php echo JText::_( 'COM_CCK_WIDTH_HEIGHT_PX_PC' ); ?></div><div><?php echo '-'; ?></div></div>
                <?php } ?>
                </li>
            </ul>
            </div>
        <?php
		} else { ?>
	        <div id="divtop">
            <ul class="sortable_header" myid="1">
                <li>
                <?php
				if ( $master == 'order' ) { ?>
					<div class="pane ph1"><div><?php echo JText::_( 'COM_CCK_OPTIONS' ); ?></div><div><?php echo JText::_( 'COM_CCK_DIRECTION' ); ?></div></div>
                <?php } elseif ( $master == 'content' ) { ?>
                    <div class="pane ph4 hide"><div><?php echo JText::_( 'COM_CCK_RESTRICTION' ); ?></div><div><?php echo JText::_( 'COM_CCK_ACCESS' ); ?></div></div>
                    <div class="pane ph3 hide"><div><?php echo JText::_( 'COM_CCK_MARKUP_CLASS' ); ?></div><div><?php echo JText::_( 'COM_CCK_MARKUP' ); ?></div></div>
                    <div class="pane ph2 hide"><div><?php echo JText::_( 'COM_CCK_TYPO' ); ?></div><div><?php echo JText::_( 'COM_CCK_LINK' ); ?></div></div>
                    <div class="pane ph1"><div><?php echo JText::_( 'COM_CCK_VARIATION' ); ?></div><div><?php echo JText::_( 'COM_CCK_LABEL' ); ?></div></div>
					<div class="pane ph11 hide"><div><?php echo JText::_( 'COM_CCK_WIDTH_HEIGHT_PX_PC' ); ?></div><div><?php echo JText::_( 'COM_CCK_CLASS' ); ?></div></div>
                <?php } else { ?>
                	<div class="pane ph7 hide"><div><?php echo '-'; ?></div><div><?php echo JText::_( 'COM_CCK_REQUIRED_VALIDATION' ); ?></div></div>
                    <div class="pane ph6 hide"><div><?php echo JText::_( 'COM_CCK_MARKUP_CLASS' ); ?></div><div><?php echo JText::_( 'COM_CCK_MARKUP' ); ?></div></div>
                    <div class="pane ph5 hide"><div>-</div><div><?php echo JText::_( 'COM_CCK_CONDITIONAL_STATES' ); ?></div></div>
                    <div class="pane ph4 hide"><div><?php echo JText::_( 'COM_CCK_RESTRICTION' ); ?></div><div><?php echo JText::_( 'COM_CCK_ACCESS' ); ?></div></div>
                    <div class="pane ph3 hide"><div><?php echo JText::_( 'COM_CCK_STAGE' ); ?></div><div><?php echo JText::_( 'COM_CCK_MATCH' ); ?></div></div>
                    <div class="pane ph2 hide"><div><?php echo JText::_( 'COM_CCK_LIVE_VALUE' ); ?></div><div><?php echo JText::_( 'COM_CCK_LIVE' ); ?></div></div>
                    <div class="pane ph1"><div><?php echo JText::_( 'COM_CCK_VARIATION' ); ?></div><div><?php echo JText::_( 'COM_CCK_LABEL' ); ?></div></div>
					<div class="pane ph11 hide"><div><?php echo JText::_( 'COM_CCK_WIDTH_HEIGHT_PX_PC' ); ?></div><div><?php echo JText::_( 'COM_CCK_CLASS' ); ?></div></div>
                <?php } ?>
                </li>
            </ul>
            </div>
        <?php
		}
	}
	
	// displayPosition
	public static function displayPosition( $p, $name, $title, $legend, $variation, $variation_status, $width, $height, $css, $info = array() )
	{
		$attr	=	'';
		$class	=	'';
		$dir	=	'down';
		$to		=	$p + 1;
		$hide	=	( $variation_status != '' ) ? '' : ' hidden';

		if ( isset( $info['template'] ) && $info['template'] != '' ) {
			if ( is_file( JPATH_SITE.'/templates/'.$info['template'].'/positions/'.$name.'.php' ) ) {
				$attr	=	' data-path="'.'templates/'.$info['template'].'/positions/'.$name.'.php'.'"';
				$class	=	' overridden';
			} elseif ( isset( $info['name'] ) && $info['name'] != '' && isset( $info['view'] ) && $info['view'] != '' && is_file( JPATH_SITE.'/templates/'.$info['template'].'/positions/'.$info['name'].'/'.$info['view'].'/'.$name.'.php' ) ) {
				$attr	=	' data-path="'.'templates/'.$info['template'].'/positions/'.$info['name'].'/'.$info['view'].'/'.$name.'.php'.'"';
				$class	=	' overridden';
			}
		}
		$pos	=	'<li class="position ui-state-disabled" id="pos-'.$p.'">'
				.	'<input class="selector" type="radio" id="position'.$p.'" name="positions" gofirst="#pos-'.($to-1).'" golast="#pos-'.$to.'" />'
				.	'<span class="title'.$class.'"'.$attr.'>'.$title.'</span>'
				.	'<input type="hidden" name="ff[pos-'.$name.']" value="position" />'
				.	'<div class="pane la">'
				.	'<div class="col1"><div class="colc">'.$legend.'</div></div>'
				.	'<div class="col2"><div class="colc">'.$variation.'<span class="c_var'.$hide.'" name="'.$name.'">+</span>'.'</div></div></div>'
				.	'<div class="pane lb" style="display: none;">'
				.	'<div class="col1"><div class="colc">'.$css.'</div></div>'
				.	'<div class="col2"><div class="colc">'.$width.$height.'</div></div>'
				.	'</div>'
				.	'</li>';
		echo $pos;
	}
	
	// displayPositionEnd
	public static function displayPositionEnd( $p = 1 )
	{
		echo '<li class="position ui-state-disabled boundary" id="pos-'.( $p + 1 ).'"><input type="hidden" name="li_end" value="1"></li>';
	}
	
	// displayPositionStatic
	public static function displayPositionStatic( $p, $name, $title )
	{
		$legend	=	'<input type="hidden" name="ffp[pos-'.$name.'][legend]" value="" />';
		$variat	=	'<input type="hidden" name="ffp[pos-'.$name.'][variation]" value="" id="pos-'.$name.'_variation" />'
				.	'<input type="hidden" id="pos-'.$name.'_variation_options" name="ffp[pos-'.$name.'][variation_options]" value="" />';
		$width	=	'<input type="hidden" name="ffp[pos-'.$name.'][width]" value="" />';
		$height	=	'<input type="hidden" name="ffp[pos-'.$name.'][height]" value="" />';
		$css	=	'<input type="hidden" name="ffp[pos-'.$name.'][css]" value="" />';
		
		self::displayPosition( $p, $name, $title, $legend, $variat, '', $width, $height, $css );
	}
	
	// displayToolbar
	public static function displayToolbar( $element, $master, $client, $uix, $clone = '' )
	{	
		$bar		=	'';
		$last		=	( $clone ) ? '' : ' last';
		
		$bar		.=	'<a class="hasTooltip cbox qtip_cck icons icon-add" title="'.JText::_( 'COM_CCK_ADD_FIELD' ).'" href="index.php?option=com_cck&task=field.add&tmpl=component&ajax_state=1&ajax_type=text"></a>'
					.	'<a class="hasTooltip first qtip_cck icons icon-up" title="'.JText::_( 'COM_CCK_FIELDS_MOVE_UP' ).'" href="javascript: JCck.DevHelper.moveTop();"></a>';
		if ( $uix == 'full' ) {
			$bar	.=	'<a class="hasTooltip qtip_cck icons icon-right" title="'.JText::_( 'COM_CCK_FIELDS_MOVE_RIGHT' ).'" href="javascript: JCck.DevHelper.moveAcross(\'#sortable2\');"></a>'
					.	'<a class="hasTooltip qtip_cck icons icon-left" title="'.JText::_( 'COM_CCK_FIELDS_MOVE_LEFT' ).'" href="javascript: JCck.DevHelper.moveAcross();"></a>';
		}
		$bar		.=	'<a class="hasTooltip qtip_cck icons icon-down" title="'.JText::_( 'COM_CCK_FIELDS_MOVE_DOWN' ).'" href="javascript: JCck.DevHelper.moveBottom();"></a>';
		
		if ( $element == 'type' ) {
			if ( $master == 'content' ) {
                $bar		.=	'<a class="hasTooltip qtip_cck icons panel pb1 icon-one first selected" title="'.JText::_( 'COM_CCK_LABEL' ).' <b>+</b> '.JText::_( 'COM_CCK_VARIATION' ).'" href="javascript:void(0);">1</a>'
							.	'<a class="hasTooltip qtip_cck icons panel pb2 icon-two" title="'.JText::_( 'COM_CCK_LINK' ).' <b>+</b> '.JText::_( 'COM_CCK_TYPO' ).'" href="javascript:void(0);">2</a>'
                			.	'<a class="hasTooltip qtip_cck icons panel pb3 icon-three" title="'.JText::_( 'COM_CCK_MARKUP' ).' <b>+</b> '.JText::_( 'COM_CCK_MARKUP_CLASS' ).'"href="javascript:void(0);">3</a>'
							.	'<a class="hasTooltip qtip_cck icons panel pb4 icon-four" title="'.JText::_( 'COM_CCK_ACCESS' ).' <b>+</b> '.JText::_( 'COM_CCK_RESTRICTION' ).'" href="javascript:void(0);">4</a>';
			} else {
				$bar		.=	'<a class="hasTooltip qtip_cck icons panel pb1 icon-one first selected" title="'.JText::_( 'COM_CCK_LABEL' ).' <b>+</b> '.JText::_( 'COM_CCK_VARIATION' ).'" href="javascript:void(0);">1</a>'
							.	'<a class="hasTooltip qtip_cck icons panel pb2 icon-two" title="'.JText::_( 'COM_CCK_LIVE' ).' <b>+</b> '.JText::_( 'COM_CCK_LIVE_VALUE' ).'"href="javascript:void(0);">2</a>'
							.	'<a class="hasTooltip qtip_cck icons panel pb3 icon-three" title="'.JText::_( 'COM_CCK_REQUIRED_VALIDATION' ).' <b>+</b> '.JText::_( 'COM_CCK_STAGE' ).'" href="javascript:void(0);">3</a>'
							.	'<a class="hasTooltip qtip_cck icons panel pb4 icon-four" title="'.JText::_( 'COM_CCK_ACCESS' ).' <b>+</b> '.JText::_( 'COM_CCK_RESTRICTION' ).'" href="javascript:void(0);">4</a>'
							.	'<a class="hasTooltip qtip_cck icons panel pb5 icon-five" title="'.JText::_( 'COM_CCK_CONDITIONAL_STATES' ).' <b>+</b> '.JText::_( 'COM_CCK_COMPUTATION' ).'" href="javascript:void(0);">5</a>'
							.	'<a class="hasTooltip qtip_cck icons panel pb6 icon-six" title="'.JText::_( 'COM_CCK_MARKUP' ).' <b>+</b> '.JText::_( 'COM_CCK_MARKUP_CLASS' ).'" href="javascript:void(0);">6</a>';
			}
		} else {
			if ( $master == 'content' ) {
				$bar		.=	'<a class="hasTooltip qtip_cck panel pb1 icons icon-one first selected" title="'.JText::_( 'COM_CCK_LABEL' ).' <b>+</b> '.JText::_( 'COM_CCK_VARIATION' ).'" href="javascript:void(0);">1</a>'
							.	'<a class="hasTooltip qtip_cck icons panel pb2 icon-two" title="'.JText::_( 'COM_CCK_LINK' ).' <b>+</b> '.JText::_( 'COM_CCK_TYPO' ).'" href="javascript:void(0);">2</a>'
							.	'<a class="hasTooltip qtip_cck icons panel pb3 icon-three" title="'.JText::_( 'COM_CCK_MARKUP' ).' <b>+</b> '.JText::_( 'COM_CCK_MARKUP_CLASS' ).'"href="javascript:void(0);">3</a>'
							.	'<a class="hasTooltip qtip_cck icons panel pb4 icon-four" title="'.JText::_( 'COM_CCK_ACCESS' ).' <b>+</b> '.JText::_( 'COM_CCK_RESTRICTION' ).'" href="javascript:void(0);">4</a>';
			} elseif ( $master == 'search' ) {
				$bar		.=	'<a class="hasTooltip qtip_cck icons panel pb1 icon-one first selected" title="'.JText::_( 'COM_CCK_LABEL' ).' <b>+</b> '.JText::_( 'COM_CCK_VARIATION' ).'" href="javascript:void(0);">1</a>'
							.	'<a class="hasTooltip qtip_cck icons panel pb2 icon-two" title="'.JText::_( 'COM_CCK_LIVE' ).' <b>+</b> '.JText::_( 'COM_CCK_LIVE_VALUE' ).'"href="javascript:void(0);">2</a>'
							.	'<a class="hasTooltip qtip_cck icons panel pb3 icon-three" title="'.JText::_( 'COM_CCK_MATCH' ).' <b>+</b> '.JText::_( 'COM_CCK_STAGE' ).'" href="javascript:void(0);">3</a>'
							.	'<a class="hasTooltip qtip_cck icons panel pb4 icon-four" title="'.JText::_( 'COM_CCK_ACCESS' ).' <b>+</b> '.JText::_( 'COM_CCK_RESTRICTION' ).'" href="javascript:void(0);">4</a>'
							.	'<a class="hasTooltip qtip_cck icons panel pb5 icon-five" title="'.JText::_( 'COM_CCK_CONDITIONAL_STATES' ).'" href="javascript:void(0);">5</a>'
							.	'<a class="hasTooltip qtip_cck icons panel pb6 icon-six" title="'.JText::_( 'COM_CCK_MARKUP' ).' <b>+</b> '.JText::_( 'COM_CCK_MARKUP_CLASS' ).'" href="javascript:void(0);">6</a>'
							.	'<a class="hasTooltip qtip_cck icons panel pb7 icon-six" title="'.JText::_( 'COM_CCK_REQUIRED_VALIDATION' ).'" href="javascript:void(0);">7</a>';
			} else {
				$bar		.=	'<a class="hasTooltip qtip_cck icons panel icon-one first selected" title="'.JText::_( 'COM_CCK_DIRECTION' ).'" href="javascript: void(0);">1</a>';
			}
		}
		if ( $master != 'order' ) {
			$bar	.=	'<a class="hasTooltip qtip_cck icons panel icon-f'.$last.'" title="<b>'.JText::_( 'COM_CCK_POSITIONS' ).':</b><br />'
					.	JText::_( 'COM_CCK_LABEL' ).' + '.JText::_( 'COM_CCK_VARIATION' ).'<br />'
					.	JText::_( 'COM_CCK_WIDTH' ).' + '.JText::_( 'COM_CCK_HEIGHT' ).'" href="javascript:void(0);">&bull;</a>';
		}
		if ( $clone ) {
			$bar	.=	'<a class="hasTooltip qtip_cck icons icon-add2 first" id="initclient" title="'.$clone.'" href="javascript:void(0);"></a>';
		}
		
		echo $bar;
	}
	
	// getDefaultStyle
	public static function getDefaultStyle( $template = '' )
	{
		static $styles	=	array();
		
		if ( !$template ) {
			$template			=	self::getDefaultTemplate();
		}
		if ( !isset( $styles[$template] ) ) {
			$styles[$template]	=	JCckDatabaseCache::loadObject( 'SELECT id, params, template FROM #__template_styles WHERE template = "'.(string)$template.'" ORDER BY id asc' );
		}
		
		return $styles[$template];
	}

	// getDefaultTemplate
	public static function getDefaultTemplate()
	{
		$name		=	JCckDatabaseCache::loadResult( 'SELECT name FROM #__cck_core_templates WHERE featured = 1 ORDER BY id' );
		if ( !$name) {
			$name	=	'seb_one';
		}

		return $name;
	}
	
	// getFields
	public static function getFields( $element, $item, $featured = '', $exclusion = false, $force = false, $pos = '' )
	{
		jimport( 'cck.construction.field.generic_more' );
		$table	=	$element.'_field';
		if ( ! isset( $item->id ) ) {
			$fields	=	array();
			if ( $featured != '' ) {
				$where	=	'WHERE '.$featured.' AND a.type != "" AND a.storage != "dev" AND ( a.storage_table NOT LIKE "#__cck_store_form_%" )';
				$fields	=	JCckDatabase::loadObjectList( 'SELECT DISTINCT a.id, a.title, a.name, a.folder, a.type, a.label FROM #__cck_core_fields AS a '.$where.' ORDER BY a.ordering ASC' );
				if ( count( $fields ) ) {
					$list	=	array();
					foreach ( $fields as $f ) {
						$f->position	=	$pos;
						$list[$pos][]	=	$f;
					}
					$fields	=	$list;
				}
			}
			
			return $fields;
		}
		if ( $exclusion !== false ) {
			$query	=	'SELECT a.fieldid FROM #__cck_core_'.$table.' AS a WHERE a.'.$element.'id = '.(int)$item->id.' AND a.client = "'.$item->client.'"';
			$fields	=	JCckDatabase::loadColumn( $query );
			if ( ! is_array( $fields ) ) {
				return '';
			}
			$fields	=	implode( ',', $fields );
		} else {
			$and	=	( $force === true ) ? ' '.$featured : '';
			$query	=	' SELECT DISTINCT a.id, a.title, a.name, a.folder, a.type, a.label, c.client, '.plgCCK_FieldGeneric_More::gm_getConstruction_Columns( $table, '_get' )
					.	' FROM #__cck_core_fields AS a '
					. 	' LEFT JOIN #__cck_core_'.$table.' AS c ON c.fieldid = a.id'
					.	' WHERE c.'.$element.'id = '.(int)$item->id.' AND c.client = "'.$item->client.'"'
					.	$and
					.	' ORDER BY c.ordering ASC'
					;
			$fields	=	JCckDatabase::loadObjectListArray( $query, 'position' );
			
			if ( ! $fields ) {
				return array();
			}
		}
		
		return $fields;
	}
	
	// getFieldsAv
	public static function getFieldsAv( $element, $item, $and, $folder = '', $or = '' )
	{
		$excluded	=	self::getFields( $element, $item, '', true );
		$select		=	'';
		$where		=	' WHERE a.published = 1 AND b.published = 1';

		if ( $and != '' ) {
			$where	.=	' AND '.$and;
		}
		if ( $element == 'type' && $item->storage_location != 'none' && $item->location == 'none' ) {
			// Should we append something here?
		} elseif ( $element == 'type' && $item->storage_location != 'none' ) {
			if ( $or != '' ) {
				$or	=	' OR ('.$or.')';
			}
			$where	.=	' AND ( (a.storage_table NOT LIKE "#__cck_store_form_%" OR a.storage_table ="#__cck_store_form_'.$item->name.'") '.$or.')';			
		} elseif ( $element == 'search' ) {
			$select	=	', a.storage_table, a.storage_field';
		}
		if ( $excluded ) {
			$where	.=	' AND a.id NOT IN ('.$excluded.')';
		}
		if ( $folder != '' ) {
			$where	.=	' AND '.$folder;
		}
		$query	=	' SELECT DISTINCT a.id, a.title, a.name, a.folder, a.type, a.label'
				.	$select
				.	' FROM #__cck_core_fields AS a '
				.	' LEFT JOIN #__cck_core_folders AS b ON b.id = a.folder '
				.	$where
				.	' GROUP BY a.id'
				.	' ORDER BY a.title ASC'
				;

		$fields	=	JCckDatabase::loadObjectList( $query );
		if ( ! $fields ) {
			return array();
		}
		
		return $fields;
	}
	
	// getParams
	public static function getParams( $element, $master, $client )
	{
		$data					=	array();
		$data['_']				=	array( 'add'=>JText::_( 'COM_CCK_ADD' ), 'configure'=>JText::_( 'COM_CCK_CONFIGURE' ), 'edit'=>JText::_( 'COM_CCK_EDIT' ),
										   'optional'=>JText::_( 'COM_CCK_OPTIONAL' ), 'required'=>JText::_( 'COM_CCK_REQUIRED' ), 'icon-friendly'=>'<span class="icon-menu-2"></span>' );
		
		$data['computation']	=	true;
		$data['conditional']	=	true;
		$data['label']			=	true;
		$data['markup_class']	=	true;

		if ( $element == 'type' ) {
			if ( $master == 'content' ) {
				$data['link']		=	array_merge(
											array( ''=>JHtml::_( 'select.option', '', JText::_( 'COM_CCK_NONE' ) ) ),
											Helper_Admin::getPluginOptions( 'field_link', 'cck_', false, false, true )
										);
				$data['typo']		=	array_merge(
											array( ''=>JHtml::_( 'select.option', '', JText::_( 'COM_CCK_NONE' ) ) ),
											Helper_Admin::getPluginOptions( 'field_typo', 'cck_', false, false, true )
										);
				$data['markup']		=	array(
											''=>JHtml::_( 'select.option', '', JText::_( 'COM_CCK_DEFAULT' ) ),
											'none'=>JHtml::_( 'select.option', 'none', JText::_( 'COM_CCK_NONE' ) )
										);
				$data['access']		=	array( 0=>(object)array( 'text'=>JText::_( 'COM_CCK_CLEAR' ), 'value'=>0 ) ) + JCckDatabase::loadObjectList( 'SELECT a.id AS value, a.title AS text FROM #__viewlevels AS a GROUP BY a.id ORDER BY title ASC', 'value' );
				$data['restriction']=	array_merge(
											array( ''=>JHtml::_( 'select.option', '', JText::_( 'COM_CCK_NONE' ) ) ),
											Helper_Admin::getPluginOptions( 'field_restriction', 'cck_', false, false, true )
										);
			} else {
				$data['client']		=	$client;
				$data['variation']	=	array(
											'hidden'=>JHtml::_( 'select.option', 'hidden', JText::_( 'COM_CCK_HIDDEN_AND_SECURED' ) ),
											'hidden_isfilled'=>JHtml::_( 'select.option', 'hidden_isfilled', JText::_( 'COM_CCK_HIDDEN_IS_FILLED_AND_SECURED' ) ),
											'value'=>JHtml::_( 'select.option', 'value', JText::_( 'COM_CCK_VALUE_AND_SECURED' ) ),
											'100'=>JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_FORM' ) ),
											''=>JHtml::_( 'select.option', '', JText::_( 'COM_CCK_DEFAULT' ) ),
											'disabled'=>JHtml::_( 'select.option', 'disabled', JText::_( 'COM_CCK_FORM_DISABLED_AND_SECURED' ) ),
											'101'=>JHtml::_( 'select.option', '</OPTGROUP>', '' )
										);
				$data['live']		=	array_merge(
											array( ''=>JHtml::_( 'select.option', '', JText::_( 'COM_CCK_DEFAULT' ) ) ),
											Helper_Admin::getPluginOptions( 'field_live', 'cck_', false, false, true )
										);
				$data['stage']		=	array(
											'0'=>JHtml::_( 'select.option', '0', JText::_( 'COM_CCK_STAGE_FINAL' ) ),
											'100'=>JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_STAGE_TEMP' ) ),
											'1'=>JHtml::_( 'select.option', '1', JText::_( 'COM_CCK_STAGE_1ST' ) ),
											'2'=>JHtml::_( 'select.option', '2', JText::_( 'COM_CCK_STAGE_2ND' ) ),
											'3'=>JHtml::_( 'select.option', '3', JText::_( 'COM_CCK_STAGE_3RD' ) ),
											'4'=>JHtml::_( 'select.option', '4', JText::_( 'COM_CCK_STAGE_4TH' ) ),
											'5'=>JHtml::_( 'select.option', '5', JText::_( 'COM_CCK_STAGE_5TH' ) ),
											'101'=>JHtml::_( 'select.option', '</OPTGROUP>', '' )
										);
				$data['markup']		=	array(
											''=>JHtml::_( 'select.option', '', JText::_( 'COM_CCK_DEFAULT' ) ),
											'none'=>JHtml::_( 'select.option', 'none', JText::_( 'COM_CCK_NONE' ) )
										);
				$data['access']		=	array( 0=>(object)array( 'text'=>JText::_( 'COM_CCK_CLEAR' ), 'value'=>0 ) ) + JCckDatabase::loadObjectList( 'SELECT a.id AS value, a.title AS text FROM #__viewlevels AS a GROUP BY a.id ORDER BY title ASC', 'value' );
				$data['validation']	=	true;
				$data['restriction']=	array_merge(
											array( ''=>JHtml::_( 'select.option', '', JText::_( 'COM_CCK_NONE' ) ) ),
											Helper_Admin::getPluginOptions( 'field_restriction', 'cck_', false, false, true )
										);
			}
		} else {
			if ( $master == 'order' ) {
				$data['match_mode']	=	array(
											'ASC'=>JHtml::_( 'select.option', 'ASC', JText::_( 'COM_CCK_ASCENDING' ) ),
											'DESC'=>JHtml::_( 'select.option', 'DESC', JText::_( 'COM_CCK_DESCENDING' ) ),
											'100'=>JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_CUSTOM' ) ),
											'FIELD'=>JHtml::_( 'select.option', 'FIELD', JText::_( 'COM_CCK_VALUES' ) ),
											'101'=>JHtml::_( 'select.option', '</OPTGROUP>', '' )
										);
			} elseif ( $master == 'content' ) {
				$data['link']		=	array_merge(
											array( ''=>JHtml::_( 'select.option', '', JText::_( 'COM_CCK_NONE' ) ) ),
											Helper_Admin::getPluginOptions( 'field_link', 'cck_', false, false, true )
										);
				$data['typo']		=	array_merge(
											array( ''=>JHtml::_( 'select.option', '', JText::_( 'COM_CCK_NONE' ) ) ),
											Helper_Admin::getPluginOptions( 'field_typo', 'cck_', false, false, true )
										);
				$data['markup']		=	array(
											''=>JHtml::_( 'select.option', '', JText::_( 'COM_CCK_DEFAULT' ) ),
											'none'=>JHtml::_( 'select.option', 'none', JText::_( 'COM_CCK_NONE' ) )
										);
				$data['access']		=	array( 0=>(object)array( 'text'=>JText::_( 'COM_CCK_CLEAR' ), 'value'=>0 ) ) + JCckDatabase::loadObjectList( 'SELECT a.id AS value, a.title AS text FROM #__viewlevels AS a GROUP BY a.id ORDER BY title ASC', 'value' );
				$data['restriction']=	array_merge(
											array( ''=>JHtml::_( 'select.option', '', JText::_( 'COM_CCK_NONE' ) ) ),
											Helper_Admin::getPluginOptions( 'field_restriction', 'cck_', false, false, true )
										);
			} else {
				$data['client']		=	$client;
				$data['variation']	=	array(
											'hidden'=>JHtml::_( 'select.option', 'hidden', JText::_( 'COM_CCK_HIDDEN_AND_SECURED' ) ),
											'value'=>JHtml::_( 'select.option', 'value', JText::_( 'COM_CCK_VALUE_AND_SECURED' ) ),
											'100'=>JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_FORM' ) ),
											''=>JHtml::_( 'select.option', '', JText::_( 'COM_CCK_DEFAULT' ) ),
											'form_filter'=>JHtml::_( 'select.option', 'form_filter', JText::_( 'COM_CCK_FORM_FILTER' ) ),
											/*
											'form_filter_ajax'=>JHtml::_( 'select.option', 'form_filter_ajax', JText::_( 'COM_CCK_FORM_FILTER_AJAX' ) ),
											*/
											'disabled'=>JHtml::_( 'select.option', 'disabled', JText::_( 'COM_CCK_FORM_DISABLED_AND_SECURED' ) ),
											'101'=>JHtml::_( 'select.option', '</OPTGROUP>', '' ) );
				$data['match_mode']	=	array(
											'none'=>JHtml::_( 'select.option', 'none', JText::_( 'COM_CCK_NONE' ) ),
											'100'=>JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_MATCH_GROUP_BASIC' ) ),
											'alpha'=>JHtml::_( 'select.option', 'alpha', JText::_( 'COM_CCK_MATCH_BEGINNING_WITH' ) ),
											'empty'=>JHtml::_( 'select.option', 'empty', JText::_( 'COM_CCK_MATCH_EMPTY' ) ),
											'zeta'=>JHtml::_( 'select.option', 'zeta', JText::_( 'COM_CCK_MATCH_ENDING_WITH' ) ),
											'exact'=>JHtml::_( 'select.option', 'exact', JText::_( 'COM_CCK_MATCH_EXACT_PHRASE' ) ),
											''=>JHtml::_( 'select.option', '', JText::_( 'COM_CCK_MATCH_DEFAULT_PHRASE' ) ),
											'101'=>JHtml::_( 'select.option', '</OPTGROUP>', '' ),
											'102'=>JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_MATCH_GROUP_DATE_AND_TIME' ) ),
											'date_past_only'=>JHtml::_( 'select.option', 'date_past_only', JText::_( 'COM_CCK_MATCH_DATE_PAST_ONLY' ) ),
											'date_past'=>JHtml::_( 'select.option', 'date_past', JText::_( 'COM_CCK_MATCH_DATE_PAST' ) ),
											'date_future'=>JHtml::_( 'select.option', 'date_future', JText::_( 'COM_CCK_MATCH_DATE_FUTURE' ) ),
											'date_future_only'=>JHtml::_( 'select.option', 'date_future_only', JText::_( 'COM_CCK_MATCH_DATE_FUTURE_ONLY' ) ),
											'103'=>JHtml::_( 'select.option', '</OPTGROUP>', '' ),
											'104'=>JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_MATCH_GROUP_EXCLUSION' ) ),
											'not_alpha'=>JHtml::_( 'select.option', 'not_alpha', JText::_( 'COM_CCK_MATCH_NOT_BEGINNING_WITH' ) ),
											'not_empty'=>JHtml::_( 'select.option', 'not_empty', JText::_( 'COM_CCK_MATCH_NOT_EMPTY' ) ),
											'not_zeta'=>JHtml::_( 'select.option', 'not_zeta', JText::_( 'COM_CCK_MATCH_NOT_ENDING_WITH' ) ),
											'not_equal'=>JHtml::_( 'select.option', 'not_equal', JText::_( 'COM_CCK_MATCH_NOT_EQUAL' ) ),
											'not_null'=>JHtml::_( 'select.option', 'not_null', JText::_( 'COM_CCK_MATCH_NOT_NULL' ) ),
											'not_any_exact'=>JHtml::_( 'select.option', 'not_any_exact', JText::_( 'COM_CCK_MATCH_NOT_ANY_WORDS_EXACT' ) ),
											'not_like'=>JHtml::_( 'select.option', 'not_like', JText::_( 'COM_CCK_MATCH_NOT_LIKE' ) ),
											'105'=>JHtml::_( 'select.option', '</OPTGROUP>', '' ),
											'106'=>JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_MATCH_GROUP_GEO_DISTANCE' ) ),
											'radius_higher'=>JHtml::_( 'select.option', 'radius_higher', JText::_( 'COM_CCK_MATCH_RADIUS_HIGHER' ) ),
											'radius_lower'=>JHtml::_( 'select.option', 'radius_lower', JText::_( 'COM_CCK_MATCH_RADIUS_LOWER' ) ),
											'107'=>JHtml::_( 'select.option', '</OPTGROUP>', '' ),
											'108'=>JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_MATCH_GROUP_NULL' ) ),
											'is_null'=>JHtml::_( 'select.option', 'is_null', JText::_( 'COM_CCK_MATCH_IS_NULL' ) ),
											'is_not_null'=>JHtml::_( 'select.option', 'is_not_null', JText::_( 'COM_CCK_MATCH_IS_NOT_NULL' ) ),
											'109'=>JHtml::_( 'select.option', '</OPTGROUP>', '' ),
											'110'=>JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_MATCH_GROUP_NUMERIC' ) ),
											'num_higher_only'=>JHtml::_( 'select.option', 'num_higher_only', JText::_( 'COM_CCK_MATCH_NUMERIC_HIGHER_ONLY' ) ),
											'num_higher'=>JHtml::_( 'select.option', 'num_higher', JText::_( 'COM_CCK_MATCH_NUMERIC_HIGHER' ) ),
											'num_lower'=>JHtml::_( 'select.option', 'num_lower', JText::_( 'COM_CCK_MATCH_NUMERIC_LOWER' ) ),
											'num_lower_only'=>JHtml::_( 'select.option', 'num_lower_only', JText::_( 'COM_CCK_MATCH_NUMERIC_LOWER_ONLY' ) ),
											'111'=>JHtml::_( 'select.option', '</OPTGROUP>', '' ),
											'112'=>JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_MATCH_GROUP_WORDS' ) ),
											'any'=>JHtml::_( 'select.option', 'any', JText::_( 'COM_CCK_MATCH_ANY_WORDS' ) ),
											'any_exact'=>JHtml::_( 'select.option', 'any_exact', JText::_( 'COM_CCK_MATCH_ANY_WORDS_EXACT' ) ),
											'each'=>JHtml::_( 'select.option', 'each', JText::_( 'COM_CCK_MATCH_EACH_WORD' ) ),
											'each_exact'=>JHtml::_( 'select.option', 'each_exact', JText::_( 'COM_CCK_MATCH_EACH_WORD_EXACT' ) ),
											'nested_exact'=>JHtml::_( 'select.option', 'nested_exact', JText::_( 'COM_CCK_MATCH_NESTED_EXACT' ) ),
											'113'=>JHtml::_( 'select.option', '</OPTGROUP>', '' )
										);
				$data['live']		=	array_merge(
											array(
												''=>JHtml::_( 'select.option', '', JText::_( 'COM_CCK_DEFAULT' ) ),
												'stage'=>JHtml::_( 'select.option', 'stage', JText::_( 'COM_CCK_STAGE' ) )
											),
											Helper_Admin::getPluginOptions( 'field_live', 'cck_', false, false, true ) );
				$data['stage']		=	array(
											'0'=>JHtml::_( 'select.option', '0', JText::_( 'COM_CCK_STAGE_FINAL' ) ),
											'100'=>JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_STAGE_TEMP' ) ),
											'1'=>JHtml::_( 'select.option', '1', JText::_( 'COM_CCK_STAGE_1ST' ) ),
											'2'=>JHtml::_( 'select.option', '2', JText::_( 'COM_CCK_STAGE_2ND' ) ),
											'3'=>JHtml::_( 'select.option', '3', JText::_( 'COM_CCK_STAGE_3RD' ) ),
											'4'=>JHtml::_( 'select.option', '4', JText::_( 'COM_CCK_STAGE_4TH' ) ),
											'5'=>JHtml::_( 'select.option', '5', JText::_( 'COM_CCK_STAGE_5TH' ) ),
											'101'=>JHtml::_( 'select.option', '</OPTGROUP>', '' )
										);
				$data['markup']		=	array(
											''=>JHtml::_( 'select.option', '', JText::_( 'COM_CCK_DEFAULT' ) ),
											'none'=>JHtml::_( 'select.option', 'none', JText::_( 'COM_CCK_NONE' ) )
										);
				$data['access']		=	array( 0=>(object)array( 'text'=>JText::_( 'COM_CCK_CLEAR' ), 'value'=>0 ) ) + JCckDatabase::loadObjectList( 'SELECT a.id AS value, a.title AS text FROM #__viewlevels AS a GROUP BY a.id ORDER BY title ASC', 'value' );
				$data['validation']	=	true;
				$data['restriction']=	array_merge(
											array( ''=>JHtml::_( 'select.option', '', JText::_( 'COM_CCK_NONE' ) ) ),
											Helper_Admin::getPluginOptions( 'field_restriction', 'cck_', false, false, true )
										);
			}
		}
		
		return $data;
	}
	
	// getPositions
	public static function getPositions( $element, $item )
	{
		if ( ! $item->id ) {
			return array();
		}
		$query	=	' SELECT a.'.$element.'id, a.position, a.client, a.legend, a.variation, a.variation_options, a.width, a.height, a.css'
				.	' FROM #__cck_core_'.$element.'_position AS a '
				.	' WHERE a.'.$element.'id = '.(int)$item->id.' AND a.client = "'.$item->client.'"'
				;
		$pos	=	JCckDatabase::loadObjectList( $query, 'position' );
		if ( ! $pos ) {
			return array();
		}
		
		return $pos;
	}
	
	// getPositionVariations
	public static function getPositionVariations( $template = '', $default = true )
	{
		$path		=	JPATH_SITE.'/libraries/cck/rendering/variations';
		$variations	=	( $default !== false ) ? array( ''=>'- '.JText::_( 'COM_CCK_INHERITED' ).' -',
													   'empty' => '- '.JText::_( 'COM_CCK_EMPTY' ).' -',
													   'none' => '- '.JText::_( 'COM_CCK_NONE' ).' -'
													)
											  : array( 'none' => '- '.JText::_( 'COM_CCK_NONE' ).' -' );
		
		jimport( 'joomla.filesystem.folder' );
		$list		=	JFolder::folders( $path, '.', false, false, array( 'empty' ) );
		if ( is_array( $list ) && count( $list ) ) {
			$variations[] 	=	JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_LIBRARY' ) );
			$list			=	array_combine ( array_values( $list ), $list );
			$variations		=	array_merge( $variations, $list );
		}
		
		if ( !self::$template && $template ) {
			self::$template	=	$template;
		}
		if ( self::$template != '' ) {
			$path		=	JPATH_SITE.'/templates/'.self::$template.'/variations';
			$list		=	JFolder::folders( $path, '.', false, false );
			if ( is_array( $list ) && count( $list ) ) {
				$variations[] 	=	JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_TEMPLATE' ) );
				$list			=	array_combine( array_values( $list ), $list );
				$variations		=	array_merge( $variations, $list );
			}
		}
		
		return $variations;
	}
	
	// getTemplateParams
	public static function getTemplateParams( $xml, $root = '', $tag = '', $values = '' )
	{
		if ( ! file_exists( $xml ) ) {
			return;
		}
		$params	=	JForm::getInstance( 'cck', $xml, array(), false, $root );
		if ( is_string( $values ) ) {
			$values	=	JCckDev::fromJSON( $values );
		}
		$params->bind( $values ); // Buggy.. or it's just me ?!
		$fieldSets	=	$params->getFieldsets( $tag );
		foreach ( $fieldSets as $name => $fieldSet ) {
			$legend		=	! empty( $fieldSet->label ) ? JText::_( $fieldSet->label ) : ucfirst( $name ) .' '. JText::_( 'COM_CCK_OPTIONS' );
			$class		=	! empty( $fieldSet->class ) ? trim( $fieldSet->class ) : 'adminformlist-2cols';
			?>
            <div class="seblod" id="cck-options-<?php echo htmlspecialchars( $name ); ?>">
                <div class="legend top left"><?php echo ' &rArr; ' . $legend; ?></div>
                <ul class="adminformlist <?php echo $class; ?>">
				<?php
				foreach ( $params->getFieldset( $name ) as $field ) {
					if ( $field->hidden ) {
						echo ( isset( $values[$field->fieldname] ) ) ? $params->getInput( $field->fieldname, $tag, $values[$field->fieldname] ) : $field->input;						
					} else {
						echo '<li>';
						echo JText::_( $field->label );
						// Can't bind the values.. & need more attributes.. so let's play a bit trashy !
						//$name2	=	$params->getFieldAttribute( $field->fieldname, 'name2', '', 'params' );
						//if ( $name2 != '' ) {
						//	$params->setFieldAttribute( $field->fieldname, 'value2', @$values[$name2], 'params' );
						//}
						echo ( isset( $values[$field->fieldname] ) ) ? $params->getInput( $field->fieldname, $tag, $values[$field->fieldname] ) : $field->input;
						echo '</li>';
					}
				}
				?>
                </ul>
            </div><?php
		}
	}
	
	// getTemplateStyle
	public static function getTemplateStyle( $vName, $selected, $default = '', $null = '' )
	{
		$style	=	null;
		
		if ( $selected != $null ) {
			$style	=	JCckDatabase::loadObject( 'SELECT id, title, template, params FROM #__template_styles WHERE id='.(int)$selected );
		} 
		if ( ! $style ) {
			$style	=	JCckDatabase::loadObject( 'SELECT id, title, template, params FROM #__template_styles WHERE template="'.(string)$default.'" ORDER BY id'  );
		}
		if ( ! $style ) {
			return $style;
		}
		
		$lang		=	JFactory::getLanguage();
		$values		=	JCckDev::fromJSON( $style->params );
		$style->xml	=	JPath::clean( JPATH_SITE.'/templates/'.$style->template.'/templateDetails.xml' );
		
		$lang->load( 'tpl_'.$style->template.'.sys', JPATH_SITE, null, false, true );
		$lang->load( 'tpl_'.$style->template, JPATH_SITE, null, false, true );
		$style->positions	=	array();
		
		if ( file_exists( $style->xml ) ) {
			$xml	=	simplexml_load_file( $style->xml );
			if ( isset( $xml->positions[0] ) ) {
				foreach ( $xml->positions[0] as $position ) {
					$toggle	=	true;
					if ( isset( $position->attributes()->toggle ) && (string)$position->attributes()->toggle ) {
						$idx	=	(string)$position->attributes()->toggle;
						$val	=	( isset( $values[$idx] ) ) ? $values[$idx] : 0;
						if ( isset( $position->attributes()->toggle_value ) ) {
							$value	=	(string)$position->attributes()->toggle_value;
							if ( strpos( $value, ',' ) !== false ) {
								$parts	=	explode( ',', $value );
								if ( count( $parts ) ) {
									$toggle2	=	false;
									foreach ( $parts as $v ) {
										if ( $val == $v ) {
											$toggle2	=	true;
										}
									}
									if ( !$toggle2 ) {
										$toggle	=	false;
									}
								}
							} else {
								if ( $val != $value ) {
									$toggle	=	false;
								}	
							}
						} else {
							if ( !$val ) {
								$toggle	=	false;
							}
						}
					}
					if ( $toggle ) {
						$pos				=	(string)$position;
						$key				=	'TPL_'.strtoupper( $style->template ).'_POSITION_'.strtoupper( str_replace( '-', '_', $pos ) );
						$label				=	( $lang->hasKey( $key ) ) ? JText::_( $key ) : $pos;
						$style->positions[]	= 	JHtml::_( 'select.option', $pos, $label );
					}
				}
			}
		}
		
		return $style;
	}
	
	// getTemplateStyleInstance
	public static function getTemplateStyleInstance( $id, $template, $template2, $params, $tag, $force = false )
	{
		if ( !$template ) {
			return 0;
		}
		$default	=	self::getDefaultStyle( $template );
		if ( is_array( $params ) ) {
			$params		=	JCckDev::toJSON( $params );
		}
		$update		=	0;
		
		if ( $template != $template2 ) {
			$id		=	$default->id;
			$update	=	1;	//or ajax reload of template params..
		}
		if ( $id == $default->id ) {
			if ( $params != '{}' && $params != $default->params && $update != 1 ) {
				$ck		=	JCckTable::getInstance( '#__template_styles' );
				$ck->load( $id );
				if ( $ck->id > 0 ) {
					$ck->id		=	0;
					$ck->title	=	$ck->template . ' - ' .$tag;
					$ck->params	=	$params;
					$ck->store();
					$id			=	( $ck->id ) ? $ck->id : $id;
				}
			}
		} else {
			$ck		=	JCckTable::getInstance( '#__template_styles' );
			$ck->load( $id );
			if ( $ck->id > 0 ) {
				if ( $force != false ) {
					$ck->id		=	0;
					$ck->title	=	$ck->template . ' - ' .$tag;
				}
				$ck->params	=	$params;
				$ck->store();
				$id			=	( $ck->id ) ? $ck->id : $id;
			}
		}
		
		return $id;
	}
}
?>