<?php
/**
 * MageWorx
 * MageWorx SeoMarkup Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoMarkup
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */


class MageWorx_SeoMarkup_Model_Observer_Breadcrumbs
{
    public function createRichsnippetBreadcrumbsMarkup($observer)
    {
        if (!Mage::helper('mageworx_seomarkup/config')->isBreadcrumbsRichsnippetEnabled()) {
            return false;
        }

        if (!Mage::helper('mageworx_seomarkup/config')->isBreadcrumbsInjectionMicrodataMethod()) {
            return false;
        }

        $block = $observer->getBlock();

//        <div class="breadcrumbs" >
//        <ul itemscope itemtype="http://schema.org/BreadcrumbList">
//        <li class="home" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
//        <a itemprop="item" href="http://magento205.local/default/" title="Go to Home Page" ><meta itemprop="name" content = 'Home'>Home</a>

        if ($this->_getHandlerForBlock($block->getNameInLayout())) {

            $transport    = $observer->getTransport();
            $normalOutput = $observer->getTransport()->getHtml();
            $modelUri     = $this->_getHandlerForBlock($block->getNameInLayout());

            $model        = Mage::getModel($modelUri);

            $modifyOutput = $model->render($normalOutput, $block, true);
            if ($modifyOutput) {
                $transport->setHtml($modifyOutput);
            }
        }
        return $this;
    }
   
    /**
     * @param type $blockName
     * @return mixed
     */
    protected function _getHandlerForBlock($blockName)
    {
        $handlers = array(
            'breadcrumbs'   => 'mageworx_seomarkup/richsnippet_catalog_product_breadcrumbs',
        );

        if (!empty($handlers[$blockName])) {
            return $handlers[$blockName];
        }
        return null;
    }
}
