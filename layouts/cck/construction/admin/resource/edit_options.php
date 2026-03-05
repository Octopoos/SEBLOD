<?php
defined( '_JEXEC' ) or die;

use Joomla\CMS\Language\Text;
?>
<div class="row-pane"><div class="row">
	<div class="col-12">
		<fieldset class="options-form">
			<legend>
				<?php echo Text::_( 'COM_CCK_INPUT' ).' '.Text::_( 'COM_CCK_INPUT_API_SUPPORT' ); ?>
			</legend>
			<?php
			echo $displayData['fields']['_input_property'];
			echo $displayData['fields']['_input'];
			?>
		</fieldset>
		<fieldset class="options-form">
			<legend>
				<?php echo Text::_( 'COM_CCK_OUTPUT' ); ?>
			</legend>
			<?php
			echo $displayData['fields']['_output_hateoas_pagination'];
			echo $displayData['fields']['_output_group_by'];
			echo $displayData['fields']['_output_limit'];
			echo $displayData['fields']['_output_ordering'];
			echo $displayData['fields']['_output_keys'];
			echo $displayData['fields']['_output'];
			?>
		</fieldset>
	</div>
</div></div>