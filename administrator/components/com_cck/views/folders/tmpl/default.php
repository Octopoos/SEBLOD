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

$uix		=	JCck::getUIX();
$css		=	array();
$doc		=	JFactory::getDocument();
$images		=	array( '0'=>'16/icon-16-download.png', '1'=>'24/icon-24-download.png' );
$user		=	JFactory::getUser();
$userId		=	$user->id;
$listOrder	=	$this->state->get( 'list.ordering' );
$listDir	=	$this->state->get( 'list.direction' );
$link2		=	'index.php?option='.$this->option.'&task=folder.export&id=';
$title2		=	JText::_( 'COM_CCK_DOWNLOAD_THIS_APP' );
$top		=	( !JCck::on() ) ? 'border-top' : 'content';

$config		=	JCckDev::init( array( '42', 'button_submit', 'checkbox', 'radio', 'select_dynamic', 'select_numeric', 'select_simple', 'text' ), true, array( 'vName' => '' ) );
$cck		=	JCckDev::preload( array( 'core_filter_input', 'core_filter_go', 'core_filter_search', 'core_filter_clear', 'core_location_filter',
										 'core_folder_filter', 'core_state_filter', 'core_depth_filter', 'core_app_stuff' ) );
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
		<tr class="half">
			<th width="32" class="center hidden-phone nowrap" rowspan="2"><?php Helper_Display::quickSlideTo( 'pagination-bottom', 'down' ); ?></th>
			<th width="30" class="center hidden-phone" rowspan="2">
				<input type="checkbox" name="toggle" value="" title="<?php echo JText::_( 'JGLOBAL_CHECK_ALL' ); ?>" onclick="Joomla.checkAll(this);" />
			</th>
			<th class="center" rowspan="2" colspan="2">
				<?php
                echo JHtml::_( 'grid.sort', 'COM_CCK_TITLE', 'a.title', $listDir, $listOrder );
				echo JHtml::_( 'grid.sort', '<img style=\'float:left;padding-left:10px;\' src=\'components/'.CCK_COM.'/assets/images/18/icon-18-folders.png\' border=\'0\' alt=\'\' />', 'a.lft', $listDir, $listOrder );				
				?>
			</th>
			<th width="10%" class="center hidden-phone nowrap" rowspan="2"><?php echo JHtml::_( 'grid.sort', 'COM_CCK_COLOR', 'a.color', $listDir, $listOrder ); ?></th>
			<th width="30%" class="center hidden-phone nowrap" colspan="4"><?php echo JText::_( 'COM_CCK_ELEMENTS' ); ?></th>
			<th width="10%" class="center hidden-phone nowrap" rowspan="2"><?php echo JHtml::_( 'grid.sort', 'COM_CCK_FEATURED', 'a.featured', $listDir, $listOrder ); ?></th>
			<th width="10%" class="center nowrap" rowspan="2"><?php echo JHtml::_( 'grid.sort', 'COM_CCK_STATUS', 'a.published', $listDir, $listOrder ); ?></th>
			<th width="32" class="center hidden-phone nowrap" rowspan="2"><?php echo JHtml::_( 'grid.sort', 'COM_CCK_ID', 'a.id', $listDir, $listOrder ); ?></th>
		</tr>
		<tr class="half">
			<th width="8%" class="center hidden-phone nowrap"><?php echo JText::_( 'COM_CCK_FORMS' ); ?></th>
            <th width="7%" class="center hidden-phone nowrap"><?php echo JText::_( 'COM_CCK_FIELDS' ); ?></th>
			<th width="8%" class="center hidden-phone nowrap"><?php echo JText::_( 'COM_CCK_LISTS' ); ?></th>
			<th width="7%" class="center hidden-phone nowrap"><?php echo JText::_( 'COM_CCK_TEMPLATES' ); ?></th>
		</tr>	
	</thead>
    <tbody>
	<?php
	foreach ( $this->items as $i=>$item ) {
		$checkedOut		= 	! ( $item->checked_out == $userId || $item->checked_out == 0 );
		$canCheckin		=	$user->authorise( 'core.manage', 'com_checkin' ) || $item->checked_out == $userId || $item->checked_out == 0;
		$canChange		=	$user->authorise( 'core.edit.state', CCK_COM.'.folder.'.$item->id ) && $canCheckin;
		$canEdit		=	$user->authorise( 'core.edit', CCK_COM.'.folder.'.$item->id );
		$canEditOwn		=	'';

		$checked_out	=	( $item->checked_out ) ? JHtml::_( 'jgrid.checkedout', $i, $item->editor, $item->checked_out_time, $this->vName.'s.', $canCheckin )."\n" : '';
		$last			=	( $item->id == 1 ) ? ' last' : '';
		$link 			=	JRoute::_( 'index.php?option='.$this->option.'&task=folder.edit&id='. $item->id );
		$linkTemplate	=	JRoute::_( 'index.php?option='.$this->option.'&view='._C1_NAME.'&folder_id='.$item->id );
		$linkType		=	JRoute::_( 'index.php?option='.$this->option.'&view='._C2_NAME.'&folder_id='.$item->id );
		$linkField		=	JRoute::_( 'index.php?option='.$this->option.'&view='._C3_NAME.'&folder_id='.$item->id );
		$linkSearch		=	JRoute::_( 'index.php?option='.$this->option.'&view='._C4_NAME.'&folder_id='.$item->id );
		$linkFilter		=	JRoute::_( 'index.php?option='.$this->option.'&view='.$this->getName().'&folder_id='.$item->id );
		
		$img			=	$images[$item->home];
		Helper_Admin::addFolderClass( $css, $item->id, $item->color, $item->colorchar, '60' );
		?>
        <tr class="row<?php echo $i % 2; ?><?php echo $last; ?>" height="64px;">
			<td class="center hidden-phone"><?php Helper_Display::quickSlideTo( 'pagination-bottom', $i + 1 ); ?></td>
			<td class="center hidden-phone"><?php echo JHtml::_( 'grid.id', $i, $item->id ); ?></td>
            <td width="30px" class="center hidden-phone"><a class="app-download" href="javascript: void(0);" title="<?php echo $item->id; ?>">
            	<img class="img-action" src="components/<?php echo CCK_COM; ?>/assets/images/<?php echo $img; ?>" border="0" alt="" title="<?php echo $title2 ?>" /></a>
			</td>
			<td>
				<div class="title-left" id="title-<?php echo $item->id; ?>">
					<?php
					if ( ( $canEdit && ! $checkedOut ) ) {
                        if ( $item->id == 1 || $item->id == 2 ) { ?>
                            <?php echo $checked_out; ?><a href="<?php echo $link; ?>"><?php echo JText::_( 'COM_CCK_'.str_replace( ' ', '_', $item->title ) ); ?></a>
                            <?php echo '<div class="small">'.strtolower( $item->name ).'</div>'; ?>
                        <?php } else { ?>
                            <?php echo str_repeat( '<span class="gtr2">\n</span>', $item->depth ).$checked_out; ?><a href="<?php echo $link; ?>"><?php echo $item->title; ?></a>
                            <?php echo '<div class="small">'.str_repeat( '<span class="gtr2">\n</span>', $item->depth ).$item->name.'</div>'; ?>
                        <?php }
                    } else {
                        if ( $item->id == 1 || $item->id == 2 ) {
                            echo $checked_out.$item->title
                             .	 '<div class="small">'.strtolower( JText::_( $item->name ) ).'</div>';
                        } else {
                            echo str_repeat( '<span class="gtr2">\n</span>', $item->depth ).$checked_out.$item->title
                             .	 '<div class="small">'.str_repeat( '<span class="gtr2">\n</span>', $item->depth ).$item->name.'</div>';
                        }
                    }
                    ?>
				</div>
			</td>
			<td align="center">
                <a href="<?php echo $linkFilter; ?>" style="text-decoration: none;" class="hidden-phone">
                    <div class="<?php echo ( $item->color || ( $item->colorchar && $item->introchar ) ) ? 'folderColor'.$item->id : ''; ?>">
                    <strong><?php echo $item->introchar; ?></strong>
                    </div>
                </a>
			</td>
			<td width="6%" class="center hidden-phone">
				<?php echo ( $item->types_nb ) ? '<a href="'.$linkType.'" style="text-decoration: none;">'
											   . '<img src="components/'.CCK_COM.'/assets/images/24/icon-24-'._C2_NAME.'.png" alt="" />'
											   . ' <span class="elements-count">' . $item->types_nb . '</b></span>'
											   . '</a>' : '-'; ?>
			</td>
			<td width="6%" class="center hidden-phone">
				<?php echo ( $item->fields_nb ) ? '<a href="'.$linkField.'" style="text-decoration: none;">'
												. '<img src="components/'.CCK_COM.'/assets/images/24/icon-24-'._C3_NAME.'.png" alt="" />'
												. ' <span class="elements-count">' . $item->fields_nb . '</b></span>'
												. '</a>' : '-'; ?>
			</td>
			<td width="6%" class="center hidden-phone">
				<?php echo ( $item->searchs_nb ) ? '<a href="'.$linkSearch.'" style="text-decoration: none;">'
												 . '<img src="components/'.CCK_COM.'/assets/images/24/icon-24-'._C4_NAME.'.png" alt="" />'
												 . ' <span class="elements-count">' . $item->searchs_nb . '</span>'
												 . '</a>' : '-'; ?>
			</td>
			<td width="6%" class="center hidden-phone">
				<?php echo ( $item->templates_nb ) ? '<a href="'.$linkTemplate.'" style="text-decoration: none;">'
												   . '<img src="components/'.CCK_COM.'/assets/images/24/icon-24-'._C1_NAME.'.png" alt="" />'
												   . ' <span class="elements-count">' . $item->templates_nb . '</span>'
												   . '</a>' : '-'; ?>
			</td>
			<td class="center hidden-phone"><?php Helper_Display::quickJGrid( 'featured', $item->featured, $i, false ); ?></td>
			<td class="center"><?php echo JHtml::_( 'jgrid.published', $item->published, $i, $this->vName.'s.', $canChange, 'cb' ); ?></td>
			<td class="center hidden-phone"><?php Helper_Display::quickSlideTo( $top, $item->id ); ?></td>
		</tr>
		<?php
	}
	?>
    </tbody>
	<tfoot>
		<tr height="40px;">
			<td class="center hidden-phone"><?php Helper_Display::quickSlideTo( $top, 'up' ); ?></td>
			<td class="center" colspan="10" id="pagination-bottom"><?php echo $this->pagination->getListFooter(); ?></td>
			<td class="center hidden-phone"><?php Helper_Display::quickSlideTo( $top, 'up' ); ?></td>
		</tr>
	</tfoot>
	</table>
    <?php } ?>
</div>
<?php
if ( $uix != 'compact' ) {
	include_once dirname(__FILE__).'/default_app.php';
}
?>
<div class="clr"></div>
<div>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDir; ?>" />
    <?php echo JHtml::_('form.token'); ?>
</div>

<?php
Helper_Include::addStyleDeclaration( implode( '', $css ) );
Helper_Display::quickCopyright();
?>
</div>
</form>

<script type="text/javascript">
(function ($){
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