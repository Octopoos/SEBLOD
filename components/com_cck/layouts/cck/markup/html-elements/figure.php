<?php
defined( '_JEXEC' ) or die;

$class	=	trim( $displayData['field']->markup_class );
$class	=	$class ? ' class="'.$class.'"' : '';
?>
<figure<?php echo $class; ?>><?php echo $displayData['html']; ?></figure>