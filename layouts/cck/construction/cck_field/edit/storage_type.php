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
<details<?php echo $class; ?> style="order:4">
	<summary class="rule-notes"><?php echo $text; ?></summary>
	<div class="rule-notes">If you need to change this setting...</div>
</details>