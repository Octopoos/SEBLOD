<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: helper_display.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

require_once JPATH_ADMINISTRATOR.'/components/'.CCK_COM.'/helpers/common/display.php';

// Helper
class Helper_Display extends CommonHelper_Display
{	
	// quickCopyright
	public static function quickCopyright( $cpanel = false )
	{
		?>
		<div class="copyright">
			<strong><a target="_blank" href="<?php echo CCK_WEBSITE; ?>"><?php echo CCK_LABEL; ?></a></strong>&nbsp;<?php echo JText::sprintf( 'COM_CCK_COPYRIGHT_SEBLOD', JText::_( 'COM_CCK_'.CCK_BUILDER ) ); ?>
			<br /><?php echo JText::_( 'JVERSION' ).' '. CCK_VERSION . ' &copy 2009 - 2017'; ?>
			<?php
			if ( $cpanel !== false ) {
				$info	=	'General Availability | Site runs on PHP '.PHP_VERSION;
				if ( version_compare( PHP_VERSION, '5.3', '<' ) ) {
					$info	.=	' >> PHP 5.3 is recommended !';
				}
				echo '<br /><br /><span style="color: #999999; font-size: 10px; font-weight: bold;">'.$info.'</span>';
			}
			?>
		</div>
		<?php
	}
}
?>