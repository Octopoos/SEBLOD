<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: helper_folder.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Helper
class Helper_Folder
{	
	// getBranch
	public static function getBranch( $id, $glue = '', $more = '' )
	{
		$query 	= 'SELECT s.id, (COUNT(parent.id) - (branch.depth2 + 1)) AS depth2'
				. ' FROM #__cck_core'.$more.'_folders AS s,'
				. ' #__cck_core'.$more.'_folders AS parent,'
				. ' #__cck_core'.$more.'_folders AS subparent,'
				. ' ('
					. ' SELECT s.id, (COUNT(parent.id) - 1) AS depth2'
					. ' FROM #__cck_core'.$more.'_folders AS s,'
					. ' #__cck_core'.$more.'_folders AS parent'
					. ' WHERE s.lft BETWEEN parent.lft AND parent.rgt'
					. ' AND s.id ='.(int)$id
					. ' GROUP BY s.id'
					. ' ORDER BY s.lft'
					. ' ) AS branch'
				. ' WHERE s.lft BETWEEN parent.lft AND parent.rgt'
				. ' AND s.lft BETWEEN subparent.lft AND subparent.rgt'
				. ' AND subparent.id = branch.id'
				. ' GROUP BY s.id'
				. ' ORDER BY s.lft';
		$branch	=	JCckDatabase::loadColumn( $query );
		if ( is_array( $branch ) && $glue != '' ) {
			$branch	=	implode( $glue, $branch );
		}
		
		return( $branch );
	}

	// getBrothers
	public static function getBrothers( $parent_id, $more = '' )
	{
		$query  = 'SELECT s.name, (COUNT(parent.name) - (sub_tree.depth2 + 1)) AS depth2'
				. ' FROM #__cck_core'.$more.'_folders AS s,'
				. ' #__cck_core'.$more.'_folders AS parent,'
				. ' #__cck_core'.$more.'_folders AS sub_parent,'
				. ' ('
		            . ' SELECT s.name, (COUNT(parent.name) - 1) AS depth2'
		            . ' FROM #__cck_core'.$more.'_folders AS s,'
		            . ' #__cck_core'.$more.'_folders AS parent'
		            . ' WHERE s.lft BETWEEN parent.lft AND parent.rgt'
		            . ' AND s.id = '.(int)$parent_id
		            . ' GROUP BY s.name'
		            . ' ORDER BY s.lft'
					. ' ) AS sub_tree'
				. ' WHERE s.lft BETWEEN parent.lft AND parent.rgt'
				. ' AND s.lft BETWEEN sub_parent.lft AND sub_parent.rgt'
				. ' AND sub_parent.name = sub_tree.name'
				. ' GROUP BY s.name'
				. ' HAVING depth2 <= 1'
				. ' ORDER BY s.lft'
				;
      	$brothers	=	JCckDatabase::loadColumn( $query );
		
		return $brothers;
	}
	
	// getParent
	public static function getParent( $id, $more = '' )
	{
		$res	=	null;
				
		$query 	= 'SELECT parent.id'
				. ' FROM #__cck_core'.$more.'_folders AS s, #__cck_core'.$more.'_folders AS parent'
				. ' WHERE ( s.lft BETWEEN parent.lft AND parent.rgt ) AND s.id != parent.id AND s.id ='.(int)$id
				. ' ORDER BY parent.lft DESC'
				;		
		$parent	=	JCckDatabase::loadResult( $query );
		
		return $parent;
	}
	
	// getPath
	public static function getPath( $id, $glue = '', $more = '' )
	{
		$query 	= 'SELECT parent.name'
				. ' FROM #__cck_core'.$more.'_folders AS s, #__cck_core'.$more.'_folders AS parent'
				. ' WHERE ( s.lft BETWEEN parent.lft AND parent.rgt ) AND parent.rgt AND s.id ='.(int)$id
				. ' ORDER BY parent.lft'
				;		
		$path	=	JCckDatabase::loadColumn( $query );
		unset( $path[0] );
		if ( is_array( $path ) && $glue != '' ) {
			$path	=	implode( $glue, $path );
		}
		
		return $path;
	}
	
	// getRoot
	public static function getRoot( $id )
	{
		$db		=	JFactory::getDbo();
		$query	= 'SELECT parent.lft, parent.app'
				. ' FROM #__cck_core_folders AS s, #__cck_core_folders AS parent'
				. ' WHERE s.lft BETWEEN parent.lft AND parent.rgt AND s.id = '.(int)$id
				. ' ORDER BY lft DESC'
				;
		
		$db->setQuery( $query );
		$root	=	$db->loadObjectList();

		return $root;
	}

