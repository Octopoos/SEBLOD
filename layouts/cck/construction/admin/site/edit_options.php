<?php
defined( '_JEXEC' ) or die;
?>
<div class="row-pane"><div class="row">
	<div class="col-12 col-lg-12">
		<fieldset class="options-form">
			<legend>
				<?php echo JText::_( 'COM_CCK_URLS' ); ?>
			</legend>
			<?php
			echo $displayData['fields']['site_aliases'];
            echo $displayData['fields']['site_exclusions'];
			?>
		</fieldset>
	</div>
	<div class="col-12 col-lg-12">
		<fieldset class="options-form">
			<legend>
				<?php echo JText::_( 'COM_CCK_FIELDS' ); ?>
			</legend>
			<?php
			echo $displayData['fields']['custom_fields'];
			?>
		</fieldset>
	</div>
</div></div>