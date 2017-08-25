<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Plugin
class plgCCK_FieldEmail extends JCckPluginField
{
	protected static $type		=	'email';
	protected static $path;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// onCCK_FieldConstruct
	public function onCCK_FieldConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		parent::g_onCCK_FieldConstruct( $data );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_FieldPrepareContent
	public function onCCK_FieldPrepareContent( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		parent::g_onCCK_FieldPrepareContent( $field, $config );
		
		$field->value	=	$value;
	}
	
	// onCCK_FieldPrepareForm
	public function onCCK_FieldPrepareForm( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		self::$path	=	parent::g_getPath( self::$type.'/' );
		parent::g_onCCK_FieldPrepareForm( $field, $config );
		
		// Init
		if ( count( $inherit ) ) {
			$id		=	( isset( $inherit['id'] ) && $inherit['id'] != '' ) ? $inherit['id'] : $field->name;
			$name	=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
		} else {
			$id		=	$field->name;
			$name	=	$field->name;
		}
		$value		=	( $value != '' ) ? $value : $field->defaultvalue;
		$value		=	( $value != ' ' ) ? $value : '';
		$value		=	str_replace(array( '"','\\' ), '', $value );
		$value		=	htmlspecialchars( $value, ENT_COMPAT, 'UTF-8' );
		
		// Validate
		$validate	=	'';
		if ( $config['doValidation'] > 1 ) {
			plgCCK_Field_ValidationRequired::onCCK_Field_ValidationPrepareForm( $field, $id, $config );
			parent::g_onCCK_FieldPrepareForm_Validation( $field, $id, $config );
			$validate	=	( count( $field->validate ) ) ? ' validate['.implode( ',', $field->validate ).']' : '';
		}
		
		// Prepare
		$class	=	'inputbox text'.$validate . ( $field->css ? ' '.$field->css : '' );
		$maxlen	=	( $field->maxlength > 0 ) ? ' maxlength="'.$field->maxlength.'"' : '';
		$attr	=	'class="'.$class.'" size="'.$field->size.'"'.$maxlen . ( $field->attributes ? ' '.$field->attributes : '' );
		$form	=	'<input type="text" id="'.$id.'" name="'.$name.'" value="'.$value.'" '.$attr.' />';
		
		// Set
		if ( ! $field->variation ) {
			$field->form	=	$form;
			if ( $field->script ) {
				parent::g_addScriptDeclaration( $field->script );
			}
		} else {
			parent::g_getDisplayVariation( $field, $field->variation, $value, $value, $form, $id, $name, '<input', '', '', $config );
		}
		$field->value	=	$value;
		
		// Return
		if ( $return === true ) {
			return $field;
		}
	}
	
	// onCCK_FieldPrepareSearch
	public function onCCK_FieldPrepareSearch( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		
		// Prepare
		self::onCCK_FieldPrepareForm( $field, $value, $config, $inherit, $return );
		
		// Return
		if ( $return === true ) {
			return $field;
		}
	}
	
	// onCCK_FieldPrepareStore
	public function onCCK_FieldPrepareStore( &$field, $value = '', &$config = array(), $inherit = array(), $return = false )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		
		// Init
		if ( count( $inherit ) ) {
			$name	=	( isset( $inherit['name'] ) && $inherit['name'] != '' )	? $inherit['name'] : $field->name;
		} else {
			$name	=	$field->name;
		}
		$options2	=	JCckDev::fromJSON( $field->options2 );
		$siteName	=	JFactory::getConfig()->get( 'sitename' );
		$valid		=	0;
		$send		=	( isset( $options2['send'] ) && $field->state != 'disabled' ) ? $options2['send'] : 0;
		$send_field	=	( isset( $options2['send_field'] ) && strlen( $options2['send_field'] ) > 0 ) ? $options2['send_field'] : 0;
		$isNew		=	( $config['pk'] ) ? 0 : 1;
		$sender		=	0;
		switch ( $send ) {
			case 0:
				$sender	=	0;
				break;
			case 1:
				if ( !$config['pk'] ) {
					$sender	=	1;
				}
				break;
			case 2:
				if ( $config['pk'] ) {
					$sender	=	1;
				}
				break;
			case 3:
				$sender	=	1;
				break;
		}
		$subject	=	( isset( $options2['subject'] ) && $options2['subject'] ) ? $options2['subject'] : $siteName . '::' . JText::_( 'COM_CCK_EMAIL_GENERIC_SUBJECT' );
		$message	=	( isset( $options2['message'] ) && $options2['message'] ) ? htmlspecialchars_decode($options2['message']) : JText::sprintf( 'COM_CCK_EMAIL_GENERIC_MESSAGE', $siteName );
		$message	=	( strlen( $options2['message'] ) > 0 ) ? htmlspecialchars_decode($options2['message']) : JText::sprintf( 'COM_CCK_EMAIL_GENERIC_MESSAGE', $siteName );
		$new_message	=	( strlen( $options2['message_field'] ) > 0 ) ? $options2['message_field'] : '';

