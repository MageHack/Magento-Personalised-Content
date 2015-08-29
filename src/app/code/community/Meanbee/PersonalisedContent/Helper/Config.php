<?php

class Meanbee_PersonalisedContent_Helper_Config extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ENABLED = 'meanbee_personalisedcontent/general/enabled';

    public function isEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED, $store);
    }
}
