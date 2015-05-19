<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: pkg_script.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Script
class pkg_cckInstallerScript
{
	// install
	function install( $parent )
	{
	}
	
	// uninstall
	function uninstall( $parent )
	{
	}
	
	// update
	function update( $parent )
	{
	}
	
	// preflight
	function preflight( $type, $parent )
	{
		$version	=	new JVersion;
		
		if ( version_compare( $version->getShortVersion(), '2.5.1', 'lt' ) ) {
			Jerror::raiseWarning( null, 'You should upgrade your site with Joomla 2.5.1 (or +) before installing SEBLOD 2.2(+).' );
			return false;
		}
		
		if ( !defined( 'DS' ) ) {
			define( 'DS', DIRECTORY_SEPARATOR );
		}
		set_time_limit( 0 );
	}
	
	// postflight
	function postflight( $type, $parent )
	{
		$lang	=	JFactory::getLanguage();
		$texts	=	array( 0=>array( 'key'=>'LIB_CCK_INSTALLATION_LEGEND_CHANGELOG', 'str'=>'Changelog of SEBLOD 3.x' ),
						   1=>array( 'key'=>'LIB_CCK_INSTALLATION_LEGEND_GETTING_STARTED', 'str'=>'Getting Started with SEBLOD 3.x' ),
						   2=>array( 'key'=>'LIB_CCK_INSTALLATION_LEGEND_RECOMMENDED_SETTINGS', 'str'=>'Recommended Settings for SEBLOD 3.x' ),
						   3=>array( 'key'=>'LIB_CCK_INSTALLATION_LATEST_CHANGELOG', 'str'=>'Open the latest Changelog.' ),
						   4=>array( 'key'=>'LIB_CCK_INSTALLATION_SEBLOD_VIDEOS', 'str'=>'Watch our Videos on SEBLOD.com' ),
						   5=>array( 'key'=>'LIB_CCK_INSTALLATION_SEBLOD_TUTORIALS', 'str'=>'Read the Tutorials on SEBLOD.com' ),
						   6=>array( 'key'=>'LIB_CCK_INSTALLATION_SEBLOD_DOCUMENTATION', 'str'=>'Learn from Manuals on SEBLOD.com' ),
						   7=>array( 'key'=>'LIB_CCK_INSTALLATION_SEBLOD_MARKETPLACE', 'str'=>'Get Free Stuff on SEBLOD.com' ),
						   8=>array( 'key'=>'LIB_CCK_INSTALLATION_CHECK_DIRECTIVE', 'str'=>'Directive' ),
						   9=>array( 'key'=>'LIB_CCK_INSTALLATION_CHECK_RECOMMENDED', 'str'=>'Recommended' ),
						   10=>array( 'key'=>'LIB_CCK_INSTALLATION_CHECK_ACTUAL', 'str'=>'Actual' ),
						   11=>array( 'key'=>'LIB_CCK_INSTALLATION_PHP_VERSION', 'str'=>'PHP Version' )
					);
		$lang->load( 'com_cck', JPATH_ADMINISTRATOR );

		foreach ( $texts as $text ) {
			if ( $lang->hasKey( $text['key'] ) == 1 ) {
				$text['str']	=	JText::_( $text['key'] );
			}
		}
		$php	=	( version_compare( PHP_VERSION, '5.3', 'lt' ) ) ? 'warning' : 'success';
		?>
		<legend><?php echo $texts[0]['str']; ?></legend>
		<p><a target="_blank" href="http://www.seblod.com/changelogs" class="btn btn-small"><span class="icon-zoom-in"></span> <?php echo $texts[3]['str']; ?></a></p>
		<legend><?php echo $texts[1]['str']; ?></legend>
		<ul class="list-striped">
			<li><a target="_blank" href="http://www.seblod.com/resources/videos"><span class="badge badge-info">1. <?php echo $texts[4]['str']; ?></span></a></li>
			<li><a target="_blank" href="http://www.seblod.com/resources/tutorials"><span class="badge badge-info">2. <?php echo $texts[5]['str']; ?></span></a></li>
			<li><a target="_blank" href="http://www.seblod.com/resources/manuals"><span class="badge badge-info">3. <?php echo $texts[6]['str']; ?></span></a></li>
			<li><a target="_blank" href="http://www.seblod.com/products"><span class="badge badge-info">4. <?php echo $texts[7]['str']; ?></span></a></li>
		</ul>
		<legend><?php echo $texts[2]['str']; ?></legend>
		<table class="adminlist table table-striped" style="width:400px;">
			<tr>
		        <th width="40%"><?php echo $texts[8]['str']; ?></th>
				<th width="30%" class="center"><?php echo $texts[9]['str']; ?></th>
				<th width="30%" class="center"><?php echo $texts[10]['str']; ?></th>
		    </tr>
		    <?php if ( $php == 'warning') { ?>
			<tr>
				<td><?php echo $texts[11]['str']; ?></td>
				<td class="center"><span class="label label-success">5.3.1 +</span></td>
				<td class="center"><span class="label label-<?php echo $php; ?>"><?php echo PHP_VERSION; ?></span></td>
			</tr>
			<?php } ?>
		    <?php
			$php	=	array(
							array( 'property'=>'max_file_uploads', 'value'=>'50' ),
							array( 'property'=>'max_input_vars', 'value'=>'3000' )
						);
		    foreach ( $php as $p ) {
		    	$current	=	ini_get( $p['property'] );
		    	$status		=	( (int)$current < (int)$p['value'] ) ? 'warning' : 'success';
		    	?>
				<tr>
		        	<td><?php echo $p['property']; ?></td>
		        	<td class="center"><span class="label label-success"><?php echo $p['value']; ?></span></td>
		        	<td class="center"><span class="label label-<?php echo $status; ?>"><?php echo $current ?></span></td>
		    	</tr>
		    	<?php
		    }
		    ?>
		</table>
		<?php
	}
}
?>