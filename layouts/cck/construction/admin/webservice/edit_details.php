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
			?>
		</fieldset>
	</div>
	<div class="col-12 col-lg-4">
		<fieldset class="options-form">
			<legend>
				<?php echo Text::_( 'COM_CCK_INTEGRATION_AUTH' ); ?>
			</legend>
			<?php
			echo $displayData['fields']['_auth'];
			?>
		</fieldset>
	</div>
</div></div>