<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: default.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2018 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

if ( ( (int)JCck::getConfig_Param( 'validation', '3' ) > 1 ) && $this->config['validation'] != '' ) {
	JCckDev::addValidation( $this->config['validation'], $this->config['validation_options'] );
	$js	=	'if (jQuery("#'.$this->form_id.'").validationEngine("validate",task) === true) { JCck.Core.submitForm((task=="save"?"search":task), document.getElementById("'.$this->form_id.'")); }';
} else {
	$js	=	'JCck.Core.submitForm((task=="save"?"search":task), document.getElementById("'.$this->form_id.'"));';
}
$app	=	Factory::getApplication();
$doc	=	Factory::getDocument();
$id		=	str_replace( ' ', '_', trim( $this->pageclass_sfx ) );
$id		=	( $id ) ? 'id="'.$id.'" ' : '';
?>
<?php if ( $this->show_form ) {
$js		=	$this->config['submit'].' = function(task) {'. $js.' };'
		.	'Joomla.submitbutton = function(task, cid)'
		.	'{'
		.	'if (task == "delete") {'
		.			'if (!confirm(Joomla.JText._(\'COM_CCK_CONFIRM_DELETE\'))) {'
		.				'return false;'
		.			'}'
		.		'}'
		.		'jQuery("#'.$this->form_id.'").append(\'<input type="hidden" id="return" name="return" value="'.base64_encode( Uri::getInstance()->toString() ).'">\');'
		.		'JCck.Core.submitForm(task,document.getElementById(\''.$this->form_id.'\'));'
		.	'};'
		.	'';
$doc->addScriptDeclaration( $js );
} ?>
<?php if ( !$this->raw_rendering ) { ?>
<div <?php echo $id; ?>class="cck_page"><div>
<?php } elseif ( trim( $this->pageclass_sfx ) ) { ?>
<div class="<?php echo trim( $this->pageclass_sfx ); ?>">
<?php }
if ( $this->params->get( 'show_page_heading' ) ) {
	echo '<h1>' . ( ( $this->escape( $this->params->get( 'page_heading' ) ) ) ? $this->escape( $this->params->get( 'page_heading' ) ) : $this->escape( $this->params->get( 'page_title' ) ) ) . '</h1>';
}
if ( $this->show_list_title ) {
	$tag		=	$this->tag_list_title;
	$class		=	trim( $this->class_list_title );
	$class		=	$class ? ' class="'.$class.'"' : '';
	echo '<'.$tag.$class.'>' . $this->title . '</'.$tag.'>';
}
if ( $this->show_list_desc && $this->description != '' && $app->input->get( 'tmpl' ) != 'raw' ) {
	$description	=	HTMLHelper::_( 'content.prepare', $this->description );
	$tag_desc		=	'';

	if ( $this->tag_desc == 'div_div' ) {
		$tag_desc	=	'div';
	}
	if ( !( $this->tag_desc == 'p' && strpos( $description, '<p>' ) === false ) ) {
		$this->tag_desc	=	'div';
	}
	if ( !$this->raw_rendering ) {
		$description	=	'<'.$this->tag_desc.' class="cck_page_desc'.$this->pageclass_sfx.'">' . $description . '</'.$this->tag_desc.'>';

		if ( $this->tag_desc == 'div' ) {
			$description	.=	'<div class="clr"></div>';
		}
	} else {
		$class			=	trim( $this->class_desc );
		$class			=	$class ? ' class="'.$class.'"' : '';

		if ( $tag_desc == 'div' ) {
			$description	=	'<div>'.$description.'</div>';
		}
		$description	=	'<'.$this->tag_desc.$class.'>' . $description . '</'.$this->tag_desc.'>';
	}
} else {
	$description	=	'';
}
if ( $this->show_list_desc == 1 && $this->description != '' ) {
	echo $description;
}
if ( $this->show_form ) {
	if ( $this->show_form == 1 ) {
		echo ( $this->config['action'] ) ? $this->config['action'] : '<form action="'.( ( $this->home ) ? Uri::base( true ) : htmlspecialchars( Uri::getInstance()->getPath() ) ).'" autocomplete="off" method="get" id="'.$this->form_id.'" name="'.$this->form_id.'">';

		if ( $this->raw_rendering ) {
			echo $this->form.$this->loadTemplate( 'hidden' );
		} else {
			echo '<div class="cck_page_search'.$this->pageclass_sfx.'">'.$this->form.$this->loadTemplate( 'hidden' ).'</div><div class="clr"></div>';
		}
		if ( !$this->form_wrapper ) {
			echo '</form>';
		}
	} elseif ( $this->show_form == 2 && $this->form_wrapper ) {
		echo ( $this->config['action'] ) ? $this->config['action'] : '<form action="'.( ( $this->home ) ? Uri::base( true ) : htmlspecialchars( Uri::getInstance()->getPath() ) ).'" autocomplete="off" method="get" id="'.$this->form_id.'" name="'.$this->form_id.'">';
	}
}

echo $this->loadTemplate( 'list' );

if ( $this->show_form ) {
	if ( $this->show_form == 2 ) {
		if ( !$this->form_wrapper ) {
			echo ( $this->config['action'] ) ? $this->config['action'] : '<form action="'.( ( $this->home ) ? Uri::base( true ) : htmlspecialchars( Uri::getInstance()->getPath() ) ).'" autocomplete="off" method="get" id="'.$this->form_id.'" name="'.$this->form_id.'">';
		}
		if ( $this->raw_rendering ) { 
			echo $this->form.$this->loadTemplate( 'hidden' );
		} else {
			echo '<div class="clr"></div><div class="cck_page_search'.$this->pageclass_sfx.'">'.$this->form.$this->loadTemplate( 'hidden' ).'</div>';
		}

		echo '</form>';
	} elseif ( $this->show_form == 1 && $this->form_wrapper ) {
		echo '</form>';
	}
}
if ( $this->show_list_desc == 2 && $this->description != '' ) {
	echo $description;
}
?>
<?php if ( !$this->raw_rendering ) { ?>
</div></div>
<?php } elseif ( trim( $this->pageclass_sfx ) ) { ?>
</div>
<?php }
if ( $this->load_ajax || $this->load_resource ) {
	echo $this->loadTemplate( 'more' );
}
