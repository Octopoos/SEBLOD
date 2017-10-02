<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: _VERSION.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckVersion
final class JCckVersion extends JCckVersionObject
{
	public $RELEASE = '3.15';
	
	public $DEV_LEVEL = '0';

	public $DEV_STATUS = '';

	public $API_VERSION = array( 'v3'=>'3.13.0' );
	
	// getApiVersion
	public function getApiVersion( $version = 'v3' )
	{
		return ( isset( $this->API_VERSION[$version] ) ) ? $this->API_VERSION[$version] : '3.12.0';
	}
}
?>