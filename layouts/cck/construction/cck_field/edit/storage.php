<?php
defined( '_JEXEC' ) or die;

if ( isset( $displayData['html']['prepend'] ) ) {
	echo $displayData['html']['prepend'];
}

echo '<div class="form-grid gap-0">';

foreach ( $displayData['form'] as $form ) {
	if ( isset( $form['fields'] ) ) {
		foreach ( $form['fields'] as $k=>$field ) {
			$attr	=	'';

			if ( isset( $form['attributes'][$k] ) && $form['attributes'][$k] ) {
				$attr	=	' '.$form['attributes'][$k];
			}

			echo '<div class="control-group"'.$attr.'>';
			echo $field;
			echo '</div>';
		}
	}
}
if ( isset( $displayData['html']['append'] ) ) {
	echo $displayData['html']['append'];
} elseif ( !isset( $displayData['html']['prepend'] ) ) {
	if ( $displayData['html'] ) {
		echo $displayData['html'];
	}	
}
if ( $displayData['script'] ) {
	echo '<script>'.$displayData['script'].'</script>';
}

echo '</div>';
?>