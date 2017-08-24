<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: admin.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// CommonHelper
class CommonHelper_Admin
{
	// addFolderClass
	public static function addFolderClass( &$css, $id, $color, $colorchar, $width = '25' )
	{
		if ( ! isset( $css[$id] ) ) {
			$bgcolor	=	$color ? ' background-color:'.$color.';' : '';
			$color		=	$colorchar ? ' color:'.$colorchar.';' : '';
			$css[$id]	=	'.folderColor'.$id.' {width: '.$width.'px; height: 18px;'.$bgcolor.$color.' padding-top:3px; padding-bottom:3px;'
						.	'vertical-align: middle; border: none; -webkit-border-radius: 20px; -moz-border-radius: 20px; border-radius:20px; text-align:center; margin-left:auto; margin-right:auto; font-size:12px;}'
						.	'.folderColor'.$id.' > strong{position:relative; top:1px;}';
		}
	}
	
	// addIcon
	public static function addIcon( $base, $link, $image, $text, $size = 48, $align = 'left', $div = '' )
	{
		$font	=	true;

		if ( is_array( $image ) ) {
			$image	=	( $font !== false ) ? $image[1] : $image[0];
		}
		if ( $div == '' ) {
			$div	=	'3';
		}
		if ( $size == 48 ) {
			$class	=	'icon icon-rounded';
			$class2	=	'wrapper-icon span'.$div;
		} else {
			$class	=	'icon icon-rounded icon_small icon_small_'.$align;
			$class2	=	'wrapper-icon half span'.$div;
		}
		$target	=	( strpos( $link, 'http://' ) !== false ) ? '_blank' : '_self';
		if ( $text == 'spacer' ) {
		?>
			<div class="<?php echo $class2; ?>">
	            <div class="icon icon_<?php echo $text; ?>">
                </div>
            </div>
		<?php
		} else {
		?>
            <div class="<?php echo $class2; ?>">
                <div class="<?php echo $class; ?>">
                    <a href="<?php echo $link; ?>" target="<?php echo $target; ?>">
                        <?php
                        if ( strpos( $image, 'icon-cck-' ) !== false ) {
                        	echo '<span class="'.$image.'"></span>';
                        } else {
							$img	=	JHtml::_( 'image', 'administrator/components/'.$base.'/assets/images/'.$size.'/icon-'.$size.'-'.$image.'.png', htmlspecialchars( str_replace( '<br />', ' ', $text ) ) );

							echo str_replace( '<img ', '<img width="'.$size.'" height="'.$size.'" ', $img );
                        }
                        ?>
                        <span><?php echo $text; ?></span>
					</a>
                </div>
            </div>
		<?php
		}
	}
	
	// addSubmenu
	public static function addSubmenu( $option, $vName )
	{
	}
	
	// addSubmenuEntries
	public static function addSubmenuEntries( $option, $vName, $items, $addons = array() )
	{
		$root	=	CCK_LABEL;
		$user	=	JFactory::getUser();
		JHtmlSidebar::addEntry( $root, CCK_LINK, $vName == CCK_NAME );
		
		if ( count( $items) ) {
			foreach ( $items as $item ) {
				if ( isset( $item['link'] ) ) {
					JHtmlSidebar::addEntry( $item['name'], $item['link'], $item['active'] );
				} else {
					$active	=	( isset( $item['active'] ) ) ? $item['active'] : $vName == constant( '_C'.$item['val'].'_NAME' );
					$s	=	( $option == 'cck_ecommerce' && $item['val'] == '3' ) ? '' : 'S'; // todo: I'll see this one later..
					JHtmlSidebar::addEntry( $item['pre'].JText::_( $item['key'].constant( '_C'.$item['val'].'_TEXT' ).$s ),
											constant( '_C'.$item['val'].'_LINK' ),
											$active );
				}
			}
		}
		if ( count( $addons ) ) {
			JHtmlSidebar::addEntry( '', '#' );
			foreach ( $addons as $addon ) {
				if ( $user->authorise( 'core.manage', $addon->element ) ) {
					JHtmlSidebar::addEntry( $addon->title, $addon->link, ( 'com_'.$option == $addon->element ) );
				}
			}
		}
	}

	// addToolbar
	public static function addToolbar( $vName, $vTitle ) 
	{
		if ( $vTitle != '' ) {
			JToolBarHelper::title( JText::_( $vTitle.'_MANAGER' ), $vName.'s.png' );
		}
	}

