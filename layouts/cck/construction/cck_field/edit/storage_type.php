<?php
defined( '_JEXEC' ) or die;

if ( $displayData['type'] ) {
	$class	=	'';
	$text	=	JText::_( 'COM_CCK_FIELD_IS_LINKED' ).' <strong>'.$displayData['type'].'</strong>';
} else {
	$class	=	' class="success"';
	$text	=	JText::_( 'COM_CCK_FIELD_IS_GENERIC' );
}
?>
<?php if ( !$displayData['isNew'] ) { ?>
<details<?php echo $class; ?> style="order:4">
	<summary class="rule-notes"><?php echo $text; ?></summary>
	<div class="rule-notes">If you need to change this setting...</div>
	<div>
		<?php
		echo $displayData['storage']['alter'];
		echo $displayData['storage']['alter_table'];
		echo $displayData['storage']['alter_type'];
		?>
	</div>
</details>
<?php } else { ?>
<details<?php echo $class; ?> style="order:6; margin-top:0; margin-bottom:8px;">
	<summary class="rule-notes"><?php echo '...'; ?></summary>
	<div>
		<?php
		echo $displayData['storage']['alter'];
		echo $displayData['storage']['alter_table'];
		echo $displayData['storage']['alter_type'];
		?>
	</div>
</details>
<?php } ?>