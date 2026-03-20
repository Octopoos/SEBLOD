<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: view.html.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// View
class CCKViewCck extends JCckBaseLegacyView
{
	protected $_showInfo	=	'';

	// completeUI
	public function completeUI()
	{
		$this->document->setTitle( JText::_( 'LIB_CCK_SEBLOD' ) );
	}

	// prepareSidebar
	protected function prepareSidebar()
	{
		$buttons        =   array();
		if ( JCck::getUIX() == 'compact' ) {
			$core       =   array(
								array( 'val'=>'2', 'pre'=>'', 'key'=>'COM_CCK_', 'img'=>'cck-form' )
							);
		} else {
			$core       =   array(
								array( 'val'=>'0', 'pre'=>'', 'key'=>'COM_CCK_', 'img'=>'cck-application' ),
								array( 'val'=>'2', 'pre'=>'', 'key'=>'COM_CCK_', 'img'=>'cck-form' ),
								array( 'val'=>'3', 'pre'=>'', 'key'=>'', 'img'=>'cck-plugin' ),
								array( 'val'=>'4', 'pre'=>'', 'key'=>'COM_CCK_', 'img'=>'cck-search' ),
								array( 'val'=>'1', 'pre'=>'', 'key'=>'', 'img'=>'cck-template' ),
								array( 'val'=>'5', 'pre'=>'', 'key'=>'', 'img'=>'cck-multisite' )
							);
		}
		$components     =   JCckDatabase::loadObjectList( 'SELECT a.title, a.link, b.element'
														. ' FROM #__menu AS a LEFT JOIN #__extensions AS b ON b.extension_id = a.component_id'
														. ' WHERE a.link LIKE "index.php?option=com_cck\_%"'
														. ' AND a.link NOT LIKE "%view=%"'
														. ' AND b.enabled = 1'
														. ' ORDER BY a.title ASC' );
		$groupedButtons =   array();
		$lang           =   JFactory::getLanguage();
		$more           =   array(
								'ADDON'=>'16',
								'PLUGIN'=>'19,20,21,22,23,24,25,112',
								'TEMPLATE'=>'27',
							);
		$user			=	JFactory::getUser();

		foreach ( $core as $k=>$v ) {
			$buttons[]  =   array(
								'access'=>array( 'core.manage', 'com_cck' ),
								'group' =>'COM_CCK_SEBLOD_CORE',
								'image' =>$v['img'],
								'link'  =>JRoute::_( constant( '_C'.$v['val'].'_LINK' ) ),
								'target'=>'_self',
								'text'  =>$v['pre'].JText::_( $v['key'].constant( '_C'.$v['val'].'_TEXT' ).'S' )
							);
		}
		foreach ( $components as $k=>$v ) {
			$lang->load( $v->element.'.sys' );
			
			$buttons[]  =   array(
								'access'=>array( 'core.manage', $v->element ),
								'group' =>'COM_CCK_SEBLOD_MORE',
								'image' =>'cck-addon',
								'link'  =>JRoute::_( $v->link ),
								'target'=>'_self',
								'text'  =>JText::_( $v->element )
							);
		}
		foreach ( $more as $k=>$v ) {
			$buttons[]  =   array(
								'access'=>array( 'core.manage', 'com_cck' ),
								'group' =>'COM_CCK_SEBLOD_STORE',
								'image' =>'download',
								'link'  =>JRoute::_( 'https://www.seblod.com/store/extensions?seb_item_category='.$v ),
								'target'=>'_blank',
								'text'  =>JText::_( 'COM_CCK_PANE_MORE_'.$k )
							);
		}

		if ( JCck::on( '4.0' ) ) {
			$this->showInfo();

			$html   =   array(  );
			$group	=	'';

			foreach ( $buttons as $button ) {
				if ( $button['group'] != $group ) {
					if ( $group ) {
						$html[]	=	'</ul><ul class="nav flex-column">';
					}
					$html[] =   '<li class="nav-header"><span>'.JText::_( $button['group'] ).'</span></li>';
					$group	=	$button['group'];
				}
				if ( !$user->authorise( $button['group'][0], $button['group'][1] ) ) {
					continue;
				}
				$html[] =   '<li class="item"><a href="'.$button['link'].'" target="'.$button['target'].'">'.$button['text'].'</a></li>';
			}

			$this->sidebar  =	'<div class="sidebar-nav">'
							.	'<ul class="nav flex-column">'.implode( '', $html ).'</ul>'
							.	'</div>';
		} else {
			foreach ( $buttons as $button ) {
				$groupedButtons[$button['group']][] =   $button;
			}

			$this->sidebar  =	'<div class="sidebar-nav quick-icons">'
						.	JHtml::_( 'links.linksgroups', $groupedButtons )
						.	'</div>';
		}
	}

	// prepareToolbar
	protected function prepareToolbar()
	{
		$bar	=	JToolBar::getInstance( 'toolbar' );
		$canDo	=	Helper_Admin::getActions();
		
		JToolBarHelper::title( CCK_LABEL, 'cck-seblod' );
		
		if ( $canDo->get( 'core.admin' ) ) {
			JToolBarHelper::preferences( CCK_COM, 560, 840, 'JTOOLBAR_OPTIONS' );
		}
		
		Helper_Admin::addToolbarHistoryButton();
		// Helper_Admin::addToolbarSupportButton();
	}

	// showInfo
	protected function showInfo()
	{
		$info	=	JFactory::getApplication()->input->get( 's' );

		if ( $info == 'patchSQL' ) {
			require_once JPATH_ADMINISTRATOR.'/manifests/packages/cck/pkg_script.php';

			$queries	=	pkg_cckInstallerScript::_getPatchQueries();

			if ( count( $queries ) ) {
				$sql		=	array();

				foreach ( $queries as $k=>$v ) {
					$sql[]	=	implode( "\n", $v );
				}

				// $this->_showInfo	.=	'<div class="modal modal-small hide fade" id="collapseModal"><div class="modal-dialog modal-lg"><div class="modal-content">'
				// 					.	'<div class="modal-header"><h3 class="modal-title">'.JText::_( 'LIB_CCK_INSTALLATION_LEGEND_UPDATING_TO_4X_FROM_J3_BTN_2' ).'</h3><button type="button" class="btn-close novalidate" onclick="toggleMyModal();" aria-label="Close"></button></div>'
				// 					.	'<div class="modal-body" style="padding:15px">';

				$this->_showInfo	.=	'<h4>SQL Tables:</h4>'
									.	'<ul><li>'.implode( '</li><li>', array_keys( $queries ) ).'</li></ul>'
									.	'<h4>SQL Queries:</h4>'
									.	'<pre style="background:#000; color:#e14872; padding:16px;">'.implode( "\n", $sql ).'</pre>';

				// $this->_showInfo	.=	'</div>'
				// 					.	'</div></div></div>';
			}
		}
	}
}
?>