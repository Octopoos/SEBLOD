<?php
/**
* @version 			SEBLOD 3.x Core ~ $Id: cckrules.php sebastienheraud $
* @package			SEBLOD (App Builder & CCK) // SEBLOD nano (Form Builder)
* @url				http://www.seblod.com
* @editor			Octopoos - www.octopoos.com
* @copyright		Copyright (C) 2009 - 2016 SEBLOD. All Rights Reserved.
* @license 			GNU General Public License version 2 or later; see _LICENSE.php
**/

defined( 'JPATH_PLATFORM' ) or die;

jimport( 'joomla.html.html' );
jimport( 'cck.joomla.access.access' );

// JFormField
class JFormFieldCCKRules extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.1
	 */
	public $type = 'CCKrules';

	/**
	 * Method to get the field input markup.
	 *
	 * TODO: Add access check.
	 *
	 * @return  string  The field input markup.
	 * @since   11.1
	 */
	protected function getInput()
	{
		JHtml::_('behavior.tooltip');

		// Initialise some field attributes.
		$section	= $this->element['section'] ? (string) $this->element['section'] : '';
		$component	= $this->element['component'] ? (string) $this->element['component'] : '';
		$assetField	= $this->element['asset_field'] ? (string) $this->element['asset_field'] : 'asset_id';

		// Get the actions for the asset.
		$actions	= CCKAccess::getActions( $component, $section );

		// Iterate over the children and add to the actions.
		foreach ($this->element->children() as $el)
		{
			if ($el->getName() == 'action') {
				$actions[] = (object) array(
					'name'			=> (string) $el['name'],
					'title'			=> (string) $el['title'],
					'description'	=> (string) $el['description']
				);
			}
		}

		// Get the explicit rules for this asset.
		if ($section == 'component') {
			// Need to find the asset id by the name of the component.
			$db = JFactory::getDbo();
			$db->setQuery('SELECT id FROM #__assets WHERE name = ' . $db->quote($component));
			$assetId = (int) $db->loadResult();
		}
		else {
			// Find the asset id of the content.
			// Note that for global configuration, com_config injects asset_id = 1 into the form.
			$assetId = $this->form->getValue($assetField);
		}

		// Use the compact form for the content rules (deprecated).
		/* @todo remove code:
		if (!empty($component) && $section != 'component') {
			return JHtml::_('rules.assetFormWidget', $actions, $assetId, $assetId ? null : $component, $this->name, $this->id);
		}
		*/


		// Full width format.

		// Get the rules for just this asset (non-recursive).
		$assetRules = CCKAccess::getAssetRules( $assetId, false, $component, $section );

		// Get the available user groups.
		$groups = $this->getUserGroups();

		// Build the form control.
		$curLevel = 0;

		// Prepare output
		$html = array();

		// Description
		$html[] = '<p class="rule-desc">' . JText::_('JLIB_RULES_SETTINGS_DESC') . '</p>';

		// Begin tabs
		$html[] = '<div id="permissions-sliders" class="tabbable tabs-left">';

		// Building tab nav
		$html[] = '<ul class="nav nav-tabs">';

		foreach ($groups as $group)
		{
			// Initial Active Tab
			$active = "";
			if ($group->value == 1)
			{
				$active = "active";
			}

			$html[] = '<li class="' . $active . '">';
				$html[] = '<a href="#permission-' . $group->value . '" data-toggle="tab">';
				$html[] = str_repeat('<span class="level">&ndash; ', $curLevel = $group->level) . $group->text;
				$html[] = '</a>';
			$html[] = '</li>';
		}
		$html[] = '</ul>';

		$html[] = '<div class="tab-content">';

		// Start a row for each user group.
		foreach ($groups as $group)
		{
			// Initial Active Pane
			$active = "";
			if ($group->value == 1)
			{
				$active = " active";
			}

			$difLevel = $group->level - $curLevel;

			$html[] = '<div class="tab-pane' . $active . '" id="permission-' . $group->value . '">';
			$html[] = '<table class="table table-striped">';
			$html[] =				'<thead>';
			$html[] =					'<tr>';

			$html[] =						'<th class="actions" id="actions-th' . $group->value . '">';
			$html[] =							'<span class="acl-action">' . JText::_('JLIB_RULES_ACTION') . '</span>';
			$html[] =						'</th>';

			$html[] =						'<th class="settings" id="settings-th' . $group->value . '">';
			$html[] =							'<span class="acl-action">' . JText::_('JLIB_RULES_SELECT_SETTING') . '</span>';
			$html[] =						'</th>';

			// The calculated setting is not shown for the root group of global configuration.
			$canCalculateSettings = ($group->parent_id || !empty($component));
			if ($canCalculateSettings) {
				$html[] =					'<th id="aclactionth' . $group->value . '">';
				$html[] =						'<span class="acl-action">' . JText::_('JLIB_RULES_CALCULATED_SETTING') . '</span>';
				$html[] =					'</th>';
			}

			$html[] =					'</tr>';
			$html[] =				'</thead>';
			$html[] =				'<tbody>';

			foreach ($actions as $action)
			{
				$html[] =				'<tr>';
				$html[] =					'<td headers="actions-th' . $group->value . '">';
				$html[] =						'<label class="tip" for="' . $this->id . '_' . $action->name . '_' . $group->value . '" title="'.htmlspecialchars(JText::_($action->title).' '.JText::_($action->description), ENT_COMPAT, 'UTF-8').'">';
				$html[] =						JText::_($action->title);
				$html[] =						'</label>';
				$html[] =					'</td>';

				$html[] =					'<td headers="settings-th' . $group->value . '">';

				if ( $action->input_type == 'text' ) {
					
					$value			=	$assetRules->allow( $action->name, $group->value );
					if ( $group->value == 8 && $value == '' ) {
						$value	=	0;
					}
					if ( $value != '' && $value > 0 ) {
						$allowed		=	JText::_( 'COM_CCK_RESTRICTED' );
						$inheritedRule	=	true;
					} else {
						$allowed		=	JText::_( 'COM_CCK_UNLIMITED' );
						$inheritedRule	=	true;
					}
					
					$html[]	=	'<input class="inputbox input-small" type="text" name="' . $this->name . '[' . $action->name . '][' . $group->value . ']" id="' . $this->id . '_' . $action->name . '_' . $group->value . '" title="' . JText::sprintf('JLIB_RULES_SELECT_ALLOW_DENY_GROUP', JText::_($action->title), trim($group->text)) . '" value="'.$value.'" size="8" />';
				} else {
				
					$html[] = '<select class="inputbox input-small" name="' . $this->name . '[' . $action->name . '][' . $group->value . ']" id="' . $this->id . '_' . $action->name . '_' . $group->value . '" title="' . JText::sprintf('JLIB_RULES_SELECT_ALLOW_DENY_GROUP', JText::_($action->title), trim($group->text)) . '">';

					$inheritedRule	= CCKAccess::checkGroup($group->value, $action->name, $assetId);

					// Get the actual setting for the action for this group.
					$assetRule		= $assetRules->allow($action->name, $group->value);

					// Build the dropdowns for the permissions sliders

					// The parent group has "Not Set", all children can rightly "Inherit" from that.
					$html[] = '<option value=""' . ($assetRule === null ? ' selected="selected"' : '') . '>' .
								JText::_(empty($group->parent_id) && empty($component) ? 'JLIB_RULES_NOT_SET' : 'JLIB_RULES_INHERITED') . '</option>';
					$html[] = '<option value="1"' . ($assetRule === true ? ' selected="selected"' : '') . '>' .
								JText::_('JLIB_RULES_ALLOWED') . '</option>';
					$html[] = '<option value="0"' . ($assetRule === false ? ' selected="selected"' : '') . '>' .
								JText::_('JLIB_RULES_DENIED') . '</option>';

					$html[] = '</select>&#160; ';
					
					$allowed	=	JText::_( 'JLIB_RULES_ALLOWED' );
				}
				// If this asset's rule is allowed, but the inherited rule is deny, we have a conflict.
				if (($assetRule === true) && ($inheritedRule === false)) {
					$html[] = JText::_('JLIB_RULES_CONFLICT');
				}

				$html[] = '</td>';

				// Build the Calculated Settings column.
				// The inherited settings column is not displayed for the root group in global configuration.
				if ($canCalculateSettings) {
					$html[] = '<td headers="aclactionth' . $group->value . '">';

					// This is where we show the current effective settings considering currrent group, path and cascade.
					// Check whether this is a component or global. Change the text slightly.

					if (CCKAccess::checkGroup($group->value, 'core.admin', $assetId) !== true)
					{
						if ($inheritedRule === null) {
							$html[] =	'<span class="label label-important">' . JText::_('JLIB_RULES_NOT_ALLOWED'). '</span>';
						}
						elseif ($inheritedRule === true)
						{
							$html[] =	'<span class="label label-success">' . $allowed . '</span>';
						}
						elseif ($inheritedRule === false) {
							if ($assetRule === false) {
								$html[] = '<span class="label label-important">'.
											JText::_('JLIB_RULES_NOT_ALLOWED').'</span>';
							}
							else {
								$html[] = '<span class="label"><i class="icon-lock icon-white"></i>'.
											JText::_('JLIB_RULES_NOT_ALLOWED_LOCKED').'</span>';
							}
						}
					}
					elseif (!empty($component)) {
						$html[] = '<span class="label label-success"><i class="icon-lock icon-white"></i>'.
									JText::_('JLIB_RULES_ALLOWED_ADMIN').'</span>';
					}
					else {
						// Special handling for  groups that have global admin because they can't  be denied.
						// The admin rights can be changed.
						if ($action->name === 'core.admin') {
							$html[] = '<span class="label label-success">'.
										JText::_('JLIB_RULES_ALLOWED').'</span>';
						}
						elseif ($inheritedRule === false) {
							// Other actions cannot be changed.
							$html[] = '<span class="label label-important"><i class="icon-lock icon-white"></i>'.
										JText::_('JLIB_RULES_NOT_ALLOWED_ADMIN_CONFLICT').'</span>';
						}
						else {
							$html[] = '<span class="label label-success"><i class="icon-lock icon-white"></i>'.
										JText::_('JLIB_RULES_ALLOWED_ADMIN').'</span>';
						}
					}

					$html[] = '</td>';
				}

				$html[] = '</tr>';
			}

			$html[] = '</tbody>';
			$html[] = '</table></div>';

		}

		$html[] = '</div></div>';
		$html[] = '<div class="alert">';
		if ($section == 'component' || $section == null ) {
			$html[] = JText::_('JLIB_RULES_SETTING_NOTES');
		} else {
			$html[] = JText::_('JLIB_RULES_SETTING_NOTES_ITEM');
		}
		$html[] = '</div>';

		// Get the JInput object
		$input = JFactory::getApplication()->input;
		
		// Get the JInput object
		$input = JFactory::getApplication()->input;

		return implode("\n", $html);
	}

	/**
	 * Get a list of the user groups.
	 *
	 * @return  array
	 * @since   11.1
	 */
	protected function getUserGroups()
	{
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true)
			->select('a.id AS value, a.title AS text, COUNT(DISTINCT b.id) AS level, a.parent_id')
			->from('#__usergroups AS a')
			->join('LEFT', '`#__usergroups` AS b ON a.lft > b.lft AND a.rgt < b.rgt')
			->group('a.id')
			->order('a.lft ASC');

		$db->setQuery($query);
		$options = $db->loadObjectList();

		return $options;
	}
}
?>