	// addToolbarDelete
	public static function addToolbarDelete( $vName, $vTitle ) 
	{
		JFactory::getApplication()->input->set( 'hidemainmenu', true );
	}

	// addToolbarEdit
	public static function addToolbarEdit( $vName, $vTitle ) 
	{
		JFactory::getApplication()->input->set( 'hidemainmenu', true );
	}
	
	// addToolbarHistoryButton
	public static function addToolbarHistoryButton( $extension = 'com_cck' )
	{
		$pk	=	JCckDatabase::loadResult( 'SELECT extension_id FROM #__extensions WHERE type = "component" AND element = "'.$extension.'"' );
		
		if ( $pk > 0 ) {
			require_once JPATH_ADMINISTRATOR.'/components/com_cck/helpers/toolbar/link.php';
			JToolBar::getInstance( 'toolbar' )->appendButton( 'CckLink', 'notification', 'COM_CCK_POSTINSTALL_HISTORY', JRoute::_( JUri::base().'index.php?option=com_postinstall&eid='.$pk ), '_self' );
		}
	}
	
	// addToolbarSupportButton
	public static function addToolbarSupportButton()
	{
		require_once JPATH_ADMINISTRATOR.'/components/com_cck/helpers/toolbar/link.php';
		JToolBar::getInstance( 'toolbar' )->appendButton( 'CckLink', 'star', 'COM_CCK_SUPPORT', JRoute::_( 'http://jed.seblod.com' ), '_blank' );
	}

	// getActions
	public static function getActions( $folderId = 0 )
	{
		$user	=	JFactory::getUser();
		$result	=	new JObject;
		
		$assetName	=	'com_'.CCK_NAME;
		$actions	=	array( 'core.admin', 'core.manage' );
		foreach ( $actions as $action ) {
			$result->set( $action, $user->authorise( $action, $assetName ) );
		}
		
		return $result;
	}
	
	// getAlphaOptions
	public static function getAlphaOptions( $selectlabel = false )
	{
		$options	=	array();
		
		if ( $selectlabel !== false ) {
			$options[]	=	JHtml::_( 'select.option', '', JText::_( 'COM_CCK_ALL_LETTERS' ), 'value', 'text' );
		}
		for ( $i = 97; $i < 123; $i++ ) {
			$options[]	=	JHtml::_( 'select.option', chr( $i ), strtoupper( chr( $i ) ), 'value', 'text' );
		}
		
		return $options;
	}
	
	// getClientOptions
	public static function getClientOptions( $selectlabel = false, $selectnone = false, $both = false )
	{
		$app		=	JFactory::getApplication();
		$options	=	array();
		$view		=	$app->input->get( 'view', '' );

		if ( $selectlabel !== false ) {
			$selectlabel	=	( is_string( $selectlabel ) ) ? $selectlabel : '- '.JText::_( 'COM_CCK_ALL_LOCATIONS' ).' -';
			$options[]		=	JHtml::_( 'select.option', '', $selectlabel, 'value', 'text' );
		}
		$options[]			=	JHtml::_( 'select.option', 'both', JText::_( 'COM_CCK_BOTH' ) );
		if ( $selectnone !== false || ( $view == 'type' || $view == 'types' ) ) {
			$options[]		=	JHtml::_( 'select.option', 'none', JText::_( 'COM_CCK_NONE' ), 'value', 'text' );
		}
		if ( $both !== false ) {
			$options[] 	=	JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_LOCATION_PERMISSIVE' ) );
			$options[]	=	JHtml::_( 'select.option', 'admin_both', JText::_( 'COM_CCK_ADMINISTRATOR' ) );
			$options[]	=	JHtml::_( 'select.option', 'site_both', JText::_( 'COM_CCK_SITE' ) );
			$options[]	=	JHtml::_( 'select.option', '</OPTGROUP>', '' );
			$options[] 	=	JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_LOCATION_EXACT' ) );
		} else {
			$options[] 	=	JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_LOCATION' ) );
		}
		$options[]	=	JHtml::_( 'select.option', 'admin', JText::_( 'COM_CCK_ADMINISTRATOR' ) );
		$options[]	=	JHtml::_( 'select.option', 'site', JText::_( 'COM_CCK_SITE' ) );
		$options[]	=	JHtml::_( 'select.option', '</OPTGROUP>', '' );
		
		return $options;
	}

