<?php
/**
 * @package     Joomla.Platform
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

//Register the renderer class with the loader
JLoader::register( 'CCK_DocumentRenderer', __DIR__.'/renderer.php' );
jimport('joomla.filter.filteroutput');

// CCK_Document
class CCK_Document extends JObject
{
	var $id	=	1;
	
	/**
	 * Contains the character encoding string
	 *
	 * @var	string
	 */
	public $_charset = 'utf-8';

	/**
	 * The document type
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $_type = null;

	/**
	 * Array of buffered output
	 *
	 * @var    mixed (depends on the renderer)
	 */
	public $_buffer = null; // cck

	/**
	 * Class constructor.
	 *
	 * @param   array  $options  Associative array of options
	 *
	 * @return  CCK_Document
	 *
	 * @since   11.1
	 */
	public function __construct($options = array())
	{
		parent::__construct();
	}

	/**
	 * Returns the global CCK_Document object, only creating it
	 * if it doesn't already exist.
	 *
	 * @param   string  $type       The document type to instantiate
	 * @param   array   $attribues  Array of attributes
	 *
	 * @return  object  The document object.
	 * @since   11.1
	 */
	public static function &getInstance($type = 'html', $attributes = array()) //public static function getInstance($type = 'html', $attributes = array()) // cck
	{
		static $instances;

		if (!isset($instances)) {
			$instances = array();
		}

		$signature = serialize(array($type, $attributes));

		if (empty($instances[$signature])) {
			$type	= preg_replace('/[^A-Z0-9_\.-]/i', '', $type);
			$path	= __DIR__ . '/' . $type . '/' . $type.'.php';
			$ntype	= null;

			// Check if the document type exists
			if (!file_exists($path)) {
				// Default to the raw format
				$ntype	= $type;
				$type	= 'raw';
			}

			// Determine the path and class
			$class = 'CCK_Document'.$type;
			if (!class_exists($class)) {
				$path	= __DIR__ . '/' . $type . '/' . $type.'.php';
				if (file_exists($path)) {
					require_once $path;
				}
				else {
					throw new RuntimeException(JText::_('JLIB_DOCUMENT_ERROR_UNABLE_LOAD_DOC_CLASS'), 500);
				}
			}

			$instance	= new $class($attributes);
			$instances[$signature] = &$instance;

			if (!is_null($ntype)) {
				// Set the type to the Document type originally requested
				$instance->setType($ntype);
			}
		}

		return $instances[$signature];
	}

	/**
	 * Set the document type
	 *
	 * @param   string  $type
	 *
	 * @return
	 * @since   11.1
	 */
	public function setType($type)
	{
		$this->_type = $type;
	}

	/**
	 * Returns the document type
	 *
	 * @return  string
	 * @since   11.1
	 */
	public function getType()
	{
		return $this->_type;
	}

	/**
	 * Get the contents of the document buffer
	 *
	 * @return  The contents of the document buffer
	 * @since   11.1
	 */
	public function getBuffer()
	{
		return $this->_buffer; //return self::$_buffer; //cck
	}

	/**
	 * Set the contents of the document buffer
	 *
	 * @param   string  $content  The content to be set in the buffer.
	 * @param   array   $options  Array of optional elements.
	 *
	 * @return  void
	 * @since   11.1
	 */
	public function setBuffer($content, $options = array())
	{
		$this->_buffer = $content; //self::$_buffer = $content; // cck
	}
	
	/**
	 * Load a renderer
	 *
	 * @param   string  $type  The renderer type
	 *
	 * @return  mixed  Object or null if class does not exist
	 * @since   11.1
	 */
	public function loadRenderer($type)
	{
		$class	= 'CCK_DocumentRenderer'.$type;

		if (!class_exists($class)) {
			$path = __DIR__ . '/' . $this->_type . '/renderer/' . $type.'.php';

			if (file_exists($path)) {
				require_once $path;
			}
			else {
				throw new RuntimeException('Unable to load renderer class', 500);
			}
		}

		if (!class_exists($class)) {
			return null;
		}

		$instance = new $class($this);

		return $instance;
	}

	/**
	 * Parses the document and prepares the buffers
	 *
	 * @return null
	 */
	public function parse($params = array())
	{
		return null;
	}

	/**
	 * Outputs the document
	 *
	 * @param   boolean  $cache     If true, cache the output
	 * @param   boolean  $compress  If true, compress the output
	 * @param   array    $params    Associative array of attributes
	 *
	 * @return  The rendered data
	 */
	public function render($cache = false, $params = array())
	{
		$this->id++;
	}
	
	// finalize
	function finalize( $mode, $name, $client, $positions, $positions_more, $infos, $pk = 0 )
	{
		$this->cck_client		=	$client;
		$this->cck_params		=	$infos['params'];
		$this->cck_path			=	$infos['path'];
		$this->cck_mode			=	$mode;
		$this->cck_type			=	$name;

		if ( $infos['context'] == 'com_finder.indexer' ) {
			if ( isset( $this->cck_params['field_label'] ) ) {
				$this->cck_params['field_label']	=	'0';
			}
			if ( isset( $this->cck_params['field_description'] ) ) {
				$this->cck_params['field_description']	=	'0';
			}
		}
		
		$this->infinite			=	( isset( $infos['infinite'] ) ) ? $infos['infinite'] : false;
		$this->pk				=	$pk;
		$this->positions		=	$positions;
		$this->positions_more	=	$positions_more;
		$this->rooturl			=	$infos['root'];
		$this->template			=	$infos['template'];
		$this->theme			=	$infos['theme'];
	}
}
