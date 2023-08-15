<?php
/** Form for manipulating vacancies on the system
 * 
 * An example of code:
 * 
 * <code>
 * <?php
 * $form = new VacancyForm();
 * ?>
 * </code>
 * @author Daniel Pett <dpett at britishmuseum.org>
 * @category   Pas
 * @package    Pas_Form
 * @copyright  Copyright (c) 2011 DEJ Pett dpett @ britishmuseum . org
 * @license http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero GPL v3.0
 * @version 1
 * @uses StaffRegions
 * @example /app/modules/admin/controllers/VacanciesController.php
*/
class VacancyForm extends Pas_Form {

    /** The constructor
     * @access public
     * @param array $options
     * @return void
     */
    public function __construct(array $options = null) {
	
	$staffregions = new StaffRegions();
	$staffregions_options = $staffregions->getOptions();

	ZendX_JQuery::enableForm($this);
	
	parent::__construct($options);

	$this->setName('vacancies');

	$title = new Zend_Form_Element_Text('title');
	$title->setLabel('Role title: ')
		->setRequired(true)
		->addFilters(array('StringTrim', 'StripTags'))
		->addErrorMessage('You must enter a title for this vacancy.')
		->setAttrib('size', 60);
	
	$salary = new Zend_Form_Element_Text('salary');
	$salary->setLabel('Salary: ')
		->setRequired(true)
		->addFilters(array('StringTrim', 'StripTags'))
		->setAttrib('size', 20)
        ->addValidator('Float')
        ->addValidator('Between', true, array('min' => 0, 'max' => 999999));

        $salary->getValidator('Between')->setMessage(
            'Salary must be less than £999,999'
        );
	
	$specification = new Pas_Form_Element_CKEditor('specification');
	$specification->setLabel('Job specification: ')
		->setRequired(true)
		->addFilters(array('BasicHtml', 'StringTrim'))
		->setAttribs(array('cols' => 50, 'rows' => 10, 'Height' => 400))
		->addErrorMessage('You must enter a job description.');
	
	$regionID = new Zend_Form_Element_Select('regionID');
	$regionID->setLabel('Location of role: ')
		->setAttrib('class', 'input-xxlarge selectpicker show-menu-arrow')
		->setRequired(true)
		->addFilters(array('StringTrim', 'StripTags'))
		->addValidator('InArray', false, array(array_keys($staffregions_options)))
		->addMultiOptions(array(
                    null => 'Choose region', 
                    'Available regions' => $staffregions_options
                ))
		->addErrorMessage('You must choose a region');
	
	$live = new Zend_Form_Element_Text('live');
	$live->setLabel('Date for advert to go live: ')
		->setRequired(true)
		->addFilters(array('StringTrim', 'StripTags'))
		->addErrorMessage('Please enter date for advert to go live')
		->setAttrib('size', 20);
	
	$expire = new Zend_Form_Element_Text('expire');
	$expire->setLabel('Date for advert to expire: ')
		->setRequired(true)
		->addFilters(array('StringTrim', 'StripTags'))
		->addErrorMessage('Please enter date for advert to expire')
		->setAttrib('size', 20);
	
	$status = new Zend_Form_Element_Select('status');
	$status->SetLabel('Publish status: ')
		->setRequired(true)
		->setAttrib('class', 'input-xxlarge selectpicker show-menu-arrow')
		->addMultiOptions(array(
                    null => 'Choose a status',
                    2 => 'Publish',
                    1 => 'Draft'
                    ))
		->setValue(2)
		->addFilters(array('StringTrim', 'StripTags'))
		->addErrorMessage('You must choose a status');
	
	$submit = new Zend_Form_Element_Submit('submit');

	$hash = new Zend_Form_Element_Hash('csrf');
	$hash->setValue($this->_salt)->setTimeout(4800);	
	
	$this->addElements(array(
            $title, $salary, $specification,
            $regionID, $live, $expire,
            $status, $submit, $hash
                ));
	
	$this->addDisplayGroup(array(
            'title', 'salary', 'specification',
            'regionID'), 'details');
	
	$this->details->setLegend('Vacancy details');
	
	$this->addDisplayGroup(array(
            'live', 'expire', 'status'),
            'dates');
	
	$this->dates->setLegend('Publication details');
	
	$this->setLegend('Vacancy details');
	
	$this->addDisplayGroup(array('submit'), 'buttons');
	
	parent::init();
    }
}