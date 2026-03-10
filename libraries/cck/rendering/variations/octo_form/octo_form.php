<?php
/**
* @version 			Octo 1.x
* @package			Octo Template Framework
* @url				https://www.octopoos.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2019 Octopoos. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$attr	=	$options->get( 'position_attributes', '' );
$attr	=	$attr ? ' '.$attr : '';
$class	=	$options->get( 'position_class', '' );
$class	=	$class ? ' class="'.$class.'"' : '';

if ( $class || $attr ) {
?>
<div<?php echo $class.$attr; ?>>
<?php } ?>
	<?php echo $content; ?>
<?php if ( $class || $attr ) { ?>
</div>
<?php } ?>