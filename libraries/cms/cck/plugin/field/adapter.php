<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: adapter_yoo.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Adapter
class JCckPluginFieldAdapter
{
	protected $config	=	array();
	protected $item		=	NULL;
	protected $type		=	'';

	// __construct
	public function __construct( $item, $type = '', $config = array() )
	{
		$this->config	=	$config;
		$this->item		=	( is_null( $item ) ) ? JTable::getInstance( 'Field', 'CCK_Table' ) : $item;
		$this->type		=	( is_object( $item ) && $item->type ) ? $item->type : $type;
	}

	// render
	public function render()
	{
		if ( $this->type == '' ) {
			return '';
		}

		JCck::loadjQuery( true, true, array( 'cck.dev-3.7.0.min.js' ) );

		$config	=	array(
						'asset'=>'',
						'asset_id'=>0,
						'client'=>'',
						'doTranslation'=>0,
						'doValidation'=>0,
						'fields'=>array(),
						'inherit'=>'core_fields',
						'item'=>'',
						'pk'=>0,
						'validation'=>array()
					);
		$file	=	JPATH_PLUGINS.'/cck_field/'.$this->type.'/tmpl/edit.php';

		if ( !$this->_isFile( $file ) ) {
			return '';
		}

		JFactory::getLanguage()->load( 'plg_cck_field_'.$this->type, JPATH_ADMINISTRATOR, null, false, true );

		ob_start();
		include_once $file;
		return ob_get_clean();
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Protected

	// _isFile
	protected function _isFile( $path )
	{
		static $paths	=	array();
		
		if ( !isset( $paths[$path] ) ) {
			$paths[$path]	=	is_file( $path );
		}

		return $paths[$path];
	}
}
?>