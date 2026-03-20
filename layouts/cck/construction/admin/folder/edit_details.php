<?php
defined( '_JEXEC' ) or die;
?>
<div class="row-pane"><div class="row">
	<div class="col-12 col-lg-8">
		<fieldset class="options-form">
			<legend>
				<?php echo JText::_( 'COM_CCK_SETTINGS' ); ?>
			</legend>
			<?php
			echo $displayData['fields']['introchar'];
			echo $displayData['fields']['colorchar'];
			?>
		</fieldset>
	</div>
	<div class="col-12 col-lg-4">
		<fieldset class="options-form">
			<legend>
				<?php echo JText::_( 'COM_CCK_ELEMENTS' ); ?>
			</legend>
			<?php echo $displayData['fields']['elements']; ?>
		</fieldset>
	</div>
</div></div>