<?php
/**
 * @package       JED
 *
 * @subpackage    VEL
 *
 * @copyright     Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$canEdit = Factory::getUser()->authorise('core.edit', 'com_jed');

if (!$canEdit && Factory::getUser()->authorise('core.edit.own', 'com_jed'))
{
	$canEdit = Factory::getUser()->id == $this->item->created_by;
}
?>

<div class="item_fields">

    <table class="table">


        <tr>
            <th><?php echo Text::_('JGLOBAL_FIELD_ID_LABEL'); ?></th>
            <td><?php echo $this->item->id; ?></td>
        </tr>

        <tr>
            <th><?php echo Text::_('COM_JED_VEL_GENERAL_FIELD_REPORTER_FULLNAME_LABEL'); ?></th>
            <td><?php echo $this->item->reporter_fullname; ?></td>
        </tr>

        <tr>
            <th><?php echo Text::_('COM_JED_VEL_GENERAL_FIELD_REPORTER_EMAIL_LABEL'); ?></th>
            <td><?php echo $this->item->reporter_email; ?></td>
        </tr>

        <tr>
            <th><?php echo Text::_('COM_JED_VEL_GENERAL_FIELD_REPORTER_ORGANISATION_LABEL'); ?></th>
            <td><?php echo $this->item->reporter_organisation; ?></td>
        </tr>

        <tr>
            <th><?php echo Text::_('COM_JED_VEL_ABANDONEDREPORT_FIELD_EXTENSION_NAME_LABEL'); ?></th>
            <td><?php echo $this->item->extension_name; ?></td>
        </tr>

        <tr>
            <th><?php echo Text::_('COM_JED_VEL_GENERAL_FIELD_DEVELOPER_NAME_LABEL'); ?></th>
            <td><?php echo $this->item->developer_name; ?></td>
        </tr>

        <tr>
            <th><?php echo Text::_('COM_JED_VEL_ABANDONEDREPORT_FIELD_EXTENSION_VERSION_LABEL'); ?></th>
            <td><?php echo $this->item->extension_version; ?></td>
        </tr>

        <tr>
            <th><?php echo Text::_('COM_JED_VEL_ABANDONEDREPORT_FIELD_EXTENSION_URL_LABEL'); ?></th>
            <td><?php echo $this->item->extension_url; ?></td>
        </tr>

        <tr>
            <th><?php echo Text::_('COM_JED_VEL_ABANDONEDREPORTS_FIELD_ABANDONED_REASON_LABEL_LABEL'); ?></th>
            <td><?php echo nl2br($this->item->abandoned_reason); ?></td>
        </tr>

        <tr>
            <th><?php echo Text::_('COM_JED_VEL_GENERAL_FIELD_CONSENT_TO_PROCESS_NOTIFICATION_LABEL'); ?></th>
            <td><?php echo $this->item->consent_to_process; ?></td>
        </tr>

        <tr>
            <th><?php echo Text::_('COM_JED_VEL_GENERAL_FIELD_PASSED_TO_VEL_LABEL'); ?></th>
            <td><?php echo $this->item->passed_to_vel; ?></td>
        </tr>
          <tr>
            <th><?php echo Text::_('COM_JED_GENERAL_FIELD_DATE_SUBMITTED_LABEL'); ?></th>
            <td><?php echo $this->item->date_submitted; ?></td>
        </tr>
<?php /*
        <tr>
            <th><?php echo Text::_('COM_JED_VEL_ABANDONEDREPORT_FIELD_VEL_ITEM_ID_LABEL'); ?></th>
            <td><?php echo $this->item->vel_item_id; ?></td>
        </tr>

        <tr>
            <th><?php echo Text::_('COM_JED_VEL_ABANDONEDREPORTS_FIELD_DATA_SOURCE_LABEL'); ?></th>
            <td><?php echo $this->item->data_source; ?></td>
        </tr>

      

        <tr>
            <th><?php echo Text::_('COM_JED_VEL_ABANDONEDREPORT_FIELD_USER_IP_LABEL'); ?></th>
            <td><?php echo $this->item->user_ip; ?></td>
        </tr>

        <tr>
            <th><?php echo Text::_('JGLOBAL_CREATED_BY'); ?></th>
            <td><?php echo $this->item->created_by_name; ?></td>
        </tr>

        <tr>
            <th><?php echo Text::_('JGLOBAL_MODIFIED_BY'); ?></th>
            <td><?php echo $this->item->modified_by_name; ?></td>
        </tr>

        <tr>
            <th><?php echo Text::_('JGLOBAL_CREATED'); ?></th>
            <td><?php echo $this->item->created; ?></td>
        </tr>

        <tr>
            <th><?php echo Text::_('JGLOBAL_MODIFIED'); ?></th>
            <td><?php echo $this->item->modified; ?></td>
        </tr> */ ?>

    </table>

</div>


