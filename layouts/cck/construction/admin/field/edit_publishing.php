<?php
defined( '_JEXEC' ) or die;
?>
<div class="row-pane"><div class="row">
	<div class="col-12 col-lg-8">
		<fieldset class="options-form">
			<legend>
				<?php echo JText::_( 'COM_CCK_PUBLISHING' ); ?>
			</legend>
			<?php
			echo $displayData['fields']['folder'];
			echo $displayData['fields']['state'];
			?>
		</fieldset>
	</div>
	<div class="col-12 col-lg-4">
		<fieldset class="options-form">
			<legend>
				<?php echo JText::_( 'COM_CCK_INFO' ); ?>
			</legend>
			<?php echo $displayData['fields']['description']; ?>
		</fieldset>
	</div>
</div></div>