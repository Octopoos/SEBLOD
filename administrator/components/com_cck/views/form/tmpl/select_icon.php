<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: select_icon.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;
?>

<div id="cpanel" class="cpanel box">
	<?php
    foreach ( $items as $item ) {
		if ( $user->authorise( 'core.create', 'com_cck.form.'.$item->id ) ) {
			$image	=	( $item->folder_icon ) ? $item->folder_icon : 'administrator/components/com_cck/assets/images/48/icon-48-form.png';
			$key	=	'APP_CCK_FORM_'.$item->name;
			$lang->load( 'pkg_app_cck_'.$item->folder_app, JPATH_SITE, null, false, false );
			$link	=	$base.'&type='.$item->name.$variables;
			if ( $lang->hasKey( $key ) == 1 ) {
				$text	=	JText::_( $key );
			} else {
				$text	=	$item->title;
			}
		?>
        <div class="wrapper-icon">
            <div class="icon">
                <a href="<?php echo $link; ?>" target="_parent">
                    <?php echo JHtml::_( 'image', $image, htmlspecialchars( str_replace( '<br />', ' ', $text ) ) ); ?>
                    <span><?php echo $text; ?></span>
				</a>
            </div>
        </div>
    <?php } } ?>
</div>