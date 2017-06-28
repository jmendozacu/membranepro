<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoBase_Block_Review_Helper extends MageWorx_SeoBase_Block_Review_Helper_Abstract
{
    public function getReviewsUrl()
    {
        if (Mage::getStoreConfig('mageworx_seo/seosuite/reviews_friendly_urls')) {
            $path = array(
                $this->getProduct()->getUrlKey(),
                'reviews'
            );
            if ($this->getProduct()->getCategoryId()) {
//                $path[] = 'category';
//                $path[] = $this->getProduct()->getCategory()->getUrlKey();
            }
            return Mage::getUrl() . implode('/', $path);
        }
        else {
            return parent::getReviewsUrl();
        }
    }


    public function _toHtml()
    {
        if (Mage::getStoreConfigFlag('advanced/modules_disable_output/Mage_Review')) {
            return '';
        }

        return parent::_toHtml();
    }
}