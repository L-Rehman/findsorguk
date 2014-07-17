<?php
/** Form for linking cases to a specific tvc date
* 
* @category   Pas
* @package    Pas_Form
* @copyright  Copyright (c) 2011 DEJ Pett dpett @ britishmuseum . org
* @license http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero GPL v3.0
*/
class TVCDateForm extends Pas_Form {

public function __construct(array $options) {
	$dates = new TvcDates();
	$list = $dates->dropdown();
	
	parent::__construct($options);
	
	$this->setName('tvcdates');
	
	$date = new Zend_Form_Element_Select('tvcID');
	$date->setLabel('Date of TVC: ')
		->setRequired(true)
		->addFilters(array('StripTags','StringTrim'))
		->setAttrib('class', 'input-xxlarge selectpicker show-menu-arrow')
		->addErrorMessage('You must choose a TVC date')
		->addMultiOptions(array('NULL' => 'Select a TVC','Valid dates' => $list))
		->addValidator('InArray', false, array(array_keys($list)));
	
	$submit = new Zend_Form_Element_Submit('submit');
	
	$hash = new Zend_Form_Element_Hash('csrf');
	$hash->setValue($this->_salt)->setTimeout(4800);
		
	$this->addElements(array(
	$date, $submit, $hash
	));
	
	$this->addDisplayGroup(array('tvcID'), 'details');
	
	$this->addDisplayGroup(array('submit'), 'buttons');
	
	parent::init();
	}
}
