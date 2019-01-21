<?php
/**
* @version 			SEBLOD 3.x Core
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				https://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( '_JEXEC' ) or die;

jimport( 'joomla.base.tree' );

// Menu
class JAdminCSSCCKMenu extends JTree
{
	var $_css = null;

	// __construct
	function __construct()
	{
		$this->_root = new JCCKMenuNode('ROOT');
		$this->_current = & $this->_root;
	}

	// addSeparator
	function addSeparator()
	{
		$this->addChild(new JCCKMenuNode(null, null, 'separator', false));
	}

	// renderMenu
	function renderMenu($id = 'cck_menu', $class = 'cck_menu')
	{
		$app	=	JFactory::getApplication();

		if (!$class) $class='cck_menu';

		$depth = 1;

		if(!empty($id)) {
			// TK added class - somehow the class isn't added below
			$id='id="'.$id.'" class="'.$class.'" ';
		}

		if(!empty($class)) {
			$class='class="'.$class.'"';
		}

		/*
		 * Recurse through children if they exist
		 */
		while ($this->_current->hasChildren())
		{
			echo "<ul ".$id." ".$class.">\n";
			foreach ($this->_current->getChildren() as $child)
			{
				$this->_current = & $child;
				$this->renderLevel($depth++);
			}
			echo "</ul>\n";
		}

		if ($this->_css) {
			// Add style to document head
			$doc =  JFactory::getDocument();
			$doc->addStyleDeclaration($this->_css);
		}
	}

	// renderLevel
	function renderLevel($depth)
	{
		$class = '';
		$more	=	( $depth == 1 ) ? ' first' : '';
		if ($this->_current->hasChildren()) {
			$class = ' class="node'.$more.'"';
		}

		if($this->_current->class == 'separator') {
			$class = ' class="separator"';
		}
		if($this->_current->class == 'disabled') {
			$class = ' class="disabled"';
		}
		echo "<li".$class.">";
		if ($this->_current->link != null) {
			$classes =	explode( '||', $this->_current->class );
			$this->_current->class = $classes[0];
			$linkClass2 = ( isset( $classes[1] ) && $classes[1] ) ? ' '.$classes[1] : '';
			$linkClass = $this->getIconClass($this->_current->class);
			if (!empty($linkClass)) {
				$linkClass = ' class="'.$linkClass.$linkClass2.'"';
			}
		}

		if ($this->_current->link != null && $this->_current->target != null) {
			echo "<a".$linkClass." href=\"".$this->_current->link."\" target=\"".$this->_current->target."\" >".$this->_current->title."</a>";
		} elseif ($this->_current->link != null && $this->_current->target == null) {
			echo "<a".$linkClass." href=\"".$this->_current->link."\">".$this->_current->title."</a>";
		} elseif ($this->_current->title != null) {
			echo "<a>".$this->_current->title."</a>\n";
		} else {
			echo "<span></span>";
		}
		while ($this->_current->hasChildren())
		{
			if ($this->_current->class) {
				echo '<ul id="cck_menu-'.strtolower($this->_current->id).'"'.
					' class="cck_menu-component">'."\n";
			} else {
				echo '<ul>'."\n";
			}
			foreach ($this->_current->getChildren() as $child)
			{
				$this->_current = & $child;
				$this->renderLevel($depth++);
			}
			echo "</ul>\n";
		}
		echo "</li>\n";
	}

	// getIconClass
	function getIconClass($identifier)
	{
		$app	=	JFactory::getApplication();

		static $classes;

		if (!is_array($classes)) {
			$classes = array();
		}
		if (!isset($classes[$identifier])) {
			if (substr($identifier, 0, 6) == 'class:') {
				// We were passed a class name
				$class = substr($identifier, 6);
				$classes[$identifier] = "icon-16-$class";
			} else {
				if ($identifier == null) {
					return null;
				}
				// Build the CSS class for the icon
				$class = preg_replace('#\.[^.]*$#', '', basename($identifier));
				$class = preg_replace('#\.\.[^A-Za-z0-9\.\_\- ]#', '', $class);
				/*
				$this->_css  .= "\n.icon-16-$class {\n" .
						"\tbackground: url($identifier) no-repeat;\n" .
						"}\n";
				*/
				$classes[$identifier] = "icon-16-$class";
			}
		}
		return $classes[$identifier];
	}
}

class JCCKMenuNode extends JNode
{
	var $title	=	null;
	var $id		=	null;
	var $link	=	null;
	var $target =	null;
	var $class	=	null;
	var $active	=	false;

	// __construct
	function __construct( $title, $link = null, $class = null, $active = false, $target = null )
	{
		$this->title	=	trim( $title );
		$this->link		=	JFilterOutput::ampReplace( $link );
		$this->class	=	$class;
		$this->active	=	$active;
		$this->id		=	( $this->title != '' ) ? str_replace( ' ', '-', $title ) : 'empty';
		$this->target	=	$target;
	}
}
?>