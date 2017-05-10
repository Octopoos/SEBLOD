<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: edit2.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$config	=	JCckDev::init( array(), true, array( 'item'=>$this->item, 'tmpl'=>'ajax' ) );
Helper_Include::addDependencies( $this->getName(), $this->getLayout(), 'ajax' );

$type	=	( $this->item->type ) ? $this->item->type : 'text';
$layer	=	JPATH_PLUGINS.'/cck_field/'.$type.'/tmpl/edit.php';
$lang 	=	JFactory::getLanguage();
$lang->load( 'plg_cck_field_'.$type, JPATH_ADMINISTRATOR, null, false, true );
if ( is_file( $layer ) ) {
	include_once $layer;
}
?>