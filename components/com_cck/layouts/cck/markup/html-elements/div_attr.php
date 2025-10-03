<?php
defined( '_JEXEC' ) or die;

$attr	=	trim( $displayData['field']->markup_class );
$attr	=	$attr ? ' '.$attr : '';
?>
<div<?php echo $attr; ?>><?php echo $displayData['html']; ?></div>