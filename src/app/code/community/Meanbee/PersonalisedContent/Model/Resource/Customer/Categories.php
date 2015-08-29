<?php

class Meanbee_PersonalisedContent_Model_Resouce_Customer_Categories extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('meanbee_personalisedcontent/customer_categories', 'id');
    }
}
