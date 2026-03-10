<?php
defined( '_JEXEC' ) or die;

use Joomla\CMS\Language\Text;
?>
<div class="row-pane"><div class="row">
	<div class="col-12">
		<fieldset class="options-form">
			<legend>
				<?php echo Text::_( 'COM_CCK_REQUEST' ); ?>
			</legend>
			<?php
			echo $displayData['fields']['_request'];
			?>
		</fieldset>
		<fieldset class="options-form">
			<legend>
				<?php echo Text::_( 'COM_CCK_RESPONSE' ); ?>
			</legend>
			<?php
			echo $displayData['fields']['_response'];
			?>
		</fieldset>
	</div>
</div></div>