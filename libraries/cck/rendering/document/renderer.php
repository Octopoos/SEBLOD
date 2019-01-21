<?php
/**
 * @package     Joomla.Platform
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

// CCK_DocumentRenderer
class CCK_DocumentRenderer extends JObject
{
	/**
	 * Reference to the CCK_Document object that instantiated the renderer
	 *
	 * @var    object
	 * @since  11.1
	 */
	protected $_doc = null;

	/**
	 * Renderer mime type
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $_mime = "text/html";

	/**
	 * Class constructor
	 *
	 * @param   object   $doc  A reference to the CCK_Document object that instantiated the renderer
	 *
	 * @since   11.1
	 */
	public function __construct(&$doc)
	{
		$this->_doc = &$doc;
	}

	/**
	 * Renders a script and returns the results as a string
	 *
	 * @param   string   $name     The name of the element to render
	 * @param   array    $array    Array of values
	 * @param   string   $content  Override the output of the renderer
	 *
	 * @return  string   The output of the script
	 * @since   11.1
	 */
	public function render($name, $params = null, $content = null)
	{
	}

	/**
	 * Return the content type of the renderer
	 *
	 * @return  string  The contentType
	 * @since   11.1
	 */
	public function getContentType() {
		return $this->_mime;
	}
}
