<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: helper_version.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Helper
class Helper_Version
{
	// checkLatest
	public static function checkLatest( $type, $pk )
	{
		$latest		=	JCckDatabase::loadResult( 'SELECT date_time FROM #__cck_core_versions WHERE e_type = "'.$type.'" AND e_id = '.$pk. ' ORDER BY date_time DESC' );
		
		if ( !$latest ) {
			return true;
		}
		
		$current	=	JFactory::getDate()->toSql();
		$diff		=	round( abs( strtotime( $current ) - strtotime( $latest ) ) / 60, 2 );
		
		$time		=	(int)JCck::getConfig_Param( 'version_auto_time', 180 );
		$time_unit	=	(int)JCck::getConfig_Param( 'version_auto_unit', 0 );
		if ( $time_unit == 2 ) {
			$diff	=	$diff / 1440;
		} elseif ( $time_unit == 1 ) {
			$diff	=	$diff / 60;
		}
		
		if ( $diff > $time ) {
			return true;
		}
		
		return false;
	}
	
	// createVersion
	public static function createVersion( $type, $pk, $note = '', $update = false )
	{
		$table	=	JTable::getInstance( ucfirst( $type ), 'CCK_Table' );
		$table->load( $pk );
		
		// Core
		if ( isset( $table->asset_id ) ) {
			$table->rules		=	JCckDatabase::loadResult( 'SELECT rules FROM #__assets WHERE id = '.(int)$table->asset_id );
		}
		$version_num			=	$table->version;
		if ( $update !== false ) {
			unset( $table->rules );
			$table->version++;
			$table->store();
		}

		// Version
		$version				=	JTable::getInstance( 'Version', 'CCK_Table' );
		$version->e_id			=	$table->id;
		$version->e_title		=	$table->title;
		$version->e_name		=	$table->name;
		$version->e_type		=	$type;
		$version->e_core		=	JCckDev::toJSON( $table );
		$version->e_version		=	$version_num;
		$version->date_time		=	JFactory::getDate()->toSql();
		$version->user_id		=	JFactory::getUser()->id;
		if ( $note ) {
			$version->note		=	$note;
		}

		// More
		$clients	=	( $type == 'search' ) ? array( 1=>'search', 2=>'filter', 3=>'list', 4=>'item', 5=>'order' ) : array( 1=>'admin', 2=>'site', 3=>'intro', 4=>'content' );
		$count		=	0;
		$e_more		=	array();
		foreach ( $clients as $i=>$client ) {
			$fields				=	JCckDatabase::loadObjectList( 'SELECT * FROM #__cck_core_'.$type.'_field WHERE '.$type.'id = '.$pk.' AND client ="'.$client.'" ORDER BY ordering' );
			$positions			=	JCckDatabase::loadObjectList( 'SELECT * FROM #__cck_core_'.$type.'_position WHERE '.$type.'id = '.$pk.' AND client ="'.$client.'"' );
			$style				=	'';

			if ( isset( $table->{'template_'.$client} ) && $table->{'template_'.$client} ) {
				$style			=	JCckDatabase::loadResult( 'SELECT params FROM #__template_styles WHERE id = '.(int)$table->{'template_'.$client}.' AND title NOT LIKE "%- Default"' );
			}
			if ( $style != '' ) {
				$style			=	json_decode( $style );
			}

			$data				=	array( 'fields'=>$fields, 'positions'=>$positions, 'template_style'=>$style );
			$e_more[$i]			=	(string)( count( $fields ) );
			$name				=	'e_more'.$i;
			
			$version->{$name}	=	JCckDev::toJSON( $data );
		}
		$version->e_more	=	JCckDev::toJSON( array( 'fields'=>$e_more ) );
		// --
		
		$version->check();
		if ( !$version->store() ) {
			return false;
		}
		
		return true;
	}

	// removeVersion
	public static function removeVersion( $type, $pk )
	{
		$offset	=	JCck::getConfig_Param( 'version_remove_offset', 20 );
		$where	=	'e_type = "'.$type.'" AND e_id = '.(int)$pk.' AND featured != 1';
		$query	=	'DELETE FROM #__cck_core_versions WHERE '.$where.' AND id <= (SELECT id FROM (SELECT id FROM #__cck_core_versions WHERE '.$where.' ORDER BY id DESC LIMIT 1 OFFSET '.$offset.') AS max_id )';

		return JCckDatabase::execute( $query );
	}
}
?>