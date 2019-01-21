<?php
defined( '_JEXEC' ) or die;

if ( !isset( $options ) ) {
	return;	
}
$opts	=	$options->toArray();

// Let's make sure we have the suitable Content Type
if ( !( isset( $opts['content_types'] ) && $opts['content_types'] != '' ) ) {
	return;
}
if ( $config['type'] != $opts['content_types'] ) {
	return;
}
if ( !$config['isNew'] ) {
	return;
}

$type			=	$opts['type']; /* '7' || '2,7' || 2,3,6,7 */
$groups			=	explode( ',', $type );
$types			=	array(
						'2'=>'registered',
						'3'=>'author',
						'6'=>'manager',
						'7'=>'administrator'
				);
$users			=	array();

if ( !count( $groups ) ) {
	return;
}
require_once JPATH_ADMINISTRATOR.'/components/com_cck/tables/site.php';
JLoader::register( 'JUser', JPATH_PLATFORM.'/joomla/user/user.php' );

JFactory::getApplication()->input->set( 'type', $type );

foreach ( $groups as $i=>$g ) {
	$account	=	$types[$g];

	if ( isset( $opts[$account] ) && $opts[$account]['email'] ) {
		if ( count( $opts[$account] ) ) {
			foreach ( $opts[$account] as $k=>$v ) {
				if ( !( $k == 'bridge' || $k == 'force_password' || $k == 'set_author' ) ) {
					if ( $v != '' ) {
						$opts[$account][$k]	=	@$fields[$v]->value;
					}	
				}
			}
		}
		if ( $opts[$account]['email'] == '' ) {
			continue;
		}
		if ( $opts[$account]['name'] == '' ) {
			if ( !( $opts[$account]['first_name'] && $opts[$account]['last_name'] ) ) {
				$opts[$account]['name']	=	$opts[$account]['email'];
			} else {
				$opts[$account]['name']	=	$opts[$account]['first_name'].' '.$opts[$account]['last_name'];
			}
		}
		if ( $opts[$account]['password'] == '' ) {
			$opts[$account]['password']	=	JUserHelper::genRandomPassword( 20 );
		}
		$opts[$account]['password2']	=	$opts[$account]['password'];

		if ( $opts[$account]['username'] == '' ) {
			$opts[$account]['username']	=	$opts[$account]['email'];
		}
		
		$data	=	$opts[$account];
		$data2	=	array_diff_key( $opts[$account], array(
														'email'=>'',
														'name'=>'',
														'password'=>'',
														'password2'=>'',
														'username'=>'',
														'bridge'=>'',
														'force_password'=>'',
														'set_author'=>''
													 ) );

		unset( $data['first_name'] );
		unset( $data['last_name'] );

		if ( count( $data2 ) ) {
			foreach ( $data2 as $k=>$v ) {
				if ( $data2[$k] == '' ) {
					unset( $data2[$k] );
				}
			}
		}
		$data['bridge']			=	(int)$opts[$account]['bridge'];
		$data['force_password']	=	(int)$opts[$account]['force_password'];
		$data['set_author']		=	(int)$opts[$account]['set_author'];
		$data['more']			=	$data2;
		
		$users[((string)$g)]	=	$data;
	}
}

if ( count( $users ) ) {
	$config['storages']['#__cck_core_sites']['groups']	=	json_encode( $users );
}
?>