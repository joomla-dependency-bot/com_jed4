<?php
/**
 * @package       JED
 *
 * @subpackage    Tickets
 *
 * @copyright     Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;

$headerlabeloptions = array('hiddenLabel' => true);
$fieldhiddenoptions = array('hidden' => true);
//var_dump($displayData);exit();
$rawData = $displayData->getData();

/* Set up Data fieldsets */

$fieldsets['vulnerabilitydetails']['title'] = "Vulnerability Details";

$fieldsets['vulnerabilitydetails']['fields'] = array(
	'vulnerability_type',
	'vulnerable_item_name',
	'vulnerable_item_version',
	'exploit_type',
	'exploit_other_description',
	'vulnerability_description',
	'vulnerability_how_found',
	'vulnerability_actively_exploited',
	'vulnerability_publicly_available',
	'vulnerability_publicly_url',
	'vulnerability_specific_impact');

$fieldsets['developerdetails']['title']       = "Developer Details";
$fieldsets['developerdetails']['description'] = "";
$fieldsets['developerdetails']['fields']      = array(
	'developer_communication_type',
	'developer_patch_download_url',
	'developer_name',
	'developer_contact_email',
	'jed_url',
	'tracking_db_name',
	'tracking_db_id',
	'developer_additional_info');

$fieldsets['filelocation']['title']       = "Location of File";
$fieldsets['filelocation']['description'] = "";
$fieldsets['filelocation']['fields']      = array(
	'download_url');

$fieldsets['aboutyou']['title']       = "Reporter";
$fieldsets['aboutyou']['description'] = "";
$fieldsets['aboutyou']['fields']      = array(
	'reporter_fullname',
	'reporter_email',
	'reporter_organisation',
	'pass_details_ok');


$fieldsets['extra']['title']       = "Extra";
$fieldsets['extra']['description'] = "";
$fieldsets['extra']['fields']      = array(
	'consent_to_process',
	'passed_to_vel',
	'vel_item_id',
	'date_submitted',
	'user_ip',
	'data_source');

?>
<div class="row">
    <div class="col">
        <div class="widget">
            <h1><?php echo $fieldsets['vulnerabilitydetails']['title']; ?></h1>
            <div class="container">
                <div class="row">
					<?php foreach ($fieldsets['vulnerabilitydetails']['fields'] as $field)
					{

						$displayData->setFieldAttribute($field, 'readonly', 'true');
						echo $displayData->renderField($field, null, null, array('class' => 'control-wrapper-' . $field));


					} ?>
                </div>
            </div>
        </div>
        <div class="widget">
            <h1><?php echo $fieldsets['filelocation']['title']; ?></h1>
            <div class="container">
                <div class="row">
					<?php foreach ($fieldsets['filelocation']['fields'] as $field)
					{

						$displayData->setFieldAttribute($field, 'readonly', 'true');
						echo $displayData->renderField($field, null, null, array('class' => 'control-wrapper-' . $field));


					} ?>
                </div>
            </div>
        </div>

        <div class="widget">
            <h1><?php echo $fieldsets['extra']['title']; ?></h1>
            <div class="container">
                <div class="row">
					<?php foreach ($fieldsets['extra']['fields'] as $field)
					{

						$displayData->setFieldAttribute($field, 'readonly', 'true');
						echo $displayData->renderField($field, null, null, array('class' => 'control-wrapper-' . $field));


					} ?>
                </div>
            </div>
        </div>

    </div>
    <div class="col">
        <div class="widget">
            <h1><?php echo $fieldsets['developerdetails']['title']; ?></h1>
            <div class="container">
                <div class="row">
					<?php foreach ($fieldsets['developerdetails']['fields'] as $field)
					{

						$displayData->setFieldAttribute($field, 'readonly', 'true');
						echo $displayData->renderField($field, null, null, array('class' => 'control-wrapper-' . $field));


					} ?>
                </div>
            </div>
        </div>
        <div class="widget">
            <h1><?php echo $fieldsets['aboutyou']['title']; ?></h1>
            <div class="container">
                <div class="row">
					<?php foreach ($fieldsets['aboutyou']['fields'] as $field)
					{

						$displayData->setFieldAttribute($field, 'readonly', 'true');
						echo $displayData->renderField($field, null, null, array('class' => 'control-wrapper-' . $field));


					} ?>
                </div>
            </div>
        </div>
        <div class="widget">
            <h1>Actions</h1>
			<?php  //var_dump($rawData->get('vel_item_id'));exit();
			?>
            <div class="container">
                <div class="row">
               	<?php
				if($rawData->get('vel_item_id') > 0) 
				{
					echo Text::_('No Actions available');
				}
				else
				{
?>
                     <button type="button" class="btn btn-primary"
                                        onclick="Joomla.submitbutton('jedticket.copyReporttoVEL')">
									<?php echo Text::_('Create VEL Item from Report'); ?>
                    </button>
                
					<?php
				} ?>
                </div>
            </div>
        </div>
    </div>

</div>
