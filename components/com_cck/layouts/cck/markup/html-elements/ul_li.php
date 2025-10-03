<?php
defined( '_JEXEC' ) or die;

$class	=	trim( $displayData['field']->markup_class );
$class	=	$class ? ' class="'.$class.'"' : '';
?>
<ul<?php echo $class; ?>>
	<li><?php echo $displayData['html']; ?></li>
</ul>