<?php
/**
 * @package       JED
 *
 * @subpackage    VEL
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
 * Velabandonedreport table
 *
 * @since  4.0.0
 */
class VelabandonedreportTable extends Table
{

	/**
	 * Constructor
	 *
	 * @param   DatabaseDriver  $db  A database connector object
	 *
	 * @since    4.0.0
	 */
	public function __construct(DatabaseDriver $db)
	{
		$this->typeAlias = 'com_jed.velabandonedreport';
		parent::__construct('#__jed_vel_abandoned_report', 'id', $db);
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
	 * @throws Exception
	 * @since   4.0.0
	 * @see     Table:bind
	 */
	public function bind($src, $ignore = ''): ?string
	{
		$date = Factory::getDate();

		$src['date_submitted'] = $date->toSQL();
		// Support for multiple field: consent_to_process
		if (isset($src['consent_to_process']))
		{
			if (is_array($src['consent_to_process']))
			{
				$src['consent_to_process'] = implode(',', $src['consent_to_process']);
			}
			elseif (strpos($src['consent_to_process'], ',') != false)
			{
				$src['consent_to_process'] = explode(',', $src['consent_to_process']);
			}
			elseif (strlen($src['consent_to_process']) == 0)
			{
				$src['consent_to_process'] = '';
			}
		}
		else
		{
			$src['consent_to_process'] = '';
		}

		// Support for multiple field: passed_to_vel
		if (isset($src['passed_to_vel']))
		{
			if (is_array($src['passed_to_vel']))
			{
				$src['passed_to_vel'] = implode(',', $src['passed_to_vel']);
			}
			elseif (strpos($src['passed_to_vel'], ',') != false)
			{
				$src['passed_to_vel'] = explode(',', $src['passed_to_vel']);
			}
			elseif (strlen($src['passed_to_vel']) == 0)
			{
				$src['passed_to_vel'] = '';
			}
		}
		else
		{
			$src['passed_to_vel'] = '';
		}

		// Support for multiple field: data_source
		if (isset($src['data_source']))
		{
			if (is_array($src['data_source']))
			{
				$src['data_source'] = implode(',', $src['data_source']);
			}
			elseif (strpos($src['data_source'], ',') != false)
			{
				$src['data_source'] = explode(',', $src['data_source']);
			}
			elseif (strlen($src['data_source']) == 0)
			{
				$src['data_source'] = '';
			}
		}
		else
		{
			$src['data_source'] = '';
		}

		// Support for empty date field: date_submitted
		if ($src['date_submitted'] == '0000-00-00' || empty($src['date_submitted']))
		{
			$src['date_submitted'] = null;
		}

		if ($src['id'] == 0 && empty($src['created_by']))
		{
			$src['created_by'] = Factory::getUser()->id;
		}

		if ($src['id'] == 0 && empty($src['modified_by']))
		{
			$src['modified_by'] = Factory::getUser()->id;
		}
		$input = Factory::getApplication()->input;
		$task  = $input->getString('task', '');
		if ($task == 'apply' || $task == 'save')
		{
			$src['modified_by'] = Factory::getUser()->id;
		}

		if ($src['id'] == 0)
		{
			$src['created'] = $date->toSql();
		}

		if ($task == 'apply' || $task == 'save')
		{
			$src['modified'] = $date->toSql();
		}


		if (!Factory::getUser()->authorise('core.admin', 'com_jed.velabandonedreport.' . $src['id']))
		{
			$actions         = Access::getActionsFromFile(
				JPATH_ADMINISTRATOR . '/components/com_jed/access.xml',
				"/access/section[@name='velabandonedreport']/"
			);
			$default_actions = Access::getAssetRules('com_jed.velabandonedreport.' . $src['id'])->getData();
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
	 * @since    4.0.0
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
	 * @since    4.0.0
	 *
	 * @see      Table::_getAssetName
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
