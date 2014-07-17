<?php
/** Controller for accessing Bronze Age guide index page
*
* @category   Pas
* @package    Pas_Controller
* @subpackage ActionAdmin
* @copyright  Copyright (c) 2011 DEJ Pett dpett @ britishmuseum . org
* @license http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero GPL v3.0
* @author     Daniel Pett
*/
class Bronzeage_IndexController extends Pas_Controller_Action_Admin {

    /** Initialise the ACL and contexts
    */
    public function init(){
    $this->_helper->_acl->allow(null);
    }

    /** Render the index pages
    */
    public function indexAction(){
    $content = new Content();
    $this->view->content =  $content->getFrontContent('bronzeage');
    }
}