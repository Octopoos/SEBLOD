<?php
defined( '_JEXEC' ) or die;
?>
<ul class="spe spe_title">
	<?php
	if ( $displayData['params']['title_mode'] ) {
		echo '<li><label>'.JText::_( 'COM_CCK_TITLE' ).'</label><div class="input-group left">'.$displayData['fields']['title'].'</div></li>';
	} else {
		echo $displayData['fields']['title'];
	}
	?>
</ul>
<ul class="spe spe_folder">
	<?php echo $displayData['fields']['folder']; ?>
</ul>
<ul class="spe spe_state">
	<?php echo $displayData['fields']['state']; ?>
</ul>
<ul class="spe spe_name">
	<?php
	if ( $displayData['params']['name_mode'] ) {
		$label	=	'<span class="star"> *</span>';
		$html	=	'';
	} else {
		$label	=	'';
		$html	=	'<span class="variation_value" style="display:block;"><strong>'.$displayData['params']['name_value'].'</strong></span>';
	}

	echo '<li><label>'.JText::_( 'COM_CCK_NAME' ).$label.'</label>'.$html.$displayData['fields']['name'].'</li>';
	?>
</ul>
<ul class="spe spe_type">
	<?php echo str_replace( 'tabindex="3"', 'tabindex="4"', $displayData['fields']['type'] ); ?>
</ul>
<ul class="spe spe_description spe_latest">
	<?php echo '<li>'.$displayData['fields']['description'].'</li>'; ?>
</ul>