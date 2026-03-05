<?php
defined( '_JEXEC' ) or die;

$class	=	isset( $displayData['class'] ) ? ' '.$displayData['class'] : '';
?>
<div class="control-group<?php echo $class; ?>">
	<?php if ( $displayData['label'] === false ) { } else { ?>
		<div class="control-label"><label><?php echo $displayData['label']; ?></label></div>
	<?php } ?>
	<div class="controls"><?php echo $displayData['html']; ?></div>
</div>