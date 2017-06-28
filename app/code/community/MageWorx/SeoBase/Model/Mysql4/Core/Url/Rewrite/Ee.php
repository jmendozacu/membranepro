<?php
/**
 * MageWorx
 * MageWorx SeoBase Extension
 * 
 * @category   MageWorx
 * @package    MageWorx_SeoBase
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoBase_Model_Mysql4_Core_Url_Rewrite_Ee extends Mage_Core_Model_Mysql4_Abstract
{

    protected function _construct()
    {
        $this->_init('catalog/product', 'entity_id');
    }

    public function getCanonicalUrl($product)
    {
        $store = Mage::app()->getStore();
        $storeId = $store->getStoreId();

        if (Mage::helper('seosuite')->isCanonicalUrlEnabled($storeId) &&
            Mage::helper('seosuite')->getProductCanonicalType($storeId))
        {
            $productCanonicalType = Mage::helper('seosuite')->getProductCanonicalType($storeId);

            if(Mage::helper('xsitemap')->isEnterpriseSince113())
            {
                if($productCanonicalType == 1){
                    // 1: Canonical Longest by Path Length
                    $sort = 'DESC';
                    $eeSuffix = "LENGTH(`url`)";
                }elseif($productCanonicalType == 4){
                    // 4: Canonical Longest by Category Counter
                    $sort = 'DESC';
                    $eeSuffix = "char_length(`url`) - char_length(replace(`url`,'/',''))";
                }elseif($productCanonicalType == 2){
                    // 2: Canonical Shortest by Path Length
                    $sort = 'ASC';
                    $eeSuffix = "LENGTH(`url`)";
                }elseif($productCanonicalType == 5){
                    // 5: Canonical Longest by Category Counter
                    $sort = 'ASC';
                    $eeSuffix = "char_length(`url`) - char_length(replace(`url`,'/',''))";
                }
            }
        }

        $urlSuffix = Mage::helper('catalog/product')->getProductUrlSuffix($storeId);

        if($urlSuffix){
            $urlSuffix = '.' . $urlSuffix;
        }else{
            $urlSuffix = '';
        }

        $read = $this->_getReadAdapter();

        $this->_select = $read->select()
            ->distinct()
            ->from(array('e' => $this->getMainTable()), $this->getIdFieldName())
            ->join(
                array('w' => $this->getTable('catalog/product_website')), "e.entity_id=w.product_id", array()
            )
            ->where('w.website_id=?', $store->getWebsiteId());


        $this->_select->where('entity_id = ?', $product->getId());

        if($productCanonicalType == 3){
            $this->_select
                ->joinLeft(
                    array('ecp' => $this->getTable('enterprise_catalog/product')),
                    'ecp.product_id = e.entity_id ' . 'AND ecp.store_id = ' . $storeId,
                    array()
                )
                ->joinLeft(array('euur' => $this->getTable('enterprise_urlrewrite/url_rewrite')),
                    'ecp.url_rewrite_id = euur.url_rewrite_id AND euur.is_system = 1',
                    array()
                )
                ->joinLeft(array('ecp2' => $this->getTable('enterprise_catalog/product')),
                    'ecp2.product_id = e.entity_id AND ecp2.store_id = 0',
                    array()
                )
                ->joinLeft(array('euur2' => $this->getTable('enterprise_urlrewrite/url_rewrite')),
                    'ecp2.url_rewrite_id = euur2.url_rewrite_id',
                    array('url' => 'concat( ' . $this->_getWriteAdapter()->getIfNullSql('euur.request_path', 'euur2.request_path') . ',"' . $urlSuffix . '")')
                );
        }else{
            $this->_select->columns(array('url' => new Zend_Db_Expr("
                    IFNULL(
                        (
                        SELECT CONCAT(rwc.request_path, '/' , rwp.identifier, '{$urlSuffix}') as `url` FROM `{$this->getMainTable()}` AS `e2`
                        LEFT JOIN `{$this->getTable('enterprise_catalog/product')}` AS `ecp` ON ecp.product_id = `e2`.`entity_id` AND `store_id` =
                            IF(
                                (SELECT `ecp2`.`store_id` FROM `{$this->getTable('enterprise_catalog/product')}` AS `ecp2` WHERE `ecp2`.`product_id` = `e2`.`entity_id` AND `store_id` = {$storeId}),
                                (SELECT {$storeId}),
                                (SELECT 0)
                            )
                        JOIN `{$this->getTable('enterprise_urlrewrite/url_rewrite')}` AS `rwp` ON `ecp`.`url_rewrite_id` = `rwp`.`url_rewrite_id`
                        JOIN `{$this->getTable('catalog/category_product')}` AS `ccp` ON `ccp`.`product_id` = `e2`.`entity_id`
                        JOIN `{$this->getTable('enterprise_catalog/category')}` AS `ecc` ON `ecc`.`category_id` = `ccp`.`category_id`
                        JOIN `{$this->getTable('enterprise_urlrewrite/url_rewrite')}` AS `rwc` ON `ecc`.`url_rewrite_id` = `rwc`.`url_rewrite_id`  AND (`rwp`.`entity_type` = 3) AND (`rwc`.`store_id` = {$storeId})
                        WHERE `e2`.`entity_id` = `e`.`entity_id`
                        ORDER BY $eeSuffix $sort LIMIT 1
                        ),

                        (
                        SELECT CONCAT(rwp.identifier, '{$urlSuffix}') AS `url` FROM `{$this->getMainTable()}` AS `e2`
                            LEFT JOIN `{$this->getTable('enterprise_catalog/product')}` AS `ecp` ON ecp.product_id = `e2`.`entity_id` AND `store_id` =
                            IF(
                                (SELECT `ecp2`.`store_id` FROM `{$this->getTable('enterprise_catalog/product')}` AS `ecp2` WHERE `ecp2`.`product_id` = `e2`.`entity_id` AND `store_id` = {$storeId}),
                                (SELECT {$storeId}),
                                (SELECT 0)
                            )
                            JOIN `{$this->getTable('enterprise_urlrewrite/url_rewrite')}` AS `rwp` ON `ecp`.`url_rewrite_id` = `rwp`.`url_rewrite_id`
                            WHERE `e2`.`entity_id` = `e`.`entity_id` LIMIT 1
                        )
                    )
                "
            )));
        }

        $query = $read->query($this->_select);
        $row = $query->fetch();

        if(!empty($row['url'])){
            $productUrl = !empty($row['url']) ? $row['url'] : 'catalog/product/view/id/' . $product->getId();
        }

        $canonicalUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK) . $productUrl;

        return $canonicalUrl;
    }
}