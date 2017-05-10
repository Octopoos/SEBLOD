<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: default_button.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;
?>
<div class="wrapper-icon">
	<div class="icon">
		<a href="<?php echo $button['link']; ?>" target="<?php echo $button['target']; ?>">
			<?php echo JHtml::_( 'image', 'administrator/components/com_cck/assets/images/48/'.$button['icon'], htmlspecialchars( $button['text'] ) ); ?>
			<span><?php echo $button['label']; ?></span>
		</a>
	</div>
</div>