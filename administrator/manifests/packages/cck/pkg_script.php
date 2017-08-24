<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: pkg_script.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Script
class pkg_cckInstallerScript
{
	// install
	function install( $parent )
	{
		JFactory::getLanguage()->load( 'com_cck.sys', JPATH_ADMINISTRATOR, null, true );
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
		if ( !defined( 'DS' ) ) {
			define( 'DS', DIRECTORY_SEPARATOR );
		}
		set_time_limit( 0 );
	}

	// postflight
	function postflight( $type, $parent )
	{
		if ( JCck::on( '3.8' ) ) {
			if ( $type == 'install' || $type == 'update' && version_compare( JCck::getConfig_Param( 'initial_version', '3' ), '3.13.0', '>=' ) ) {
				$db			=	JFactory::getDbo();
				$query		=	$db->getQuery( true );

				$query->select( $db->quoteName( array( 'extension_id' ) ) )
					  ->from( $db->quoteName( '#__extensions' ) )
					  ->where( $db->quoteName( 'type' ) . ' = ' . $db->quote( 'module' ) )
					  ->where( $db->quoteName( 'element' ) . ' = ' . $db->quote( 'mod_cck_menu' ) );
				$db->setQuery( $query );
				$module_id	=	$db->loadResult();
				
				$installer  =   JInstaller::getInstance();
				
				$module     =   JTable::getInstance( 'Extension' );
				$module->load( $module_id );
				
				if ( $module->type == 'module' ) {
					$installer->uninstall( $module->type, $module_id );

					if ( $type == 'install' ) {
						$new_module	=	JTable::getInstance( 'Module', 'JTable' );
					
						if ( $new_module->save( array(
													'access'=>3,
													'client_id'=>1,
													'language'=>'*',
													'module'=>'mod_menu',
													'ordering'=>2,
													'params'=>'{"menutype":"*","preset":"cck","check":"1","shownew":"1","showhelp":"1"}',
													'position'=>'menu',
													'published'=>1,
													'showtitle'=>0,
													'title'=>'Admin Menu - SEBLOD'
												   ) ) ) {
							try {
								$query	=	'INSERT INTO #__modules_menu (moduleid, menuid) VALUES ('.$new_module->id.', 0)';
								$db->setQuery( $query );
								$db->execute();
							} catch ( Exception $e ) {
								// Do nothing
							}
						}
					}
				}
			}
		}

		$php	=	( version_compare( PHP_VERSION, '5.3', 'lt' ) ) ? 'warning' : 'success';
		?>
		<style type="text/css">
			.hero-unit{border:1px solid #dedede; -webkit-border-radius: 3px; -moz-border-radius: 3px; border-radius: 3px;} .table{margin-bottom:30px;}
    	</style>
		<div class="hero-unit">
			<a href="index.php?option=com_cck" style="display:block;">
				<svg viewBox="0 0 350 60" xmlns="http://www.w3.org/2000/svg" width="350" height="60"><path d="M259.619 58.82c-.014-.043-1.238-3.855-1.516-9.548-.578.577-1.376.933-2.257.933-1.768 0-3.2-1.431-3.2-3.198s1.433-3.2 3.2-3.2c.901 0 1.716.377 2.298.979.219-2.874.795-6 1.382-9.074 2.449.469 4.933.932 7.313 1.217-.125.406-.193.838-.193 1.285 0 2.427 1.974 4.399 4.399 4.399s4.398-1.973 4.398-4.399c0-.41-.06-.807-.166-1.184 4.211-.355 6.978-1.143 7.748-1.386l.017-.638-.017-.627-.172.06c-.039.014-3.686 1.245-9.544 1.515.578.578.935 1.379.935 2.26 0 1.767-1.433 3.2-3.199 3.2-1.768 0-3.199-1.434-3.199-3.2 0-.904.377-1.721.98-2.303-2.874-.215-6-.79-9.073-1.378.469-2.447.931-4.931 1.216-7.311.406.123.836.191 1.283.191 2.425 0 4.397-1.974 4.397-4.399s-1.973-4.399-4.397-4.399c-.41 0-.805.061-1.182.166-.356-4.213-1.143-6.981-1.383-7.752l-.64-.016-.626.016.058.172c.016.041 1.245 3.688 1.515 9.544.58-.574 1.377-.929 2.258-.929 1.767 0 3.198 1.431 3.198 3.198s-1.432 3.198-3.198 3.198c-.904 0-1.719-.375-2.299-.976-.218 2.874-.792 5.999-1.38 9.07-2.448-.469-4.933-.932-7.314-1.217.123-.406.191-.836.191-1.28 0-2.428-1.975-4.4-4.399-4.4-2.425 0-4.399 1.972-4.399 4.4 0 .409.063.804.168 1.183-4.14.359-6.975 1.145-7.749 1.38l-.016.635.016.624.163-.054c.041-.014 3.855-1.239 9.549-1.515-.574-.578-.93-1.375-.93-2.253 0-1.768 1.431-3.201 3.198-3.201 1.768 0 3.2 1.433 3.2 3.201 0 .899-.375 1.712-.974 2.294 2.871.219 5.998.795 9.069 1.382-.469 2.449-.932 4.934-1.217 7.316-.407-.125-.838-.192-1.284-.192-2.428 0-4.399 1.974-4.399 4.398 0 2.426 1.972 4.4 4.399 4.4.412 0 .809-.063 1.188-.168.36 4.137 1.146 6.971 1.379 7.745l.636.016.623-.016-.053-.163z" fill="#fff"/><path d="M283.042 34.374c-.33-12.756-10.604-23.029-23.358-23.361.235.754 1.03 3.533 1.388 7.768.377-.105.772-.166 1.182-.166 2.425 0 4.397 1.974 4.397 4.399s-1.973 4.399-4.397 4.399c-.447 0-.877-.068-1.283-.191-.285 2.38-.747 4.864-1.216 7.311 3.073.588 6.199 1.163 9.073 1.378-.603.582-.98 1.398-.98 2.303 0 1.767 1.432 3.2 3.199 3.2s3.199-1.434 3.199-3.2c0-.881-.356-1.682-.935-2.26 5.858-.27 9.505-1.501 9.544-1.515l.188-.064zm-26.008 16.864c-.379.105-.775.168-1.188.168-2.428 0-4.399-1.974-4.399-4.4 0-2.425 1.972-4.398 4.399-4.398.446 0 .877.067 1.284.192.285-2.382.748-4.867 1.217-7.316-3.071-.587-6.198-1.163-9.069-1.382.599-.582.974-1.395.974-2.294 0-1.768-1.433-3.201-3.2-3.201-1.768 0-3.198 1.433-3.198 3.201 0 .878.356 1.675.93 2.253-5.694.275-9.508 1.501-9.549 1.515l-.18.059c.33 12.759 10.605 23.035 23.363 23.365-.228-.756-1.022-3.602-1.384-7.762zm90.324-37.431c-1.466 1.472-3.236 2.208-5.316 2.208-2.08 0-3.845-.736-5.304-2.208-1.451-1.464-2.179-3.239-2.179-5.325 0-2.066.734-3.829 2.198-5.295 1.459-1.456 3.221-2.187 5.284-2.187 2.08 0 3.851.73 5.316 2.187 1.465 1.459 2.197 3.224 2.197 5.295 0 2.078-.733 3.854-2.197 5.325zm-9.874-9.884c-1.253 1.262-1.88 2.785-1.88 4.569 0 1.798.622 3.333 1.87 4.6 1.254 1.267 2.776 1.902 4.567 1.902 1.792 0 3.315-.635 4.568-1.902 1.254-1.267 1.883-2.802 1.883-4.6 0-1.785-.629-3.308-1.883-4.569-1.259-1.267-2.784-1.902-4.568-1.902-1.777 0-3.296.635-4.558 1.902zm4.415.42c1.022 0 1.772.1 2.249.297.854.354 1.277 1.05 1.277 2.088 0 .736-.268 1.279-.805 1.626-.283.184-.682.316-1.189.399.644.101 1.114.371 1.413.808.299.434.447.859.447 1.275v.601c0 .191.006.396.022.613.014.219.037.361.07.43l.051.102h-1.359l-.02-.082-.021-.092-.029-.264v-.652c0-.951-.26-1.58-.779-1.886-.305-.176-.843-.266-1.612-.266h-1.145v3.242h-1.453v-8.238h2.883zm1.57 1.298c-.368-.205-.954-.306-1.76-.306h-1.24v2.983h1.311c.614 0 1.077-.061 1.384-.184.566-.225.851-.654.851-1.289 0-.6-.184-1.002-.546-1.205z" fill="#a1a1a1"/><path d="M55.793 9.016c-7.423-5.332-16.627-8-27.616-8-8.175 0-14.502 1.498-18.991 4.485-4.485 2.986-6.165 6.114-6.165 11.282 0 8.602 5.399 13.725 23.123 15.057 9.536.665 15.439 1.55 17.609 2.155 4.157 1.172 7.336 3.499 7.336 6.994 0 2.985-2.16 10.013-19.895 10.013-9.055 0-17.658-2.542-25.54-7.381l-5.212 6.067c8.564 5.313 17.487 9.312 30.601 9.312 9.036 0 15.964-1.596 20.797-4.792 4.829-3.19 7.246-7.713 7.246-13.557 0-4.564-1.896-7.793-4.987-10.276-4.837-3.325-7.858-5.272-28.083-6.613-4.85-.307-14.96-.783-14.96-7.01 0-6.52 11.224-7.738 16.476-7.738 12.024 0 18.304 2.968 23.078 6.049l5.18-6.047zm59.281 41.986h-39.989v-12.996h24.993v-7.998h-24.993v-10.997h36.99v-7.998h-44.988v47.987h47.987v-7.998zm7.998-20.994v28.992h31.54c10.771 0 16.478-5.957 16.478-14.512 0-5.335-1.739-7.875-6.445-10.497 3.362-2.511 4.518-5.391 4.518-9.482 0-4.718-2.561-13.497-14.297-13.497h-31.793v7.998h31.849c4.04 0 6.176 1.812 6.176 5.499 0 3.686-2.15 5.498-6.176 5.498h-31.849zm7.998 20.994v-12.996h23.847c5.748 0 8.203 2.382 8.203 6.498 0 4.108-2.47 6.498-8.203 6.498h-23.847zm95.987 0h-39.988v-39.989h-7.998v47.987h47.986v-7.998zm71.98 0v-20.994h-7.997v28.992h24.952c20.078 0 23.035-17.72 23.035-24.056 0-6.971-2.688-23.932-23.035-23.932h-24.952v7.998h24.946c10.513 0 15.043 6.457 15.043 15.932 0 9.019-4.374 16.06-15.029 16.06h-16.963zm-23.759-13.973c.106.377.166.773.166 1.184 0 2.427-1.974 4.399-4.398 4.399-2.426 0-4.399-1.973-4.399-4.399 0-.447.068-.879.193-1.285-2.381-.285-4.864-.748-7.313-1.217-.587 3.074-1.163 6.2-1.382 9.074-.582-.602-1.397-.979-2.298-.979-1.768 0-3.2 1.434-3.2 3.2 0 1.768 1.433 3.198 3.2 3.198.881 0 1.679-.355 2.257-.933.277 5.693 1.502 9.505 1.516 9.548l.059.18c12.758-.33 23.032-10.605 23.364-23.361-.753.238-3.53 1.033-7.764 1.391zm-13.025-17.214c-.881 0-1.678.355-2.258.929-.269-5.856-1.499-9.503-1.515-9.544l-.063-.188c-12.759.33-23.033 10.605-23.363 23.363.756-.23 3.602-1.023 7.766-1.385-.106-.379-.168-.773-.168-1.183 0-2.428 1.975-4.4 4.399-4.4 2.425 0 4.399 1.972 4.399 4.4 0 .444-.068.874-.191 1.28 2.382.285 4.866.748 7.314 1.217.588-3.071 1.162-6.196 1.38-9.07.58.601 1.394.976 2.299.976 1.767 0 3.198-1.431 3.198-3.198s-1.432-3.198-3.198-3.198z" fill="#184d9d" /></svg>
			</a>
		</div>
		<legend><?php echo JText::_( 'LIB_CCK_INSTALLATION_LEGEND_GETTING_STARTED' ); ?></legend>
		<div style="margin-bottom:30px;">
			<a class="btn btn-success" href="https://www.seblod.com/store" target="_blank"><span class="icon-puzzle"></span> <?php echo JText::_( 'LIB_CCK_INSTALLATION_SEBLOD_MARKETPLACE' ); ?></a>
			<a class="btn btn-primary" href="https://www.seblod.com/resources/manuals" target="_blank"><span class="icon-book"></span> <?php echo JText::_( 'LIB_CCK_INSTALLATION_SEBLOD_DOCUMENTATION' ); ?></a>
			<a class="btn btn-info" href="https://www.seblod.com/resources/videos" target="_blank"><span class="icon-play-circle"></span> <?php echo JText::_( 'LIB_CCK_INSTALLATION_SEBLOD_VIDEOS' ); ?></a>
			<a class="btn" href="https://www.seblod.com/changelogs" target="_blank"><span class="icon-file-2"></span> <?php echo JText::_( 'LIB_CCK_INSTALLATION_LATEST_CHANGELOG' ); ?></a>
		</div>
		<legend><?php echo JText::_( 'LIB_CCK_INSTALLATION_LEGEND_RECOMMENDED_SETTINGS' ); ?></legend>
		<table class="table table-bordered">
			<thead>
				<tr>
			        <th class="span6"><?php echo JText::_( 'LIB_CCK_INSTALLATION_CHECK_DIRECTIVE' ); ?></th>
					<th class="span3 center"><?php echo JText::_( 'LIB_CCK_INSTALLATION_CHECK_RECOMMENDED' ); ?></th>
					<th class="span3 center"><?php echo JText::_( 'LIB_CCK_INSTALLATION_CHECK_ACTUAL' ); ?></th>
			    </tr>
		    </thead>
		    <tbody>
		    	<?php if ( $php == 'warning') { ?>
				<tr>
					<td><?php echo JText::_( 'LIB_CCK_INSTALLATION_PHP_VERSION' ); ?></td>
					<td class="center"><span class="badge badge-success">5.3.1+</span></td>
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
		        	<td><code><?php echo $p['property']; ?></code></td>
		        	<td class="center"><span class="badge badge-success"><?php echo $p['value']; ?></span></td>
		        	<td class="center"><span class="badge badge-<?php echo $status; ?>"><?php echo $current ?></span></td>
		    	</tr>
			    <?php } ?>
		    </tbody>
		</table>
		<legend><?php echo JText::_( 'LIB_CCK_INSTALLATION_UNINSTALL_SEBLOD' ); ?></legend>
		<?php
		if ( JCck::getConfig_Param( 'uninstall_sql' ) ) {
			$badge	=	'warning';
			$info	=	'';
			$text	=	JText::_( 'LIB_CCK_INSTALLATION_UNINSTALL_SEBLOD_SQL_DROP' );
		} else {
			$badge	=	'success';
			$info	=	'<br />'.JText::_( 'LIB_CCK_INSTALLATION_UNINSTALL_SEBLOD_SQL_INFO2' );
			$text	=	JText::_( 'LIB_CCK_INSTALLATION_UNINSTALL_SEBLOD_SQL_BACKUP' );
		}
		echo '<p>'.JText::_( 'LIB_CCK_INSTALLATION_UNINSTALL_SEBLOD_SQL' ).'<span class="label label-'.$badge.'">'.$text.'</span></p>';
		echo '<p>'.JText::_( 'LIB_CCK_INSTALLATION_UNINSTALL_SEBLOD_SQL_INFO' ).$info.'</p>';
		echo '<br />';
	}
}
?>