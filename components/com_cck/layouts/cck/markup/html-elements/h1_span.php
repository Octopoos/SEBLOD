<?php
defined( '_JEXEC' ) or die;

$class	=	trim( $displayData['field']->markup_class );
$class	=	$class ? ' class="'.$class.'"' : '';
?>
<h1<?php echo $class; ?>><span><?php echo $displayData['html']; ?></span></h1>