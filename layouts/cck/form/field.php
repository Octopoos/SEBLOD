<?php
defined( '_JEXEC' ) or die;

$class	=	isset( $displayData['class'] ) ? ' '.$displayData['class'] : '';
?>
<div class="control-group<?php echo $class; ?>">
	<div class="control-label"><label><?php echo $displayData['label']; ?></label></div>
	<div class="controls"><?php echo $displayData['html']; ?></div>
</div>