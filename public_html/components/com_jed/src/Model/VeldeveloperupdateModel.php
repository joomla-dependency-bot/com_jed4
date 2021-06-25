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
 * VEL Developer Update Model Class.
 *
 * @since 4.0.0
 */
class VeldeveloperupdateModel extends ItemModel
{
	public $_item;

	/**
	 * Method to get an object.
	 *
	 * @param   integer  $pk  The id of the object to get.
	 *
	 * @return  false|object|null    Object on success, false on failure.
	 *
	 * @throws Exception
	 *
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
				$pk = $this->getState('veldeveloperupdate.id');
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
					$properties  = $table->getProperties(1);
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


		if (!empty($this->_item->consent_to_process))
		{
			$this->_item->consent_to_process = Text::_('COM_JED_GENERAL_CONSENT_TO_PROCESS_OPTION_' . $this->_item->consent_to_process);
		}

		if (!empty($this->_item->update_data_source))
		{
			$this->_item->update_data_source = Text::_('COM_JED_VEL_DEVELOPERUPDATES_FIELD_UPDATE_DATA_SOURCE_OPTION_' . $this->_item->update_data_source);
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
	 * @param   string  $name     Name of the JTable class to get an instance of.
	 * @param   string  $prefix   Prefix for the table class name. Optional.
	 * @param   array   $options  Array of configuration values for the JTable object. Optional.
	 *
	 * @return Table Table if success, false on failure.
	 * @throws Exception
	 * @since 4.0.0
	 */
	public function getTable($name = 'Veldeveloperupdate', $prefix = 'Administrator', $options = array()): Table
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
	 */
	public function checkin(int $id = null): bool
	{
		// Get the id.
		$id = (!empty($id)) ? $id : (int) $this->getState('veldeveloperupdate.id');
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
				->from($db->quoteName('#__jed_vel_developer_update'))
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
		$id = (!empty($id)) ? $id : (int) $this->getState('veldeveloperupdate.id');

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
	 * Publish the element
	 *
	 * @param   int  $id     Item id
	 * @param   int  $state  Publish state
	 *
	 * @return  boolean
	 *
	 * @throws Exception
	 * @since 4.0.0
	 */
	public function publish(int $id, int $state): bool
	{
		$table = $this->getTable();
		if ($id || $this->userIDItem($id) || JedHelper::isAdminOrSuperUser())
		{
			$table->load($id);

			$table->state = $state;

			return $table->store();
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
			$id = Factory::getApplication()->getUserState('com_jed.edit.veldeveloperupdate.id');
		}
		else
		{
			$id = Factory::getApplication()->input->get('id');
			Factory::getApplication()->setUserState('com_jed.edit.veldeveloperupdate.id', $id);
		}

		$this->setState('veldeveloperupdate.id', $id);

		// Load the parameters.
		$params       = $app->getParams();
		$params_array = $params->toArray();

		if (isset($params_array['item_id']))
		{
			$this->setState('veldeveloperupdate.id', $params_array['item_id']);
		}

		$this->setState('params', $params);
	}

	/**
	 * Method to delete an item
	 *
	 * No deletion of data in front end.
	 *
	 * @param   int  $id  Element id
	 *
	 * @return  bool
	 *
	 * @throws Exception
	 * @since 4.0.0
	 */
	/*public function delete($id) : bool
	{
		$table = $this->getTable();

                if(empty($result) || JedHelper::isAdminOrSuperUser() || $table->created_by == Factory::getUser()->id){
                    return $table->delete($id);
                } else {
                                                throw new Exception(Text::_("JERROR_ALERTNOAUTHOR"), 401);
                                          }
	}*/


}
