<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: default.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$uix			=	JCck::getUIX();
$css			=	array();
$doc			=	JFactory::getDocument();
$user			=	JFactory::getUser();
$userId			=	$user->id;
$label			=	strtolower( JText::_( 'COM_CCK_FIELDS' ) );
$listOrder		=	$this->state->get( 'list.ordering' );
$listDir		=	$this->state->get( 'list.direction' );
$template_name	=	Helper_Admin::getDefaultTemplate();
$title2			=	JText::_( 'COM_CCK_CREATE_ITEM_USING_THIS_FORM' );
$top			=	( !JCck::on() ) ? 'border-top' : 'content';

$config			=	JCckDev::init( array( '42', 'button_submit', 'select_simple', 'text' ), true, array( 'vName'=>$this->vName ) );
$cck			=	JCckDev::preload( array( 'core_filter_input', 'core_filter_go', 'core_filter_search', 'core_filter_clear', 'core_location_filter',
										 'core_folder_filter', 'core_state_filter', 'core_folder', 'core_dev_text', 'core_storage_location2', 'core_client_filter' ) );
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

<?php include_once dirname(__FILE__).'/default_filter.php'; ?>
<div class="<?php echo $this->css['items']; ?>">
	<?php
    if ( $uix == 'compact' ) {
		include_once dirname(__FILE__).'/default_compact.php';
	} else {
	?>
	<table class="<?php echo $this->css['table']; ?>">
	<thead>
		<tr>
			<th width="32" class="center hidden-phone"><?php Helper_Display::quickSlideTo( 'pagination-bottom', 'down' ); ?></th>
			<th width="30" class="center hidden-phone">
            	<input type="checkbox" name="toggle" value="" title="<?php echo JText::_( 'JGLOBAL_CHECK_ALL' ); ?>" onclick="Joomla.checkAll(this);" />
			</th>
			<th class="center" colspan="2"><?php echo JHtml::_( 'grid.sort', 'COM_CCK_TITLE', 'a.title', $listDir, $listOrder ); ?></th>
			<th class="center hidden-phone nowrap" width="20%" colspan="2"><?php echo JHtml::_( 'grid.sort', 'COM_CCK_'._C0_TEXT, 'folder_title', $listDir, $listOrder ); ?></th>
			<th class="center hidden-phone nowrap" width="8%"><?php echo JText::_( 'COM_CCK_ADMIN_FORM' ); ?></th>
			<th class="center hidden-phone nowrap" width="8%"><?php echo JText::_( 'COM_CCK_SITE_FORM' ); ?></th>
			<th class="center hidden-phone nowrap" width="7%"><?php echo JText::_( 'COM_CCK_INTRO' ); ?></th>
			<th class="center hidden-phone nowrap" width="7%"><?php echo JText::_( 'COM_CCK_CONTENT' ); ?></th>
			<th width="10%" class="center nowrap" colspan="2"><?php echo JHtml::_( 'grid.sort', 'COM_CCK_STATUS', 'a.published', $listDir, $listOrder ); ?></th>
			<th width="32" class="center hidden-phone"><?php echo JHtml::_( 'grid.sort', 'COM_CCK_ID', 'a.id', $listDir, $listOrder ); ?></th>
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
		$canCreateItem	=	$user->authorise( 'core.create', CCK_COM.'.form.'.$item->id );
		
		$link 			=	JRoute::_( 'index.php?option='.$this->option.'&task='.$this->vName.'.edit&id='. $item->id );
		$link2			=	JRoute::_( 'index.php?option='.$this->option.'&view=form&type='.$item->name.'&return_o=cck&return_v=types' );
		$linkFilter		=	JRoute::_( 'index.php?option='.$this->option.'&view='.$this->getName().'&folder_id='.$item->folder );
		$linkFolder		=	JRoute::_( 'index.php?option='.$this->option.'&task=folder.edit&id='. $item->folder );
		$linkVersion	=	JRoute::_( 'index.php?option='.$this->option.'&view=versions&filter_e_type=type&e_id='.$item->id );
		
		Helper_Admin::addFolderClass( $css, $item->folder, $item->folder_color, $item->folder_colorchar );
		?>
		<tr class="row<?php echo $i % 2; ?>" height="64px;">
			<td class="center hidden-phone"><?php Helper_Display::quickSlideTo( 'pagination-bottom', $i + 1 ); ?></td>
			<td class="center hidden-phone"><?php echo JHtml::_( 'grid.id', $i, $item->id ); ?></td>
			<td width="30px" class="center hidden-phone">
            	<?php if ( $item->published && $item->adminFields && $item->location != 'site' && $item->storage_location != 'none' && $canCreateItem ) { ?>
					<a target="_self" href="<?php echo $link2; ?>"><img class="img-action" src="components/<?php echo CCK_COM; ?>/assets/images/24/icon-24-forms.png" border="0" alt="" title="<?php echo $title2 ?>" /></a>
                <?php } ?>
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
				echo ( $canEditFolder ) ? '<a href="'.$linkFolder.'">' . $this->escape( $item->folder_title ) . '</a>' . $folder_parent : '<span>' . $this->escape( $item->folder_title ) . '</span>' . $folder_parent;
                ?>
			</td>
			<td class="center hidden-phone"><?php
				$client	=	'<strong>'.$item->adminTemplate.'</strong><br />'.$item->adminFields.' '.$label;
                echo ( !$item->adminFields ) ? '-' : ( ( $canEdit && !$checkedOut ) ? '<a class="edit-view hasTooltip" href="'.$link.'&client=admin" title="'.htmlspecialchars( $client ).'">'.$item->adminFields.'</a>' : $item->adminFields ); ?></td>
			<td class="center hidden-phone"><?php
				$client	=	'<strong>'.$item->siteTemplate.'</strong><br />'.$item->siteFields.' '.$label;
                echo ( !$item->siteFields ) ? '-' : ( ( $canEdit && !$checkedOut ) ? '<a class="edit-view hasTooltip" href="'.$link.'&client=site" title="'.htmlspecialchars( $client ).'">'.$item->siteFields.'</a>' : $item->siteFields ); ?></td>
			<td class="center hidden-phone"><?php
				$client	=	'<strong>'.$item->introTemplate.'</strong><br />'.$item->introFields.' '.$label;
                echo ( !$item->introFields ) ? '-' : ( ( $canEdit && !$checkedOut ) ? '<a class="edit-view hasTooltip" href="'.$link.'&client=intro" title="'.htmlspecialchars( $client ).'">'.$item->introFields.'</a>' : $item->introFields ); ?></td>
			<td class="center hidden-phone"><?php
				$client	=	'<strong>'.$item->contentTemplate.'</strong><br />'.$item->contentFields.' '.$label;
                echo ( !$item->contentFields ) ? '-' : ( ( $canEdit && !$checkedOut ) ? '<a class="edit-view hasTooltip" href="'.$link.'&client=content" title="'.htmlspecialchars( $client ).'">'.$item->contentFields.'</a>' : $item->contentFields ); ?></td>
			<td class="center">
				<?php echo JHtml::_( 'jgrid.published', $item->published, $i, $this->vName.'s.', $canChange, 'cb' ); ?>
			</td>
			<td width="3%" class="center hidden-phone">
				<?php echo ( $item->versions ) ? '<a href="'.$linkVersion.'"><img class="img-action" src="components/'.CCK_COM.'/assets/images/16/icon-16-version.png" border="0" alt="" /></a>' : ''; ?>
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
			<td class="center" colspan="11" id="pagination-bottom"><?php echo $this->pagination->getListFooter(); ?></td>
			<td class="center hidden-phone"><?php Helper_Display::quickSlideTo( $top, 'up' ); ?></td>
		</tr>
	</tfoot>
	</table>
    <?php } ?>
