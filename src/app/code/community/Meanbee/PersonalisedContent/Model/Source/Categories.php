<?php

class Meanbee_PersonalisedContent_Model_Source_Categories
{

    public function toArray() {
        $options = $this->toOptionArray();
        $categories = array();
        foreach($options as $option) {
            $categories[$option['value']] = $option['label'];
        }

        return $categories;
    }

    public function toOptionArray() {
        $options = array();

        $collection = Mage::getResourceModel('catalog/category_collection');

        $collection->addAttributeToSelect('name')
            ->addRootLevelFilter()
            ->load();

        foreach ($collection as $category) {
            $options = array_merge($options, $this->_getCategoryChildrenAsOptionArray($category));
        }

        return $options;
    }

    protected function _getCategoryChildrenAsOptionArray($category, $indent = '') {
        $options = array(
            array(
                'label' => $indent . ' '. $category->getName(),
                'value' => $category->getId()
            )
        );

        if ($category->hasChildren()) {
            foreach ($category->getChildrenCategories() as $child) {
                $options = array_merge($options, $this->_getCategoryChildrenAsOptionArray($child, $indent . '--'));
            }
        }

        return $options;
    }
}
