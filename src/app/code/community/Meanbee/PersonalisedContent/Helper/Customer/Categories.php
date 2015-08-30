<?php

class Meanbee_PersonalisedContent_Helper_Customer_Categories
{
    /** @var Meanbee_PersonalisedContent_Helper_Config $config */
    protected $_config;


    /**
     * Update indexes of customer interactions with categories.
     *
     * @return $this
     */
    public function reindexCustomerCategories()
    {
        /** @var Meanbee_PersonalisedContent_Helper_Config $config */
        $config = $this->getConfig();
        if (!$config->isEnabled()) {
            return $this;
        }

        // Empty index table

        /** @var Meanbee_PersonalisedContent_Model_Resource_Customer_Categories $customerCategoriesResource */
        $customerCategoriesResource = Mage::getResourceModel('meanbee_personalisedcontent/customer_categories');
        $customerCategoriesResource->truncate();

        if ($config->isUseOrders()) {
            // Iterate through all orders from logged in customers.
            /** @var Mage_Sales_Model_Resource_Order_Collection $sales */
            $sales = Mage::getModel('sales/order')->getCollection()
                ->addFieldToFilter('customer_id', array('notnull' => true));

            Mage::getSingleton('core/resource_iterator')->walk($sales->getSelect(), array(array($this, 'getProductsForOrder')));
        }

        if ($config->isUsePageviews()) {
            // Iterate through all views products
            // Can't use built-in reports very easily as they're very tailored around product collections and admin reports
            $zendDb = Mage::getModel('core/resource')->getConnection('core_read');
            $tableName = Mage::getSingleton('core/resource')->getTableName('reports/viewed_product_index');
            $select = sprintf('SELECT * FROM %s WHERE customer_id is not NULL;', $tableName);

            Mage::getSingleton('core/resource_iterator')->walk($select, array(array($this, 'getProductsForView')), null, $zendDb);
        }

        return $this;
    }

    /**
     * Find products within an order
     *
     * Accepts resource iterator of products
     * @param $args
     */
    public function getProductsForOrder($args)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order');
        $order->setData($args['row']);

        $orderItemIds = array();
        $visibleItems = $order->getAllVisibleItems();
        foreach ($visibleItems as $visibleItem) {
            $orderItemIds[] = $visibleItem->getProductId();
        }

        /** @var Mage_Catalog_Model_Resource_Product_Collection $products */
        $products = Mage::getModel('catalog/product')->getCollection()
            ->addIdFilter($orderItemIds);

        Mage::getSingleton('core/resource_iterator')->walk($products->getSelect(), array(array($this, 'getProductCategoryIds')), array('customer_id' => $order->getCustomerId()));
    }

    /**
     * Take products that a customer has ordered, find categories and update our index
     *
     * Accepts resource iterator of products and customer_id
     * @param $args
     *
     * @return $this
     * @throws Exception
     */
    public function getProductCategoryIds($args)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = Mage::getModel('catalog/product');
        $product->setData($args['row']);

        $categoryIds = $product->getCategoryIds();

        if (empty($categoryIds)) {
            return;
        }

        $config = $this->getConfig();

        $score = $config->getOrderWeighting()/ count($categoryIds);
        foreach ($categoryIds as $categoryId) {
            /** @var Meanbee_PersonalisedContent_Model_Resource_Customer_Categories_Collection $customerCategories */
            $customerCategories = Mage::getModel('meanbee_personalisedcontent/customer_categories')->getCollection()
                ->addFieldToFilter('customer_id', $args['customer_id'])
                ->addFieldToFilter('category_id', $categoryId)
                ->setPageSize(1)
                ->setCurPage(1);

            if ($customerCategories->count()) {
                /** @var Meanbee_PersonalisedContent_Model_Customer_Categories $customerCategory */
                $customerCategory = $customerCategories->getFirstItem();
                $customerCategory->setData('score', $customerCategory->getData('score') + $score);
            } else {
                $customerCategory = Mage::getModel('meanbee_personalisedcontent/customer_categories');
                $customerCategory->setData(array(
                    'customer_id' => $args['customer_id'],
                    'category_id' => $categoryId,
                    'score'       => $score
                ));
            }

            $customerCategory->save();
        }
    }

    /**
     * Find products viewed
     *
     * Accepts resource iterator of products
     * @param $args
     */
    public function getProductsForView($args)
    {
        /** @var Mage_Reports_Model_Product_Index_Viewed $order */
        $view = Mage::getModel('reports/product_index_viewed');
        $view->setData($args['row']);

        /** @var Mage_Catalog_Model_Product $products */
        $product = Mage::getModel('catalog/product')->load($view->getProductId());
        $product->getCategoryIds();

        $categoryIds = $product->getCategoryIds();

        if (empty($categoryIds)) {
            return;
        }

        $config = $this->getConfig();

        $score = $config->getPageviewWeighting() / count($categoryIds);
        foreach ($categoryIds as $categoryId) {
            /** @var Meanbee_PersonalisedContent_Model_Resource_Customer_Categories_Collection $customerCategories */
            $customerCategories = Mage::getModel('meanbee_personalisedcontent/customer_categories')->getCollection()
                ->addFieldToFilter('customer_id', $view->getCustomerId())
                ->addFieldToFilter('category_id', $categoryId)
                ->setPageSize(1)
                ->setCurPage(1);

            if ($customerCategories->count()) {
                /** @var Meanbee_PersonalisedContent_Model_Customer_Categories $customerCategory */
                $customerCategory = $customerCategories->getFirstItem();
                $customerCategory->setData('score', $customerCategory->getData('score') + $score);
            } else {
                $customerCategory = Mage::getModel('meanbee_personalisedcontent/customer_categories');
                $customerCategory->setData(array(
                    'customer_id' => $view->getCustomerId(),
                    'category_id' => $categoryId,
                    'score'       => $score
                ));
            }

            $customerCategory->save();
        }
    }

    /**
     * @return Meanbee_PersonalisedContent_Helper_Config
     */
    private function getConfig() {
        if (!$this->_config) {
            $this->_config = Mage::helper('meanbee_personalisedcontent/config');
        }

        return $this->_config;
    }
}