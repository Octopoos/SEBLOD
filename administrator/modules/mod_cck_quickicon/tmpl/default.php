<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: default.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$html	=	JHtml::_( 'links.linksgroups', modCCKQuickIconHelper::groupButtons( $buttons ) );

if ( !empty( $html ) ) { ?>
    <div class="sidebar-nav quick-icons">
        <?php echo $html; ?>
    </div>
<?php } ?>