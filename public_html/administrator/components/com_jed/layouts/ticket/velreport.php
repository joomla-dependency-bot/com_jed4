<?php
/**
 * @package       JED
 *
 * @subpackage    Tickets
 *
 * @copyright     Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
?>
 <div class="row">
                    <div class="col">
                        <div class="widget">
                            <h1><?php echo $fieldsets['vulnerabilitydetails']['title']; ?></h1>
                            <div class="container">
                                <div class="row">
									<?php foreach ($fieldsets['vulnerabilitydetails']['fields'] as $field)
									{

										$this->linked_form->setFieldAttribute($field, 'readonly', 'true');
										echo $this->linked_form->renderField($field, null, null, array('class' => 'control-wrapper-' . $field));


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

										$this->linked_form->setFieldAttribute($field, 'readonly', 'true');
										echo $this->linked_form->renderField($field, null, null, array('class' => 'control-wrapper-' . $field));


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

										$this->linked_form->setFieldAttribute($field, 'readonly', 'true');
										echo $this->linked_form->renderField($field, null, null, array('class' => 'control-wrapper-' . $field));


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

										$this->linked_form->setFieldAttribute($field, 'readonly', 'true');
										echo $this->linked_form->renderField($field, null, null, array('class' => 'control-wrapper-' . $field));


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

										$this->linked_form->setFieldAttribute($field, 'readonly', 'true');
										echo $this->linked_form->renderField($field, null, null, array('class' => 'control-wrapper-' . $field));


									} ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
				
    