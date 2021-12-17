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
use Jed\Component\Jed\Site\Helper\JedemailHelper;
use Jed\Component\Jed\Site\Helper\JedHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Table\Table;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use function defined;

/**
 * VEL Developer Update Form Model Class.
 *
 * @since 4.0.0
 */
class VeldeveloperupdateformModel extends FormModel
{
	private $item = null;

    /** Data Table
	 * @since 4.0.0
	 **/
    private string $dbtable="#__jed_vel_developer_update";
	/**
	 * Method to get the profile form.
	 *
	 * The base form is loaded from XML
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return    Form    A JForm object on success, false on failure
	 *
	 * @throws Exception
	 * @since 4.0.0
	 */
	public function getForm($data = array(), $loadData = true): Form
	{
		// Get the form.
		$form = $this->loadForm('com_jed.veldeveloperupdate', 'veldeveloperupdateform', array(
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
	public function save(array $data): bool
	{
		$id              = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('veldeveloperupdate.id');

		$data['user_ip'] = $_SERVER['REMOTE_ADDR'];
		$isLoggedIn      = JedHelper::IsLoggedIn();
        $user = Factory::getUser();

		if ((!$id || JedHelper::isAdminOrSuperUser()) && $isLoggedIn)
		{

			/* Any logged in user can report a vulnerable Item */

			$table = $this->getTable();

			if ($table->save($data) === true)
			{
                $ticket = JedHelper::CreateVELTicket(2, $table->id);
                $ticket_message = JedHelper::CreateVELTicketMessage(3,$table->id);
                $ticket_message['subject'] = $ticket['ticket_subject'];
                $ticket_message['message']           = $ticket['ticket_text'];
                $ticket_message['message_direction'] = 1; /*  1 for coming in, 0 for going out */
                 
                
                $ticket_model = BaseDatabaseModel::getInstance('Jedticketform', 'JedModel', ['ignore_request' => true]);
				$ticket_model->save($ticket);
                $ticket_id = $ticket_model->getId();
                /* We need to store the incoming ticket message */
                $ticket_message['ticket_id'] = $ticket_id;

                $ticket_message_model =  BaseDatabaseModel::getInstance('Ticketmessageform', 'JedModel', ['ignore_request' => true]);
                //$ticket_message_model = $this->getModel('Ticketmessageform', 'Site');

                $ticket_message_model->save($ticket_message);
               
                /* We need to email standard message to user and store message in ticket */
                $message_out = JedHelper::GetMessageTemplate(1000);
                if (isset($message_out->subject))
                {
                    JedemailHelper::sendEmail($message_out->subject, $message_out->template, $user, 'mark@burninglight.co.uk');

                    $ticket_message['id'] = 0;
                    $ticket_message['subject']           = $message_out->subject;
                    $ticket_message['message']           = $message_out->template;
                    $ticket_message['message_direction'] = 0; /* 1 for coming in, 0 for going out */
                    $ticket['created_by']       = -1;
                    $ticket['modified_by']      = -1;
                $ticket_message_model->save($ticket_message);

                }

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
	 * Method to get the table
	 *
	 * @param   string  $name     Name of the JTable class
	 * @param   string  $prefix   Optional prefix for the table class name
	 * @param   array   $options  Optional configuration array for JTable object
	 *
	 * @return  Table Table if found, throws exception on failure
	 *
	 * @throws Exception
	 * @since 4.0.0
	 */
	public function getTable($name = 'Veldeveloperupdate', $prefix = 'Administrator', $options = array()): Table
	{

		return parent::getTable($name, $prefix, $options);
	}

	/**
	 * Check if data can be saved
	 *
	 * @return bool
	 * @throws Exception
	 * @since 4.0.0
	 */
	public function getCanSave(): bool
	{
		$table = $this->getTable();

		return $table !== false;
	}


	/**
	 * Method to check in an item.
	 *
	 * @param   int|null  $pk  The id of the row to check out.
	 *
	 * @return  boolean True on success, false on failure.
	 *
	 * @throws Exception
	 * @since 4.0.0
	 */
	public function checkin($pk = null): bool
	{
		// Get the id.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('veldeveloperupdate.id');
		if (!$pk || JedHelper::userIDItem($pk,$this->dbtable) || JedHelper::isAdminOrSuperUser())
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
	 * @since 4.0.0
	 */
	public function checkout(int $pk = null): bool
	{
		// Get the user id.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('veldeveloperupdate.id');
		if (!$pk || JedHelper::userIDItem($pk,$this->dbtable) || JedHelper::isAdminOrSuperUser())
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
		$app = Factory::getApplication();

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
	 * Method to get the data that should be injected in the form.
	 *
	 * @return    array  The default data is an empty array.
	 * @throws Exception
	 * @since 4.0.0
	 */
	protected function loadFormData()
	{
		$data = Factory::getApplication()->getUserState('com_jed.edit.veldeveloperupdate.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		if ($data)
		{

			// Support for multiple or not foreign key field: consent_to_process
			$array = array();

			foreach ((array) $data->consent_to_process as $value)
			{
				if (!is_array($value))
				{
					$array[] = $value;
				}
			}
			if (!empty($array))
			{

				$data->consent_to_process = $array;
			}
			// Support for multiple or not foreign key field: update_data_source
			$array = array();

			foreach ((array) $data->update_data_source as $value)
			{
				if (!is_array($value))
				{
					$array[] = $value;
				}
			}
			if (!empty($array))
			{

				$data->update_data_source = $array;
			}

			return $data;
		}

		return array();
	}

	/**
	 * Method to delete data
	 *
	 * Commented out as Reports should not be deleted from front-end
	 *
	 * @param   int  $pk  Item primary key
	 *
	 * @return  int  The id of the deleted item
	 *
	 * @throws Exception
	 *
	 * @since 4.0.0
	 */
	/*public function delete($pk)
	{
		/*$user = Factory::getUser();

		if (!$pk || JedHelper::userIDItem($pk,$this->dbtable) || JedHelper::isAdminOrSuperUser())
		{
			if (empty($pk))
			{
				$pk = (int) $this->getState('veldeveloperupdate.id');
			}

			if ($pk == 0 || $this->getItem($pk) == null)
			{
				throw new \Exception(Text::_('COM_JED_ITEM_DOESNT_EXIST'), 404);
			}

			if ($user->authorise('core.delete', 'com_jed') !== true)
			{
				throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
			}

			$table = $this->getTable();

			if ($table->delete($pk) !== true)
			{
				throw new \Exception(Text::_('JERROR_FAILED'), 501);
			}

			return $pk;
		}
		else
		{
			throw new Exception(Text::_("JERROR_ALERTNOAUTHOR"), 401);
		}
	} */

	/**
	 * Method to get an object.
	 *
	 * @param   null  $id  The id of the object to get.
	 *
	 * @return false|object|null
	 * @throws Exception
	 *
	 * @since 4.0.0
	 */
	public function getItem($id = null)
	{
		if ($this->item === null)
		{
			$this->item = false;

			if (empty($id))
			{
				$id = $this->getState('veldeveloperupdate.id');
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

					// Convert the JTable to a clean JObject.
					$properties = $table->getProperties(1);
					$this->item = ArrayHelper::toObject($properties, 'JObject');

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

}
