<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: helper_workshop.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Helper
class Helper_Workshop
{
	protected static $template	=	'';
	
	// displayField
	public static function displayField( &$field, $type_field = '' )
	{
		$link	=	'index.php?option=com_cck&task=field.edit&id='.$field->id.'&tmpl=component';
		?><li class="field <?php echo 't-'.$field->type.' f-'.$field->folder.' a-'. strtolower( substr( $field->title, 0, 1 ) ).$type_field; ?>" id="<?php echo $field->id; ?>"><a class="cbox edit" href="<?php echo $link; ?>"></a><span class="title" onDblClick="JCck.Dev.moveDir('<?php echo $field->id; ?>');"><?php echo $field->title; ?><span class="subtitle">(<?php echo JText::_( 'PLG_CCK_FIELD_'.$field->type.'_LABEL2' ); ?>)</span></span><input type="hidden" id="k<?php echo $field->id; ?>" name="ff[<?php echo $field->name; ?>]" value="<?php echo $field->id; ?>" /><?php echo '<div class="move" onClick="JCck.Dev.moveDir('.$field->id.');"></div>'; ?><div class="drag"></div><?php echo @$field->params; ?></li><?php
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
	public static function displayPosition( $p, $name, $title, $legend, $variation, $variation_status, $width, $height, $css )
	{
		$dir	=	'down';
		$to		=	$p + 1;
		$hide	=	( $variation_status != '' ) ? '' : ' hidden';
		
		$pos	=	'<li class="position ui-state-disabled" id="pos-'.$p.'">'
				.	'<a href="#pos-'.$to.'"><img class="left" src="'.JROOT_MEDIA_CCK.'/images/12/icon-12-'.$dir.'.png" alt="" /></a>'
				.	'<input class="selector" type="radio" id="position'.$p.'" name="positions" gofirst="#pos-'.($to-1).'" golast="#pos-'.$to.'" />'
				.	'<span class="title">'.$title.'</span>'
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
		$root		=	JROOT_MEDIA_CCK;
				
		$bar		.=	'<a class="hasTooltip cbox qtip_cck icons icon-add" title="'.JText::_( 'COM_CCK_ADD_FIELD' ).'" href="index.php?option=com_cck&task=field.add&tmpl=component&ajax_state=1&ajax_type=text"></a>'
					.	'<a class="hasTooltip first qtip_cck icons icon-up" title="'.JText::_( 'COM_CCK_FIELDS_MOVE_UP' ).'" href="javascript: JCck.Dev.moveTop();"></a>';
		if ( $uix == 'full' ) {
			$bar	.=	'<a class="hasTooltip qtip_cck icons icon-right" title="'.JText::_( 'COM_CCK_FIELDS_MOVE_RIGHT' ).'" href="javascript: JCck.Dev.move(\'#sortable2\');"></a>'
					.	'<a class="hasTooltip qtip_cck icons icon-left" title="'.JText::_( 'COM_CCK_FIELDS_MOVE_LEFT' ).'" href="javascript: JCck.Dev.move();"></a>';
		}
		$bar		.=	'<a class="hasTooltip qtip_cck icons icon-down" title="'.JText::_( 'COM_CCK_FIELDS_MOVE_DOWN' ).'" href="javascript: JCck.Dev.moveBottom();"></a>';
		
		if ( $element == 'type' ) {
			if ( $master == 'content' ) {
                $bar		.=	'<a class="hasTooltip qtip_cck icons panel icon-one first selected" title="'.JText::_( 'COM_CCK_LABEL' ).' <b>+</b> '.JText::_( 'COM_CCK_VARIATION' ).'" href="javascript:void(0);">1</a>'
							.	'<a class="hasTooltip qtip_cck icons panel icon-two" title="'.JText::_( 'COM_CCK_LINK' ).' <b>+</b> '.JText::_( 'COM_CCK_TYPO' ).'" href="javascript:void(0);">2</a>'
                			.	'<a class="hasTooltip qtip_cck icons panel icon-three" title="'.JText::_( 'COM_CCK_MARKUP' ).' <b>+</b> '.JText::_( 'COM_CCK_MARKUP_CLASS' ).'"href="javascript:void(0);">3</a>'
							.	'<a class="hasTooltip qtip_cck icons panel icon-four" title="'.JText::_( 'COM_CCK_ACCESS' ).' <b>+</b> '.JText::_( 'COM_CCK_RESTRICTION' ).'" href="javascript:void(0);">4</a>';
			} else {
				$bar		.=	'<a class="hasTooltip qtip_cck icons panel icon-one first selected" title="'.JText::_( 'COM_CCK_LABEL' ).' <b>+</b> '.JText::_( 'COM_CCK_VARIATION' ).'" href="javascript:void(0);">1</a>'
							.	'<a class="hasTooltip qtip_cck icons panel icon-two" title="'.JText::_( 'COM_CCK_LIVE' ).' <b>+</b> '.JText::_( 'COM_CCK_LIVE_VALUE' ).'"href="javascript:void(0);">2</a>'
							.	'<a class="hasTooltip qtip_cck icons panel icon-three" title="'.JText::_( 'COM_CCK_REQUIRED_VALIDATION' ).' <b>+</b> '.JText::_( 'COM_CCK_STAGE' ).'" href="javascript:void(0);">3</a>'
							.	'<a class="hasTooltip qtip_cck icons panel icon-four" title="'.JText::_( 'COM_CCK_ACCESS' ).' <b>+</b> '.JText::_( 'COM_CCK_RESTRICTION' ).'" href="javascript:void(0);">4</a>'
							.	'<a class="hasTooltip qtip_cck icons panel icon-five" title="'.JText::_( 'COM_CCK_CONDITIONAL_STATES' ).' <b>+</b> '.JText::_( 'COM_CCK_COMPUTATION' ).'" href="javascript:void(0);">5</a>'
							.	'<a class="hasTooltip qtip_cck icons panel icon-six" title="'.JText::_( 'COM_CCK_MARKUP' ).' <b>+</b> '.JText::_( 'COM_CCK_MARKUP_CLASS' ).'" href="javascript:void(0);">6</a>';
			}
		} else {
			if ( $master == 'content' ) {
				$bar		.=	'<a class="hasTooltip qtip_cck panel icons icon-one first selected" title="'.JText::_( 'COM_CCK_LABEL' ).' <b>+</b> '.JText::_( 'COM_CCK_VARIATION' ).'" href="javascript:void(0);">1</a>'
							.	'<a class="hasTooltip qtip_cck icons panel icon-two" title="'.JText::_( 'COM_CCK_LINK' ).' <b>+</b> '.JText::_( 'COM_CCK_TYPO' ).'" href="javascript:void(0);">2</a>'
							.	'<a class="hasTooltip qtip_cck icons panel icon-three" title="'.JText::_( 'COM_CCK_MARKUP' ).' <b>+</b> '.JText::_( 'COM_CCK_MARKUP_CLASS' ).'"href="javascript:void(0);">3</a>'
							.	'<a class="hasTooltip qtip_cck icons panel icon-four" title="'.JText::_( 'COM_CCK_ACCESS' ).' <b>+</b> '.JText::_( 'COM_CCK_RESTRICTION' ).'" href="javascript:void(0);">4</a>';
			} elseif ( $master == 'search' ) {
				$bar		.=	'<a class="hasTooltip qtip_cck icons panel icon-one first selected" title="'.JText::_( 'COM_CCK_LABEL' ).' <b>+</b> '.JText::_( 'COM_CCK_VARIATION' ).'" href="javascript:void(0);">1</a>'
							.	'<a class="hasTooltip qtip_cck icons panel icon-two" title="'.JText::_( 'COM_CCK_LIVE' ).' <b>+</b> '.JText::_( 'COM_CCK_LIVE_VALUE' ).'"href="javascript:void(0);">2</a>'
							.	'<a class="hasTooltip qtip_cck icons panel icon-three" title="'.JText::_( 'COM_CCK_MATCH' ).' <b>+</b> '.JText::_( 'COM_CCK_STAGE' ).'" href="javascript:void(0);">3</a>'
							.	'<a class="hasTooltip qtip_cck icons panel icon-four" title="'.JText::_( 'COM_CCK_ACCESS' ).' <b>+</b> '.JText::_( 'COM_CCK_RESTRICTION' ).'" href="javascript:void(0);">4</a>'
							.	'<a class="hasTooltip qtip_cck icons panel icon-five" title="'.JText::_( 'COM_CCK_CONDITIONAL_STATES' ).'" href="javascript:void(0);">5</a>'
							.	'<a class="hasTooltip qtip_cck icons panel icon-six" title="'.JText::_( 'COM_CCK_MARKUP' ).' <b>+</b> '.JText::_( 'COM_CCK_MARKUP_CLASS' ).'" href="javascript:void(0);">6</a>'
							.	'<a class="hasTooltip qtip_cck icons panel icon-six" title="'.JText::_( 'COM_CCK_REQUIRED_VALIDATION' ).'" href="javascript:void(0);">7</a>';
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
	public static function getDefaultStyle( $template )
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
				$where	=	'WHERE '.$featured.' AND a.storage != "dev" AND ( a.storage_table NOT LIKE "#__cck_store_form_%" )';
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
	public static function getFieldsAv( $element, $item, $objects, $featured = '' )
	{
		$excluded	=	self::getFields( $element, $item, '', true );
		$where		=	' WHERE a.published = 1 AND b.published = 1';
		if ( $objects != '' ) {
			$where	.=	' AND a.storage_location IN ('.$objects.')';
		}
		if ( $element == 'type' && $item->storage_location != 'none' ) {
			$where		.=	' AND ( a.storage_table NOT LIKE "#__cck_store_form_%" OR a.storage_table ="#__cck_store_form_'.$item->name.'" )';
		}
		if ( $excluded ) {
			$where	.=	' AND a.id NOT IN ('.$excluded.')';
		}
		if ( ! isset ( $item->id ) && $featured != '' ) {
			$where	.=	' AND '.$featured;
		}
		$query	=	' SELECT DISTINCT a.id, a.title, a.name, a.folder, a.type, a.label'
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
		$data		=	array();
		$data['_']	=	array( 'add'=>JText::_( 'COM_CCK_ADD' ), 'configure'=>JText::_( 'COM_CCK_CONFIGURE' ), 'edit'=>JText::_( 'COM_CCK_EDIT' ),
							   'optional'=>JText::_( 'COM_CCK_OPTIONAL' ), 'required'=>JText::_( 'COM_CCK_REQUIRED' ) );
		
		if ( $element == 'type' ) {
			if ( $master == 'content' ) {
				$data['link']		=	array_merge( array( JHtml::_( 'select.option', '', JText::_( 'COM_CCK_NONE' ) ) ), Helper_Admin::getPluginOptions( 'field_link', 'cck_', false, false, true ) );
				$data['typo']		=	array_merge( array( JHtml::_( 'select.option', '', JText::_( 'COM_CCK_NONE' ) ) ), Helper_Admin::getPluginOptions( 'field_typo', 'cck_', false, false, true ) );
				$data['markup']		=	array(
											JHtml::_( 'select.option', '', JText::_( 'COM_CCK_DEFAULT' ) ),
											JHtml::_( 'select.option', 'none', JText::_( 'COM_CCK_NONE' ) )
										);
				$data['access']		=	JCckDatabase::loadObjectList( 'SELECT a.id AS value, a.title AS text FROM #__viewlevels AS a GROUP BY a.id ORDER BY title ASC' );
				$data['restriction']=	array_merge( array( JHtml::_( 'select.option', '', JText::_( 'COM_CCK_NONE' ) ) ), Helper_Admin::getPluginOptions( 'field_restriction', 'cck_', false, false, true ) );
			} else {
				$data['client']		=	$client;
				$data['variation']	=	array( JHtml::_( 'select.option', 'hidden', JText::_( 'COM_CCK_HIDDEN' ) ),
											   JHtml::_( 'select.option', 'value', JText::_( 'COM_CCK_VALUE' ) ),
											   JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_FORM' ) ),
											   JHtml::_( 'select.option', '', JText::_( 'COM_CCK_DEFAULT' ) ),
											   JHtml::_( 'select.option', 'disabled', JText::_( 'COM_CCK_FORM_DISABLED' ) ),
											   JHtml::_( 'select.option', '</OPTGROUP>', '' ) );
				$data['live']		=	array_merge( array( JHtml::_( 'select.option', '', JText::_( 'COM_CCK_DEFAULT' ) ) ), Helper_Admin::getPluginOptions( 'field_live', 'cck_', false, false, true ) );
				$data['stage']		=	array( JHtml::_( 'select.option', 0, JText::_( 'COM_CCK_STAGE_FINAL' ) ),
											   JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_STAGE_TEMP' ) ),
											   JHtml::_( 'select.option', 1, JText::_( 'COM_CCK_STAGE_1ST' ) ),
											   JHtml::_( 'select.option', 2, JText::_( 'COM_CCK_STAGE_2ND' ) ),
											   JHtml::_( 'select.option', 3, JText::_( 'COM_CCK_STAGE_3RD' ) ),
											   JHtml::_( 'select.option', 4, JText::_( 'COM_CCK_STAGE_4TH' ) ),
											   JHtml::_( 'select.option', 5, JText::_( 'COM_CCK_STAGE_5TH' ) ),
											   JHtml::_( 'select.option', '</OPTGROUP>', '' ) );
				$data['markup']		=	array(
											JHtml::_( 'select.option', '', JText::_( 'COM_CCK_DEFAULT' ) ),
											JHtml::_( 'select.option', 'none', JText::_( 'COM_CCK_NONE' ) )
										);
				$data['access']		=	JCckDatabase::loadObjectList( 'SELECT a.id AS value, a.title AS text FROM #__viewlevels AS a GROUP BY a.id ORDER BY title ASC' );
				$data['validation']	=	true;
				$data['restriction']=	array_merge( array( JHtml::_( 'select.option', '', JText::_( 'COM_CCK_NONE' ) ) ), Helper_Admin::getPluginOptions( 'field_restriction', 'cck_', false, false, true ) );
			}
		} else {
			if ( $master == 'order' ) {
				$data['match_mode']	=	array(
											JHtml::_( 'select.option', 'ASC', JText::_( 'COM_CCK_ASCENDING' ) ),
											JHtml::_( 'select.option', 'DESC', JText::_( 'COM_CCK_DESCENDING' ) ),
											JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_CUSTOM' ) ),
											JHtml::_( 'select.option', 'FIELD', JText::_( 'COM_CCK_VALUES' ) ),
											JHtml::_( 'select.option', '</OPTGROUP>', '' )
										);
			} elseif ( $master == 'content' ) {
				$data['link']		=	array_merge( array( JHtml::_( 'select.option', '', JText::_( 'COM_CCK_NONE' ) ) ), Helper_Admin::getPluginOptions( 'field_link', 'cck_', false, false, true ) );
				$data['typo']		=	array_merge( array( JHtml::_( 'select.option', '', JText::_( 'COM_CCK_NONE' ) ) ), Helper_Admin::getPluginOptions( 'field_typo', 'cck_', false, false, true ) );
				$data['markup']		=	array(
											JHtml::_( 'select.option', '', JText::_( 'COM_CCK_DEFAULT' ) ),
											JHtml::_( 'select.option', 'none', JText::_( 'COM_CCK_NONE' ) )
										);
				$data['access']		=	JCckDatabase::loadObjectList( 'SELECT a.id AS value, a.title AS text FROM #__viewlevels AS a GROUP BY a.id ORDER BY title ASC' );
				$data['restriction']=	array_merge( array( JHtml::_( 'select.option', '', JText::_( 'COM_CCK_NONE' ) ) ), Helper_Admin::getPluginOptions( 'field_restriction', 'cck_', false, false, true ) );
			} else {
				$data['client']		=	$client;
				$data['variation']	=	array( JHtml::_( 'select.option', 'hidden', JText::_( 'COM_CCK_HIDDEN' ) ),
											   JHtml::_( 'select.option', 'value', JText::_( 'COM_CCK_VALUE' ) ),
											   JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_FORM' ) ),
											   JHtml::_( 'select.option', '', JText::_( 'COM_CCK_DEFAULT' ) ),
											   JHtml::_( 'select.option', 'form_filter', JText::_( 'COM_CCK_FORM_FILTER' ) ),
											   JHtml::_( 'select.option', 'disabled', JText::_( 'COM_CCK_FORM_DISABLED' ) ),
											   JHtml::_( 'select.option', '</OPTGROUP>', '' ) );
				$data['match_mode']	=	array( JHtml::_( 'select.option', 'none', JText::_( 'COM_CCK_NONE' ) ),
											   JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_MATCH_GROUP_BASIC' ) ),
											   JHtml::_( 'select.option', 'alpha', JText::_( 'COM_CCK_MATCH_BEGINNING_WITH' ) ),
											   JHtml::_( 'select.option', 'empty', JText::_( 'COM_CCK_MATCH_EMPTY' ) ),
											   JHtml::_( 'select.option', 'zeta', JText::_( 'COM_CCK_MATCH_ENDING_WITH' ) ),
											   JHtml::_( 'select.option', 'exact', JText::_( 'COM_CCK_MATCH_EXACT_PHRASE' ) ),
											   JHtml::_( 'select.option', '', JText::_( 'COM_CCK_MATCH_DEFAULT_PHRASE' ) ),
											   JHtml::_( 'select.option', '</OPTGROUP>', '' ),
											   JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_MATCH_GROUP_DATE_AND_TIME' ) ),
											   JHtml::_( 'select.option', 'date_past_only', JText::_( 'COM_CCK_MATCH_DATE_PAST_ONLY' ) ),
											   JHtml::_( 'select.option', 'date_past', JText::_( 'COM_CCK_MATCH_DATE_PAST' ) ),
											   JHtml::_( 'select.option', 'date_future', JText::_( 'COM_CCK_MATCH_DATE_FUTURE' ) ),
											   JHtml::_( 'select.option', 'date_future_only', JText::_( 'COM_CCK_MATCH_DATE_FUTURE_ONLY' ) ),
											   JHtml::_( 'select.option', '</OPTGROUP>', '' ),
											   JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_MATCH_GROUP_EXCLUSION' ) ),
											   JHtml::_( 'select.option', 'not_alpha', JText::_( 'COM_CCK_MATCH_NOT_BEGINNING_WITH' ) ),
											   JHtml::_( 'select.option', 'not_empty', JText::_( 'COM_CCK_MATCH_NOT_EMPTY' ) ),
											   JHtml::_( 'select.option', 'not_zeta', JText::_( 'COM_CCK_MATCH_NOT_ENDING_WITH' ) ),
											   JHtml::_( 'select.option', 'not_equal', JText::_( 'COM_CCK_MATCH_NOT_EQUAL' ) ),
											   JHtml::_( 'select.option', 'not_null', JText::_( 'COM_CCK_MATCH_NOT_NULL' ) ),
											   JHtml::_( 'select.option', 'not_any_exact', JText::_( 'COM_CCK_MATCH_NOT_ANY_WORDS_EXACT' ) ),
											   JHtml::_( 'select.option', 'not_like', JText::_( 'COM_CCK_MATCH_NOT_LIKE' ) ),
											   JHtml::_( 'select.option', '</OPTGROUP>', '' ),
											   JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_MATCH_GROUP_GEO_DISTANCE' ) ),
											   JHtml::_( 'select.option', 'radius_higher', JText::_( 'COM_CCK_MATCH_RADIUS_HIGHER' ) ),
											   JHtml::_( 'select.option', 'radius_lower', JText::_( 'COM_CCK_MATCH_RADIUS_LOWER' ) ),
											   JHtml::_( 'select.option', '</OPTGROUP>', '' ),
											   JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_MATCH_GROUP_NULL' ) ),
											   JHtml::_( 'select.option', 'is_null', JText::_( 'COM_CCK_MATCH_IS_NULL' ) ),
											   JHtml::_( 'select.option', 'is_not_null', JText::_( 'COM_CCK_MATCH_IS_NOT_NULL' ) ),
											   JHtml::_( 'select.option', '</OPTGROUP>', '' ),
											   JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_MATCH_GROUP_NUMERIC' ) ),
											   JHtml::_( 'select.option', 'num_higher_only', JText::_( 'COM_CCK_MATCH_NUMERIC_HIGHER_ONLY' ) ),
											   JHtml::_( 'select.option', 'num_higher', JText::_( 'COM_CCK_MATCH_NUMERIC_HIGHER' ) ),
											   JHtml::_( 'select.option', 'num_lower', JText::_( 'COM_CCK_MATCH_NUMERIC_LOWER' ) ),
											   JHtml::_( 'select.option', 'num_lower_only', JText::_( 'COM_CCK_MATCH_NUMERIC_LOWER_ONLY' ) ),
											   JHtml::_( 'select.option', '</OPTGROUP>', '' ),
											   JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_MATCH_GROUP_WORDS' ) ),
											   JHtml::_( 'select.option', 'any', JText::_( 'COM_CCK_MATCH_ANY_WORDS' ) ),
											   JHtml::_( 'select.option', 'any_exact', JText::_( 'COM_CCK_MATCH_ANY_WORDS_EXACT' ) ),
											   JHtml::_( 'select.option', 'each', JText::_( 'COM_CCK_MATCH_EACH_WORD' ) ),
											   JHtml::_( 'select.option', 'each_exact', JText::_( 'COM_CCK_MATCH_EACH_WORD_EXACT' ) ),
											   JHtml::_( 'select.option', 'nested_exact', JText::_( 'COM_CCK_MATCH_NESTED_EXACT' ) ),
											   JHtml::_( 'select.option', '</OPTGROUP>', '' ) );
				$data['live']		=	array_merge( array( JHtml::_( 'select.option', '', JText::_( 'COM_CCK_DEFAULT' ) ),
															JHtml::_( 'select.option', 'stage', JText::_( 'COM_CCK_STAGE' ) ) 
														), Helper_Admin::getPluginOptions( 'field_live', 'cck_', false, false, true ) );
				$data['stage']		=	array( JHtml::_( 'select.option', 0, JText::_( 'COM_CCK_STAGE_FINAL' ) ),
											   JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_STAGE_TEMP' ) ),
											   JHtml::_( 'select.option', 1, JText::_( 'COM_CCK_STAGE_1ST' ) ),
											   JHtml::_( 'select.option', 2, JText::_( 'COM_CCK_STAGE_2ND' ) ),
											   JHtml::_( 'select.option', 3, JText::_( 'COM_CCK_STAGE_3RD' ) ),
											   JHtml::_( 'select.option', 4, JText::_( 'COM_CCK_STAGE_4TH' ) ),
											   JHtml::_( 'select.option', 5, JText::_( 'COM_CCK_STAGE_5TH' ) ),
											   JHtml::_( 'select.option', '</OPTGROUP>', '' ) );
				$data['markup']		=	array(
											JHtml::_( 'select.option', '', JText::_( 'COM_CCK_DEFAULT' ) ),
											JHtml::_( 'select.option', 'none', JText::_( 'COM_CCK_NONE' ) )
										);
				$data['access']		=	JCckDatabase::loadObjectList( 'SELECT a.id AS value, a.title AS text FROM #__viewlevels AS a GROUP BY a.id ORDER BY title ASC' );
				$data['validation']	=	true;
				$data['restriction']=	array_merge( array( JHtml::_( 'select.option', '', JText::_( 'COM_CCK_NONE' ) ) ), Helper_Admin::getPluginOptions( 'field_restriction', 'cck_', false, false, true ) );
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
		$variations	=	( $default !== false ) ? array( ''=>'- '.JText::_( 'COM_CCK_DEFAULT' ).' -',
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
								$values	=	explode( ',', $value );
								if ( count( $values ) ) {
									$toggle2	=	false;
									foreach ( $values as $v ) {
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
						$key				=	'TPL_'.$style->template.'_POSITION_'.$pos;
						$label				=	( $lang->hasKey( $key ) ) ? JText::_( 'TPL_'.$style->template.'_POSITION_'.$pos ) : $pos;
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