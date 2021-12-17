<?php
/**
 * @package       JED
 *
 * @subpackage    VEL
 *
 * @copyright     Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Jed\Component\Jed\Administrator\Model;
// No direct access.
defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Table\Table;

/**
 * VEL Vulnerable Item model.
 *
 * @since  4.0.0
 */
class VelvulnerableitemModel extends AdminModel
{
	/**
	 * @since  4.0.0
	 * @var    string    Alias to manage history control
	 */
	public $typeAlias = 'com_jed.velvulnerableitem';
	/**
	 * @since  4.0.0
	 * @var      string    The prefix to use with controller messages.
	 */
	protected $text_prefix = 'COM_JED';
	/**
	 * @since  4.0.0
	 * @var null  Item data
	 */
	protected $item = null;

	/**
	 * @since 4.0.0
	 * @var int ID Of VEL Report
	 */
	protected int $idVelReport = -1;

	/**
	 * @since 4.0.0
	 * @var int ID Of VEL linked item (report, abandoned report or developer update
	 */
	protected int $linked_item_id = -1;

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  Form|bool  A Form object on success, false on failure
	 *
	 * @throws
	 * @since  4.0.0
	 *
	 */
	public function getForm($data = array(), $loadData = true): Form
	{

		// Get the form.
		$form = $this->loadForm(
			'com_jed.velvulnerableitem', 'velvulnerableitem',
			array('control'   => 'jform',
			      'load_data' => $loadData
			)
		);


		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  object|bool    Object on success, false on failure.
	 *
	 * @throws Exception
	 * @since  4.0.0
	 */
	public function getItem($pk = null)
	{
		$pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');

		return parent::getItem($pk);
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $name     The table type to instantiate
	 * @param   string  $prefix   A prefix for the table class name. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return    Table    A database object
	 *
	 * @throws Exception
	 * @since  4.0.0
	 */
	public function getTable($name = 'Velvulnerableitem', $prefix = 'Administrator', $options = array()): Table
	{
		return parent::getTable($name, $prefix, $options);
	}


	/**
	 * Gets VEL Linked Reports
	 * @return array
	 *
	 * @throws Exception
	 * @since version
	 */
	public function getVELLinkedReports() : array
	{

		$input = Factory::getApplication()->input;

		$vel_item_id = $input->get('id');


		$output['velreport']          = null;
		$output['veldeveloperupdate'] = null;
		$output['velabandonware']     = null;

		$velReportData  = $this->getVelReportData($vel_item_id);
		$velReportModel = BaseDatabaseModel::getInstance('Velreport', 'JedModel', ['ignore_request' => true]);
		$velReportForm  = $velReportModel->getForm($velReportData, false);
		$velReportForm->bind($velReportData);

		$output['velreport']['data']  = $velReportData;
		$output['velreport']['model'] = $velReportModel;
		$output['velreport']['form']  = $velReportForm;

		$velDeveloperUpdateData  = $this->getvelDeveloperUpdateData($vel_item_id);
		$velDeveloperUpdateModel = BaseDatabaseModel::getInstance('Veldeveloperupdate', 'JedModel', ['ignore_request' => true]);
		$velDeveloperUpdateForm  = $velDeveloperUpdateModel->getForm($velDeveloperUpdateData, false);
		$velDeveloperUpdateForm->bind($velDeveloperUpdateData);

		$output['veldeveloperupdate']['data']  = $velDeveloperUpdateData;
		$output['veldeveloperupdate']['model'] = $velDeveloperUpdateModel;
		$output['veldeveloperupdate']['form']  = $velDeveloperUpdateForm;

		$velAbandonwareDataData  = $this->getvelAbandonwareData($vel_item_id);
		$velAbandonwareDataModel = BaseDatabaseModel::getInstance('Velabandonedreport', 'JedModel', ['ignore_request' => true]);
		$velAbandonwareDataForm  = $velAbandonwareDataModel->getForm($velAbandonwareDataData, false);
		$velAbandonwareDataForm->bind($velAbandonwareDataData);

		$output['velabandonware']['data']  = $velAbandonwareDataData;
		$output['velabandonware']['model'] = $velAbandonwareDataModel;
		$output['velabandonware']['form']  = $velAbandonwareDataForm;

		return $output;
	}

	public function getVelAbandonwareData(int $vel_item_id)
	{

		// Create a new query object.
		$db    = Factory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select('a.*');

		// From the vel_report table
		$query->from($db->quoteName('#__jed_vel_abandoned_report', 'a'));

		if (is_numeric($vel_item_id))
		{
			$query->where('a.vel_item_id = ' . (int) $vel_item_id);
		}
		else
		{
			$query->where('a.vel_item_id = -5');
		}


		// Load the items
		$db->setQuery($query);
		$db->execute();
		if ($db->getNumRows())
		{
			return $db->loadObjectList();

		}

		return array();
	}

	public function getVelDeveloperUpdateData(int $vel_item_id)
	{

		// Create a new query object.
		$db    = Factory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select('a.*');

		// From the vel_report table
		$query->from($db->quoteName('#__jed_vel_developer_update', 'a'));

		if (is_numeric($vel_item_id))
		{
			$query->where('a.vel_item_id = ' . (int) $vel_item_id);
		}
		else
		{
			$query->where('a.vel_item_id = -5');
		}


		// Load the items
		$db->setQuery($query);
		$db->execute();
		if ($db->getNumRows())
		{
			return $db->loadObjectList();

		}

		return array();
	}

	public function getVelReportData(int $vel_item_id)
	{

		// Create a new query object.
		$db    = Factory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select('a.*');

		// From the vel_report table
		$query->from($db->quoteName('#__jed_vel_report', 'a'));


		if (is_numeric($vel_item_id))
		{
			$query->where('a.vel_item_id = ' . (int) $vel_item_id);
		}
		else
		{
			$query->where('a.vel_item_id = -5');
		}


		// Load the items
		$db->setQuery($query);
		$db->execute();
		if ($db->getNumRows())
		{
			return $db->loadObjectList();
		}

		return array();
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return   mixed  The data for the form.
	 *
	 * @throws
	 * @since  4.0.0
	 *
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_jed.edit.velvulnerableitem.data', array());

		if (empty($data))
		{
			if ($this->item === null)
			{
				$this->item = $this->getItem();
			}

			$data = $this->item;


			// Support for multiple or not foreign key field: status
			$array = array();

			foreach ((array) $data->status as $value)
			{
				if (!is_array($value))
				{
					$array[] = $value;
				}
			}
			if (!empty($array))
			{

				$data->status = $array;
			}

			// Support for multiple or not foreign key field: risk_level
			$array = array();

			foreach ((array) $data->risk_level as $value)
			{
				if (!is_array($value))
				{
					$array[] = $value;
				}
			}
			if (!empty($array))
			{

				$data->risk_level = $array;
			}

			// Support for multiple or not foreign key field: exploit_type
			$array = array();

			foreach ((array) $data->exploit_type as $value)
			{
				if (!is_array($value))
				{
					$array[] = $value;
				}
			}
			if (!empty($array))
			{

				$data->exploit_type = $array;
			}

			// Support for multiple or not foreign key field: discoverer_public
			$array = array();

			foreach ((array) $data->discoverer_public as $value)
			{
				if (!is_array($value))
				{
					$array[] = $value;
				}
			}
			if (!empty($array))
			{

				$data->discoverer_public = $array;
			}
		}

		return $data;
	}

	/**
	 * Publish the element
	 *
	 * @param   int  $pks    Item id
	 * @param   int  $value  Publish state
	 *
	 * @return  boolean
	 * @throws Exception
	 * @since 4.0.0
	 */
	public function publish(&$pks = array(), int $value = 1): bool
	{

		if (!Factory::getUser()->authorise('core.edit.state', 'com_jed'))
		{
			Factory::getApplication()->enqueueMessage(Text::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), 'error');

			return false;
		}

		$result = true;
		if (!is_array($pks))
		{
			$pks = array($pks);
		}
		$table = $this->getTable();


		if (!$table->publish($pks, $value))
		{
			$this->setError($table->getError());
			$result = false;
		}


		return $result;

	}

}
