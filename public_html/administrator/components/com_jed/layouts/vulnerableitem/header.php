<?php
/**
 * @package       JED
 *
 * @subpackage    VEL
 *
 * @copyright     Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to $displayData file
defined('_JEXEC') or die('Restricted access');

$headerlabeloptions = array('hiddenLabel' => true);
$fieldhiddenoptions = array('hidden' => true);
?>
<div class="span10 form-horizontal">
	<div class="row ticket-header-row">
		<div class="col-md-12 ticket-header">
			<h1>Published Title <button type="button" class="" id="buildTitle">
									<span class="icon-wand"></span>
                    </button></h1>
			<?php echo $displayData->renderField('title', null, null, $headerlabeloptions); ?>
		</div>
	</div>
    <div class="row ticket-header-row">
        <div class="col-md-3 ticket-header">
            <h1>Extension Name</h1>

			<?php echo $displayData->renderField('vulnerable_item_name', null, null, $headerlabeloptions); ?>
        </div>
        <div class="col-md-3  ticket-header">
            <h1>Version</h1>
			<?php echo $displayData->renderField('vulnerable_item_version', null, null, $headerlabeloptions); ?>
        </div> 
        <div class="col-md-3  ticket-header">
            <h1>Status</h1>
                <?php echo $displayData->renderField('status', null, null, $headerlabeloptions); ?>
            
        </div>
        <div class="col-md-3  ticket-header">
            <h1>Risk</h1>
                <?php echo $displayData->renderField('risk_level', null, null, $headerlabeloptions); ?>
            
        </div>
    </div>
        <div class="row ticket-header-row">
        <div class="col-md-3 ticket-header">
            <h1>First Version containing vulnerability</h1>

			<?php echo $displayData->renderField('start_version', null, null, $headerlabeloptions); ?>
        </div>
        <div class="col-md-3  ticket-header">
            <h1>Latest Version containing vulnerability</h1>
			<?php echo $displayData->renderField('vulnerable_version', null, null, $headerlabeloptions); ?>
        </div> 
        <div class="col-md-3  ticket-header">
            <h1>Patched Version</h1>
                <?php echo $displayData->renderField('patch_version', null, null, $headerlabeloptions); ?>
            
        </div>
        <div class="col-md-3  ticket-header">
            <h1>Exploit Type</h1>
                <?php echo $displayData->renderField('exploit_type', null, null, $headerlabeloptions); ?>
                <?php echo $displayData->renderField('exploit_other_description', null, null, $headerlabeloptions); ?>
        </div>
    </div>


