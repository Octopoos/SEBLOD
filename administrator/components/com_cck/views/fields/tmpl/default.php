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

$action			=	'<span class="icon-eye"></span>';
$action_attr	=	' class="cbox btn btn-micro hasTooltip" title="'.JText::_( 'COM_CCK_PREVIEW_THIS_FIELD' ).'"';
$css			=	array();
$doc			=	JFactory::getDocument();
$user			=	JFactory::getUser();
$userId			=	$user->id;
$listOrder		=	$this->state->get( 'list.ordering' );
$listDir		=	$this->state->get( 'list.direction' );
$location		=	$this->state->get( 'filter.location' );
$search			=	$this->state->get( 'filter.search' );
$canOrder		=	0;
$saveOrder		=	0;
$top			=	'content';

$config			=	JCckDev::init( array( '42', 'button_submit', 'select_simple', 'text' ), true, array( 'vName'=>$this->vName ) );
$cck			=	JCckDev::preload( array( 'core_filter_input', 'core_filter_go', 'core_filter_search', 'core_filter_clear', 'core_location_filter',
										 'core_type_filter', 'core_folder_filter', 'core_state_filter', 'core_folder' ) );
JText::script( 'COM_CCK_CONFIRM_DELETE' );
JPluginHelper::importPlugin( 'cck_storage_location' );
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
		<tr>
			<th width="32" class="center hidden-phone nowrap"><?php Helper_Display::quickSlideTo( 'pagination-bottom', 'down' ); ?></th>
			<th width="30" class="center hidden-phone">
            	<input type="checkbox" name="toggle" value="" title="<?php echo JText::_( 'JGLOBAL_CHECK_ALL' ); ?>" onclick="Joomla.checkAll(this);" />
			</th>
			<th class="center" colspan="2"><?php echo JHtml::_( 'grid.sort', 'COM_CCK_TITLE', 'a.title', $listDir, $listOrder ); ?></th>
			<th width="20%" class="center hidden-phone nowrap" colspan="2"><?php echo JHtml::_( 'grid.sort', 'COM_CCK_'._C0_TEXT, 'folder_title', $listDir, $listOrder ); ?></th>
			<th width="15%" class="center hidden-phone nowrap"><?php echo JText::_( 'COM_CCK_STORAGE' ); ?></th>
			<th width="15%" class="center hidden-phone nowrap"><?php echo JHtml::_( 'grid.sort', 'COM_CCK_TYPE', 'a.type', $listDir, $listOrder ); ?></th>
            <?php if ( $location == 'folder_id' && $search > 0 ) {
				$canOrder	=	$user->authorise( 'core.edit.state', 'com_cck.folder' );
				$saveOrder	=	( JCckDatabase::loadResult( 'SELECT featured FROM #__cck_core_folders WHERE id = '.(int)$search ) ); ?>
                <th width="10%" class="center hidden-phone nowrap">
                    <?php
                    echo JHtml::_( 'grid.sort',  'JGRID_HEADING_ORDERING', 'a.ordering', $listDir, $listOrder );
					if ( $canOrder && $saveOrder ) {
						echo JHtml::_( 'grid.order',  $this->items, 'filesave.png', 'fields.saveorder' );
					}
					?>
                </th>
            <?php } else { ?>
                <th width="10%" class="center nowrap"><?php echo JHtml::_( 'grid.sort', 'COM_CCK_STATUS', 'a.published', $listDir, $listOrder ); ?></th>
            <?php } ?>
			<th width="32" class="center hidden-phone nowrap"><?php echo JHtml::_( 'grid.sort', 'COM_CCK_ID', 'a.id', $listDir, $listOrder ); ?></th>
		</tr>
	</thead>
    <tbody>
	<?php
	foreach ( $this->items as $i=>$item ) {
		$ordering		=	( $listOrder == 'ordering' );
		$checkedOut		= 	! ( $item->checked_out == $userId || $item->checked_out == 0 );
		$canCheckin		=	$user->authorise( 'core.manage', 'com_checkin' ) || $item->checked_out == $userId || $item->checked_out == 0;
		$canChange		=	$user->authorise( 'core.edit.state', CCK_COM.'.folder.'.$item->folder ) && $canCheckin;
		$canEdit		=	$user->authorise( 'core.edit', CCK_COM.'.folder.'.$item->folder );
		$canEditFolder	=	$user->authorise( 'core.edit', CCK_COM.'.folder.'.$item->folder );
		$canEditOwn		=	'';
		
		$link 			=	JRoute::_( 'index.php?option='.$this->option.'&task='.$this->vName.'.edit&id='. $item->id );
		$link2			=	JRoute::_( 'index.php?option=com_cck&task=box.add&tmpl=component&file=administrator/components/com_cck/views/field/tmpl/preview.php&name='.$item->name );
		$linkFilter		=	JRoute::_( 'index.php?option='.$this->option.'&view='.$this->getName().'&folder_id='.$item->folder );
		$linkFolder		=	JRoute::_( 'index.php?option='.$this->option.'&task=folder.edit&id='. $item->folder );
		
		Helper_Admin::addFolderClass( $css, $item->folder, $item->folder_color, $item->folder_colorchar );
		?>
		<tr class="row<?php echo $i % 2; ?>" height="64px;">
			<td class="center hidden-phone"><?php Helper_Display::quickSlideTo( 'pagination-bottom', $i + 1 ); ?></td>
			<td class="center hidden-phone"><?php echo JHtml::_( 'grid.id', $i, $item->id ); ?></td>
			<td width="30px" class="center hidden-phone">
            	<?php if ( $item->id != 33 ) { ?>
					<a href="<?php echo $link2; ?>"<?php echo $action_attr; ?>>
						<?php echo $action; ?>
					</a>
                <?php } ?>
            </td>
			<td>
				<div class="title-left" id="title-<?php echo $item->id; ?>">
					<?php
					if ( $item->checked_out ) {
						echo JHtml::_( 'jgrid.checkedout', $i, $item->editor, $item->checked_out_time, $this->vName.'s.', $canCheckin )."\n";
					}
					if ( ( $canEdit && ! $checkedOut ) && $item->id != 33 ) {
						echo '<a href="'.$link.'">'.$this->escape( $item->title ).'</a><div class="small">'.$this->escape( $item->name ).'</div>';
					} else {
						echo '<span>'.$this->escape( $item->title ).'</span><div class="small">'.$this->escape( $item->name ).'</div>';
					}
					?>
				</div>
			</td>
			<td align="center" width="4%">
                <a href="<?php echo $linkFilter; ?>" style="text-decoration: none;" class="hidden-phone">
                    <div class="<?php echo ( $item->folder_color || ( $item->folder_colorchar && $item->folder_introchar ) ) ? 'folderColor'.$item->folder : ''; ?>" style="vertical-align: middle;">
                        <strong><?php echo $item->folder_introchar; ?></strong>
                    </div>
                </a>
			</td>
			<td class="center hidden-phone">
            	<?php
                if ( ! $item->folder_parent ) {
                    $linkFolderTree	=	JRoute::_( 'index.php?option='.$this->option.'&view=folders&filter_folder='. $item->folder );
                    $folder_parent	=	'';
                } else {
                    $linkFolderTree	=	JRoute::_( 'index.php?option='.$this->option.'&view=folders&filter_folder='. $item->folder_parent );
                    $folder_parent	=	'<br /><a class="folder-parent small" href="'.$linkFolderTree.'">'.$item->folder_parent_title.'</a>';
                }
				echo ( $canEditFolder ) ? '<a href="'.$linkFolder.'">' . $this->escape( $item->folder_title ) . '</a>' . $folder_parent : '<span>' . $this->escape( $item->folder_title ) . '</span>' . $folder_parent;
                ?>
			</td>
            <td class="center hidden-phone small">
				<?php
				$storage	=	'<strong>'.( $item->storage == 'dev' ? 'dev' : $item->storage_table ) .'</strong><br />'. ( $item->storage_field2 ? $item->storage_field.'['.$item->storage_field2.']' : $item->storage_field );
                echo ( $item->storage == 'none' ) ? '-' : '<span class="storage-format hasTooltip" title="'.htmlspecialchars( $storage ).'">'.( $item->storage ).'</span>';
				?>
            </td>
            <td class="center hidden-phone small"><?php echo JText::_( 'PLG_CCK_FIELD_'.$item->type.'_LABEL2' ); ?></td>
            <?php if ( $location == 'folder_id' ) { ?>
				<td class="center order">
					<?php if ( $canChange ) {
						$disabled	=	$saveOrder ?  '' : 'disabled="disabled"'; ?>
						<input type="text" name="order[]" size="5" value="<?php echo $item->ordering; ?>" <?php echo $disabled; ?> class="text-area-order input-mini" style="text-align:center; width:30px;" />
					<?php } else {
						echo $item->ordering;
					} ?>
				</td>
            <?php } else { ?>
				<td class="center"><?php echo JHtml::_( 'jgrid.published', $item->published, $i, $this->vName.'s.', $canChange, 'cb' ); ?></td>
            <?php } ?>
			<td class="center hidden-phone"><?php Helper_Display::quickSlideTo( $top, $item->id ); ?></td>
		</tr>
		<?php
	}
	?>
    </tbody>
	<tfoot>
		<tr height="40px;">
	        <td class="center hidden-phone"><?php Helper_Display::quickSlideTo( $top, 'up' ); ?></td>
			<td class="center" colspan="8" id="pagination-bottom"><?php echo $this->pagination->getListFooter(); ?></td>
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
	<input type="hidden" name="return_v" id="return_v" value="fields" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDir; ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>
</div>

<?php
Helper_Include::addStyleDeclaration( implode( '', $css ) );
Helper_Display::quickCopyright();

$js	=	'
		(function ($){
			Joomla.orderTable = function()
			{
				table = document.getElementById("sortTable");
				direction = document.getElementById("directionTable");
				order = table.options[table.selectedIndex].value;
				if (order != "'.$listOrder.'") {
					dirn = "asc";
				} else {
					dirn = direction.options[direction.selectedIndex].value;
				}
				Joomla.tableOrdering(order, dirn, "");
			}
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
			$(document).ready(function() {
				$(document).keypress(function(e) {
					if (!$(":input:focus").length) {
						e.preventDefault();

						if (e.which == 64) {
							if ( $("#filter_search").val() != "" ) {
								$("#filter_search").select();
							} else {
								$("#filter_search").focus();
							}
						} else if (e.which == 110) {
							$("#toolbar-new > button").click();
						}
					}
				});
			});
		})(jQuery);
		';
$doc->addScriptDeclaration( $js );
?>
</div>
</form>