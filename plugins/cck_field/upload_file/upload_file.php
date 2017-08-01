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

jimport( 'joomla.filesystem.file' );

// Plugin
class plgCCK_FieldUpload_File extends JCckPluginField
{
	protected static $type		=	'upload_file';
	protected static $path;
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Construct
	
	// onCCK_FieldConstruct
	public function onCCK_FieldConstruct( $type, &$data = array() )
	{
		if ( self::$type != $type ) {
			return;
		}
		if ( $data['json']['options2']['path'][strlen($data['json']['options2']['path'])-1] != '/' ) {
			$data['json']['options2']['path']	.=	'/';
		}
		$data['json']['options2']['path']		=	trim( $data['json']['options2']['path'] );
		
		JCckDevHelper::createFolder( JPATH_SITE.'/'.$data['json']['options2']['path'] );
		
		parent::g_onCCK_FieldConstruct( $data );
	}
	
	// onCCK_FieldConstruct_SearchSearch
	public static function onCCK_FieldConstruct_SearchSearch( &$field, $style, $data = array(), &$config = array() )
	{
		if ( !isset( $config['construction']['match_mode'][self::$type] ) ) {
			$data['match_mode']	=	array(
										'none'=>JHtml::_( 'select.option', 'none', JText::_( 'COM_CCK_NONE' ) ),
										''=>JHtml::_( 'select.option', '', JText::_( 'COM_CCK_AUTO' ) )
									);

			$config['construction']['match_mode'][self::$type]	=	$data['match_mode'];
		} else {
			$data['match_mode']									=	$config['construction']['match_mode'][self::$type];
		}
		
		parent::onCCK_FieldConstruct_SearchSearch( $field, $style, $data, $config );
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Delete

	// onCCK_FieldDelete
	public function onCCK_FieldDelete( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}

		$value_json			=	JCckDev::fromJSON( $value );
		if ( $value == '' || isset( $value_json['file_location'] ) && $value_json['file_location'] == '' ) {
			return;
		}
		
		// Init
		$value_json			=	JCckDev::fromJSON( $value );
		$options2			=	JCckDev::fromJSON( $field->options2 );
		if ( is_array( $value_json ) && !empty( $value_json ) ) {
			$value			=	( trim($value_json['file_location'] ) == '' ) ? trim( $field->defaultvalue ) : trim( $value_json['file_location'] ) ;
			$file_name		=	( $value == '' ) ? '' : substr( strrchr( $value, '/' ), 1 );
		} else {
			$value			=	( trim($value) == '' ) ? trim($field->defaultvalue) : trim( $value ) ;
			$file_name		=	( $value == '' ) ? '' : substr( strrchr( $value, '/' ), 1 );
		}
		$file		=	$value;
		$path		=	@$options2['path'];
		if ( @$options2['storage_format'] ) {
			$path	.=	( @$options2['path_content'] ) ? $config['pk'].'/' : '';
			$file	=	$path.$value;
		}
		
		// Process
		if ( $file != '' && JFile::exists( JPATH_SITE.'/'.$file ) ) {
			$path		=	substr( $value, 0, strrpos( $value, '/' ) ).'/';

			if ( $options2['path_content'] ) {
				jimport( 'joomla.filesystem.folder' );
				if ( $path != '' && strpos( $path, $options2['path'] ) !== false && JFolder::exists( JPATH_SITE.'/'.$path ) ) {
					if ( JFolder::delete( JPATH_SITE.'/'.$path ) ) {
						return true;
					}
				}
			} else {
				if ( JFile::delete( JPATH_SITE.'/'.$file ) ) {
					return true;
				}
			}
		}

		return false;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Prepare
	
	// onCCK_FieldPrepareContent
	public function onCCK_FieldPrepareContent( &$field, $value = '', &$config = array(), $inherit = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}
		parent::g_onCCK_FieldPrepareContent( $field, $config );
		
		if ( isset( $inherit['parent'] ) ) {
			$collection	=	$inherit['parent'];
			$xi			=	$inherit['xi'];
			$link_more	=	'&collection='.$collection.'&xi='.$xi;
		} else {
			$collection	=	'';
			$xi			=	0;
			$link_more	=	'';
		}
		
		// Set
		$field->html		=	'';
		$value_json			=	JCckDev::fromJSON( $value );
		$options2			=	JCckDev::fromJSON( $field->options2 );
		if ( is_array( $value_json ) && !empty( $value_json ) ) {
			$value			=	( trim($value_json['file_location'] ) == '' ) ? trim( $field->defaultvalue ) : trim( $value_json['file_location'] ) ;
			$file_name		=	( $value == '' ) ? '' : substr( strrchr( $value, '/' ), 1 );
			$file_title		=	trim( $value_json['file_title'] );
			$file_title		=	htmlspecialchars( $file_title, ENT_QUOTES );
		} else {
			$value			=	( trim($value) == '' ) ? trim($field->defaultvalue) : trim( $value ) ;
			$file_name		=	( $value == '' ) ? '' : substr( strrchr( $value, '/' ), 1 );
			$file_title		=	'' ;
		}
		
		// Set More
		$file		=	$value;
		$path		=	@$options2['path'];
		if ( @$options2['storage_format'] ) {
			$path	.=	( @$options2['path_content'] ) ? $config['pk'].'/' : '';
			$file	=	$path.$value;
		}
		$field->file_folder		=	$path;
		if ( $value ) {
			$link_more			=	( ( $config['client'] == 'intro' /*|| $config['client'] == 'list' || $config['client'] == 'item'*/ ) ? '&client='.$config['client'] : '' ) . $link_more;
			$field->text		=	( $file_title != '' ) ? $file_title : ( ( @$options2['storage_format'] ) ? $value : substr( $value, strrpos( $value, '/' ) + 1 ) );
			$field->hits		=	self::_getHits( $config['id'], $field->name, $collection, $xi );
			$field->file_size	=	( file_exists( $file ) ) ? self::_formatBytes( filesize( $file ) ) : self::_formatBytes( 0 );
			$field->link		=	'index.php?option=com_cck&task=download'.$link_more.'&file='.$field->name.'&id='.$config['id'];
			$field->linked		=	true;
			$field->html		=	'<a href="'.$field->link.'" title="'.$file_title.'">'.$field->text.'</a>';
			$field->typo_target	=	'text';
		}
		$field->value			=	$value;
	}
	
