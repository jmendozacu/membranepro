<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_USE_PRODUCT_CANONICAL_FOR_REVIEW     = 'mageworx_seo/seosuite/use_product_canonical_for_review';
    const XML_PATH_ALLOW_FILTERS_CANONICAL              = 'mageworx_seo/seosuite/enable_canonical_tag_for_layered_navigation';
    const XML_PATH_CANONICAL_ASSOCIATED_PRODUCT_ENABLED = 'mageworx_seo/seosuite/canonical_associated_product';
    const XML_PATH_CANONICAL_FOR_CONF_PRODUCT           = 'mageworx_seo/seosuite/canonical_configurable';
    const XML_PATH_CANONICAL_FOR_BUNDLE_PRODUCT         = 'mageworx_seo/seosuite/canonical_bundle';
    const XML_PATH_CANONICAL_FOR_GROUPED_PRODUCT        = 'mageworx_seo/seosuite/canonical_grouped';
    const XML_PATH_NOINDEX_FOR_LN_COUNT                 = 'mageworx_seo/seosuite/count_filters_for_noindex';
    const XML_PATH_CANONICAL_USE_LIMIT_ALL              = 'mageworx_seo/seosuite/use_limit_all';


    /**
     * Admin config setting
     */
    const CATEGORY_LN_CANONICAL_OFF          = 0;
    const CATEGORY_LN_CANONICAL_USE_FILTERS  = 1;
    const CATEGORY_LN_CANONICAL_CATEGORY_URL = 2;

    /**
     * Attribut individual setting
     */
    const ATTRIBUTE_LN_CANONICAL_BY_CONFIG    = 0;
    const ATTRIBUTE_LN_CANONICAL_USE_FILTERS  = 1;
    const ATTRIBUTE_LN_CANONICAL_CATEGORY_URL = 2;

    protected $_enterpriseSince113 = null;

    public function showFullActionName()
    {
        return Mage::getStoreConfig('mageworx_seo/tools/show_action_name');
    }

    public function isAssociatedCanonicalEnabled($storeId)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CANONICAL_ASSOCIATED_PRODUCT_ENABLED, $storeId);
    }

    public function isUseLimitAll($storeId = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CANONICAL_USE_LIMIT_ALL, $storeId);
    }

    public function getProductTypeForReplaceCanonical($storeId)
    {
        $types = array();
        switch ('use_parent') {
            case Mage::getStoreConfig(self::XML_PATH_CANONICAL_FOR_CONF_PRODUCT, $storeId):
                $types[] = Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE;
            case Mage::getStoreConfig(self::XML_PATH_CANONICAL_FOR_BUNDLE_PRODUCT, $storeId):
                $types[] = Mage_Catalog_Model_Product_Type::TYPE_BUNDLE;
            case Mage::getStoreConfig(self::XML_PATH_CANONICAL_FOR_GROUPED_PRODUCT, $storeId):
                $types[] = Mage_Catalog_Model_Product_Type::TYPE_GROUPED;
                break;
        }
        return $types;
    }

    /**
     * Retrive filter count for noindex pages
     *
     * @param int|null $storeId
     * @return bool|int
     */
    public function getCountFiltersForNoindex($storeId = null)
    {
        if((string)Mage::getStoreConfig(self::XML_PATH_NOINDEX_FOR_LN_COUNT, $storeId) === ''){
            return false;
        }else{
            return (int)Mage::getStoreConfig(self::XML_PATH_NOINDEX_FOR_LN_COUNT, $storeId);
        }
    }

    public function isProductCanonicalUrlOnReviewPage()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_USE_PRODUCT_CANONICAL_FOR_REVIEW);
    }

    public function isProductPage($fullActionName)
    {
        $product = Mage::registry('current_product');
        if (is_object($product) && $product->getId()) {

            $productActions = array(
                'catalog_product_view',
                'review_product_list',
                'review_product_view',
                'productquestions_show_index',
            );

            if (in_array($fullActionName, $productActions)) {
                return true;
            }
        }
        return false;
    }

    public function isCategoryPage($fullActionName)
    {
        $category = Mage::registry('current_category');
        if (is_object($category) && $category->getId()) {

            $categoryActions = array(
                'catalog_category_view',
            );

            if (in_array($fullActionName, $categoryActions)) {
                return true;
            }
        }
        return false;
    }

    public function isHomePage($fullActionName)
    {
        return ('cms_index_index' == $fullActionName);
    }

    public function isLNFriendlyUrlsEnabled()
    {
        return false;
    }

    /**
     * Determines by global value from a config and to value (based on attributes setting and position)
     * existence of filters in canonical url.
     *
     * @return boolean
     */
    public function isIncludeLNFiltersToCanonicalUrl()
    {
        $enableByConfig  = $this->isIncludeLNFiltersToCanonicalUrlByConfig();
        $answerByFilters = $this->isIncludeLNFiltersToCanonicalUrlByFilters();

        if ($enableByConfig == self::CATEGORY_LN_CANONICAL_USE_FILTERS && $answerByFilters == self::ATTRIBUTE_LN_CANONICAL_CATEGORY_URL) {
            return false;
        }

        if ($enableByConfig == self::CATEGORY_LN_CANONICAL_CATEGORY_URL && $answerByFilters == self::ATTRIBUTE_LN_CANONICAL_USE_FILTERS) {
            return true;
        }
        if ($enableByConfig == self::CATEGORY_LN_CANONICAL_USE_FILTERS) {
            return true;
        }
        return false;
    }

    public function isIncludeLNFiltersToCanonicalUrlByConfig()
    {
        return (int) Mage::getStoreConfig(self::XML_PATH_ALLOW_FILTERS_CANONICAL);
    }

    public function isIncludeLNFiltersToCanonicalUrlByFilters()
    {
        $filtersData = $this->getLayeredNavigationFiltersData();

        if (!$filtersData) {
            return 'default';
        }
        usort($filtersData, array($this, "_cmp"));
        foreach ($filtersData as $data) {
            if (!empty($data['use_in_canonical'])) {
                return $data['use_in_canonical'];
            }
        }
        return false;
    }

    protected function _cmp($a, $b)
    {
        $a['position'] = (empty($a['position'])) ? 0 : $a['position'];
        $b['position'] = (empty($b['position'])) ? 0 : $b['position'];

        if ($a['position'] == $b['position']) {
            return 0;
        }
        return ($a['position'] < $b['position']) ? +1 : -1;
    }

    /**
     * @return bool
     */
    public function applyedLayeredNavigationFilters()
    {
        $appliedFilters = Mage::getSingleton('catalog/layer')->getState()->getFilters();
        return (is_array($appliedFilters) && count($appliedFilters) > 0) ? true : false;
    }

    /**
     * Retrive specific filters data as array (use for canonical url)
     * @return array | false
     */
    public function getLayeredNavigationFiltersData()
    {
        $filterData     = array();
        $appliedFilters = Mage::getSingleton('catalog/layer')->getState()->getFilters();

        if (is_array($appliedFilters) && count($appliedFilters) > 0) {
            foreach ($appliedFilters as $item) {


                if (is_null($item->getFilter()->getData('attribute_model'))) {
                    //Ex: If $item->getFilter()->getRequestVar() == 'cat'
                    $use_in_canonical = 0;
                    $position         = 0;
                }
                else {
                    $use_in_canonical = $item->getFilter()->getAttributeModel()->getLayeredNavigationCanonical();
                    $position         = $item->getFilter()->getAttributeModel()->getPosition();
                }

                $filterData[] = array(
                    'name'             => $item->getName(),
                    'label'            => $item->getLabel(),
                    'code'             => $item->getFilter()->getRequestVar(),
                    'use_in_canonical' => $use_in_canonical,
                    'position'         => $position
                );
            }
        }
        return (count($filterData) > 0) ? $filterData : false;
    }

    public function useSpecificPortInCanonical()
    {
        return Mage::getStoreConfigFlag('mageworx_seo/seosuite/add_canonical_url_port');
    }

    public function isCanonicalUrlEnabled($storeId = null)
    {
        return Mage::getStoreConfigFlag('mageworx_seo/seosuite/enabled', $storeId);
    }

    public function getProductCanonicalType($storeId = null)
    {
        if (!$this->useCategoriesPathInProductUrl($storeId)) {
            return 3;
        }
        return Mage::getStoreConfig('mageworx_seo/seosuite/product_canonical_url', $storeId);
    }

    public function useCategoriesPathInProductUrl($store = null)
    {
        return Mage::getStoreConfigFlag('catalog/seo/product_use_categories', $store);
    }

    public function getTrailingSlashAction($storeId = null)
    {
        return Mage::getStoreConfig('mageworx_seo/seosuite/trailing_slash', $storeId);
    }

    public function cropTrailingSlashForHomePageUrl($storeId = null)
    {
        return Mage::getStoreConfigFlag('mageworx_seo/seosuite/trailing_slash_home_page', $storeId);
    }


    public function trailingSlash($url)
    {
        $trailingSlash = $this->getTrailingSlashAction();

        if ($trailingSlash == 'add') {
            $url        = rtrim($url);
            $extensions = array('rss', 'html', 'htm', 'xml', 'php');
            if (substr($url, -1) != '/' && !in_array(substr(strrchr($url, '.'), 1), $extensions)) {
                $url.= '/';
            }
        }
        elseif ($trailingSlash == 'crop') {
            $url = rtrim(rtrim($url), '/');
        }
        elseif ($trailingSlash == 'default') {

        }
        else {

        }

        return $url;
    }

    public function getRssGenerator()
    {
        return base64_decode('TWFnZVdvcnggU0VPIFN1aXRlIChodHRwOi8vd3d3Lm1hZ2V3b3J4LmNvbS8p');
    }

    public function getAttributeParamDelimiter()
    {
        return Mage::getStoreConfigFlag('mageworx_seo/seosuite/layered_hide_attributes') ? '/' : $this->getAttributeValueDelimiter();
    }

    /**
     * Add Link Rel="next/prev"
     * 0 : No
     * 1 : Yes
     * 2 : Yes, excepting pages with layered navigation
     * @return int
     */
    public function getStatusLinkRel()
    {
        return (int) Mage::getStoreConfig('mageworx_seo/seosuite/enable_link_rel');
    }

    public function getCanonicalUrl($product)
    {
        if (!Mage::getStoreConfig('mageworx_seo/seosuite/enabled')) {
            return;
        }
        $canonicalUrl        = null;
        $productCanonicalUrl = Mage::getStoreConfig('mageworx_seo/seosuite/product_canonical_url');

        $productActions = array(
            'catalog_product_view',
            'review_product_list',
            'review_product_view',
            'productquestions_show_index',
        );

        $useCategories = Mage::getStoreConfigFlag('catalog/seo/product_use_categories');

        $canonicalUrl = $product->getCanonicalUrl();
        $canonicalUrl = trim($canonicalUrl, '/');

        if ($canonicalUrl) {
            $urlRewrite   = Mage::getModel('core/url_rewrite')->setStoreId(Mage::app()->getStore()->getId())->loadByIdPath($canonicalUrl);
            $canonicalUrl = Mage::getUrl('') . $urlRewrite->getRequestPath();
        }
        else {
            $canonicalUrl = $this->getUrlRewriteCanonical($product);

            if (!$canonicalUrl) {
                $canonicalUrl = $product->getProductUrl('mw_false');        # fix recursion
                if (!$canonicalUrl || $productCanonicalUrl == 0) {
                    $product->setDoNotUseCategoryId(!$useCategories);
                    $canonicalUrl = $product->getProductUrl('mw_false'); # fix recursion
                }
            }
        }
        $canonicalUrl = trim($canonicalUrl, '/');
        if ($canonicalUrl) {
            $canonicalUrl = $this->trailingSlash($canonicalUrl);
        }

        // apply crossDomainUrl
        $crossDomainStore = false;
        if ($product->getCanonicalCrossDomain()) {
            $crossDomainStore = $product->getCanonicalCrossDomain();
        }
        elseif (Mage::getStoreConfig('mageworx_seo/seosuite/cross_domain')) {
            $crossDomainStore = Mage::getStoreConfig('mageworx_seo/seosuite/cross_domain');
        }

        if ($crossDomainStore) {
            $url          = Mage::app()->getStore($crossDomainStore)->getBaseUrl();
            $canonicalUrl = str_replace(Mage::getUrl(), $url, $canonicalUrl);
        }
        return $canonicalUrl;
    }

    public function getUrlRewriteCanonical($product)
    {
        $canonicalUrl        = '';
        $productCanonicalUrl = $this->getProductCanonicalType();

        if (Mage::helper('seosuite')->isEnterpriseSince113() && is_object($product)) {
            $canonicalUrl = Mage::getResourceModel('seosuite/core_url_rewrite_ee')->getCanonicalUrl($product);
        }
        else {
            $collection = Mage::getResourceModel('seosuite/core_url_rewrite_collection')
                ->filterAllByProductId($product->getId(), $productCanonicalUrl)
                ->addStoreFilter(Mage::app()->getStore()->getId(), false);

            $urlRewrite = $collection->getFirstItem();
            if ($urlRewrite && $urlRewrite->getRequestPath()) {
                switch ($productCanonicalUrl) {
                    case "1": //Use Longest by url
                    case "2": //Use Shortest  by url
                        $canonicalUrl = $urlRewrite->getRequestPath();
                        break;
                    case "3": // use root
                        foreach ($collection as $urlRewrite) {
                            if (empty($urlRewrite['category_id']) && $urlRewrite->getRequestPath()) {
                                $canonicalUrl = $urlRewrite['request_path'];
                            }
                        }
                        break;
                    case "4": //Use Longest by category
                        $list    = $this->_sortUrlRewriteColletion($collection);
                        $maxItem = array_pop($list);
                        if (is_array($maxItem)) {
                            $canonicalUrl = array_pop($maxItem);
                        }
                        else {
                            $canonicalUrl = $maxItem;
                        }
                        break;
                    case "5": //Use Shortest by category
                        $list    = $this->_sortUrlRewriteColletion($collection);
                        $minItem = array_shift($list);
                        if (is_array($minItem)) {
                            $canonicalUrl = array_shift($minItem);
                        }
                        else {
                            $canonicalUrl = $minItem;
                        }
                        break;
                }
                $canonicalUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK) . $canonicalUrl;
                $canonicalUrl = trim($canonicalUrl, '/');
            }
        }

        return $canonicalUrl;
    }

    private function _sortUrlRewriteColletion($collection)
    {
        $list = array();
        foreach ($collection as $item) {
            $count = count(array_filter(explode('/', $item->getRequestPath())));
            if (!isset($list[$count])) {
                $list[$count] = array();
            }
            $list[$count][strlen($item->getRequestPath())] = $item->getRequestPath();
            ksort($list[$count]);
        }
        if (isset($list[1])) {
            unset($list[1]);
        }
        ksort($list);
        return $list;
    }

    public function getPagerUrlFormat()
    {
        if ($this->isLNFriendlyUrlsEnabled()) {
            $pagerUrlFormat = trim(Mage::getStoreConfig('mageworx_seo/seosuite/pager_url_format'));
            if (strpos($pagerUrlFormat, '[page_number]') !== false) {
                return $pagerUrlFormat;
            }
        }
        return false;
    }

    public function isCompoundProductType($typeId)
    {
        switch ($typeId) {
            case (Mage_Catalog_Model_Product_Type::TYPE_BUNDLE):
                $ret = true;
                break;
            case (Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE):
                $ret = true;
                break;
            case (Mage_Catalog_Model_Product_Type::TYPE_GROUPED):
                $ret = true;
                break;
            case (Mage_Catalog_Model_Product_Type::TYPE_SIMPLE):
                $ret = false;
                break;
            case (Mage_Catalog_Model_Product_Type::TYPE_VIRTUAL):
                $ret = false;
                break;
        }

        return (isset($ret)) ? $ret : null;
    }

    public function getLastCompoundProductByChildProductId($id)
    {
        $ids          = $this->getParentProductIds($id);
        $productTypes = $this->getProductTypeForReplaceCanonical(Mage::app()->getStore()->getStoreId());

        if (count($ids) && count($productTypes)) {
            $visibilityStatuses = array(
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG,
                Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_SEARCH
            );

            $collection = Mage::getModel('catalog/product')->getCollection()
                ->addIdFilter($ids)
                ->addStoreFilter()
                ->addAttributeToFilter('status', array('eq' => 1))
                ->addFieldToFilter('visibility', array('in' => $visibilityStatuses))
                ->addAttributeToFilter('type_id', array('in' => $productTypes))
                ->setOrder('entity_id', 'DESC');

            if ($collection->count()) {
                $product = $collection->getFirstItem();
                return $product;
            }
        }
        return null;
    }

    /**
     * @todo replace to model
     */
    public function getParentProductIds($id)
    {
        $coreResource = Mage::getSingleton('core/resource');
        $conn         = $coreResource->getConnection('core_read');
        $select       = $conn->select()
            ->from($coreResource->getTableName('catalog/product_relation'), array('parent_id'))
            ->where('child_id = ?', $id);

        return $conn->fetchCol($select);
    }

    public function getCurrentFullActionName()
    {
        $controller = Mage::app()->getFrontController();
        if (is_object($controller) && is_callable(array($controller, 'getAction'))) {
            $action = $controller->getAction();
            if (is_object($action) && is_callable(array($action, 'getFullActionName'))) {
                $actionName = $action->getFullActionName();
                if ($actionName) return $actionName;
            }
        }
        return null;
    }

    public function isEnterpriseSince113()
    {
        if (is_null($this->_enterpriseSince113)) {
            $mage = new Mage();
            if (is_callable(array($mage, 'getEdition')) && Mage::getEdition() == Mage::EDITION_ENTERPRISE
                && version_compare(Mage::getVersion(), '1.13.0.0', '>=')) {
                $this->_enterpriseSince113 = true;
            }
            else {
                $this->_enterpriseSince113 = false;
            }
        }
        return $this->_enterpriseSince113;
    }
}