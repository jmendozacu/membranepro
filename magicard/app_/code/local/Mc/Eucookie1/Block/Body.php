<?php
/**
 * @version		$Id$
 * @author		Qubesys Technologies Pvt.Ltd (info@qubesys.com)
 * @category	Mc
 * @package		Mc_Eucookie1
 * @copyright	Copyright (C) 2013 - 2014 Qubesys Technologis Pvt.Ltd. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */



class Mc_Eucookie1_Block_Body extends Mage_Core_Block_Template {

	protected function getConfigData($field, $path = 'general') {
		$full_path = 'eucookie1/' . ($path ? $path . '/' : '') . $field;

		return Mage::getStoreConfig($full_path);
	}
}