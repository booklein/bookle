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
class Ves_Blog_Adminhtml_PostController extends Mage_Adminhtml_Controller_Action {
    protected function _initAction() {
        $this->loadLayout()
        ->_setActiveMenu('ves_blog/post');

        return $this;
    }

    public function indexAction() {

        $this->_title($this->__('Posts Manager'));
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('ves_blog/adminhtml_post') );
        $this->renderLayout();

    }

    public function addAction(){
        $this->_redirect('*/*/edit');
    }

    public function editAction() {
        $postId     = $this->getRequest()->getParam('id');

        $_model  = Mage::getModel('ves_blog/post')->load($postId);

        $this->_title($_model->getId() ? $this->__('Edit Blog: ').$_model->getTitle() : $this->__('Ves Blog : New Record'));

        Mage::register('post_data', $_model);
        Mage::register('current_post', $_model);

        $this->_initAction();
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Posts Manager'), Mage::helper('adminhtml')->__('Posts Manager'), $this->getUrl('*/*/'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Edit Record'), Mage::helper('adminhtml')->__('Edit Record'));

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
        $this->_addContent($this->getLayout()->createBlock('ves_blog/adminhtml_post_edit'))
        ->_addLeft($this->getLayout()->createBlock('ves_blog/adminhtml_post_edit_tabs'));
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
            ->addItem('js_css','prototype/windows/themes/default.css')
            ->addCss('lib/prototype/windows/themes/magento.css');
        }
        $this->renderLayout();

    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            try {
			    //Duplicate Block Builder Profile
                if ($this->getRequest()->getParam("duplicate")) {
                        $model_clone = Mage::getModel('ves_blog/post');
                        $model = Mage::getModel('ves_blog/post')
                                ->load($this->getRequest()->getParam("id"));

                        $post_id = 0;
                        $post_data = array('is_active' => $model->getIsActive(),
                           'title' => $model->getTitle(),
                           'identifier' => $model->getIdentifier()."-clone-".rand(0,9).time(),
                           'description' => $model->getDescription(),
                           'detail_content' => $model->getDetailContent(),
                           'created' => date( 'Y-m-d H:i:s' ),
                           'updated' => date( 'Y-m-d H:i:s' ),
                           'user_id' => $model->getUserId(),
                           'update_user' => $model->getUpdateUser(),
                           'meta_keywords' => $model->getMetaKeywords(),
                           'meta_description' => $model->getMetaDescription(),
                           'position' => $model->getPosition(),
                           'tags' => $model->getTags(),
                           'hits' => $model->getHits(),
                           'stores' => $model->getStoreId(),
                           'file' => $model->getFile(),
                           'category_id' => $model->getCategoryId());

                        $model_clone->setData($post_data);

                        try {

                            $today = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
                            $model_clone->setCreated($today);
                            $model_clone->setUpdated($today);
                            $_user = Mage::getSingleton('admin/session')->getUser();

                            $model_clone->setUserId($_user->getId());

                            $model_clone->save();
                            $extension = ".html";

                            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ves_blog')->__('Profile was successfully duplicated'));
                            Mage::getSingleton('adminhtml/session')->setFormData(false);

                        } catch (Exception $e) {
                            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                            Mage::getSingleton('adminhtml/session')->setFormData($post_data);
                        }
                    } else {
                        if(isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
                            try {
                                /* Starting upload */
                                $uploader = new Varien_File_Uploader('file');

                                                // Any extention would work
                                $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                                $uploader->setAllowRenameFiles(false);

                                                // Set the file upload mode
                                                // false -> get the file directly in the specified folder
                                                // true -> get the file in the product like folders
                                                //  (file.jpg will go in something like /media/f/i/file.jpg)
                                $uploader->setFilesDispersion(false);

                                                // We set media as the upload dir
                                $path = Mage::getBaseDir('media') . '/blog/';
                                $uploader->save($path, $_FILES['file']['name'] );

                            } catch (Exception $e) {

                            }
                                            //this way the name is saved in DB
                            $data['file'] = 'blog/' .preg_replace("#\s+#","_", $_FILES['file']['name']);

                            $sizes = Mage::helper("ves_blog")->getImageSizeModes();
                            foreach( $sizes as $key => $size ){
                                $c = Mage::getStoreConfig( 'ves_blog/general_setting/'.$key );
                                $tmp = explode( "x", $c );
                                if( count($tmp) > 0 && (int)$tmp[0] ){
                                    Mage::helper('ves_blog')->resizeImage( $data['file'], $size, (int)$tmp[0], (int)$tmp[1] );
                                }
                            }
                        } elseif((isset($data['file']['delete']) && $data['file']['delete'] == 1)){
                            //set path to null and save to database
                            $data['file'] = "";

                            //can also delete file from fs
                            unlink(Mage::getBaseDir('media') . DS . $data['file']['value']);
                        } else {
                            $data['file'] = isset($data['file']['value'])?$data['file']['value']:"";
                        }

                        $_model = Mage::getModel('ves_blog/post');

                        $_model->setData($data)->setId($this->getRequest()->getParam('id'));

                        $created_at = $this->getRequest()->getParam('created');
                        $updated_at = $this->getRequest()->getParam('updated');
                        $today = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);

                        //Created date
                        if(!empty($created_at)){
                            $_model->setCreated($created_at);
                        }else{
                            $_model->setCreated($today);

                        }
                        //Updated date
                        if(!empty($updated_at)){
                            $_model->setUpdated($updated_at);
                        }else{
                            $_model->setUpdated($today);

                        }

                       $_user = Mage::getSingleton('admin/session')->getUser();

                       $_model->setUserId($_user->getId());

                       $_model->save();

                       Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('ves_blog')->__('Record was successfully saved'));
                       Mage::getSingleton('adminhtml/session')->setFormData(false);


                       if ($this->getRequest()->getParam('back')) {
                            $this->_redirect('*/*/edit', array('id' => $_model->getId()));
                            return;
                        }

                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    Mage::getSingleton('adminhtml/session')->setFormData($data);
                                //$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                    return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('ves_blog')->__('Unable to find record to save'));
        $this->_redirect('*/*/');
}

