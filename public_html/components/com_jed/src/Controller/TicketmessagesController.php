<?php
/**
 * @package       JED
 *
 * @subpackage    TICKETS
 *
 * @copyright     Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Jed\Component\Jed\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;

/**
 * Ticketmessages class.
 *
 * @since  4.0.0
 */
class TicketmessagesController extends FormController
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional
	 * @param   array   $config  Configuration array for model. Optional
	 *
	 * @return object    The model
	 *
	 * @since    4.0.0
	 */
	public function getModel($name = 'Ticketmessages', $prefix = 'Site', $config = array()) : object
	{
		return parent::getModel($name, $prefix, array('ignore_request' => true));
	}
}