		$dest				=	array();
		$from				=	( isset( $options2['from'] ) ) ? $options2['from'] : 0;
		$from_param			=	( isset( $options2['from_param'] ) ) ? $options2['from_param'] : '';
		$from_name			=	( isset( $options2['from_name'] ) ) ? $options2['from_name'] : 0;
		$from_name_param	=	( isset( $options2['from_name_param'] ) ) ? $options2['from_name_param'] : '';
		$cc					=	( isset( $options2['cc'] ) ) ? $options2['cc'] : 0;
		$cc_param			=	( isset( $options2['cc_param'] ) ) ? $options2['cc_param'] : '';
		$bcc				=	( isset( $options2['bcc'] ) ) ? $options2['bcc'] : 0;
		$bcc_param			=	( isset( $options2['bcc_param'] ) ) ? $options2['bcc_param'] : '';
		$moredest			=	( isset( $options2['to_field'] ) ) ? $options2['to_field'] : '';
		$send_attach		=	( isset( $options2['send_attachment_field'] ) && strlen( $options2['send_attachment_field'] ) > 0 ) ? $options2['send_attachment_field'] : 1;
		$moreattach			=	( isset( $options2['attachment_field'] ) && strlen( $options2['attachment_field'] ) > 0 ) ? $options2['attachment_field'] : '';
		
		// Prepare
		if ( isset( $options2['to'] ) && $options2['to'] != '' ) {
			$to		=	self::_split( $options2['to'] );
			$dest	=	array_merge( $dest, $to );
			$valid	=	1;
		}
		if ( $moredest ) {
			$valid	=	1;
		}
		if ( isset( $options2['to_admin'] ) && $options2['to_admin'] != '' ) {
			$to_admin	=	( count( $options2['to_admin'] ) ) ? implode( ',', $options2['to_admin'] ) : $options2['to_admin'];
			if ( strpos( $to_admin, ',' ) !== false ) {
				$recips = explode( ',', $to_admin );
				foreach ( $recips as $recip ) {
					$recip_mail = JCckDatabase::loadResult( 'SELECT email FROM #__users WHERE block=0 AND id='.$recip );
					if ( $recip_mail ) {
						$dest[]	=	$recip_mail;
						$valid	=	1;
					}
				}
			} else {
				$recip_mail = JCckDatabase::loadResult( 'SELECT email FROM #__users WHERE block=0 AND id='.$to_admin );
				if ( $recip_mail ) {
					$dest[]	=	$recip_mail;
					$valid	=	1;
				}
			}
		}
		if ( $value ) {
			// if () TODO check multiple
			$m_value		=	self::_split( $value );
			$m_value_size	=	count( $m_value );
			if ( $m_value_size > 1 ) {
				for ( $i = 0; $i < $m_value_size; $i++ )
					$dest[]	= 	$m_value[$i];
			} else {
				$dest[]	= 	$value;
			}
			$valid	=	1;
		}
		
		// Validate
		parent::g_onCCK_FieldPrepareStore_Validation( $field, $name, $value, $config );
		
		// Add Process
		if ( ( $sender || $send_field ) && $valid ) {
			parent::g_addProcess( 'afterStore', self::$type, $config, array( 'isNew'=>$isNew, 'sender'=>$sender, 'send_field'=>$send_field, 'name'=>$name, 'valid'=>$valid, 'subject'=>$subject, 'message'=>$message, 'new_message'=>$new_message, 'dest'=>$dest, 'from'=>$from, 'from_param'=>$from_param,  'from_name'=>$from_name, 'from_name_param'=>$from_name_param, 'cc'=>$cc, 'cc_param'=>$cc_param, 'bcc'=>$bcc, 'bcc_param'=>$bcc_param, 'moredest'=>$moredest, 'send_attach'=>$send_attach, 'moreattach'=>$moreattach, 'format'=>@(string)$options2['format'] ) );
		}
		
