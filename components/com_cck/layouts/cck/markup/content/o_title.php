<?php
defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;

$class	=	trim( $displayData['field']->markup_class );
$class	=	$class ? ' class="'.$class.'"' : '';

if ( Factory::getApplication()->input->get( 'tmpl' ) == 'raw' ) {
?>
	<span data-cck-modal-title style="display:none;"><?php echo $displayData['html']; ?></span>
<?php } else { ?>
	<h1<?php echo $class; ?>><?php echo $displayData['html']; ?></h1>
<?php
} ?>