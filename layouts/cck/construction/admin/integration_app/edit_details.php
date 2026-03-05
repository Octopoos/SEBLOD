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
			echo $displayData['fields']['_auth'];
			echo $displayData['fields']['_run_as'];
			?>
		</fieldset>
		<fieldset class="options-form">
			<legend>
				<?php echo Text::_( 'COM_CCK_ENCRYPTION' ); ?>
			</legend>
			<?php
			echo $displayData['fields']['_key_private'];
			echo $displayData['fields']['_key_public'];
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