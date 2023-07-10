<?php

/** Controller for displaying images
 * @author Daniel Pett <dpett at britishmuseum.org>
 * @category   Pas
 * @package Pas_Controller_Action
 * @subpackage Admin
 * @copyright  Copyright (c) 2011 DEJ Pett dpett @ britishmuseum . org
 * @license http://www.gnu.org/licenses/agpl-3.0.txt GNU Affero GPL v3.0
 * @version 1
 * @uses Slides
 * @uses Pas_Zoomify_FileProcessor
 * @uses Pas_ArrayFunctions
 * @uses FindsImages
 * @uses ImageLinkForm
 * @uses Pas_Exception_Param
 * @uses Zend_File_Transfer_Adapter_Http
 *
 */
class Database_ImagesController extends Pas_Controller_Action_Admin
{

    /** The redirect
     *
     */
    const REDIRECT = 'database/images/';
    /** The thumbnail path
     *
     */
    const THUMB = 'thumbnails/';
    /** The small path
     *
     */
    const SMALL = 'small/';
    /** The medium path
     *
     */
    const MEDIUM = 'medium/';
    /** The large path
     *
     */
    const LARGE = 'large/';
    /** The display path
     *
     */
    const DISPLAY = 'display/';
    /** The extensions
     *
     */
    const EXT = '.jpg';
    /** The images model
     * @access protected
     * @var \Slides
     */
    protected $_images;
    /** The zoomify class
     * @access protected
     * @var \Pas_Zoomify_FileProcessor
     */
    protected $_zoomifyObject;
    /** The array tools function class
     * @access protected
     * @var \Pas_ArrayFunctions
     */
    protected $_arrayTools;

    /** Set up the ACL and contexts
     * @access public
     * @return void
     */
    public function init()
    {
        $this->_helper->_acl->allow('public', array('image', 'zoom', 'index'));
        $this->_helper->_acl->allow('member', array('add', 'delete', 'edit', 'attached'));
        $this->_helper->_acl->allow('flos', null);

        $this->_helper->contextSwitch()->setAutoJsonSerialization(false);
        $this->_helper->contextSwitch()
            ->setAutoDisableLayout(true)
            ->addContext('csv', array('suffix' => 'csv'))
            ->addContext('kml', array('suffix' => 'kml'))
            ->addContext('rss', array('suffix' => 'rss'))
            ->addContext('atom', array('suffix' => 'atom'))
            ->addActionContext('image', array('xml', 'json'))
            ->addActionContext('index', array('xml', 'json'))
            ->initContext();
        $this->_images = new Slides();
        $this->_zoomifyObject = new Pas_Zoomify_FileProcessor();
        $this->_arrayTools = new Pas_ArrayFunctions();
    }

    /** Display index page of images
     * @access public
     * @return void
     */
    public function indexAction()
    {
        $form = new SolrForm();
        $form->removeElement('thumbnail');
        $form->removeElement('3D');
        $this->view->form = $form;
        $search = new Pas_Solr_Handler();
        $search->setCore('images');
        $search->setFields(array(
            'id', 'identifier', 'objecttype',
            'title', 'broadperiod', 'imagedir',
            'filename', 'thumbnail', 'old_findID',
            'county', 'licenseAcronym', 'findID',
            'institution', 'recordtype'
        ));
        $search->setFacets(array(
            'licenseAcronym', 'broadperiod', 'county',
            'objecttype', 'institution', 'imageRights'
        ));
        if ($this->getRequest()->isPost() && $form->isValid($this->_request->getPost())
            && !is_null($this->getParam('submit'))
        ) {
            //Check if valid
            if ($form->isValid($form->getValues())) {
                $params = $this->_arrayTools->array_cleanup($form->getValues());
                $this->_helper->Redirector->gotoSimple('index', 'images', 'database', $params);
            } else {
                //if failed, refill form
                $form->populate($this->_request->getPost());
                $params = $form->getValues();
            }
        } else {
            $params = $this->getAllParams();
            $form->populate($this->getAllParams());
        }
        //If q parameter is not set or is '', set default query
        if (!isset($params['q']) || $params['q'] == '') {
            $params['q'] = '*';
        }
        $params['show'] = 18;
        //Set the search params
        $search->setParams($params);
        //Execute the search
        $search->execute();
        //Process the facets
        $search->processFacets();
        //Send pagination to view
        $this->view->paginator = $search->createPagination();
        //Send results to view
        $this->view->results = $search->processResults();
        //Send facets to view
        $this->view->facets = $search->processFacets();
    }

    /** View details of a specific image
     * @access public
     * @return void
     * @throws Exception
     */
    public function imageAction()
    {
        if ($this->getParam('id', false)) {
            $this->view->images = $this->_images->getImage((int)$this->getParam('id'));
            $this->view->finds = $this->_images->getLinkedFinds((int)$this->getParam('id'), $this->getParam('recordtype'));
        } else {
            throw new Pas_Exception_Param($this->_missingParameter, 500);
        }
    }

