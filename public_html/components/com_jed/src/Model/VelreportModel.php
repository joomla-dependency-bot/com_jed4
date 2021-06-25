<?php
/**
 * @package       JED
 *
 * @subpackage    VEL
 *
 * @copyright     Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Jed\Component\Jed\Site\Model;
// No direct access.
defined('_JEXEC') or die;

use Exception;
use Jed\Component\Jed\Site\Helper\JedHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\ItemModel;
use Joomla\CMS\Table\Table;
use Joomla\Utilities\ArrayHelper;
use function defined;

/**
 * VEL Report Model Class.
 *
 * @since  4.0
 */
class VelreportModel extends ItemModel
{


	/**
	 * Method to get a single record.
	 *
	 * @param   int|null  $pk  The id of the object to get.
	 *
	 * @return  object    Object on success, false on failure.
	 *
	 * @throws Exception
	 * @since 4.0.0
	 */
	public function getItem($pk = null)
	{
		$app = Factory::getApplication();
		if ($this->_item === null)
		{
			$this->_item = false;

			if (empty($pk))
			{
				$pk = $this->getState('velreport.id');
			}

			// Get a level row instance.
			$table = $this->getTable();

			// Attempt to load the row.
			$keys = array("id" => $pk, "created_by" => Factory::getUser()->id);

			if ($table->load($keys))
			{
				if (empty($result) || JedHelper::isAdminOrSuperUser())
				{

					// Check published state.
					if ($published = $this->getState('filter.published'))
					{
						if (isset($table->state) && $table->state != $published)
						{
							$app->enqueueMessage("Item is not published", "message");

							return null;
						}
					}

					// Convert the JTable to a clean JObject.
					$properties = $table->getProperties(1);

					$this->_item = ArrayHelper::toObject($properties, 'JObject');

				}
				else
				{
					$app->enqueueMessage("Sorry you did not create that report item", "message");

					return null;
					//throw new Exception(Text::_("JERROR_ALERTNOAUTHOR"), 401);
				}
			}

			if (empty($this->_item))
			{

				$app->enqueueMessage(Text::_('COM_JED_SECURITY_CANT_LOAD'), "message");

				return null;
			}
		}


		if (!JedHelper::is_blank($this->_item->pass_details_ok))
		{
			$this->_item->pass_details_ok = Text::_('COM_JED_VEL_REPORTS_FIELD_PASS_DETAILS_OK_OPTION_' . $this->_item->pass_details_ok);
		}

		if (!JedHelper::is_blank($this->_item->vulnerability_type))
		{
			$this->_item->vulnerability_type = Text::_('COM_JED_VEL_GENERAL_FIELD_VULNERABILITY_TYPE_OPTION_' . $this->_item->vulnerability_type);
		}

		if (!JedHelper::is_blank($this->_item->exploit_type))
		{
			$this->_item->exploit_type = Text::_('COM_JED_VEL_GENERAL_FIELD_EXPLOIT_TYPE_OPTION_' . $this->_item->exploit_type);
		}

		if (!JedHelper::is_blank($this->_item->vulnerability_actively_exploited))
		{
			$this->_item->vulnerability_actively_exploited = Text::_('COM_JED_VEL_REPORTS_FIELD_VULNERABILITY_ACTIVELY_EXPLOITED_OPTION_' . $this->_item->vulnerability_actively_exploited);
		}

		if (!JedHelper::is_blank($this->_item->vulnerability_publicly_available))
		{
			$this->_item->vulnerability_publicly_available = Text::_('COM_JED_VEL_REPORTS_FIELD_VULNERABILITY_PUBLICLY_AVAILABLE_OPTION_' . $this->_item->vulnerability_publicly_available);
		}

		if (!JedHelper::is_blank($this->_item->developer_communication_type))
		{
			$this->_item->developer_communication_type = Text::_('COM_JED_VEL_GENERAL_FIELD_DEVELOPER_COMMUNICATION_TYPE_OPTION_' . $this->_item->developer_communication_type);
		}

		if (!JedHelper::is_blank($this->_item->consent_to_process))
		{
			$this->_item->consent_to_process = Text::_('COM_JED_GENERAL_CONSENT_TO_PROCESS_OPTION_' . $this->_item->consent_to_process);
		}

		if (!JedHelper::is_blank($this->_item->passed_to_vel))
		{
			$this->_item->passed_to_vel = Text::_('COM_JED_VEL_GENERAL_FIELD_PASSED_TO_VEL_OPTION_' . $this->_item->passed_to_vel);
		}

		if (!JedHelper::is_blank($this->_item->data_source))
		{
			$this->_item->data_source = Text::_('COM_JED_VEL_GENERAL_FIELD_DATA_SOURCE_OPTION_' . $this->_item->data_source);
		}

		if (isset($this->_item->created_by))
		{
			$this->_item->created_by_name = Factory::getUser($this->_item->created_by)->name;
		}

		if (isset($this->_item->modified_by))
		{
			$this->_item->modified_by_name = Factory::getUser($this->_item->modified_by)->name;
		}

		return $this->_item;
	}

