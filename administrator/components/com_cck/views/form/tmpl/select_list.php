<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: select_list.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;
?>

<ul class="menu_types">
	<?php
    foreach ( $items as $item ) {
        if ( isset( $item[0] ) ) {
            $lang->load( 'pkg_app_cck_'.$item[0]->folder_app, JPATH_SITE, null, false, false );
            $key    =   'APP_CCK_'.$item[0]->folder_app;
            if ( $lang->hasKey( $key ) == 1 ) {
                $name   =   JText::_( $key );
            } else {
                $name   =   $item[0]->folder;
            }
        } else {
            $name   =   JText::_( 'COM_CCK_FOLDER' );
        }
	?>
	<li>
        <dl class="menu_type">
            <dt><?php echo $name; ?></dt>
            <dd>
                <ul>
                    <?php
                    foreach ( $item as $i ) {
						if ( $user->authorise( 'core.create', 'com_cck.form.'.$i->id ) ) {
							$desc      =	strip_tags( $i->description );
	                        $key       =   'APP_CCK_FORM_'.$i->name;
                            if ( $lang->hasKey( $key ) == 1 ) {
                                $text   =   JText::_( $key );
                            } else {
                                $text   =   $i->title;
                            }
                            $link       =   $base.'&type='.$i->name.$variables;
						?>
                        <li>
                            <a href="<?php echo $link; ?>" target="_parent" title="<?php echo $desc; ?>"><?php echo $text; ?></a>
                        </li>
                    <?php } } ?>
                </ul>
            </dd>
        </dl>
	</li>
	<?php } ?>
</ul>