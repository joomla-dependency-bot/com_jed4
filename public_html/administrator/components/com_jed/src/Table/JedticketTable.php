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
 * Jedticket table
 *
 * @since  4.0.0
 */
class JedticketTable extends Table
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
		$this->typeAlias = 'com_jed.jedticket';
		parent::__construct('#__jed_jedtickets', 'id', $db);
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
	 * @throws Exception
	 * @see     Table:bind
	 */
	public function bind($src, $ignore = ''): ?string
	{
		$date = Factory::getDate();


		// Support for multiple field: ticket_origin
		if (isset($src['ticket_origin']))
		{
			if (is_array($src['ticket_origin']))
			{
				$src['ticket_origin'] = implode(',', $src['ticket_origin']);
			}
			elseif (strpos($src['ticket_origin'], ',') != false)
			{
				$src['ticket_origin'] = explode(',', $src['ticket_origin']);
			}
			elseif (strlen($src['ticket_origin']) == 0)
			{
				$src['ticket_origin'] = '';
			}
		}
		else
		{
			$src['ticket_origin'] = '';
		}

		// Support for multiple or not foreign key field: ticket_category_type
		if (!empty($src['ticket_category_type']))
		{
			if (is_array($src['ticket_category_type']))
			{
				$src['ticket_category_type'] = implode(',', $src['ticket_category_type']);
			}
			else if (strrpos($src['ticket_category_type'], ',') != false)
			{
				$src['ticket_category_type'] = explode(',', $src['ticket_category_type']);
			}
		}
		else
		{
			$src['ticket_category_type'] = 0;
		}

		// Support for multiple or not foreign key field: allocated_group
		if (!empty($src['allocated_group']))
		{
			if (is_array($src['allocated_group']))
			{
				$src['allocated_group'] = implode(',', $src['allocated_group']);
			}
			else if (strrpos($src['allocated_group'], ',') != false)
			{
				$src['allocated_group'] = explode(',', $src['allocated_group']);
			}
		}
		else
		{
			$src['allocated_group'] = 0;
		}

		// Support for multiple or not foreign key field: linked_item_type
		if (!empty($src['linked_item_type']))
		{
			if (is_array($src['linked_item_type']))
			{
				$src['linked_item_type'] = implode(',', $src['linked_item_type']);
			}
			else if (strrpos($src['linked_item_type'], ',') != false)
			{
				$src['linked_item_type'] = explode(',', $src['linked_item_type']);
			}
		}
		else
		{
			$src['linked_item_type'] = 0;
		}

		// Support for multiple field: ticket_status
		if (isset($src['ticket_status']))
		{
			if (is_array($src['ticket_status']))
			{
				$src['ticket_status'] = implode(',', $src['ticket_status']);
			}
			elseif (strpos($src['ticket_status'], ',') != false)
			{
				$src['ticket_status'] = explode(',', $src['ticket_status']);
			}
			elseif (strlen($src['ticket_status']) == 0)
			{
				$src['ticket_status'] = '';
			}
		}
		else
		{
			$src['ticket_status'] = '';
		}
		$input = Factory::getApplication()->input;
		$task  = $input->getString('task', '');

		if ($src['id'] == 0 && empty($src['created_by']))
		{
			$src['created_by'] = Factory::getUser()->id;
		}

		if ($src['id'] == 0)
		{
			$src['created_on'] = $date->toSql();
		}

		if ($src['id'] == 0 && empty($src['modified_by']))
		{
			$src['modified_by'] = Factory::getUser()->id;
		}

		if ($task == 'apply' || $task == 'save')
		{
			$src['modified_by'] = Factory::getUser()->id;
		}

		if ($task == 'apply' || $task == 'save')
		{
			$src['modified_on'] = $date->toSql();
		}


		if (!Factory::getUser()->authorise('core.admin', 'com_jed.jedticket.' . $src['id']))
		{
			$actions         = Access::getActionsFromFile(
				JPATH_ADMINISTRATOR . '/components/com_jed/access.xml',
				"/access/section[@name='jedticket']/"
			);
			$default_actions = Access::getAssetRules('com_jed.jedticket.' . $src['id'])->getData();
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