		// Set or Return
		if ( $return === true ) {
			return $value;
		}
		$field->value	=	$value;
		parent::g_onCCK_FieldPrepareStore( $field, $name, $value, $config );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Render
	
	// onCCK_FieldRenderContent
	public static function onCCK_FieldRenderContent( $field, &$config = array() )
	{
		return parent::g_onCCK_FieldRenderContent( $field );
	}
	
	// onCCK_FieldRenderForm
	public static function onCCK_FieldRenderForm( $field, &$config = array() )
	{		
		return parent::g_onCCK_FieldRenderForm( $field );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Special Events
	
	// onCCK_FieldAfterStore
	public static function onCCK_FieldAfterStore( $process, &$fields, &$storages, &$config = array() )
	{
		$isNew		=	$process['isNew'];
		$sender		=	$process['sender'];
		$send_field	=	$process['send_field'];

		if ( $send_field ) {
			$sender	=	0;
			if ( isset( $fields[$send_field]->value ) ){
				switch ( $fields[$send_field]->value ) {
					case 0:
						$sender	=	0;
						break;
					case 1:
						if ( $isNew ) {
							$sender	=	1;
						}
						break;
					case 2:
						if ( !$isNew ) {
							$sender	=	1;
						}
						break;
					case 3:
						$sender	=	1;
						break;
				}		
			}
		}
		if ( !$sender ) {
			return;
		}

		$dest	=	$process['dest'];
		if ( $process['moredest'] ) {
			$more	=	self::_split( $process['moredest'] );
			foreach ( $more as $mor ) {
				if ( isset( $fields[$mor]->value ) && $fields[$mor]->value ) {
					$more_dest	=	self::_split( $fields[$mor]->value );
					$dest		=	array_merge( $dest, $more_dest );
				}
			}
		}
		$n	=	count( array_filter( $dest ) );
		if ( $n ) {	
			$config2		=	JFactory::getConfig();
			$cfg_MailFrom	=	$config2->get( 'mailfrom' );
			$cfg_FromName	=	$config2->get( 'fromname' );
			if ( $cfg_MailFrom != '' && $cfg_FromName != '') {
				$mailFrom	=	$cfg_MailFrom;
				$fromName	=	$cfg_FromName;
			}
			
			$subject	=	$process['subject'];
			$new_message	=	( $process['new_message'] != '' ) ?  $fields[$process['new_message']]->value : '';
			$body		=	( strlen( $new_message ) > 0 ) ?  urldecode( $new_message ) : $process['message'];
			
			$subject	=	str_replace( '[id]', $config['id'], $subject );
			$subject	=	str_replace( '[pk]', $config['pk'], $subject );
			$subject	=	str_replace( '[sitename]', $config2->get( 'sitename' ), $subject );
			$subject	=	str_replace( '[siteurl]', JUri::base(), $subject );
			
			// J(translate) for subject
			if ( $subject != '' && strpos( $subject, 'J(' ) !== false ) {
				$matches	=	'';
				$search		=	'#J\((.*)\)#U';
				preg_match_all( $search, $subject, $matches );
				if ( count( $matches[1] ) ) {
					foreach ( $matches[1] as $text ) {
						$subject	=	str_replace( 'J('.$text.')', JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $text ) ) ), $subject );
					}
				}
			}
			
			if ( isset( $config['registration_activation'] ) ) {
				$body		=	str_replace( '[activation]', JUri::root().'index.php?option=com_users&task=registration.activate&token='.$config['registration_activation'], $body );
				$body		=	str_replace( '[username]', $fields['username']->value, $body );
				$subject	=	str_replace( '[username]', $fields['username']->value, $subject );
			}

