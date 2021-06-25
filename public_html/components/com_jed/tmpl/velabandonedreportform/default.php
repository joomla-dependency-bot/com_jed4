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

use Jed\Component\Jed\Site\Helper\JedHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$wa = $this->document->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_jed');
$wa->useScript('keepalive')
	->useScript('form.validate');
HTMLHelper::_('bootstrap.tooltip');

// Load admin language file
$lang = Factory::getLanguage();
$lang->load('com_jed', JPATH_SITE);
$doc = Factory::getDocument();
$doc->addScript(Uri::base() . '/media/com_jed/js/form.js');


$canEdit = JedHelper::canUserEdit($this->item);


?>

<div class="velabandonedreport-edit front-end-edit">
	<?php if (!$canEdit) : ?>
        <h3>
			<?php throw new Exception(Text::_('COM_JED_ERROR_MESSAGE_NOT_AUTHORISED'), 403); ?>
        </h3>
	<?php else : ?>

        <form id="form-velabandonedreport"
              action="<?php echo Route::_('index.php?option=com_jed&task=velabandonedreportform.save'); ?>"
              method="post" class="form-validate form-horizontal" enctype="multipart/form-data">


			<?php

			$fieldsets['overview']['title']       = JTEXT::_('COM_JED_VEL_ABANDONEDREPORT_OVERVIEW_TITLE');
			$fieldsets['overview']['description'] = JTEXT::_('COM_JED_VEL_ABANDONEDREPORT_OVERVIEW_DESCRIPTION');
			$fieldsets['overview']['fields']      = array();


			$fieldsets['aboutyou']['title']       = JTEXT::_('COM_JED_VEL_GENERAL_FIELD_ABOUT_YOU_LABEL');
			$fieldsets['aboutyou']['description'] = "";
			$fieldsets['aboutyou']['fields']      = array(
				'reporter_fullname',
				'reporter_email',
				'reporter_organisation');

			$fieldsets['extensiondetails']['title']       = JTEXT::_('COM_JED_VEL_ABANDONEDREPORT_EXTENSION_TITLE');
			$fieldsets['extensiondetails']['description'] = "";
			$fieldsets['extensiondetails']['fields']      = array(
				'extension_name',
				'developer_name',
				'extension_version',
				'extension_url',
				'abandoned_reason',
				'consent_to_process');
			$fscount                                      = 0;


			foreach ($fieldsets as $fs)
			{

				$fscount = $fscount + 1;
				if ($fs['title'] <> '')
				{
					if ($fscount > 1)
					{
						echo '</fieldset>';
					}

					echo '<fieldset class="velabandonwareform"><legend>' . $fs['title'] . '</legend>';


				}
				if ($fs['description'] <> '')
				{
					echo $fs['description'];
				}
				$fields       = $fs['fields'];
				$hiddenFields = array('user_ip');
				foreach ($fields as $field)
				{

					if (in_array($field, $hiddenFields))
					{
						$this->form->setFieldAttribute($field, 'type', 'hidden');
					}

					echo $this->form->renderField($field, null, null, array('class' => 'control-wrapper-' . $field));
				}


			}


			?>

            <div class="control-group">
                <div class="controls">

					<?php if ($this->canSave): ?>
                        <button type="submit" class="validate btn btn-primary">
                            <span class="fas fa-check" aria-hidden="true"></span>
							<?php echo Text::_('JSUBMIT'); ?>
                        </button>
					<?php endif; ?>
                    <a class="btn btn-danger"
                       href="<?php echo Route::_('index.php?option=com_jed&task=velabandonedreportform.cancel'); ?>"
                       title="<?php echo Text::_('JCANCEL'); ?>">
                        <span class="fas fa-times" aria-hidden="true"></span>
						<?php echo Text::_('JCANCEL'); ?>
                    </a>
                </div>
            </div>

            <input type="hidden" name="option" value="com_jed"/>
            <input type="hidden" name="task"
                   value="velabandonedreportform.save"/>
			<?php echo HTMLHelper::_('form.token'); ?>
        </form>
	<?php endif; ?>
</div>
