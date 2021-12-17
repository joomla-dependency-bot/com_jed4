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
 * VEL Abandoned Report Form Model Class.
 *
 * @since  4.0.0
 */
class VelabandonedreportformModel extends FormModel
{
	private $item = null;
	/** Data Table
	 * @since 4.0.0
	 **/
	private string $dbtable = "#__jed_vel_abandoned_report";

	

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
	public function getTable($name = 'Velabandonedreport', $prefix = 'Administrator', $options = array()): Table
	{

		return parent::getTable($name, $prefix, $options);
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
	 * @since 4.0.0
	 */
	public function getForm($data = array(), $loadData = true): Form
	{
		// Get the form.
		$form = $this->loadForm('com_jed.velabandonedreport', 'velabandonedreportform', array(
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
		$id = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('velabandonedreport.id');


		$data['user_ip']       = $_SERVER['REMOTE_ADDR'];
		$data['data_source']   = CURL_HTTP_VERSION_1_0;
		$data['passed_to_vel'] = 0;

		$isLoggedIn = JedHelper::IsLoggedIn();
        $user = Factory::getUser();
 
		if ((!$id || JedHelper::isAdminOrSuperUser()) && $isLoggedIn)
		{

			/* Any logged in user can report an abandoned Item */

			$table = $this->getTable();

			if ($table->save($data) === true)
			{
                $ticket = JedHelper::CreateVELTicket(3, $table->id);
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
               
		    // exit();
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
	/*public function delete($pk)
	{
		$user = Factory::getUser();

		if (!$pk || JedHelper::userIDItem($pk,$this->dbtable) || JedHelper::isAdminOrSuperUser())
		{
			if (empty($pk))
			{
				$pk = (int) $this->getState('velabandonedreport.id');
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
	}*/

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

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return    array  The default data is an empty array.
	 * @throws Exception
	 * @since 4.0.0
	 */
	protected function loadFormData()
	{
		$data = Factory::getApplication()->getUserState('com_jed.edit.velabandonedreport.data', array());

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
			// Support for multiple or not foreign key field: passed_to_vel
			$array = array();

			foreach ((array) $data->passed_to_vel as $value)
			{
				if (!is_array($value))
				{
					$array[] = $value;
				}
			}
			if (!empty($array))
			{

				$data->passed_to_vel = $array;
			}
			// Support for multiple or not foreign key field: data_source
			$array = array();

			foreach ((array) $data->data_source as $value)
			{
				if (!is_array($value))
				{
					$array[] = $value;
				}
			}
			if (!empty($array))
			{

				$data->data_source = $array;
			}

			return $data;
		}

		return array();
	}

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
				$id = $this->getState('velabandonedreport.id');
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

}
