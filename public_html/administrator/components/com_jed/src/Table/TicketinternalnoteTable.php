<?php
/**
 * @package       JED
 *
 * @subpackage    Tickets
 *
 * @copyright     Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Jed\Component\Jed\Administrator\Table;
// No direct access
defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Access\Access;
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table as Table;
use Joomla\Database\DatabaseDriver;


/**
 * Ticketinternalnote table
 *
 * @since  4.0.0
 */
class TicketinternalnoteTable extends Table
{

	/**
	 * Constructor
	 *
	 * @param   DatabaseDriver  $db  A database connector object
	 *
	 * @since 4.0.0
	 */
	public function __construct(DatabaseDriver $db)
	{
		$this->typeAlias = 'com_jed.ticketinternalnote';
		parent::__construct('#__jed_ticket_internal_notes', 'id', $db);
		$this->setColumnAlias('published', 'state');

	}

	/**
	 * Get the type alias for the history table
	 *
	 * @return  string  The alias as described above
	 *
	 * @since   4.0.0
	 */
	public function getTypeAlias(): string
	{
		return $this->typeAlias;
	}

	/**
	 * Overloaded bind function to pre-process the params.
	 *
	 * @param   array  $src     Named array
	 * @param   mixed  $ignore  Optional array or list of parameters to ignore
	 *
	 * @return  null|string  null is operation was satisfactory, otherwise returns an error
	 *
	 * @since   4.0.0
	 * @throws  Exception
	 * @see     Table:bind
	 */
	public function bind($src, $ignore = ''): ?string
	{
		$input = Factory::getApplication()->input;
		$task  = $input->getString('task', '');

		if ($src['id'] == 0 && empty($src['created_by']))
		{
			$src['created_by'] = Factory::getUser()->id;
		}

		if ($src['id'] == 0 && empty($src['modified_by']))
		{
			$src['modified_by'] = Factory::getUser()->id;
		}

		if ($task == 'apply' || $task == 'save')
		{
			$src['modified_by'] = Factory::getUser()->id;
		}

		// Support for multiple or not foreign key field: ticket_id
		if (!empty($src['ticket_id']))
		{
			if (is_array($src['ticket_id']))
			{
				$src['ticket_id'] = implode(',', $src['ticket_id']);
			}
			else if (strrpos($src['ticket_id'], ',') != false)
			{
				$src['ticket_id'] = explode(',', $src['ticket_id']);
			}
		}
		else
		{
			$src['ticket_id'] = 0;
		}

		if (!Factory::getUser()->authorise('core.admin', 'com_jed.ticketinternalnote.' . $src['id']))
		{
			$actions         = Access::getActionsFromFile(
				JPATH_ADMINISTRATOR . '/components/com_jed/access.xml',
				"/access/section[@name='ticketinternalnote']/"
			);
			$default_actions = Access::getAssetRules('com_jed.ticketinternalnote.' . $src['id'])->getData();
			$array_jaccess   = array();

			foreach ($actions as $action)
			{
				if (key_exists($action->name, $default_actions))
				{
					$array_jaccess[$action->name] = $default_actions[$action->name];
				}
			}

			$src['rules'] = $this->JAccessRulestoArray($array_jaccess);
		}

		// Bind the rules for ACL where supported.
		if (isset($src['rules']) && is_array($src['rules']))
		{
			$this->setRules($src['rules']);
		}

		return parent::bind($src, $ignore);
	}

	/**
	 * This function convert an array of Access objects into an rules array.
	 *
	 * @param   array  $jaccessrules  An array of Access objects.
	 *
	 * @return  array
	 * @since 4.0.0
	 */
	private function JAccessRulestoArray(array $jaccessrules): array
	{
		$rules = array();

		foreach ($jaccessrules as $action => $jaccess)
		{
			$actions = array();

			if ($jaccess)
			{
				foreach ($jaccess->getData() as $group => $allow)
				{
					$actions[$group] = ((bool) $allow);
				}
			}

			$rules[$action] = $actions;
		}

		return $rules;
	}

	/**
	 * Delete a record by id
	 *
	 * @param   mixed  $pk  Primary key value to delete. Optional
	 *
	 * @return bool
	 *
	 * @since 4.0.0
	 */
	public function delete($pk = null): bool
	{
		$this->load($pk);

		return parent::delete($pk);
	}

	/**
	 * Define a namespaced asset name for inclusion in the #__assets table
	 *
	 * @return string The asset name
	 *
	 * @since 4.0.0
	 * @see   Table::_getAssetName
	 */
	protected function _getAssetName(): string
	{
		$k = $this->_tbl_key;

		return $this->typeAlias . '.' . (int) $this->$k;
	}

	/**
	 * Returns the parent asset's id. If you have a tree structure, retrieve the parent's id using the external key field
	 *
	 * @param   Table|null  $table  Table name
	 * @param   int|null    $id     Id
	 *
	 * @return mixed The id on success, false on failure.
	 * @since 4.0.0
	 * @see   Table::_getAssetParentId
	 *
	 */
	protected function _getAssetParentId(Table $table = null, int $id = null)
	{
		// We will retrieve the parent-asset from the Asset-table
		$assetParent = Table::getInstance('Asset');

		// Default: if no asset-parent can be found we take the global asset
		$assetParentId = $assetParent->getRootId();

		// The item has the component as asset-parent
		$assetParent->loadByName('com_jed');

		// Return the found asset-parent-id
		if ($assetParent->id)
		{
			$assetParentId = $assetParent->id;
		}

		return $assetParentId;
	}
}
