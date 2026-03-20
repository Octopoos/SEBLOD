<?php
defined( '_JEXEC' ) or die;
?>
<div class="row-pane"><div class="row">
	<div class="col-12 col-lg-12">
		<fieldset class="options-form">
			<legend>
				<?php echo JText::_( 'COM_CCK_SETTINGS' ); ?>
			</legend>
			<?php
			echo $displayData['fields']['site_name'];
			echo $displayData['fields']['site_pagetitles'];
			echo $displayData['fields']['site_pagetitle'];
			echo $displayData['fields']['site_offline'];
			echo $displayData['fields']['site_metadesc'];
			echo $displayData['fields']['site_metakeys'];
			echo $displayData['fields']['site_homepage'];
			echo $displayData['fields']['site_language'];
			echo $displayData['fields']['site_template_style'];
			?>
		</fieldset>
	</div>
</div></div>