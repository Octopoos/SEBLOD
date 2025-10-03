<?php
defined( '_JEXEC' ) or die;

$attr	=	trim( $displayData['field']->markup_class );
$attr	=	$attr ? ' '.$attr : '';
?>
<h3<?php echo $attr; ?>><?php echo $displayData['html']; ?></h3>