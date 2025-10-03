<?php
defined( '_JEXEC' ) or die;

$class	=	trim( $displayData['field']->markup_class );
$class	=	$class ? ' class="'.$class.'"' : '';
?>
<div<?php echo $class; ?>><h3><?php echo $displayData['html']; ?></h3></div>