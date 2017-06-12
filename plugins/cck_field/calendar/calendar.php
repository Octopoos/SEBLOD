<?php
/**
 * @version 			SEBLOD 3.x Core
 * @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
 * @url				https://www.seblod.com
 * @editor			Octopoos - www.octopoos.com
 * @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
 * @license 			GNU General Public License version 2 or later; see _LICENSE.php
 **/

defined( '_JEXEC' ) or die;

// Plugin
class plgCCK_FieldCalendar extends JCckPluginField
{
	protected static $type		=	'calendar';
	protected static $friendly	=	1;
	protected static $path;

	protected $userTimeZone		=	null;

	// __construct
	public function __construct( &$subject, $config = array() )
	{
		$this->setLocale();
		$this->userTimeZone	=	new DateTimeZone( JFactory::getUser()->getParam( 'timezone', JFactory::getConfig()->get( 'offset' ) ) );

		parent::__construct( $subject, $config );
	}

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
			$date			=	JFactory::getDate( $value, 'UTC' );
			$date->setTimezone( $this->userTimeZone );

			// Transform the date string.
			$value			=	$date->format( 'Y-m-d H:i:s', true, true );

			$this->_setText($field, $value, $date);

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

		$value	=	trim( $value );

		if ( !empty($value) && (isset($config['isNew']) && $config['isNew'] == 1 ) ) {
			// If this is a new item non-empty value comes from live, process it to take care of today, now, offsets
			$value = JFactory::getDate( $value, 'UTC' )->toSql();
			$field->value	=	$value;
		}

		// If value is still empty try with default, process it to take care of today, now, offsets
		$field->defaultvalue	=	trim( $field->defaultvalue );
		if ( empty($value) && !empty( $field->defaultvalue ) ) {
			$value	=  JFactory::getDate( trim($field->defaultvalue), 'UTC' )->toSql();
			$field->value	=	$value;
		}


		if ( empty( $value ) || $value == '0000-00-00 00:00:00' ) {
			$hiddenValue		=	'';
			$displayValue		=	'';
			$scriptDate         =	'';
		} else {
			$date		=	JFactory::getDate( $value, 'UTC' );

			if ( $config['client'] != '' ) {
				$date->setTimezone( $this->userTimeZone );
			}

			// Transform the date string.
			$hiddenValue						=	$date->format( 'Y-m-d H:i:s', true, true );
			$options2['storage_format']         =	( isset( $options2['storage_format'] ) ) ? $options2['storage_format'] : '0';
			$scriptDate				            =	$date->format( 'Ymd', true, true );
			$displayValue   					=	$date->format( @$options2['format'], true, true );
		}

		$default_hour	=	@$options2['default_hour'];
		$default_min	=	@$options2['default_min'];
		$default_sec	=	@$options2['default_sec'];
		$format_jscal2	=	self::_toJScal2Format( array( 'format'=>@$options2['format'], 'default_hour'=>$default_hour, 'default_min'=>$default_min, 'default_sec'=>$default_sec, 'time'=>$options2['time'] ) );

		// Validate
		$validate	=	'';
		if ( $config['doValidation'] > 1 ) {
			plgCCK_Field_ValidationRequired::onCCK_Field_ValidationPrepareForm( $field, $id, $config );
			parent::g_onCCK_FieldPrepareForm_Validation( $field, $id, $config );
			$validate	=	( count( $field->validate ) ) ? ' validate['.implode( ',', $field->validate ).']' : '';
		}

		$class		=	'inputbox text'.$validate . ( $field->css ? ' '.$field->css : '' );
		$maxlen		=	( $field->maxlength > 0 ) ? ' maxlength="'.$field->maxlength.'"' : '';
		$readonly	=	( $field->bool2 ) ? '' : ' readonly="readonly"';
		$attr		=	'class="'.$class.'" size="'.$field->size.'"'.$readonly.$maxlen . ( $field->attributes ? ' '.$field->attributes : '' );
		$visibleForm		=	'<input type="text" id="'.$id.'" name="'.$name.'" value="'.$displayValue.'" '.$attr.' />';

		// Prepare
		if (strpos($name, '[]') !== false) { //FieldX
			$nameSearched = substr($name, 0, -2);
		} elseif ($name[(strlen($name) - 1)] == ']') { //GroupX
			$nameSearched = substr($name, 0, -1);
		} else { //Default
			$nameSearched = $name;
		}

		$nameH = str_replace($nameSearched, $nameSearched.'_hidden', $name);
		$nameS = str_replace($nameSearched, $nameSearched.'_datasource', $name);

		$hiddenForm	=	'<input class="inputbox" type="hidden" id="'.$id.'_hidden" name="'.$nameH.'" value="'.$hiddenValue.'" />';
		$hiddenForm	.=	'<input class="inputbox" type="hidden" id="'.$id.'_datasource" name="'.$nameS.'" value="computed" />';