			// {del fieldname}{/del}
			if ( $body != '' && strpos( $body, '{del' ) !== false ) {
				$dels	=	NULL;
				$body = str_replace( "\n", "", $body );
				preg_match_all( '#\{del ([^\{]*)\}([^\{]*)\{\/del\}#', $body, $dels );
				for ( $i = 0, $n = count( $dels[1] ); $i <= $n; $i++ ) {
					$match	=	str_replace( '#', '' ,$dels[1][$i] );
					if ( isset( $fields[$match]->value ) && trim( $fields[$match]->value ) ){
						$body	=	str_replace( $dels[0][$i], $dels[2][$i], $body );
					} else {
						$body	=	str_replace( $dels[0][$i], '', $body );
					}
				}
			}

			// #fieldnames#
			$matches	=	NULL;
			preg_match_all( '#\#([a-zA-Z0-9_]*)\##U', $body, $matches );
			if ( count( $matches[1] ) ) {
				foreach ( $matches[1] as $match ) {
					if ( trim( $match ) && isset( $fields[$match]->text ) && trim( $fields[$match]->text != '' ) ) {
						$body	=	str_replace( '#'.$match.'#', $fields[$match]->text, $body );
					} else {
						$body	=	( trim( $match ) && isset( $fields[$match]->value ) && trim( $fields[$match]->value ) ) ? str_replace( '#'.$match.'#', $fields[$match]->value, $body ) : str_replace( '#'.$match.'#', '', $body );
					}
				}
			}
			$matches	=	NULL;
			preg_match_all( '#\#([a-zA-Z0-9_]*)\##U', $subject, $matches );
			if ( count( $matches[1] ) ) {
				foreach ( $matches[1] as $match ) {
					if ( trim( $match ) && isset( $fields[$match]->text ) && trim( $fields[$match]->text ) != '' ) {
						$subject	=	str_replace( '#'.$match.'#', $fields[$match]->text, $subject );
					} else {
						$subject	=	( trim( $match ) && isset( $fields[$match]->value ) && trim( $fields[$match]->value ) != '' ) ? str_replace( '#'.$match.'#', $fields[$match]->value, $subject ) : str_replace( '#'.$match.'#', '', $subject );
					}
				}
			}
			
			// $cck->getAttr('fieldname');
			if ( $body != '' && strpos( $body, '$cck->get' ) !== false ) {
				$matches	=	'';
				$search		=	'#\$cck\->get([a-zA-Z0-9_]*)\( ?\'([a-zA-Z0-9_]*)\' ?\)(;)?#';
				preg_match_all( $search, $body, $matches );
				if ( count( $matches[1] ) ) {
					for ( $i = 0, $n = count( $matches[1] ); $i <= $n; $i++ ) {
						$attr	=	strtolower( $matches[1][$i] );
						$match	=	$matches[2][$i];
						if ( isset( $fields[$match]->$attr ) && trim( $fields[$match]->$attr ) != '' ){
							$body	=	str_replace( $matches[0][$i], $fields[$match]->$attr, $body );
						} else {
							$body	=	str_replace( $matches[0][$i], '', $body );
						}
					}
				}
			}
			
