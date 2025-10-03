<?php
defined( '_JEXEC' ) or die;

$class	=	trim( $displayData['field']->markup_class );
$class	=	$class ? ' class="'.$class.'"' : '';
?>
<div><h1<?php echo $class; ?>><span><?php echo $displayData['html']; ?></span></h1></div>