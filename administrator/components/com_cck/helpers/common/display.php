<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: display.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\Utilities\ArrayHelper;

// CommonHelper
class CommonHelper_Display
{
	// quickCopyright
	public static function quickCopyright( $cpanel = false )
	{
		?>
		<div class="copyright">
			<strong><a target="_blank" href="<?php echo CCK_WEBSITE; ?>"><?php echo CCK_LABEL; ?></a></strong>&nbsp;<?php echo JText::_( 'COM_CCK_COPYRIGHT_SEBLOD_ADDON' ); ?>
			<br /><?php echo JText::_( 'JVERSION' ).' '. CCK_VERSION . ' &copy 2009 - 2017'; ?>
		</div>
		<?php
	}
	
	// quickJGrid
	public static function quickJGrid( $type, $value = 0, $i, $canChange = true )
	{
		$states	=	array(
						0=>array( 'disabled.png', 'folders.featured', 'COM_CCK_UNFEATURED', 'COM_CCK_TOGGLE_TO_FEATURE', '', 'unfeatured' ),
						1=>array( 'featured.png', 'folders.unfeatured', 'COM_CCK_FEATURED', 'COM_CCK_TOGGLE_TO_UNFEATURE', ' active', 'featured' )
					);
		$state	=	ArrayHelper::getValue( $states, (int) $value, $states[1] );
		$html	=	'<span class="icon-'.$state[5].'"></span>';

		if ( $canChange ) {
			$html	= '<a href="#" onclick="return listItemTask(\'cb'.$i.'\',\''.$state[1].'\')" title="'.JText::_( $state[3] ).'">' .$html. '</a>';
		} else {
			$html	= '<a href="#" class="btn btn-micro disabled hasTooltip'.$state[4].'" title="'.JText::_( $state[2] ).'">' .$html. '</a>';
		}

		echo $html;
	}

	// quickSlideTo
	public static function quickSlideTo( $direction, $text = '', $class = '' )
	{
		$direction	=	'#' . $direction;
		if ( $text == 'up' ) {
			echo '<a href="'.$direction.'" class="scroll '.$class.'" style="text-decoration: none;">&nbsp;<span class="icon-arrow-up-2"></span></a>';
		} elseif ( $text == 'down' ) {
			echo '<a href="'.$direction.'" class="scroll '.$class.'" style="text-decoration: none;">&nbsp;<span class="icon-arrow-down-2"></span></a>';
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
		foreach ( $items as $item ) {
			$edit_link	=	'index.php?option=com_cck&task=session.edit&extension='.$options['extension'].'&id='.$item->id;
			$html	.=	'<li><a class="featured_sessions" href="javascript:void(0);" mydata="'.$item->type.'"'
					.	'mydata2="'.htmlspecialchars( $item->options ).'">' . $item->title . '</a><span class="featured_sessions_del icon-delete" mydata="'.$item->id.'"></span><a href="'.$edit_link.'" class="featured_sessions_edit icon-edit"></a>'
					.	'</li>';
		}
		$html	=	'<button class="btn btn-primary dropdown-toggle cck-float-none" type="button" data-toggle="dropdown"><span class="caret"></span></button>'
				.	'<ul class="dropdown-menu featured-sessions pull-right">'.$html.'</ul>';
		$js		=	'jQuery(document).ready(function($){ $("#'.$id.'").after(\''.$html.'\'); });';

		if ( $css ) {
			$doc->addStyleDeclaration( $css );
		}
		if ( $js ) {
			$doc->addScriptDeclaration( $js );
		}
	}
}
?>