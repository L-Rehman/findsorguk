<?php
/** Form for manipulating events details
 *
 * An example of code use:
 *
 * <code>
 * <?php
 * $form = new EventForm();
 * $form->details->setLegend('Add a new event');
 * $form->submit->setLabel('Save event');
 * $this->view->form = $form;
 * ?>
 * </code>
 *
 * @author Daniel Pett <dpett at britishmuseum.org>
 * @copyright (c) 2014 Daniel Pett
 * @category Pas
 * @package Pas_Form
 * @version 1
 * @license http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero GPL v3.0
 * @example /app/modules/admin/controllers/EventsController.php
 * @uses StaffRegions
 * @uses EventTypes
 */
class EventForm extends Pas_Form {

    /** The constructor
     * @access public
     * @param array $options
     * @return void
     */
    public function __construct(array $options = null) {

	$staffregions = new StaffRegions();
	$staffregions_options = $staffregions->getOptions();

	$eventtypes = new EventTypes();
	$event_options = $eventtypes->getTypes();

	$orgs = array(
            'PAS' => 'The Portable Antiquities Scheme',
            'BM' => 'The British Museum',
            'MLA' => 'MLA',
            'HLF' => 'Heritage Lottery Fund',
            'IFA' => 'Institute of Archaeology',
            'CBA' => 'Council for British Archaeology',
            'ARCH' => 'Current Archaeology',
            'AF' => 'The Art Fund',
            'LOC' => 'Local museum',
            'NADFAS' => 'NADFAS',
            'CASPAR' => 'CASPAR'
            );

	ZendX_JQuery::enableForm($this);

        parent::__construct($options);

        $this->setName('event');

	$eventTitle = new Zend_Form_Element_Text('eventTitle');
	$eventTitle->setLabel('Event title: ')
	->setRequired(true)
	->addErrorMessage('You must enter an event title')
	->addFilters(array('StripTags','StringTrim', 'Purifier'))
	->setAttrib('size',70);

	$eventDescription = new Pas_Form_Element_CKEditor('eventDescription');
	$eventDescription->setLabel('Event description: ')
	->setRequired(true)
	->addFilters(array('StringTrim','WordChars','BasicHtml','EmptyParagraph'))
	->setAttrib('rows',10)
	->setAttrib('cols',40)
	->setAttrib('Height',400)
	->setAttrib('ToolbarSet','Finds');

	$address = new Zend_Form_Element_Text('eventLocation');
	$address->setLabel('Address: ')
	->setRequired(true)
	->addErrorMessage('You must enter an address')
	->setAttrib('size',70)
	->addFilters(array('StripTags','StringTrim', 'Purifier'));

	$eventStartTime = new Zend_Form_Element_Text('eventStartTime');
	$eventStartTime->setLabel('Event start time: ')
	->setRequired(true)
	->addFilters(array('StripTags','StringTrim'))
	->addValidator(new Zend_Validate_Date('H:i:s'))
	->setDescription('Enter in 24 hour clock format');

	$eventEndTime = new Zend_Form_Element_Text('eventEndTime');
	$eventEndTime->setLabel('Event end time: ')
	->setRequired(true)
	->addFilters(array('StripTags','StringTrim'))
	->addValidator(new Zend_Validate_Date('H:i:s'))
	->setDescription('Enter in 24 hour clock format');

	$eventStartDate = new Zend_Form_Element_Text('eventStartDate');
	$eventStartDate->setLabel('Event start date: ')
	->setRequired(true)
	->addFilters(array('StripTags','StringTrim'))
	->addErrorMessage('You need a start date')
	->setAttrib('size', 20);

	$eventEndDate = new Zend_Form_Element_Text('eventEndDate');
	$eventEndDate->setLabel('Event end date: ')
	->setRequired(false)
	->addFilters(array('StripTags','StringTrim'))
	->addErrorMessage('You need an end date')
	->setAttrib('size', 20);

	$eventRegion = new Zend_Form_Element_Select('eventRegion');
	$eventRegion->setLabel('Organising section: ')
	->setRequired(true)
	->addFilters(array('StripTags','StringTrim'))
	->setAttrib('class', 'input-xxlarge selectpicker show-menu-arrow')
	->addValidator('stringLength', false, array(1,10))
	->addValidator('inArray', false, array(array_keys($staffregions_options)))
	->addMultiOptions(array(null => 'Please choose a region', 'Valid regions' => $staffregions_options));

	$eventType = new Zend_Form_Element_Select('eventType');
	$eventType->setLabel('Type of event: ')
	->setRequired(true)
	->setAttrib('class', 'input-xxlarge selectpicker show-menu-arrow')
	->addFilters(array('StripTags','StringTrim'))
	->addValidator('stringLength', false, array(1,10))
	->addValidator('inArray', false, array(array_keys($event_options)))
	->addMultiOptions($event_options);

	$adultsAttend = new Zend_Form_Element_Text('adultsAttend');
	$adultsAttend->setLabel('Adults attending: ')
	->setRequired(false)
	->addFilters(array('StripTags','StringTrim'))
	->addValidator('Int');

	$childrenAttend = new Zend_Form_Element_Text('childrenAttend');
	$childrenAttend->setLabel('Children attending: ')
	->setRequired(false)
	->addFilters(array('StripTags','StringTrim'))
	->addValidator('Int');

	$organisation = new Zend_Form_Element_Select('organisation');
	$organisation->setLabel('Organised by: ')
	->setRequired(false)
	->setAttrib('class', 'input-xxlarge selectpicker show-menu-arrow')
	->setValue('PAS')
	->addFilters(array('StripTags','StringTrim'))
	->addMultioptions(array(
            null => 'Choose an organisation',
            'Available institutions' => $orgs
	))
	->addValidator('InArray', false, array(array_keys($orgs)));

	$hash = new Zend_Form_Element_Hash('csrf');
	$hash->setValue($this->_salt)->setTimeout(4800);

	$submit = new Zend_Form_Element_Submit('submit');

	$this->addElements(array(
            $eventTitle,$eventDescription,$eventStartTime,
            $eventEndTime,$eventStartDate,$eventEndDate,
            $organisation,$childrenAttend,$eventRegion,
            $adultsAttend,$address,$eventType,
            $submit, $hash
	));

	$this->addDisplayGroup(array(
            'eventTitle','eventDescription','eventLocation',
            'eventStartTime','eventEndTime','eventStartDate',
            'eventEndDate','eventRegion','organisation',
            'childrenAttend','adultsAttend','eventType'), 'details');

	$this->addDisplayGroup(array('submit'), 'buttons');

	parent::init();
	}
}