	// getFolderOptions
	public static function getFolderOptions( $selectlabel = false, $quickfolder = true, $top = false, $published = true, $element = '', $featured = false, $more = '' )
	{
		$component	=	JFactory::getApplication()->input->getCmd( 'option' );
		$options	=	array();
		
		if ( $selectlabel !== false ) {
			$options[]	=	JHtml::_( 'select.option', '', JText::_( 'COM_CCK_ALL_FOLDERS_SL' ), 'value', 'text' );
		}
		if ( $quickfolder !== false ) {
			$options[]	=	JHtml::_( 'select.option', '1', JText::_( 'COM_CCK_QUICK_FOLDER' ), 'value', 'text' );
		}
		
		$n			=	( $top ) ? 1 : 2;
		$orderby	=	' GROUP BY s.id ORDER BY s.lft';	
		$where		=	( $top ) ? ' WHERE s.lft > 0 AND s.lft BETWEEN parent.lft AND parent.rgt' : ' WHERE s.lft > 1 AND s.lft BETWEEN parent.lft AND parent.rgt';
		$where		=	( $published ) ? $where . ' AND s.published = 1' : $where;
		
		if ( $featured ) {
			$options	=	array();
			$where		.=	' AND s.featured = 1';
			$query		= 'SELECT CONCAT(s.title, " (", LCASE(parent.title), ")") as text, s.id as value'
						. ' FROM #__cck_core'.$more.'_folders AS s LEFT JOIN #__cck_core'.$more.'_folders AS parent ON parent.id = s.parent_id'
						. $where
						. ' GROUP BY s.id ORDER BY s.title'
						;
		} else {
			if ( $component == 'com_cck' && $element && $element != 'session' ) {
				$where	.=	' AND s.elements LIKE "%'.$element.'%"';
			}
			$query		= 'SELECT CONCAT( REPEAT("- ", COUNT(parent.title) - '.$n.'), s.title) AS text, s.id AS value'
							. ' FROM #__cck_core'.$more.'_folders AS s, #__cck_core'.$more.'_folders AS parent'
						. $where
						. $orderby
						;
		}
		
		$options2	=	JCckDatabase::loadObjectList( $query );
		if ( count( $options2 ) ) {
			if ( $top && $options2[0]->value == '2' ) {
				$options2[0]->text	=	JText::_( 'COM_CCK_'.$options2[0]->text );
			}
			$optgroup			=	( defined( '_C0_TEXT' ) ) ? 'COM_CCK_'._C0_TEXT.'S' : 'COM_CCK_APP_FOLDERS';
			$options[]		 	=	JHtml::_( 'select.option', '<OPTGROUP>', JText::_( $optgroup ) );
			$options			=	array_merge( $options, $options2 );
			$options[]			=	JHtml::_( 'select.option', '</OPTGROUP>', '' );
		}
		
		return $options;
	}
	
