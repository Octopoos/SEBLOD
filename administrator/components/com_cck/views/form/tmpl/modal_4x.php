<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: modal_icon.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\String\StringHelper;

$app		=	JFactory::getApplication();
$base		=	'index.php?option=com_cck&view=form';
$doc		=	JFactory::getDocument();
$lang		=	JFactory::getLanguage();
$type		=	'';
$user		=	JFactory::getUser();
if ( isset( $params['quickadd'] ) && $params['quickadd'] == 1 ) {
	$legacy	=	0;
	$url	=	'none';
} else {
	$legacy	=	$options->get( 'add_alt' );
	$url	=	'';
}
$folders	=	JCckDevIntegration::getForms( $url, $type, 'folder' );
$target_id	=	( isset( $target_id ) ) ? $target_id : 'collapseModal2';

if ( $legacy == 1 ) {
	$legend		=	'';
	$legend		=	JText::sprintf( 'LIB_CCK_INTEGRATION_MODAL_BOX_LEGEND1',
									'<a id="joomla-standard-content" href="'.$options->get( 'add_alt_link' ).'">'.JText::_( 'LIB_CCK_INTEGRATION_CLICK_HERE' ).'</a>' );
	$legend2	=	'';
} elseif ( $legacy == 2 ) {
	$legend		=	JText::_( 'LIB_CCK_INTEGRATION_SELECT_A_FORM' );
	$legend2	=	JText::sprintf( 'LIB_CCK_INTEGRATION_MODAL_BOX_LEGEND2', '<a id="joomla-standard-content" href="'.$options->get( 'add_alt_link' ).'">'.StringHelper::strtolower( JText::_( 'LIB_CCK_INTEGRATION_CLICK_HERE' ) ).'</a>' );
} else {
	$legend		=	JText::_( 'LIB_CCK_INTEGRATION_SELECT_A_FORM' );
	$legend2	=	'';
}
// $doc->addStyleDeclaration( 'div.modal-footer a.pull-left{position:relative; top:8px;}' );
?>
<div class="modal modal-small hide fade" id="collapseModal2">
	<div class="modal-dialog modal-lg"><div class="modal-content">
	<div class="modal-header">
		<h3 class="modal-title">New Form &amp; Content Type</h3><button type="button" class="btn-close novalidate" data-bs-dismiss="modal" aria-label="Close"></button>
	</div>
	<div class="modal-body"><div class="contentpane component">
	<?php
		// echo JHtml::_( 'bootstrap.startAccordion', $target_id.'-cckForms', array( 'active'=>'slide1' ) );
		$i	=	0;
		foreach ( $folders as $items ) {
			if ( isset( $items[0] ) ) {
				$lang->load( 'pkg_app_cck_'.$items[0]->folder_app, JPATH_SITE, null, false, false );
				$key		=	'APP_CCK_'.$items[0]->folder_app;
				if ( $lang->hasKey( $key ) == 1 ) {
					$name	=	JText::_( $key );
				} else {
					$name	=	$items[0]->folder;
				}
			} else {
				$name	=	JText::_( 'COM_CCK_FOLDER' );
			}
			// echo JHtml::_( 'bootstrap.addSlide', $target_id.'-cckForms', $name, $target_id.'-collapseFolder'.$i++ );
			echo '<h4>'.$name.'</h4>';
		?>
			<div class="list-group pb-3">
			<?php
            foreach ( $items as $item ) {
				if ( $user->authorise( 'core.create', 'com_cck.form.'.$item->id ) ) {
					$desc			=	'APP_CCK_'.$item->name.'_DESC';					
					$description	=	( $lang->hasKey( $desc ) == 1 ) ? JText::_( $desc ) : JText::_( 'LIB_CCK_INTEGRATION_CLICK_TO_SELECT_THIS_FORM' );
					$link			=	$base.'&type='.$item->name.$variables;
					$key			=	'APP_CCK_FORM_'.$item->name;
					if ( $lang->hasKey( $key ) == 1 ) {
						$text	=	JText::_( $key );
					} else {
						$text	=	$item->title;
					}
				?>
				<a class="choose_type list-group-item list-group-item-action" href="<?php echo $link; ?>">
					<?php if ( $doc->direction != "rtl" ) { ?>
						<?php echo $text; ?><!-- <small class="muted"><?php echo $description; ?></small> -->
					<?php } else { ?>
						<!-- <small class="muted"><?php echo $description; ?></small>  --><?php echo $text; ?>
					<?php } ?>
				</a>
			<?php } } ?>
			</div>
		<?php
        	// echo JHtml::_( 'bootstrap.endSlide' );
		}
		// echo JHtml::_( 'bootstrap.endAccordion' );
		echo $legend2;
		?>
	</div></div>
	<div class="modal-footer">
		<button class="btn btn-secondary" onclick="window.open('https://www.seblod.com/store/extensions/applications/', '_blank'); return false;">
			<?php echo JText::_( 'LIB_CCK_INTEGRATION_GET_MORE_APPS' ); ?>
		</button>
		<button class="btn btn-secondary" type="button" data-dismiss="modal">
			<?php echo JText::_( 'JCANCEL' ); ?>
		</button>
	</div>
	</div></div>
</div>