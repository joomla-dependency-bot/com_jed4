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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;


HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
$wa = $this->document->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_jed');
$wa->useScript('keepalive')
	->useScript('form.validate');
HTMLHelper::_('bootstrap.tooltip');

// Import CSS
$document = Factory::getDocument();
$document->addStyleSheet(Uri::root() . 'media/com_jed/css/form.css');
?>
<script type="text/javascript">
    js = jQuery.noConflict();
    js(document).ready(function () {

    });

    Joomla.submitbutton = function (task) {
        if (task == 'velreport.cancel') {
            Joomla.submitform(task, document.getElementById('velreport-form'));
        } else {

            if (task != 'velreport.cancel' && document.formvalidator.isValid(document.id('velreport-form'))) {

                Joomla.submitform(task, document.getElementById('velreport-form'));
            } else {
                alert('<?php echo $this->escape(Text::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
</script>

<form
        action="<?php echo Route::_('index.php?option=com_jed&layout=edit&id=' . (int) $this->item->id); ?>"
        method="post" enctype="multipart/form-data" name="adminForm" id="velreport-form"
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
				<?php echo $this->form->renderField('pass_details_ok'); ?>
				<?php echo $this->form->renderField('vulnerability_type'); ?>
				<?php echo $this->form->renderField('vulnerable_item_name'); ?>
				<?php echo $this->form->renderField('vulnerable_item_version'); ?>
				<?php echo $this->form->renderField('exploit_type'); ?>
				<?php echo $this->form->renderField('exploit_other_description'); ?>
				<?php echo $this->form->renderField('vulnerability_description'); ?>
				<?php echo $this->form->renderField('vulnerability_how_found'); ?>
				<?php echo $this->form->renderField('vulnerability_actively_exploited'); ?>
				<?php echo $this->form->renderField('vulnerability_publicly_available'); ?>
				<?php echo $this->form->renderField('vulnerability_publicly_url'); ?>
				<?php echo $this->form->renderField('vulnerability_specific_impact'); ?>
				<?php echo $this->form->renderField('developer_communication_type'); ?>
				<?php echo $this->form->renderField('developer_patch_download_url'); ?>
				<?php echo $this->form->renderField('developer_name'); ?>
				<?php echo $this->form->renderField('developer_contact_email'); ?>
				<?php echo $this->form->renderField('tracking_db_name'); ?>
				<?php echo $this->form->renderField('tracking_db_id'); ?>
				<?php echo $this->form->renderField('jed_url'); ?>
				<?php echo $this->form->renderField('developer_additional_info'); ?>
				<?php echo $this->form->renderField('download_url'); ?>
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
				<?php if ($this->state->params->get('save_history', 1)) : ?>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('version_note'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('version_note'); ?></div>
                    </div>
				<?php endif; ?>
            </fieldset>
        </div>
    </div>
	<?php echo HTMLHelper::_('uitab.endTab'); ?>


	<?php echo HTMLHelper::_('uitab.endTabSet'); ?>

    <input type="hidden" name="task" value=""/>
	<?php echo HTMLHelper::_('form.token'); ?>

</form>
