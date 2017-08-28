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

JText::script( 'COM_CCK_CONFIRM_DELETE' );
Helper_Include::addDependencies( $this->getName(), $this->getLayout() );
JHtml::_( 'stylesheet', 'media/cck/css/definitions/all.css', array(), false );
if ( ( JCck::getConfig_Param( 'validation', 2 ) > 1 ) && $this->config['validation'] != '' ) {
	JCckDev::addValidation( $this->config['validation'], $this->config['validation_options'] );
	$js	=	'if (jQuery("#'.$this->config['formId'].'").validationEngine("validate",task) === true) { JCck.Core.submitForm(((task=="save"||task=="list.save")?"search":task), document.getElementById("'.$this->config['formId'].'")); }';
} else {
	$js	=	'JCck.Core.submitForm(((task=="save"||task=="list.save")?"search":task), document.getElementById("'.$this->config['formId'].'"));';
}
$app	=	JFactory::getApplication();
$css	=	'div.cck_forms.cck_search div.cck_label label{line-height:28px;} div.seblod.pagination{text-align:center;}'
		.	'form div.pagination div.button2-left,form div.pagination div.button2-right, form div.pagination div.limit{margin-right:10px!important;}'
		.	'div.cck_page_list div.pagination .total{float:right; line-height:28px;}';
JFactory::getDocument()->addStyleDeclaration( $css );
?>

<script type="text/javascript">
<?php echo $this->config['submit']; ?> = function(task) { <?php echo $js; ?> }
Joomla.submitbutton = function(task, cid)
{
	if (task == "<?php echo $this->vName; ?>.delete") {
		if (!confirm(Joomla.JText._('COM_CCK_CONFIRM_DELETE'))) {
			return false;
		}
	}
	jQuery("#<?php echo $this->form_id; ?>").append('<input type="hidden" id="return" name="return" value="<?php echo base64_encode( JUri::getInstance()->toString() ); ?>">');
	JCck.Core.submitForm(task);
}
</script>

<?php
if ( $this->show_list_desc == 1 && $this->description != '' ) {
	echo '<div class="cck_page_desc'.$this->pageclass_sfx.'">' . JHtml::_( 'content.prepare', $this->description ) . '</div><div class="clr"></div>';
}

echo ( $this->config['action'] ) ? $this->config['action'] : '<form action="'.JRoute::_( 'index.php?option='.$this->option.'&view='.$this->getName() ).'" autocomplete="off" method="get" id="'.$this->config['formId'].'" name="'.$this->config['formId'].'">';
echo '<div class="seblod first container-fluid">' . $this->form . '</div>';
?>

<div class="cck_page_list<?php echo $this->pageclass_sfx; ?>" id="system">
	<?php
	if ( $this->show_pagination == -1 || $this->show_pagination == 1 ) {
		echo '<div class="'.$this->class_pagination.'">' . $this->pagination->getPagesLinks() . '</div>';
	}
	if ( @$this->search->content > 0 ) {
		echo '<div class="seblod cck_page_items">'.$this->data.'</div>';
	} else {
		echo $this->loadTemplate( 'items' );
	}
	if ( $this->show_pages_number || $this->show_pagination > -1 ) {
		$item_number	=	'';
		if ( $this->show_items_number ) {
			$label	=	$this->label_items_number;
			if ( $this->config['doTranslation'] ) {
				$label	=	'COM_CCK_' . str_replace( ' ', '_', trim( $label ) );

				if ( ( $this->total == 0 || $this->total == 1 ) && JFactory::getLanguage()->hasKey( $label.'_1' ) ) {
					$label	.=	'_1';
				}
				$label	=	JText::_( $label );
			} elseif ( $this->total == 0 || $this->total == 1 ) {
				if ( JFactory::getLanguage()->hasKey( 'COM_CCK_' . str_replace( ' ', '_', trim( $label ).'_1' ) ) ) {
					$label	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $label ).'_1' ) );	
				}			
			}
			$item_number	=	'<div class="'.$this->class_items_number.'"><span>'.$this->total.'</span>&nbsp;'.$label.'</div>';
		}
		if ( isset( $this->pagination->pagesTotal ) ) {
			$pages_total	=	$this->pagination->pagesTotal;
		} elseif ( isset( $this->pagination->{'pages.total'} ) ) {
			$pages_total	=	$this->pagination->{'pages.total'};
		} else {
			$pages_total	=	0;
		}
		
		if ( $this->show_pagination > -1 ) {
			echo '<div class="seblod '.$this->class_pagination.'">';

			if ( $pages_total > 1 ) {
				$pages	=	str_replace( 'document.adminForm.limitstart', 'document.'.$this->config['formId'].'.limitstart', $this->pagination->getListFooter() );
				$pages	=	str_replace( 'Joomla.submitform()', 'Joomla.submitform(\'\',document.getElementById(\''.$this->config['formId'].'\'))', $pages );
				
				echo str_replace( '<div class="pagination pagination-toolbar clearfix">', '<div class="pagination pagination-toolbar clearfix">'.$item_number, $pages );
			} else {
				echo $item_number;
			}
			echo '</div>';
		}
	}
    ?>
</div>
<div class="clr"></div>
<div>
	<input type="hidden" name="boxchecked" value="0" data-cck-remove-before-search="" />
	<input type="hidden" id="option" name="option" value="com_cck" data-cck-keep-for-search="" />
	<input type="hidden" id="view" name="view" value="list" data-cck-keep-for-search="" />
	<input type="hidden" name="search" value="<?php echo $this->search->name; ?>" data-cck-keep-for-search="" />
	<input type="hidden" id="task" name="task" value="search" data-cck-keep-for-search="" />
	<?php
	$tmpl	=	$app->input->get( 'tmpl', '' );
	if ( $tmpl ) { ?>
	<input type="hidden" name="tmpl" value="<?php echo $tmpl; ?>" data-cck-keep-for-search="" />
	<?php } ?>
</div>
</form>
<?php
if ( $this->show_list_desc == 2 && $this->description != '' ) {
	echo '<div class="seblod cck_page_desc'.$this->pageclass_sfx.'">' . JHtml::_( 'content.prepare', $this->description ) . '</div><div class="clr"></div>';
}
Helper_Display::quickCopyright();
?>