</div>
<?php include_once dirname(__FILE__).'/default_batch.php'; ?>
<div class="clr"></div>
<div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="return_v" id="return_v" value="types" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDir; ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>
</div>

<?php
Helper_Include::addStyleDeclaration( implode( '', $css ) );
Helper_Display::quickCopyright();
?>
</div>
</form>

<script type="text/javascript">
(function ($){
	JCck.Dev = {
		addNew: function(id) {
			var tpl_a = "<?php echo $template_name; ?>";
			var tpl_s = "<?php echo $template_name; ?>";
			var tpl_c = "<?php echo $template_name; ?>";
			var tpl_i = "<?php echo $template_name; ?>";
			var url = "index.php?option=com_cck&task=type.add&skeleton_id="+id+"&tpl_a="+tpl_a+"&tpl_s="+tpl_s+"&tpl_c="+tpl_c+"&tpl_i="+tpl_i;
			window.location.href = url;
			return false;
		}
	}
	Joomla.orderTable = function()
	{
		table = document.getElementById("sortTable");
		direction = document.getElementById("directionTable");
		order = table.options[table.selectedIndex].value;
		if (order != '<?php echo $listOrder; ?>') {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
	Joomla.submitbutton = function(task, cid) {
		if (task == "<?php echo $this->vName.'s'; ?>.delete") {
			if (confirm(Joomla.JText._('COM_CCK_CONFIRM_DELETE'))) {
				Joomla.submitform(task);
			} else {
				return false;
			}
		}
		Joomla.submitform(task);
	}
})(jQuery);
</script>