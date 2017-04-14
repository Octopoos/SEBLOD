<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: form.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.modeladmin' );

// Model
class JCckBaseLegacyModelAdmin extends JModelAdmin
{
	// __construct
	public function __construct( $config = array() )
	{
		$config	=	array_merge(
						array(
							'event_after_delete'  => 'onCckConstructionAfterDelete',
							'event_after_save'    => 'onCckConstructionAfterSave',
							'event_before_delete' => 'onCckConstructionBeforeDelete',
							'event_before_save'   => 'onCckConstructionBeforeSave',
							'events_map'          => array(	'delete'=>'content', 'save'=>'content' )
						), $config
					);
		
		parent::__construct( $config );
	}
	
	// getForm
	public function getForm( $data = array(), $loadData = true )
	{
	}

	// prepareTable
	protected function prepareTable( $table )
	{
		$data	=	$this->prepareData();
		
		$this->prepareTable2( $table, $data );
		
		$table->bind( $data );
		if ( isset( $table->version ) && isset( $table->id ) && $table->id > 0 ) {
			$table->version++;
		}
	}

	// prepareTable2
	protected function prepareTable2( &$table, &$data )
	{
	}
}
?>