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

$action			=	'<span class="icon-download"></span>';
$action_attr	=	' class="btn btn-micro hasTooltip" title="'.JText::_( 'COM_CCK_DOWNLOAD_THIS_VARIATION' ).'"';
$doc			=	JFactory::getDocument();
$user			=	JFactory::getUser();
$userId			=	$user->id;
$listOrder		=	$this->state->get( 'list.ordering' );
$listDir		=	$this->state->get( 'list.direction' );
$top			=	'content';

$config			=	JCckDev::init( array( '42', 'button_submit', 'select_simple', 'text' ), true, array( 'vName'=>$this->vName ) );
$cck			=	JCckDev::preload( array( 'core_filter_input', 'core_filter_go', 'core_filter_search', 'core_filter_clear', 'core_location_filter' ) );
JText::script( 'COM_CCK_CONFIRM_DELETE' );
Helper_Include::addDependencies( $this->getName(), $this->getLayout() );
?>

<form action="<?php echo JRoute::_( 'index.php?option='.$this->option.'&view='.$this->getName() ); ?>" method="post" id="adminForm" name="adminForm">
<?php if ( !empty( $this->sidebar ) ) { ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
<?php } else { ?>
	<div id="j-main-container">
<?php } ?>

<?php include_once __DIR__.'/default_filter.php'; ?>
<div class="<?php echo $this->css['items']; ?>">
	<table class="<?php echo $this->css['table']; ?>">
	<thead>
		<tr class="half">
			<th width="32" class="center hidden-phone nowrap"><?php Helper_Display::quickSlideTo( 'pagination-bottom', 'down' ); ?></th>
			<th width="30" class="center hidden-phone">
            	<input type="checkbox" name="toggle" value="" title="<?php echo JText::_( 'JGLOBAL_CHECK_ALL' ); ?>" onclick="Joomla.checkAll(this);" />
			</th>
			<th class="center" colspan="2"><?php echo JHtml::_( 'grid.sort', 'COM_CCK_TITLE', 'a.title', $listDir, $listOrder ); ?></th>
            <th width="30%" class="center hidden-phone nowrap"><?php echo JText::_( 'COM_CCK_FOLDER' ); ?></th>
            <th width="20%" class="center hidden-phone nowrap"><?php echo JText::_( 'COM_CCK_LOCATION' ); ?></th>
			<th width="32" class="center hidden-phone nowrap"><?php echo JHtml::_( 'grid.sort', 'COM_CCK_ID', 'a.id', $listDir, $listOrder ); ?></th>
		</tr>
	</thead>
    <tbody>
	<?php
	foreach ( $this->items as $i=>$item ) {
		$link	=	JRoute::_( 'index.php?option='.$this->option.'&task=template.export_variation&variation='.$item->title.'&folder='.$item->folder );
		?>
		<tr class="row<?php echo $i % 2; ?>" height="64px;">
			<td class="center hidden-phone"><?php Helper_Display::quickSlideTo( 'pagination-bottom', $i + 1 ); ?></td>
			<td class="center hidden-phone"><?php echo JHtml::_( 'grid.id', $i, $item->id ); ?></td>
            <td width="30px" class="center hidden-phone">
            	<a href="<?php echo $link; ?>"<?php echo $action_attr; ?>>
            		<?php echo $action; ?>
            	</a>
			</td>
			<td>
				<div class="title-left" id="title-<?php echo $item->id; ?>">
					<?php
					if ( !$item->type ) {
						echo '<span><strong>'.$this->escape( $item->title ).'</strong></span>';
					} else {
						echo '<span>'.$this->escape( $item->title ).'</span>';
					}
					?>
				</div>
			</td>
            <td class="center hidden-phone"><?php echo $item->folder; ?></td>
            <td class="center hidden-phone"><?php echo ( !$item->template ) ? strtolower( JText::_( 'COM_CCK_LIBRARY' ) ) : $item->template; ?></td>
			<td class="center hidden-phone"><?php Helper_Display::quickSlideTo( $top, $item->id ); ?></td>
		</tr>
		<?php
	}
	?>
    </tbody>
	<tfoot>
		<tr height="40px;">
	        <td class="center hidden-phone"><?php Helper_Display::quickSlideTo( $top, 'up' ); ?></td>
			<td class="center" colspan="5" id="pagination-bottom"><?php echo $this->pagination->getListFooter(); ?></td>
			<td class="center hidden-phone"><?php Helper_Display::quickSlideTo( $top, 'up' ); ?></td>
		</tr>
	</tfoot>
	</table>
</div>
<?php include_once __DIR__.'/default_batch.php'; ?>
<div class="clr"></div>
<div>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="return_v" id="return_v" value="variations" />
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDir; ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>
</div>

<?php
Helper_Display::quickCopyright();

$js	=	'
		(function ($){
			Joomla.submitbutton = function(task, cid) {
				if (task == "'.$this->vName.'s.delete") {
					if (confirm(Joomla.JText._("COM_CCK_CONFIRM_DELETE"))) {
						Joomla.submitform(task);
					} else {
						return false;
					}
				}
				Joomla.submitform(task);
			}
		})(jQuery);
		';
$doc->addScriptDeclaration( $js );
?>
</div>
</form>