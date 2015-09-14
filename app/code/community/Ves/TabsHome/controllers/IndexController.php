<?php

class Ves_TabsHome_IndexController extends Mage_Core_Controller_Front_Action {

	public function ajaxAction() {

		$json = array();
		$data = $this->getRequest()->getPost();
		
		 // Check if request have data
		if ( empty($data) || !isset($data['moduleid']) || !isset($data['source_type']) )
		{
			Mage::app()->getResponse()->setRedirect(Mage::getBaseUrl());
			return;
		}
		
		$categoryid = isset($data['categoryid'])?$data['categoryid']:0;
		$page = isset($data['page'])?$data['page']:1;
		$source_type = isset($data['source_type'])?$data['source_type']:"news";
		$moduleid = isset($data['moduleid'])?$data['moduleid']:"";
		$time_temp = isset($data['time_temp'])?$data['time_temp']:"";
		$helper = Mage::helper('ves_tabshome/data');

		$config_widget = isset($data['wg'])?$data['wg']:"";
		$attributes = array();
		if($config_widget) {
			$attributes = base64_decode($config_widget);
			$attributes = unserialize($attributes);
		}

		foreach ($attributes as $k => $v) {
			if( $v == 'false' ){
				$attributes[$k] = false;
			}
			if( $v == 'true' ){
				$attributes[$k] = true;
			}
		}

		$config = $helper->get($attributes);
		$config['page'] = $page;
		$limit_item = isset($config['limit_item'])?$config['limit_item']:16;
		$itemspage = isset($config['itemspage'])?$config['itemspage']:6;
		$itemsrow = isset($config['itemsrow'])?$config['itemsrow']:4;

		switch ($source_type) {
			case 'allproducts':
			$lists = Mage::getModel('ves_tabshome/product')->getListAllProducts($config);
			break;
			case 'news':
			$lists = Mage::getModel('ves_tabshome/product')->getListNewProducts($config);
			break;
			case 'featured':
			$lists = Mage::getModel('ves_tabshome/product')->getListFeaturedProducts($config);
			break;
			case 'mostview':
			$lists = Mage::getModel('ves_tabshome/product')->getListMostViewedProducts($config);
			break;
			case 'bestseller':
			$data['bestseller_from_date'] = Mage::getStoreConfig('ves_tabshome/ves_tabshome/bestseller_from_date')!=''?Mage::getStoreConfig('ves_tabshome/ves_tabshome/bestseller_from_date'):'';
			$data['bestseller_to_date'] = Mage::getStoreConfig('ves_tabshome/ves_tabshome/bestseller_to_date')!=''?Mage::getStoreConfig('ves_tabshome/ves_tabshome/bestseller_to_date'):'';
			$lists = Mage::getModel('ves_tabshome/product')->getListBestSellerProducts($config);
			break;
			case 'specical':
			$lists = Mage::getModel('ves_tabshome/product')->getListSpecialProducts($config);
			break;
			case 'related':
			$lists = Mage::getModel('ves_tabshome/product')->getListRelatedProducts($config);
			break;
			case 'upsell':
			$lists = Mage::getModel('ves_tabshome/product')->getListUpsellProducts($config);
			break;
		}

		$hasNextData = $lists['hasNextData'];
		$json['catblockid'] = isset($data['catblockid'])?$data['catblockid']:0;
		$json['moduleId'] = $moduleid;
		if($hasNextData){
			$json['hasNextData'] = 1;
		}else{
			$json['hasNextData'] = 0;
		}
		$json['row'] = $json['rows'] = '';

		$row = $itemsrow -  (( ($page-1) * $itemspage) % $itemsrow);
		if( $row % $itemsrow == 0 ){ $row=0; }


		$i = $x = 0;
		$c = 1;
		$size = count($lists['products']);
		$loadScript = true;

		if($row>0){
			$x = ($itemsrow - $row) + 1;
		}

		$number_item = 0;
		$lastPage = ceil( $limit_item / $itemspage );

		if(  $page == $lastPage ){
			$number_item = $limit_item - (($page-1) * $itemspage);
		}

		foreach ($lists['products'] as $product) {
			if($i<$row && $page>1){
				$json['row'].= Mage::app()
				->getLayout()
				->createBlock("ves_tabshome/item")
				->setConfig('curPage',$page)
				->setAllConfig($config)
				->assign('i', $x)
				->assign('product',$product)
				->assign('loadScript',$loadScript)
				->toHtml();
			}else{
				$json['rows'] .= Mage::app()
				->getLayout()
				->createBlock("ves_tabshome/item")
				->setConfig('page',$page)
				->setAllConfig($config)
				->assign('i', $c)
				->assign('product',$product)
				->assign('loadScript',$loadScript)
				->toHtml();
				$c++;
			}
			if( $i == $number_item - 1 && $source_type != 'allproducts'){ break; }
			$i ++;
		}
		echo Mage::helper('core')->jsonEncode( $json );
	}
}