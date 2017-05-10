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

$action			=	'<span class="icon-loop"></span>';
$action_attr	=	' title="'.JText::_( 'COM_CCK_REVERT_TO_THIS_VERSION' ).'"';
$doc			=	JFactory::getDocument();
$user			=	JFactory::getUser();
$userId			=	$user->id;
$listOrder		=	$this->state->get( 'list.ordering' );
$listDir		=	$this->state->get( 'list.direction' );
$top			=	'content';

$config			=	JCckDev::init( array( '42', 'button_submit', 'select_simple', 'text' ), true, array( 'vName'=>$this->vName ) );
$cck			=	JCckDev::preload( array( 'core_filter_input', 'core_filter_go', 'core_filter_search', 'core_filter_clear',
										 'core_version_location_filter', 'core_state_filter', 'core_version_e_type_filter' ) );
JText::script( 'COM_CCK_CONFIRM_DELETE' );
JText::script( 'COM_CCK_CONFIRM_RESTORE_VERSION' );
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
			<th width="32" class="center hidden-phone nowrap" rowspan="2"><?php Helper_Display::quickSlideTo( 'pagination-bottom', 'down' ); ?></th>
			<th width="30" class="center hidden-phone" rowspan="2">
            	<input type="checkbox" name="toggle" value="" title="<?php echo JText::_( 'JGLOBAL_CHECK_ALL' ); ?>" onclick="Joomla.checkAll(this);" />
			</th>
			<th class="center" rowspan="2" colspan="2"><?php echo JHtml::_( 'grid.sort', 'COM_CCK_TITLE', 'a.title', $listDir, $listOrder ); ?></th>
			<th width="20%" class="center hidden-phone nowrap" colspan="2" rowspan="2"><?php echo JHtml::_( 'grid.sort', 'COM_CCK_VERSION', 'a.date_time', $listDir, $listOrder ); ?></th>
            <th width="40%" class="center hidden-phone nowrap" colspan="5"><?php echo JText::_( 'COM_CCK_DETAILS' ); ?></th>
			<th width="32" class="center hidden-phone nowrap" rowspan="2"><?php echo JHtml::_( 'grid.sort', 'COM_CCK_ID', 'a.id', $listDir, $listOrder ); ?></th>
		</tr>
		<tr class="half">
			<th width="15%" class="center hidden-phone nowrap"><?php echo JText::_( 'COM_CCK_TITLE_NAME' ); ?></th>
			<th width="25%" class="center hidden-phone nowrap" colspan="4"><?php echo JText::_( 'COM_CCK_FIELDS' ); ?></th>
		</tr>
	</thead>
    <tbody>
	<?php
	$named	=	'';
	foreach ( $this->items as $i=>$item ) {
		$checkedOut		= 	! ( $item->checked_out == $userId || $item->checked_out == 0 );
		$canCheckin		=	$user->authorise( 'core.manage', 'com_checkin' ) || $item->checked_out == $userId || $item->checked_out == 0;
		$canChange		=	$user->authorise( 'core.edit.state', CCK_COM ) && $canCheckin;
		$canEdit		=	$user->authorise( 'core.edit', CCK_COM );
		$canEditOwn		=	'';	
		
		$more			=	JCckDev::fromJSON( $item->e_more );
		$link 			=	JRoute::_( 'index.php?option='.$this->option.'&task='.$this->vName.'.edit&id='.$item->id );
		if ( $canEdit ) {
			$goBackToVersion	=	'<a href="javascript:void(0);" onclick="Joomla.submitbutton(\'versions.revert\',\'cb'.$i.'\');"'.' class="btn btn-micro'.( $item->featured ? ' btn-primary' : '' ).' hasTooltip"'.$action_attr.'>'
								.	$action
								.	'</a>';
		} else {
			$goBackToVersion	=	'-';
		}
		?>
		<tr class="row<?php echo $i % 2; ?>" height="64px;">
			<td class="center hidden-phone"><?php Helper_Display::quickSlideTo( 'pagination-bottom', $i + 1 ); ?></td>
			<td class="center hidden-phone"><?php echo JHtml::_( 'grid.id', $i, $item->id ); ?></td>
			<td width="30px" class="center hidden-phone"><?php echo $goBackToVersion; ?></td>
			<td>
				<div class="title-left" id="title-<?php echo $item->id; ?>">
					<?php
					if ( $item->checked_out ) {
						echo JHtml::_( 'jgrid.checkedout', $i, $item->editor, $item->checked_out_time, $this->vName.'s.', $canCheckin )."\n";
					}
					if ( $canEdit && ! $checkedOut ) {
						echo '<a href="'.$link.'">'.$this->escape( $item->title ).'</a>';
					} else {
						echo '<span>'.$this->escape( $item->title ).'</span>';
					}
					echo ( $item->note ) ? '<div class="small">'.$this->escape( $item->note ).'</div>' : '';
					?>
				</div>
			</td>
			<td width="4%" class="center">
				<strong><?php echo JText::_( 'COM_CCK_REVISION_SHORT' ) .'<br />'. $item->e_version; ?></strong>
			</td>
			<td class="center hidden-phone"><?php echo JHtml::_( 'date', $item->date_time, JText::_( 'DATE_FORMAT_LC4' ).' H:i' ).( ( $item->user_id > 0 ) ? '<br />'.$item->created_by : '' ); ?></td>
			<td class="center hidden-phone"><?php echo '<span>'.$item->e_title.'</span>' .'<br />'. $item->e_name; ?></td>
            <td class="center hidden-phone"><?php echo @$more['fields'][1] ? @$more['fields'][1] : '-'; ?></td>
            <td class="center hidden-phone"><?php echo @$more['fields'][2] ? @$more['fields'][2] : '-'; ?></td>
            <td class="center hidden-phone"><?php echo @$more['fields'][3] ? @$more['fields'][3] : '-'; ?></td>
            <td class="center hidden-phone"><?php echo @$more['fields'][4] ? @$more['fields'][4] : '-'; ?></td>
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
</div>
<?php /* include_once __DIR__.'/default_batch.php'; */ ?>
<div class="clr"></div>
<div>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="element_type" value="<?php echo $this->e_type; ?>" />
    <input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="return_v" id="return_v" value="versions" />
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDir; ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>
</div>

<?php
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
				if (task == "'.$this->vName.'s.revert") {
					jQuery(\'input:checkbox[name="cid[]"]:checked\').prop("checked","");
					jQuery("#"+cid).prop("checked",true);
					if (confirm(Joomla.JText._("COM_CCK_CONFIRM_RESTORE_VERSION"))) {
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