<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: rendering_item.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Rendering Item
class CCK_Item
{
	private $me;
	
	var $path;
	var $path_lib;
	
	var $mode;
	var $template;
	var $theme;
	var $type;

	var $fields;
	var $params;
	var $positions;
	
	var $methodRender;

	var $css;
	var $js;
	var $js2;
	
	// __construct
	function __construct( $template = '', $type = '', $pk = 0 )
	{
		$this->config		=	array();
		$this->id			=	'cck'.$pk;
		$this->mode			=	'content';
		$this->path 		= 	JPATH_SITE.'/templates/'.$template;
		$this->path_lib		=	__DIR__;
		$this->template		=	$template;
		$this->theme		=	JFactory::getApplication()->getTemplate();
		$this->type			=	$type;
	}

	// __call
	public function __call( $method, $args )
	{
		$prefix		=	strtolower( substr( $method, 0, 3 ) );
        $property	=	strtolower( substr( $method, 3 ) );
		
		if ( empty( $prefix ) ) {
			return;
		}
		
        if ( $prefix == 'get' ) {
			$count	=	count( $args );
			if ( $count ==  1 ) {
				$fieldname	=	$args[0];

				if ( empty( $property ) ) {
					if ( isset( $this->me[$fieldname] ) ) {
						return $this->me[$fieldname];
					}
				} else {
					if ( isset( $this->me[$fieldname]->$property ) ) {
						return $this->me[$fieldname]->$property;
					}
				}
			} elseif ( $count == 0 ) {
				return;
			} else {
				$fieldname	=	$args[0];

				if ( $count == 2 ) {					
					return empty( $property ) ? @$this->me[$fieldname]->value[$args[1]] : @$this->me[$fieldname]->value[$args[1]]->$property;
				} else {
					return empty( $property ) ? @$this->me[$fieldname]->value[$args[1]]->value[$args[2]] : @$this->me[$fieldname]->value[$args[1]][$args[2]]->$property;
				}
			}
        }
    }
	
	// __get
    public function __get( $property ) {
		if ( isset( $this->$property ) ) {
			return $this->$property;
		}
    }

	// getAuthor
    public function getAuthor()
    {
    	return $this->author;
    }

	// getId
    public function getId()
    {
    	return $this->pid;
    }

    // getPk
    public function getPk()
    {
		return $this->pk;
    }

	// getType
    public function getType()
    {
		return $this->cck;
    }

	// initialize
	public function init() { $this->initialize(); } // (deprecated)
	public function initialize()
	{
		$this->me			=	( isset( $this->fields_list ) && $this->fields_list ) ? $this->fields_list : ( ( isset( $this->fields ) ) ? $this->fields : new stdClass );
		$this->methodRender	=	'onCCK_FieldRenderContent';
	}

