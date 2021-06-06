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
 * Messagetemplate table
 *
 * @since  4.0.0
 */
class MessagetemplateTable extends Table
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
		$this->typeAlias = 'com_jed.messagetemplate';
		parent::__construct('#__jed_message_templates', 'id', $db);
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
		$task = Factory::getApplication()->input->get('task');


		// Support for multiple field: email_type
		if (isset($src['email_type']))
		{
			if (is_array($src['email_type']))
			{
				$src['email_type'] = implode(',', $src['email_type']);
			}
			elseif (strpos($src['email_type'], ',') != false)
			{
				$src['email_type'] = explode(',', $src['email_type']);
			}
			elseif (strlen($src['email_type']) == 0)
			{
				$src['email_type'] = '';
			}
		}
		else
		{
			$src['email_type'] = '';
		}

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

		if ($src['id'] == 0)
		{
			$src['created'] = $date->toSql();
		}

		if ($task == 'apply' || $task == 'save')
		{
			$src['modified'] = $date->toSql();
		}

		if (!Factory::getUser()->authorise('core.admin', 'com_jed.emailtemplate.' . $src['id']))
		{
			$actions         = Access::getActionsFromFile(
				JPATH_ADMINISTRATOR . '/components/com_jed/access.xml',
				"/access/section[@name='messagetemplate']/"
			);
			$default_actions = Access::getAssetRules('com_jed.messagetemplate.' . $src['id'])->getData();
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
	 * Overloaded check function
	 *
	 * @return bool
	 *
	 * @since 4.0.0
	 */
	public function check(): bool
	{
		// If there is an ordering column and this is a new row then get the next ordering value
		if (property_exists($this, 'ordering') && $this->id == 0)
		{
			$this->ordering = self::getNextOrder();
		}


		return parent::check();
	}

	/**
	 * Define a namespaced asset name for inclusion in the #__assets table
	 *
	 * @return string The asset name
	 *
	 * @since 4.0.0
	 * @see   Table::_getAssetName
	 *
	 */
	protected function _getAssetName(): string
	{
		$k = $this->_tbl_key;

		return $this->typeAlias . '.' . (int) $this->$k;
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
}
