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
	if ( $attr && strpos( $attr, '$cck->' ) !== false ) {
		$matches	=	'';
		$search		=	'#\$cck\->get([a-zA-Z0-9_]*)\( ?\'([a-zA-Z0-9_,]*)\' ?\)(;)?#';

		preg_match_all( $search, $attr, $matches );

		foreach ( $matches[2] as $k=>$field_name ) {
			$get		=	'get'.$matches[1][$k];
			$replace	=	$this->$get( $field_name );
			$attr		=	str_replace( $matches[0][$k], $replace, $attr );
		}
	}
?>
<div<?php echo $class.$attr; ?>>
<?php }
echo $content;
if ( $class || $attr ) { ?>
</div>
<?php } ?>