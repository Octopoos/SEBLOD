<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: view.html.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Language\Text;

// View
class CCKViewSites extends JCckBaseLegacyViewList
{
	protected $vName	=	'site';
	protected $vTitle	=	_C5_TEXT;

	// getSortFields
	protected function getSortFields()
	{
		return array(
					'a.id'=>Text::_( 'COM_CCK_ID' ),
					'a.published'=>Text::_( 'COM_CCK_STATUS' ),
					'a.title'=>Text::_( 'COM_CCK_TITLE' )
				);
	}
}
?>