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
						   3=>array( 'key'=>'LIB_CCK_INSTALLATION_LATEST_CHANGELOG', 'str'=>'Read Changelog' ),
						   4=>array( 'key'=>'LIB_CCK_INSTALLATION_SEBLOD_VIDEOS', 'str'=>'Watch Videos' ),
						   5=>array( 'key'=>'LIB_CCK_INSTALLATION_SEBLOD_TUTORIALS', 'str'=>'Get Tutorials' ),
						   6=>array( 'key'=>'LIB_CCK_INSTALLATION_SEBLOD_DOCUMENTATION', 'str'=>'Learn SEBLOD' ),
						   7=>array( 'key'=>'LIB_CCK_INSTALLATION_SEBLOD_MARKETPLACE', 'str'=>'Extend SEBLOD' ),
						   8=>array( 'key'=>'LIB_CCK_INSTALLATION_CHECK_DIRECTIVE', 'str'=>'Directive' ),
						   9=>array( 'key'=>'LIB_CCK_INSTALLATION_CHECK_RECOMMENDED', 'str'=>'Recommended' ),
						   10=>array( 'key'=>'LIB_CCK_INSTALLATION_CHECK_ACTUAL', 'str'=>'Actual' ),
						   11=>array( 'key'=>'LIB_CCK_INSTALLATION_PHP_VERSION', 'str'=>'PHP Version' ),
						   12=>array( 'key'=>'LIB_CCK_INSTALLATION_UNINSTALL_SEBLOD', 'str'=>'Uninstalling SEBLOD ?' ),
						   13=>array( 'key'=>'LIB_CCK_INSTALLATION_UNINSTALL_SEBLOD_SQL', 'str'=>'In case you need to uninstall SEBLOD, the system will ' ),
						   14=>array( 'key'=>'LIB_CCK_INSTALLATION_UNINSTALL_SEBLOD_SQL_INFO', 'str'=>'It can be changed: <em>SEBLOD 3.x > Options > Component > While uninstalling...</em>' ),
						   15=>array( 'key'=>'LIB_CCK_INSTALLATION_UNINSTALL_SEBLOD_SQL_INFO2', 'str'=>'Please note that for now, no table will be restored automatically if you choose to install SEBLOD again. You\'ll get a fresh install of SEBLOD, and be able to restore manually, if needed.' ),
						   16=>array( 'key'=>'LIB_CCK_INSTALLATION_UNINSTALL_SEBLOD_SQL_BACKUP', 'str'=>'Backup SQL Tables' ),
						   17=>array( 'key'=>'LIB_CCK_INSTALLATION_UNINSTALL_SEBLOD_SQL_DROP', 'str'=>'Drop SQL Tables' )
						   
					);
		$lang->load( 'com_cck', JPATH_ADMINISTRATOR );

		foreach ( $texts as $text ) {
			if ( $lang->hasKey( $text['key'] ) == 1 ) {
				$text['str']	=	JText::_( $text['key'] );
			}
		}
		$php	=	( version_compare( PHP_VERSION, '5.3', 'lt' ) ) ? 'warning' : 'success';
		?>
		<legend><?php echo $texts[1]['str']; ?></legend>
		<p>
		<a target="_blank" href="http://www.seblod.com/products" class="btn btn-small btn-success"><?php echo $texts[7]['str']; ?></a>
		<a target="_blank" href="http://www.seblod.com/resources/manuals" class="btn btn-small btn-primary"><?php echo $texts[6]['str']; ?></a>
		<a target="_blank" href="http://www.seblod.com/changelogs" class="btn btn-small"><?php echo $texts[3]['str']; ?></a>
		<a target="_blank" href="http://www.seblod.com/resources/videos" class="btn btn-small"><?php echo $texts[4]['str']; ?></a>
		</p><br />
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
				<td class="center"><span class="badge badge-success">5.3.1 +</span></td>
				<td class="center"><span class="badge badge-<?php echo $php; ?>"><?php echo PHP_VERSION; ?></span></td>
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
		        	<td class="center"><span class="badge badge-success"><?php echo $p['value']; ?></span></td>
		        	<td class="center"><span class="badge badge-<?php echo $status; ?>"><?php echo $current ?></span></td>
		    	</tr>
		    	<?php
		    }
		    ?>
		</table>
		<legend><?php echo $texts[12]['str']; ?></legend>
		<?php
		if ( JCck::getConfig_Param( 'uninstall_sql' ) ) {
			$badge	=	'warning';
			$info	=	'';
			$text	=	$texts[17]['str'];
		} else {
			$badge	=	'success';
			$info	=	'<br />'.$texts[15]['str'];
			$text	=	$texts[16]['str'];
		}
		
		echo $texts[13]['str'].'<span class="badge badge-'.$badge.'">'.$text.'</span>';
		echo '<p>'.$texts[14]['str'].$info.'</p><br />';
	}
}
?>