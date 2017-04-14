<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: restriction.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class JCckPluginRestriction extends JPlugin
{
	protected static $construction	=	'cck_field_restriction';
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff
	
	// authorise (temporary)
	public static function authorise( &$field, &$config )
	{
		$user	=	JCck::getUser();
		
		$check	=	JCckDatabase::loadResult( 'SELECT COUNT(a.id) FROM #__cck_more_ecommerce_order_product AS a LEFT JOIN #__cck_more_ecommerce_orders AS b ON b.id = a.order_id'
											. ' WHERE a.product_id = '.$config['pk'].' AND b.user_id ='.$user->id );
		if ( (int)$check > 0 ) {
			//
		} else {
			$field->display	=	0;
		}
	}
	
	// g_addProcess
	public static function g_addProcess( $event, $type, &$config, $params, $priority = 3 )
	{
		if ( $event && $type ) {
			$process						=	new stdClass;
			$process->group					=	self::$construction;
			$process->type					=	$type;
			$process->params				=	$params;
			$process->priority				=	$priority;
			$config['process'][$event][]	=	$process;
		}
	}
	
	// g_getPath
	public static function g_getPath( $type = '' )
	{
		return JUri::root( true ).'/plugins/'.self::$construction.'/'.$type;
	}
	
	// g_getRestriction
	public static function g_getRestriction( $params, $format = '' )
	{
		if ( $format != '' )  {
			return JCckDev::fromJSON( $params, $format );
		} else {
			$reg	=	new JRegistry;
		
			if ( $params ) {			
				$reg->loadString( $params );
			}
			
			return $reg;
		}
	}
}
?>