<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: display.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// CommonHelper
class CommonHelper_Display
{
	// quickCopyright
	public static function quickCopyright( $cpanel = false )
	{
		?>
		<div class="copyright">
			<strong><a target="_blank" href="<?php echo CCK_WEBSITE; ?>"><?php echo CCK_LABEL; ?></a></strong>&nbsp;<?php echo JText::_( 'COM_CCK_COPYRIGHT_SEBLOD_ADDON' ); ?>
            <br /><?php echo JText::_( 'JVERSION' ).' '. CCK_VERSION . ' &copy 2015'; ?>
		</div>
		<?php
	}
	
	// quickJGrid
	public static function quickJGrid( $type, $value = 0, $i, $canChange = true )
	{
		$states	= array( 0=>array( 'disabled.png', 'folders.featured', 'COM_CCK_UNFEATURED', 'COM_CCK_TOGGLE_TO_FEATURE' ),
						 1=>array( 'featured.png', 'folders.unfeatured', 'COM_CCK_FEATURED', 'COM_CCK_TOGGLE_TO_UNFEATURE' ) );
		
		$state	=	JArrayHelper::getValue( $states, (int) $value, $states[1] );
		$html	=	JHtml::_( 'image','admin/'.$state[0], JText::_( $state[2] ), NULL, true );
		if ( $canChange ) {
			$html	= '<a href="#" onclick="return listItemTask(\'cb'.$i.'\',\''.$state[1].'\')" title="'.JText::_($state[3]).'">' .$html. '</a>';
		}
		
		echo $html;
	}

	// quickSlideTo
	public static function quickSlideTo( $direction, $text = '', $class = '' )
	{
		$direction	=	'#' . $direction;
		if ( $text == 'up' ) {
			echo '<a href="'.$direction.'" class="scroll '.$class.'" style="text-decoration: none;">&nbsp;<img src="'.JROOT_MEDIA_CCK.'/images/12/icon-12-up.png" />&nbsp;</a>';
		} elseif ( $text == 'down' ) {
			echo '<a href="'.$direction.'" class="scroll '.$class.'" style="text-decoration: none;">&nbsp;<img src="'.JROOT_MEDIA_CCK.'/images/12/icon-12-down.png" />&nbsp;</a>';
		} else {
			echo '<a href="'.$direction.'" class="scroll '.$class.'" style="text-decoration: none; color: #666666;">&nbsp;'.$text.'&nbsp;</a>';
		}
	}
	
	// quickSession
	public static function quickSession( $options, $id = 'featured_session' )
	{
		$doc	=	JFactory::getDocument();
		$js		=	'';
		$css	=	'';
		$title	=	JText::_( 'COM_CCK_SESSIONS_SAVE_SELECT' );
		$html	=	'';
		
		$where		=	'extension="'.$options['extension'].'"';
		if ( isset( $options['folder'] ) ) {
			$where	.=	'type="'.$options['folder'].'"';
		}
		if ( isset( $options['type'] ) ) {
			$where	.=	'type="'.$options['type'].'"';
		}
		$items		=	JCckDatabase::loadObjectList( 'SELECT id, title, type, options FROM #__cck_more_sessions WHERE '.$where.' ORDER BY title' );
		if ( !count( $items ) ) {
			return;
		}
			
		if ( JCck::on() ) {
			foreach ( $items as $item ) {
				$edit_link	=	'index.php?option=com_cck&task=session.edit&extension='.$options['extension'].'&id='.$item->id;
				$html	.=	'<li><a class="featured_sessions" href="javascript:void(0);" mydata="'.$item->type.'"'
						.	'mydata2="'.htmlspecialchars( $item->options ).'">' . $item->title . '</a><span class="featured_sessions_del icon-delete" mydata="'.$item->id.'"></span><a href="'.$edit_link.'" class="featured_sessions_edit icon-edit"></a>'
						.	'</li>';
			}
			$html	=	'<button class="btn btn-primary dropdown-toggle cck-float-none" type="button" data-toggle="dropdown"><span class="caret"></span></button>'
					.	'<ul class="dropdown-menu featured-sessions pull-right">'.$html.'</ul>';
			$js	=	'
					jQuery(document).ready(function($){
						$("#'.$id.'").after(\''.$html.'\');
					});
					';
		} else {
			$css	=	'
						ul.toolbar-tiplist {padding: 0px; margin-left:0px; margin-right:0px;}
						ul.toolbar-tiplist li { list-style: none; padding: 5px;}
						ul.toolbar-tiplist li:hover {background-color: #ffffff; -webkit-border-radius: 1px; -moz-border-radius: 1px; border-radius: 1px;}
						.ui-tooltip-grey .ui-tooltip-content{background-color: #ffffff;}
						.ui-tooltip-rounded .ui-tooltip-titlebar{-webkit-border-radius: 2px; -moz-border-radius: 2px; border-radius: 2px;}
						.ui-tooltip-rounded .ui-tooltip-titlebar + .ui-tooltip-content{-webkit-border-radius: 2px; -moz-border-radius: 2px; border-radius: 2px;}
						';
			
			$doc->addStyleSheet( JURI::root( true ).'/media/cck/scripts/jquery-qtip/css/jquery.qtip.css' );
			JCck::loadjQuery();
			$doc->addScript( JURI::root( true ).'/media/cck/scripts/jquery-qtip/js/jquery.qtip.min.js' );
			
			foreach ( $items as $item ) {
				$html	.=	'<li><a class="featured_sessions" href="javascript:void(0);" mydata="'.$item->type.'"'
						.	'mydata2="'.htmlspecialchars( $item->options ).'">' . $item->title . '</a>'
						.	'<img class="featured_sessions_del" src="'.JROOT_MEDIA_CCK.'/images/14/icon-14-trash.png" mydata="'.$item->id.'" /></li>';
			}
			
			// Tooltip
			$html		=	'<div><ul class="toolbar-tiplist">'.$html.'</ul></div>' . '<div class="clr"></div>';
			$search		=	array( '.' , '<', '>', '"', '%', ';' );
			$replace	=	array( '\.', '\<', '\>', '\"', '\%', '\;' );
			$html		=	preg_replace( "/(\r\n|\n|\r)/", " ", $html );
			$html		=	str_replace( $search, $replace, $html );
			
			$js	=	'
					jQuery(document).ready(function($){
						$("#'.$id.'").qtip({
							prerender: true,
							content: { text: "'.$html.'", title: { text: "'.$title.'" } },
							hide: { event: "unfocus" },
							style: { tip: true, classes: "ui-tooltip-grey ui-tooltip-rounded" },
							position: { at: "right center", my: "left center", adjust: { x: 23 } }
						});
					});
					';
		}

		if ( $css ) {
			$doc->addStyleDeclaration( $css );
		}
		
		if ( $js ) {
			$doc->addScriptDeclaration( $js );
		}
	}
}
?>