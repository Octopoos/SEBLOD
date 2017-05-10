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
$action_attr	=	' class="app-download btn btn-micro hasTooltip" title="'.JText::_( 'COM_CCK_DOWNLOAD_THIS_APP' ).'"';
$uix			=	JCck::getUIX();
$css			=	array();
$doc			=	JFactory::getDocument();
$hasToolbox		=	JCckToolbox::getConfig()->def( 'KO' ) ? false : true;
$images			=	array( '0'=>'16/icon-16-download.png', '1'=>'24/icon-24-download.png' );
$user			=	JFactory::getUser();
$userId			=	$user->id;
$listOrder		=	$this->state->get( 'list.ordering' );
$listDir		=	$this->state->get( 'list.direction' );
$link2			=	'index.php?option='.$this->option.'&task=folder.export&id=';
$top			=	'content';

$config			=	JCckDev::init( array( '42', 'button_submit', 'checkbox', 'radio', 'select_dynamic', 'select_numeric', 'select_simple', 'text' ), true, array( 'vName' => '' ) );
$cck			=	JCckDev::preload( array( 'core_filter_input', 'core_filter_go', 'core_filter_search', 'core_filter_clear', 'core_location_filter',
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

<?php include_once __DIR__.'/default_filter.php'; ?>
<div class="<?php echo $this->css['items']; ?>">
	<?php
	if ( $uix == 'compact' ) {
		include_once __DIR__.'/default_compact.php';
	} else {
    ?>
	<table class="<?php echo $this->css['table']; ?>">
	<thead>
		<tr class="half">
			<th width="32" class="center hidden-phone nowrap"><?php Helper_Display::quickSlideTo( 'pagination-bottom', 'down' ); ?></th>
			<th width="30" class="center hidden-phone">
				<input type="checkbox" name="toggle" value="" title="<?php echo JText::_( 'JGLOBAL_CHECK_ALL' ); ?>" onclick="Joomla.checkAll(this);" />
			</th>
			<th class="center caret-fix" colspan="2">
				<?php
				echo JHtml::_( 'grid.sort', '<span class="icon-menu-2" style="float:left; position:relative; top:4px; left:8px;"></span>', 'a.lft', $listDir, $listOrder );
                echo JHtml::_( 'grid.sort', 'COM_CCK_TITLE', 'a.title', $listDir, $listOrder );				
				?>
			</th>
			<th width="10%" class="center hidden-phone nowrap"><?php echo JHtml::_( 'grid.sort', 'COM_CCK_COLOR', 'a.color', $listDir, $listOrder ); ?></th>
			<th width="6%" class="center hidden-phone nowrap"><?php echo '<span class="icon-cck-form hasTooltip large" title="'.JText::_( 'COM_CCK_FORMS' ).'"></span>'; ?></th>
            <th width="6%" class="center hidden-phone nowrap"><?php echo '<span class="icon-cck-plugin hasTooltip large" title="'.JText::_( 'COM_CCK_FIELDS' ).'"></span>'; ?></th>
			<th width="6%" class="center hidden-phone nowrap"><?php echo '<span class="icon-cck-search hasTooltip large" title="'.JText::_( 'COM_CCK_LISTS' ).'"></span>'; ?></th>
			<th width="6%" class="center hidden-phone nowrap"><?php echo '<span class="icon-cck-template hasTooltip large" title="'.JText::_( 'COM_CCK_TEMPLATES' ).'"></span>'; ?></th>
			<th width="6%" class="center hidden-phone nowrap"><?php echo '<span class="icon-cck-addon hasTooltip large" title="'.JText::_( 'COM_CCK_PROCESSINGS' ).'"></span>'; ?></th>
			<th width="10%" class="center nowrap"><?php echo JHtml::_( 'grid.sort', 'COM_CCK_STATUS', 'a.published', $listDir, $listOrder ); ?></th>
			<th width="32" class="center hidden-phone nowrap"><?php echo JHtml::_( 'grid.sort', 'COM_CCK_ID', 'a.id', $listDir, $listOrder ); ?></th>
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

		if ( $hasToolbox ) {
			$classProcessing	=	'';
			$linkProcessing		=	JRoute::_( 'index.php?option=com_cck_toolbox&view=processings&folder_id='.$item->id );
		} else {
			$classProcessing	=	' disabled';
			$linkProcessing		=	'javascript:void(0);';
		}
		$linkFilter		=	JRoute::_( 'index.php?option='.$this->option.'&view='.$this->getName().'&folder_id='.$item->id );
		
		$img			=	$images[$item->home];
		$action_attr2	=	( $item->home ) ? str_replace( 'btn-micro', 'btn-primary btn-micro', $action_attr ) : $action_attr;
		Helper_Admin::addFolderClass( $css, $item->id, $item->color, $item->colorchar, '60' );
		?>
        <tr class="row<?php echo $i % 2; ?><?php echo $last; ?>" height="64px;">
			<td class="center hidden-phone"><?php Helper_Display::quickSlideTo( 'pagination-bottom', $i + 1 ); ?></td>
			<td class="center hidden-phone"><?php echo JHtml::_( 'grid.id', $i, $item->id ); ?></td>
            <td width="30px" class="center hidden-phone">
            	<a href="javascript: void(0);" data-id="<?php echo $item->id; ?>"<?php echo $action_attr2; ?>>
            		<?php echo $action; ?>
            	</a>
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
                            <?php echo '<div>'.str_repeat( '<span class="gtr2">\n</span>', $item->depth ).'<span class="small">'.$item->name.'</span></div>'; ?>
                        <?php }
                    } else {
                        if ( $item->id == 1 || $item->id == 2 ) {
                            echo $checked_out.$item->title
                             .	 '<div class="small">'.strtolower( JText::_( $item->name ) ).'</div>';
                        } else {
                            echo str_repeat( '<span class="gtr2">\n</span>', $item->depth ).$checked_out.$item->title
                             .	 '<div>'.str_repeat( '<span class="gtr2">\n</span>', $item->depth ).'<span class="small">'.$item->name.'</span></div>';
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
				<?php echo ( $item->types_nb ) ? '<a class="btn btn-micro btn-count hasTooltip" href="'.$linkType.'" style="text-decoration: none;" title="'.JText::_( 'COM_CCK_FILTER_FORMS' ).'">'
											   . '<span>'.$item->types_nb.'</span>'
											   . '</a>' : '-'; ?>
			</td>
			<td width="6%" class="center hidden-phone">
				<?php echo ( $item->fields_nb ) ? '<a class="btn btn-micro btn-count hasTooltip" href="'.$linkField.'" style="text-decoration: none;" title="'.JText::_( 'COM_CCK_FILTER_FIELDS' ).'">'
												. '<span>'.$item->fields_nb.'</span>'
												. '</a>' : '-'; ?>
			</td>
			<td width="6%" class="center hidden-phone">
				<?php echo ( $item->searchs_nb ) ? '<a class="btn btn-micro btn-count hasTooltip" href="'.$linkSearch.'" style="text-decoration: none;" title="'.JText::_( 'COM_CCK_FILTER_LISTS' ).'">'
												 . '<span>'.$item->searchs_nb.'</span>'
												 . '</a>' : '-'; ?>
			</td>
			<td width="6%" class="center hidden-phone">
				<?php echo ( $item->templates_nb ) ? '<a class="btn btn-micro btn-count hasTooltip" href="'.$linkTemplate.'" style="text-decoration: none;" title="'.JText::_( 'COM_CCK_FILTER_TEMPLATES' ).'">'
												   . '<span>'.$item->templates_nb.'</span>'
												   . '</a>' : '-'; ?>
			</td>
			<td width="6%" class="center hidden-phone">
				<?php echo ( $item->processings_nb ) ? '<a class="btn btn-micro btn-count hasTooltip'.$classProcessing.'" href="'.$linkProcessing.'" style="text-decoration: none;" title="'.JText::_( 'COM_CCK_FILTER_PROCESSINGS' ).'">'
												   . '<span>'.$item->processings_nb.'</span>'
												   . '</a>' : '-'; ?>
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
			<td class="center" colspan="10" id="pagination-bottom"><?php echo $this->pagination->getListFooter(); ?></td>
			<td class="center hidden-phone"><?php Helper_Display::quickSlideTo( $top, 'up' ); ?></td>
		</tr>
	</tfoot>
	</table>
    <?php } ?>
</div>
<?php
if ( $uix != 'compact' ) {
	include_once __DIR__.'/default_app.php';
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
		})(jQuery);
		';
$doc->addScriptDeclaration( $js );
?>
</div>
</form>