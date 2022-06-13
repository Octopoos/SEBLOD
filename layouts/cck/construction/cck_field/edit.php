<?php
defined( '_JEXEC' ) or die;

echo '<div class="row-pane"><div class="row">';

foreach ( $displayData['form'] as $form ) {
	$attr	=	'';
	$class	=	'';
	$class2	=	'';

	if ( isset( $form['legend'] ) && $form['legend'] ) {
		$col	=	'8';
		$legend	=	$form['legend'];
		
	} elseif ( isset( $form['mode'] ) && $form['mode'] == 'storage' ) {
		$class	=	' minus-1';
		$class2	=	' is-sticky';
		$col	=	'4';
		$legend	=	JText::_( 'COM_CCK_'.$form['mode'] );
	} else {
		$class	=	' minus-2';
		$col	=	'8';
		$legend	=	JText::_( 'COM_CCK_SETTINGS' );
	}
	if ( $legend ) {
		$legend	=	'<legend>'.$legend.'</legend>';
	}

	echo '<div class="col-12 col-lg-'.$col.$class.'">';
	echo '<fieldset class="options-form'.$class2.'">'.$legend;
	// echo '<div class="form-grid">';

	if ( isset( $form['fields'] ) ) {
		foreach ( $form['fields'] as $field ) {
			echo $field;
		}
	}

	// echo '</div>';
	echo '</fieldset>';
	echo '</div>';
}
if ( $displayData['script'] ) {
	echo '<script>'.$displayData['script'].'</script>';
}

echo '</div></div>';
?>