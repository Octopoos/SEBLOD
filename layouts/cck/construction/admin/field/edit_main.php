<?php
defined( '_JEXEC' ) or die;
?>
<div class="row title-alias form-vertical mb-3">
	<div class="col-12 col-md-4">
		<?php
		if ( $displayData['params']['title_mode'] ) {
			echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.form.field', array(
				'html'=>'<div class="input-group left">'.$displayData['fields']['title'].'</div>',
				'label'=>'<label>'.JText::_( 'COM_CCK_TITLE' ).'<span class="star"> *</span></label>'
			) );
		} else {
			echo $displayData['fields']['title'];
		}
		?>
	</div>
	<div class="col-12 col-md-4">
		<?php
		if ( $displayData['params']['name_mode'] ) {
			$html	=	'';
		} else {
			$html	=	'<input type="text" id="name_" name="name_" value="'.$displayData['params']['name_value'].'" class="form-control" disabled="disabled" maxlength="50" size="28" tabindex="3" />';	
		}

		echo JCckDev::renderLayoutFile( 'cck'.JCck::v().'.form.field', array(
			'html'=>$html.$displayData['fields']['name'],
			'label'=>'<label>'.JText::_( 'COM_CCK_NAME' ).'<span class="star"> *</span></label>'
		) );
		?>
	</div>
	<div class="col-12 col-md-4">
		<?php echo $displayData['fields']['type']; ?>
	</div>
</div>