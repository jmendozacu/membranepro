<?php
/**
 * @version		$Id$
 * @author		Qubesys Technologies Pvt.Ltd (info@qubesys.com)
 * @category	Mc
 * @package		Mc_Eucookie3
 * @copyright	Copyright (C) 2013 - 2014 Qubesys Technologies Pvt.Ltd. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */



class Mc_Eucookie3_Block_Body extends Mage_Core_Block_Template {

	protected function getConfigData($field, $path = 'general') {
		$full_path = 'eucookie3/' . ($path ? $path . '/' : '') . $field;

		return Mage::getStoreConfig($full_path);
	}
}