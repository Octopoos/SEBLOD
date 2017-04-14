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
			<th class="center">
				<?php
                echo JHtml::_( 'grid.sort', 'COM_CCK_TITLE', 'title', $listDir, $listOrder );
				echo JHtml::_( 'grid.sort', '<img style=\'float:left;padding-left:10px;\' src=\'components/'.CCK_COM.'/assets/images/18/icon-18-folders.png\' border=\'0\' alt=\'\' />', 'a.lft', $listDir, $listOrder );				
				?>
			</th>
			<th width="10%" class="center hidden-phone nowrap"><?php echo JHtml::_( 'grid.sort', 'COM_CCK_COLOR', 'color', $listDir, $listOrder ); ?></th>
			<th width="30%" class="center hidden-phone nowrap"><?php echo JText::_( 'COM_CCK_ELEMENTS' ); ?></th>
			<th width="10%" class="center hidden-phone nowrap"><?php echo JHtml::_( 'grid.sort', 'COM_CCK_FEATURED', 'featured', $listDir, $listOrder ); ?></th>
			<th width="10%" class="center nowrap"><?php echo JHtml::_( 'grid.sort', 'COM_CCK_STATUS', 'published', $listDir, $listOrder ); ?></th>
			<th width="32" class="center hidden-phone nowrap"><?php echo JHtml::_( 'grid.sort', 'COM_CCK_ID', 'id', $listDir, $listOrder ); ?></th>
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

		$last			=	( $item->id == 1 ) ? ' last' : '';
		$link 			=	JRoute::_( 'index.php?option='.$this->option.'&task=folder.edit&id='. $item->id );
		$linkTemplate	=	JRoute::_( 'index.php?option='.$this->option.'&view='._C1_NAME.'&folder_id='.$item->id );
		$linkType		=	JRoute::_( 'index.php?option='.$this->option.'&view='._C2_NAME.'&folder_id='.$item->id );
		$linkField		=	JRoute::_( 'index.php?option='.$this->option.'&view='._C3_NAME.'&folder_id='.$item->id );
		$linkSearch		=	JRoute::_( 'index.php?option='.$this->option.'&view='._C4_NAME.'&folder_id='.$item->id );
		$linkFilter		=	JRoute::_( 'index.php?option='.$this->option.'&view='.$this->getName().'&folder_id='.$item->id );
		
		Helper_Admin::addFolderClass( $css, $item->id, $item->color, $item->colorchar, '60' );
		?>
        <tr class="row<?php echo $i % 2; ?><?php echo $last; ?>" height="64px;">
			<td class="center hidden-phone"><?php Helper_Display::quickSlideTo( 'pagination-bottom', $i + 1 ); ?></td>
			<td class="center hidden-phone"><?php echo JHtml::_( 'grid.id', $i, $item->id ); ?></td>
			<td>
				<div class="title-left" id="title-<?php echo $item->id; ?>">
					<?php
					if ( $item->checked_out ) {
						echo JHtml::_( 'jgrid.checkedout', $i, $item->editor, $item->checked_out_time, $this->vName.'s.', $canCheckin )."\n";
					}
					if ( ( $canEdit && ! $checkedOut ) ) {
                        if ( $item->id == 1 || $item->id == 2 ) { ?>
                            <a href="<?php echo $link; ?>"><?php echo $item->title; ?></a>
                            <?php echo '<div class="small">'.strtolower( JText::_( $item->name ) ).'</div>'; ?>
                        <?php } else { ?>
                            <?php echo str_repeat( '<span class="gtr">&mdash;</span>', $item->depth ) ?><a href="<?php echo $link; ?>"><?php echo $item->title; ?></a>
                            <?php echo '<div class="small">'.str_repeat( '<span class="gtr">&mdash;</span>', $item->depth ).$item->name.'</div>'; ?>
                        <?php }
                    } else {
                        if ( $item->id == 1 || $item->id == 2 ) {
                            echo $item->title;
                            echo '<div class="small">'.strtolower( JText::_( $item->name ) ).'</div>';
                        } else {
                            echo str_repeat( '.'._NBSP2, $item->depth ) . $item->title;
                            echo '<div class="small">'.str_repeat( '.'._NBSP2, $item->depth ).$item->name.'</div>';
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
											   . ' <span class="elements-count">' . $item->types_nb.' '.JText::_( 'COM_CCK_FORMS' ). '</b></span>'
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
			<td class="center" colspan="6" id="pagination-bottom"><?php echo $this->pagination->getListFooter(); ?></td>
			<td class="center hidden-phone"><?php Helper_Display::quickSlideTo( $top, 'up' ); ?></td>
		</tr>
	</tfoot>
</table>