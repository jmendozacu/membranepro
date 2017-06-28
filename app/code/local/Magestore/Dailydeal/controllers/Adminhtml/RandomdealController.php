<?php

class Magestore_Dailydeal_Adminhtml_RandomdealController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction(){
			if(!Mage::registry('is_random_dailydeal'))
                Mage::helper('dailydeal')->updateDailydealStatus();
		$this->loadLayout()
			->_setActiveMenu('dailydeal/randomdeal')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Deal Generator Manager'), Mage::helper('adminhtml')->__('Deal Generator Manager'));
             
		return $this;
	}
 
	public function indexAction(){
		if(!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)){ return; }
		$this->_initAction()
			->renderLayout();
	}
    
	public function editAction() {
		if(!Mage::helper('magenotification')->checkLicenseKeyAdminController($this)){ return; }
        Mage::getSingleton('adminhtml/session')->addNotice(Mage::helper('dailydeal')->__('Deal Generator helps you create deals automatically in random mode. Please select products for which you would like to create deal, then fill in the information pattern below.'));
		
		$id	 = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('dailydeal/dailydeal')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data))
				$model->setData($data);

			Mage::register('randomdeal_data', $model);
			$this->loadLayout();
			$this->_setActiveMenu('dailydeal/dailydeal');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Deal Generator Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Deal Generator News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
			$this->_addContent($this->getLayout()->createBlock('dailydeal/adminhtml_randomdeal_edit'))
				->_addLeft($this->getLayout()->createBlock('dailydeal/adminhtml_randomdeal_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('dailydeal')->__('Deal Generator does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
        public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
                        if(isset($_FILES['thumbnail']['name']) && $_FILES['thumbnail']['name'] != '') {
				try {
					/* Starting upload */	
					$uploader1 = new Varien_File_Uploader('thumbnail');
					
					// Any extention would work
			   		$uploader1->setAllowedExtensions(array('jpg','jpeg','gif','png'));
					$uploader1->setAllowRenameFiles(false);
					
					// Set the file upload mode 
					// false -> get the file directly in the specified folder
					// true -> get the file in the product like folders 
					//	(file.jpg will go in something like /media/f/i/file.jpg)
					$uploader1->setFilesDispersion(false);
							
					// We set media as the upload dir
					$path_thumbnail_image = Mage::getBaseDir('media') . DS ;
					$uploader1->save($path_thumbnail_image.'dailydeal/main', $_FILES['thumbnail']['name'] );
					
				} catch (Exception $e) {}
				
				//this way the name is saved in DB
	  			$data['thumbnail_image'] ='dailydeal/main/'.$_FILES['thumbnail']['name'];
			}
			$data = $this->_filterDateTime($data,array('start_time','close_time')); 
                   if(isset($data['thumbnail'])){
                        $delete_thumbnail=($data['thumbnail']);
                        if ($delete_thumbnail['delete']==1){
                            $data['thumbnail_image']=null;
                        }
                   }
			$model = Mage::getModel('dailydeal/dailydeal')
                                ->load($this->getRequest()->getParam('id'));
                        
                       $data['is_random']=1;
                       $data['product_name']=$data['title'];
                       $str=implode(',', $data['in_products']);
                       $data['products']=$str;
                       if (($model->getStatus()==3 )||($model->getStatus()==4 )){
                           $data['products']=$model->getProducts();
                       }
                       $data['store_id']=implode(',', $data['stores']);
                        $data['status']=$data['status_form'];
                        if ($data['status']==2) $data['product_id']=0;
                        
                        try {
                        	$data['start_time']=date('Y-m-d H:i:s',Mage::getModel('core/date')->gmtTimestamp(strtotime($data['start_time'])));
                        	$data['close_time']=date('Y-m-d H:i:s',Mage::getModel('core/date')->gmtTimestamp(strtotime($data['close_time'])));
                        } catch (Exception $e) {} 

			if(!$model->getTimeLeft()) $data['time_left']=now();
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			try {
                            
				$model->save();
				if(!Mage::registry('is_random_dailydeal'))
                                Mage::helper('dailydeal')->updateDailydealStatus();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('dailydeal')->__('Deal generator was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);
                        
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
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('dailydeal')->__('Unable to find deal generator to save'));
		$this->_redirect('*/*/');
	}
 
        public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('dailydeal/dailydeal');
				$t=($model->load($this->getRequest()->getParam('id'))
                                      ->getStatus());
                                if (($t==4)||($t==2))
                                {
				$model->delete();
                                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Deal generator was successfully deleted'));                                
                                }  else {
                                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Deal generator should not be deleted'));
                                }
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

	public function massDeleteAction() {
		$randomdealIds = $this->getRequest()->getParam('randomdeal');
		if(!is_array($randomdealIds)){
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select deal generator(s)'));
		}else{
			try {
                                $i=0;
				foreach ($randomdealIds as $randomdealId) {
					$randomdeal= Mage::getModel('dailydeal/dailydeal')->load($randomdealId);
                                        $t=$randomdeal->getStatus();
                                        if (($t==4)||($t==2))
                                        {
                                            $randomdeal->delete();
                                            $i++;
                                        }
				}
                                if ($i>0){
                                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Total of %d deal generator(s) were successfully deleted', $i));
                                }else{
                                    Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Deal generators should not be deleted'));
                                } 
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}
	
        function massStatusAction() {
		$randomdealIds = $this->getRequest()->getParam('randomdeal');
		if(!is_array($randomdealIds)) {
			Mage::getSingleton('adminhtml/session')->addError($this->__('Please select deal generator(s)'));
		} else {
			try {
				foreach ($randomdealIds as $randomdealId) {
					$randomdeal = Mage::getSingleton('dailydeal/dailydeal')
						->load($randomdealId)
                                                ->setProductId(0)
						->setStatus($this->getRequest()->getParam('status'))
						->setIsMassupdate(true)
						->save();
				}
				$this->_getSession()->addSuccess(
					$this->__('Total of %d deal generator(s) were successfully updated', count($randomdealIds))
				);
			} catch (Exception $e) {
				$this->_getSession()->addError($e->getMessage());
			}
		}
		$this->_redirect('*/*/index');
	}
        public function exportCsvAction(){
		$fileName   = 'randomdeal.csv';
		$content	= $this->getLayout()->createBlock('dailydeal/adminhtml_randomdeal_grid')->getCsv();
		$this->_prepareDownloadResponse($fileName,$content);
	}

	public function exportXmlAction(){
		$fileName   = 'randomdeal.xml';
		$content	= $this->getLayout()->createBlock('dailydeal/adminhtml_randomdeal_grid')->getXml();
		$this->_prepareDownloadResponse($fileName,$content);
	}

        public function listproductAction()
	{
        $this->loadLayout();
        $this->getLayout()->getBlock('randomdeal.edit.tab.listproduct')
            ->setProduct($this->getRequest()->getPost('aproduct', null));
        $this->renderLayout();	
	}
        public function listproductGridAction()
	{
        $this->loadLayout();
        $this->getLayout()->getBlock('randomdeal.edit.tab.listproduct')
            ->setProduct($this->getRequest()->getPost('aproduct', null));
        $this->renderLayout();		
	}
        public function listorderGridAction()
	{
        $this->loadLayout();
        $this->renderLayout();		
	}
}