	/**
	 * Get an instance of Table class
	 *
	 * @param   string  $name
	 * @param   string  $prefix  Prefix for the table class name. Optional.
	 * @param   array   $options
	 *
	 * @return  Table Table if success, throws exception on failure.
	 * @throws Exception
	 * @since 4.0.0
	 */
	public function getTable($name = 'Velreport', $prefix = 'Administrator', $options = array()): Table
	{
		return parent::getTable($name, $prefix, $options);
	}

	/**
	 * Method to check in an item.
	 *
	 * @param   int|null  $id  The id of the row to check out.
	 *
	 * @return  boolean True on success, false on failure.
	 *
	 * @throws Exception
	 * @since 4.0.0
	 *
	 */
	public function checkin(int $id = null): bool
	{
		// Get the id.
		$id = (!empty($id)) ? $id : (int) $this->getState('velreport.id');
		if ($id || $this->userIDItem($id) || JedHelper::isAdminOrSuperUser())
		{
			if ($id)
			{
				// Initialise the table
				$table = $this->getTable();

				// Attempt to check the row in.
				if (method_exists($table, 'checkin'))
				{
					if (!$table->checkin($id))
					{
						return false;
					}
				}
			}

			return true;
		}
		else
		{
			throw new Exception(Text::_("JERROR_ALERTNOAUTHOR"), 401);
		}
	}

	/**
	 * This method revises if the $id of the item belongs to the current user
	 *
	 * @param   integer  $id  The id of the item
	 *
	 * @return  boolean             true if the user is the owner of the row, false if not.
	 *
	 * @since 4.0.0
	 */
	public function userIDItem(int $id): bool
	{
		try
		{
			$user = Factory::getUser();
			$db   = Factory::getDbo();

			$query = $db->getQuery(true);
			$query->select("id")
				->from($db->quoteName('#__jed_vel_report'))
				->where("id = " . $db->escape($id))
				->where("created_by = " . $user->id);

			$db->setQuery($query);

			$results = $db->loadObject();
			if ($results)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		catch (Exception $exc)
		{
			return false;
		}
	}


	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since 4.0.0
	 *
	 */
	protected function populateState()
	{
		$app  = Factory::getApplication();
		$user = Factory::getUser();

		// Check published state
		if ((!$user->authorise('core.edit.state', 'com_jed')) && (!$user->authorise('core.edit', 'com_jed')))
		{
			$this->setState('filter.published', 1);
			$this->setState('filter.archived', 2);
		}

		// Load state from the request userState on edit or from the passed variable on default
		if (Factory::getApplication()->input->get('layout') == 'edit')
		{
			$id = Factory::getApplication()->getUserState('com_jed.edit.velreport.id');
		}
		else
		{
			$id = Factory::getApplication()->input->get('id');
			Factory::getApplication()->setUserState('com_jed.edit.velreport.id', $id);
		}

		$this->setState('velreport.id', $id);

		// Load the parameters.
		$params       = $app->getParams();
		$params_array = $params->toArray();

		if (isset($params_array['item_id']))
		{
			$this->setState('velreport.id', $params_array['item_id']);
		}

		$this->setState('params', $params);
	}

}
