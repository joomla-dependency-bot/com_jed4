<?php
/**
 * @package       JED
 *
 * @subpackage    Tickets
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
        method="post" enctype="multipart/form-data" name="adminForm" id="ticketcategory-form"
        class="form-validate form-horizontal">


	<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', array('active' => 'ticketcategory')); ?>
	<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'ticketcategory', Text::_('COM_JED_TAB_TICKETCATEGORY', true)); ?>
    <div class="row-fluid">
        <div class="span10 form-horizontal">
            <fieldset class="adminform">
                <legend><?php echo Text::_('COM_JED_FIELDSET_TICKETCATEGORY'); ?></legend>
				<?php echo $this->form->renderField('categorytype'); ?>
            </fieldset>
        </div>
    </div>
	<?php echo HTMLHelper::_('uitab.endTab'); ?>
    <input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>"/>
    <input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>"/>
    <input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>"/>
    <input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>"/>
    <input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>"/>
	<?php echo $this->form->renderField('created_by'); ?>
	<?php echo $this->form->renderField('modified_by'); ?>


	<?php echo HTMLHelper::_('uitab.endTabSet'); ?>

    <input type="hidden" name="task" value=""/>
	<?php echo HTMLHelper::_('form.token'); ?>

</form>
