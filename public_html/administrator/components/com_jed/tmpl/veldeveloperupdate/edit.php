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
        if (task == 'veldeveloperupdate.cancel') {
            Joomla.submitform(task, document.getElementById('veldeveloperupdate-form'));
        } else {

            if (task != 'veldeveloperupdate.cancel' && document.formvalidator.isValid(document.id('veldeveloperupdate-form'))) {

                Joomla.submitform(task, document.getElementById('veldeveloperupdate-form'));
            } else {
                alert('<?php echo $this->escape(Text::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
</script>

<form
        action="<?php echo Route::_('index.php?option=com_jed&layout=edit&id=' . (int) $this->item->id); ?>"
        method="post" enctype="multipart/form-data" name="adminForm" id="veldeveloperupdate-form"
        class="form-validate form-horizontal">


	<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'update')); ?>
	<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'update', Text::_('COM_JED_VEL_TAB_UPDATE', true)); ?>
    <div class="row-fluid">
        <div class="span10 form-horizontal">
            <fieldset class="adminform">
                <legend><?php echo Text::_('COM_JED_FIELDSET_UPDATE'); ?></legend>
				<?php echo $this->form->renderField('id'); ?>
				<?php echo $this->form->renderField('contact_fullname'); ?>
				<?php echo $this->form->renderField('contact_organisation'); ?>
				<?php echo $this->form->renderField('contact_email'); ?>
				<?php echo $this->form->renderField('vulnerable_item_name'); ?>
				<?php echo $this->form->renderField('vulnerable_item_version'); ?>
				<?php echo $this->form->renderField('extension_update'); ?>
				<?php echo $this->form->renderField('new_version_number'); ?>
				<?php echo $this->form->renderField('update_notice_url'); ?>
				<?php echo $this->form->renderField('changelog_url'); ?>
				<?php echo $this->form->renderField('download_url'); ?>
				<?php echo $this->form->renderField('consent_to_process'); ?>
				<?php echo $this->form->renderField('vel_item_id'); ?>
				<?php echo $this->form->renderField('update_data_source'); ?>
				<?php echo $this->form->renderField('update_date_submitted'); ?>
				<?php echo $this->form->renderField('update_user_ip'); ?>
            </fieldset>
        </div>
    </div>
	<?php echo HTMLHelper::_('uitab.endTab'); ?>
	<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'Publishing', Text::_('COM_JED_TAB_PUBLISHING', true)); ?>
    <div class="row-fluid">
        <div class="span10 form-horizontal">
            <fieldset class="adminform">
                <legend><?php echo Text::_('COM_JED_FIELDSET_PUBLISHING'); ?></legend>
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
