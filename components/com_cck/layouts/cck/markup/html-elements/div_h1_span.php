<?php
defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;

$class	=	trim( $displayData['field']->markup_class );
$class	=	$class ? ' class="'.$class.'"' : '';

if ( Factory::getApplication()->input->get( 'tmpl' ) === 'raw' ) {
	echo '<span class="o-hide" data-cck-modal-title>'.$displayData['html'].'</span>';

	return;
}
?>
<div><h1<?php echo $class; ?>><span><?php echo $displayData['html']; ?></span></h1></div>