	// onCCK_FieldPrepareDownload
	public function onCCK_FieldPrepareDownload( &$field, $value = '', &$config = array() )
	{
		if ( self::$type != $field->type ) {
			return;
		}

		// Prepare
		self::onCCK_FieldPrepareContent( $field, $value, $config );

		// Path Folder
		$f_opt2		=	JCckDev::fromJSON( $field->options2 );
		$file		=	'';
		if ( isset( $f_opt2['storage_format'] ) && $f_opt2['storage_format'] ) {
			$file	.=	$f_opt2['path'];
			$file	.=	( isset( $f_opt2['path_user'] ) && $f_opt2['path_user'] ) ? $config['author'].'/' : '';
			$file	.=	( isset( $f_opt2['path_content'] ) && $f_opt2['path_content'] ) ? $config['pk'].'/' : '';
		}
		$file		.=	$field->value;

		$field->filename	=	$file;
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
			$xk		=	( isset( $inherit['xk'] ) ) ? $inherit['xk'] : '';
		} else {
			$id		=	$field->name;
			$name	=	$field->name;
			$xk		=	'';
		}

		// Clear Value for assets
		if ( isset( $config['copyfrom_id'] ) && $config['copyfrom_id'] ) {
			$value	=	'';
		}

		// Validate
		$validate	=	'';
		if ( $config['doValidation'] > 1 ) {
			plgCCK_Field_ValidationRequired::onCCK_Field_ValidationPrepareForm( $field, $id, $config );
			$validate	=	( count( $field->validate ) ) ? ' validate['.implode( ',', $field->validate ).']' : '';
		}