public function deleteAction() {
    if( $this->getRequest()->getParam('id') > 0 ) {
        try {
            $model = Mage::getModel('ves_blog/post');

                 // save rewrite url
            Mage::getModel('core/url_rewrite')
            ->loadByIdPath('venusblog/post/'.$this->getRequest()->getParam('id'))
            ->delete();

            $model->setId($this->getRequest()->getParam('id'))
            ->delete();



            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Record was successfully deleted'));
            $this->_redirect('*/*/');
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
        }
    }
    $this->_redirect('*/*/');
}

public function massRewriteAction(){
  try {
     $collection = Mage::getModel('ves_blog/post')->getCollection();
     $resroute = Mage::getStoreConfig('ves_blog/general_setting/route');
     $extension = ".html";
     foreach( $collection as $post ){
        Mage::getModel('core/url_rewrite')
        ->loadByIdPath('venusblog/post/'.$post->getId())
        ->setIdPath('venusblog/post/'.$post->getId())
        ->setRequestPath( $resroute.'/'.$post->getIdentifier().$extension)
        ->setTargetPath('venusblog/post/view/id/'.$post->getId())
        ->save();
    }
    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Rewrite URLs Of All Post are resized successful'));
} catch ( Exception $e ) {
   Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
}

$this->_redirect('*/*/');
}

