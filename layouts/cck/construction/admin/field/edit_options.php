<?php
defined( '_JEXEC' ) or die;
?>
<div class="row-pane"><div class="row">
	<div class="col-12 col-lg-8">
		<fieldset class="options-form">
			<legend>
				<?php echo JText::_( 'COM_CCK_ATTRIBUTES' ); ?>
			</legend>
			<?php
			echo $displayData['fields']['css'];
			echo $displayData['fields']['attributes'];
			echo $displayData['fields']['script'];
			?>
		</fieldset>
	</div>
	<div class="col-12 col-lg-8">
		<fieldset class="options-form" style="display: none;">
			<legend>
				<?php echo JText::_( 'COM_CCK_DEVELOPMENT' ); ?>
			</legend>
			<?php echo $displayData['fields']['required']; ?>
		</fieldset>
	</div>
</div></div>