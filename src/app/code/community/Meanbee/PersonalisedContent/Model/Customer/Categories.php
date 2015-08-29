<?php
/**
 * RapidCampaign
 *
 * @category    RapidCampaign
 * @package     RapidCampaign_Promotions
 * @copyright   Copyright (c) 2015 RapidCampaign (http://rapidcampaign.com)
 */

/**
 * @method getExpireTime()
 * @method setExpireTime($datetime)
 */
class Meanbee_PersonalisedContent_Model_Customer_Categories extends Mage_Core_Model_Abstract
{
    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('meanbee_personalisedcontent/customer_categories');
    }
}
