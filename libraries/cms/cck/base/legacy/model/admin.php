<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: form.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

jimport( 'joomla.application.component.modeladmin' );

// Model
if ( JCck::on() ) {
	class JCckBaseLegacyModelAdmin extends JModelAdmin
	{
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
}  else {
	class JCckBaseLegacyModelAdmin extends JModelAdmin
	{
		// getForm
		public function getForm( $data = array(), $loadData = true )
		{
		}

		// prepareTable
		protected function prepareTable( &$table )
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
}
?>