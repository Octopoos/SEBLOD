<?php
defined( '_JEXEC' ) or die;

use Joomla\CMS\Language\Text;
?>
<div class="row-pane"><div class="row min-hf-300">
	<div class="col-12 col-lg-8">
		<fieldset class="options-form">
			<legend>
				<?php echo Text::_( 'COM_CCK_SETTINGS' ); ?>
			</legend>
			<?php
			echo $displayData['fields']['_content_type'];
			echo $displayData['fields']['_content_types'];
			echo $displayData['fields']['_content_table'];
			echo $displayData['fields']['_field_name'];
			echo $displayData['fields']['_processing'];
			echo $displayData['fields']['_debug'];
			echo $displayData['fields']['_hateoas'];
			?>
		</fieldset>
	</div>
	<div class="col-12 col-lg-4">
		<fieldset class="options-form">
			<legend>
				<?php echo Text::_( 'COM_CCK_METHODS' ); ?>
			</legend>
			<?php
			echo $displayData['fields']['_methods'];
			?>
		</fieldset>
	</div>
</div></div>