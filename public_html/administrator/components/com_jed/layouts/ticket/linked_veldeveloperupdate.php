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

$headerlabeloptions = array('hiddenLabel' => true);
$fieldhiddenoptions = array('hidden' => true);
//var_dump($displayData);exit();
$rawData = $displayData->getData();

/* Set up Data fieldsets */

$fieldsets['aboutyou']['title']  = JTEXT::_('COM_JED_VEL_GENERAL_FIELD_ABOUT_YOU_LABEL');
$fieldsets['aboutyou']['fields'] = array(
	'contact_fullname',
	'contact_organisation',
	'contact_email');

$fieldsets['vulnerabilitydetails']['title']  = JTEXT::_('COM_JED_VEL_DEVELOPERUPDATES_FORM_VULNERABILITY_DETAILS_TITLE');
$fieldsets['vulnerabilitydetails']['fields'] = array(
	'vulnerable_item_name',
	'vulnerable_item_version',
	'extension_update',
	'new_version_number',
	'update_notice_url',
	'changelog_url',
	'download_url',
	'consent_to_process',
	'update_date_submitted');


$fieldsets['final']['title']       = "VEL Details";
$fieldsets['final']['description'] = "";

$fieldsets['final']['fields'] = array(
	'vel_item_id');
$fscount                      = 0;

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
    </div>

    <div class="col">
        <div class="widget">
            <h1><?php echo $fieldsets['aboutyou']['title']; ?></h1>
            <div class="container">
                <div class="row">
					<?php foreach ($fieldsets['aboutyou']['fields'] as $field)
					{

						$displayData->setFieldAttribute($field, 'readonly', 'true');
						echo $displayData->renderField($field, null, null, array('class' => 'control-wrapper-' . $field));


					} ?>
                    <input type="hidden" id="veldeveloperupdate_id" name="jform[veldeveloperupdate_id]" value="<?php echo $rawData->get('id'); ?>">
                </div>
            </div>
        </div>

        <div class="widget">
            <h1><?php echo $fieldsets['final']['title']; ?></h1>
            <div class="container">
                <div class="row">
					<?php foreach ($fieldsets['final']['fields'] as $field)
					{

						//$displayData->setFieldAttribute($field, 'readonly', 'true');
						echo $displayData->renderField($field, null, null, array('class' => 'control-wrapper-' . $field));


					} ?>
					<div id="veldeveloperbutton"></div>
                </div>
            </div>
        </div>
    </div>
</div>
