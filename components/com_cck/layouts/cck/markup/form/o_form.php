<?php
defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

static $isSuperUser	=	0;
static $viewlevels	=	array(
							'3'=>'special',
							'7'=>'admin',
							'6'=>'super-user'
						);

if ( $isSuperUser === 0 ) {
	$isSuperUser	=	Factory::getUser()->authorise( 'core.admin' );
}

// Base
$attr	=	'';
$class	=	$displayData['field']->markup_class;
$desc	=	'';
$hasId	=	false;
$label	=	'';

// Computation
if ( isset( $displayData['field']->computation ) && $displayData['field']->computation ) {
	$displayData['cck']->setComputationRules( $displayData['field'] );

	$hasId	=	true;
}

// Conditional
if ( isset( $displayData['field']->conditional ) && $displayData['field']->conditional ) {
	$displayData['cck']->setConditionalStates( $displayData['field'] );

	$hasId	=	true;
}

if ( $hasId ) {
	$attr	=	' id="'.$displayData['cck']->id.'_'.$displayData['field']->name.'"';
}

if ( $isSuperUser && isset( $viewlevels[(string)$displayData['field']->access] ) ) {
	$attr	.=	' data-access="'.$viewlevels[(string)$displayData['field']->access].'"';
}

// Description
if ( $displayData['options']->get( 'field_description', $displayData['cck']->getStyleParam( 'field_description', 0 ) ) ) {
	if ( $displayData['field']->description != '' ) {
		if ( $displayData['options']->get( 'field_description', $displayData['cck']->getStyleParam( 'field_description', 0 ) ) == 5 ) {
			$class	.=	' o-help';
			HTMLHelper::_( 'bootstrap.popover', '.hasPopover', array( 'container'=>'body', 'html'=>true, 'trigger'=>'hover' ) );
			$desc	=	'<div class="hasPopover" data-placement="left" data-animation="false" data-content="'.htmlspecialchars( $displayData['field']->description ).'" title="'.htmlspecialchars( $displayData['field']->label ).'"><span class="icon-help"></span></div>';
			$desc	=	 '<div class="o-help-icon">'.$desc.'</div>';
		} else {
			$desc	=	$displayData['field']->description;
			$desc	=	 '<p class="o-help">'.$desc.'</p>';
		}
	}
}

// Label
if ( $displayData['options']->get( 'field_label', $displayData['cck']->getStyleParam( 'field_label', 1 ) ) ) {
	$label	=	$displayData['cck']->getLabel( $displayData['field']->name, true, ( $displayData['field']->required ? '*' : '' ) );
	$label	=	( $label != '' ) ? '<div class="o-label">'.$label.'</div>' : '';
}
?>
<div class="o-field<?php echo $class; ?>"<?php echo $attr; ?>>
	<?php echo $label; ?>
	<div class="o-input">
		<?php echo $displayData['html'].$desc; ?>
	</div>
</div>