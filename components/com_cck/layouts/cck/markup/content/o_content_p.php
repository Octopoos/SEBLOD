<?php
defined( '_JEXEC' ) or die;

// Base
$attr	=	'';
$class	=	trim( $displayData['field']->markup_class );
$class	=	$class ? ' class="'.$class.'"' : '';
$label	=	'';

// Label
if ( $displayData['options']->get( 'field_label', $displayData['cck']->getStyleParam( 'field_label', 1 ) ) ) {
	$label	=	$displayData['cck']->getLabel( $displayData['field']->name, false );
}
?>
<p<?php echo $class; ?>>
	<span><?php echo $label; ?></span>
	<span>
		<?php echo $displayData['html']; ?>
	</span>
</p>