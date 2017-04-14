<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: cckjs.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2017 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

// JFormField
class JFormFieldCCKjs extends JFormField
{
	protected $type	=	'CCKjs';

	// getLabel
	protected function getLabel()
	{
		return;
	}

	// getInput
	protected function getInput()
	{
		$format	=	(string)$this->element['js_format'];
		if ( $format != 'raw' ) {
			JCck::loadjQuery( true, true, array( 'cck.dev-3.7.0.min.js', 'jquery.json.min.js', 'jquery.ui.effects.min.js' ) );
		}
		
		return $this->_addScripts( $this->id, array( 'appendTo'=>(string)$this->element['js_appendto'],
													 'isVisibleWhen'=>(string)$this->element['js_isvisiblewhen'],
													 'isDisabledWhen'=>(string)$this->element['js_isdisabledwhen'] ), $format );
	}
	
	// _addScripts
	protected function _addScripts( $id, $events, $format )
	{
		$app	=	JFactory::getApplication();
		$doc	=	JFactory::getDocument();
		$js		=	'';		
		$js2	=	'';
		$js3	=	'';
		$option	=	$app->input->get( 'option' );
		$tweak	=	false;
		$tweaks	=	array();

		if ( $option == 'com_menus' || $option == 'com_modules' ) {
			$tweak		=	true;
		}

		// appendTo
		if ( $events['appendTo'] ) {
			$move		=	explode( '=', $events['appendTo'] );
			$element	=	$move[0];
			$fragments	=	explode( ',', $move[1] );
			$id			=	$this->id;
			$js			=	'';
			$js2		=	'';
			
			if ( count( $fragments ) ) {
				foreach ( $fragments as $fragment ) {
					$fragment	=	trim( $fragment );
					if ( strpos( $fragment, 'J(' ) !== false ) {
						if ( $tweak ) {
							//
						} else {
							$fragment		=	substr( $fragment, 2, -1 );
							if ( strpos( $fragment, '|' ) !== false ) {
								$frag		=	explode( '|', $fragment );
								$frag[1]	=	' '.$frag[1];
							} else {
								$frag		=	array( 0=>$fragment, 1=>'' );
							}
							$frag[0]	=	JText::_( 'COM_CCK_' . str_replace( ' ', '_', $frag[0] ) );
							$f			=	'<div class=\'textdesc'.$frag[1].'\'>'.$frag[0].'</div>';
							$js			.=	'.append("'.$f.'")';
						}
					} else {
						if ( $tweak ) {
							$tweaks[]	=	$fragment;
						} else {
							$js			.=	'.append($("'.$fragment.'"))';
							$js2		.=	'if ($("'.$fragment.'-lbl")) { $("'.$fragment.'-lbl").parent().remove(); }';
						}
					}
				}
			}
			if ( $js != '' && !$tweak ) {
				$js	=	' $("#'.$element.'").parent().append("<div id=\"'.$id.'-more\"></div>"); $("div#'.$id.'-more")'.$js.'; '.$js2;
			}
		}
		
		// isVisibleWhen
		if ( $events['isVisibleWhen'] ) {
			$e	=	explode( '=', $events['isVisibleWhen'] );
			if ( isset( $e[1] ) ) {
				if ( count( $tweaks ) ) {
					foreach ( $tweaks as $tw ) {
						$js2	.=	'$("'.$tw.'").isVisibleWhen('.$e[1].');';	
					}
				} else {
					$js2		=	'$("'.$e[0].'").isVisibleWhen('.$e[1].');';	
				}
			} else {
				if ( count( $tweaks ) ) {
					foreach ( $tweaks as $tw ) {
						$js2	=	'$("'.$tw.'").isVisibleWhen('.$e[0].');';
					}
				} else {
					$js2		=	'$("#'.$id.'").isVisibleWhen('.$e[0].');';
				}
			}
		}
		
		// isDisabledWhen
		if ( $events['isDisabledWhen'] ) {
			$e	=	explode( '=', $events['isDisabledWhen'] );
			if ( isset( $e[1] ) ) {
				$js3	=	'$("'.$e[0].'").isDisabledWhen('.$e[1].');';
			} else {
				$js3	=	'$("#'.$id.'").isDisabledWhen('.$e[0].');';
			}
		}
		
		// Set
		if ( $js || $js2 || $js3 ) {
			$js	=	'jQuery(document).ready(function($){'.$js.' '.$js2.' '.$js3.'});';
			
			if ( $format == 'raw' ) {
				return '<script type="text/javascript">'.$js.'</script>';
			} else {
				$doc->addScriptDeclaration( $js );
			}
		}
		
		return;
	}
}
?>