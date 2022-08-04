<?php
defined( '_JEXEC' ) or die;

echo '<div class="row-pane"><div class="row">';
echo '<div class="col-12">';

foreach ( $displayData['form'] as $k=>$form ) {
	if ( isset( $form['legend'] ) && $form['legend'] ) {
		$legend	=	$form['legend'];
	} else {
		$legend	=	JText::_( 'COM_CCK_SETTINGS' );
	}

	$legend	=	'<legend>'.$legend.'</legend>';

	echo '<fieldset class="options-form">'.$legend;

	if ( isset( $form['fields'] ) ) {
		foreach ( $form['fields'] as $field ) {
			echo $field;
		}
	}

	echo '</fieldset>';
}
if ( isset( $displayData['script'] ) && $displayData['script'] ) {
	echo '<script>'.$displayData['script'].'</script>';
}

echo '</div>';
echo '</div></div>';
?>