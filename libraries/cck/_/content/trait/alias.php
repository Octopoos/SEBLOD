<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: alias.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Application\ApplicationHelper;

if ( version_compare( PHP_VERSION, '5.4', '<=' ) ) {
	return;
}

// JCckContentTrait
trait JCckContentTraitAlias
{
	// _tag
	protected function _updateAlias()
	{
		if ( !$this->_pk ) {
			return false;
		}
		if ( !$this->can( 'save' ) ) {
			$this->log( 'error', 'Permissions denied.' );

			return false;
		}

		if ( trim( $this->_instance_base->title ) == '' ) {
			return false;
		}
		$alias	=	trim( $this->_instance_base->title );
		$alias	=	ApplicationHelper::stringURLSafe( $alias, $this->_instance_base->language );

		if ( trim( str_replace( '-', '', $alias ) ) == '' ) {
			$alias	=	JFactory::getDate()->format( 'Y-m-d-H-i-s' );
		}

		$this->_instance_base->alias	=	$alias;

		return $this->_instance_base->store();
	}
}
?>