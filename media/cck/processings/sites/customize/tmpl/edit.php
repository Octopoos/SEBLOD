<?php
/**
* @version 			SEBLOD 3.x More
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

JCckDev::initScript( 'processing', $this->item );

$count		=	0;
$group_id	=	'tabs';
$params		=	array(
					array( 'name'=>'email', 'label'=>'Email', 'required'=>'', 'attributes'=>'placeholder="ex: seb_site_administrator_email" autocomplete="off"' ),
					array( 'name'=>'password', 'label'=>'Password', 'required'=>'', 'attributes'=>'placeholder="Optional" autocomplete="off"' ),
					array( 'name'=>'username', 'label'=>'Username', 'required'=>'', 'attributes'=>'placeholder="If empty: Email"' ),
					array( 'name'=>'name', 'label'=>'Name', 'required'=>'', 'attributes'=>'placeholder="If empty: First Name + Last Name"' ),
					array( 'name'=>'first_name', 'label'=>'First Name', 'required'=>'', 'attributes'=>'placeholder="ex: seb_site_administrator_first_name"' ),
					array( 'name'=>'last_name', 'label'=>'Last Name', 'required'=>'', 'attributes'=>'placeholder="ex: seb_site_administrator_last_name"' )
				);
$types		=	array(
					'2'=>'registered',
					'3'=>'author',
					'6'=>'manager',
					'7'=>'administrator'
				);
?>
<div class="seblod">
	<?php echo JCckDev::renderLegend( JText::_( 'COM_CCK_SETTINGS' ) ); ?>
	<?php
	echo '<ul class="adminformlist adminformlist-2cols" style="width:100%">';
	echo JCckDev::renderBlank();
	echo JCckDev::renderForm( 'core_content_type', @$options['content_types'], $config, array( 'label'=>'Content Type Targets', 'defaultvalue'=>'', 'storage_field'=>'json[options][content_types]', 'required'=>'required' ) );
	echo '</ul>';

	if ( @$options['type'] != '' ) {
		$users	=	explode( ',', @$options['type'] );
		$count	=	count( $users );
	}

	// User Accounts
	for ( $i = 0; $i < $count; $i++ ) {
		$k			=	$users[$i];
		$account	=	$types[$k];
		$label		=	'"'.JText::_( 'COM_CCK_'.strtoupper( $account ) ).'"';
		
		if ( $i == 0 ) {
			echo JCckDevTabs::start( $group_id, 'account'.$i, $label, array( 'active'=>'account'.$i ) );
		} else {
			echo JCckDevTabs::open( $group_id, 'account'.$i, $label );
		}
		
		echo '<ul class="adminformlist adminformlist-2cols">';

		foreach ( $params as $p ) {
			echo JCckDev::renderForm( 'core_dev_text', @$options[$account][$p['name']], $config, array( 'label'=>$p['label'], 'storage_field'=>'json[options]['.$account.']['.$p['name'].']', 'required'=>$p['required'], 'attributes'=>$p['attributes'] ) );
		}
		echo JCckDev::renderForm( 'core_bool', @$options[$account]['bridge'], $config, array( 'label'=>'Bridge', 'defaultvalue'=>'0', 'storage_field'=>'json[options]['.$account.'][bridge]' ) );
		echo JCckDev::renderForm( 'core_bool', @$options[$account]['force_password'], $config, array( 'label'=>'Password', 'defaultvalue'=>'0', 'options'=>'Clear=0||MD5=1', 'storage_field'=>'json[options]['.$account.'][force_password]' ) );
		echo JCckDev::renderForm( 'core_bool', @$options[$account]['set_author'], $config, array( 'label'=>'Set As Author', 'defaultvalue'=>'0', 'storage_field'=>'json[options]['.$account.'][set_author]' ) );

		echo '</ul>';
	}

	// Options
	if ( !$count ) {
		echo JCckDevTabs::start( $group_id, 'tabs1', JText::_( 'COM_CCK_OPTIONS' ), array( 'active'=>'tabs1' ) );
	} else {
		echo JCckDevTabs::open( $group_id, 'tabs2', JText::_( 'COM_CCK_OPTIONS' ) );
	}
	echo '<ul class="adminformlist adminformlist-2cols">';
	echo JCckDev::renderForm( 'core_dev_select', @$options['type'], $config, array( 'label'=>'Type', 'defaultvalue'=>'2,7', 'selectlabel'=>'', 'options'=>'Minimal=6||Basic=7||Standard=2,7||Advanced=2,3,6,7', 'storage_field'=>'json[options][type]' ) );
	echo '</ul>';

	echo JCckDevTabs::end();
	?>
</div>