	// getTree
	public static function getTree( $excluded, $published, $more = '' )
	{
		$db	=	JFactory::getDbo();
		
		$n			=	( $excluded ) ? 2 : 1;
		$orderby	=	' GROUP BY s.title ORDER BY s.lft';
		$where		=	( $excluded ) ? ' WHERE s.lft > 1 AND s.lft BETWEEN parent.lft AND parent.rgt' : ' WHERE s.lft > 0 AND s.lft BETWEEN parent.lft AND parent.rgt';
		$where		=	( $published ) ? $where . ' AND s.published = 1' : $where;
		
		$query	= 'SELECT CONCAT( REPEAT("- ", COUNT(parent.title) - '.$n.'), s.title) AS text, s.id AS value'
				. ' FROM #__cck_core'.$more.'_folders AS s, #__cck_core'.$more.'_folders AS parent'
				. $where
				. $orderby
				;
		
		$db->setQuery( $query );
		$categories	=	$db->loadObjectList();
		if ( count( $categories ) ) {
			$categories[0]->text	=	( ! $excluded ) ? JText::_( $categories[0]->text ) : $categories[0]->text;
		} else {
			$categories	=	array();
		}

		return $categories;
	}
	
	// prepareTree
	public static function prepareTree( $parent_id, $title, $more = '' )
	{
		$brothers	=	Helper_Folder::getBrothers( $parent_id, $more );
		$parent		=	array_shift( $brothers );
		$brothers[]	=	$title;
		sort( $brothers );
		$key		=	array_search( $title, $brothers );
		
		if ( $key == 0 ) {
			$query	= ' SELECT lft FROM #__cck_core'.$more.'_folders'
					. ' WHERE name = "'.$parent.'"'
					;
		} else {
			$bigbro	=	$brothers[$key - 1];
			$query	= ' SELECT rgt FROM #__cck_core'.$more.'_folders'
					. ' WHERE name = "'.$bigbro.'"'
					;
		}
		$limit	=	JCckDatabase::loadResult( $query );
		
		$query	= 'UPDATE #__cck_core'.$more.'_folders'
				. ' SET lft = CASE WHEN lft > '.$limit.' THEN lft + 2 ELSE lft END,'
				. ' rgt = CASE WHEN rgt >= '.$limit.' THEN rgt + 2 ELSE rgt END'
				. ' WHERE rgt > '.$limit
				;
		if ( ! JCckDatabase::doQuery( $query ) ) {
			return false;
		}
		
		return ( $limit + 1 ).'||'.( $limit + 2 );
	}

	// rebuildBranch
	public static function rebuildBranch( $root_id )
	{
		$app	=	'';
		$branch	=	self::getBranch( $root_id );
		$tree	=	self::getRoot( $root_id );

		if ( count( $tree ) ) {
			foreach ( $tree as $parent ) {
				if ( $parent->app ) {
					$app	=	$parent->app;
					break;
				}
			}
		}
		if ( !$app ) {
			$isRoot	=	JCckDatabase::loadObject( 'SELECT name, home FROM #__cck_core_folders WHERE id = '.(int)$root_id );
			if ( $isRoot->home ) {
				$app	=	$isRoot->name;
			}
		}
		if ( count( $branch ) ) {
			$set_app	=	( $app ) ? ', app = "'.$app.'"' : '';
			foreach ( $branch as $folder ) {
				$path	=	self::getPath( $folder, '/' );
				JCckDatabase::doQuery( 'UPDATE #__cck_core_folders SET path = "'.(string)$path.'"'.$set_app.' WHERE id = '.(int)$folder );
			}
		}
	}

	// rebuildTree
	public static function rebuildTree( $parent_id = 0, $lft = 0, $depth = 0, $more = '' )
	{
		$childs	=	JCckDatabase::loadColumn( 'SELECT id FROM #__cck_core'.$more.'_folders WHERE parent_id = '.(int)$parent_id.' ORDER BY parent_id, title' );
		$n		=	count( $childs );
		$rgt	=	$lft + 1;
		
		for ( $i = 0; $i < $n; $i++ ) {
			$depth++;
			$rgt	=	self::rebuildTree( $childs[$i], $rgt, $depth );	
			$depth--;
			if ( $rgt === false ) {
				return false;
			}
		}
		if ( ! JCckDatabase::doQuery( 'UPDATE #__cck_core_folders SET depth = '.(int)$depth.', lft = '.(int)$lft . ', rgt = '.(int)$rgt.' WHERE id ='.(int)$parent_id ) ) {
			return false;
		}
		
		return $rgt + 1;
	}
	
	// getRules
	public static function getRules( $rules, $default = '{}' )
	{
		$json	=	'';
		
		foreach ( $rules as $name => $r ) {
			$j	=	'';
			foreach ( $r as $k => $v ) {
				if ( $v != '' ) {
					$j	.=	'"'.$k.'":'.$v.',';
				}
			}
			$json	.=	'"'.$name.'":'.( $j ? '{'.substr( $j, 0, -1 ).'}' : '[]' ).',';
		}
		$json	=	substr( $json, 0, -1 );
		
		return ( $json != '' ) ? '{'.$json.'}' : $default;
	}
}
?>