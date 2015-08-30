<?php

/**
 * Class Meanbee_PersonalisedContent_Block_Widget_Content
 * @method bool getIsEnabled
 */
class Meanbee_PersonalisedContent_Block_Widget_Content extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface
{

    public function isEnabled()
    {
        return (bool) $this->getData('is_enabled');
    }

    /**
     * @return string
     */
    public function getPersonlisedContentHtml()
    {

        $categories = $this->getPersonlisedCategoryItems(Mage::getSingleton('customer/session')->getCustomer());
        $identifier = $this->getPersonsaliedCmsIdentifier($categories);
        return $this->renderCmsBlock($identifier);
    }

    /**
     * @param $customer
     * @return mixed
     */
    public function getPersonlisedCategoryItems($customer)
    {
        $categories = array();
        $personalisedCustomerCategoryItems = Mage::getModel('meanbee_personalisedcontent/customer_categories')
            ->getCollection()
            ->addFieldToFilter('customer_id', array('eq' => $customer->getId()))
            ->setOrder('score', 'desc')
            ->setPageSize(5)
            ->setCurPage(1);

        // Fallback on a default if nothing is found.
        if($personalisedCustomerCategoryItems->count() == 0) {
            return array($this->getDefaultCategory());
        }

        foreach($personalisedCustomerCategoryItems as $item) {
            $categories[] = $item->getCategoryId();
        }

        return $categories;
    }

    /**
     * @param $categories
     * @return mixed
     */
    public function getPersonsaliedCmsIdentifier($categories)
    {
        $adapter = Mage::getSingleton('core/resource')->getConnection('core_read');
        $select  = $adapter->select()
            ->from('meanbee_personalisedcontent_cms_block_personalisation', 'block_id')
            ->where('category_id IN (?)', $categories)
            ->limitPage(1, 1);

        $data = $adapter->fetchCol($select);

        if(isset($data[0])) {
            return $data[0];
        }

        return '';
    }

    /**
     * @param $identifier
     * @return string
     */
    public function renderCmsBlock($identifier)
    {
        /** @var Mage_Cms_Block_Block $block */
        $block = Mage::getBlockSingleton('cms/block')->setBlockId($identifier);
        return $block->toHtml();
    }

    protected function _toHtml()
    {
        return $this->getPersonlisedContentHtml();
    }

}
