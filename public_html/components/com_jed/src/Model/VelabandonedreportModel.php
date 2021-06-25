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
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Table\Table;
use Joomla\Utilities\ArrayHelper;
use function defined;

/**
 * VEL Abandoned Report Model Class.
 *
 * @since  4.0
 */
class VelabandonedreportModel extends ItemModel
{
	public $_item;

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
				$pk = $this->getState('velabandonedreport.id');
			}

			// Get a level row instance.
			$table = $this->getTable();

			// Attempt to load the row.
			if ($table->load($pk))
			{
				if (empty($result) || JedHelper::isAdminOrSuperUser())
				{

					// Check published state.
					if ($published = $this->getState('filter.published'))
					{
						if (isset($table->state) && $table->state != $published)
						{
							throw new Exception(Text::_('COM_JED_ITEM_NOT_LOADED'), 403);
						}
					}

					// Convert the Table to a clean CMSObject.
					$properties  = $table->getProperties(1);
					$this->_item = ArrayHelper::toObject($properties, CMSObject::class);

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


		if (!empty($this->_item->consent_to_process) || $this->_item->consent_to_process == 0)
		{
			$this->_item->consent_to_process = Text::_('COM_JED_GENERAL_CONSENT_TO_PROCESS_OPTION_' . $this->_item->consent_to_process);
		}

		if (!empty($this->_item->passed_to_vel) || $this->_item->passed_to_vel == 0)
		{
			$this->_item->passed_to_vel = Text::_('COM_JED_VEL_GENERAL_FIELD_PASSED_TO_VEL_OPTION_' . $this->_item->passed_to_vel);
		}

		if (!empty($this->_item->data_source) || $this->_item->data_source == 0)
		{
			$this->_item->data_source = Text::_('COM_JED_VEL_ABANDONEDREPORTS_FIELD_DATA_SOURCE_LABEL_OPTION_' . $this->_item->data_source);
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
	 * @return  Table|bool Table if success, false on failure.
	 * @throws Exception
	 * @since 4.0.0
	 */
	public function getTable($name = 'Velabandonedreport', $prefix = 'Administrator', $options = array()): Table
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
		$id = (!empty($id)) ? $id : (int) $this->getState('velabandonedreport.id');
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
	 * Method to check out an item for editing.
	 *
	 * @param   int|null  $id  The id of the row to check out.
	 *
	 * @return  boolean True on success, false on failure.
	 *
	 * @throws Exception
	 * @since 4.0.0
	 *
	 */
	public function checkout(int $id = null): bool
	{
		// Get the user id.
		$id = (!empty($id)) ? $id : (int) $this->getState('velabandonedreport.id');

		if ($id || $this->userIDItem($id) || JedHelper::isAdminOrSuperUser())
		{
			if ($id)
			{
				// Initialise the table
				$table = $this->getTable();

				// Get the current user object.
				$user = Factory::getUser();

				// Attempt to check the row out.
				if (method_exists($table, 'checkout'))
				{
					if (!$table->checkout($user->get('id'), $id))
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
			$id = Factory::getApplication()->getUserState('com_jed.edit.velabandonedreport.id');
		}
		else
		{
			$id = Factory::getApplication()->input->get('id');
			Factory::getApplication()->setUserState('com_jed.edit.velabandonedreport.id', $id);
		}

		$this->setState('velabandonedreport.id', $id);

		// Load the parameters.
		$params       = $app->getParams();
		$params_array = $params->toArray();

		if (isset($params_array['item_id']))
		{
			$this->setState('velabandonedreport.id', $params_array['item_id']);
		}

		$this->setState('params', $params);
	}


}
