<?php
/** Controller for displaying informatuon index
* 
* @category   Pas
* @package    Pas_Controller
* @subpackage ActionAdmin
* @copyright  Copyright (c) 2011 DEJ Pett dpett @ britishmuseum . org
* @license http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero GPL v3.0
*/
class Info_IndexController extends Pas_Controller_Action_Admin {
	/** Set up acl
	*/	
    public function init() {
    $this->_helper->_acl->allow(null);
    }
	
    /** Display the list of topics or individual pages.
     * 
     */	   
    public function indexAction() {
    $content = new Content();
    $this->view->contents = $content->getFrontContent('info');
    }
    
}