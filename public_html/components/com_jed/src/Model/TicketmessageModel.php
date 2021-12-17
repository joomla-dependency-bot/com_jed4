<?php
/**
 * @package       JED
 *
 * @subpackage    TICKETS
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


/**
 * Jed model.
 *
 * @since  4.0.0
 */
class TicketmessageModel extends ItemModel
{
	public $_item;
	/** Data Table
	 * @since 4.0.0
	 **/
	private string $dbtable = "#__jed_ticket_messages";
	/**
	 * Method to get an object.
	 *
	 * @param   integer  $pk  The id of the object to get.
	 *
	 * @return  object    Object on success, false on failure.
	 *
	 * @throws Exception
	 *
	 * @since 4.0.0
	 */
	public function getItem($pk = null)
	{
		if ($this->_item === null)
		{
			$this->_item = false;

			if (empty($pk))
			{
				$pk = $this->getState('ticketmessage.id');
			}

			// Get a level row instance.
			$table = $this->getTable();

			// Attempt to load the row.
			if ($table->load($pk))
			{
				if (empty($result) || JedHelper::isAdminOrSuperUser() || $table->created_by == Factory::getUser()->id)
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
					throw new Exception(Text::_("JERROR_ALERTNOAUTHOR"), 401);
				}
			}

			if (empty($this->_item))
			{
				throw new Exception(Text::_('COM_JED_ITEM_NOT_LOADED'), 404);
			}
		}


		if (isset($this->_item->created_by))
		{
			$this->_item->created_by_name = Factory::getUser($this->_item->created_by)->name;
		}

		if (isset($this->_item->modified_by))
		{
			$this->_item->modified_by_name = Factory::getUser($this->_item->modified_by)->name;
		}

		if (isset($this->_item->ticket_id) && $this->_item->ticket_id != '')
		{
			if (is_object($this->_item->ticket_id))
			{
				$this->_item->ticket_id = ArrayHelper::fromObject($this->_item->ticket_id);
			}

			$values = (is_array($this->_item->ticket_id)) ? $this->_item->ticket_id : explode(',', $this->_item->ticket_id);

			$textValue = array();

			foreach ($values as $value)
			{
				$db    = Factory::getDbo();
				$query = $db->getQuery(true);

				$query
					->select('`#__jedtickets_3591992`.`ticket_subject`')
					->from($db->quoteName('#__jedtickets', '#__jedtickets_3591992'))
					->where($db->quoteName('id') . ' = ' . $db->quote($value));

				$db->setQuery($query);
				$results = $db->loadObject();

				if ($results)
				{
					$textValue[] = $results->ticket_subject;
				}
			}

			$this->_item->ticket_id = !empty($textValue) ? implode(', ', $textValue) : $this->_item->ticket_id;

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
	 *
	 * @throws Exception
	 * @since 4.0.0
	 */
	public function getTable($name = 'Ticketmessage', $prefix = 'Administrator', $options = array())
	{
		return parent::getTable($name, $prefix, $options);
	}

	/**
	 * Get the id of an item by alias
	 *
	 * @param   string  $alias  Item alias
	 *
	 * @return  mixed
	 * @throws Exception
	 * @since 4.0.0
	 */
	public function getItemIdByAlias(string $alias)
	{
		$table      = $this->getTable();
		$properties = $table->getProperties();
		$result     = null;
		$aliasKey   = null;
		if (method_exists($this, 'getAliasFieldNameByView'))
		{
			$aliasKey = $this->getAliasFieldNameByView('ticketmessage');
		}


		if (key_exists('alias', $properties))
		{
			$table->load(array('alias' => $alias));
			$result = $table->id;
		}
		elseif (isset($aliasKey) && key_exists($aliasKey, $properties))
		{
			$table->load(array($aliasKey => $alias));
			$result = $table->id;
		}
		if (empty($result) || JedHelper::isAdminOrSuperUser() || $table->created_by == Factory::getUser()->id)
		{
			return $result;
		}
		else
		{
			throw new Exception(Text::_("JERROR_ALERTNOAUTHOR"), 401);
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
	 * @since    4.0.0
	 */
	public function checkout(int $id = null) : bool
	{
		// Get the user id.
		$id = (!empty($id)) ? $id : (int) $this->getState('ticketmessage.id');

		if ($id || JedHelper::userIDItem($id,$this->dbtable) || JedHelper::isAdminOrSuperUser())
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
	 * @throws Exception
	 * @since 4.0.0
	 */
	public function publish(int $id, int $state) : bool
	{
		$table = $this->getTable();
		if ($id || JedHelper::userIDItem($id,$this->dbtable) || JedHelper::isAdminOrSuperUser())
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
	 * Method to delete an item
	 *
	 * @param   int  $id  Element id
	 *
	 * @return  bool
	 * @since 4.0.0
	 */
	/*public function delete($id)
	{
		$table = $this->getTable();

		if (empty($result) || JedHelper::isAdminOrSuperUser() || $table->created_by == Factory::getUser()->id)
		{
			return $table->delete($id);
		}
		else
		{
			throw new Exception(Text::_("JERROR_ALERTNOAUTHOR"), 401);
		}
	}*/

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since    4.0.0
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
			$id = Factory::getApplication()->getUserState('com_jed.edit.ticketmessage.id');
		}
		else
		{
			$id = Factory::getApplication()->input->get('id');
			Factory::getApplication()->setUserState('com_jed.edit.ticketmessage.id', $id);
		}

		$this->setState('ticketmessage.id', $id);

		// Load the parameters.
		$params       = $app->getParams();
		$params_array = $params->toArray();

		if (isset($params_array['item_id']))
		{
			$this->setState('ticketmessage.id', $params_array['item_id']);
		}

		$this->setState('params', $params);
	}


}
