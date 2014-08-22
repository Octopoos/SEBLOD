<?php
/**
 * @package     Joomla.Platform
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

// CCK_DocumentRendererModule
class CCK_DocumentRendererModule extends CCK_DocumentRenderer
{
	/**
	 * Renders a module script and returns the results as a string
	 *
	 * @param   string  $name      The name of the module to render
	 * @param   array   $attribs   Associative array of values
	 * @param   string  $content   If present, module information from the buffer will be used
	 *
	 * @return  string  The output of the script
	 *
	 * @since   11.1
	 */
	public function render($module, $attribs = array(), $content = null)
	{
	}
}
