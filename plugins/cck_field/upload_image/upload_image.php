<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2013 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

jimport( 'joomla.filesystem.file' );

// Plugin
class plgCCK_FieldUpload_Image extends JCckPluginField
{
	protected static $type		=	'upload_image';
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
		
		jimport( 'joomla.filesystem.folder' );
		if ( ! JFolder::exists( JPATH_SITE.'/'.$data['json']['options2']['path'] ) ) {
			JFolder::create( JPATH_SITE.'/'.$data['json']['options2']['path'] );
			$buffer	=	'<!DOCTYPE html><title></title>';
			JFile::write( JPATH_SITE.'/'.$data['json']['options2']['path'].'/'.'index.html', $buffer );
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
		
		// Set
		$value_json		=	JCckDev::fromJSON( $value );
		$options2		=	JCckDev::fromJSON( $field->options2 );
		if ( is_array( $value_json ) && !empty( $value_json ) ) {
			$value		=	( trim( $value_json['image_location'] ) == '' ) ? trim( $field->defaultvalue ) : trim( $value_json['image_location'] ) ;
			$file_name	=	( $value == '' ) ? '' : substr( strrchr( JFile::stripExt( $value ), '/' ), 1 );
			$img_title	=	self::_getTitle( trim( $value_json['image_title'] ), $file_name );
			$img_title	=	htmlspecialchars( $img_title, ENT_QUOTES );
			$img_desc	=	( isset( $value_json['image_description'] ) ) ? trim($value_json['image_description']) : ( ( isset( $value_json['image_descr'] ) ) ? trim( $value_json['image_descr'] ) : '' ) ;
			$img_desc	=	self::_getAlt( $img_desc, $img_title, $file_name );
			$img_desc	=	htmlspecialchars( $img_desc, ENT_QUOTES );
		} else {
			$value		=	( trim( $value ) == '' ) ? trim( $field->defaultvalue ) : trim( $value ) ;
			$file_name	=	( $value == '' ) ? '' : substr( strrchr( JFile::stripExt( $value ), '/' ), 1 );
			$img_title	=	$file_name;
			$img_desc	=	$file_name;
		}
		if ( $value && JFile::exists( JPATH_SITE.'/'.$value ) ) {
			$path		=	substr( $value, 0, strrpos( $value, '/' ) ).'/';
			for ( $i = 1; $i < 11; $i++ ) {
				$thumb					=	$path.'_thumb'.$i.'/'.substr( strrchr( $value, '/' ), 1 );
				$field->{'thumb'.$i}	=	( JFile::exists( JPATH_SITE.'/'.$thumb ) ) ? $thumb : '';
			}
			
			self::_addThumbs( $field, $options2, $value, $path, $this->params->get( 'quality_jpeg', '90' ), $this->params->get( 'quality_png', '90' ) );
			if ( isset( $options2['content_preview'] ) && $options2['content_preview'] ) {
				$i				=	(int)$options2['content_preview'];
				$field->html	=	( $field->{'thumb'.$i} ) ?  '<img src="'.$field->{'thumb'.$i}.'" title="'.$img_title.'" alt="'.$img_desc.'" />' : '<img src="'.$value.'" title="'.$img_title.'" alt="'.$img_desc.'" />';
			} else {
				$field->html	=	'<img src="'.$value.'" title="'.$img_title.'" alt="'.$img_desc.'" />';
			}
			$field->value		=	$value;
			$field->image_title	=	$img_title;
			$field->image_alt	=	$img_desc;
		} else {
			$field->value		=	'';
			$field->html		=	'';
			$field->image_title	=	'';
			$field->image_alt	=	'';
		}
		
		$field->typo_target	=	'html';
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
		
		// Validate
		$validate	=	'';
		if ( $config['doValidation'] > 1 ) {
			plgCCK_Field_ValidationRequired::onCCK_Field_ValidationPrepareForm( $field, $id, $config );
			$validate	=	( count( $field->validate ) ) ? ' validate['.implode( ',', $field->validate ).']' : '';
		}
		
		// Prepare
		$options2		=	JCckDev::fromJSON( $field->options2 );
		if ( $config['doTranslation'] ) {
			$title_label	=	trim( @$options2['title_label'] ) ? JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( @$options2['title_label'] ) ) ) : '';
			$path_label		=	trim( @$options2['path_label'] ) ? JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( @$options2['path_label'] ) ) ) : '';
			$desc_label		=	trim( @$options2['desc_label'] ) ? JText::_( 'COM_CCK_' . str_replace( ' ', '_', trim( @$options2['desc_label'] ) ) ) : '';
		} else {
			$title_label	=	@$options2['title_label'];
			$path_label		=	@$options2['path_label'];
			$desc_label		=	@$options2['desc_label'];
		}
		$value		=	trim( $value );
		if ( $value == '') {
			$value	=	array( 'image_location'=>'', 'image_title'=>'', 'image_description'=>'' );
		} else {
			$value_json	=	JCckDev::fromJSON( $value );
			if ( is_array( $value_json ) && !empty( $value_json ) ) {
				$value_json['image_location']	=	trim( $value_json['image_location'] );
				$value							=	$value_json;
			} else {
				$value							=	array( 'image_location'=>$value, 'image_title'=>'', 'image_description'=>'' );
			}
		}
		
		$save_value		=	$value;
		$value2			=	( $value['image_location'] != '' ) ? $value['image_location'] : $options2['path'];
		$value['image_location']	=	( $value['image_location'] != '' ) ? $value['image_location'] : $field->defaultvalue;
		$title			=	( $value['image_location'] == '' ) ? '' : substr( strrchr( JFile::stripExt( $value['image_location'] ), '/' ), 1 );
		$image_title	=	( @$value['image_title'] ) ? trim( $value['image_title'] ) : substr( strrchr( $value['image_location'] , '/' ), 1 );
		$image_title	=	htmlspecialchars( $image_title, ENT_QUOTES );
		$image_desc		=	( isset( $value_json['image_description'] ) ) ? trim( $value_json['image_description'] ) : ( ( isset( $value_json['image_descr'] ) ) ? trim( $value_json['image_descr'] ) : '' ) ;
		$image_desc		=	htmlspecialchars( $image_desc, ENT_QUOTES );
		$form_more2		=	'';
		$form_more3		=	'';
		$form_more4		=	'';
		$chkbox			=	'';
		$onchange		=	'';
		$preview		=	'';
		$lock			=	'';
		$params			=	array();
		$legal_ext		=	isset( $options2['media_extensions'] ) ? $options2['media_extensions'] : 'custom';
		if ( $legal_ext == 'custom' ) {
			$legal_ext	=	$options2['legal_extensions'];
		} else {
			$legal_ext	=	JCck::getConfig_Param( 'media_'.$legal_ext.'_extensions' );
			if ( !$legal_ext ) {
				$legal_ext	=	$options2['legal_extensions'];
			}
		}

		if ( $value['image_location'] && JFile::exists( JPATH_ROOT.'/'.$value['image_location'] ) ) {
			$path	=	substr( $value['image_location'], 0, strrpos( $value['image_location'], '/' ) ).'/';
			for ( $i = 1; $i < 11; $i++ ) {
				$thumb	=	$path.'_thumb'.$i.'/'.substr( strrchr( $value['image_location'], '/' ), 1 );
				$field->{'thumb'.$i}	=	( JFile::exists( JPATH_ROOT.'/'.$thumb ) ) ? $thumb : '';
			}
			self::_addThumbs( $field, $options2, $value['image_location'], $path, $this->params->get( 'quality_jpeg', '90' ), $this->params->get( 'quality_png', '90' ) );
		}
		
		$class				=	'inputbox file'.$validate . ( $field->css ? ' '.$field->css : '' );
		$attr_input_text	=	'class="inputbox text" size="'.$field->size.'"';
		
		if ( strpos( $name, '[]' ) !== false ) { //FieldX
			$nameH	=	substr( $name, 0, -2 );
			$form_more 	=	'<input class="inputbox" type="hidden" id="'.$id.'_hidden" name="'.$nameH.'_hidden[]" value="'.$value2.'" />';
			if ( $options2['title_image'] == '1' && @$options2['multivalue_mode'] == '1' ) {
				$form_more2	=	self::_addFormText( $id.'_title', $nameH.'_title[]', $attr_input_text, $title_label, $image_title, 'upload_image' );
			}
			if ( $options2['custom_path'] == '1' ) {
				$form_more3	=	self::_addFormText( $id.'_path', $nameH.'_path[]', $attr_input_text,  $path_label, $value2, 'upload_image', false );
				$lock		=	'<a class="switch lock_img" href="javascript:void(0);"><span class="linkage linked"></span></a>';		//TODO!
			}
			if ( @$options2['desc_image'] == '1' && @$options2['multivalue_mode'] == '1' ) {
				$form_more4	=	self::_addFormText( $id.'_description', $nameH.'_description[]', $attr_input_text,  $desc_label, $image_desc, 'upload_image' );
			}
			if ( $options2['delete_box'] && $value['image_location'] && $value['image_location'] != $field->defaultvalue ) {
				$onchange	=	' onchange="$(\''.$id.'_delete\').checked=true;"';
				$chkbox		=	'<input class="inputbox" type="checkbox" id="'.$id.'_delete" name="'.$nameH.'_delete['.$xk.']" value="1" />';				
			}
		} elseif ( $name[(strlen($name) - 1 )] == ']' ) { //GroupX
			$nameH	=	substr( $name, 0, -1 );
			$form_more 	=	'<input class="inputbox" type="hidden" id="'.$id.'_hidden" name="'.$nameH.'_hidden]" value="'.$value2.'" />';
			if ( $options2['title_image'] == '1' && @$options2['multivalue_mode'] == '1' ) {
				$form_more2	=	self::_addFormText( $id.'_title', $nameH.'_title]', $attr_input_text, $title_label, $image_title, 'upload_image' );
			}
			if ( $options2['custom_path'] == '1' ) {
				$form_more3	=	self::_addFormText( $id.'_path', $nameH.'_path]', $attr_input_text,  $path_label, $value2, 'upload_image', false );
				$lock		=	'<a class="switch lock_img" href="javascript:void(0);"><span class="linkage linked"></span></a>';		//TODO!
			}
			if ( @$options2['desc_image'] == '1' && @$options2['multivalue_mode'] == '1' ) {
				$form_more4	=	self::_addFormText( $id.'_description', $nameH.'_description]', $attr_input_text,  $desc_label, $image_desc, 'upload_image' );
			}
			if ( $options2['delete_box'] && $value['image_location'] && $value['image_location'] != $field->defaultvalue ) {
				$onchange	=	' onchange="$(\''.$id.'_delete\').checked=true;"';
				$chkbox		=	'<input class="inputbox" type="checkbox" id="'.$id.'_delete" name="'.$nameH.'_delete]" value="1" />';
			}
		} else { //Default
			$form_more	=	'<input class="inputbox" type="hidden" id="'.$name.'_hidden" name="'.$name.'_hidden" value="'.$value2.'" />';
			if ( $options2['title_image'] == '1' && @$options2['multivalue_mode'] == '1' ) {
				$form_more2	=	self::_addFormText( $id.'_title', $name.'_title', $attr_input_text, $title_label, $image_title, 'upload_image' );
			}
			if ( $options2['custom_path'] == '1' ) {
				$form_more3	=	self::_addFormText( $id.'_path', $name.'_path', $attr_input_text,  $path_label, $value2, 'upload_image', false );
				$lock		=	'<a class="switch lock_img" href="javascript:void(0);"><span class="linkage linked"></span></a>';	//TODO!
			}
			if ( @$options2['desc_image'] == '1' && @$options2['multivalue_mode'] == '1' ) {
				$form_more4	=	self::_addFormText( $id.'_description', $name.'_description', $attr_input_text,  $desc_label, $image_desc, 'upload_image' );
			}
			if ( $options2['delete_box'] && $value['image_location'] && $value['image_location'] != $field->defaultvalue ) {
				$onchange	=	' onchange="$(\''.$name.'_delete\').checked=true;"';
				$chkbox		=	'<input class="inputbox" type="checkbox" id="'.$name.'_delete" name="'.$name.'_delete" value="1" />';
			}
		}
		
		$attr	=	'class="'.$class.'" size="'.$field->size.'"'.$onchange . ( $field->attributes ? ' '.$field->attributes : '' );
		$form	=	'<input type="file" id="'.$id.'" name="'.$name.'" '.$attr.' />';

		$params['custom_path']	=	@$options2['custom_path'];
		
		if ( $chkbox != '' ) {
			$form	.=	'<span title="'.JText::_( 'COM_CCK_CHECK_TO_DELETE_FILE' ).'">'.$chkbox.'</span>';	//TODO
		}
		
		if ( $options2['form_preview'] != -1 && $value['image_location'] ) {
			$params['image_colorbox']	=	'1';
			$title_image	=	self::_getTitle( $image_title, $title );
			$desc_image		=	self::_getAlt( $image_desc, $image_title, $title );
			$title_colorbox	=	$desc_image;
			if ( $options2['form_preview'] > 1 ) {
				if ( $options2['form_preview'] == 2 ) {
					$width		=	( $options2['image_width'] ) ? 'width="'.$options2['image_width'].'"' : '';
					$height		=	( $options2['image_height'] ) ? 'height="'.$options2['image_height'].'"' : '';
					$preview	=	'<a id="colorBox'.$field->id.'" href="'.JURI::root().$value['image_location'].'" rel="colorBox'.$field->id.'" title="'.$title_colorbox.'" '.$width.' '.$height.'>
										<img title="'.$title_image.'" alt="'.$desc_image.'" src="'.JURI::root().$value['image_location'].'" />
									</a>';
				} else {
					$thumb_location	=	str_replace( $title,'_thumb'.( $options2['form_preview'] - 2 ).'/'.$title,$value['image_location'] );
					$preview	=	'<a id="colorBox'.$field->id.'" href="'.JURI::root().$value['image_location'].'" rel="colorBox'.$field->id.'" title="'.$title_colorbox.'">
										<img title="'.$title_image.'" alt="'.$desc_image.'" src="'.JURI::root().$thumb_location.'" />
									</a>';
				}
			} elseif ( $options2['form_preview'] == 1 ) {
				$preview	=	'<a id="colorBox'.$field->id.'" href="'.JURI::root().$value['image_location'].'" rel="colorBox'.$field->id.'" title="'.$title_colorbox.'">
									<img title="'.$title_image.'" alt="'.$desc_image.'" src="'.JURI::root().'media/cck/images/16/icon-16-preview.png" />
								</a>';
			} else {
				$preview	=	'<a class="cck_preview" id="colorBox'.$field->id.'" href="'.JURI::root().$value['image_location'].'" rel="colorBox'.$field->id.'" title="'.$title_colorbox.'">'.$title_image.'</a>';
			}
			$preview		=	self::_addFormPreview( $id, JText::_( 'COM_CCK_PREVIEW' ), $preview, 'upload_image' );
		} else {
			$params['image_colorbox']	=	'0';
		}
		$form	=	$form.$form_more.$lock.$form_more3.$form_more2.$form_more4.$preview;

		// Set
		$value	=	$save_value;
		if ( ! $field->variation ) {
			$field->form	=	$form;
			if ( $field->script ) {
				parent::g_addScriptDeclaration( $field->script );
			}
		} else {
			$field->text	=	( $preview ) ? $preview : $value['image_location'];
			parent::g_getDisplayVariation( $field, $field->variation, $value['image_location'], $field->text, $form, $id, $name, '<input', '', $form_more, $config );
		}
		$field->value	=	JCckDev::toJSON( $value );
		self::_addScripts( $field->id, $params );

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
		$form	=	JCckDevField::getForm( 'core_not_empty_image', $value, $config, array( 'id'=>$field->id, 'name'=>$field->name, 'variation'=>$field->variation ) );
		
		// Set
		$field->form		=	$form;
		$field->match_mode	=	'not_empty';
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
		$app		=	JFactory::getApplication();
		$options2	=	JCckDev::fromJSON( $field->options2 );
		if ( count( $inherit ) ) {
			$name		=	( isset( $inherit['name'] ) && $inherit['name'] != '' ) ? $inherit['name'] : $field->name;
			$xk			=	( isset( $inherit['xk'] ) ) ? $inherit['xk'] : -1;
			$xi			=	( isset( $inherit['xi'] ) ) ? $inherit['xi'] : -1;
			$parent		=	( isset( $inherit['parent'] ) ) ? $inherit['parent'] : '';
			$array_x	=	( isset( $inherit['array_x'] ) ) ? $inherit['array_x'] : 0;
			$itemPath	=	( isset( $inherit['post'] ) ) ? $inherit['post'][$name.'_hidden'] : @$config['post'][$name.'_hidden'];
			$deleteBox	=	( isset( $inherit['post'] ) ) ? @$inherit['post'][$name.'_delete'] : @$config['post'][$name.'_delete'];
			$imageTitle	=	( isset( $inherit['post'] ) )	? @$inherit['post'][$name.'_title'] 	: @$config['post'][$name.'_title'];
			$imageDesc	=	( isset( $inherit['post'] ) )	? @$inherit['post'][$name.'_description'] 	: @$config['post'][$name.'_description'];
			$imageCustomDir	=	( isset( $inherit['post'] ) ) ? @$inherit['post'][$name.'_path'] : @$config['post'][$name.'_path'];
		} else {
			$name		=	$field->name;
			$xk			=	-1;
			$xi			=	-1;
			$parent		=	'';
			$array_x	=	0;
			$itemPath	=	@$config['post'][$name.'_hidden'];
			$deleteBox	=	@$config['post'][$name.'_delete'];
			$imageTitle	=	@$config['post'][$name.'_title'];
			$imageDesc	=	@$config['post'][$name.'_description'];
			$imageCustomDir	=	@$config['post'][$name.'_path'];
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
					$itemPath	=	trim( $itemPath[$xk] );
				}
				if ( is_array( $imageTitle ) ) {
					$imageTitle	=	trim( $imageTitle[$xk] );
				}
				if ( is_array( $imageDesc ) ) {
					$imageDesc	=	trim( $imageDesc[$xk] );
				}
				if ( is_array( $imageCustomDir ) ) {
					$imageCustomDir	=	trim( $imageCustomDir[$xk] );
				}
				if ( is_array( $deleteBox ) ) {
					$deleteBox	=	$deleteBox[$xk];
				}
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
						$user_folder	=	substr( $user_folder, strrpos( $user_folder, '/' )+1 ).'/';
					} else {
						$user_folder	=	'';
					}
					$content_folder		=	( $options2['path_content'] ) ? $config['pk'].'/' : '';
					for ( $i = 1; $i < 11; $i++ ) {
						if ( JFile::exists( JPATH_SITE.'/'.$options2['path'].$user_folder.$content_folder.'_thumb'.$i.'/'.$title ) ) {
							JFile::delete( JPATH_SITE.'/'.$options2['path'].$user_folder.$content_folder.'_thumb'.$i.'/'.$title );
						}
					}
					if ( JFile::exists( JPATH_SITE.'/'.$options2['path'].$user_folder.$content_folder.$title ) ) {	
						JFile::delete( JPATH_SITE.'/'.$options2['path'].$user_folder.$content_folder.$title );
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
				$ImageCustomName	=	$userfile['name'];
				if ( @$options2['custom_path'] ) {
					if ( strrpos( $imageCustomDir, '.') === false ) {
						$ImageCustomPath	=	( $imageCustomDir == '' ) ? substr( $itemPath, 0, strrpos($itemPath,'/') + 1 ) : ( ( $imageCustomDir[strlen($imageCustomDir)-1] == '/' ) ? $imageCustomDir : $imageCustomDir.'/' );
					} else {
						$ImageCustomPath	=	substr( $itemPath, 0, strrpos($itemPath,'/') + 1 );
					}
				}
				if ( count( $legal_ext ) ) {
					$old_legal	=	( strrpos( $userfile['name'], '.' ) ) ? substr( $userfile['name'], strrpos( $userfile['name'], '.' ) + 1 ) : '';
					$legal		=	( $ImageCustomName ==  $userfile['name'] ) ? $old_legal : substr( $ImageCustomName, strrpos( $ImageCustomName, '.' ) + 1 );
					if ( $old_legal && array_search( $old_legal, $legal_ext ) === false ) {
						JFactory::getApplication()->enqueueMessage( $ImageCustomName .' - '. JText::_( 'COM_CCK_ERROR_LEGAL_EXTENSIONS' ), 'notice' );
						$field->error	=	true;
					} else {
						if ( trim( $legal ) != trim( $old_legal ) ) {
							$ImageCustomName	.=	'.'.trim( $old_legal );
						}
					}
				}
				$file_path			=	$ImageCustomPath ? $ImageCustomPath : $options2['path'];
				$current_user		=	JFactory::getUser();
				$current_user_id	=	$current_user->id;
				if ( strpos( $file_path, '/'.$current_user_id.'/' ) === false ) {
					$file_path		.=	( $options2['path_user'] && $current_user_id ) ? $current_user_id.'/' : '';
				}
				if ( strpos( $file_path, '/'.$config['pk'].'/' ) === false ) {
					$file_path		.=	( $options2['path_content'] && $config['pk'] > 0 ) ? $config['pk'].'/' : '';
				}
				$value				=	$file_path.$ImageCustomName;
				$process			=	true;
			} else {
				if ( $deleteBox == 1 ) {
					$imageTitle	=	'';
					$imageDesc	=	'';
				}
				if ( $imageCustomDir != '' && strrpos( $imageCustomDir, '.') > 0 && JFile::exists( JPATH_SITE.'/'.$imageCustomDir ) ) {
					if ( count( $legal_ext ) ) {
						$legal		=	( strrpos( $imageCustomDir, '.' ) ) ? substr( $imageCustomDir, strrpos( $imageCustomDir, '.' ) + 1 ) : '';
						if ( $legal && array_search( $legal, $legal_ext ) === false ) {
							JFactory::getApplication()->enqueueMessage( $imageCustomDir .' - '. JText::_( 'COM_CCK_ERROR_LEGAL_EXTENSIONS' ), 'notice' );
							$field->error	=	true;
						}
					}
					$value	=	$imageCustomDir;
				} else {
					if ( $userfile['name'] ) {
						JFactory::getApplication()->enqueueMessage( JText::_( 'COM_CCK_ERROR_INVALID_FILE' ), "error" );
						$field->error	=	true;
					}
					if ( $options2['path'] == $itemPath ) {
						$value	=	'';
					} else {
						if ( $xk != -1 && !$array_x ) {
							$value	=	( $itemPath == $options2['path'] ) ? '' : $itemPath;
						} else {
							$value	=	$itemPath;
						}
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
		$imageTitle =	( $imageTitle ) ? addcslashes( $imageTitle, '"' ) : '';
		$imageDesc	=	( $imageDesc ) ? addcslashes( $imageDesc, '"' ) : '';
		
		// Add Process
		if ( $process === true ) {
			$content_folder	=	( $options2['path_content'] ) ? $options2['path_content'] : 0;
			$process_params	=	array( 'field_name'=>$name, 'true_name'=>$field->name, 'array_x'=>$array_x, 'parent_name'=>$parent, 'field_type'=>$field->type, 'file_path'=>$file_path,
									   'file_name'=>$ImageCustomName, 'tmp_name'=>$userfile['tmp_name'], 'xi'=>$xi, 'content_folder'=>$content_folder, 'options2'=>$options2, //'value'=>$field->value,
									   'quality_jpeg'=>$this->params->get( 'quality_jpeg', '90' ), 'quality_png'=>$this->params->get( 'quality_png', '3' ),
									   'storage'=>$field->storage, 'storage_field' => $field->storage_field, 'storage_field2'=>( $field->storage_field2 ? $field->storage_field2 : $field->name ), 
									   'storage_table'=>$field->storage_table, 'file_title'=>$imageTitle, 'file_descr'=>$imageDesc );
			parent::g_addProcess( 'afterStore', self::$type, $config, $process_params );
		}
		
		$value		=	( $imageTitle == '' && $imageDesc == '' ) ? $value : '{"image_location":"'.$value.'","image_title":"'.$imageTitle.'","image_description":"'.$imageDesc.'"}';
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
		
		include dirname(__FILE__).'/includes/afterstore.php';
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff & Script
	
	// _addScripts
	protected function _addScripts( $id, $params = array() )
	{
		$doc	=	JFactory::getDocument();
		
		if ( $params['image_colorbox'] ) {
			JCck::loadjQuery();
			JCck::loadModalBox();
			
			$js	=	'jQuery(document).ready(function($){ $("a[rel=\'colorBox'.$id.'\']").colorbox(); });';
			$doc->addScriptDeclaration( $js );
		}
		
		static $loaded	=	0;
		if ( $loaded ) {
			return;
		}
		
		if ( $params['custom_path'] ) {
			$loaded	=	1;
			
			JCck::loadjQuery();
			$doc->addStyleSheet( self::$path.'assets/css/upload_image.css' );
			$doc->addScript( self::$path.'assets/js/upload_image.js' );
		}
	}
	
	// _getTitle
	protected static function _getTitle( $title_image, $file_name )
	{
		return	( $title_image == '' ) ? $file_name : $title_image;
	}

	// _getAlt
	protected static function _getAlt( $desc_image, $title_image, $file_name )
	{
		return	( $desc_image == '' ) ? self::_getTitle( $title_image, $file_name ) : $desc_image;
	}

	// _addThumbs
	protected static function _addThumbs( &$field, $options2, $value, $path, $quality_jpeg, $quality_png )
	{
		switch ( @$options2['force_thumb_creation'] ) {
			case '0':
				break;
			case '1':
				jimport( 'joomla.filesystem.folder' );
				for ( $i = 1; $i < 11; $i++ ) {
					if ( ! $field->{'thumb'.$i} || $field->{'thumb'.$i} == '' ) {
						$process_params			=	array( 'quality_jpeg'=> $quality_jpeg, 'quality_png'=> $quality_png );
						$thumb_result			=	self::_addThumb ( $value, $options2, $i, $process_params );
						$field->{'thumb'.$i}	=	( $thumb_result ) ? $path.'_thumb'.$i.'/'.substr( strrchr( $value, '/' ), 1 ) : '';
					}
				}
				break;
			case '2' :
				jimport( 'joomla.filesystem.folder' );
				for ( $i = 1; $i < 11; $i++ ) {
					$process_params			=	array( 'quality_jpeg'=> $quality_jpeg, 'quality_png'=> $quality_png );
					$thumb_result			=	self::_addThumb ( $value, $options2, $i, $process_params );
					$field->{'thumb'.$i}	=	( $thumb_result ) ? $path.'_thumb'.$i.'/'.substr( strrchr( $value, '/' ), 1 ) : '';
				}
				break;
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

	// _available_img_dim
	protected static function _available_img_dim( $dim )
	{
		return $dim && trim( $dim ) != '' && is_numeric( $dim );
	}
	
	// _addThumb
	protected static function _addThumb( $location, $options, $thumb, $params )
	{
		$location_old	=	$location;
		$location		=	JPATH_SITE.'/'.$location;
		$newSize		=	getimagesize( JPATH_SITE.'/'.$location_old );
		$newWidth		=	$newSize[0];
		$newHeight		=	$newSize[1];
		$newRatio		=	$newWidth / $newHeight;	
		$newExt			=	substr( strrchr( $location_old, "." ), 1 );
		$waterI			=	''; // $options['image_watermark'];
		$waterExtI		=	substr( strrchr( $waterI, "." ), 1 );
		
		switch( $newExt ) {
			case 'gif':
			case 'GIF':
				$resImage	=	@ImageCreateFromGIF( JPATH_SITE.'/'.$location_old );
				break;
			case 'jpg':
			case 'JPG':
			case 'jpeg': 
			case 'JPEG': 
				$resImage	=	@ImageCreateFromJPEG( JPATH_SITE.'/'.$location_old );
				break;
			case 'png':
			case 'PNG':
				$resImage	=	@ImageCreateFromPNG( JPATH_SITE.'/'.$location_old );
				break;
			default:
				break;
		}
		if ( ! $resImage ) {
			//...
		}
		//umask(0002);
		if ( count( $options ) ) {
			//for ( $i = 0; $i < $thumb_count; $i++ ) {
				$newWidth	=	$newSize[0];
				$newHeight	=	$newSize[1];
				$format_name	=	'thumb'.$thumb.'_process';
				$width_name		=	'thumb'.$thumb.'_width';
				$height_name	=	'thumb'.$thumb.'_height';
				if ( trim( $options[$format_name] ) ) {
					ob_start();
					$newX	= 	0;
					$newY	=	0;
					$thumbX	=	0;
					$thumbY	=	0;
					if ( ! self::_available_img_dim ( $options[$width_name] ) && ! self::_available_img_dim ( $options[$height_name] ) ) {
						return false;
					}							
					$width	=  ( ! $options[$width_name] && $options[$height_name] ) ? round( $options[$height_name] * $newRatio ) : $options[$width_name];
					$height	=  ( $options[$width_name] && ! $options[$height_name] ) ? round( $options[$width_name] / $newRatio ) : $options[$height_name];
					$ratio	=	$width / $height;
					switch( $options[$format_name] )
					{
						case "addcolor":
							$thumbWidth		=	( $ratio > $newRatio ) ? round( $height * $newRatio ) : $width;
							$thumbHeight	=	( $ratio < $newRatio ) ? round( $width / $newRatio ) : $height;
							$thumbX			=	( $width / 2 ) - ( $thumbWidth / 2 );
							$thumbY			=	( $height / 2 ) - ( $thumbHeight / 2 );
							break;
						case "crop":
							if ( $ratio > $newRatio ) {
								$zoom		=	$newWidth / $width;
								$crop_h		=	floor( $zoom * $height );
								$crop_w		=	$newWidth;
								$crop_x		=	0;
								$crop_y		=	floor( 0.5 * ( $newHeight - $crop_h ) );
							} else {
								$zoom		=	$newHeight / $height;
								$crop_h		=	$newHeight;
								$crop_w		=	floor( $zoom * $width );
								$crop_x		=	floor( 0.5 * ( $newWidth - $crop_w ) );
								$crop_y		=	0;
							}
							$newX			=	$crop_x;
							$newY			=	$crop_y;
							$newWidth		=	$crop_w;
							$newHeight		=	$crop_h;
							$thumbWidth		=	$width;
							$thumbHeight	=	$height;
							$thumbX			=	0;
							$thumbY			=	0;
							break;
						case "crop_dynamic":
							if ( $newWidth > $newHeight ) {
								if ( $ratio > $newRatio ) {
									$zoom		=	$newWidth / $width;
									$crop_h		=	floor( $zoom * $height );
									$crop_w		=	$newWidth;
									$crop_x		=	0;
									$crop_y		=	floor( 0.5 * ( $newHeight - $crop_h ) );
								} else {
									$zoom		=	$newHeight / $height;
									$crop_h		=	$newHeight;
									$crop_w		=	floor( $zoom * $width );
									$crop_x		=	floor( 0.5 * ( $newWidth - $crop_w ) );
									$crop_y		=	0;
								}
								$newX			=	$crop_x;
								$newY			=	$crop_y;
								$newWidth		=	$crop_w;
								$newHeight		=	$crop_h;
								$thumbWidth		=	$width;
								$thumbHeight	=	$height;
								$thumbX			=	0;
								$thumbY			=	0;
							} else {
								if ( $ratio > $newRatio ) {
									$zoom		=	$newWidth / $width;
									$crop_h		=	floor( $zoom * $height );
									$crop_w		=	$newWidth;
									$crop_x		=	0;
									$crop_y		=	floor( 0.5 * ( $newHeight - $crop_h ) );
								} else {
									$zoom		=	$newHeight / $height;
									$crop_h		=	$newHeight;
									$crop_w		=	floor( $zoom * $width );
									$crop_x		=	floor( 0.5 * ( $newWidth - $crop_w ) );
									$crop_y		=	0;
								}
								$newX			=	$crop_x;
								$newY			=	$crop_y;
								$newWidth		=	$crop_h;
								$newHeight		=	$crop_w;
								$thumbWidth		=	$height;
								$thumbHeight	=	$width;
								$width			=	$thumbWidth;
								$height			=	$thumbHeight;
								$thumbX			=	0;
								$thumbY			=	0;
							}
							break;
						case "maxfit":
							$width			=	( $width > $newWidth ) ? $newWidth : $width;
							$height			=	( $height > $newHeight ) ? $newHeight : $height;
							$width			=	( $ratio > $newRatio ) ? round( $height * $newRatio ) : $width;
							$height			=	( $ratio < $newRatio ) ? round( $width / $newRatio ) : $height;
							$thumbWidth		=	$width;
							$thumbHeight	=	$height;
							break;
						case "shrink":
							$width			=	( $width > $newWidth ) ? $newWidth : $width;
							$height			=	( $height > $newHeight ) ? $newHeight : $height;
							$thumbWidth		=	$width;
							$thumbHeight	=	$height;
							break;
						case "shrink_dynamic":
							if ( $newWidth > $newHeight ) {
								$width			=	( $width > $newWidth ) ? $newWidth : $width;
								$height			=	( $height > $newHeight ) ? $newHeight : $height;
								$thumbWidth		=	$width;
								$thumbHeight	=	$height;
							} else {
								$thumbWidth		=	( $height > $newWidth ) ? $newWidth : $height;
								$thumbHeight	=	( $width > $newHeight ) ? $newHeight : $width;
								$width			=	$thumbWidth;
								$height			=	$thumbHeight;
							}
							break;
						case "stretch":
							$thumbWidth		=	$width;
							$thumbHeight	=	$height;
							break;
						case "stretch_dynamic":
							if ( $newWidth > $newHeight ) {
								$thumbWidth		=	$width;
								$thumbHeight	=	$height;
							} else {
								$thumbWidth		=	$height;
								$thumbHeight	=	$width;
								$width			=	$thumbWidth;
								$height			=	$thumbHeight;
							}
							break;
						default:
							break;
					}
					$thumbImage	=	imageCreateTrueColor( $width, $height );
					if ( $newExt == 'png' || $newExt == 'PNG' ) {
						imagealphablending( $thumbImage, false );
					}
					//add color
					if ( $options[$format_name] == 'addcolor' ) {
						$r		=	hexdec( substr( $options['image_color'], 1, 2 ) );
						$g		=	hexdec( substr( $options['image_color'], 3, 2 ) );
						$b		=	hexdec( substr( $options['image_color'], 5, 2 ) );
						$color	=	imagecolorallocate( $thumbImage, $r, $g, $b );
						imagefill( $thumbImage, 0, 0, $color );
					}
					//
					imagecopyresampled( $thumbImage, $resImage, $thumbX, $thumbY, $newX, $newY, $thumbWidth, $thumbHeight, $newWidth, $newHeight );
					
					$file_path		=	substr( $location, 0, strrpos( $location, '/' ) + 1 );
					$old_file_path	=	substr( $location_old, 0, strrpos( $location_old, '/' ) + 1 );
					$old_file_name	=	substr( $location_old, strrpos( $location_old, '/' ) + 1 );
					if ( ! JFolder::exists( JPATH_SITE.'/'.$old_file_path.'_thumb'.$thumb ) ) {
						JFolder::create( JPATH_SITE.'/'.$old_file_path.'_thumb'.$thumb );
						$file_body	=	'<!DOCTYPE html><title></title>';
						JFile::write( JPATH_SITE.'/'.$old_file_path.'_thumb'.$thumb.'/index.html', $file_body );
					}
					$thumbLocation	=	JPATH_SITE.'/'.$old_file_path.'_thumb'.$thumb.'/';
					switch( $newExt ) {
						case 'gif':
						case 'GIF':
							imagegif( $thumbImage ); //imagegif( $thumbImage, $thumbLocation.$old_file_name );
							break;
						case 'jpg':
						case 'JPG':
						case 'jpeg': 
						case 'JPEG': 
							imagejpeg( $thumbImage, NULL, $params['quality_jpeg'] ); //imagejpeg( $thumbImage, $thumbLocation.$old_file_name, $params['quality_jpeg'] );
							break;
						case 'png':
						case 'PNG':
							imagesavealpha( $thumbImage, true );
							$quality_png	=	(int) $params['quality_png'];
							imagepng( $thumbImage, NULL, $params['quality_png'] ); //$process['quality_png'] );//imagepng( $thumbImage, $thumbLocation.$old_file_name, $quality_png );
							break;
						default:
							break;
					}
					$output = ob_get_contents();
					ob_end_clean();
					JFile::write( $thumbLocation.$old_file_name, $output );
					return true;
				} else {
					return false;
				}
			//}
			// ob_end_clean(); 
		}
		// -- Image Process End
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