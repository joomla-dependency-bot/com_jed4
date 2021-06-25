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

$user    = Factory::getUser();
$canEdit = JedHelper::canUserEdit($this->item);

$isLoggedIn  = JedHelper::IsLoggedIn();
$redirectURL = JedHelper::getLoginlink();

if (!$isLoggedIn)
{
	$app = JFactory::getApplication();

	$app->enqueueMessage(Text::_('COM_JED_VEL_REPORTS_NO_ACCESS'), 'success');
	$app->redirect($redirectURL);

}
else
{


	?>

    <div class="velreport-edit front-end-edit">
		<?php if (!$canEdit) : ?>
            <h3>
				<?php throw new Exception(Text::_('COM_JED_ERROR_MESSAGE_NOT_AUTHORISED'), 403); ?>
            </h3>
		<?php else : ?>


            <form id="form-velreport"
                  action="<?php echo Route::_('index.php?option=com_jed&task=velreportform.save'); ?>"
                  method="post" class="form-validate form-horizontal" enctype="multipart/form-data">


				<?php

				$fieldsets['overview']['title']       = JTEXT::_('COM_JED_VEL_REPORTS_OVERVIEW_TITLE');
				$fieldsets['overview']['description'] = JTEXT::_('COM_JED_VEL_REPORTS_FORM_OVERVIEW_DESCR');
				$fieldsets['overview']['fields']      = array();


				$fieldsets['aboutyou']['title']       = JTEXT::_('COM_JED_VEL_GENERAL_FIELD_ABOUT_YOU_LABEL');
				$fieldsets['aboutyou']['description'] = "";
				$fieldsets['aboutyou']['fields']      = array(
					'reporter_fullname',
					'reporter_email',
					'reporter_organisation',
					'pass_details_ok');

				$fieldsets['vulnerabilitydetails']['title']       = JTEXT::_('COM_JED_VEL_REPORTS_VULNERABILITY_DETAILS_TITLE');
				$fieldsets['vulnerabilitydetails']['description'] = "";
				$fieldsets['vulnerabilitydetails']['fields']      = array(
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

				$fieldsets['developerdetails']['title']       = JTEXT::_('COM_JED_VEL_REPORTS_DEVELOPER_DETAILS_TITLE');
				$fieldsets['developerdetails']['description'] = JTEXT::_('COM_JED_VEL_REPORTS_DEVELOPER_DETAILS_DESCRIPTION');
				$fieldsets['developerdetails']['fields']      = array(
					'developer_communication_type',
					'developer_patch_download_url');

				$fieldsets['moredeveloperdetails']['title']       = "";
				$fieldsets['moredeveloperdetails']['description'] = JTEXT::_('COM_JED_VEL_REPORTS_FORM_DEVELOPER_DETAILS_MORE_DESCR');
				$fieldsets['moredeveloperdetails']['fields']      = array(
					'developer_name',
					'developer_contact_email',
					'jed_url',
					'tracking_db_name',
					'tracking_db_id');


				$fieldsets['additionaldeveloperdetails']['title']       = JTEXT::_('COM_JED_VEL_REPORTS_DEVELOPER_DETAILS_ADDITIONAL_TITLE_LABEL');
				$fieldsets['additionaldeveloperdetails']['description'] = "";
				$fieldsets['additionaldeveloperdetails']['fields']      = array(
					'developer_additional_info');

				$fieldsets['filelocation']['title']       = JTEXT::_('COM_JED_VEL_REPORTS_FILELOCATION_TITLE');
				$fieldsets['filelocation']['description'] = "";
				$fieldsets['filelocation']['fields']      = array(
					'download_url',
					'consent_to_process');

				$fieldsets['final']['title']       = "";
				$fieldsets['final']['description'] = JTEXT::_('COM_JED_VEL_REPORTS_FORM_FINAL_DESCR');

				$fieldsets['final']['fields'] = array('captcha', 'passed_to_vel',
					'date_submitted',
					'data_source');
				$fscount                      = 0;


				foreach ($fieldsets as $fs)
				{

					$fscount = $fscount + 1;
					if ($fs['title'] <> '')
					{
						if ($fscount > 1)
						{
							echo '</fieldset>';
						}

						echo '<fieldset class="velreportform"><legend>' . $fs['title'] . '</legend>';


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
                           href="<?php echo Route::_('index.php?option=com_jed&task=velreportform.cancel'); ?>"
                           title="<?php echo Text::_('JCANCEL'); ?>">
                            <span class="fas fa-times" aria-hidden="true"></span>
							<?php echo Text::_('JCANCEL'); ?>
                        </a>
                    </div>
                </div>

                <input type="hidden" name="option" value="com_jed"/>
                <input type="hidden" name="task"
                       value="velreportform.save"/>
				<?php echo HTMLHelper::_('form.token'); ?>
            </form>
		<?php endif; ?>
    </div>
	<?php
}
?>