			// J(translate)
			if ( $body != '' && strpos( $body, 'J(' ) !== false ) {
				$matches	=	'';
				$search		=	'#J\((.*)\)#U';
				preg_match_all( $search, $body, $matches );
				if ( count( $matches[1] ) ) {
					foreach ( $matches[1] as $text ) {
						$body	=	str_replace( 'J('.$text.')', JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( $text ) ) ), $body );
					}
				}
			}
			
			$body		=	str_replace( '[id]', $config['id'], $body );
			$body		=	str_replace( '[pk]', $config['pk'], $body );
			$body		=	str_replace( '[sitename]', $config2->get( 'sitename' ), $body );
			$body		=	str_replace( '[siteurl]', JUri::base(), $body );

			if ( $body != '' && strpos( $body, '$user->' ) !== false ) {
				$user			=	JCck::getUser();
				$matches		=	'';
				$search			=	'#\$user\->([a-zA-Z0-9_]*)#';
				preg_match_all( $search, $body, $matches );
				if ( count( $matches[1] ) ) {
					foreach ( $matches[1] as $k=>$v ) {
						$body	=	str_replace( $matches[0][$k], $user->$v, $body );
					}
				}
			}

			// [date(.*)]
			if ( $body != '' && strpos( $body, '[date' ) !== false ) {
				$matches	=	NULL;
				preg_match_all( '#\[date(.*)\]#U', $body, $matches );
				if ( count( $matches[1] ) ) {
					foreach ( $matches[1] as $match ) {
						$date		=	date( $match );
						$body		=	str_replace( '[date'.$match.']', $date, $body );
					}
				}
			}

			// [fields]
			if ( strpos( $body, '[fields]' ) !== false ) {
				$bodyF	=	NULL;
				if ( count( $fields ) ) {
					foreach ( $fields as $field ) {
						$fieldName	=	$field->name;
						if ( ! ( $field->type == 'password' && $field->value == 'XXXX' ) && isset( $field->value ) && trim( $field->value ) != '' && ( $field->variation != 'hidden' ) ) {
							$valF	=	( isset( $field->text ) && trim( $field->text ) != '' ) ? trim( $field->text ) : trim( $field->value );
							$bodyF	.=	'- '.$field->label.' : '.$valF.'<br /><br />';
						}
					}
				}
				$body	=	( strpos( $body, '[fields]' ) !== false ) ? str_replace( '[fields]', $bodyF, $body ) : $body.substr( $bodyF, 0, -12 );
			}

			$cc		=	$process['cc'];
			$from	=	$process['from'];

			switch ( $from ) {
				case 3:
					$from		=	$fields[$process['from_param']]->value;
					$fromName	=	$from;
					break;
				case 1:
					$from		=	$process['from_param'];
					break;
				default:
					$from		=	$mailFrom;
					break;
			}
			switch ( $process['from_name'] ) {
				case 3:
					$fromName		=	$fields[$process['from_name_param']]->value;
					break;
				case 1:
					$fromName		=	$process['from_name_param'];
					break;
				default:
					$fromName		=	$fromName;
					break;
			}
			switch ( $cc ) {
				case 3:
					$cc		=	self::_split( $fields[$process['cc_param']]->value );
					break;
				case 1:
					$cc		=	self::_split( $process['cc_param'] );
					break;
				default:
					$cc		=	NULL;
					break;
			}
			if ( ( is_string( $cc ) && empty( $cc ) ) || is_array( $cc ) && empty( $cc[0] ) ) {
				$cc	=	array();
			}
			$bcc	=	$process['bcc'];
			switch ( $bcc ) {
				case 3:
					$bcc		=	self::_split( $fields[$process['bcc_param']]->value );
					break;
				case 1:
					$bcc		=	self::_split( $process['bcc_param'] );
					break;
				default:
					$bcc	=	NULL;
					break;
			}
			if ( ( is_string( $bcc ) && empty( $bcc ) ) || is_array( $bcc ) && empty( $bcc[0] ) ) {
				$bcc	=	array();
			}
			$send_attach	=	$process['send_attach'];
			if ( $send_attach != 1 && strlen( $process['send_attach'] ) > 1 ){
				if ( isset( $fields[$send_attach]->value ) )
					$send_attach	=	$fields[$send_attach]->value;				
			}
			$attach	=	NULL;
			if ( $send_attach && $process['moreattach'] ) {
				$attach	=	array();
				$more	=	self::_split( $process['moreattach'] );
				foreach ( $more as $mor ) {
					if ( isset( $fields[$mor]->attachment ) && $fields[$mor]->attachment != '' ) {
						$attach[]	=	JPATH_SITE.'/'.$fields[$mor]->attachment;
					} else if ( isset( $fields[$mor]->value ) && $fields[$mor]->value ) {
						$attach[]	=	JPATH_SITE.'/'.$fields[$mor]->value;
					}
				}
			}
			if ( $process['format'] == '0' ) {
				$format		=	false;
				$body		=	strip_tags( $body );
			} elseif ( $process['format'] == '2' ) {
				$format		=	false;
			} else {
				$format		=	true;
			}
			
			JFactory::getMailer()->sendMail( $from, $fromName, $dest, $subject, $body, $format, $cc, $bcc, $attach );
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff & Script
	
	// _split
	protected static function _split( $string )
	{
		$string		=	str_replace( array( ' ', "\r" ), '', $string );
		if ( strpos( $string, ',' ) !== false ) {
			$tab	=	explode( ',', $string );
		} else if ( strpos( $string, ';' ) !== false ) {
			$tab	=	explode( ';', $string );
		} else {
			$tab	=	explode( "\n", $string );
		}
		
		return $tab;
	}
}
?>