    /** Edit a specific image
     * @access public
     * @throws Pas_Exception_Param
     */
    public function editAction()
    {
        if ($this->getParam('id', 0)) {
            $help = new Help();
            $this->view->contents = $help->fetchRow('id = 14')->toArray();
            $form = new ImageEditForm();
            $form->submit->setLabel('Update image');
            $this->view->form = $form;
            if ($this->_request->isPost()) {
                if ($form->isValid($this->_request->getPost())) {
                    $updateData = $form->getValues();
                    $where = $this->_images->getAdapter()->quoteInto('imageID = ?', $this->getParam('id'));
                    $this->_images->update($updateData, $where);
                    $this->_helper->solrUpdater->update('images', $this->getParam('id'), $this->getParam('recordtype'));
                    $this->getFlash()->addMessage('Image and metadata updated!');
                    $this->redirect(self::REDIRECT . 'image/id/' . $this->getParam('id') . '/recordtype/' . $this->getParam('recordtype'));
                } else {
                    $form->populate($this->_request->getPost());
                }
            } else {
                $id = (int)$this->_request->getParam('id', 0);
                $image = $this->_images->getImage($id);
                if (!empty($image)) {
                    $form->populate($image['0']);
                } else {
                    throw new Pas_Exception('No image with that ID found', 500);
                }
            }
        } else {
            throw new Pas_Exception_Param($this->_missingParameter, 500);
        }
    }

    /** Delete an image
     * @access public
     * @return void
     */
    public function deleteAction()
    {
        $recordtype = $this->_request->getParam('recordtype');
        $this->view->recordtype = $recordtype;

        if ($this->_request->isPost()) {
            $id = (int)$this->_request->getPost('id');
            $del = $this->_request->getPost('del');
            $type  = $this->_request->getPost('recordtype');
            if ($del == 'Yes' && $id > 0) {
                $imagedata = $this->_images->getFileName($id, $type);
                $filename = $imagedata['0']['f'];
                $splitf = explode('.', $filename);
                $spf = $splitf['0'];
                $imagedir = rtrim($imagedata['0']['imagedir'],'/') . '/';
                $imagenumber = $imagedata['0']['imageID'];
                $zoom = './' . $imagedir . 'zoom/' . $spf . '_zdata';
                $thumb = rtrim(IMAGE_PATH, '/') . '/' . 'thumbnails/' . $imagenumber . '.jpg';
                $small = './' . $imagedir . 'small/' . $filename;
                $display = './' . $imagedir . 'display/' . $filename;
                $medium = './' . $imagedir . 'medium/' . $filename;
                $original = './' . $imagedir . $filename;
                $where = 'imageID = ' . $id;
                $this->_images->delete($where);
                $this->_helper->solrUpdater->deleteById('images', $id);
                $linked = new FindsImages();
                $wherelinks = array();
                $wherelinks[] = $linked->getAdapter()->quoteInto('image_id = ?', $imagedata['0']['secuid']);
                $linked->delete($wherelinks);
                $this->getFlash()->addMessage('Image and metadata deleted');
                $images = array($thumb, $display, $small, $medium, $original, $zoom);

                //Delete images from server
                $this->unlinker($images);
                $this->deleteImageOriginals($original);

                $this->_helper->solrUpdater->update('objects', $imagedata['0']['id'], $recordtype);
            }
            $this->redirect('/database/myscheme/myimages/');
        } else {
            $id = (int)$this->_request->getParam('id');
            if ((int)$id > 0) {
                $this->view->slide = $this->_images->fetchRow('imageID =' . $id);
            }
        }
    }

    /** Function to unlink images
     * @access public
     * @return true
     */
    public function unlinker($images)
    {
        foreach ($images as $image) {
            if (file_exists($image)) {
                unlink($image);
            }
        }
        return $this;
    }

    /** Delete any non .jpg original images
     * @param $originalImage
     * @return void
     */
    private function deleteImageOriginals($originalImage)
    {
        $listOfImages = glob(pathinfo($originalImage, PATHINFO_DIRNAME) . '/' . pathinfo($originalImage, PATHINFO_FILENAME) . '.*');

        foreach ($listOfImages as $image) {
            if (file_exists($image)) {
                 unlink($image);
            }
        }
    }

    /** View a zooming image of the file
     * @access public
     * @return void
     * @throws Pas_Exception_Param
     */
    public function zoomAction()
    {
        if ($this->getParam('id', false)) {
            $file = $this->_images->getFileName($this->getParam('id'), $this->getParam('recordtype'));
            $this->view->data = $file;
            $this->view->path = $file[0]['f'];
        } else {
            throw new Pas_Exception_Param($this->_missingParameter, 404);
        }
    }

    /** Upload images
     * Most of the magic happens via ajax calls
     * @access public
     * @return void
     */
    public function addAction()
    {
        $form = new UploadForm();
        $this->view->form = $form;
        $this->view->findID = $this->getParam('id');
        $this->view->recordtype = $this->getParam('recordtype');
    }

    /** Show images attached to record
     * @access public
     * @return void
     * @throws Pas_Exception_Param
     */
    public function attachedAction()
    {
        if ($this->getParam('id', false)) {
            $help = new Help();
            $this->view->contents = $help->fetchRow('id = 14')->toArray();
            $images = new Slides();
            $this->view->images = $images->getSlides($this->getParam('id'), $this->getParam('recordtype'));
            $this->view->recordType = $this->getParam('recordtype');
        } else {
            throw new Pas_Exception_Param($this->_missingParameter, 404);
        }
    }
}