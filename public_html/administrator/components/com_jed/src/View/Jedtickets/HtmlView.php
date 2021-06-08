<?php
/**
 * @package       JED
 *
 * @subpackage    Tickets
 *
 * @copyright     Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license       GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Jed\Component\Jed\Administrator\View\Jedtickets;
// No direct access
defined('_JEXEC') or die;

use Exception;
use Jed\Component\Jed\Administrator\Helper\JedHelper;
use Joomla\CMS\Form\Form;
use Joomla\CMS\HTML\Helpers\Sidebar;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Component\Content\Administrator\Extension\ContentComponent;

/**
 * View class for a list of JED Tickets.
 *
 * @since  4.0.0
 */
class HtmlView extends BaseHtmlView
{
    protected array $items = [];

    protected Pagination $pagination;

    protected CMSObject $state;

	public ?Form $filterForm;

	public array $activeFilters = [];

	public string $sidebar;

    /**
     * Display the view
     *
     * @param string $tpl Template name
     *
     * @return void
     *
     * @throws Exception
     *
     * @since 4.0.0
     */
    public function display($tpl = null)
    {
        $this->state = $this->get('State');
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        $this->addToolbar();

        $this->sidebar = Sidebar::render();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return void
     *
     * @since  4.0.0
     */
    protected function addToolbar()
    {
        $this->state = $this->get('State');
        $canDo = JedHelper::getActions();

        ToolbarHelper::title(Text::_('COM_JED_TITLE_JEDTICKETS'), "generic");

        $toolbar = Toolbar::getInstance();

        // Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR . '/src/View/Jedtickets';

        if (file_exists($formPath)) {
            if ($canDo->get('core.create')) {
                $toolbar->addNew('jedticket.add');
            }
        }

        if ($canDo->get('core.edit.state') ) {
            $dropdown = $toolbar->dropdownButton('status-group')
                ->text('JTOOLBAR_CHANGE_STATUS')
                ->toggleSplit(false)
                ->icon('fas fa-ellipsis-h')
                ->buttonClass('btn btn-action')
                ->listCheck(true);

            $childBar = $dropdown->getChildToolbar();

            if (isset($this->items[0]->state)) {
                $childBar->publish('jedtickets.publish')->listCheck(true);
                $childBar->unpublish('jedtickets.unpublish')->listCheck(true);
                $childBar->archive('jedtickets.archive')->listCheck(true);
            } elseif (isset($this->items[0])) {
                // If this component does not use state then show a direct delete button as we can not trash
                $toolbar->delete('jedtickets.delete')
                    ->text('JTOOLBAR_EMPTY_TRASH')
                    ->message('JGLOBAL_CONFIRM_DELETE')
                    ->listCheck(true);
            }

            $childBar->standardButton('duplicate')
                ->text('JTOOLBAR_DUPLICATE')
                ->icon('fas fa-copy')
                ->task('jedtickets.duplicate')
                ->listCheck(true);

            if (isset($this->items[0]->checked_out)) {
                $childBar->checkin('jedtickets.checkin')->listCheck(true);
            }

            if (isset($this->items[0]->state)) {
                $childBar->trash('jedtickets.trash')->listCheck(true);
            }
        }


        // Show trash and delete for components that uses the state field
        if (isset($this->items[0]->state)) {

            if ($this->state->get('filter.state') == ContentComponent::CONDITION_TRASHED && $canDo->get('core.delete')) {
                $toolbar->delete('jedtickets.delete')
                    ->text('JTOOLBAR_EMPTY_TRASH')
                    ->message('JGLOBAL_CONFIRM_DELETE')
                    ->listCheck(true);
            }
        }
JedHelper::addConfigToolbar($toolbar);
        if ($canDo->get('core.admin')) {
            $toolbar->preferences('com_jed');
        }

        // Set sidebar action
        Sidebar::setAction('index.php?option=com_jed&view=jedtickets');
    }

    /**
     * Method to order fields
     *
     * @return array
     *
     * @since 4.0.0
     */
    protected function getSortFields() : array
    {
        return array(
            'a.`id`' => Text::_('JGRID_HEADING_ID'),
            'a.`ticket_origin`' => Text::_('COM_JED_JEDTICKETS_FIELD_TICKET_ORIGIN_LABEL'),
            'a.`ticket_category_type`' => Text::_('COM_JED_JEDTICKETS_FIELD_TICKET_CATEGORY_TYPE_LABEL'),
            'a.`ticket_subject`' => Text::_('COM_JED_JEDTICKETS_FIELD_TICKET_SUBJECT_LABEL'),
            'a.`allocated_group`' => Text::_('COM_JED_JEDTICKETS_FIELD_ALLOCATED_GROUP_LABEL'),
            'a.`allocated_to`' => Text::_('COM_JED_JEDTICKETS_FIELD_ALLOCATED_TO_LABEL'),
            'a.`linked_item_type`' => Text::_('COM_JED_JEDTICKETS_FIELD_LINKED_ITEM_TYPE_LABEL'),
            'a.`linked_item_id`' => Text::_('COM_JED_JEDTICKETS_FIELD_LINKED_ITEM_ID_LABEL'),
            'a.`ticket_status`' => Text::_('COM_JED_JEDTICKETS_FIELD_TICKET_STATUS_LABEL'),
            'a.`parent_id`' => Text::_('COM_JED_JEDTICKETS_FIELD_PARENT_ID_LABEL'),
            'a.`state`' => Text::_('JSTATUS'),
            'a.`ordering`' => Text::_('JGRID_HEADING_ORDERING'),
            'a.`created_by`' => Text::_('JGLOBAL_FIELD_CREATED_BY_LABEL'),
            'a.`created_on`' => Text::_('COM_JED_JEDTICKETS_FIELD_CREATED_ON_LABEL'),
            'a.`modified_by`' => Text::_('JGLOBAL_FIELD_MODIFIED_BY_LABEL'),
            'a.`modified_on`' => Text::_('COM_JED_JEDTICKETS_FIELD_MODIFIED_ON_LABEL'),
        );
    }

    /**
     * Check if state is set
     *
     * @param mixed $state State
     *
     * @return bool
     *
     * @since 4.0.0
     */
    public function getState($state) : bool
    {
        return isset($this->state->{$state}) ? $this->state->{$state} : false;
    }
}
