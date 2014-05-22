<?php
/** Controller for news map
* 
* @category   Pas
* @package    Pas_Controller
* @subpackage ActionAdmin
* @author     Daniel Pett <dpett@britishmuseum.org>
* @copyright  Daniel Pett 2011 <dpett@britishmuseum.org>
* @license    GNU General Public License
*/
class News_MapController extends Pas_Controller_Action_Admin {
	
	
	/** Initialise the ACL and contexts
	*/ 
	public function init() {
 		$this->_helper->_acl->allow(null);
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
	}
	/** Initialise index pages
	*/ 
	public function indexAction(){
	}

}