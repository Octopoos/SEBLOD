<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: default_hidden.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

$app	=	JFactory::getApplication();
?>
<input type="hidden" name="boxchecked" id="boxchecked" value="0" data-cck-remove-before-search="" />
<?php if ( !JFactory::getConfig()->get( 'sef' ) || !$this->config['Itemid'] ) { ?>
<input type="hidden" name="option" value="com_cck" data-cck-keep-for-search="" />
<input type="hidden" name="view" value="list" data-cck-keep-for-search="" />
<?php if ( $this->home === false ) { ?>
<input type="hidden" name="Itemid" value="<?php echo $app->input->getInt( 'Itemid', 0 ); ?>" data-cck-keep-for-search="" />
<?php } }
$tmpl	=	$app->input->get( 'tmpl', '' );
if ( $tmpl ) { ?>
<input type="hidden" name="tmpl" value="<?php echo $tmpl; ?>" data-cck-keep-for-search="" />
<?php } ?>
<input type="hidden" name="search" value="<?php echo $this->search->name; ?>" data-cck-keep-for-search="" />
<input type="hidden" name="task" value="search" data-cck-keep-for-search="" />