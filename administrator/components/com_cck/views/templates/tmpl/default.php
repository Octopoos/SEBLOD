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
$action_attr	=	' class="cbox btn btn-micro hasTooltip" title="'.JText::_( 'COM_CCK_PREVIEW_THIS_TEMPLATE' ).'"';
$css			=	array();
$doc			=	JFactory::getDocument();
$user			=	JFactory::getUser();
$userId			=	$user->id;
$listOrder		=	$this->state->get( 'list.ordering' );
$listDir		=	$this->state->get( 'list.direction' );
$top			=	'content';

$config			=	JCckDev::init( array( '42', 'button_submit', 'select_simple', 'text' ), true, array( 'vName'=>$this->vName ) );
$cck			=	JCckDev::preload( array( 'core_filter_input', 'core_filter_go', 'core_filter_search', 'core_filter_clear', 'core_location_filter',
										 'core_type_filter_template', 'core_folder_filter', 'core_state_filter', 'core_folder' ) );
jimport( 'joomla.filesystem.folder' );
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
		<tr>
			<th width="32" class="center hidden-phone nowrap"><?php Helper_Display::quickSlideTo( 'pagination-bottom', 'down' ); ?></th>
			<th width="30" class="center hidden-phone">
            	<input type="checkbox" name="toggle" value="" title="<?php echo JText::_( 'JGLOBAL_CHECK_ALL' ); ?>" onclick="Joomla.checkAll(this);" />
			</th>
			<th class="center" colspan="2"><?php echo JHtml::_( 'grid.sort', 'COM_CCK_TITLE', 'a.title', $listDir, $listOrder ); ?></th>
			<th width="20%" class="center hidden-phone nowrap" colspan="2"><?php echo JHtml::_( 'grid.sort', 'COM_CCK_'._C0_TEXT, 'folder_title', $listDir, $listOrder ); ?></th>
			<th width="15%" class="center hidden-phone nowrap"><?php echo JHtml::_( 'grid.sort', 'COM_CCK_TYPE', 'a.mode', $listDir, $listOrder ); ?></th>
			<th width="15%" class="center hidden-phone nowrap"><?php echo JText::_( 'COM_CCK_DETAILS' ); ?></th>
			<th width="10%" class="center nowrap"><?php echo JHtml::_( 'grid.sort', 'COM_CCK_STATUS', 'a.published', $listDir, $listOrder ); ?></th>
			<th width="32" class="center hidden-phone nowrap"><?php echo JHtml::_( 'grid.sort', 'COM_CCK_ID', 'a.id', $listDir, $listOrder ); ?></th>
		</tr>
	</thead>
    <tbody>
	<?php
	foreach ( $this->items as $i=>$item ) {
		$checkedOut		= 	! ( $item->checked_out == $userId || $item->checked_out == 0 );
		$canCheckin		=	$user->authorise( 'core.manage', 'com_checkin' ) || $item->checked_out == $userId || $item->checked_out == 0;
		$canChange		=	$user->authorise( 'core.edit.state', CCK_COM.'.folder.'.$item->folder ) && $canCheckin;
		$canEdit		=	$user->authorise( 'core.edit', CCK_COM.'.folder.'.$item->folder );
		$canEditFolder	=	$user->authorise( 'core.edit', CCK_COM.'.folder.'.$item->folder );
		$canEditOwn		=	'';
		
		$link 			=	JRoute::_( 'index.php?option='.$this->option.'&task='.$this->vName.'.edit&id='. $item->id );
		$link2			=	JRoute::_( 'index.php?option=com_cck&task=box.add&tmpl=component&file='.JUri::root().'/templates/'.$item->name.'/template_preview.png' );
		$linkFilter		=	JRoute::_( 'index.php?option='.$this->option.'&view='.$this->getName().'&folder_id='.$item->folder );
		$linkFolder		=	JRoute::_( 'index.php?option='.$this->option.'&task=folder.edit&id='. $item->folder );
		Helper_Admin::addFolderClass( $css, $item->folder, $item->folder_color, $item->folder_colorchar );
		?>
		<tr class="row<?php echo $i % 2; ?>" height="64px;">
			<td class="center hidden-phone"><?php Helper_Display::quickSlideTo( 'pagination-bottom', $i + 1 ); ?></td>
			<td class="center hidden-phone"><?php echo JHtml::_( 'grid.id', $i, $item->id ); ?></td>
			<td width="30px" class="center hidden-phone">
            	<a href="<?php echo $link2; ?>"<?php echo $action_attr; ?>>
            		 <?php echo $action; ?>
            	</a>
			</td>
			<td>
				<div class="title-left" id="title-<?php echo $item->id; ?>">
					<?php
					if ( $item->checked_out ) {
						echo JHtml::_( 'jgrid.checkedout', $i, $item->editor, $item->checked_out_time, $this->vName.'s.', $canCheckin )."\n";
					}
					if ( $canEdit && ! $checkedOut ) {
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
				echo ( $canEditFolder ) ? '<a href="'.$linkFolder.'">' . $this->escape( $item->folder_title ) . '</a>' . $folder_parent
										: '<span>' . $this->escape( $item->folder_title ) . '</span>' . $folder_parent;
                ?>
			</td>
			<td class="center hidden-phone small">
				<?php
				switch ( $item->mode ) {
					case 2: echo JText::_( 'COM_CCK_LIST' ); break;
					case 0:
					default: echo JText::_( 'COM_CCK_CONTENT_FORM' ); break;
				}
                ?>
			</td>
			<td class="center hidden-phone small">
	            <?php
				$positions	=	'-';
                $path		=	JPATH_SITE.'/templates/'.$item->name.'/templateDetails.xml';
				if ( file_exists( $path ) ) {
					$xml	=	simplexml_load_file( $path );
					if ( isset( $xml->positions[0] ) ) {
						$count		=	count( $xml->positions[0] );
						$positions	=	( $count > 0 ) ? JText::_( 'COM_CCK_POSITIONS' ).': '.$count : '-';
					}
				}
				$overrides	=	'-';
				$path		=	JPATH_SITE.'/templates/'.$item->name.'/positions';
				if ( file_exists( $path ) ) {
					$overrides	=	JFolder::files( $path, '^[^_]*\.php$', true, false );
					$count		=	count( $overrides );
					$overrides	=	( $count > 0 ) ? JText::_( 'COM_CCK_OVERRIDES' ).': '.$count : '-';
				}
				$variations	=	'-';
				$path		=	JPATH_SITE.'/templates/'.$item->name.'/variations';
				if ( file_exists( $path ) ) {
					$variations	=	JFolder::folders( $path, '.', false, false );
					$count		=	count( $variations );
					$variations	=	( $count > 0 ) ? JText::_( 'COM_CCK_VARIATIONS' ).': '.$count : '-';
				}
				echo $overrides.'<br />'.$positions.'<br />'.$variations;
				?>
			</td>
			<td class="center">
				<div class="btn-group">
					<?php
					echo JHtml::_( 'jgrid.published', $item->published, $i, $this->vName.'s.', $canChange, 'cb' );

					Helper_Display::quickJGrid( 'featured', $item->featured, $i, false );
					?>
				</div>
			</td>
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
	<input type="hidden" name="return_v" id="return_v" value="templates" />
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