	// finalize
	public function finalize( $clear = false )
	{
		$doc	=	JFactory::getDocument();
		
		// Stuff
		if ( $this->css != '' ) {
			$doc->addStyleDeclaration( $this->css );
			$this->css	=	'';
		}
		if ( $this->js != '' ) {
			$doc->addScriptDeclaration( '(function ($){$(document).ready(function(){'.$this->js.'});})(jQuery);' );
			$this->js	=	'';
		}
		if ( $this->js2 != '' ) {
			$doc->addScriptDeclaration( '(function ($){$(window).load(function(){'.$this->js2.'});})(jQuery);' );
			$this->js2	=	'';
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Fields

	// countFields
	public function countFields( $position = '' )
	{
		$count	=	0;
		
		if ( isset( $this->positions[$position] ) ) {
			$count	=	count( $this->positions[$position] );
		}
		
		return $count;	//return $this->isDesc( $position );
	}

	// getFields
	public function getFields( $position = '', $type = '', $prepare = true )
	{
		$fields	=	array();
		
		if ( isset( $this->positions[$position] ) ) {
			if ( $type ) {
				foreach ( $this->positions[$position] as $name ) {
					if ( $type == $this->getType( $name ) ) {
						if ( $prepare === true ) {
							$fields[$name]	=	$this->get( $name );
						} else {
							$fields[]		=	$name;
						}
					}
				}
			} else {
				if ( $prepare === true ) {
					foreach ( $this->positions[$position] as $name ) {
						$fields[$name]	=	$this->get( $name );
					}
				} else {
					return $this->positions[$position];
				}
			}
		}
		
		return $fields;
	}

	// getLabel
	public function getLabel( $fieldname = '', $html = false, $suffix = '' )
	{
		if ( ! isset ( $this->me[$fieldname] ) ) {
			return;
		}
		
		$label	=	trim( $this->me[$fieldname]->label );
		if ( !( $html === true && $label ) ) {
			return trim( $label );
		}
		
		return '<label>'.$label.'</label>';
	}

	// renderField
	public function getField( $fieldname ) { return $this->renderField( $fieldname ); } // (deprecated)
	public function renderField( $fieldname, $options = NULL )
	{
		$field	=	$this->get( $fieldname );
		$html	=	'';
		if ( !$field ) {
			return $html;
		}
		
		if ( $field->display ) {
			$html	=	JCck::callFunc_Array( 'plgCCK_Field'.$field->type, $this->methodRender, array( &$field, &$this->config ) );
			
			if ( $field->display > 1 && $html != '' ) {
				if ( ! $options ) {
					return $html;
				}

				if ( $field->markup == 'none' ) {
					// Label
					$label	=	'';
					if ( $options->get( 'field_label', $this->getStyleParam( 'field_label', 1 ) ) ) {
						$label	=	$this->getLabel( $fieldname, true, ( $field->required ? '*' : '' ) );
						$html	=	$label.$html;
					}
				} elseif ( $this->markup ) {
					// todo
				} else {					
					// Description
					$desc	=	'';
					if ( $this->getStyleParam( 'field_description', 0 ) ) {
						$desc	=	( $field->description != '' ) ? '<div id="'.$this->id.'_desc_'.$fieldname.'" class="cck_desc cck_desc_'.$field->type.'">'.$field->description.'</div>' : '';
					}
					
					// Label
					$label	=	'';
					if ( $options->get( 'field_label', $this->getStyleParam( 'field_label', 1 ) ) ) {
						$label	=	$this->getLabel( $fieldname, true, ( $field->required ? '*' : '' ) );
						$label	=	( $label != '' ) ? '<div id="'.$this->id.'_label_'.$fieldname.'" class="cck_label cck_label_'.$field->type.'">'.$label.'</div>' : '';
					}
					
					// Markup
					$html	=	'<div id="'.$this->id.'_value_'.$fieldname.'" class="cck_value cck_value_'.$field->type.@$field->markup_class.'">'.$html.'</div>';
					$html	=	'<div id="'.$this->id.'_'.$fieldname.'" class="cck_'.$this->mode.'s cck_list cck_'.$field->type.' cck_'.$fieldname.'">'.$label.$html.$desc.'</div>';
				}
			}
		}
		
		return $html;
	}
	
	// retrieveValue
	public function retrieveValue( $fieldname )
	{
		$field	=	$this->get( $fieldname );
		
		if ( !is_object( $field ) || !$field->display ) {
			return '';
		}
		
		return $field->value;
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Positions

	// getPosition
	public function getPosition( $name )
	{
		return ( isset( $this->positions_m[$name] ) ) ? $this->positions_m[$name] : new stdClass;
	}

	// forcePosition
	public function forcePosition( $position = '', $variation = 'none', $height = '', $excluded = array() )
	{
		return $this->renderPosition( $position, 'none', $height, $excluded, true );
	}

	// renderPos
	protected function renderPos( $pos = '' )
	{
		global $user;
		$cck	=&	$this;
		
		if ( $this->isFile( $pos ) ) {
			ob_start();
			include $pos;
			return ob_get_clean();
		}
		
		return;
	}

	// renderPosition
	public function renderPosition( $position, $variation = '', $height = '', $excluded = array(), $force = false )
	{
		$html		=	'';		
		$legend		=	( isset( $this->positions_m[$position]->legend ) && $this->positions_m[$position]->legend ) ? trim( $this->positions_m[$position]->legend ) : '';
		if ( isset( $this->positions_m[$position]->variation_options ) && $this->positions_m[$position]->variation_options != '' ) {
			$options	=	new JRegistry;
			$options->loadString( $this->positions_m[$position]->variation_options );
		} else {
			$options	=	NULL;
		}
		if ( ! $variation ) {
			$variation	=	( isset( $this->positions_m[$position]->variation ) && $this->positions_m[$position]->variation ) ? $this->positions_m[$position]->variation : (string)$this->getStyleParam( 'variation_default', '' );
		}

		$pos2		=	$this->path.'/positions/'.$this->type.'/list/'.$position.'.php';
		$pos1		=	$this->path.'/positions/'.$position.'.php';
		
		if ( $this->isFile( $pos2 ) && ! $force ) {
			$html	.=	$this->renderPos( $pos2 );
		} elseif ( $this->isFile( $pos1 ) && ! $force ) {
			$html	.=	$this->renderPos( $pos1 );
		} else {
			$n	=	$this->countFields( $position );

			if ( $n > 0 ) {
				$names	=	$this->positions[$position];

				if ( count( $excluded ) ) {
					$names	=	array_diff( $names, $excluded );
				}
				foreach ( $names as $name ) {
					$html	.=	$this->renderField( $name, $options );
				}
			} else {
				$legend	=	'';
			}
		}
		
		if ( $html != '' && trim( $variation ) ) {
			$html	=	$this->renderVariation( $variation, $legend, $html, $options, $position, $height );
		}

		// Initialize (only if needed)
		static $cache	=	array();
		if ( !isset( $cache[$this->type] ) ) {
			
			$css	=	$this->getStyleParam( 'rendering_css_core' );
			$css	=	(int)( ( $css != '' ) ? $css : JCck::getConfig_Param( 'css_core', '1' ) );
			if ( $css < 0 ) {
				$css	=	$css * -1;
			}
			if ( $css == 1 || $css == 2 ) {
				if ( $this->isFile( $this->path.'/css/list.css' ) ) {
					JFactory::getDocument()->addStyleSheet( JUri::root( true ).'/templates/'.$this->name.'/css/list.css' );
				} else {
					JFactory::getDocument()->addStyleSheet( JUri::root( true ).'/media/cck/css/cck.list.css' );
				}
			}
			$cache[$this->type]	=	'';
		}

		// Finalize
		$this->finalize( true );

		return $html;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Variations
	
	// renderVariation
	public function renderVariation( $variation, $legend, $content, $options, $position, $height = 0, $markup = true )
	{
		if ( $variation != 'none' ) {
			$file		=	'variations/'.$variation.'/'.$variation.'.php';
			if ( $this->isFile( $this->path.'/'.$file ) ) {
				$file	=	$this->path.'/'.$file;
			} else {
				$file	=	$this->path_lib.'/'.$file;
			}
			
			// Init
			global $user;
			static $loaded	=	array();
			$id				=	$this->id.'_'.$position;
			$app			=	JFactory::getApplication();
			$cck			=	&$this;
			$css			=	'';
			$doc			=	JFactory::getDocument();

			// Prepare
			if ( $this->translate && trim( $legend ) ) {
				$legend	=	trim( $legend );
				$key	=	'COM_CCK_' . str_replace( ' ', '_', $legend );

				if ( JFactory::getLanguage()->hasKey( $key ) ) {
					$legend	=	JText::_( $key );
				}
			}
			if ( is_object( $options ) ) {
				$orientation			=	$options->get( 'field_orientation', 'vertical' );
				$field_width			=	$options->get( 'field_width', '100%' );
				if ( $orientation == 'horizontal' ) {
					$field_width		=	( $field_width == '100%') ? '50%' : $field_width;
					$css				.=	'#'.$id.'.'.$variation.'.'.$orientation.' div.cck_'.$cck->mode.'s { width: '.$field_width.'; }'."\n";
				}
				$field_focus_border_color	=	$options->get( 'field_focus_border_color', '#888888' );
				if ( $field_focus_border_color != '#888888' ) {
					$css				.=	'#'.$id.'.'.$variation.'.'.$orientation.' div.cck_'.$cck->mode.'s input.inputbox:focus, '."\n"
										.	'#'.$id.'.'.$variation.'.'.$orientation.' div.cck_'.$cck->mode.'s textarea.inputbox:focus, '."\n"
										.	'#'.$id.'.'.$variation.'.'.$orientation.' div.cck_'.$cck->mode.'s select.inputbox:focus, '."\n"
										.	'#'.$id.'.'.$variation.'.'.$orientation.' div.cck_'.$cck->mode.'s button.inputbox:focus{ border-color:'.$field_focus_border_color.'!important; }'."\n";
				}
				$field_label_position	=	$options->get( 'field_label_position', 'left' );
				if ( $field_label_position == 'top' ) {
					$css				.=	'#'.$id.'.'.$variation.'.'.$orientation.' div.cck_'.$cck->mode.'s div.cck_value{ float:none; clear:both; }'."\n";
				}
				$field_label_color		=	$options->get( 'field_label_color', '' );
				if ( $field_label_color != '' ) {
					$css				.=	'#'.$id.'.'.$variation.'.'.$orientation.' div.cck_'.$cck->mode.'s div.cck_label{ color:'.$field_label_color.'; }'."\n";
				}
				$field_label_align		=	$options->get( 'field_label_align', 'left' );
				$field_label_width		=	$options->get( 'field_label_width', '145px' );
				$field_label_padding	=	$options->get( 'field_label_padding', '0' );
				$css					.=	'#'.$id.'.'.$variation.'.'.$orientation.' div.cck_label { width:'.$field_label_width.'; text-align:'.$field_label_align
										.	'; padding:'.$field_label_padding.'; }'."\n";
				$hasOptions				=	true;
				$legend2				=	$options->get( 'legend_fieldname', '' );
				if ( $legend2 != '' ) {
					if ( $this->getTypo( $legend2 ) != '' ) {
						$legend	=	$this->getTypo( $legend2 );	
					//} elseif ( $this->getLink( $legend2 ) != '' ) {
					//	$legend	=	$this->getHtml( $legend2 );
					} else {
						$target	=	$this->getTypo_target( $legend2 );
						$legend	=	$this->{'get'.$target}( $legend2 );	
					}
				}
			} elseif ( is_string( $options ) ) {
				$options2	=	$options;
				$options	=	new JRegistry;
				$options->loadString( $options2 );
			} else {
				$options				=	new JRegistry;
				$orientation			=	'vertical';
				$hasOptions				=	false;
				$field_label_width		=	'145px';
			}
			$field_description	=	$this->getStyleParam( 'field_description', 0 );
			if ( $field_description == 3 ) {
				$css	.=	'#'.$id.'.'.$variation.'.'.$orientation.' div.cck_'.$this->mode.'s div.cck_desc{ width: '.$field_label_width.'; }'."\n";
			} elseif ( $field_description == 2 ) {
				$css	.=	'#'.$id.'.'.$variation.'.'.$orientation.' div.cck_'.$this->mode.'s div.cck_desc{ margin-left: '.$field_label_width.'; }'."\n";
			}
			
			// Render
			if ( $this->isFile( $file ) ) {
				ob_start();
				include $file;
				return ob_get_clean();
			}
		}
		
		return $content;
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Style

	// getStyleParam
	public function getStyleParam( $param = '', $default = '' )
	{		
		if ( isset( $this->params[$param] ) ) {
			return $this->params[$param];
		} else {
			return $default;
		}
	}

	// -------- -------- -------- -------- -------- -------- -------- -------- // Stuff
	
	// replaceLive
	public function replaceLive( $attr )
	{
		if ( $attr != '' ) {
			if ( $attr != '' && strpos( $attr, '$cck' ) !== false ) {
				$matches	=	'';
				$search		=	'#\$cck\->(get|retrieve)([a-zA-Z0-9_]*)\( ?\'([a-zA-Z0-9_,]*)\' ?\)(;)?#';
				preg_match_all( $search, $attr, $matches );

				if ( count( $matches[2] ) ) {
					foreach ( $matches[3] as $k=>$fieldname ) {
						$target		=	$matches[2][$k];
						$method		=	( $matches[1][$k] == 'retrieve' ) ? $matches[1][$k] : 'get';
						$get		=	$method.$target;
						$replace	=	$this->$get( $fieldname );
						$attr		=	str_replace( $matches[0][$k], $replace, $attr );
					}
				}
			}
		}

		return $attr;
	}

	// isFile
	public function isFile( $path )
	{
		static $paths	=	array();
		
		if ( !isset( $paths[$path] ) ) {
			$paths[$path]	=	is_file( $path );
		}

		return $paths[$path];
	}

	// addCSS
	public function addCSS( $css )
	{
		if ( $css ) {
			$this->css	.=	$css;
		}
	}
	
	// addJS
	public function addJS( $js, $event = 'ready' )
	{
		if ( $js ) {
			if ( $event == 'load' ) {
				$this->js2	.=	$js;
			} else {
				$this->js	.=	$js;
			}
		}
	}
}
?>
