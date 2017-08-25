<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: rendering.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// Rendering
class CCK_Rendering
{
	static $instance;
	static $instance2;
	
	// getInstance
	public static function getInstance( $template = '' )
	{
		if ( $template == JFactory::getApplication()->getTemplate() ) {
			print( 'You should NOT set "'.$template.'" as Default Template.' );
			die;
		}

		$app		=	JFactory::getApplication();
		$instance	=	'instance';

    	if ( isset( $app->cck_idx ) && $app->cck_idx[0] !== false ) {
			$instance	=	'instance2';
		}

		if ( !self::${$instance} ) {
			self::${$instance}	=	new CCK_Rendering();
		}

		return self::${$instance};	
	}
	
	private $me;
	
	var $id;
	var $desc;
	var $form;
	var $mode;
	var $mode_property;
	var $name;
	var $type;
	var $type_infos;
	
	var $markup;
	var $markup_pos;
	var $method;
	var $methodRender;
	var $responsive;
	
	var $path;
	var $path_lib;
	var $params;
	var $positions;
	
	var $grid;
	var $infinite;
	
	var $css;
	var $browser;
	var $debug;
	var $js;
	var $js2;
	var $profiler;
	
	// __call
	public function __call( $method, $args )
	{
		$prefix		=	strtolower( substr( $method, 0, 3 ) );
		$property	=	strtolower( substr( $method, 3 ) );
		
		if ( empty( $prefix ) ) {
			return;
		}
		
        if ( $prefix == 'get' ) {
			$fieldname	=	$args[0];
			$count		=	count( $args );
			if ( $count ==  1 ) {
				if ( empty( $property ) ) {
					if ( isset ( $this->me[$fieldname] ) ) {
						return $this->me[$fieldname];
					}
				} else {
					if ( isset ( $this->me[$fieldname]->$property ) ) {
						return $this->me[$fieldname]->$property;
					}
				}
			} else {
				if ( $count == 2 ) {					
					return empty( $property ) ? @$this->me[$fieldname]->{$this->mode_property}[$args[1]]
											  : @$this->me[$fieldname]->{$this->mode_property}[$args[1]]->$property;
				} else {
					return empty( $property ) ? @$this->me[$fieldname]->{$this->mode_property}[$args[1]]->{$this->mode_property}[$args[2]]
											  : @$this->me[$fieldname]->{$this->mode_property}[$args[1]][$args[2]]->$property;
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
	
	// doDebug
	public function doDebug()
	{		
		return $this->debug;
	}
		
	// isDesc
	protected function isDesc( $position = '' )
	{
		return ( $this->desc && $this->desc == $position ) ? -1 : 0;
	}
	
	// renderDesc
	protected function renderDesc()
	{
		$desc	=	'';
		
		if ( $desc != '' ) {
			JPluginHelper::importPlugin( 'content' );	
			$desc	=	JHtml::_( 'content.prepare', $desc );
		}
		
		return $desc;
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Init
	
	// initialize
	public function init() { $this->initialize(); }
	public function initialize()
	{	
		$app				=	JFactory::getApplication();
		
		$idx				=	'_';
		if ( isset( $app->cck_idx ) ) {
			$app->cck_idx[0]	=	false;

			if ( count( $app->cck_idx ) > 1 ) {
				$idx		=	array_pop( $app->cck_idx );
			}
		}
		$me					=	CCK_Document::getInstance( 'html' );
		$this->me			=	( isset( $me->fields ) ) ? $me->fields : array();
		$this->config		=	array( 'doComputation'=>0, 'mode'=>$me->cck_mode );
		
		$this->id			=	'cck'.( ( (int)$me->pk > 0 ) ? $me->pk : $me->id.'r' );
		$this->desc			=	'';
		$this->mode			=	$me->cck_mode;
		$this->client		=	$me->cck_client;
		$this->name			=	$me->template;
		$this->template		=	$me->template;
		$this->type			=	$me->cck_type;
		$this->type_infos	=	NULL;
		$this->location		=	( $app->isClient( 'administrator' ) ) ? 'admin' : 'site';
		$this->theme		=	$me->theme;
		
		$this->infinite		=	$me->infinite;
		$this->params		=	$me->cck_params;
		$this->path			=	$me->cck_path;
		$this->path_lib		=	__DIR__;
		$this->positions	=	$me->positions;
		$this->positions2	=	array();
		$this->positions_m	=	$me->positions_more;
		
		$this->base			=	JUri::root( true );
		$this->css			=	'';
		$this->js			=	'';
		$this->profiler		=	@$me->profiler;
		$this->profiler_log	=	@$me->profiler_log;
		$this->translate	=	JCck::getConfig_Param( 'language_jtext', 0 );

		// Nested Lists.. yeah!
		if ( isset( $me->list[$idx] ) ) {
			$this->list		=	$me->list[$idx];
		} elseif ( isset( $me->list ) ) {
			$this->list		=	$me->list;
		}

		// Additional parameters (renderItem)
		if ( isset( $me->i_infos ) ) {
			$this->i_infos			=	$me->i_infos;
		}
		if ( isset( $me->i_params ) ) {
			$this->i_params			=	$me->i_params;	
		}
		if ( isset( $me->i_positions ) ) {
			$this->i_positions		=	$me->i_positions;
		}
		if ( isset( $me->i_positions_more ) ) {
			$this->i_positions_more	=	$me->i_positions_more;
		}
		
		if ( ! @$this->params['variation_default'] ) {
			if ( $app->isClient( 'administrator' ) ) {
				$this->params['variation_default']	=	'seb_css3b';
			} else {
				$this->params['variation_default']	=	JCck::getConfig_Param( ( $this->mode == 'form' ? 'site_variation_form' : 'site_variation' ), 'seb_css3' );
			}
		}
		$this->id_attributes	=	( isset( $this->params['rendering_custom_attributes'] ) && $this->params['rendering_custom_attributes'] ) ? ' '.$this->params['rendering_custom_attributes'].' ' : '';
		$this->id_class			=	( isset( $this->params['rendering_css_class'] ) && $this->params['rendering_css_class'] ) ? $this->params['rendering_css_class'].' ' : '';
		$this->item_attributes	=	( isset( $this->params['rendering_item_attributes'] ) && $this->params['rendering_item_attributes'] ) ? ' '.$this->params['rendering_item_attributes'].' ' : '';

		if ( $this->initRendering() === false ) {
			$app	=	JFactory::getApplication();
			$app->enqueueMessage( 'Oops! Template Init. failed.. ; (', 'error' );
			return false;
		}
		$this->initHtml();
		$this->initDebug();

		// Scripts
		JCck::loadjQuery();
		
		// Stylesheets
		$base	=	true;
		$css	=	$this->getStyleParam( 'rendering_css_core' );
		$css	=	(int)( ( $css != '' ) ? $css : JCck::getConfig_Param( 'css_core', '1' ) );
		$doc	=	JFactory::getDocument();
		if ( !$css ) {
			return;
		} elseif ( $css < 0 ) {
			$base	=	false;
			$css	=	$css * -1;
		}
		if ( $base ) {
			$doc->addStyleSheet( $this->base.'/media/cck/css/cck.css' );
			if ( $this->responsive ) {
				$doc->addStyleSheet( $this->base.'/media/cck/css/cck.responsive.css' );
			}
		}
		if ( $css == 1 || ( $css == 2 && $this->mode == 'content' ) || ( $css == 3 && $this->mode == 'form' ) ) {
			if ( $this->client != 'list' ) {
				if ( $this->isFile( $this->path.'/css/'.$this->client.'.css' ) ) {
					$doc->addStyleSheet( $this->base.'/templates/'.$this->name. '/css/'.$this->client.'.css' );
				} else {
					$doc->addStyleSheet( $this->base.'/media/cck/css/cck.'.$this->client.'.css' );
				}
			}
		}
	}
	
	// initRendering
	protected function initRendering()
	{
		switch ( $this->mode ) {
			case 'content':
				$this->method			=	'getValue';
				$this->methodRender		=	'onCCK_FieldRenderContent';
				$this->mode_property	=	'value';
				break;
			case 'form':
				$this->method			=	'getForm';
				$this->methodRender		=	'onCCK_FieldRenderForm';
				$this->mode_property	=	'form';
				break;
			default:
				return false;
				break;
		}
		$this->config['client']			=	$this->client;
		$this->config['computation']	=	array();
		$this->config['rendering_id']	=	$this->id;
		
		// Markup
		$file2	=	$this->path.'/fields/'.$this->type.'/markup.php';
		$file1	=	$this->path.'/fields/markup.php';
		if ( $this->isFile( $file2 ) ) {
			$this->markup	=	'cckMarkup_'.$this->name.'_'.$this->type;
			include_once $file2;
		} elseif ( $this->isFile( $file1 ) ) {
			$this->markup	=	'cckMarkup_'.$this->name;
			include_once $file1;
		}
		$this->responsive	=	( $this->location == 'admin' ) ? 1 : JCck::getConfig_Param( 'responsive', 0 );
		
		return true;
	}
	
	// initHtml
	protected function initHtml()
	{
		$this->grid	=	array( 1=>array( 0=>1, 1=>'100' ),
							   2=>array( 0=>1, 1=>'50', 2=>'50' ),
							   3=>array( 0=>1, 1=>'33f', 2=>'34f', 3=>'33f' ),
							   4=>array( 0=>1, 1=>'25', 2=>'25', 3=>'25', 4=>'25' ),
							   5=>array( 0=>1, 1=>'20', 2=>'20', 3=>'20', 4=>'20', 5=>'20' ),
							   6=>array( 0=>1, 1=>'17f', 2=>'16f', 3=>'17f', 4=>'17f', 5=>'16f', 6=>'17f' ) );
		
		return true;
	}
	
	// initDebug
	protected function initDebug()
	{	
		if ( $this->getStyleParam( 'debug', 0 ) ) {
			$this->debug	=	true;
			if ( @$this->profiler ) {
				$this->profiler_log	=	'<br />'.$this->profiler_log.'<br />';
			} else {
				jimport( 'joomla.error.profiler' );
				$this->profiler		=	new JProfiler();
				$this->profiler_log	=	'<br />';
			}
		}
		
		return true;
	}
	
	// finalize
	public function finalize()
	{
		$app	=	JFactory::getApplication();
		$doc	=	JFactory::getDocument();
		$js		=	'';
		$tmpl	=	$app->input->get( 'tmpl' );

		// Computation
		if ( $this->mode == 'form' && $this->config['doComputation'] ) {
			$format	=	JCck::getConfig_Param( 'computation_format', 0 );
			if ( !$format  ) {
				$format	=	JText::_( 'COM_CCK_COMPUTATION_FORMAT_AUTO' );
			}
			$doc->addScript( $this->base.'/media/cck/js/cck.calculation-3.10.0.min.js' );
			if ( !( $format == '1,234,567.89' || $format == 'COM_CCK_COMPUTATION_FORMAT_AUTO' ) ) {
				if ( $format == '1 234 567.89' ) {
					$search		=	'/(-?\$?)(\d+( \d{3})*(\.\d{1,})?|\.\d{1,})/g';
					$replace	=	'v.replace(/[^0-9.\-]/g, "")';
					$sepD		=	'.';
					$sepT		=	' ';
				} elseif ( $format == '1 234 567,89' ) {
					$search		=	'/(-?\$?)(\d+( \d{3})*(,\d{1,})?|,\d{1,})/g';
					$replace	=	'v.replace(/[^0-9,\-]/g, "").replace(/,/g, ".")';
					$sepD		=	',';
					$sepT		=	' ';
				} elseif ( $format == '1.234.567,89' ) {
					$search		=	'/(-?\$?)(\d+(\.\d{3})*(,\d{1,})?|,\d{1,})/g';
					$replace	=	'v.replace(/[^0-9,\-]/g, "").replace(/,/g, ".")';
					$sepD		=	',';
					$sepT		=	'.';
				} elseif ( $format == '1234567.89' ) {
					$search		=	'/(-?\$?)(\d+(\d{3})*(,\d{1,})?|.\d{1,})/g';
					$replace	=	'v.replace(/[^0-9.\-]/g, "")';
					$sepD		=	',';
					$sepT		=	'';
				} elseif ( $format == '1234567,89' ) {
					$search		=	'/(-?\$?)(\d+(\d{3})*(,\d{1,})?|,\d{1,})/g';
					$replace	=	'v.replace(/[^0-9,\-]/g, "").replace(/,/g, ".")';
					$sepD		=	',';
					$sepT		=	'';
				}
				$formatNumber	=	JCck::getConfig_Param( 'computation_format_out', 0 ) ? 'formatNumber:1, ' : '';
				$doc->addScriptDeclaration( 'jQuery.Calculation.setDefaults({ '.$formatNumber.'sepDecimals:"'.$sepD.'", sepThousands:"'.$sepT.'", reNumbers:'.$search.', cleanseNumber:function (v){ return '.$replace.'; } });' );
			} elseif( JCck::getConfig_Param( 'computation_format_out', 0 ) ) {
				$doc->addScriptDeclaration( 'jQuery.Calculation.setDefaults({ formatNumber:1 });' );
			}
		}
		if ( count( $this->config['computation'] ) ) {
			$computation	=	'';
			foreach ( $this->config['computation'] as $event=>$compute ) {
				$ids		=	array();
				if ( count( $compute ) ) {
					foreach ( $compute as $k=>$v ) {
						$computation	.=	$v['js'].' ';
						$ids_k			=	explode( ',', $v['_'] );
						$ids			=	array_merge( $ids, $ids_k );
					}
				}
				$ids	=	implode( ', ', array_unique( $ids ) );
				if ( $event != 'none' ) {
					$this->js	.=	'$("'.$ids.'").bind("'.$event.'", JCck.Core.recalc);';
				}
			}
			$js				=	'JCck.Core.recalc = function() {'.$computation.'}';
			$this->js		.=	'JCck.Core.recalc();';
		}
		
		// Stuff
		if ( $this->css != '' ) {
			if ( $tmpl == 'raw' ) {
				echo '<style type="text/css">'.$this->css.'</style>';
			} else {
				$doc->addStyleDeclaration( $this->css );
			}			
		}
		if ( $this->js != '' ) {
			$js		=	'(function ($){'.$js."\n".'$(document).ready(function(){'.$this->js.'});})(jQuery);';

			if ( $tmpl == 'raw' ) {
				echo '<script type="text/javascript">'.$js.'</script>';
			} else {
				$doc->addScriptDeclaration( $js );
			}			
		}
		if ( $this->js2 != '' ) {
			$js		=	'(function ($){$(window).load(function(){'.$this->js2.'});})(jQuery);';

			if ( $tmpl == 'raw' ) {
				echo '<script type="text/javascript">'.$js.'</script>';
			} else {
				$doc->addScriptDeclaration( $js );
			}
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Fields
	
	// checkConditional
	public function checkConditional( $fieldname, $value )
	{
		$values	=	explode( ',', $value );
		$value	=	$this->getValue( $fieldname );
		
		if ( is_array( $value ) ) {
			if ( array_diff( $value, $values ) ) {
				if ( array_diff( $values, $value ) ) {
					return 'style="display: none;"';					
				}
			}
		} else {
			if ( !in_array( $value, $values ) ) {
				return 'style="display: none;"';
			}
		}
		
		return '';
	}
	
	// countFields
	public function countFields( $position = '', $siblings = false )
	{
		$count	=	0;
		
		if ( $siblings === true ) {
			if ( $position == 'body' ) {
				$positions	=	array( 'mainbody', 'topbody', 'bottombody', 'sidebody-a', 'sidebody-b' );
				foreach ( $positions as $p ) {
					if ( isset( $this->positions[$p] ) ) {
						$count++;
					}
				}
			} else {
				for ( $i = 0; $i < 6; $i++ ) {
					$p	=	chr( $i + 97 );
					if ( isset( $this->positions[$position.'-'.$p] ) ) {
						$count++;
					}
				}
			}
		} else {
			if ( isset( $this->positions[$position] ) ) {
				$count	=	count( $this->positions[$position] );
			}
		}
		
		return $count;	//return $this->isDesc( $position );
	}
		
	// doExist
	public function doExist( $fieldname = '' )
	{
		if ( isset( $this->me[$fieldname] ) ) {
			return true;
		}
		
		return false;
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
		if ( $this->mode_property == 'value' ) {
			$label	=	'<label>'.$label.'</label>';
		} else {
			if ( $suffix ) {
				if ( $label != '&nbsp;' ) {
					$label	.=	'<span class="star"> '.$suffix.'</span>';
				}
			}
			if ( $label ) {
				$label	=	'<label for="'.$this->me[$fieldname]->name.'">'.$label.'</label>';
			}
		}
		
		return $label;
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
	
	// renderField
	public function renderField( $fieldname, $options = NULL )
	{
		$field		=	$this->get( $fieldname );
		$html		=	'';
		if ( !$field ) {
			return $html;
		}
		
		if ( $field->display ) {
			$html	=	JCck::callFunc_Array( 'plgCCK_Field'.$field->type, $this->methodRender, array( &$field, &$this->config ) );
			
			if ( $field->display > 1 && $html != '' ) {
				if ( ! $options ) {
					$options	= new JRegistry;
				}

				if ( $field->markup == 'none' ) {
					if ( $this->methodRender == 'onCCK_FieldRenderForm' ) {
						// Conditional
						if ( $field->conditional ) {
							$this->setConditionalStates( $field );
						}
					}

					// Label
					$label	=	'';
					if ( $options->get( 'field_label', $this->getStyleParam( 'field_label', 1 ) ) ) {
						$label	=	$this->getLabel( $fieldname, true, ( $field->required ? '*' : '' ) );
						$html	=	$label.$html;
					}
				} elseif ( $this->markup ) {
					$call	=	$this->markup;
					$html	=	$call( $this, $html, $field, $options );
				} else {
					if ( $this->methodRender == 'onCCK_FieldRenderForm' ) {
						// Computation
						if ( @$field->computation ) {
							$this->setComputationRules( $field );
						}
						// Conditional
						if ( @$field->conditional ) {
							$this->setConditionalStates( $field );
						}
					}
					
					// Description
					$desc	=	'';
					if ( $this->getStyleParam( 'field_description', 0 ) ) {
						if ( $field->description != '' ) {
							if ( $this->getStyleParam( 'field_description', 0 ) == 5 ) {
								JHtml::_( 'bootstrap.popover', '.hasPopover', array( 'container'=>'body', 'html'=>true, 'trigger'=>'hover' ) );
								$desc	=	'<div class="hasPopover" data-placement="top" data-animation="false" data-content="'.htmlspecialchars( $field->description ).'" title="'.htmlspecialchars( $field->label ).'"><span class="icon-help"></span></div>';
							} else {
								$desc	=	$field->description;
							}
							$desc	=	 '<div id="'.$this->id.'_desc_'.$fieldname.'" class="cck_desc cck_desc_'.$field->type.'">'.$desc.'</div>';
						}
					}
					
					// Label
					$label	=	'';
					if ( $options->get( 'field_label', $this->getStyleParam( 'field_label', 1 ) ) ) {
						$label	=	$this->getLabel( $fieldname, true, ( $field->required ? '*' : '' ) );
						$label	=	( $label != '' ) ? '<div id="'.$this->id.'_label_'.$fieldname.'" class="cck_label cck_label_'.$field->type.'">'.$label.'</div>' : '';
					}
					
					// Markup
					$html	=	'<div id="'.$this->id.'_'.$this->mode_property.'_'.$fieldname.'" class="cck_'.$this->mode_property.' cck_'.$this->mode_property.'_'.$field->type.@$field->markup_class.'">'.$html.'</div>';
					$html	=	'<div id="'.$this->id.'_'.$fieldname.'" class="cck_'.$this->mode.'s cck_'.$this->client.' cck_'.$field->type.' cck_'.$fieldname.'">'.$label.$html.$desc.'</div>';
				}
			}
		}
		
		return $html;
	}
	
	// retrieveValue
	public function retrieveValue( $fieldname )
	{
		$field	=	$this->get( $fieldname );
		
		if ( !$field->display ) {
			return '';
		}
		
		return $field->value;
	}
	
	// setComputationRules
	public function setComputationRules( &$field )
	{
		$computation			=	new JRegistry;
		$computation->loadString( $field->computation_options );
		$computation_options	=	$computation->toObject();
		
		if ( $computation_options->calc == 'custom' ) {
			$computed		=	'';
			$computations	=	explode( ',', $field->computation );
			if ( count( $computations ) ) {
				foreach ( $computations as $k=>$v ) {
					$computed	.=	chr( 97 + $k ).':$("'.$v.'")'.',';
				}
				$computed		=	substr( $computed, 0, -1 );
			}
			$event		=	@$computation_options->event ? $computation_options->event : 'keyup';
			$targets	=	@$computation_options->targets ? json_encode( $computation_options->targets ) : '[]';
			$format		=	'';
			if ( $computation_options->format == 'toFixed' ) {
				$format	=	'.'.$computation_options->format.'('.$computation_options->precision.')';
			} elseif ( $computation_options->format ) {
				$format	=	'.'.$computation_options->format.'()';
			}
			if ( @$computation_options->recalc ) {
				$this->config['computation'][$event][]	=	array( '_'=>$field->computation,
																   'js'=>'$("#'.$field->name.'").calc( "'.$computation_options->custom.'", {'.$computed.'}, '
																										 .$targets.', function (s){return s'.$format.';} );' );
			} else {
				$this->addScriptDeclaration( '(function ($){JCck.Core.recalc_'.$field->name.' = function() {'
					.'$("#'.$field->name.'").calc( "'.$computation_options->custom.'", {'.$computed.'}, '.$targets.', function (s){return s'.$format.';} );}'.'})(jQuery);' );
				if ( $event != 'none' ) {
					$this->addJS( '$("'.$field->computation.'").bind("'.$event.'", JCck.Core.recalc_'.$field->name.'); JCck.Core.recalc_'.$field->name.'();' );
				}
			}
		} else {
			$event		=	@$computation_options->event ? $computation_options->event : 'keyup';
			$targets	=	@$computation_options->targets ? ', '.json_encode( $computation_options->targets ) : '';
			if ( @$computation_options->recalc ) {
				$this->config['computation'][$event][]	=	array( '_'=>$field->computation,
																   'js'=>'$("'.$field->computation.'").'
																		.$computation_options->calc.'("'.$event.'", "#'.$field->name.'"'.$targets.');' );
			} else {
				$this->addJS( '$("'.$field->computation.'").'.$computation_options->calc.'("'.$event.'", "#'.$field->name.'"'.$targets.');' );
			}
		}
		$this->config['doComputation']	=	1;
	}
	
	// setConditionalStates
	public function setConditionalStates( &$field )
	{	
		if ( $field->markup == 'none' ) {
			$field->conditional_options	=	str_replace( array( ' #form#', '#form#' ), '', $field->conditional_options );
			$selector					=	$field->name;
		} else {
			$field->conditional_options	=	str_replace( '#form#', '#'.$field->name, $field->conditional_options );
			$selector					=	$this->id.'_'.$field->name;
		}
		$this->addJS( '$("#'.$selector.'").conditionalStates('.$field->conditional_options.');' );
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Items
	
	// getItem
	public function getItem( $pk )
	{
		return ( isset( $this->list[$pk] ) ) ? $this->list[$pk] : NULL;
	}	
	
	// getItems
	public function getItems()
	{
		return $this->list;
	}
	
	// renderItem
	public function renderItem( $pk, $params = array() )
	{
		$doc			=	CCK_Document::getInstance( 'html' );
		$doc->fields	=	$this->list[$pk]->fields;
		
		$doc->finalize( 'content', $this->type, 'item', $this->i_positions, $this->i_positions_more, $this->i_infos, $this->list[$pk]->pid ); 	
		
		return $doc->render( false, $this->i_params );
	}
	
	// renderItemField
	public function renderItemField( $pk, $params = array() )
	{
		$fields	=	$this->list[$pk]->fields;
		$name	=	$params['field_name'];
		$target	=	$params['target'];
		
		$class	=	'';
		$res	=	'';
		$type	=	'text';
		
		if ( isset( $fields[$name] ) ) {
			$res	=	$fields[$name]->$target;
			$type	=	$fields[$name]->type;
			$class	=	$fields[$name]->markup_class;
		}
		
		return '<div><div class="cck_contents cck_item cck_'.$type.'"><div class="cck_value cck_value_'.$type.$class.'">'.$res.'</div></div></div>';
	}
	
	// renderItemLink
	public function renderItemLink( $pk )
	{
		$res	=	'';
		$items	=	$this->list[$pk];
		
		if ( isset( $items ) ) {
			$res	=	$item->location .':'. $pk . ' (coming soon)';
		}
		
		return '<div class="cck_contents cck_item cck_text"><div class="cck_value cck_value_text">'.$res.'</div></div>';
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Positions
	
	// forcePosition
	public function forcePosition( $position = '', $variation = 'none', $height = '', $excluded = array() )
	{
		return $this->renderPosition( $position, 'none', $height, $excluded, true );
	}

	// getPosition
	public function getPosition( $name )
	{
		return ( isset( $this->positions_m[$name] ) ) ? $this->positions_m[$name] : new stdClass;
	}

	// getPositions
	public function getPositions()
	{
		if ( !count( $this->positions2 ) && count( $this->positions ) ) {
			foreach ( $this->positions as $k=>$v ) {
				$this->positions2[$k]	=&	$this->positions_m[$k];
			}
		}
		
		return $this->positions2;
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
	public function renderPosition( $position = '', $variation = '', $height = '', $excluded = array(), $force = false )
	{
        $html	=	'';
		if ( ! $variation ) {
			$variation	=	( isset( $this->positions_m[$position]->variation ) && $this->positions_m[$position]->variation ) ? $this->positions_m[$position]->variation : $this->params['variation_default'];
		}
		
		if ( isset( $this->positions_m[$position]->variation_options ) && $this->positions_m[$position]->variation_options != '' ) {
			$options	=	new JRegistry;
			$options->loadString( $this->positions_m[$position]->variation_options );
		} else {
			$options	=	NULL;
		}
		
		$legend		=	( isset( $this->positions_m[$position]->legend ) && $this->positions_m[$position]->legend ) ? trim( $this->positions_m[$position]->legend ) : (( $this->doDebug() ) ? $position : '' );
		$pos2		=	$this->path.'/positions/'.$this->type.'/'.$this->client.'/'.$position.'.php';
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
			$html		=	$this->renderVariation( $variation, $legend, $html, $options, $position, $height );
		}
		
		return $html;
	}
	
	// renderPositions
	public function renderPositions( $position = '', $variation = '', $n = 0, $w = '', $h = '' )
	{
		$doc		=	JFactory::getDocument();
		$html		=	'';
		$positions	=	array();

		for ( $i = 0; $i < 6; $i++ ) {
			$p		=	chr( $i + 97 );
			$pos	=	$position.'-'.$p;
			$width	=	'';
			$height	=	'';
			if ( @$this->positions_m[$pos]->width != '' ) {
				if ( strpos( $this->positions_m[$pos]->width, 'px' ) !== false ) {
					$width	=	$this->positions_m[$pos]->width;
					$doc->addStyleDeclaration( '.cck-w'.$width.'{width:'.$width.';}' );
				} else {
					$width	=	str_replace( '%', '', $this->positions_m[$pos]->width);
				}
			} else {
				$width	=	$w;
			}
			if ( @$this->positions_m[$pos]->height != '' ) {
				//$height	=	'cck-h'.$this->positions_m[$pos]->height;
				//$doc->addStyleDeclaration( '.cck-h'.$this->positions_m[$pos]->height.'{height:'.$this->positions_m[$pos]->height.';}' );
				$height	=	$this->positions_m[$pos]->height;
			} elseif ( $h != '' ) {
				//$height	=	'cck-h'.$h;
				//$doc->addStyleDeclaration( '.cck-h'.$h.'{height:'.$h.';}' );
				$height	=	$h;
			}
			if ( $this->countFields( $pos ) ) {
				$pos_html	=	$this->renderPosition( $pos, '', $height );
				if ( $pos_html != '' ) {
					$positions[$pos]	=	array( 'html'=>$pos_html, 'width'=>$width );
				}
            }
		}
		$n	=	count( $positions );
		foreach ( $positions as $k=>$p ) {
			$html	.=	'<div class="cck-w'. $this->w_grid( $n, $p['width'] ).' cck-fl cck-ptb">'
					.	'<div class="cck-plr">'
					.	$p['html']
					.	'</div>'
					.	'</div>'
					;
		}
		if ( $variation ) {
			$legend		=	'';	//Todo
			$options	=	new JRegistry;
			$html		=	$this->renderVariation( $variation, $legend, $html, $options, $position.'_line', '', false );
		}
		if ( $html != '' ) {
			$html	=	'<div class="cck-line-'.$position.'">'
					.	$html
					.	'<div class="clr"></div>'
					.	'</div>'
					;
			// Height
			if ( $this->getStyleParam( 'position_'.$position, 1 ) == 2 ) {
				$js	=	'$("#'.$this->id.' .cck-line-'.$position.' > div:not(.clr)").deepestHeight();';
				$js	.=	'$("#'.$this->id.' .cck-line-'.$position.' div.'.$this->id.'-deepest").deepestHeight();';
				$this->addJS( $js );
			}
		}
		
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
				if ( !( $legend[0] == '<' || strpos( $legend, ' / ' ) !== false ) ) {
					$legend	=	trim( $legend );
					$key	=	'COM_CCK_' . str_replace( ' ', '_', $legend );

					if ( JFactory::getLanguage()->hasKey( $key ) ) {
						$legend	=	JText::_( $key );
					}
				}
			}
			if ( is_object( $options ) ) {
				if ( strpos( $position, '_line' ) !== false ) {
					//
				} else {
					$orientation			=	$options->get( 'field_orientation', 'vertical' );
					$field_width			=	$options->get( 'field_width', '100%' );
					if ( $orientation == 'horizontal' ) {
						$field_width		=	( $field_width == '100%') ? '50%' : $field_width;
						$css				.=	'#'.$id.'.'.$variation.'.'.$orientation.' div.cck_'.$cck->mode.'s { width: '.$field_width.'; }'."\n";
					}
					$field_focus_border_color	=	trim( $options->get( 'field_focus_border_color', '' ) );
					if ( $field_focus_border_color != '' && $field_focus_border_color != '#888888' ) {
						$css				.=	'#'.$id.'.'.$variation.'.'.$orientation.' div.cck_'.$cck->mode.'s input.inputbox:focus, '."\n"
											.	'#'.$id.'.'.$variation.'.'.$orientation.' div.cck_'.$cck->mode.'s textarea.inputbox:focus, '."\n"
											.	'#'.$id.'.'.$variation.'.'.$orientation.' div.cck_'.$cck->mode.'s select.inputbox:focus, '."\n"
											.	'#'.$id.'.'.$variation.'.'.$orientation.' div.cck_'.$cck->mode.'s button.inputbox:focus{ border-color:'.$field_focus_border_color.'!important; }'."\n";
					}
					$field_label_position	=	$options->get( 'field_label_position', 'left' );
					if ( $field_label_position == 'top' ) {
						$css				.=	'#'.$id.'.'.$variation.'.'.$orientation.' div.cck_'.$cck->mode.'s div.cck_'.$cck->mode_property.'{ float:none; clear:both; }'."\n";
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
			if ( $field_description == 4 || $field_description == 5 ) {
				$css	.=	'#'.$id.'.'.$variation.'.'.$orientation.' div.cck_'.$this->mode.'s div.cck_desc{clear:none; float:left;}'."\n";
			} elseif ( $field_description == 3 ) {
				$css	.=	'#'.$id.'.'.$variation.'.'.$orientation.' div.cck_'.$this->mode.'s div.cck_desc{width:'.$field_label_width.';}'."\n";
			} elseif ( $field_description == 2 ) {
				$css	.=	'#'.$id.'.'.$variation.'.'.$orientation.' div.cck_'.$this->mode.'s div.cck_desc{margin-left:'.$field_label_width.';}'."\n";
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

	// -------- -------- -------- -------- -------- -------- -------- -------- // Style & Template

	// getStyleParam
	public function getStyleParam( $param = '', $default = '' )
	{		
		if ( isset( $this->params[$param] ) ) {
			return $this->params[$param];
		} else {
			return $default;
		}
	}

	// getTemplateParam
	public function getTemplateParam( $param = '', $default = '' )
	{		
		static $templates = array();

		if ( !isset( $templates[$this->template] ) ) {
			$templates[$this->template]	=	JCckDatabase::loadResult( 'SELECT options FROM #__cck_core_templates WHERE name = "'.$this->template.'"' );
			$templates[$this->template]	=	( $templates[$this->template] != '' ) ? json_decode( $templates[$this->template], true ) : array();
		}

		if ( isset( $templates[$this->template][$param] ) ) {
			return $templates[$this->template][$param];
		} else {
			return $default;
		}
	}
	
	// -------- -------- -------- -------- -------- -------- -------- -------- // Type

	// getTypeInfos
	public function getTypeInfos()
	{
		if ( !$this->type_infos ) {
			$this->type_infos	=	'';
		}
		
		return $this->type_infos;
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
	
	// getBrowser
	public function getBrowser( $property = 'name' )
	{
		if ( ! $this->browser ) {
			$browser	=	JBrowser::getInstance();
			$this->browser->name	=	$browser->getBrowser();
			$this->browser->agent	=	$browser->getAgentString();
			$this->browser->version	=	$browser->getVersion();
			//todo: process to get the right info from agent...!
		}
		
		return @$this->browser->$property;
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

	// isGoingtoLoadMore
	public function isGoingtoLoadMore()
	{
		if ( $this->isLoadingMore() == -1 ) {
			return true;
		}

		return $this->infinite;
	}

	// isLoadingMore
	public function isLoadingMore()
	{
		$app	=	JFactory::getApplication();
		
		if ( $app->input->get( 'format' ) == 'raw' ) {
			$infinite	=	$app->input->getInt( 'infinite' );

			if ( $infinite == -1 ) {
				return -1;
			} elseif ( $infinite ) {
				return 1;
			}
		}
		
		return 0;
	}

	// fakeModule (deprecated)
	public function fakeModule( $legend, $content )
	{
		$module				=	new stdClass;
		$module->showtitle	=	trim( $legend ) != '' ? 1 : 0;
		$module->title		=	$legend;
		$module->content	=	$content;
		
		return $module;
	}
	
	// w_grid
	public function w_grid( $n, $default = '' )
	{
		if ( $default ) {
			return $default;
		}
		$w	=	$this->grid[$n][$this->grid[$n][0]++];
		if ( $this->grid[$n][0] == count( $this->grid[$n] ) ) {
			$this->grid[$n][0]	=	1;
		}
		return $w;
	}

	// w
	public function w( $position )
	{
		return ( @$this->positions_m[$position]->width != '' ) ? $this->positions_m[$position]->width : '';
	}

	// h
	public function h( $position )
	{	
		return ( @$this->positions_m[$position]->height != '' ) ? $this->positions_m[$position]->height : '';
	}
	
	// setHeight
	public function setHeight( $height, $id, $class = '', $markup = '' )
	{
		if ( $height ) {
			$class	=	( $class ) ? ' .'.$class : '';
			JFactory::getDocument()->addStyleDeclaration( '#'.$id.$class.' '.$markup.'{height:'.$height.';}' );
		}
	}
	
	// addScript
	public function addScript( $url, $type = 'text/javascript', $defer = false, $async = false )
	{
		JFactory::getDocument()->addScript( $url, $type, $defer, $async );
	}

	// addScriptDeclaration
	public function addScriptDeclaration( $js, $event = '' )
	{	
		if ( $js ) {
			if ( $event == 'ready' ) {
				$js	=	'(function ($){$(document).ready(function(){'.$js.'});})(jQuery);';
			} elseif ( $event == 'load' ) {
				$js	=	'(function ($){$(window).load(function(){'.$js.'});})(jQuery);';
			}
			JFactory::getDocument()->addScriptDeclaration( $js );
		}
	}

	// addStyleSheet
	public function addStyleSheet( $url, $type = 'text/css', $media = null, $attribs = array() )
	{
		JFactory::getDocument()->addStyleSheet( $url, $type, $media, $attribs );
	}

	// addStyleDeclaration
	public function addStyleDeclaration( $css )
	{
		if ( $css ) {
			JFactory::getDocument()->addStyleDeclaration( $css );
		}
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
