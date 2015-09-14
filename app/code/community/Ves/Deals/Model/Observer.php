<?php
 /*------------------------------------------------------------------------
  # Ves Blog Module 
  # ------------------------------------------------------------------------
  # author:    Ves.Com
  # copyright: Copyright (C) 2012 http://www.ves.com. All Rights Reserved.
  # @license: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
  # Websites: http://www.ves.com
  # Technical Support:  http://www.ves.com/
-------------------------------------------------------------------------*/
			
class Ves_Deals_Model_Observer  extends Varien_Object
{
	/**
	 *
	 */
	 public function initControllerRouters($observer){
	
        $request = $observer->getEvent()->getFront()->getRequest();
		if (!Mage::app()->isInstalled()) {
            return;
        }
	
		$identifier = trim($request->getPathInfo(), '/');
	
	 
        $condition = new Varien_Object(array(
            'identifier' => $identifier,
            'continue'   => true
        ));
        Mage::dispatchEvent('deals_controller_router_match_before', array(
            'router'    => $this,
            'condition' => $condition
        ));
        $identifier = $condition->getIdentifier();
		$identifier = trim($identifier, "/");
		 
        if ($condition->getRedirectUrl()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect($condition->getRedirectUrl())
                ->sendResponse();
            $request->setDispatched(true);
            return true;
        }

        if (!$condition->getContinue())
            return false;
		
		$route = trim( Mage::getStoreConfig('ves_deals/deals_setting/route') );

        if($identifier) {
        	
	            if(  preg_match("#^".$route."(\.html)?$#",$identifier, $match) ) {

	                $request->setModuleName('vesdeals')
	                        ->setControllerName('index')
	                        ->setActionName('index');
	                $request->setAlias(
	                    Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
	                    $identifier
	                );

			        return true;
	      
				
	            } elseif(str_replace('/rss', '', str_replace($route, '', $identifier)) == '') {

					$request->setModuleName('vesdeals')
				    ->setControllerName('rss')
				    ->setActionName('index');
			    
					$request->setAlias(
						Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
						$identifier
					);
					return true;
			    } elseif( preg_match("#".$route."/filter/(\w+)\.?#",$identifier, $match) ) {

					if( count($match)<= 1 ){
						return false;
					}  

					$request->setModuleName('vesdeals')
					    ->setControllerName('index')
					    ->setActionName('index')
						->setParam("filter",$match[1]);
				    
					$request->setAlias(
						Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
						$identifier
					);
					return true;
				}

			}
        return false;
	}

   public function getModuleConfig( $val ){
		return Mage::getStoreConfig( "ves_deals/module_setting/".$val );
   }

}
