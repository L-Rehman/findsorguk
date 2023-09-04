<?php

/** Form for entering and editing research projects
 *
 * An example of code use:
 *
 * <code>
 * <?php
 * $form = new ResearchForm();
 * ?>
 * </code>
 * @author Daniel Pett <dpett at britishmuseum.org>
 * @category   Pas
 * @package    Pas_Form
 * @copyright  Copyright (c) 2011 DEJ Pett dpett @ britishmuseum . org
 * @license http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero GPL v3.0
 * @uses ProjectTypes
 * @version 1
 * @example /app/modules/admin/controllers/ResearchController.php
 */
class ResearchForm extends Pas_Form
{

    /** The constructor
     * @access public
     * @param array $options
     * @return void
     */
    public function __construct(array $options = null)
    {

        $projecttypes = new ProjectTypes();
        $projectype_list = $projecttypes->getTypes();

        ZendX_JQuery::enableForm($this);

        parent::__construct($options);

        $this->setName('research');

        $investigator = new Zend_Form_Element_Text('investigator');
        $investigator->setLabel('Principal work conducted by: ')
            ->setRequired(true)
            ->setAttrib('size', 60)
            ->addFilters(array('StripTags', 'StringTrim'))
            ->addErrorMessage('You must enter a lead for this project.');

        $level = new Zend_Form_Element_Select('level');
        $level->setLabel('Level of research: ')
            ->setRequired(true)
            ->addFilters(array('StripTags', 'StringTrim'))
            ->setAttrib('class', 'input-xxlarge selectpicker show-menu-arrow')
            ->addMultiOptions(array(null => null, 'Choose type of research' => $projectype_list))
            ->addValidator('inArray', false, array(array_keys($projectype_list)));

        $title = new Zend_Form_Element_Text('title');
        $title->setLabel('Project title: ')
            ->setRequired(true)
            ->addFilters(array('StripTags', 'StringTrim', 'Purifier'))
            ->setAttrib('size', 60)
            ->addErrorMessage('Choose title for the project.');

        $description = new Pas_Form_Element_CKEditor('description');
        $description->setLabel('Short description of project: ')
            ->setRequired(false)
            ->setAttribs(array('cols' => 80, 'rows' => 10))
            ->addFilters(array('BasicHtml', 'StringTrim', 'EmptyParagraph'));

        $startDate = new Zend_Form_Element_Text('startDate');
        $startDate->setLabel('Start date of project')
            ->setAttrib('size', 20)
            ->setRequired(false)
            ->addErrorMessage('You must enter a start date for this project');

        $endDate = new Zend_Form_Element_Text('endDate');
        $endDate->setLabel('End date of project')
            ->setAttrib('size', 20)
            ->setRequired(false)
            ->addErrorMessage('You must enter an end date for this project');

        $valid = new Zend_Form_Element_Checkbox('valid');
        $valid->setLabel('Make public: ')
            ->setRequired(true)
            ->addFilters(array('StripTags', 'StringTrim'))
            ->addValidator('Digits');

        $submit = new Zend_Form_Element_Submit('submit');

        $this->addElements(array(
            $title, $description, $level,
            $startDate, $endDate, $valid,
            $investigator, $submit
        ));

        $this->addDisplayGroup(array(
            'title', 'investigator', 'level',
            'description', 'startDate', 'endDate',
            'valid',), 'details');

        $this->addDisplayGroup(array('submit'), 'buttons');

        parent::init();
    }
}
