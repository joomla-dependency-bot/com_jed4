<?php
/**
 * @package    JED
 *
 * @copyright  Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Jed\Component\Jed\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Toolbar\Toolbar;
use function defined;

/**
 * JED Helper
 *
 * @since     4.0.0
 * @package   JED
 */
class JedHelper
{
	/**
	 * Add config toolbar to admin pages
	 * @since 4.0.0s
	 */
	public static function addConfigToolbar(Toolbar $bar)
	{

		$newbutton = $bar->linkButton('tickets')
			->text(Text::_('COM_JED_TITLE_TICKETS'))
			->url('index.php?option=com_jed&view=tickets')
			->icon('fa fa-ticket-alt');

		$newbutton = $bar->linkButton('extensions')
			->text(Text::_('COM_JED_TITLE_EXTENSIONS'))
			->url('index.php?option=com_jed&view=extensions')
			->icon('fa fa-puzzle-piece');


		$newbutton = $bar->linkButton('reviews')
			->text(Text::_('COM_JED_TITLE_REVIEWS'))
			->url('index.php?option=com_jed&view=reviews')
			->icon('fa fa-comments');


		$newbutton = $bar->linkButton('vulnerable')
			->text('Vulnerable Items')
			->url('index.php?option=com_jed&view=vel')
			->icon('fa fa-bug');

		$newbutton = $bar->customHtml('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');

		$configGroup = $bar->dropdownButton('config-group')
			->text(Text::_('COM_JED_GENERAL_CONFIG_LABEL'))
			->toggleSplit(false)
			->icon('fa fa-cog')
			->buttonClass('btn btn-action')
			->listCheck(false);

		$configChild = $configGroup->getChildToolbar();

		$configChild->linkButton('emailtemplates')
			->text('COM_JED_TITLE_EMAILTEMPLATES')
			->icon('fa fa-envelope')
			->url('index.php?option=com_jed&view=emailtemplates');

		$configChild->linkButton('messagetemplates')
			->text('COM_JED_TITLE_MESSAGETEMPLATES')
			->icon('fa fa-comment')
			->url('index.php?option=com_jed&view=messagetemplates');

		$configChild->linkButton('ticketgroups')
			->text('COM_JED_TITLE_ALLOCATEDGROUPS')
			->icon('fa fa-user-friends')
			->url('index.php?option=com_jed&view=ticketallocatedgroups');

		$configChild->linkButton('ticketcategories')
			->text('COM_JED_TITLE_TICKET_CATEGORIES')
			->icon('fa fa-folder')
			->url('index.php?option=com_jed&view=ticketcategories');

		$configChild->linkButton('ticketlinkeditemtypes')
			->text('COM_JED_TITLE_LINKED_ITEM_TYPES')
			->icon('fa fa-link')
			->url('index.php?option=com_jed&view=ticketlinkeditemtypes');
        
        	$newbutton = $bar->customHtml('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;');
        
        $debugGroup = $bar->dropdownButton('debug-group')
			->text('Debug')
			->toggleSplit(false)
			->icon('fa fa-cog')
			->buttonClass('btn btn-action')
			->listCheck(false);
        
        $debugChild = $debugGroup->getChildToolbar();
        
        $debugChild->linkButton('ticketmessages')
			->text('Ticket Messages')
			->icon('fa fa-link')
			->url('index.php?option=com_jed&view=ticketmessages');
        
        $debugChild->linkButton('ticketinternalnotes')
			->text('Ticket Internal Notes')
			->icon('fa fa-link')
			->url('index.php?option=com_jed&view=ticketinternalnotes');
        
        $debugChild->linkButton('jedtickets')
			->text('JED Tickets')
			->icon('fa fa-link')
			->url('index.php?option=com_jed&view=jedtickets');
	}


	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return CMSObject
	 *
	 * @since    4.0.0
	 */
	public static function getActions(): CMSObject
	{
		$user   = Factory::getUser();
		$result = new CMSObject();

		$assetName = 'com_jed';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action)
		{
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}

}

