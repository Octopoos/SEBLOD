<?php
defined( '_JEXEC' ) or die;

echo '<li class="w100">';
echo '<label>' . JText::_( 'COM_CCK_STORAGE_LABEL' ) . '</label>';

if ( isset( $displayData['html']['prepend'] ) ) {
	echo $displayData['html']['prepend'];
}
foreach ( $displayData['form'] as $form ) {
	if ( isset( $form['fields'] ) ) {
		foreach ( $form['fields'] as $k=>$field ) {
			$attr	=	'';

			if ( isset( $form['attributes'][$k] ) ) {
				$attr	=	' '.$form['attributes'][$k];
			}

			// echo '<div class="control-group"'.$attr.'>';
			echo $field;
			// echo '</div>';
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

echo '</li>';

echo '</ul>';
echo '<div id="toggle_more2" style="display:none;" class="toggle_more closed" '.( $displayData['params']['value'] != 'dev' ) ? '' : 'style="display: none;"></div>';
echo '</div>';
?>