<?php
/**
 * @package       JED
 *
 * @subpackage    Tickets
 *
 * @copyright     Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');

$headerlabeloptions = array('hiddenLabel' => true);
$fieldhiddenoptions = array('hidden' => true);
?>
<div class="span10 form-horizontal">

    <div class="row ticket-header-row">
        <div class="col-md-3 ticket-header">
            <h1>Subject</h1>

			<?php echo $displayData->renderField('ticket_subject', null, null, $headerlabeloptions); ?>
        </div>
        <div class="col-md-3  ticket-header">
            <h1>Category</h1>
			<?php echo $displayData->renderField('ticket_category_type', null, null, $headerlabeloptions); ?>
        </div>
        <div class="col-md-3  ticket-header">
            <div class="row mf">
                <div class="col"><h1>Ticket Status</h1></div>
                <div class="col"><h1>Origin</h1></div>
            </div>
            <div class="row">
                <div class="col"><?php echo $displayData->renderField('ticket_status', null, null, $headerlabeloptions); ?></div>
                <div class="col"><?php echo $displayData->renderField('ticket_origin', null, null, $headerlabeloptions); ?></div>
            </div>
        </div>
        <div class="col-md-3  ticket-header">
            <h1>Assigned</h1>
            <div class="row">
                <div class="col"><?php echo $displayData->renderField('allocated_group', null, null, $headerlabeloptions); ?></div>
                <div class="col"><?php echo $displayData->renderField('allocated_to', null, null, $headerlabeloptions); ?></div>
            </div>

        </div>
    </div>

</div>
