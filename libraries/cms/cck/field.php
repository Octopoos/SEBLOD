<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: field.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckField
class JCckField
{
	protected static $instances	=	array();
	protected $_config 			=	array();
	protected $_name			=	'';
	protected $_params 			=	NULL;
	
	// __construct
	public function __construct( $name, $config )
	{
		$this->_config	=	$config;
		$this->_name	=	$name;
		$this->_params	=	JCckDatabase::loadObject( 'SELECT * FROM #__cck_core_fields WHERE name = "'.$name.'"' );
	}
	
	// __call
	public function __call( $method, $args )
	{
		$prefix		=	strtolower( substr( $method, 0, 3 ) );
        $property	=	strtolower( substr( $method, 3 ) );
		
		if ( empty( $prefix ) ) {
			return;
		}
		
        if ( $prefix == 'get' ) {
			return ( $property ) ? $this->_params->$property : $this->_params;
        }
    }
	
	// getInstance
	public static function getInstance( $name = '', $config = array( 'doTranslation'=>1, 'doValidation'=>2, 'mode'=>'content' ) )
	{
		if ( !$name ) {
			return;
		}
		
		if ( empty( self::$instances[$name] ) ) {
			self::$instances[$name]	=	new JCckField( $name, $config );
		}
		
		return self::$instances[$name];
	}
	
	// getLabel
	public function getLabel( $html = false )
	{
		$label	=	trim( $this->_params->label );
		if ( $html === true && $label ) {
			$label	=	'<label for="'.$this->_name.'">'.$label.'</label>';
		}
		
		return $label;
	}
	
	// load
	public function load( $id )
	{
		//
	}
	
	// loadValue
	public function loadValue( $value )
	{
		JEventDispatcher::getInstance()->trigger( 'onCCK_FieldPrepare'.$this->_config['mode'], array( &$this->_params, $value, &$this->_config ) );
	}
	
	// render
	public function render()
	{
		if ( isset( $this->_params->type ) ) {
			return JCck::callFunc_Array( 'plgCCK_Field'.$this->_params->type, 'onCCK_FieldRender'.$this->_config['mode'], array( $this->_params, &$this->_config ) );
		}
	}
}
?>