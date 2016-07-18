<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: default.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$app	=	JFactory::getApplication();
$uix	=	JCck::getUIX();
Helper_Include::addDependencies( $this->getName(), $this->getLayout() );

if ( $app->input->get( 'debug', 0 ) == 1 ) {
	include_once JPATH_COMPONENT.'/helpers/scripts/_debug.php';
}
?>

<form action="<?php echo JRoute::_( 'index.php?option='.$this->option ); ?>" method="post" id="adminForm" name="adminForm">
<?php if ( !empty( $this->sidebar ) ) { ?>
    <div id="j-sidebar-container" class="span3">
        <?php echo $this->sidebar; ?>
    </div>
    <div id="j-main-container" class="span9">
<?php } else { ?>
    <div id="j-main-container">
<?php } ?>

<div class="<?php echo $this->css['wrapper']; ?> hidden-phone">
    <div class="<?php echo $this->css['w100']; ?>">
        <div class="seblod first cpanel_news <?php echo $uix; ?>">
            <div class="legend top center plus"><?php echo CCK_LABEL .' &rarr; '. JText::_( 'COM_CCK_'.CCK_BUILDER ); ?></div>
            <ul class="adminformlist">
                <li style="text-align:center;">
                    <?php echo JText::_( 'COM_CCK_'.CCK_BUILDER.'_DESC' ); ?>
                </li>
            </ul>
            <div class="clr"></div>
        </div>
        <div class="seblod-less cpanel_news <?php echo $uix; ?>">
            <?php echo JCckDevAccordion::start( 'cckOptions', 'collapse0', JText::_( 'COM_CCK_PANE_CORE' ), array( 'active'=>'collapse0', 'useCookie'=>'1' ) ); ?>
            <div id="cpanel" class="cpanel <?php echo $uix; ?>">
                <?php
                if ( $uix == 'compact' ) {
                    echo '<div class="fltlft">';
                    Helper_Admin::addIcon( CCK_COM, _C2_LINK, _C2_NAME, JText::_( 'COM_CCK_'._C2_TEXT.'_MANAGER'.'_BR' ), 48, 'left', '6' );
                    Helper_Admin::addIcon( CCK_COM, _C0_LINK, _C0_NAME, JText::_( 'COM_CCK_'._C0_TEXT.'_MANAGER'.'_BR' ) );
                    echo '</div>';
                } else {
                    echo '<div class="fltlft">';
                    Helper_Admin::addIcon( CCK_COM, _C2_LINK, array( _C2_NAME, 'icon-cck-form' ), JText::_( 'COM_CCK_'._C2_TEXT.'_MANAGER'.'_BR' ) );
                    Helper_Admin::addIcon( CCK_COM, _C3_LINK, array( _C3_NAME, 'icon-cck-plugin' ), JText::_( _C3_TEXT.'_MANAGER'.'_BR' ) );
                    Helper_Admin::addIcon( CCK_COM, _C4_LINK, array( _C4_NAME, 'icon-cck-search' ), JText::_( 'COM_CCK_'._C4_TEXT.'_MANAGER'.'_BR' ) );
                    Helper_Admin::addIcon( CCK_COM, _C1_LINK, array( _C1_NAME, 'icon-cck-template' ), JText::_( _C1_TEXT.'_MANAGER'.'_BR' ) );
                    echo '</div><div class="clr"></div>'
                     .   '<div class="fltlft">';
                    Helper_Admin::addIcon( CCK_COM, 'spacer', 'spacer', 'spacer', 24 );
                    Helper_Admin::addIcon( CCK_COM, _C0_LINK, array( _C0_NAME, 'icon-cck-application' ), JText::_( 'COM_CCK_'._C0_TEXT.'_MANAGER'.'_BR' ), 24, 'left' );
                    Helper_Admin::addIcon( CCK_COM, _C5_LINK, array( _C5_NAME, 'icon-cck-multisite' ), JText::_( _C5_TEXT.'_MANAGER'.'_BR' ), 24, 'right' );
                    echo '</div>';
                }
                ?>
            </div>
            <?php
            if ( $uix == 'compact' ) {
                echo JCckDevAccordion::open( 'cckOptions', 'collapse1', JText::_( 'COM_CCK_PANE_NANO' ) );
                ?>
                <div class="<?php echo $this->css['items']; ?>">
                    <ul class="adminformlist">
                        <li>
                            <span class="variation_value" style="font-size:12px; font-style:italic; text-align:center;"><?php echo JText::_( 'COM_CCK_PANE_NANO_DESC' ); ?></span>
                        </li>
                    </ul>
                </div>
            <?php } ?>
            <?php echo JCckDevAccordion::end(); ?>
            <?php if ( $uix != 'compact' ) { ?>
            <div class="alert alert-info">
                <a href="http://jed.seblod.com" target="_blank" class="close"><span class="icon-arrow-right-2"></span></a>
                <div><strong>If you use SEBLOD, please post a rating & review at the JED. Thank you.</strong></div>
            </div>
            <?php } ?>
        </div>
        
    </div>
</div>

<div class="clr"></div>
<div>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_( 'form.token' ); ?>
</div>

<?php
Helper_Display::quickCopyright( true );
?>
</div>
</form>