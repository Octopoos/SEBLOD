<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: menu.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( 'JPATH_PLATFORM' ) or die;

// CCKMenu
class CCKMenu extends JMenu
{
	protected $user_id	=	0;

	// __construct
	public function __construct( $options = array() )
	{
		if ( isset( $options['user_id'] ) ) {
			$this->user_id	=	(int)$options['user_id'];

			unset( $options['user_id'] );
		}

		parent::__construct( $options );
	}

	// makeHimLive
	public function makeHimLive()
	{
		if ( isset( self::$instances['site'] ) ) {
			self::$instances['site']->user	=	JFactory::getUser( $this->user_id );
		}
	}
}
?>