public function massResizeAction(){
  try {
     $collection = Mage::getModel('ves_blog/post')->getCollection();

     foreach( $collection as $post ){
        if( $post->getFile() ){
                //$storecl->addAttributeToFilter('post_id', array('eq' => $post->getId()));
            $post_model = Mage::getModel("ves_blog/post")->load($post->getId());
            foreach ($post_model->getStoreId() as $store_id) {
               $sizes = array("large_imagesize"=>"l","medium_imagesize"=>"m","small_imagesize"=>"s");
               foreach( $sizes as $key => $size ){
                            //echo $store_id;
                  $c = Mage::getStoreConfig( 'ves_blog/general_setting/'.$key, $store_id);
                  $tmp = explode( "x", $c );
                  if( count($tmp) > 0 && (int)$tmp[0] ){
                    $image= str_replace("/",DS, $post->getFile());
                    $_imageUrl = Mage::getBaseDir('media').DS.$image;
                    if(!$store_id){
                        $imageResized = Mage::getBaseDir('media').DS."resized".DS.$size.DS.$image;
                        if(file_exists($imageResized)) {
                            unlink($imageResized);
                        }
                        Mage::helper('ves_blog')->resizeImage( $post->getFile(), $size, (int)$tmp[0], (int)$tmp[1], 0 );
                    }else{
                        $image2 = str_replace("blog".DS,"", $image);
                        $imageResized = Mage::getBaseDir('media').DS."resized".DS.$size.DS."blog".DS.$store_id.DS.$image2;
                        if(file_exists($imageResized)) {
                            unlink($imageResized);
                        }
                        Mage::helper('ves_blog')->resizeImage( $post->getFile(), $size, (int)$tmp[0], (int)$tmp[1], $store_id );
                    }
                }
            }
        }
    }
}
Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Images Of All Post are resized successful'));
} catch ( Exception $e ) {
 Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
}

$this->_redirect('*/*/');

}


public function massDeleteAction() {
    $IDList = $this->getRequest()->getParam('post');
    if(!is_array($IDList)) {
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select record(s)'));
    } else {
        try {
            foreach ($IDList as $itemId) {
                    // save rewrite url
                Mage::getModel('core/url_rewrite')
                ->loadByIdPath('venusblog/post/'.$itemId)
                ->delete();

                $_model = Mage::getModel('ves_blog/post')
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
    $IDList = $this->getRequest()->getParam('post');
    if(!is_array($IDList)) {
        Mage::getSingleton('adminhtml/session')->addError($this->__('Please select record(s)'));
    } else {
        try {
            foreach ($IDList as $itemId) {
                $_model = Mage::getSingleton('ves_blog/post')
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

protected function _title($text = null, $resetIfExists = true)
{
    if (is_string($text)) {
        $this->_titles[] = $text;
    } elseif (-1 === $text) {
        if (empty($this->_titles)) {
            $this->_removeDefaultTitle = true;
        } else {
            array_pop($this->_titles);
        }
    } elseif (empty($this->_titles) || $resetIfExists) {
        if (false === $text) {
            $this->_removeDefaultTitle = false;
            $this->_titles = array();
        } elseif (null === $text) {
            $this->_removeDefaultTitle = true;
            $this->_titles = array();
        }
    }
    return $this;
}
private function _getCurrentStoreId() {
        if (strlen($code = Mage::getSingleton('adminhtml/config_data')->getStore())) // store level
        {
            $store_id = Mage::getModel('core/store')->load($code)->getId();
        }
        elseif (strlen($code = Mage::getSingleton('adminhtml/config_data')->getWebsite())) // website level
        {
            $website_id = Mage::getModel('core/website')->load($code)->getId();
            $store_id = Mage::app()->getWebsite($website_id)->getDefaultStore()->getId();
        }
        else // default level
        {
            $store_id = 0;
        }
        return $store_id;
    }

     /**
     * Export order grid to CSV format
     */
     public function exportCsvAction()
     {
      $fileName = 'ves_blog_post.csv';
      $grid = $this->getLayout()->createBlock('ves_blog/adminhtml_post_exportGrid');
      $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
  }

  public function uploadCsvAction() {
      $this->loadLayout();
      $block = $this->getLayout()->createBlock('ves_blog/adminhtml_post_upload');
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
          Mage::getSingleton('ves_blog/import_post')->process($filepath, $stores);
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