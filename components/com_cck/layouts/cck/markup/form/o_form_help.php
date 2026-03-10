<?php
defined( '_JEXEC' ) or die;

// Base
$class	=	$displayData['field']->markup_class;

// Label
if ( $displayData['options']->get( 'field_label', $displayData['cck']->getStyleParam( 'field_label', 1 ) ) ) {
	$label	=	$displayData['cck']->getLabel( $displayData['field']->name, true, ( $displayData['field']->required ? '*' : '' ) );
	$label	=	( $label != '' ) ? '<div class="o-label">'.$label.'</div>' : '';
}
?>
<div class="o-field<?php echo $class; ?>">
	<?php echo $label; ?>
	<div class="o-input">
		<p class="o-help"><?php echo $displayData['html'] ?></p>
	</div>
</div>