	// getLocationOptions
	public static function getLocationOptions( $folder = false )
	{
		$app		=	JFactory::getApplication();
		$option		=	$app->input->get( 'option', 'com_cck' );
		$view		=	$app->input->get( 'view', '' );
		$options	=	array();
		
		$options[]	=	JHtml::_( 'select.option', 'title', JText::_( 'COM_CCK_TITLE' ) );
		if ( $option == 'com_cck_ecommerce' ) {
			if ( $view == 'zones' ) {
				$options[]	=	JHtml::_( 'select.option', 'name', JText::_( 'COM_CCK_NAME' ) );
			} elseif ( $view == 'orders' ) {
				$options[]	=	JHtml::_( 'select.option', 'number', JText::_( 'COM_CCK_INVOICE' ) );
			}
		} else {
			$options[]	=	JHtml::_( 'select.option', 'name', JText::_( 'COM_CCK_NAME' ) );
		}
		if ( $option == 'com_cck' && $view == 'fields' ) {
			$options[]	=	JHtml::_( 'select.option', 'label', JText::_( 'COM_CCK_LABEL' ) );	
		}
		$options[]	=	JHtml::_( 'select.option', 'description', JText::_( 'COM_CCK_DESCRIPTION' ) );
		$options[]	= 	JHtml::_( 'select.option', 'id', JText::_( 'COM_CCK_IDS' ) );
		
		if ( $option == 'com_cck' ) {
			if ( $view != 'folders' ) {
				$options[] 	=	JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_'._C0_TEXT.'S' ) );
				$options[]	= 	JHtml::_( 'select.option', 'folder_id', JText::_( 'COM_CCK_'._C0_TEXT.'_ID' ) );
				$options[]	=	JHtml::_( 'select.option', '</OPTGROUP>', '' );
			}
			if ( $view == 'types' || $view == 'searchs' ) {
				$options[] 	=	JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_FIELDS' ) );
				$options[]	= 	JHtml::_( 'select.option', 'field_name', JText::_( 'COM_CCK_FIELD_NAME' ) );
				$options[]	=	JHtml::_( 'select.option', '</OPTGROUP>', '' );
				$options[] 	=	JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_TEMPLATES' ) );
				$options[]	= 	JHtml::_( 'select.option', 'template_name', JText::_( 'COM_CCK_TEMPLATE_NAME' ) );
				$options[]	=	JHtml::_( 'select.option', '</OPTGROUP>', '' );
			}
		} elseif ( $option == 'com_cck_ecommerce' ) {
			if ( $view == 'carts' || $view == 'orders' || $view == 'stores' || $view == 'subscriptions' ) {
				$key		=	( $view == 'stores' ) ? 'COM_CCK_OWNERS' : 'COM_CCK_CUSTOMERS';
				$options[] 	=	JHtml::_( 'select.option', '<OPTGROUP>', JText::_( $key ) );
				$options[]	= 	JHtml::_( 'select.option', 'user_id', JText::_( 'COM_CCK_USER_IDS' ) );
				$options[]	= 	JHtml::_( 'select.option', 'user_name', JText::_( 'COM_CCK_USER_NAME' ) );
				$options[]	= 	JHtml::_( 'select.option', 'user_username', JText::_( 'COM_CCK_USER_USERNAME' ) );
				$options[]	=	JHtml::_( 'select.option', '</OPTGROUP>', '' );
			}
		} elseif ( $option == 'com_cck_toolbox' ) {
			if ( $view == 'processings' ) {
				$options[] 	=	JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_APP_FOLDERS' ) );
				$options[]	= 	JHtml::_( 'select.option', 'folder_id', JText::_( 'COM_CCK_APP_FOLDER_ID' ) );
				$options[]	=	JHtml::_( 'select.option', '</OPTGROUP>', '' );
			}
		}
		
		return $options;
	}	
	
	// getPluginOptions
	public static function getPluginOptions( $folder, $prefix = '', $selectlabel = false, $selectnone = false, $language = false, $excluded = array(), $file = '' )
	{
		$base		=	JPATH_SITE.'/plugins/'.$prefix.$folder;
		$options	=	array();
		$options2	=	array();
		$lang		=	JFactory::getLanguage();
		
		if ( $selectlabel !== false ) {
			$selectlabel	=	( is_string( $selectlabel ) ) ? $selectlabel : JText::_( 'COM_CCK_ALL_TYPES_SL' );
			$options[]		=	JHtml::_( 'select.option', '', $selectlabel, 'value', 'text' );
		}
		if ( $selectnone !== false ) {
			$options[]		=	JHtml::_( 'select.option', '', JText::_( 'COM_CCK_NONE' ), 'value', 'text' );
		}

		$where2	=	'';
		if ( count( $excluded ) ) {
			$where2	=	' AND element NOT IN ("' . implode( '","', $excluded ) . '")';
		}
		$query	=	'SELECT name AS text, element AS value, params'
				.	' FROM #__extensions'
				.	' WHERE folder = "'.$prefix.$folder.'" AND enabled = 1'
				.	$where2
				.	' ORDER BY text'
				;
		$plugins	=	JCckDatabase::loadObjectList( $query );
		
		foreach ( $plugins as $plugin ) {
			if ( ( !is_file( $base.'/'.$plugin->value.'/'.$plugin->value.'.php' ) ) ||
				 ( $file && !is_file( $base.'/'.$plugin->value.'/'.$file ) ) ) {
				continue;
			}
			if ( $language ) {
				$lang->load( 'plg_'.$prefix.$folder.'_'.$plugin->value, JPATH_ADMINISTRATOR, null, false, true );
			}
			$plugin->text								=	JText::_( 'plg_'.$prefix.$folder.'_'.$plugin->value.'_LABEL' );
			$params										=	JCckDev::fromJSON( $plugin->params );
			$group										=	JText::_( $params['group'] );

			if ( $prefix.$folder == 'cck_field_link'
			  || $prefix.$folder == 'cck_field_live'
			  || $prefix.$folder == 'cck_field_restriction'
			  || $prefix.$folder == 'cck_field_typo' ) {
				$groups[$group][$plugin->value]				=	$plugin;
			} else {
				$groups[$group][$group.'_'.$plugin->text]	=	$plugin;
			}
		}
		if ( ! isset( $groups ) ) {
			return $options;
		}
		ksort( $groups );
		foreach ( $groups as $k => $v ) {
			if ( $k != 'CORE' ) {
				if ( $k == '-' ) {
					$options	=	array_merge( $options, $v );
				} elseif ( strpos( $k, '#' ) !== false ) {
					if ( $k == '#' ) {
						$options2[]	=	JHtml::_( 'select.option', '<OPTGROUP>', $k.' Core' );
						$options2	=	array_merge( $options2, $v );
						$options2[]	=	JHtml::_( 'select.option', '</OPTGROUP>', '' );
					} else {
						$options2[] =	JHtml::_( 'select.option', '<OPTGROUP>', $k );
						ksort( $v );
						$options2	=	array_merge( $options2, $v );
						$options2[]	=	JHtml::_( 'select.option', '</OPTGROUP>', '' );
					}
				} else {
					$options[] 	=	JHtml::_( 'select.option', '<OPTGROUP>', $k );
					ksort( $v );
					$options	=	array_merge( $options, $v );
					$options[]	=	JHtml::_( 'select.option', '</OPTGROUP>', '' );
				}
			}
		}
		if ( count( $options2 ) ) {
			$options	=	array_merge( $options, $options2 );
		}
		
		return $options;
	}
	
