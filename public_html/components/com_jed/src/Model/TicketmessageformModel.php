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
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Table\Table;
use Joomla\Utilities\ArrayHelper;

/**
 * Jed model.
 *
 * @since  4.0.0
 */
class TicketmessageformModel extends FormModel
{
	private $item = null;
	/** Data Table
	 * @since 4.0.0
	 **/
	private string $dbtable = "#__jed_ticket_messages";



	/**
	 * Method to get the table
	 *
	 * @param   string  $name
	 * @param   string  $prefix  Optional prefix for the table class name
	 * @param   array   $options
	 *
	 * @return  Table|boolean Table if found, boolean false on failure
	 *
	 * @throws Exception
	 * @since 4.0.0
	 */
	public function getTable($name = 'Ticketmessage', $prefix = 'Administrator', $options = array())
	{

		return parent::getTable($name, $prefix, $options);
	}


	/**
	 * Method to check in an item.
	 *
	 * @param   int|null  $pk  The id of the row to check out.
	 *
	 * @return  boolean True on success, false on failure.
	 *
	 * @throws Exception
	 * @since    4.0.0
	 */
	public function checkin(int $pk = null) : bool
	{
		// Get the id.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('ticketmessage.id');
		if (!$pk || JedHelper::userIDItem($pk, $this->dbtable) || JedHelper::isAdminOrSuperUser())
		{
			if ($pk)
			{
				// Initialise the table
				$table = $this->getTable();

				// Attempt to check the row in.
				if (method_exists($table, 'checkin'))
				{
					if (!$table->checkin($pk))
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
	 * Method to check out an item for editing.
	 *
	 * @param   int|null  $pk  The id of the row to check out.
	 *
	 * @return  boolean True on success, false on failure.
	 *
	 * @throws Exception
	 * @since    4.0.0
	 */
	public function checkout(int $pk = null) : bool
	{
		// Get the user id.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('ticketmessage.id');
		if (!$pk || JedHelper::userIDItem($pk, $this->dbtable) || JedHelper::isAdminOrSuperUser())
		{
			if ($pk)
			{
				// Initialise the table
				$table = $this->getTable();

				// Get the current user object.
				$user = Factory::getUser();

				// Attempt to check the row out.
				if (method_exists($table, 'checkout'))
				{
					if (!$table->checkout($user->get('id'), $pk))
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
	 * Method to get the profile form.
	 *
	 * The base form is loaded from XML
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return    Form    A Form object on success, false on failure
	 *
	 * @throws Exception
	 * @since    4.0.0
	 */
	public function getForm($data = array(), $loadData = true): Form
	{
		// Get the form.
		$form = $this->loadForm('com_jed.ticketmessage', 'ticketmessageform', array(
				'control'   => 'jform',
				'load_data' => $loadData
			)
		);

		if (!is_object($form))
		{
			throw new Exception(Text::_('JERROR_LOADFILE_FAILED'), 500);
		}

		return $form;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data
	 *
	 * @return bool
	 *
	 * @throws Exception
	 * @since 4.0.0
	 */
	public function save(array $data) : bool
	{
		$id    = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('ticketmessage.id');

		$isLoggedIn      = JedHelper::IsLoggedIn();

//print_r($data);

		if (!$id || JedHelper::userIDItem($id, $this->dbtable) || JedHelper::isAdminOrSuperUser()  && $isLoggedIn)
		{

			$table = $this->getTable();

			if ($table->save($data) === true)
			{

				return $table->id;
			}
			else
			{

				return false;
			}
		}
		else
		{
			throw new Exception(Text::_("JERROR_ALERTNOAUTHOR"), 401);
		}
	}

	/**
	 * Method to delete data
	 *
	 * @param   int  $pk  Item primary key
	 *
	 * @return  int  The id of the deleted item
	 *
	 * @throws Exception
	 *
	 * @since 4.0.0
	 */
/*	public function delete($pk) : bool
	{
		$user = Factory::getUser();

		if (!$pk || JedHelper::userIDItem($pk, $this->dbtable) || JedHelper::isAdminOrSuperUser())
		{
			if (empty($pk))
			{
				$pk = (int) $this->getState('ticketmessage.id');
			}

			if ($pk == 0 || $this->getItem($pk) == null)
			{
				throw new Exception(Text::_('COM_JED_ITEM_DOESNT_EXIST'), 404);
			}

			if ($user->authorise('core.delete', 'com_jed') !== true)
			{
				throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
			}

			$table = $this->getTable();

			if ($table->delete($pk) !== true)
			{
				throw new Exception(Text::_('JERROR_FAILED'), 501);
			}

			return $pk;
		}
		else
		{
			throw new Exception(Text::_("JERROR_ALERTNOAUTHOR"), 401);
		}
	} */

	/**
	 * Method to get an ojbect.
	 *
	 * @param   int|null  $id  The id of the object to get.
	 *
	 * @return Object|boolean Object on success, false on failure.
	 *
	 * @throws Exception
	 * @since 4.0.0
	 */
	public function getItem(int $id = null)
	{
		if ($this->item === null)
		{
			$this->item = false;

			if (empty($id))
			{
				$id = $this->getState('ticketmessage.id');
			}

			// Get a level row instance.
			$table = $this->getTable();

			if ($table !== false && $table->load($id) && !empty($table->id))
			{
				$user = Factory::getUser();
				$id   = $table->id;
				if (empty($id) || JedHelper::isAdminOrSuperUser() || $table->created_by == Factory::getUser()->id)
				{

					$canEdit = $user->authorise('core.edit', 'com_jed') || $user->authorise('core.create', 'com_jed');

					if (!$canEdit && $user->authorise('core.edit.own', 'com_jed'))
					{
						$canEdit = $user->id == $table->created_by;
					}

					if (!$canEdit)
					{
						throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
					}

					// Check published state.
					if ($published = $this->getState('filter.published'))
					{
						if (isset($table->state) && $table->state != $published)
						{
							return $this->item;
						}
					}

					// Convert the Table to a clean CMSObject.
					$properties = $table->getProperties(1);
					$this->item = ArrayHelper::toObject($properties, CMSObject::class);

					if (isset($this->item->category_id) && is_object($this->item->category_id))
					{
						$this->item->category_id = ArrayHelper::fromObject($this->item->category_id);
					}

				}
				else
				{
					throw new Exception(Text::_("JERROR_ALERTNOAUTHOR"), 401);
				}
			}
		}

		return $this->item;
	}

	/**
	 * Check if data can be saved
	 *
	 * @return bool
	 *
	 * @throws Exception
	 * @since 4.0.0
	 */
	public function getCanSave() : bool
	{
		$table = $this->getTable();

		return $table !== false;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since  4.0.0
	 *
	 */
	protected function populateState()
	{
		$app = Factory::getApplication();

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

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return    array  The default data is an empty array.
	 * @throws Exception
	 * @since    4.0.0
	 */
	protected function loadFormData()
	{
		$data = Factory::getApplication()->getUserState('com_jed.edit.ticketmessage.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		if ($data)
		{


			return $data;
		}

		return array();
	}

}