		if (  !in_array($field->variation,array('value', 'disabled')) ) {

			if ( isset( $field->markup_class ) ) {
				$field->markup_class	.=	' input-append';
			} else {
				$field->markup_class	=	' input-append';
			}

			$visibleForm			.=	'<button class="btn btn-default" id="'.$id.'-trigger"><span class="icon-calendar"></span></button>';
			$visibleForm			.=	self::_addScript( $id, array( 'dateFormat' => $format_jscal2, 'time' => @$options2['time'],
			                                                            'weekNumbers' => @$options2['week_numbers'], 'timePos' => @$options2['time_pos'], 'dates' => @$options2['dates'], 'scriptDate' => $scriptDate,
			                                                            'default_hour' => $default_hour, 'default_min' => $default_min, 'default_sec' => $default_sec, 'type' => 'form', 'input_text'=>$field->bool2 ) );
		}

		self::_addScripts( array( 'theme'=>@$options2['theme'] ) );

		// Set
		if ( ! $field->variation ) {
			$field->form			=	$visibleForm . $hiddenForm;
		} else {
			parent::g_getDisplayVariation( $field, $field->variation, $value, $displayValue, $visibleForm, $id, $name, '<input', '', $hiddenForm, $config );
		}


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

		$input			=	JFactory::getApplication()->input;
		$name			=	$field->name;
		$valueHidden	=	$input->getString( $name.'_hidden' );
		$valueReal	    =	$input->getString( $name );
		$datasource 	=	$input->getString( $name.'_datasource' );

		if ( $datasource == "computed" ) {
			$value = $valueHidden;
		}

		$date		=	null;
		$options2	=	JCckDev::fromJSON( $field->options2 );

		// $value comes in user timezone, needs to be transformed. If $valueReal is empty value is NOW or similar
		if ( ( trim( $valueReal ) != '0000-00-00 00:00:00' ) && !empty( $valueReal ) ) {
			// Return an SQL formatted datetime string in UTC.
			$locale = $this->setLocale();

			// If data was created by script we have in fixed format, else we need to parse string
			if ( $datasource == "computed" ) {
				$date	=	JDate::createFromFormat( 'Y-m-d H:i:s', $value, $this->userTimeZone );
			} else {
				$date	=	JDate::createFromFormat( $options2['format'], $value, $this->userTimeZone );
			}

			if ( $date == false ) {
				throw new OutOfBoundsException( 'You either used wrong format or locale set by language file is not supported on your server - ' .$locale );
			} else {
				$value	=	$date->format( 'Y-m-d H:i:s' );
			}

			$date	=	JFactory::getDate( $value, $this->userTimeZone );

			// pass value as UTC to prepareForm
			$date->setTimezone(new DateTimeZone('UTC'));

			if ( $options2['storage_format'] == '0' ) {
				$value	=	$date->toSql();
			} else {
				$value	=	$date->toUnix();
			}
		}
		else
		{
			// Value comes from live, set new to 1 so that it will be processed by onCCK_FieldPrepareForm
			$config['isNew'] = 1;
		}

		// Set
		$field->value	=	$value;

