<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckContent
class JCckContentJoomla_User extends JCckContent
{
	// getInstanceBase
	protected function getInstanceBase()
	{
		return JUser::getInstance();
	}

	// check
	public function check( $instance_name )
	{
		if ( $instance_name == 'base' ) {
			return true;
		} else {
			return $this->{'_instance_'.$instance_name}->check();
		}
	}

	// store
	public function store( $instance_name )
	{
		if ( $instance_name == 'base' ) {
			return $this->{'_instance_'.$instance_name}->save();
		} else {
			return $this->{'_instance_'.$instance_name}->store();
		}
	}
}
?>