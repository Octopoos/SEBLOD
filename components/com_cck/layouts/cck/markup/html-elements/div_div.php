<?php
defined( '_JEXEC' ) or die;

$class	=	trim( $displayData['field']->markup_class );
$class	=	$class ? ' class="'.$class.'"' : '';
?>
<div<?php echo $class; ?>>
	<div><?php echo $displayData['html']; ?></div>
</div>