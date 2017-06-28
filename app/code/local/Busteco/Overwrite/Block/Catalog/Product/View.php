<?php
class Busteco_Overwrite_Block_Catalog_Product_View extends Mage_Catalog_Block_Product_View
{
    /**
     * get a human readable file size
     * @return string
     */
    public function formatFilesize($bytes, $decimals = 2)
    {
        $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
    }
    
    /**
     * get attributes details from a specified set/info
     * @return object
     */
    public function getProductAttributesBySet($_product, $attrSet = false, $attrInfo = false)
    {
        if (!$attrSet && !$attrInfo) {
            return Mage::getResourceSingleton('catalog/product')->loadAllAttributes()->getAttributesByCode();
        } else {
            $productAttributes = Mage::getResourceSingleton('catalog/product')->loadAllAttributes()->getAttributesByCode();
            
            $i = 0;
            foreach ($productAttributes as $attribute) {
                if ($attribute['attribute_set_info'][$attrSet]['group_id'] == $attrInfo) {
                    $attributeText = $_product->getAttributeText($attribute->getAttributeCode());
					
					$attributes[$i]['code'] = $attribute->getAttributeCode();
					$attributes[$i]['title'] = $attribute->getStoreLabel();
                    
                    if ($attributeText) {
                        if (is_array($attributeText)) {
                            $attributeText = implode(', ', $attributeText);
                        }
                        
                        $attributes[$i]['value'] = $attributeText;
                    } else {
                        $attributes[$i]['value'] = $_product->getData($attribute->getAttributeCode());
                    }
					$i++;
                }
            }
        }
        
        return $attributes;
    }
}


