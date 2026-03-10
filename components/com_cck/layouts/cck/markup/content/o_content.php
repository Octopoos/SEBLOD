<?php
defined( '_JEXEC' ) or die;

// Base
$attr	=	'';
$class	=	trim( $displayData['field']->markup_class );
$label	=	'';

// Label
if ( $displayData['options']->get( 'field_label', $displayData['cck']->getStyleParam( 'field_label', 1 ) ) ) {
	$label	=	$displayData['cck']->getLabel( $displayData['field']->name, false );
	$label	=	$label ? '<p><span>'.$label.'</span></p>' : '';
}

if ( $class || $label ) {
	$class	=	$class ? ' class="'.$class.'"' : '';
?>
<div<?php echo $class; ?>>
	<?php echo $label.$displayData['html']; ?>
</div>
<?php } else {
	echo $displayData['html'];
}
?>