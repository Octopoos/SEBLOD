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
class CCKViewCck extends JCckBaseLegacyView
{
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
                                array( 'val'=>'2', 'pre'=>'&bull;&nbsp;', 'key'=>'COM_CCK_', 'img'=>'cck-form' ),
                                array( 'val'=>'3', 'pre'=>'&bull;&nbsp;', 'key'=>'', 'img'=>'cck-plugin' ),
                                array( 'val'=>'4', 'pre'=>'&bull;&nbsp;', 'key'=>'COM_CCK_', 'img'=>'cck-search' ),
                                array( 'val'=>'1', 'pre'=>'&bull;&nbsp;', 'key'=>'', 'img'=>'cck-template' ),
                                array( 'val'=>'5', 'pre'=>'', 'key'=>'', 'img'=>'cck-multisite' )
                            );
        }
        $components     =   JCckDatabase::loadObjectList( 'SELECT a.title, a.link, b.element'
                                                        . ' FROM #__menu AS a LEFT JOIN #__extensions AS b ON b.extension_id = a.component_id'
                                                        . ' WHERE a.link LIKE "index.php?option=com_cck\_%"'
                                                        . ' AND a.link NOT IN ("index.php?option=com_cck_ecommerce&view=listen","index.php?option=com_cck_toolbox&view=processing","index.php?option=com_cck_webservices&view=api")'
                                                        . ' AND b.enabled = 1'
                                                        . ' ORDER BY a.title ASC' );
        $groupedButtons =   array();
        $lang           =   JFactory::getLanguage();
        $more           =   array(
                                'ADDON'=>16,
                                'PLUGIN_FIELD'=>19,
                                'PLUGIN_LINK'=>20,
                                'PLUGIN_LIVE'=>21,
                                'PLUGIN_OBJECT'=>22,
                                'PLUGIN_RESTRICTION'=>112,
                                /*
                                'PLUGIN_STORAGE'=>23,
                                */
                                'PLUGIN_TYPOGRAPHY'=>24,
                                'PLUGIN_VALIDATION'=>25,
                                'TEMPLATE'=>27,
                            );

        foreach ( $core as $k=>$v ) {
            $buttons[]  =   array(
                                'access'=>array( 'core.manage', 'com_cck' ),
                                'group' =>'COM_CCK_CORE',
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

        foreach ( $buttons as $button ) {
            $groupedButtons[$button['group']][] =   $button;
        }

        $this->sidebar  =   '<div class="sidebar-nav quick-icons">'
                        .   JHtml::_( 'links.linksgroups', $groupedButtons )
                        .   '</div>';
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
}
?>