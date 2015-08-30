<?php

class Meanbee_PersonalisedContent_Helper_Config extends Mage_Core_Helper_Abstract
{
    const XML_PATH_ENABLED            = 'meanbee_personalisedcontent/general/enabled';
    const XML_PATH_USE_ORDERS         = 'meanbee_personalisedcontent/interactions/use_orders';
    const XML_PATH_USE_PAGEVIEWS      = 'meanbee_personalisedcontent/interactions/use_pageviews';
    const XML_PATH_ORDER_WEIGHTING    = 'meanbee_personalisedcontent/interactions/order_weighting';
    const XML_PATH_PAGEVIEW_WEIGHTING = 'meanbee_personalisedcontent/interactions/pageview_weighting';

    /**
     * @param null $store
     *
     * @return bool
     */
    public function isEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED, $store);
    }

    /**
     * @param null $store
     *
     * @return bool
     */
    public function isUseOrders($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_USE_ORDERS, $store);
    }

    /**
     * @param null $store
     *
     * @return bool
     */
    public function isUsePageviews($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_USE_PAGEVIEWS, $store);
    }

    /**
     * @param null $store
     *
     * @return mixed
     */
    public function getOrderWeighting($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_ORDER_WEIGHTING, $store);
    }

    /**
     * @param null $store
     *
     * @return mixed
     */
    public function getPageviewWeighting($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_PAGEVIEW_WEIGHTING, $store);
    }

}
