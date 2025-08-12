<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: form.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Model
class CCKModelForm extends JModelLegacy
{
	// store
	public function store( $preconfig, $task = '' )
	{
		$preconfig['client']	=	'site';
		
		jimport( 'cck.base.form.form' );
		include_once JPATH_SITE.'/libraries/cck/base/form/store_inc.php';
		
		return $config;
	}

	// storeField
	public function storeField( $preconfig )
	{
		// $preconfig['client']	=	'content|intro';
		
		$config	=	array(
						'id'=>$preconfig['id'],
						'pk'=>0
					);

		$content	=	JCckContent::getInstance( $preconfig['id'] );

		if ( $content->isSuccessful() ) {
			$field	=	JCckDatabase::loadObject( 'SELECT id, storage, storage_field, storage_field2 FROM #__cck_core_fields WHERE name = "'.$preconfig['target'].'"' );

			if ( $field->storage != 'standard' ) {
				return $config;
			}

			$content->setProperty( $field->storage_field, $preconfig['value'] );

			if ( $content->store() ) {
				$config['pk']	=	$content->getPk();
			}
		}
		
		return $config;
	}
}
?>