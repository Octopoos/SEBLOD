<?php
defined( '_JEXEC' ) or die;

use Joomla\CMS\Language\Text;
?>
<div class="row-pane"><div class="row">
	<div class="col-12 col-lg-8">
		<fieldset class="options-form">
			<legend>
				<?php echo Text::_( 'COM_CCK_SETTINGS' ); ?>
			</legend>
			<?php
			echo $displayData['fields']['_key'];
			echo $displayData['fields']['_value'];
			echo $displayData['fields']['_mode'];
			echo $displayData['fields']['_username'];
			echo $displayData['fields']['_password'];
			echo $displayData['fields']['_token'];
			?>
		</fieldset>
	</div>
	<div class="col-12 col-lg-4">
	</div>
</div></div>