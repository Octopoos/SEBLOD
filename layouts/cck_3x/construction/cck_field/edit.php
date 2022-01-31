<?php
defined( '_JEXEC' ) or die;

echo '<div class="seblod">';

foreach ( $displayData['form'] as $form ) {
	if ( isset( $form['legend'] ) && $form['legend'] ) {
		echo JCckDev::renderSpacer( $form['legend'] );
	} elseif ( isset( $form['mode'] ) && $form['mode'] == 'storage' ) {
		echo JCckDev::renderSpacer( JText::_( 'COM_CCK_STORAGE' ), JText::_( 'COM_CCK_STORAGE_DESC' ) );
	} else {
		echo JCckDev::renderLegend( JText::_( 'COM_CCK_CONSTRUCTION' ), JText::_( 'PLG_CCK_FIELD_'.$displayData['item']->type.'_DESC' ) );
		echo '<ul class="adminformlist adminformlist-2cols">';
	}

	if ( isset( $form['fields'] ) ) {
		foreach ( $form['fields'] as $field ) {
			echo $field;
		}
	}
}

echo '</ul>';
echo '</div>';

if ( $displayData['script'] ) {
	echo '<script>'.$displayData['script'].'</script>';
}
?>