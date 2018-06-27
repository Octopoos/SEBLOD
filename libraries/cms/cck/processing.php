<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: processing.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JCckProcessing
abstract class JCckProcessing
{
	protected $_event	=	null;
	protected $_script	=	null;

	public $options		=	null;

	// __construct
	public function __construct()
	{
	}

	// execute
	public function execute()
	{
	}

	// isFirstItem
	public function isFirstItem()
	{
	}

	// isLastItem
	public function isLastItem()
	{
	}

	// isSecure
	public function isSecure()
	{
		if ( $this->_event ) {
			return true;
		}

		return false;
	}

	// sendMail
	public function sendMail()
	{
	}

	// renderLayout
	public function renderLayout()
	{
	}
}
?>