		// Prepare
		$this->onCCK_FieldPrepareForm( $field, $value, $config, $inherit, $return );

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
			$name			=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
			$xk				=	( isset( $inherit['xk'] ) ) ? $inherit['xk'] : -1;
			$valueHidden	=	( isset( $inherit['post'] ) ) ? $inherit['post'][$name.'_hidden'] : @$config['post'][$name.'_hidden'];
			$datasource		=	( isset( $inherit['post'] ) ) ? $inherit['post'][$name.'_datasource'] : @$config['post'][$name.'_datasource'];
		}
		else
		{
			$name			=	$field->name;
			$xk				=	-1;
			$valueHidden	=	@$config['post'][$name.'_hidden'];
			$datasource 	=	@$config['post'][$name.'_datasource'];
		}

		if ( is_array( $value ) ) {
			$value	=	trim( $value[$xk] );
		}

		if ( $datasource == "computed" ) {
			$value	=	$valueHidden;
		}

		// Validate
		parent::g_onCCK_FieldPrepareStore_Validation( $field, $name, $value, $config );

		$date		=	null;
		$options2	=	JCckDev::fromJSON( $field->options2 );

		// $value is posted in user timezone, transform it to UTC
		if ( ( trim( $value ) != '0000-00-00 00:00:00' ) && !empty( $value ) ) {
			// Return an SQL formatted datetime string in UTC.
			$locale	=	$this->setLocale();

			// If data was created by script we have in fixed format, else we need to parse string
			if ( $datasource == "computed" ) {
				$date	=	JDate::createFromFormat( 'Y-m-d H:i:s', $value, $this->userTimeZone );
			} else {
				$date	=	JDate::createFromFormat( $options2['format'], $value, $this->userTimeZone );
			}

			if ( $date == false ) {
				throw new OutOfBoundsException( 'You either used wrong format or locale set by language file is not supported on your server - ' .$locale );
			} else {
				$value	=	$date->format( 'Y-m-d H:i:s' );
			}

			$date	=	JFactory::getDate( $value, $this->userTimeZone );

			// transform value to UTC
			$date->setTimezone(new DateTimeZone('UTC'));

			if ( $options2['storage_format'] == '0' ) {
				$value	=	$date->toSql();
			} else {
				$value	=	$date->toUnix();
			}
		}

		$this->_setText( $field, $value, $date );

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

		$app	=	JFactory::getApplication();
		$doc	=	JFactory::getDocument();
		$lang	=	JFactory::getLanguage();
		$loaded	=	1;
		$path	=	'assets/js/lang/'.self::_getTag_Lang( $lang ).'.js';

		if ( !is_file( JPATH_SITE.'/plugins/cck_field/calendar/'.$path ) ) {
			$path	=	'assets/js/lang/en.js';
		}

		if ( $app->input->get( 'tmpl' ) == 'raw' ) {
			echo '<link rel="stylesheet" href="'.self::$path.'assets/css/jscal2.css'.'" type="text/css" />';
			echo '<link rel="stylesheet" href="'.self::$path.'assets/css/border-radius.css'.'" type="text/css" />';
			echo '<link rel="stylesheet" href="'.self::$path.'assets/css/theme/'.$params['theme'].'/'.$params['theme'].'.css'.'" type="text/css" />';
			echo '<script src="'.self::$path.'assets/js/jscal2.js'.'" type="text/javascript"></script>';
			echo '<script src="'.self::$path.$path.'" type="text/javascript"></script>';
		} else {
			$doc->addStyleSheet( self::$path.'assets/css/jscal2.css' );
			$doc->addStyleSheet( self::$path.'assets/css/border-radius.css' );
			$doc->addStyleSheet( self::$path.'assets/css/theme/'.$params['theme'].'/'.$params['theme'].'.css' );
			$doc->addScript( self::$path.'assets/js/jscal2.js' );
			$doc->addScript( self::$path.$path );
		}
	}

	// _addScript
	protected static function _addScript( $id, $params = array() )
	{
		$js = '<script type="text/javascript">
		var cal = Calendar.setup({';

		if ( $params['type'] == 'form' ) {
			$id1 = $id;
			$js .= 'trigger	: "' . $id . '-trigger",';
			$js .= 'inputField	: "' . $id . '",';
		} else {
			$id1 = $id . '_hidden';
			$js .= 'trigger	: "' . $id . '_hidden-trigger",';
			$js .= 'inputField	: "' . $id . '_hidden",';
		}

		$js .= 'dateFormat	: "' . $params['dateFormat'] . '",
				weekNumbers	: ' . ($params['weekNumbers']
				? 'true'
				: 'false') . ',
				timePos		: "' . $params['timePos'] . '",';
		if ( $params['scriptDate'] != '' ) {
			$js .= 'date	: ' . $params['scriptDate'] . ',';
		}
		$js .= '	showTime	: ' . ($params['time']
				? $params['time']
				: 'false');
		if ( $params['dates'] != '0' ) {
			$js .= ',';
		}
		$js .= self::_availableDates( array( 'dates' => $params['dates'] ) );
		$js .= ',	onSelect	: function(cal) { 
								var sel_date = this.selection.get();
								var hours	=	cal.getHours();
								var minutes	=	cal.getMinutes();
								var sel_date = Calendar.intToDate(sel_date);
								sel_date.setHours(hours);
								sel_date.setMinutes(minutes);';

		$js .= ( $params['time'] == '0' )
			? 'var Jdate = Calendar.printDate(sel_date, "%Y-%m-%d ' . $params['default_hour'] . ':' . $params['default_min'] . ':' . $params['default_sec'] . '");'
			: 'var Jdate = Calendar.printDate(sel_date, "%Y-%m-%d %H:%M:00");';

		$js .= ($params['type'] == 'form')
			? 'jQuery("#' . $id . '_hidden").val(Jdate);'
			: 'jQuery("#' . $id . '").val(Jdate) ;';

		$js .= 'jQuery("#' . $id . '_datasource").val("computed"); ';
		$js .= 'this.hide(); jQuery("#'.$id.'").trigger("change"); }';
		$js .= '});';

		if ( $params['input_text'] ) {
			$js .= 'jQuery(document).ready(function($){ $(document).on("change", "#' . $id1 . '", function() {
			  jQuery("#' . $id . '_datasource").val("manual");
			}); });';
		}

		$js .= '</script>
				';

		return $js;
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

	private function _setText( &$field, &$value, $date = null )
	{
		$options2		=	JCckDev::fromJSON( $field->options2 );
		$options2['storage_format']	=	( isset( $options2['storage_format'] ) ) ? $options2['storage_format'] : '0';

		$field->text	=	( $value == '' || $date === null ) ? '' : $date->format( @$options2['format'], true, true );
	}

	private function setLocale()
	{
		// Let's actually use locale from the lang file
		$locale		=	JFactory::getLanguage()->getLocale();
		$localeset	=	setlocale( LC_TIME, $locale );

		return $localeset;
	}
}