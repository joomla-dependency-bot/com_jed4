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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;


HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
	->useScript('form.validate');
HTMLHelper::_('bootstrap.tooltip');
?>

<form
        action="<?php echo Route::_('index.php?option=com_jed&layout=edit&id=' . (int) $this->item->id); ?>"
        method="post" enctype="multipart/form-data" name="adminForm" id="velabandonedreport-form"
        class="form-validate form-horizontal">


	<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'report')); ?>
	<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'report', Text::_('COM_JED_VAL_TAB_REPORT', true)); ?>
    <div class="row-fluid">
        <div class="span10 form-horizontal">
            <fieldset class="adminform">
                <legend><?php echo Text::_('COM_JED_FIELDSET_REPORT'); ?></legend>
				<?php echo $this->form->renderField('id'); ?>
				<?php echo $this->form->renderField('reporter_fullname'); ?>
				<?php echo $this->form->renderField('reporter_email'); ?>
				<?php echo $this->form->renderField('reporter_organisation'); ?>
				<?php echo $this->form->renderField('extension_name'); ?>
				<?php echo $this->form->renderField('developer_name'); ?>
				<?php echo $this->form->renderField('extension_version'); ?>
				<?php echo $this->form->renderField('extension_url'); ?>
				<?php echo $this->form->renderField('abandoned_reason'); ?>
				<?php echo $this->form->renderField('consent_to_process'); ?>
				<?php echo $this->form->renderField('passed_to_vel'); ?>
				<?php echo $this->form->renderField('vel_item_id'); ?>
            </fieldset>
        </div>
    </div>
	<?php echo HTMLHelper::_('uitab.endTab'); ?>
	<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'Publishing', Text::_('COM_JED_TAB_PUBLISHING', true)); ?>
    <div class="row-fluid">
        <div class="span10 form-horizontal">
            <fieldset class="adminform">
                <legend><?php echo Text::_('COM_JED_FIELDSET_PUBLISHING'); ?></legend>
				<?php echo $this->form->renderField('data_source'); ?>
				<?php echo $this->form->renderField('date_submitted'); ?>
				<?php echo $this->form->renderField('user_ip'); ?>
				<?php echo $this->form->renderField('created_by'); ?>
				<?php echo $this->form->renderField('modified_by'); ?>
				<?php echo $this->form->renderField('created'); ?>
				<?php echo $this->form->renderField('modified'); ?>
            </fieldset>
        </div>
    </div>
	<?php echo HTMLHelper::_('uitab.endTab'); ?>


	<?php echo HTMLHelper::_('uitab.endTabSet'); ?>

    <input type="hidden" name="task" value=""/>
	<?php echo HTMLHelper::_('form.token'); ?>

</form>
