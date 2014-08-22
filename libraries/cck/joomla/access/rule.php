<?php
/**
 * @package     Joomla.Platform
 * @subpackage  Access
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

// CCKRule
class CCKRule
{
	/**
	 * A named array
	 *
	 * @var    array
	 * @since  11.1
	 */
	protected $_data = array();

	/**
	 * Constructor.
	 *
	 * The input array must be in the form: array(-42 => true, 3 => true, 4 => false)
	 * or an equivalent JSON encoded string.
	 *
	 * @param   mixed  $identities  A JSON format string (probably from the database) or a named array.
	 *
	 * @since   11.1
	 */
	public function __construct($identities, $var_type = 'boolean')
	{
		// Convert string input to an array.
		if (is_string($identities))
		{
			$identities = json_decode($identities, true);
		}

		$this->mergeIdentities($identities, $var_type);
	}

	/**
	 * Get the data for the action.
	 *
	 * @return  array  A named array
	 *
	 * @since   11.1
	 */
	public function getData()
	{
		return $this->_data;
	}

	/**
	 * Merges the identities
	 *
	 * @param   mixed  $identities  An integer or array of integers representing the identities to check.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function mergeIdentities($identities, $var_type = 'boolean')
	{
		if ($identities instanceof CCKRule)
		{
			$identities = $identities->getData();
		}

		if (is_array($identities))
		{
			foreach ($identities as $identity => $allow)
			{
				$this->mergeIdentity($identity, $allow, $var_type);
			}
		}
	}

	/**
	 * Merges the values for an identity.
	 *
	 * @param   integer  $identity  The identity.
	 * @param   boolean  $allow     The value for the identity (true == allow, false == deny).
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	public function mergeIdentity($identity, $allow, $var_type = 'boolean')
	{
		$identity	= (int) $identity;
		settype( $allow, $var_type );
		
		// Check that the identity exists.
		if (isset($this->_data[$identity]))
		{
			// Explicit deny always wins a merge.
			if ($this->_data[$identity] !== 0)
			{
				$this->_data[$identity] = $allow;
			}
		}
		else
		{
			$this->_data[$identity] = $allow;
		}
	}

	/**
	 * Checks that this action can be performed by an identity.
	 *
	 * The identity is an integer where +ve represents a user group,
	 * and -ve represents a user.
	 *
	 * @param   mixed  $identities  An integer or array of integers representing the identities to check.
	 *
	 * @return  mixed  True if allowed, false for an explicit deny, null for an implicit deny.
	 *
	 * @since   11.1
	 */
	public function allow($identities, $var_type = 'boolean')
	{
		// Implicit deny by default.
		$result = null;

		// Check that the inputs are valid.
		if (!empty($identities))
		{
			if (!is_array($identities))
			{
				$identities = array($identities);
			}

			foreach ($identities as $identity)
			{
				// Technically the identity just needs to be unique.
				$identity = (int) $identity;

				// Check if the identity is known.
				if (isset($this->_data[$identity]))
				{
					$result	=	$this->_data[$identity];
					settype( $result, $var_type );

					// An explicit deny wins.
					if ($result === false)
					{
						break;
					}
				}

			}
		}

		return $result;
	}

	/**
	 * Convert this object into a JSON encoded string.
	 *
	 * @return  string  JSON encoded string
	 *
	 * @since   11.1
	 */
	public function __toString()
	{
		return json_encode($this->_data);
	}
}
