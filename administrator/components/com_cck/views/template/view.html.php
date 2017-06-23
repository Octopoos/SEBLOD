<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: view.html.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// View
class CCKViewTemplate extends JCckBaseLegacyViewForm
{
	protected $vName	=	'template';
	protected $vTitle	=	_C1_TEXT;
	
	// prepareDisplay
	function prepareDisplay()
	{
		$app			=	JFactory::getApplication();
		$model 			=	$this->getModel();
		$this->form		=	$this->get( 'Form' );
		$this->item		=	$this->get( 'Item' );
		$this->option	=	$app->input->get( 'option', '' );
		$this->state	=	$this->get( 'State' );
		
		// Check Errors
		if ( count( $errors	= $this->get( 'Errors' ) ) ) {
			throw new Exception( implode( "\n", $errors ), 500 );
		}
		
		$this->isNew			=	( @$this->item->id > 0 ) ? 0 : 1;
		$this->item->folder		=	Helper_Admin::getSelected( $this->vName, 'folder', $this->item->folder, 1 );
		$this->item->published	=	Helper_Admin::getSelected( $this->vName, 'state', $this->item->published, 1 );
		$this->item->mode		=	Helper_Admin::getSelected( $this->vName, 'mode', $this->state->get( 'mode', $this->item->mode ), '0' );
		
		if ( !$this->isNew ) {
			jimport( 'joomla.filesystem.folder' );
			$this->item->tree	=	$this->_generateTree();
			$this->item->files	=	JFolder::files( JPATH_SITE.'/templates/'.$this->item->name );
		}
		
		Helper_Admin::addToolbarEdit( $this->vName, _C1_TEXT, array( 'isNew'=>$this->isNew, 'folder'=>$this->state->get( 'filter.folder' ), 'checked_out'=>$this->item->checked_out ) );
	}
	
	// _generateTree
	function _generateTree()
	{
		$path		=	JPATH_SITE.'/templates/'.$this->item->name;
		$folders	=	JFolder::listFolderTree( $path, '.', 5 );
		$i			=	0;
		$path		=	array();
		$prev		=	0;
		$tree		=	'';
		
		foreach ( $folders as $k => $f ) {
			$p	=	$f['parent'];
			if ( $p > $prev ) {
				$tree		.=	'<ul><li id="phtml_'.$f['id'].'">'.'<a href="#">'.$f['name'].'</a>';
				$path[$i++]	=	$prev;
			} else {
				if ( $p < $prev ) {
					for ( $j = $i - 1; $j >= 0; $j-- ) {
						$tree	.=	'</li></ul>';
						$last	=	$path[$j];
						unset( $path[$j] );
						$i--;
						if ( $p == $last ) {
							break;
						}
					}
				}
				$tree	.=	'</li><li id="phtml_'.$f['id'].'">'.'<a href="#">'.$f['name'].'</a>';
			}
			$prev	=	$p;
			//$folders[$k]['files']	=	JFolder::files( $f['fullname'], '.', false, true );
		}
		$tree	=	'<ul><li id="phtml_0" class="jstree-open jstree-last">'.'<a href="#" class="jstree-clicked">'.'./'.'</a><ul>' . substr( $tree, 5 ) . '</li></ul></li></ul>';
		
		return $tree;
	}
}
?>