<?php

class Synerise_Export_Model_Category extends Mage_Catalog_Model_Abstract {

    protected $_categories;
    public $rootCategoryId;
    public $defaultSortDir;
    public $catalog = array();
    
    public function __construct() {   
        $this->rootCategoryId = Mage::app()->getStore()->getRootCategoryId();
    }
    
    protected function _getConfig() {
        return Mage::getModel('synerise_export/config');
    }
    
    protected function _addCategory($category)
    {
        $this->catalog['categories'][$category->getId()]['category'] = array(
            'id' => $category->getId(),
            'name' => $category->getName(),
            'parent_id' => ($category->getParentId() != $this->rootCategoryId) ? $category->getParentId() : '',
            'order' => $category->getPosition()
        );
    }
    
    protected function _addProduct($product) {
        if(!isset($this->catalog['products'][$product->getId()])) {
            $this->catalog['products'][$product->getId()]['product'] = array(
                'id' => $product->getId(),
                'sku' => $product->getSku(),
                'name' => $product->getName(),
            );      
        }
    }
    
    protected function _addProductCategory($product, $category, $position = 0) {      
        $this->catalog['products'][$product->getId()]['product']['categories'][$category->getId()]['category'] = array(
            'id' => $category->getId(),
            'order' => $position
        );       
    }
    
    public function getDefaultSortDir(){
        if(!$this->defaultSortDir) {
            // get product_list layout blocks
            $layout = Mage::getSingleton('core/layout');
            $layout->getUpdate()->load('catalog_category_layered');
            $layout->generateXml()->generateBlocks();
            $toolbarBlock = $layout->getBlock('product_list_toolbar');
            $this->defaultSortDir = $toolbarBlock->getCurrentDirection();            
        }
        return $this->defaultSortDir;
    }
    
    public function getStoreCategories() {
        if(empty($this->_categories)) {
            $this->_categories = Mage::helper('catalog/category')->getStoreCategories(false,true,false);
            $this->_categories
                ->addAttributeToSelect('default_sort_by')                    
                ->addAttributeToSelect('is_anchor')
                ->setOrder('level');
        }
        return $this->_categories;
    }
    

    public function getCatalogData($storeId) {
        $this->catalog['products']['xmlUrl'] = $this->_getConfig()->getOffersUrl($storeId);  
        foreach($this->getStoreCategories() as $category) {
            $this->getCatalogDataByCategory($category);
        }
                
        return $this->catalog;
    }    
    
    public function getCatalogDataByCategory($category) {

        if (!$category->getIsActive()) {
            return false;
        }

        $this->_addCategory($category);        

        $product_collection = $category->getProductCollection();
        $product_collection->addAttributeToSelect('name');
        $product_collection->addAttributeToSelect('type');
        
        
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($product_collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($product_collection);
    
        // default category sort_by [after getLoadedProductCollection]
        $sort = $category->getDefaultSortBy();
        // default product list dir
        $dir = $this->getDefaultSortDir();

        // apply order
        $product_collection->setOrder($sort, $dir);        

        $cur_position = 1;           

        // paging
        $product_collection->setPageSize(100); 
        $pages = $product_collection->getLastPageNumber();
        $currentPage = 1;         

        do {
            $product_collection->setCurPage($currentPage);
            $product_collection->load();
            
            foreach ($product_collection as $product) {
                    $this->_addProduct($product);
                    $this->_addProductCategory($product, $category, $cur_position++);
            }

            $currentPage++;
            //clear collection and free memory
            $product_collection->clear();              

        } while ($currentPage <= $pages);
        
    }

}
