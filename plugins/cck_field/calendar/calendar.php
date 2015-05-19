<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class plgCCK_FieldCalendar extends JCckPluginField
{
	protected static $type		=	'calendar';
	protected static $friendly	=	1;
	protected static $path;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// onCCK_FieldConstruct
	public function onCCK_FieldConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		parent::g_onCCK_FieldConstruct( $data );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_FieldPrepareContent
	public function onCCK_FieldPrepareContent( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		parent::g_onCCK_FieldPrepareContent( $field, $config );
		
		// Set
		$value	=	trim( $value );
		if ( $value == '' || $value == '0000-00-00 00:00:00' ) {
			$field->value	=	'';
			$field->text	=	'';
		} else {
			$field->value	=	$value;
			$options2		=	JCckDev::fromJSON( $field->options2 );
			$options2['storage_format']	=	( isset( $options2['storage_format'] ) ) ? $options2['storage_format'] : '0';
			$value			=	( trim( $value ) == '' ) ? '' : ( ( $options2['storage_format'] == '0' ) ? strtotime ( $value ) : $value );		
			$field->text	=	( $value == '' ) ? '' : self::_getDateByLang( @$options2['format'], $value );
		}
		$field->typo_target	=	'text';
	}
	
	// onCCK_FieldPrepareForm
	public function onCCK_FieldPrepareForm( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		self::$path	=	parent::g_getPath( self::$type.'/' );
		parent::g_onCCK_FieldPrepareForm( $field, $config );
		
		// Init
		$options2	=	JCckDev::fromJSON( $field->options2 );
		if ( count( $inherit ) ) {
			$id		=	( isset( $inherit['id'] ) && $inherit['id'] != '' ) ? $inherit['id'] : $field->name;
			$name	=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
		} else {
			$id		=	$field->name;
			$name	=	$field->name;
		}
		
		$value		=	( trim( $value ) != '' ) ? trim( $value ) : trim( $field->defaultvalue );
		if ( trim( $value ) == '' || $value == '0000-00-00 00:00:00' ) {
			$Jdate		=	'';
			$value		=	'';
			$storedDate	=	'';
		} else {
			$options2['storage_format']	=	( isset( $options2['storage_format'] ) ) ? $options2['storage_format'] : '0';
			$value	=	( $options2['storage_format'] == '0' ) ? strtotime( $value ) : $value;
			$Jdate	=	date( 'Y-m-d H:i:s',  $value  );
			$storedDate	=	date( 'Ymd', $value );
			$value	=	date( @$options2['format'], $value  );
		}
		$default_hour	=	@$options2['default_hour'];
		$default_min	=	@$options2['default_min'];
		$default_sec	=	@$options2['default_sec'];
		$format_jscal2	=	self::_toJScal2Format( array( 'format'=>@$options2['format'], 'default_hour'=>$default_hour, 'default_min'=>$default_min, 'default_sec'=>$default_sec, 'time'=>$options2['time'] ) );

		// Validate
		$validate	=	'';
		if ( $config['doValidation'] > 1 ) {
			plgCCK_Field_ValidationRequired::onCCK_Field_ValidationPrepareForm( $field, $id, $config );
			$validate	=	( count( $field->validate ) ) ? ' validate['.implode( ',', $field->validate ).']' : '';
		}
		
		// Prepare
		if ( strpos( $name, '[]' ) !== false ) { //FieldX
			$nameH	=	substr( $name, 0, -2 );
			$form_more	=	'<input class="inputbox" type="hidden" id="'.$id.'_hidden" name="'.$nameH.'_hidden[]" value="'.$Jdate.'" />';
		} elseif ( $name[(strlen($name) - 1 )] == ']' ) { //GroupX
			$nameH	=	substr( $name, 0, -1 );
			$form_more	=	'<input class="inputbox" type="hidden" id="'.$id.'_hidden" name="'.$nameH.'_hidden]" value="'.$Jdate.'" />';
		} else { //Default
			$form_more	=	'<input class="inputbox" type="hidden" id="'.$id.'_hidden" name="'.$name.'_hidden" value="'.$Jdate.'" />';
		}
		$class		=	'inputbox text'.$validate . ( $field->css ? ' '.$field->css : '' );
		$maxlen		=	( $field->maxlength > 0 ) ? ' maxlength="'.$field->maxlength.'"' : '';
		$readonly	=	( $field->bool2 ) ? '' : ' readonly="readonly"';
		$attr		=	'class="'.$class.'" size="'.$field->size.'"'.$readonly.$maxlen . ( $field->attributes ? ' '.$field->attributes : '' );
		$form		=	'<input type="text" id="'.$id.'" name="'.$name.'" value="'.$value.'" '.$attr.' />'
					.	$form_more;
		
		// Set
		if ( ! $field->variation ) {
			if ( JCck::on() ) {
				$form		.=	'<button class="btn" id="'.$id.'-trigger"><span class="icon-calendar"></span></button>';
			} else {
				$form		.=	'<img src="'.self::$path.'assets/images/calendar.png" alt="Calendar" class="calendar" id="'.$id.'-trigger" />';	
			}
			$form			.=	self::_addScript( $id, array( 'dateFormat' => $format_jscal2, 'time' => @$options2['time'], 
								'weekNumbers' => @$options2['week_numbers'], 'timePos' => @$options2['time_pos'], 'dates' => @$options2['dates'], 'storedDate' => $storedDate,
								'default_hour' => $default_hour, 'default_min' => $default_min, 'default_sec' => $default_sec, 'type' => 'form', 'input_text'=>$field->bool2 ) );
			$field->form			=	$form;
			$field->markup_class	.=	' input-append';
			self::_addScripts( array( 'theme'=>@$options2['theme'] ) );
		} else {
			parent::g_getDisplayVariation( $field, $field->variation, $value, $value, $form, $id, $name, '<input', '', '', $config );
		}
		$field->value	=	$Jdate; //$value;
		
		// Return
		if ( $return === true ) {
			return $field;
		}
	}
	
	// onCCK_FieldPrepareResource
	public function onCCK_FieldPrepareResource( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}

		$field->data	=	( $value != '' && $value != '0000-00-00 00:00:00' ) ? JFactory::getDate( $value )->format( 'Y-m-d\TH:i:s\Z' ) : '0000-00-00T00:00:00Z';
	}

	// onCCK_FieldPrepareSearch
	public function onCCK_FieldPrepareSearch( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		self::$path	=	parent::g_getPath( self::$type.'/' );
		parent::g_onCCK_FieldPrepareForm( $field, $config );
		
		// Init
		$options2	=	JCckDev::fromJSON( $field->options2 );
		if ( count( $inherit ) ) {
			$id		=	( isset( $inherit['id'] ) && $inherit['id'] != '' ) ? $inherit['id'] : $field->name;
			$name	=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
		} else {
			$id		=	$field->name;
			$name	=	$field->name;
		}
		
		$value		=	( trim( $value ) != '' ) ? trim( $value ) : trim( $field->defaultvalue );
		if ( trim( $value ) == '' || $value == '0000-00-00 00:00:00' ) {
			$Jdate		=	'';
			$value		=	'';
			$storedDate	=	'';
		} else {
			$options2['storage_format']	=	( isset( $options2['storage_format'] ) ) ? $options2['storage_format'] : '0';
			$value	=	( $options2['storage_format'] == '0' ) ? strtotime( $value ) : $value;
			$Jdate	=	date( 'Y-m-d H:i:s',  $value  );
			$storedDate	=	date( 'Ymd', $value );
			$value	=	date( @$options2['format'], $value  );
		}
		$default_hour	=	@$options2['default_hour'];
		$default_min	=	@$options2['default_min'];
		$default_sec	=	@$options2['default_sec'];
		$format_jscal2	=	self::_toJScal2Format( array( 'format'=>@$options2['format'], 'default_hour'=>$default_hour, 'default_min'=>$default_min, 'default_sec'=>$default_sec, 'time'=>$options2['time'] ) );
		$format_search	=	self::_toSearchFormat( array( 'format'=>@$options2['format'], 'default_hour'=>$default_hour, 'default_min'=>$default_min, 'default_sec'=>$default_sec, 'time'=>$options2['time'] ) );

		// Validate
		$validate	=	'';
		if ( $config['doValidation'] > 1 ) {
			plgCCK_Field_ValidationRequired::onCCK_Field_ValidationPrepareForm( $field, $id, $config );
			$validate	=	( count( $field->validate ) ) ? ' validate['.implode( ',', $field->validate ).']' : '';
		}
		
		// Prepare
		if ( strpos( $name, '[]' ) !== false ) { //FieldX
			$nameH	=	substr( $name, 0, -2 );
			$form_more	=	'<input class="inputbox" type="hidden" id="'.$id.'" name="'.$nameH.'[]" value="'.$Jdate.'" />';
		} elseif ( $name[(strlen($name) - 1 )] == ']' ) { //GroupX
			$nameH	=	substr( $name, 0, -1 );
			$form_more	=	'<input class="inputbox" type="hidden" id="'.$id.'" name="'.$nameH.']" value="'.$Jdate.'" />';
		} else { //Default
			$form_more	=	'<input class="inputbox" type="hidden" id="'.$id.'" name="'.$name.'" value="'.$Jdate.'" />';
		}
		$class		=	'inputbox text'.$validate . ( $field->css ? ' '.$field->css : '' );
		$maxlen		=	( $field->maxlength > 0 ) ? ' maxlength="'.$field->maxlength.'"' : '';
		$readonly	=	( $field->bool2 ) ? '' : ' readonly="readonly"';
		$attr		=	'class="'.$class.'" size="'.$field->size.'"'.$readonly.$maxlen . ( $field->attributes ? ' '.$field->attributes : '' );
		$form		=	'<input type="text" id="'.$id.'_hidden" name="'.$name.'_hidden" value="'.$value.'" '.$attr.' />'
					.	$form_more;
		
		// Set
		if ( ! $field->variation ) {
			if ( JCck::on() ) {
				$form		.=	'<button class="btn" id="'.$id.'_hidden-trigger"><span class="icon-calendar"></span></button>';
			} else {
				$form		.=	'<img src="'.self::$path.'assets/images/calendar.png" alt="Calendar" class="calendar" id="'.$id.'_hidden-trigger" />';	
			}
			$form			.=	self::_addScript( $id, array( 'dateFormat' => $format_jscal2, 'time' => @$options2['time'], 
								'weekNumbers' => @$options2['week_numbers'], 'timePos' => @$options2['time_pos'], 'dates' => @$options2['dates'], 'storedDate' => $storedDate,
								'default_hour' => $default_hour, 'default_min' => $default_min, 'default_sec' => $default_sec, 'type' => 'search', 'input_text'=>$field->bool2 ) );
			$field->form			=	$form;
			$field->markup_class	.=	' input-append';
			self::_addScripts( array( 'theme'=>@$options2['theme'] ) );
		} else {
			parent::g_getDisplayVariation( $field, $field->variation, $value, $value, $form, $id, $name, '<input', '', '', $config );
		}
		
		// Set
		$field->value	=	( $Jdate == '' ) ? '' : date( $format_search, strtotime( $Jdate ) );
		
		// Return
		if ( $return === true ) {
			return $field;
		}
	}
	
	// onCCK_FieldPrepareStore
	public function onCCK_FieldPrepareStore( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		
		// Init
		if ( count( $inherit ) ) {
			$name	=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
			$xk		=	( isset( $inherit['xk'] ) ) ? $inherit['xk'] : -1;
			$value	=	( isset( $inherit['post'] ) ) ? $inherit['post'][$name.'_hidden'] : @$config['post'][$name.'_hidden'];
		} else {
			$name	=	$field->name;
			$xk		=	-1;
			$value	=	@$config['post'][$name.'_hidden'];
		}
		if ( is_array( $value ) ) {
			$value	=	trim( $value[$xk] );
		}
		$options2	=	JCckDev::fromJSON( $field->options2 );
		$options2['storage_format']	=	( isset( $options2['storage_format'] ) ) ? $options2['storage_format'] : '0';
		$value	=	( $options2['storage_format'] == '0' ) ? $value : strtotime( $value );
    		
		// Validate
		parent::g_onCCK_FieldPrepareStore_Validation( $field, $name, $value, $config );
		
		// Set or Return
		if ( $return === true ) {
			return $value;
		}
		
		$field->value	=	$value;
		parent::g_onCCK_FieldPrepareStore( $field, $name, $value, $config );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Render
	
	// onCCK_FieldRenderContent
	public static function onCCK_FieldRenderContent( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderContent( $field, 'text' );
	}
	
	// onCCK_FieldRenderForm
	public static function onCCK_FieldRenderForm( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderForm( $field );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff & Script
	
	// _addScripts
	protected static function _addScripts( $params = array() )
	{
		static $loaded	=	0;
		if ( $loaded ) {
			return;
		}
		
		$doc	=	JFactory::getDocument();
		$lang	=	JFactory::getLanguage();
		$loaded	=	1;
		
		$doc->addStyleSheet( self::$path.'assets/css/jscal2.css' );
		$doc->addStyleSheet( self::$path.'assets/css/border-radius.css' );
		$doc->addStyleSheet( self::$path.'assets/css/theme/'.$params['theme'].'/'.$params['theme'].'.css' );
		$doc->addScript( self::$path.'assets/js/jscal2.js' );
		$doc->addScript( self::$path.'assets/js/lang/'.self::_getTag_Lang( $lang ).'.js' );
	}
	
	// _addScript
	protected static function _addScript( $id, $params = array() )
	{
		$doc	=	JFactory::getDocument();
		$js	=	'
				<script type="text/javascript">
					var cal = Calendar.setup({';
		if ( $params['type'] == 'form' ) {
			$id1	=	$id;
			$id2	=	$id.'_hidden';
			$js		.=	'trigger	: "'.$id.'-trigger",';
			$js		.=	'inputField	: "'.$id.'",';
		} else {
			$id1	=	$id.'_hidden';
			$id2	=	$id;
			$js		.=	'trigger	: "'.$id.'_hidden-trigger",';
			$js		.=	'inputField	: "'.$id.'_hidden",';
		}
		$js	.=	'dateFormat	: "'.$params['dateFormat'].'",
				weekNumbers	: '.( $params['weekNumbers'] ? 'true' : 'false' ).',
				timePos		: "'.$params['timePos'].'",';
		if ( $params['storedDate'] != '' ) {
			$js .= 'date	: '.$params['storedDate'].',';
		}
		$js .=		'	showTime	: '.( $params['time'] ? $params['time'] : 'false' );
		if ( $params['dates'] != '0' ) {
			$js	.=	',';
		}
		$js	.=	self::_availableDates( array( 'dates' => $params['dates'] ) );
		$js	.=	',	onSelect	: function(cal) { 
								var sel_date = this.selection.get();
								var hours	=	cal.getHours();
								var minutes	=	cal.getMinutes();
								var sel_date = Calendar.intToDate(sel_date);
								sel_date.setHours(hours);
								sel_date.setMinutes(minutes);';
		$js	.=	( $params['time'] == '0' ) ? 'var Jdate = Calendar.printDate(sel_date, "%Y-%m-%d '.$params['default_hour'].':'.$params['default_min'].':'.$params['default_sec'].'");' : 'var Jdate = Calendar.printDate(sel_date, "%Y-%m-%d %H:%M:00");';
		$js	.=	( $params['type'] == 'form' ) ? 'jQuery("#'.$id.'_hidden").val(Jdate);' : 'jQuery("#'.$id.'").val(Jdate) ;';
		$js	.=	'this.hide(); jQuery("#'.$id.'").trigger("change"); }';
		$js	.=		'});';
		if ( $params['input_text'] ) {
			$js	.=	'jQuery(document).ready(function($){ $("#'.$id1.'").live("change", function() { $("#'.$id2.'").val($("#'.$id1.'").val()); }); });';
		}
		$js	.=	'</script>
				';
				
		return $js;
	}
	
	// _toSearchFormat
	protected static function _toSearchFormat( $params = array() )
	{
		$pos_hour1	=	stripos( $params['format'], 'g' );
		$pos_hour2	=	stripos( $params['format'], 'h' );
		$pos_min	=	stripos( $params['format'], 'i' );
		
		if ($params['time'] == '0' ) {
			$format_hour	=	$params['default_hour'];
			$format_min		=	$params['default_min'];
			$format_sec		=	$params['default_sec'];
		} else {
			if ( $pos_hour1 !== false || $pos_hour2 !== false ) {
				$format_hour	=	'H';
			} else {
				$format_hour	=	'00';
			}
			if ( $pos_min !== false ) {
				$format_min		=	'i';
			} else {
				$format_min		=	'00';
			}
			$format_sec			=	'00';
		}
		
		return 'Y-m-d '.$format_hour.':'.$format_min.':'.$format_sec;
	}

	// _toJScal2Format
	protected static function _toJScal2Format( $params = array() )
	{
		if ( $params['time'] == '0' ) {
			$default_hour	=	$params['default_hour'] % 12;
			$hour_G			=	(int)$params['default_hour'];
			$hour_g			=	$default_hour;
			$hour_H			=	$params['default_hour'];
			$hour_h			=	str_pad( $default_hour, 2, '0' , STR_PAD_LEFT );
			$pre_trans		=	array( 'H'=>$hour_H, 'h'=>$hour_h, 'G'=>$hour_G, 'g'=>$hour_g, 'i'=>$params['default_min'] );
		} else {
			$pre_trans		=	array( 'H'=>'%H', 'h'=>'%I', 'G'=>'%k', 'g'=>'%l', 'i'=>'%M' );
		}
		$format	=	strtr( $params['format'], $pre_trans );
		$trans	=	array( 'D'=>'%a', 'l'=>'%A', 'M'=>'%b', 'F'=>'%B', 'd'=>'%d', 'j'=>'%e', 'z'=>'%j', 'm'=>'%m', 'n'=>'%o', 'i'=>'%M',
						   'A'=>'%p', 'a'=>'%P', 'W'=>'%W', 'N'=>'%u', 'w'=>'%w', 'y'=>'%y', 'Y'=>'%Y', 'S'=>'', 's'=>'%S', '%H'=>'%H', '%I'=>'%I', '%k'=>'%k', '%l'=>'%l', '%M'=>'%M' );
		
		return strtr( $format, $trans );
	}
	
	// _availableDates
	protected static function _availableDates( $params = array() )
	{
		switch ( $params['dates'] ) {
			case '1':	return '
						max	: '.date("Ymd", time()-86400).',
						bottomBar	: false';
			case '2':	return '
						max			: '.date("Ymd");
			case '3':	return '
						min			: '.date("Ymd");
			case '4':	return '
						min	: '.date("Ymd", time()+86400).',
						bottomBar	: false';
			case '0':
			default:
				return '';
		}
	}

	// _getDateByLang
	protected static function _getDateByLang( $format, $date_n )
	{
		$date_eng		=	date( $format, $date_n );
		$month_short	=	date( 'M', $date_n );
		$month			=	date( 'F', $date_n );		
		$t_month_short	=	JText::_( strtoupper( $month ).'_SHORT' );
		$t_month		=	JText::_( strtoupper( $month ) );
		$day_short		=	date( 'D', $date_n );
		$t_day_short	=	JText::_( strtoupper( $day_short ) );
		$day			=	date( 'l', $date_n );
		$t_day			=	JText::_( strtoupper( $day ) );
		
		$before	=	array( $month, $day );
		$after	=	array( $t_month, $t_day );
		$date	=	str_replace( $before, $after, $date_eng );
		$before	=	array( $month_short, $day_short );
		$after	=	array( $t_month_short, $t_day_short );
		$date	=	str_replace( $before, $after, $date );
		
		return $date;
	}

	// _getTag_Lang
	protected static function _getTag_Lang( $lang )
	{
		switch ( $lang->getTag() ) {
			case 'cs-CZ':
				$tag	=	'cz';
				break;
			case 'zh-CN':
				$tag	=	'cn';
				break;
			case 'zh-TW':
				$tag	=	'cn';
				break;
			default:
				$tag	=	substr( $lang->getTag(), 0, 2 );
				break;
		}
		
		return $tag;
	}

	// isFriendly
	public static function isFriendly()
	{
		return self::$friendly;
	}
}
?>