<?php
defined( '_JEXEC' ) or die;

use Joomla\CMS\HTML\HTMLHelper;

echo HTMLHelper::_( 'uitab.startTabSet', 'myTab', ['active' => 'tab0', 'recall' => true, 'breakpoint' => 768] );

foreach ( $displayData['form'] as $k=>$form ) {
	if ( isset( $form['legend'] ) && $form['legend'] ) {
		$label	=	$form['legend'];
		$legend	=	$form['legend'];
	} else {
		$label	=	JText::_( 'COM_CCK_'.strtoupper( $displayData['type'] ).'_SETTINGS' );
		$legend	=	JText::_( 'COM_CCK_SETTINGS' );
	}
	
	echo HTMLHelper::_( 'uitab.addTab', 'myTab', 'tab'.$k, $label );

	if ( $legend ) {
		$legend	=	'<legend>'.$legend.'</legend>';
	}
	
	echo '<div class="row-pane"><div class="row">';
	echo '<div class="col-12">';
	echo '<fieldset class="options-form">'.$legend;

	if ( isset( $form['fields'] ) ) {
		foreach ( $form['fields'] as $field ) {
			echo $field;
		}
	}

	echo '</fieldset>';
	echo '</div>';
	echo '</div></div>';

	echo HTMLHelper::_( 'uitab.endTab' );
}
if ( isset( $displayData['script'] ) && $displayData['script'] ) {
	echo '<script>'.$displayData['script'].'</script>';
}

echo HTMLHelper::_( 'uitab.endTabSet' );
?>