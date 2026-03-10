<?php
defined( '_JEXEC' ) or die;

// Base
$attr	=	'';
$class	=	trim( $displayData['field']->markup_class );

if ( isset( $displayData['field']->editable ) && $displayData['field']->editable ) {
	$attr	=	' is-editable=\''.$displayData['field']->editable.'\'';
}

if ( $attr || $class || $displayData['options']->get( 'markup', 0 ) ) {
	$class	=	$class ? ' class="'.$class.'"' : '';
?>
<div<?php echo $class.$attr; ?>>
	<?php echo $displayData['html']; ?>
</div>
<?php } else {
	echo $displayData['html'];
}
?>