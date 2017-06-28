<?php

/**
 * upgrade made by BGB's team
 */

$installer = $this;
$installer->startSetup();

$attributeCodes = array(
                  'pfpj_tip_pers',
                  'pfpj_cui',
                  'pfpj_reg_com',
                  'pfpj_banca',
                  'pfpj_iban',
                  'pfpj_cnp',
                  'pfpj_serienr_buletin',
                  'pfpj_for_billing',
                  'pfpj_for_shipping',
              );
              
foreach ($attributeCodes as $attr) {
    $attribute       = Mage::getSingleton('eav/config')->getAttribute('customer_address', $attr);
    $newSortOrder    = $attribute->getSortOrder() + 100; 
    
    $used_in_forms   = array();
    $used_in_forms[] = 'customer_register_address';
    //$used_in_forms[] = 'customer_address_edit';
    $used_in_forms[] = 'adminhtml_customer_address';
    
    $attribute->setData('used_in_forms', $used_in_forms)
        ->setData('is_system', false)
        ->setData('is_user_defined', true)
        ->setData('is_visible', true)
        ->setData('sort_order', $newSortOrder);
    
    $attribute->save();
}

$installer->endSetup();