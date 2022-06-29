<?php
defined( '_JEXEC' ) or die;
?>
	<div>
		<ul class="spe spe_title">
			<?php
			echo $displayData['fields']['title'].$displayData['fields']['name'];
			?>
		</ul>
		<ul class="spe spe_folder">
			<?php echo $displayData['fields']['folder']; ?>
		</ul>
		<ul class="spe spe_state spe_third">
			<?php echo $displayData['fields']['state']; ?>
		</ul>
		<ul class="spe spe_description">
			<?php echo '<li>'.$displayData['fields']['description'].'</li>'; ?>
		</ul>
	</div>
	<div class="clr"></div>
	<div class="togglebar">
		<div>
		<?php
		echo $displayData['fields']['bar_clients'];
		echo $displayData['fields']['bar_panels'];
		?>
		</div>
	</div>
	<div id="toggle_more" class="toggle_more <?php echo $this->panel_class; ?>"></div>
</div>
<div class="seblod first" id="more" style="<?php echo $this->panel_style; ?>height:<?php echo $this->css['panel_height']; ?>;">
	<div>
		<ul class="spe spe_title">
			<?php echo $displayData['fields']['alias']; ?>
		</ul>
		<ul class="spe spe_folder">
			<?php echo $displayData['fields']['folder']; ?>
		</ul>
		<ul class="spe spe_third">
			<?php
			$html	=	JCckDev::getForm( 'core_description', $this->item->permissions, $config, array( 'selectlabel'=>'Button Icon Edit', 'options2'=>'{"editor":"none"}', 'bool8'=>false, 'storage_field'=>'permissions', 'attributes'=>'style="margin:0 0 0 2px;"' ) );
			echo JCckDev::renderForm( $cck['core_rules_type'], $this->item->asset_id, $config, array(), array( 'after'=>$html ) );
			?>
		</ul>
		<ul class="spe spe_name">
			<?php
			if ( !$this->item->id ) {
				echo '<li><label>'.JText::_( 'COM_CCK_QUICK_MENU_ITEM' ).'</label>'
				 .	 '<select id="quick_menuitem" name="quick_menuitem" class="inputbox" style="max-width:180px;">'
				 .	 '<option value="">- '.JText::_( 'COM_CCK_SELECT_A_PARENT').' -</option>'
				 .	 JHtml::_( 'select.options', JHtml::_( 'menu.menuitems' ) )
				 .	 '</select></li>';
			} else {
				echo JCckDev::renderForm( 'core_dev_select', $this->item->admin_form, $config, array( 'label'=>'Admin Form', 'defaultvalue'=>'0', 'selectlabel'=>'', 'options'=>'Administrator Only=0||Administrator or Allowed Groups=optgroup||Administrator or Allowed Groups Always=1||Administrator or Allowed Groups Edit=2', 'storage_field'=>'admin_form' ) );
			}
			?>
		</ul>
		<ul class="spe spe_type">
			<?php echo $displayData['fields']['location']; ?>
		</ul>
		<ul class="spe spe_sixth">
			<?php echo $displayData['fields']['css_core']; ?>
		</ul>
		<ul class="spe spe_name">
			<?php echo $displayData['fields']['parent']; ?>
		</ul>
		<ul class="spe spe_type">
			<?php echo $displayData['fields']['access']; ?>
		</ul>
		<ul class="spe spe_sixth">
			<?php echo $displayData['fields']['indexed']; ?>
		</ul>
	</div>
