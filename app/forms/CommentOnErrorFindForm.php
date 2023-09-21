<?php
/** Form for submitting an error
 *
 * @category   Pas
 * @package    Pas_Form
 * @copyright  Copyright (c) 2011 DEJ Pett dpett @ britishmuseum . org
 * @license http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero GPL v3.0
 * @version 1
 * @author Daniel Pett <dpett at britishmuseum.org>
*/
class CommentOnErrorFindForm extends Pas_Form {

    /** The constructor
     * @access public
     * @param array $options
     * @return void
     */
    public function __construct(array $options = null) {

        parent::__construct($options);

        $this->setName('comments');

	$comment_author_IP = new Zend_Form_Element_Hidden('user_ip');
	$comment_author_IP->addFilters(array('StripTags','StringTrim'))
                ->setRequired(true)
                ->addValidator('Ip')
                ->setValue($_SERVER['REMOTE_ADDR']);

	$comment_agent = new Zend_Form_Element_Hidden('user_agent');
	$comment_agent->addFilters(array('StripTags','StringTrim'))
                ->setValue($_SERVER['HTTP_USER_AGENT'])
                ->setRequired(true);

	$comment_subject = new Zend_Form_Element_Hidden('comment_subject');
	$comment_subject->addFilters(array('StripTags','StringTrim'))
                ->setRequired(true);

	$comment_findID = new Zend_Form_Element_Hidden('comment_findID');
	$comment_findID->setRequired(true)
                ->addFilters(array('StripTags','StringTrim'))
                ->addValidators(array('Int'));

	$comment_author = new Zend_Form_Element_Text('comment_author');
	$comment_author->setLabel('Enter your name: ')
                ->setRequired(true)
                ->addFilters(array('StripTags','StringTrim'))
                ->addErrorMessage('Please enter a valid name!');

	$comment_author_email = new Zend_Form_Element_Text('comment_author_email');
	$comment_author_email->setLabel('Enter your email address: ')
		->setRequired(true)
		->setAttrib('size',40)
		->addFilters(array('StripTags','StringTrim','StringToLower'))
		->addValidator('EmailAddress')
		->addErrorMessage('Please enter a valid email address!')
		->setDescription('* This will not be displayed to the public');

	$comment_type = new Zend_Form_Element_Select('comment_type');
	$comment_type->setLabel('Error type: ')
		->setRequired(true)
		->addMultiOptions(array(
                    null => 'Choose error type',
                    'Available types' => array(
                        'Incorrect ID' => 'Incorrect identification',
                        'More info' => 'I have further information',
                        'Incorrect image' => 'Incorrect image',
                        'Incorrect parish' => 'Incorrect parish',
                        'Grid reference issue' => 'Grid reference wrong',
                        'Date found wrong' => 'Date of discovery wrong',
                        'Spelling errors' => 'Spelling errors',
                        'Duplicated record' => 'Duplicated record',
                        'Data problems' => 'Data problems',
                        'Other' => 'Other reason')
                    ))
		->addErrorMessage('You must enter an error report type')
		->setAttrib('class', 'input-xxlarge selectpicker show-menu-arrow');

	$comment_author_url = new Zend_Form_Element_Text('comment_author_url');
	$comment_author_url->setLabel('Enter your web address: ')
                ->setRequired(false)
                ->addFilters(array('StripTags','StringTrim','StringToLower'))
                ->addErrorMessage('Please enter a valid address!')
                ->setDescription('* Not compulsory');

	$comment_content = new Pas_Form_Element_CKEditor('comment_content');
	$comment_content->setLabel('Enter your comment: ')
                ->setRequired(true)
                ->addFilter('StringTrim')
                ->setAttrib('Height',400)
                ->setAttrib('ToolbarSet','Basic')
                ->addFilters(array('StringTrim','WordChars','HtmlBody','EmptyParagraph'))
                ->addErrorMessage('Please enter something in the comments box!');

        $this->addElement(
            (new Pas_Form_Element_Recaptcha('captcha'))
                ->setLabel('Please complete the Captcha field to prove you exist')
        );

	$hash = new Zend_Form_Element_Hash('csrf');
	$hash->setValue($this->_salt)->setTimeout(6000);

	$submit = new Zend_Form_Element_Submit('submit');

	$auth = Zend_Auth::getInstance();

	if(!$auth->hasIdentity()) {
            $this->addElements(array(
                $comment_findID, $comment_author_IP, $comment_agent,
                $comment_subject, $comment_author, $comment_author_email,
                $comment_content, $comment_author_url, $comment_type,
                $submit, $hash));


            $this->addDisplayGroup(array(
                'comment_author','comment_author_email','comment_author_url',
                'comment_type','comment_content', 'captcha',
                'submit'), 'details');
	} else {
            $this->addElements(array(
                $comment_findID, $comment_subject, $comment_author_IP,
                $comment_agent, $comment_author, $comment_author_email,
                $comment_content, $comment_author_url, $comment_type,
                $submit, $hash));

	$this->addDisplayGroup(array(
            'comment_author', 'comment_author_email', 'comment_author_url',
            'comment_type', 'comment_content','captcha', 'submit'), 'details');
	}

	$this->details->setLegend('Enter your error report: ');

	parent::init();
    }
}