	// getTypeOptions
	public static function getTypeOptions( $selectlabel = false, $published = true )
	{
		$options	=	array();
		
		if ( $selectlabel !== false ) {
			$options[]	=	JHtml::_( 'select.option', '', JText::_( 'COM_CCK_ALL_'._C2_TEXT.'S_SL' ), 'value', 'text' );
		}
		$where		=	( $published ) ? ' WHERE a.published = 1 ' : ' WHERE a.published != -44';
		$options2	=	JCckDatabase::loadObjectList( 'SELECT a.title AS text, a.id AS value FROM #__cck_core_types AS a '.$where.' ORDER BY a.title' );
		if ( count( $options2 ) ) {
			$options[] 	=	JHtml::_( 'select.option', '<OPTGROUP>', JText::_( 'COM_CCK_'._C2_TEXT.'S' ) );
			$options	=	array_merge( $options, $options2 );
			$options[]	=	JHtml::_( 'select.option', '</OPTGROUP>', '' );
		}
		
		return $options;
	}
	
	// getSelected
	public static function getSelected( $vName, $elem, $selected, $default = '', $null = '' )
	{
		$app	=	JFactory::getApplication();
		
		if ( $selected != $null ) {
			return $selected;
		}
		$selected	=	$app->getUserState( CCK_COM.'.'.$vName.'s.filter.'.$elem );
		if ( $selected != $null ) {
			return $selected;
		}
		
		return $default;
	}
	
	// initACL
	public static function initACL( $options, $pks, $rules = array() )
	{
		$db		=	JFactory::getDbo();
		require_once JPATH_ADMINISTRATOR.'/components/com_cck/tables/'.$options['table'].'.php';
		$table	=	JTable::getInstance( ucfirst( $options['table'] ), 'CCK_Table' );
		
		foreach ( $pks as $pk ) {
			$table->load( $pk );
			if ( ! $table->asset_id ) {
				$table->asset_id	=	JCckDatabase::loadResult( 'SELECT id FROM #__assets WHERE name = "com_cck.'.$options['name'].'.'.$pk.'"' );
			}
			$table->store();
			if ( $table->asset_id > 0 ) {
				$rule	=	( isset( $rules[$pk] ) ) ? $rules[$pk] : $options['rules'];
				JCckDatabase::execute( 'UPDATE #__assets SET rules = "'.$db->escape( $rule ).'" WHERE id = '.(int)$table->asset_id );
			}
		}
		
		return true;
	}
}
?>