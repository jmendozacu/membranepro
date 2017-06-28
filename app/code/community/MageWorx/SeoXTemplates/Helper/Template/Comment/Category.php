<?php
/**
 * MageWorx
 * MageWorx SeoXTemplates Extension
 *
 * @category   MageWorx
 * @package    MageWorx_SeoXTemplates
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_SeoXTemplates_Helper_Template_Comment_Category extends MageWorx_SeoXTemplates_Helper_Template_Comment
{
    /**
     * Retrive comment for template edit page
     * @param int $typeId
     * @return string
     * @throws Exception
     */
    public function getComment($typeId)
    {
        $comment = '';
        switch($typeId){
            case MageWorx_SeoXTemplates_Helper_Template_Category::CATEGORY_SEO_NAME:
                $comment = '<p><p><b>Template variables</b><br>[category],[website_name],[categories],[store_name],[store_view_name]';
                break;
            case MageWorx_SeoXTemplates_Helper_Template_Category::CATEGORY_META_TITLE:
                $comment = '<p><p><b>Template variables</b><br>[category],[website_name],[categories],[store_name],[store_view_name]';
                break;
            case MageWorx_SeoXTemplates_Helper_Template_Category::CATEGORY_META_DESCRIPTION:
                $comment = '<p><p><b>Template variables</b><br>[category],[website_name],[parent_category],[categories],[store_name],[store_view_name]';
                break;
            case MageWorx_SeoXTemplates_Helper_Template_Category::CATEGORY_META_KEYWORDS:
                $comment = '<p><p><b>Template variables</b><br>[category],[website_name],[parent_category],[categories],[store_name],[store_view_name]';
                break;
            case MageWorx_SeoXTemplates_Helper_Template_Category::CATEGORY_DESCRIPTION:
                $comment = '<p><p><b>Template variables</b><br>[category],[website_name],[parent_category],[categories],[store_name],[store_view_name]';
                break;
            default:
                throw new Exception($this->__('SEO XTemplates: Unknow Category Template Type'));
                break;
        }
        return $comment;
    }
}