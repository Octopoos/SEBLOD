<?php
/**
 * @package     Joomla.Platform
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

jimport( 'joomla.application.module.helper' );
jimport( 'cck.rendering.document.document' );

// CCK_DocumentHTML
class CCK_DocumentHTML extends CCK_Document
{
	/**
	 * Name of the template
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $template = null;

	/**
	 * Base url
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $baseurl = null;

	/**
	 * Array of template parameterss
	 *
	 * @var    array
	 * @since  11.1
	 */
	public $params = null;

	/**
	 * File name
	 *
	 * @var    array
	 * @since  11.1
	 */
	public $_file = null;

	/**
	 * String holding parsed template
	 *
	 * @var    string
	 * @since  11.1
	 */
	protected $_template = '';

	/**
	 * Array of parsed template JDoc tags
	 *
	 * @var    array
	 * @since  11.1
	 */
	protected $_template_tags = array();

	/**
	 * Integer with caching setting
	 *
	 * @var    integer
	 * @since  11.1
	 */
	protected $_caching = null;

	/**
	 * Class constructor
	 *
	 * @param   array  $options  Associative array of options
	 *
	 * @since   11.1
	 */
	public function __construct($options = array()) // !
	{
		parent::__construct($options);

		// Set document type
		$this->_type = 'html';
	}
	
	/**
	 * Get the contents of a document include
	 *
	 * @param   string  $type     The type of renderer
	 * @param   string  $name     The name of the element to render
	 * @param   array   $attribs  Associative array of remaining attributes.
	 *
	 * @return  The output of the renderer
	 *
	 * @since   11.1
	 */
	public function getBuffer($type = null, $name = null, $attribs = array())
	{
		// If no type is specified, return the whole buffer
		if ($type === null) {
			return parent::$_buffer;
		}

		$result = null;
		if (isset(parent::$_buffer)) {
			return parent::$_buffer[$type][$name];
		}

		// If the buffer has been explicitly turned off don't display or attempt to render
		if ($result === false) {
			return null;
		}

		$renderer = $this->loadRenderer($type);
		if ($this->_caching == true && $type == 'modules') {
			$cache = JFactory::getCache('com_modules', '');
			$hash = md5(serialize(array($name, $attribs, $result, $renderer)));
			$cbuffer = $cache->get('cbuffer_'.$type);

			if (isset($cbuffer[$hash])) {
				return JCache::getWorkarounds($cbuffer[$hash], array('mergehead' => 1));
			} else {

				$options = array();
				$options['nopathway'] = 1;
				$options['nomodules'] = 1;
				$options['modulemode'] = 1;

				$this->setBuffer($renderer->render($name, $attribs, $result), $type, $name);
				$data = parent::$_buffer[$type][$name];

				$tmpdata = JCache::setWorkarounds($data, $options);


				$cbuffer[$hash] = $tmpdata;

				$cache->store($cbuffer, 'cbuffer_'.$type);
			}

		} else {
			$this->setBuffer($renderer->render($name, $attribs, $result), $type, $name);
		}

		return parent::$_buffer[$type][$name];
	}

	/**
	 * Set the contents a document includes
	 *
	 * @param   string  $content	The content to be set in the buffer.
	 * @param   array   $options	Array of optional elements.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function setBuffer($content, $options = array())
	{
		// The following code is just for backward compatibility.
		if (func_num_args() > 1 && !is_array($options)) {
			$args = func_get_args(); $options = array();
			$options['type'] = $args[1];
			$options['name'] = (isset($args[2])) ? $args[2] : null;
		}

		parent::$_buffer[$options['type']][$options['name']] = $content;
	}

	/**
	 * Parses the template and populates the buffer
	 *
	 * @param   array  $params  Parameters for fetching the template
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function parse($params = array()) // !
	{
		$this->_fetchTemplate($params);
		$this->_parseTemplate();
	}

	/**
	 * Outputs the template to the browser.
	 *
	 * @param   boolean  $cache   If true, cache the output
	 * @param   array    $params  Associative array of attributes
	 *
	 * @return  The rendered data
	 *
	 * @since   11.1
	 */
	public function render($caching = false, $params = array()) // !
	{
		$this->_caching = $caching;

		$this->parse($params);
		$data = $this->_renderTemplate();

		if ( (int)JCck::getConfig_Param( 'debug', 0 ) > 0 ) {
			$data = '<!-- Begin: SEBLOD 3.x Document -->'. $data .'<!-- End: SEBLOD 3.x Document -->';
		}

		parent::render();
		return $data;
	}

	/**
	 * Load a template file
	 *
	 * @param   string  $template  The name of the template
	 * @param   string  $filename  The actual filename
	 *
	 * @return  string  The contents of the template
	 *
	 * @since   11.1
	 */
	protected function _loadTemplate($directory, $filename) // !
	{
		//		$component	= JApplicationHelper::getComponentName();

		$contents = '';

		// Check to see if we have a valid template file
		if (file_exists($directory . '/' . $filename))
		{
			// Store the file path
			$this->_file = $directory . '/' . $filename;

			//get the file content
			ob_start();
			require $directory . '/' . $filename;
			$contents = ob_get_contents();
			ob_end_clean();
		}

		return $contents;
	}

	/**
	 * Fetch the template, and initialise the params
	 *
	 * @param   array  $params  Parameters to determine the template
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	protected function _fetchTemplate($params = array())
	{
		// Check
		$directory = isset($params['directory']) ? $params['directory'] : 'templates';
		$filter = JFilterInput::getInstance();
		$template = $filter->clean($params['template'], 'cmd');
		$file = $filter->clean($params['file'], 'cmd');

		if (!file_exists($directory . '/' . $template . '/' . $file))
		{
			$template = 'system';
		}

		// Load the language file for the template
		$lang = JFactory::getLanguage();

		// 1.5 or core then 1.6
		$lang->load('tpl_' . $template, JPATH_BASE, null, false, true)
			|| $lang->load('tpl_' . $template, $directory . '/' . $template, null, false, true);

		// Assign the variables
		$this->template = $template;
		$this->baseurl = JUri::base(true);
		$this->params = isset($params['params']) ? $params['params'] : new JRegistry;

		// Load
		$this->_template = $this->_loadTemplate($directory . '/' . $template, $file);
	}

	/**
	 * Parse a document template
	 *
	 * @return  The parsed contents of the template
	 *
	 * @since   11.1
	 */
	protected function _parseTemplate() // !
	{
		$matches = array();

		if (preg_match_all('#<jdoc:include\ type="([^"]+)" (.*)\/>#iU', $this->_template, $matches))
		{
			$template_tags_first 	= array();
			$template_tags_last 	= array();

			// Step through the jdocs in reverse order.
			for ($i = count($matches[0])-1; $i >= 0; $i--) {
				$type  		= $matches[1][$i];
				$attribs 	= empty($matches[2][$i]) ? array() : JUtility::parseAttributes($matches[2][$i]);
				$name 		= isset($attribs['name']) ? $attribs['name'] : null;

				// Separate buffers to be executed first and last
				if ($type == 'module' || $type == 'modules') {
					$template_tags_first[$matches[0][$i]] = array('type'=>$type, 'name'=>$name, 'attribs'=>$attribs);
				} else {
					$template_tags_last[$matches[0][$i]] = array('type'=>$type, 'name'=>$name, 'attribs'=>$attribs);
				}
			}
			// Reverse the last array so the jdocs are in forward order.
			$template_tags_last = array_reverse($template_tags_last);

			$this->_template_tags = $template_tags_first + $template_tags_last;
		}
	}

	/**
	 * Render pre-parsed template
	 *
	 * @return string rendered template
	 *
	 * @since   11.1
	 */
	protected function _renderTemplate()
	{
		$replace = array();
		$with = array();

		foreach ($this->_template_tags as $jdoc => $args)
		{
			$replace[] = $jdoc;
			$with[] = $this->getBuffer($args['type'], $args['name'], $args['attribs']);
		}

		return str_replace($replace, $with, $this->_template);
	}
}
