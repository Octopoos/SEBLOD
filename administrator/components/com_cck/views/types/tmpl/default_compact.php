<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: default_compact.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;
?>

<table class="<?php echo $this->css['table']; ?>">
<thead>
    <tr>
        <th width="32" class="center hidden-phone nowrap"><?php Helper_Display::quickSlideTo( 'pagination-bottom', 'down' ); ?></th>
        <th width="30" class="center hidden-phone">
        	<input type="checkbox" name="toggle" value="" title="<?php echo JText::_( 'JGLOBAL_CHECK_ALL' ); ?>" onclick="Joomla.checkAll(this);" />
		</th>
        <th class="center" colspan="2"><?php echo JHtml::_( 'grid.sort', 'COM_CCK_TITLE', 'title', $listDir, $listOrder ); ?></th>
        <th width="20%" class="center hidden-phone nowrap" colspan="2"><?php echo JHtml::_( 'grid.sort', 'COM_CCK_'._C0_TEXT, 'folder_title', $listDir, $listOrder ); ?></th>
		<th width="15%" class="center hidden-phone nowrap"><?php echo JText::_( 'COM_CCK_ADMIN_FORM' ); ?></th>
		<th width="15%" class="center hidden-phone nowrap"><?php echo JText::_( 'COM_CCK_SITE_FORM' ); ?></th>
        <th width="10%" class="center nowrap"><?php echo JHtml::_( 'grid.sort', 'COM_CCK_STATUS', 'published', $listDir, $listOrder ); ?></th>
        <th width="32" class="center hidden-phone nowrap"><?php echo JHtml::_( 'grid.sort', 'COM_CCK_ID', 'id', $listDir, $listOrder ); ?></th>
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
    $link2			=	JRoute::_( 'index.php?option='.$this->option.'&view=form&type='.$item->name.'&return=cck&return_v=types' );
    $linkFilter		=	JRoute::_( 'index.php?option='.$this->option.'&view='.$this->getName().'&folder_id='.$item->folder );
    $linkFolder		=	JRoute::_( 'index.php?option='.$this->option.'&task=folder.edit&id='. $item->folder );
    
    Helper_Admin::addFolderClass( $css, $item->folder, $item->folder_color, $item->folder_colorchar );
    ?>
    <tr class="row<?php echo $i % 2; ?>" height="64px;">
        <td class="center hidden-phone"><?php Helper_Display::quickSlideTo( 'pagination-bottom', $i + 1 ); ?></td>
        <td class="center hidden-phone"><?php echo JHtml::_( 'grid.id', $i, $item->id ); ?></td>
        <td width="30px" class="center hidden-phone">
            <?php if ( $item->published && $item->adminFields ) { ?>
                <a target="_self" href="<?php echo $link2; ?>"><img src="components/<?php echo CCK_COM; ?>/assets/images/18/icon-18-form.png" border="0" alt="" /></a>
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
		<td class="center hidden-phone"><?php echo ( ! $item->adminFields ) ? '-' : '<a class="edit-view" href="'.$link.'&client=admin">'.$item->adminFields.' '.JText::_( 'COM_CCK_FIELDS' ).'</a>'; ?></td>
        <td class="center hidden-phone"><?php echo ( ! $item->siteFields ) ? '-' : '<a class="edit-view" href="'.$link.'&client=site">'.$item->siteFields.' '.JText::_( 'COM_CCK_FIELDS' ).'</a>'; ?></td>
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
        <td class="center" colspan="8" id="pagination-bottom"><?php echo $this->pagination->getListFooter(); ?></td>
        <td class="center hidden-phone"><?php Helper_Display::quickSlideTo( $top, 'up' ); ?></td>
    </tr>
</tfoot>
</table>