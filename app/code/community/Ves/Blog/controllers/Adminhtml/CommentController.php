<?php
/**
 * Venustheme
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Venustheme EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.venustheme.com/LICENSE-1.0.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.venustheme.com/ for more information
 *
 * @category   Ves
 * @package    Ves_Blog
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */

/**
 * Ves Blog Extension
 *
 * @category   Ves
 * @package    Ves_Blog
 * @author     Venustheme Dev Team <venustheme@gmail.com>
 */
class Ves_Blog_Adminhtml_CommentController extends Mage_Adminhtml_Controller_Action {
    protected function _initAction() {
        $this->loadLayout()
        ->_setActiveMenu('ves_blog/comment');

        return $this;
    }


	/**
	 * index action
	 */
    public function indexAction() {

      $this->_title($this->__('Comment Manager'));
      $this->_initAction();
      $this->_addContent($this->getLayout()->createBlock('ves_blog/adminhtml_comment') );
      $this->renderLayout();

  }

  public function editAction(){
      $this->_title($this->__('Edit Record'));
      $id     = $this->getRequest()->getParam('id');
      $_model  = Mage::getModel('ves_blog/comment')->load( $id );

      Mage::register('comment_data', $_model);
      Mage::register('current_comment', $_model);
      $this->loadLayout();
      $this->_setActiveMenu('ves_blog/comment');
      $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Blog Manager'), Mage::helper('adminhtml')->__('Blog Manager'), $this->getUrl('*/*/'));
      $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Add Comment'), Mage::helper('adminhtml')->__('Add Comment'));

      $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

      $this->_addContent($this->getLayout()->createBlock('ves_blog/adminhtml_comment_edit'))
      ->_addLeft($this->getLayout()->createBlock('ves_blog/adminhtml_comment_edit_tabs'));
      if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {

        $this->getLayout()->getBlock('head')
        ->setCanLoadTinyMce(true)
        ->addItem('js','tiny_mce/tiny_mce.js')
        ->addItem('js','mage/adminhtml/wysiwyg/tiny_mce/setup.js')
        ->addJs('mage/adminhtml/browser.js')
        ->addJs('prototype/window.js')
        ->addJs('lib/FABridge.js')
        ->addJs('lib/flex.js')
        ->addJs('mage/adminhtml/flexuploader.js')
        ->addCss('lib/prototype/windows/themes/magento.css');

    }
    $this->renderLayout();
}

public function addAction(){
  $this->_forward('edit');
}

public function saveAction() {
  if ($data = $this->getRequest()->getPost()) {
     $model = Mage::getModel('ves_blog/comment');
     $model->setData($data)
     ->setId($this->getRequest()->getParam('id'));
     try {
        $model->save();


				// save rewrite url
        if ($this->getRequest()->getParam('back')) {
           $this->_redirect('*/*/edit', array('id' => $model->getId()));
           return;
       }
       $this->_redirect('*/*/');
       return;
   } catch (Exception $e) {
    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
    Mage::getSingleton('adminhtml/session')->setFormData($data);
    $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
    return;
}
}

Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ves_blog')->__('Unable to find Comment to save'));
$this->_redirect('*/*/');
}

public function imageAction() {
    $result = array();
    try {
        $uploader = new Ves_Blog_Media_Uploader('image');
        $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(true);
        $result = $uploader->save(
            Mage::getSingleton('ves_blog/config')->getBaseMediaPath()
            );

        $result['url'] = Mage::getSingleton('ves_blog/config')->getMediaUrl($result['file']);
        $result['cookie'] = array(
            'name'     => session_name(),
            'value'    => $this->_getSession()->getSessionId(),
            'lifetime' => $this->_getSession()->getCookieLifetime(),
            'path'     => $this->_getSession()->getCookiePath(),
            'domain'   => $this->_getSession()->getCookieDomain()
            );
    } catch (Exception $e) {
        $result = array('error'=>$e->getMessage(), 'errorcode'=>$e->getCode());
    }

    $this->getResponse()->setBody(Zend_Json::encode($result));
}
public function massDeleteAction() {
    $IDList = $this->getRequest()->getParam('comment');
    if(!is_array($IDList)) {
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select record(s)'));
    } else {
        try {
            foreach ($IDList as $itemId) {
                $_model = Mage::getModel('ves_blog/comment')
                ->setIsMassDelete(true)->load($itemId);
                $_model->delete();
            }
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('adminhtml')->__(
                    'Total of %d record(s) were successfully deleted', count($IDList)
                    )
                );
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
    }
    $this->_redirect('*/*/index');
}

public function massStatusAction() {
    $IDList = $this->getRequest()->getParam('comment');
    if(!is_array($IDList)) {
        Mage::getSingleton('adminhtml/session')->addError($this->__('Please select record(s)'));
    } else {
        try {
            foreach ($IDList as $itemId) {
                $_model = Mage::getSingleton('ves_blog/comment')
                ->setIsMassStatus(true)
                ->load($itemId)
                ->setIsActive($this->getRequest()->getParam('status'))
                ->save();
            }
            $this->_getSession()->addSuccess(
                $this->__('Total of %d record(s) were successfully updated', count($IDList))
                );
        } catch (Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
    }
    $this->_redirect('*/*/index');
}


	/**
	 * Delete
	 */
  public function deleteAction() {

      if( $this->getRequest()->getParam('id') > 0 ) {
         try {
            $model = Mage::getModel('ves_blog/comment');

            $model->setId($this->getRequest()->getParam('id'));

            Mage::getModel('core/url_rewrite')->loadByIdPath('ves_blog/comment/'.$model->getId())->delete();

            $model->delete();

            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('This Comment Was Deleted Done'));
            $this->_redirect('*/*/');

        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
        }
    }
    $this->_redirect('*/*/');
}

    /**
     * Export order grid to CSV format
     */
    public function exportCsvAction()
    {
      $fileName = 'ves_blog_comment.csv';
      $grid = $this->getLayout()->createBlock('ves_blog/adminhtml_comment_exportGrid');
      $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
  }

  public function uploadCsvAction() {
      $this->loadLayout();
      $block = $this->getLayout()->createBlock('ves_blog/adminhtml_comment_upload');
      $this->getLayout()->getBlock('content')->append($block);
      $this->renderLayout();
  }

  public function importCsvAction(){
      $profile = $this->getRequest()->getParam('file');
      $sub_folder = $this->getRequest()->getParam('subfolder');

      $filepath = Mage::helper("ves_blog")->getUploadedFile();

      if ($filepath != null) {
        try {
          $stores = Mage::helper("ves_blog")->getAllStores();
                // import into model
          Mage::getSingleton('ves_blog/import_comment')->process($filepath, $stores);
          Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cms')->__('CSV Imported Successfully'));
          $this->_redirect('*/*/index');

      } catch (Exception $e) {
          Mage::logException($e);
          Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cms')->__('An Error occured importing CSV.'));
          Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            } // end if
        }else{
            $this->_redirect('*/*/index');
        }
    }
}