		// Prepare
		$value			=	trim( $value );
		if ( $value == '' ) {
			$value	=	array( 'file_location' => '', 'file_title' => '' );
		} else {
			$value_json	=	JCckDev::fromJSON( $value );
			if ( is_array( $value_json ) && !empty( $value_json ) ) {
				$value_json['file_location']	=	trim( $value_json['file_location'] );
				$value	=	$value_json;
			} else {
				$value	=	array('file_location' => $value, 'file_title' => '' );
			}
		}
		$save_value				=	$value;
		$options2				=	JCckDev::fromJSON( $field->options2 );
		if ( $config['doTranslation'] ) {
			$title_label	=	trim( @$options2['title_label'] ) ? JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( @$options2['title_label'] ) ) ) : '';
		} else {
			$title_label	=	@$options2['title_label'];
		}
		$value2			=	( @$options2['storage_format'] ) ? $options2['path'] : ''; 
		if ( @$options2['storage_format'] && $config['pk'] ) {
			$value2		.=	( @$options2['path_content'] )	? $config['pk'].'/' : '';
		}
		$value2			.= 	( $value['file_location'] != '' ) ? $value['file_location'] : '';
		$location		=	( $value['file_location'] != '' ) ? $value['file_location'] : $field->defaultvalue;
		$value3			=	( @$options2['storage_format'] ) ? substr( $value2, strrpos( $value2, '/' ) + 1 ) : ( $value2 ? $value2 : $options2['path'] );
		if ( !@$options2['storage_format'] && $config['pk'] ) {
			if ( strpos( $value2, '/'.$config['pk'].'/' ) === false && $options2['path_content'] ) {
				$value2	=	$config['pk'].'/'.$value2;
			}
			if ( strpos( $value2, $options2['path'] ) === false ) {
				$value2	=	$options2['path'].$value2;
			}
			if ( strpos( $location, '/'.$config['pk'].'/' ) === false && $options2['path_content'] ) {
				$location	=	$config['pk'].'/'.$location;
			}
			if ( strpos( $location, $options2['path'] ) === false ) {
				$location	=	$options2['path'].$location;
			}
		}
		$fold_3			=	( @$options2['storage_format'] && $config['pk'] ) ? ' ( '.substr( $value2, 0, strrpos( $value2, '/' ) + 1 ).' )': '';
		$chkbox			=	'';
		$onchange		=	'';
		$preview		=	'';
		$form_more2		=	'';
		$form_more3		=	'';
		$lock			=	'';
		$file_title		=	trim( @$value['file_title'] );
		$file_title		=	htmlspecialchars( $file_title, ENT_QUOTES );
		$params			=	array();
		$legal_ext		=	isset( $options2['media_extensions'] ) ? $options2['media_extensions'] : 'custom';
		if ( $legal_ext == 'custom' ) {
			$legal_ext	=	$options2['legal_extensions'];
		} else {
			$default	=	array(
								'archive'=>'7z,bz2,gz,rar,zip,7Z,BZ2,GZ,RAR,ZIP',
								'audio'=>'flac,mp3,ogg,wma,wav,FLAC,MP3,OGG,WMA,WAV',
								'document'=>'csv,doc,docx,pdf,pps,ppsx,ppt,pptx,txt,xls,xlsx,CSV,DOC,DOCX,PDF,PPS,PPSX,PPT,PPTX,TXT,XLS,XLSX',
								'image'=>'bmp,gif,jpg,jpeg,png,tif,tiff,BMP,GIF,JPEG,JPG,PNG,TIF,TIFF',
								'video'=>'flv,mov,mp4,mpg,mpeg,swf,wmv,FLV,MOV,MP4,MPG,MPEG,SWF,WMV',
								'common'=>'bmp,csv,doc,docx,gif,jpg,pdf,png,pps,ppsx,ppt,pptx,txt,xls,xlsx,zip,BMP,CSV,DOC,DOCX,GIF,JPG,PDF,PNG,PPS,PPSX,PPT,PPTX,TXT,XLS,XLSX,ZIP',
								'preset1'=>'',
								'preset2'=>'',
								'preset3'=>''
							);
			$legal_ext	=	JCck::getConfig_Param( 'media_'.$legal_ext.'_extensions', $default[$legal_ext] );
			if ( !$legal_ext ) {
				$legal_ext	=	$options2['legal_extensions'];
			}
		}

		$class				=	'inputbox file'.$validate . ( $field->css ? ' '.$field->css : '' );
		$attr_input_text	=	'class="inputbox text" size="'.$field->size.'"';
		$collection			=	'';
		
		if ( strpos( $name, '[]' ) !== false ) { //FieldX
			$nameH			=	substr( $name, 0, -2 );
			$collection 	=	$nameH;
			$form_more 		=	'<input class="inputbox" type="hidden" id="'.$id.'_hidden" name="'.$nameH.'_hidden[]" value="'.$location.'" />';
			if ( $options2['custom_path'] == '1' ) {
				$form_more2	=	self::_addFormText( $id.'_path', $nameH.'_path[]', $attr_input_text,  @$options2['path_label'].$fold_3 , $value3, 'upload_file', false ); 
			}
			if ( $options2['delete_box'] && $value['file_location'] && $location != '' ) {
				$onchange	=	' onchange="jQuery(\'#'.$id.'_delete\').prop(\'checked\',true);"';
				$chkbox		=	'<input class="inputbox" type="checkbox" id="'.$id.'_delete" name="'.$nameH.'_delete['.$xk.']" value="1" />';				
			}
			if ( $options2['title_file'] == '1' && $options2['multivalue_mode'] == '1' ) {
				$form_more3	=	self::_addFormText( $id.'_title', $nameH.'_title[]', $attr_input_text, $title_label, $file_title, 'upload_file' );
			}
		} elseif ( $name[(strlen($name) - 1 )] == ']' ) { //GroupX
			$nameH			=	substr( $name, 0, -1 );
			$collection 	=	substr( $name, 0, strpos( $name, '[' ) );
			$form_more 		=	'<input class="inputbox" type="hidden" id="'.$id.'_hidden" name="'.$nameH.'_hidden]" value="'.$location.'" />';
			if ( $options2['custom_path'] == '1' ) {
				$form_more2	=	self::_addFormText( $id.'_path', $nameH.'_path]', $attr_input_text,  @$options2['path_label'].$fold_3 , $value3, 'upload_file', false );
			}
			if ( $options2['delete_box'] && $value['file_location'] && $location != '' ) {
				$onchange	=	' onchange="jQuery(\'#'.$id.'_delete\').prop(\'checked\',true);"';
				$chkbox		=	'<input class="inputbox" type="checkbox" id="'.$id.'_delete" name="'.$nameH.'_delete]" value="1" />';
			}
			if ( $options2['title_file'] == '1' && $options2['multivalue_mode'] == '1' ) {
				$form_more3	=	self::_addFormText( $id.'_title', $nameH.'_title]', $attr_input_text, $title_label, $file_title, 'upload_file' );
			}
		} else { //Default
			$form_more	=	'<input class="inputbox" type="hidden" id="'.$name.'_hidden" name="'.$name.'_hidden" value="'.$location.'" />';
			if ( $options2['custom_path'] == '1' ) {
				$form_more2	=	self::_addFormText( $id.'_path', $name.'_path', $attr_input_text,  @$options2['path_label'].$fold_3	, $value3, 'upload_file', false );
			}
			if ( $options2['delete_box'] && $value['file_location'] && $location != '' ) {
				$onchange	=	' onchange="jQuery(\'#'.$name.'_delete\').prop(\'checked\',true);"';
				$chkbox		=	'<input class="inputbox" type="checkbox" id="'.$name.'_delete" name="'.$name.'_delete" value="1" />';
			}
			if ( $options2['title_file'] == '1' && $options2['multivalue_mode'] == '1' ) {
				$form_more3	=	self::_addFormText( $id.'_title', $name.'_title', $attr_input_text, $title_label, $file_title, 'upload_file' );
			}
		}
		$params['custom_path']	=	@$options2['custom_path'];
		$attr	=	'class="'.$class.'" size="'.$field->size.'"'.$onchange . ( $field->attributes ? ' '.$field->attributes : '' );
		$form	=	'<input type="file" id="'.$id.'" name="'.$name.'" '.$attr.' />';
		if ( $options2['custom_path'] == '1' ) {
			$lock	=	'<a class="switch lock_file" href="javascript:void(0);"><span class="linkage linked"></span></a>';	//TODO
		}
		
		if ( $chkbox != '' ) {
			$form	.=	'<span class="hasTooltip" title="'.JText::_( 'COM_CCK_CHECK_TO_DELETE_FILE' ).'">'.$chkbox.'</span>';	//TODO
		}
		$form	=	$form.$form_more.$lock.$form_more2.$form_more3;
		if ( $options2['preview'] != -1 && $value['file_location'] && $value2 != '' ) {
			$more	=	( $collection ) ? '&collection='.$collection.'&xi='.$xk : '';
			$label	=	JText::_( 'COM_CCK_PREVIEW' );
			if ( isset( $config['id'] ) && $config['id'] ) {
				$link	=	JRoute::_( 'index.php?option=com_cck&task=download'.$more.'&file='.( ( !$config['client'] && $inherit['name'] ) ? $inherit['name'] : $field->name ).'&id='.$config['id'] );
				$target	=	'';
			} else {
				$link	=	JUri::root().$value2;
				$target	=	'target="_blank"';
			}
			$title	=	( $value['file_title'] != '' ) ? $value['file_title'] : ( ( strrpos( $value2, '/' ) === false ) ? $value2 : substr( $value2, strrpos( $value2, '/' ) + 1 ) );
			if ( $options2['preview'] == 8 ) {
				$label		=	'';
				$preview	=	'<span class="cck_preview">'.$title.'</span>';
			} else if ( $options2['preview'] == 1 ) {
				$preview	=	'<a href="'.$link.'"'.$target.' title="'.$value['file_title'].'"><img src="'.JUri::root().'media/cck/images/16/icon-16-preview.png" alt="" title=""/></a>';
			} else {
				$preview	=	'<a class="cck_preview" href="'.$link.'"'.$target.' title="'.$value['file_title'].'">'.$title.'</a>';
			}
			$preview	=	self::_addFormPreview( $id, $label, $preview, 'upload_file' );
			$form		.=	$preview;
		}
		
		// Set
		$value	=	$save_value;
		if ( ! $field->variation ) {
			$field->form	=	$form;
			if ( $field->script ) {
				parent::g_addScriptDeclaration( $field->script );
			}
		} else {
			$field->text	=	( $preview ) ? $preview : $value;
			$value2			=	( is_array( $value ) && isset( $value['file_location'] ) ) ? $value['file_location'] : $value;
			parent::g_getDisplayVariation( $field, $field->variation, $value2, $field->text, $form, $id, $name, '<input', '', $form_more, $config );
		}
		$field->value	=	$value;
		self::_addScripts( $params );

		// Return
		if ( $field->description ) {
			$field->description	=	str_replace( '*legal_extensions*', $legal_ext, $field->description );
		}
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
		parent::g_onCCK_FieldPrepareSearch( $field, $config );
		
		// Init
		$divider	=	$field->match_value ? $field->match_value : ' ';
		if ( is_array( $value ) ) {
			$value	=	implode( $divider, $value );
		}
		
		// Prepare
		$form	=	JCckDevField::getForm( 'core_not_empty_file', $value, $config, array( 'id'=>$field->id, 'name'=>$field->name, 'variation'=>$field->variation ) );
		
		// Set
		$field->form		=	$form;
		
		if ( $field->match_mode != 'none' ) {
			if ( $value != '' ) {
				$field->match_mode	=	'not_empty';
			} else {
				$field->match_mode	=	'';
			}
		}
		$field->type		=	'checkbox';
		$field->value		=	$value;
		
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
		$app			=	JFactory::getApplication();
		$options2		=	JCckDev::fromJSON( $field->options2 );
		$itemPrePath	=	'';
		if ( $config['pk'] && @$options2['storage_format'] ) {
			$itemPrePath	.=	( @$options2['storage_format'] ) ? $options2['path'] : '';
			$itemPrePath	.=	( @$options2['path_content'] )	? $config['pk'].'/' : '';
		}
		$item_custom_dir	=	$itemPrePath;
		$item_custom_title 	=	'';
		
		if ( count( $inherit ) ) {
			$name		=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
			$xk			=	( isset( $inherit['xk'] ) ) ? $inherit['xk'] : -1;
			$xi			=	( isset( $inherit['xi'] ) ) ? $inherit['xi'] : -1;
			$parent		=	( isset( $inherit['parent'] ) ) ? $inherit['parent'] : '';
			$array_x	=	( isset( $inherit['array_x'] ) ) ? $inherit['array_x'] : 0;
			$itemPath	=	( isset( $inherit['post'] ) ) ? $inherit['post'][$name.'_hidden'] : @$config['post'][$name.'_hidden'];
			$deleteBox	=	( isset( $inherit['post'] ) ) ? @$inherit['post'][$name.'_delete'] : @$config['post'][$name.'_delete'];

			if ( $options2['multivalue_mode'] ) {
				$item_custom_dir	.=	( isset( $inherit['post'] ) ) ? $inherit['post'][$name.'_path'] : @$config['post'][$name.'_path'];
				$item_custom_title	=	( isset( $inherit['post'] ) )	? @$inherit['post'][$name.'_title'] 	: @$config['post'][$name.'_title'];
			}

			if ( isset( $field->error ) && $field->error === true ) {
				$field->error = false;
			}

		} else {
			$name		=	$field->name;
			$xk			=	-1;
			$xi			=	-1;
			$parent		=	'';
			$array_x	=	0;
			$itemPath	=	@$config['post'][$name.'_hidden'];
			$deleteBox	=	@$config['post'][$name.'_delete'];
			if ( $options2['multivalue_mode'] ) {
				$item_custom_dir	.=	@$config['post'][$name.'_path'];
				$item_custom_title	=	@$config['post'][$name.'_title'];
			}
		}
		
		// Prepare
		$legal_ext		=	isset( $options2['media_extensions'] ) ? $options2['media_extensions'] : 'custom';
		if ( $legal_ext == 'custom' ) {
			$legal_ext	=	$options2['legal_extensions'];
		} else {
			$legal_ext	=	JCck::getConfig_Param( 'media_'.$legal_ext.'_extensions' );
			if ( !$legal_ext ) {
				$legal_ext	=	$options2['legal_extensions'];
			}
		}
		$legal_ext	=	explode( ',', $legal_ext );
		$userfile 	=	( $array_x ) ? JRequest::getVar( $parent, NULL, 'files', 'array' ) : JRequest::getVar( $name, NULL, 'files', 'array' );
		if ( is_array( $userfile['name'] ) ) {
			if ( $array_x ) {
				$userfile_name			=	$userfile['name'][$xk][$name];
				$userfile_type			=	$userfile['type'][$xk][$name];
				$userfile_tmp_name		=	$userfile['tmp_name'][$xk][$name];
				$userfile_error			=	$userfile['error'][$xk][$name];
				$userfile_size			=	$userfile['size'][$xk][$name];
				$userfile				=	null;
				$userfile				=	array();
				$userfile['name']		=	$userfile_name;
				$userfile['type']		=	$userfile_type;
				$userfile['tmp_name']	=	$userfile_tmp_name;
				$userfile['error']		=	$userfile_error;
				$userfile['size']		=	$userfile_size;
			} else {
				$userfile_name			=	$userfile['name'][$xk];
				$userfile_type			=	$userfile['type'][$xk];
				$userfile_tmp_name		=	$userfile['tmp_name'][$xk];
				$userfile_error			=	$userfile['error'][$xk];
				$userfile_size			=	$userfile['size'][$xk];
				$userfile				=	null;
				$userfile				=	array();
				$userfile['name']		=	$userfile_name;
				$userfile['type']		=	$userfile_type;
				$userfile['tmp_name']	=	$userfile_tmp_name;
				$userfile['error']		=	$userfile_error;
				$userfile['size']		=	$userfile_size;
				if ( is_array( $itemPath ) ) {
					$itemPath	=	$itemPath[$xk];
				}

				if ( $options2['multivalue_mode'] ) {
					if ( is_array( $item_custom_dir ) ) {
						$item_custom_dir	=	trim( $item_custom_dir[$xk] );
					}
					if ( is_array( $item_custom_title ) ) {
						$item_custom_title	=	trim( $item_custom_title[$xk] );
					}
				}
				if ( is_array( $deleteBox ) ) {
					$deleteBox	=	$deleteBox[$xk];
				}
			}
		}
		// Short Format Path
		if ( @$options2['storage_format'] ) {
			$itemPath	=	$itemPrePath.$itemPath;

			if ( strrpos( $item_custom_dir, '.') > 0 ) {
				$item_custom_name	=	substr(strrchr( $item_custom_dir, '/'), 1);
				$item_custom_path	=	substr( $item_custom_dir, 0, strlen( $item_custom_name ) * (-1) );
			} else {
				$item_custom_name	=	$userfile['name'];
				$item_custom_path	=	( $item_custom_dir == '' ) ? $itemPath : ( ( $item_custom_dir[strlen($item_custom_dir)-1] == '/' ) ? $item_custom_dir : $item_custom_dir.'/' );
			}
		}
		
		if ( $deleteBox == 1 ) {
			$title	=	strrpos( $itemPath, '/' ) ? substr( $itemPath, strrpos( $itemPath, '/' ) + 1 ) : $itemPath;

			if ( self::_checkPath( $config['pk'], $options2, $itemPath, $legal_ext ) ) {
				if ( $itemPath != $field->defaultvalue ) {
					if ( $options2['path_user'] ) {
						$user_folder	=	substr( $itemPath, 0, strrpos( $itemPath, '/' ) );
						if ( $options2['path_content'] ) {
							$user_folder=	substr( $user_folder, 0, strrpos( $user_folder, '/' ) );
						}
						$user_folder	=	substr( $user_folder, strrpos( $user_folder, '/' )+1 ).'/' ;
					} else {
						$user_folder	=	'';
					}
					$content_folder		=	( $options2['path_content'] ) ? $config['pk'].'/' : '';
	
					if ( JFile::exists( JPATH_SITE.'/'.$itemPath ) ) {	
						JFile::delete( JPATH_SITE.'/'.$itemPath );
					}
				}
				$itemPath	=	'';
			}
		}
		$file_path	=	'';
		$process	=	false;
		switch ( @$options2['size_unit'] ) {
			case '0' : $unit_prod = 1; break;
			case '1' : $unit_prod = 1000; break;
			case '2' : $unit_prod = 1000000; break;
			default  : $unit_prod = 1; break;
		}
		$maxsize			=	floatval( $options2['max_size'] ) * $unit_prod;
		$filename			=	JFile::stripExt( $userfile['name'] );
		$userfile['name']	=	str_replace( $filename, JCckDev::toSafeSTRING( $filename, JCck::getConfig_Param( 'media_characters', '-' ) ), $userfile['name'] );
		if ( ! $maxsize || ( $maxsize && $userfile['size'] < $maxsize ) ) {
			if ( $userfile && $userfile['name'] && $userfile['tmp_name'] ) {
				$item_custom_name	=	$userfile['name'];
				if ( @$options2['custom_path'] ) {
					if ( strrpos( $item_custom_dir, '.' ) === false ) {
						$item_custom_path	=	( $item_custom_dir == '' ) ? substr( $itemPath, 0, strrpos( $itemPath, '/' ) + 1 ) : ( ( $item_custom_dir[strlen( $item_custom_dir ) - 1] == '/' ) ? $item_custom_dir : $item_custom_dir.'/' );
					} else {
						$item_custom_path	=	substr( $itemPath, 0, strrpos( $itemPath, '/' ) + 1 );
					}
				}
				if ( count( $legal_ext ) ) {
					$old_legal	=	( strrpos( $userfile['name'], '.' ) ) ? substr( $userfile['name'], strrpos( $userfile['name'], '.' ) + 1 ) : '';
					$legal		=	( $item_custom_name ==  $userfile['name'] ) ? $old_legal : substr( $item_custom_name, strrpos( $item_custom_name, '.' ) + 1 );
					if ( $old_legal && array_search( $old_legal, $legal_ext ) === false ) {
						JFactory::getApplication()->enqueueMessage( $item_custom_name .' - '. JText::_( 'COM_CCK_ERROR_LEGAL_EXTENSIONS' ), 'notice' );
						$field->error	=	true;
					} else {
						if ( trim( $legal ) != trim( $old_legal ) ) {
							$item_custom_name	.=	'.'.trim( $old_legal );
						}
					}
				}					
				$file_path			=	$item_custom_path ? $item_custom_path : $options2['path'];
				$current_user		=	JFactory::getUser();
				$current_user_id	=	$current_user->id;
				$user_in_path		=	( @$options2['storage_format'] ) ? 1 : strpos( $file_path, '/'.$current_user_id.'/' ); // Force User To None
				$in_path			=	strpos( $file_path, '/'.$current_user_id.'/'.$config['pk'].'/' );
				if ( $options2['path_user'] && $options2['path_content'] && $in_path === false ) {
					$file_path	=	$options2['path'];
				}
				if ( $user_in_path === false ) {
					$file_path	.=	( $options2['path_user'] && $current_user_id ) ? $current_user_id.'/' : '';
				}
				if ( strpos($file_path, '/'.$config['pk'].'/' ) === false ) {
					$file_path	.=	( $options2['path_content'] && $config['pk'] > 0 ) ? $config['pk'].'/' : '';
				}
				$value				=	$file_path.$item_custom_name;
				$process			=	true;
			} else {
				if ( $deleteBox == 1 ) {
					$item_custom_title	=	'';
				}
				if ( $item_custom_dir != '' && strrpos( $item_custom_dir, '.' ) > 0 && JFile::exists( JPATH_SITE.'/'.$item_custom_dir ) ) {
					if ( count( $legal_ext ) ) {
						$legal		=	( strrpos( $item_custom_dir, '.' ) ) ? substr( $item_custom_dir, strrpos( $item_custom_dir, '.' ) + 1 ) : '';
						if ( $legal && array_search( $legal, $legal_ext ) === false ) {
							JFactory::getApplication()->enqueueMessage( $imageCustomDir .' - '. JText::_( 'COM_CCK_ERROR_LEGAL_EXTENSIONS' ), 'notice' );
							$field->error	=	true;
						}
					}
					$value	=	$item_custom_dir;
				} else {
					if ( $userfile['name'] ) {
						JFactory::getApplication()->enqueueMessage( JText::_( 'COM_CCK_ERROR_INVALID_FILE' ), "error" );
						$field->error	=	true;
					}
					if ( $options2['path'] == $itemPath ) {
						$value	=	'';
					} else {
						if ( strrpos( $itemPath, '.') > 0 && JFile::exists( JPATH_SITE.'/'.$itemPath ) ) {
							$value	=	$itemPath;
						} else {
							$value	=	'';
						}
						/*
						if ( $xk != -1 && !$array_x ) {
							$value	=	( $itemPath == $options2['path'] ) ? '' : $itemPath;
						} else {
							$value	=	$itemPath;
						}
						*/
					}
				}
			}
		} else {
			JFactory::getApplication()->enqueueMessage( $userfile['name'] .' - '. JText::_( 'COM_CCK_ERROR_MAX_SIZE' ), 'notice' );
			$field->error	=	true;
		}
		
		// Validate
		parent::g_onCCK_FieldPrepareStore_Validation( $field, $name, $value, $config );
		if ( isset( $field->error ) && $field->error === true ) {
			return;
		}
		$item_custom_title	=	addcslashes( $item_custom_title, '"' );
		
		// Add Process
		if ( $process === true ) {
			$content_folder	=	( $options2['path_content'] ) ? $options2['path_content'] : 0;
			$forbidden_ext	=	( $options2['forbidden_extensions'] != '' ) ? $options2['forbidden_extensions'] : JCck::getConfig_Param( 'media_content_forbidden_extensions', '0' );
			$process_params	=	array( 'field_name'=>$name, 'true_name'=>$field->name, 'array_x'=>$array_x, 'parent_name'=>$parent, 'field_type'=>$field->type, 'file_path'=>$file_path, 'forbidden_ext'=>$forbidden_ext,
									   'file_name'=>$item_custom_name, 'tmp_name'=>$userfile['tmp_name'], 'xi'=>$xi, 'content_folder'=>$content_folder, 'options2'=>$options2, 'value'=>$field->value,
									   'storage'=>$field->storage, 'storage_field'=>$field->storage_field, 'storage_field2'=>($field->storage_field2 ? $field->storage_field2 : $field->name ), 
									   'storage_table'=>$field->storage_table, 'file_title'=>$item_custom_title );
			parent::g_addProcess( 'afterStore', self::$type, $config, $process_params );
		}
		$item_custom_title	=	( $item_custom_title ) ? $item_custom_title : '';
		$value				=	( @$options2['storage_format'] ) ? substr( $value, strrpos( $value, '/' ) + 1 ) : $value;
		$value				=	( $item_custom_title == '' ) ? $value : '{"file_location":"'.$value.'","file_title":"'.$item_custom_title.'"}';
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
		return parent::g_onCCK_FieldRenderContent( $field, 'html' );
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
		jimport( 'joomla.filesystem.folder' );
		
		include __DIR__.'/includes/afterstore.php';
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff & Script
	
	// _formatBytes
	protected static function _formatBytes( $bytes, $precision = 2 )
	{ 
		$units	=	array( 'B', 'KB', 'MB', 'GB', 'TB' ); 
	   
		$bytes	=	max( $bytes, 0 );
		$pow	=	floor( ( $bytes ? log( $bytes ) : 0 ) / log( 1024 ) );
		$pow	=	min( $pow, count( $units ) - 1 );
		$bytes	/=	pow( 1024, $pow );
		
		return round( $bytes, $precision ) .' '. $units[$pow];
	}
	
	// _getHits
	protected static function _getHits( $id, $fieldname, $collection = '', $x = 0 )
	{
		$query	=	'SELECT a.hits FROM #__cck_core_downloads AS a WHERE a.id = '.(int)$id.' AND a.field = "'.(string)$fieldname.'" AND a.collection = "'.(string)$collection.'" AND a.x = '.(int)$x;
		$hits	=	JCckDatabase::loadResult( $query ); //@
		
		return ( $hits ) ? $hits : 0;
	}
	
	// _addScripts
	protected function _addScripts( $params )
	{
		static $loaded	=	0;
		if ( $loaded ) {
			return;
		}
		
		if ( $params['custom_path'] ) {
			$doc	=	JFactory::getDocument();
			$loaded	=	1;
			
			JCck::loadjQuery();
			$doc->addStyleSheet( self::$path.'assets/css/upload_file.css' );
			$doc->addScript( self::$path.'assets/js/upload_file.js' );
		}
	}

	// _addFormText
	protected static function _addFormText( $id, $name, $attr, $label, $value, $suffix, $display = true )
	{
		$form	=	( $display ) ? '<div class="cck_forms cck_'.$suffix.'">' : '<div class="cck_forms cck_upload_image_'.$suffix.'" style="display:none">';
		$form	.=	'<div class="cck_label cck_label_'.$suffix.'"><label for="'.$id.'" >'.$label.'</label></div>';
		$form	.=	'<div class="cck_form cck_form_'.$suffix.'"><input type="text" id="'.$id.'" name="'.$name.'" value="'.$value.'" '.$attr.' /></div>';
		$form	.=	'</div>';
		
		return $form;
	}

	// _addFormPreview
	protected static function _addFormPreview( $id, $label, $preview, $suffix )
	{
		$form	=	'<div class="cck_forms cck_'.$suffix.'">';
		$form	.=	'<div class="cck_label cck_label_'.$suffix.'"><label for="'.$id.'" >'.$label.'</label></div>';
		$form	.=	'<div class="cck_form cck_form_'.$suffix.'">'.$preview.'</div>';
		$form	.=	'</div>';
		
		return $form;
	}
	
	// _checkPath
	protected static function _checkPath( $pk, $options2, $itemPath, $legal_ext )
	{
		$default	=	$options2['path'];
		$bool		=	0;
		
		if ( ! $options2['custom_path'] ) {
			if ( $options2['path_user'] ) {
				$current_user	=	JFactory::getUser();
				$default		.=	$current_user->id.'/';
			}
			if ( $options2['path_content'] ) {
				$default		.=	$pk.'/';
			}
			$bool	=	( strpos( $itemPath, $default ) !== false ) ? 1 : 0;
		} else {
			$bool	=	1;
		}
		
		$legal		=	( strrpos( $itemPath, '.' ) ) ? substr( $itemPath, strrpos( $itemPath, '.' ) + 1 ) : '';
		if ( $legal && array_search( $legal, $legal_ext ) === false ) {
			$bool	=	0;
		}
		
		return $bool;
	}
}
?>