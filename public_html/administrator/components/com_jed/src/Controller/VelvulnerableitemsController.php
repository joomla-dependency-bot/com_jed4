<?php
/**
 * @package       JED
 *
 * @subpackage    VEL
 *
 * @copyright     Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Jed\Component\Jed\Administrator\Controller;

defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Router\Route;
use Joomla\Utilities\ArrayHelper;

/**
 * Velvulnerableitems class.
 *
 * @since  4.0.0
 */
class VelvulnerableitemsController extends AdminController
{


	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    Optional. Model name
	 * @param   string  $prefix  Optional. Class prefix
	 * @param   array   $config  Optional. Configuration array for model
	 *
	 * @return  object    The Model
	 *
	 * @since 4.0.0
	 */
	public function getModel($name = 'Velvulnerableitem', $prefix = 'Administrator', $config = array()): object
	{

		return parent::getModel($name, $prefix, array('ignore_request' => true));
	}

	/**
	 * Publish/Unpublish Vulnerable Item
	 *
	 * @return    void
	 *
	 * @throws Exception
	 * @since    4.0.0
	 */
	public function publish()
	{

		// Checking if the user can remove object
		$user = Factory::getUser();

		if ($user->authorise('core.edit', 'com_jed') || $user->authorise('core.edit.state', 'com_jed'))
		{
			$model = $this->getModel();

			// Get the user data.

			$id = $this->input->getInt('cid');

			$values = array('publish' => 1, 'unpublish' => 0, 'deleteOverrideHistory' => -3);
			$task   = $this->getTask();
			$value  = ArrayHelper::getValue($values, $task, 0, 'int');

			$return = $model->publish($id, $value);

			// Check for errors.
			if ($return === false)
			{
				$this->setMessage(Text::sprintf('Save failed: %s', $model->getError()), 'warning');
			}


			$this->setRedirect(Route::_('index.php?option=com_jed&view=velvulnerableitems', false));

		}
		else
		{
			throw new Exception(500);
		}
	}

	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 * @throws Exception
	 * @since 4.0.0
	 *
	 */
	public function saveOrderAjax()
	{
		// Get the input
		$input = Factory::getApplication()->input;
		$pks   = $input->post->get('cid', array(), 'array');
		$order = $input->post->get('order', array(), 'array');

		// Sanitize the input
		ArrayHelper::toInteger($pks);
		ArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		Factory::getApplication()->close();
	}

}
