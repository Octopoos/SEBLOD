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

// Init
$app	=	JFactory::getApplication();
$res	=	array( 0=>$app->input->get( 'fieldId', '' ) );
$value	=	$app->input->getString( 'fieldValue', '' );
$value	=	str_replace( array( '%26lt;', '%26gt;', '%27' ), array( '<', '>', "'" ), $value );
$and	=	'';
$column	=	$app->input->getString( 'avColumn', '' );
$key	=	$app->input->getString( 'avKey', '' );
$where	=	$app->input->getString( 'avWhere', '' );
$table	=	$app->input->getString( 'avTable', '' );

// Process
if ( $where ) {
	$fields	=	JCckDatabase::loadObjectList( 'SELECT name, storage, storage_table, storage_field FROM #__cck_core_fields WHERE name IN ("'.str_replace( ',', '","', $where ).'")', 'name' );
	$where	=	explode( ',', $where );
	foreach ( $where as $w ) {
		if ( isset( $fields[$w] ) && $fields[$w]->storage == 'standard' && $fields[$w]->storage_table == '#__'.$table ) {
			$v		=	$app->input->get( $w );
			if ( $v != '' ) {
				$and	.=	' '.$fields[$w]->storage_field.'="'.JCckDatabase::escape( $v ).'"';
			}
		}
	}
	$and	=	( $and ) ? ' AND'.$and : '';
}
if ( $key ) {
	$pk		=	$app->input->getInt( 'avPk', 0 );
	$pv		=	$app->input->getString( 'avPv', '' );
	$pv		=	str_replace( array( '%26lt;', '%26gt;', '%27' ), array( '<', '>', "'" ), $pv );
	$count	=	(int)JCckDatabase::loadResult( 'SELECT '.$key.' FROM #__'.$table.' WHERE '.$column.' = "'.JCckDatabase::escape( $value ).'"'.$and );
	$res[1]	=	( $count > 0 && $count != $pk ) ? false : true;
} else {
	$count	=	(int)JCckDatabase::loadResult( 'SELECT COUNT('.$column.') FROM #__'.$table.' WHERE '.$column.' = "'.JCckDatabase::escape( $value ).'"'.$and );
	$res[1]	=	( $count > 0 ) ? false : true;
}

// Set
echo json_encode( $res );
?>