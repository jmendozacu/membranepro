/**
 * Daniel Ifrim
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   PfpjRom
 * @package    Js
 * @copyright  Copyright (c) Daniel Ifrim
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @author     Daniel Ifrim
 */
if(!PfpjRom) var PfpjRom = {};

PfpjRom.AddressTippersBehaviour = Class.create();
PfpjRom.AddressTippersBehaviour.prototype = {
    initialize : function(tippers_value, state, config, levels_to_paret, is_tippers_required, enable_obj, primary_billing_id, primary_shipping_id, is_default_billing, is_default_shipping) {
    	this.enableObj = (enable_obj == true || enable_obj == null ? true : false);
    	this.config = config || {};
    	is_tippers_required = (is_tippers_required == true || enable_obj == null ? true : false);
    	this.triggerBaseId = (this.config.trigger || null);
    	this.requiredHintPath = (this.config.required_hint_path || "span.required");
    	this.optionsConfig = (this.config.options || {});
    	this.fieldsConfig = (this.config.fields || {});
    	this.requiredClass = (this.config.required_class || "required-entry");
    	this.state = state; // billing, shipping or all

    	this.primaryBilling = (primary_billing_id != null && $(primary_billing_id) != undefined ? $(primary_billing_id) : null);
    	this.primaryShipping = (primary_shipping_id != null && $(primary_shipping_id) != undefined ? $(primary_shipping_id) : null);

    	this.isDefaultBilling = (is_default_billing == true ? true : false);
    	this.isDefaultShipping = (is_default_shipping == true ? true : false);

    	if (!is_tippers_required) {
    		for(var fieldName in this.fieldsConfig) {
    			if (fieldName.indexOf('pfpj_tip_pers') != -1) {
    				for(var fieldOption in this.fieldsConfig[fieldName]) {
    					if (this.state == 'all' || this.state == 'billing')
    						this.fieldsConfig[fieldName][fieldOption]['billing'].required = false;
    					if (this.state == 'all' || this.state == 'shipping')
    						this.fieldsConfig[fieldName][fieldOption]['shipping'].required = false;
    				}
    				break;
    			}
    		}
    	}

    	this.tippersValue = (!(tippers_value == "" || tippers_value == undefined)  ? tippers_value : this.config.default_option);

    	this.levelsToParet = (levels_to_paret != undefined ? levels_to_paret : 1);

    	this.enableObject(this, this.enableObj);
    },
    enableObject : function(addr, enableObj) {
    	if (!enableObj) {
    		addr.enableObj = false;
    		return false;
    	}
    	addr.enableObj = enableObj;
    	addr.fields = {};
    	for(var fieldName in addr.fieldsConfig) {
    		if ($(fieldName)) {
    			addr.fields[fieldName] = $(fieldName);
    		}
    	}

    	addr.options = {};
    	for(var optionValue in addr.optionsConfig)
    		if ($(addr.triggerBaseId + optionValue))
    			addr.options[optionValue] = $(addr.triggerBaseId + optionValue);

    	for(var option in addr.options)
    		Event.observe(addr.options[option],'click',addr.eventListenerTippers.bindAsEventListener(addr, addr));

    	if (addr.state == 'all' || addr.state == 'billing') {
    		Event.observe(addr.fields[addr.getFieldName(addr, 'pfpj_for_billing')],'change',addr.eventListenerStateFields.bindAsEventListener(addr, addr));
    	}

    	if (addr.state == 'all' || addr.state == 'shipping') {
    		Event.observe(addr.fields[addr.getFieldName(addr, 'pfpj_for_shipping')],'change',addr.eventListenerStateFields.bindAsEventListener(addr, addr));
    	}

    	if (addr.primaryBilling != null)
    		Event.observe(addr.primaryBilling,'change',addr.eventListenerPrimaryBilling.bindAsEventListener(addr, addr));
    	if (addr.primaryShipping != null)
    		Event.observe(addr.primaryShipping,'change',addr.eventListenerPrimaryShipping.bindAsEventListener(addr, addr));

    	addr.initObject(addr);

    	return true;
    },
    initObject : function(addr) {
    	addr.switchOptions(addr);
    },
    setTippersValue : function (addr, v) {
    	addr.tippersValue = v;
    },
    setStateDefaultValue : function(addr, name, state) {
    	var _name = addr.getFieldName(addr, name);
    	if (addr.isDefaultBilling && _name == 'pfpj_for_billing') {
    		addr.fields[_name].value = 1;
    		addr.fields[_name].checked = true;
    	} else if (addr.isDefaultShipping && _name == 'pfpj_for_shipping') {
    		addr.fields[_name].value = 1;
    		addr.fields[_name].checked = true;
    	} else {
    		addr.fields[_name].value = addr.getDefaultValue(addr, _name, addr.tippersValue, state);
    		addr.fields[_name].checked = (addr.fields[_name].value == 1 ? true : false);
    	}
    },
    eventListenerTippers : function(e, addr) {
    	addr.setTippersValue(addr, Event.element(e).value);
    	if (addr.state == 'all' || addr.state == 'billing')
    		addr.setStateDefaultValue(addr, 'pfpj_for_billing', addr.state);
    	if (addr.state == 'all' || addr.state == 'shipping')
    		addr.setStateDefaultValue(addr, 'pfpj_for_shipping', addr.state);
    	addr.switchOptions(addr);
    },
    eventListenerStateFields : function(e, addr) {
    	var el = Event.element(e);
    	for(var optionValue in addr.options) {
			if (addr.options[optionValue].checked == true) {
				addr.setTippersValue(addr, optionValue);
			}
		}

		addr.setStateFieldsByPrimary(addr, el);
    	addr.switchOptions(addr);
    },
    setStateFieldsByPrimary : function (addr, el) {
    	if (el.id == addr.getFieldName(addr, 'pfpj_for_billing')) {
			if (addr.primaryBilling !== null) {
				if (addr.primaryBilling.checked)
					addr.isDefaultBilling = true;
				else
					addr.isDefaultBilling = false;
			}
    		if (addr.isDefaultBilling) {
				el.checked = true;
    			el.value = 1;
    		}
		}
		if (el.id == addr.getFieldName(addr, 'pfpj_for_billing') && addr.primaryBilling != null) {
			el.value = (el.checked ? 1 : 0);
			if (!el.checked && addr.primaryBilling.checked)
				addr.primaryBilling.checked = false;
		}

		if (el.id == addr.getFieldName(addr, 'pfpj_for_shipping')) {
			if (addr.primaryShipping !== null) {
				if (addr.primaryShipping.checked) {
					addr.isDefaultShipping = true;
				} else {
					addr.isDefaultShipping = false;
				}
			}
			if (addr.isDefaultShipping) {
    			el.checked = true;
    			el.value = 1;
			}
		}
		if (el.id == addr.getFieldName(addr, 'pfpj_for_shipping') && addr.primaryShipping != null) {
			el.value = (el.checked ? 1 : 0);
			if (!el.checked && addr.primaryShipping.checked)
				addr.primaryShipping.checked = false;
		}
    },
    eventListenerPrimaryBilling : function(e, addr) {
    	if (Event.element(e).checked) {
    		var billingName = addr.getFieldName(addr, 'pfpj_for_billing');
    		addr.fields[billingName].checked = true;
    		addr.fields[billingName].value = 1;
    		addr.isDefaultBilling = true;
    		//Event.observe(addr.fields[addr.getFieldName(addr, 'pfpj_for_billing')],'change',addr.eventListenerCheckFieldState.bindAsEventListener(addr, addr));
    	} else {
    		addr.isDefaultBilling = false;
    		//addr.fields[addr.getFieldName(addr, 'pfpj_for_billing')].stopObserving('change', addr.eventListenerCheckFieldState);
    	}
    	addr.eventListenerStateFields(e, addr);
    },
    eventListenerPrimaryShipping : function(e, addr) {
    	if (Event.element(e).checked) {
    		var shippingName = addr.getFieldName(addr, 'pfpj_for_shipping');
    		addr.fields[shippingName].checked = true;
    		addr.fields[shippingName].value = 1;
    		addr.isDefaultShipping = true;
    		//Event.observe(addr.fields[addr.getFieldName(addr, 'pfpj_for_shipping')],'change',addr.eventListenerCheckFieldState.bindAsEventListener(addr, addr));
    	} else {
    		addr.isDefaultShipping = false;
    		//addr.fields[addr.getFieldName(addr, 'pfpj_for_shipping')].stopObserving('change', addr.eventListenerCheckFieldState);
    	}
    	addr.eventListenerStateFields(e, addr);
    },
    getFieldName : function (addr, name) {
    	for(var fieldName in addr.fields) {
    		if (fieldName.indexOf(name) != -1)
    			return fieldName;
    	}
    	return null;
    },
    getIsForBilling : function (addr) {
    	var billingEl = addr.fields[addr.getFieldName(addr, 'pfpj_for_billing')];
    	if (billingEl.checked == true)
    		return true;
    	return false;
    },
    getIsForShipping : function (addr) {
    	var shippingEl = addr.fields[addr.getFieldName(addr, 'pfpj_for_shipping')];
    	if (shippingEl.checked == true)
    		return true;
    	return false;
    },
    isShowField : function (addr, fieldName, tippers_value, state, check_state_field) {
    	var ret;
    	if (state == 'billing') {
    		ret = ((check_state_field && addr.getIsForBilling(addr)) || !check_state_field) && addr.fieldsConfig[fieldName][tippers_value][state].show;
    	} else if (state == 'shipping') {
    		ret = ((check_state_field && addr.getIsForShipping(addr)) || !check_state_field) && addr.fieldsConfig[fieldName][tippers_value][state].show;
    	} else {
    		ret = ((check_state_field && addr.getIsForBilling(addr)) || !check_state_field) && addr.fieldsConfig[fieldName][tippers_value]['billing'].show;
    		ret = ret || (((check_state_field && addr.getIsForShipping(addr)) || !check_state_field) && addr.fieldsConfig[fieldName][tippers_value]['shipping'].show);
    	}
    	return ret;
    },
    isRequiredField : function (addr, fieldName, tippers_value, state, check_state_field) {
    	var ret;
    	if (state == 'billing') {
    		ret = ((check_state_field && addr.getIsForBilling(addr)) || !check_state_field) && addr.fieldsConfig[fieldName][tippers_value][state].required;
    	} else if (state == 'shipping') {
    		ret = ((check_state_field && addr.getIsForShipping(addr)) || !check_state_field) && addr.fieldsConfig[fieldName][tippers_value][state].required;
    	} else {
    		ret = ((check_state_field && addr.getIsForBilling(addr)) || !check_state_field) && addr.fieldsConfig[fieldName][tippers_value]['billing'].required;
    		ret = ret || (((check_state_field && addr.getIsForShipping(addr)) || !check_state_field) && addr.fieldsConfig[fieldName][tippers_value]['shipping'].required);
    	}
    	return ret;
    },
    getDefaultValue : function(addr, fieldName, tippers_value, state) {
    	var defaultValue;
    	if (state != 'all') {
    		defaultValue = addr.fieldsConfig[fieldName][tippers_value][state].defaultValue;
    	} else {
    		defaultValue = (addr.fieldsConfig[fieldName][tippers_value]['billing'].defaultValue == 1 ||
    						addr.fieldsConfig[fieldName][tippers_value]['shipping'].defaultValue == 1 ? 1 : 0);
    	}
    	return defaultValue;
    },
    switchOptions : function(addr) {
    	var billingName = addr.getFieldName(addr, 'pfpj_for_billing');
    	var shippingName = addr.getFieldName(addr, 'pfpj_for_shipping');

    	if (addr.state == 'all' || addr.state == 'billing')
    		addr.setStateFieldsByPrimary(addr, addr.fields[billingName]);
    	if (addr.state == 'all' || addr.state == 'shipping')
			addr.setStateFieldsByPrimary(addr, addr.fields[shippingName]);

    	for(var fieldName in addr.fields) {
    		if (billingName != fieldName && shippingName != fieldName) {
	    		if (addr.isShowField(addr, fieldName, addr.tippersValue, addr.state, true)) {
	    			addr.fields[fieldName].up(addr.levelsToParet).show();
	    		} else {
	    			addr.fields[fieldName].up(addr.levelsToParet).hide();
	    			addr.fields[fieldName].value = "";
	    		}
    		}
    	}

    	if (addr.state == 'all' || addr.state == 'billing') {
	    	if (addr.isShowField(addr, billingName, addr.tippersValue, addr.state, false)) {
	    		addr.fields[billingName].up(addr.levelsToParet).show();
	    	} else {
	    		addr.fields[billingName].up(addr.levelsToParet).hide();
	    		addr.setStateDefaultValue(addr, 'pfpj_for_billing', addr.state);
	    	}
    	}

    	if (addr.state == 'all' || addr.state == 'shipping') {
	    	if (addr.isShowField(addr, shippingName, addr.tippersValue, addr.state, false)) {
	    		addr.fields[shippingName].up(addr.levelsToParet).show();
	    	} else {
	    		addr.fields[shippingName].up(addr.levelsToParet).hide();
	    		addr.setStateDefaultValue(addr, 'pfpj_for_shipping', addr.state);
	    	}
    	}

    	for(var fieldName in addr.fields) {
    		if (billingName != fieldName && shippingName != fieldName) {
    			var spanReqEl = addr.fields[fieldName].up(addr.levelsToParet).down(addr.requiredHintPath);
	    		if (addr.isRequiredField(addr, fieldName, addr.tippersValue, addr.state, true)) {
	    			addr.fields[fieldName].removeClassName(addr.requiredClass);
	    			addr.fields[fieldName].addClassName(addr.requiredClass);
	    			if (spanReqEl != undefined)
	    				spanReqEl.show();
	    		} else {
	    			addr.fields[fieldName].removeClassName(addr.requiredClass);
	    			if (spanReqEl != undefined)
	    				spanReqEl.hide();
	    		}
    		}
    	}

    	/*if (addr.isRequiredField(addr, billingName, addr.tippersValue, addr.state, false)) {
    		addr.fields[billingName].removeClassName(addr.requiredClass);
	    	addr.fields[billingName].addClassName(addr.requiredClass);
    	} else {
    		addr.fields[billingName].removeClassName(addr.requiredClass);
    	}

    	if (addr.isRequiredField(addr, shippingName, addr.tippersValue, addr.state, false)) {
    		addr.fields[shippingName].removeClassName(addr.requiredClass);
	    	addr.fields[shippingName].addClassName(addr.requiredClass);
    	} else {
    		addr.fields[shippingName].removeClassName(addr.requiredClass);
    	}*/

    },
    mutateFieldsNames : function(addr, prefix_new, prefix_old, suffix_new, suffix_old) {
    	var newFieldsConfig = {};
    	for(var fieldName in addr.fieldsConfig) {
    		var newFieldName = addr._mutateName(fieldName, prefix_new, prefix_old, suffix_new, suffix_old);;
    		newFieldsConfig[newFieldName] = addr.fieldsConfig[fieldName];
    	}

    	addr.fieldsConfig = newFieldsConfig;
    	addr.triggerBaseId = addr._mutateName(addr.triggerBaseId, prefix_new, prefix_old, suffix_new, suffix_old);
    },
    mutatePrimaryNames : function(addr, prefix_new, prefix_old, suffix_new, suffix_old) {
    	var primary_billing_id;
		var primary_shipping_id;

		if (addr.primaryBilling != null) {
			primary_billing_id = addr._mutateName(addr.primaryBilling.id, prefix_new, prefix_old, suffix_new, suffix_old);
			addr.primaryBilling = ($(primary_billing_id) != undefined ? $(primary_billing_id) : null);
		}

		if (addr.primaryShipping != null) {
			primary_shipping_id = addr._mutateName(addr.primaryShipping.id, prefix_new, prefix_old, suffix_new, suffix_old);
			addr.primaryShipping = ($(primary_shipping_id) != undefined ? $(primary_shipping_id) : null);
		}

		/*addr.primaryBilling = ($(primary_billing_id) != undefined ? $(primary_billing_id) : null);
    	addr.primaryShipping = ($(primary_shipping_id) != undefined ? $(primary_shipping_id) : null);*/

    },
    _mutateName : function (fieldName, prefix_new, prefix_old, suffix_new, suffix_old) {
    	var newFieldName = fieldName;
    	if (!((prefix_new == "" || prefix_new == null) && (prefix_old == "" || prefix_old == null))) {
    		if (!(prefix_old == "" || prefix_old == null)) {
    			if (newFieldName.indexOf(prefix_old) == 0) {
    				newFieldName = prefix_new + newFieldName.substr(prefix_old.length);
    			}
    		} else {
    			newFieldName = prefix_new + newFieldName;
    		}
		}

		if (!((suffix_new == "" || suffix_new == null) && (suffix_old == "" || suffix_old == null))) {
    		if (!(suffix_old == "" || suffix_old == null)) {
    			if (newFieldName.indexOf(suffix_old) > -1 && newFieldName.indexOf(suffix_old) == newFieldName.length - suffix_old.length) {
    				newFieldName = newFieldName.substr(0, newFieldName.indexOf(suffix_old)) + suffix_new;
    			}
    		} else {
    			newFieldName = newFieldName + suffix_new;
    		}
		}

    	return newFieldName;
    },
    setFieldsValues : function (addr, addr_source, prefix_source, prefix_target, suffix_source, sufix_target) {
    	for(fieldName in addr.fields) {
    		var field_source;
    		if(addr.fields[fieldName]) {
    			field_source = $(addr._mutateName(fieldName, prefix_source, prefix_target, suffix_source, sufix_target));
    			if (field_source)
    				addr.fields[fieldName].value = field_source.value;
    		}
    	}
		for(var optionValue in addr.options) {
			addr.options[optionValue].checked = addr_source.options[optionValue].checked;
			if (addr.options[optionValue].checked == true) {
				addr.setTippersValue(addr, optionValue);
			}
		}
		/*if (addr.state == 'all' || addr.state == 'billing')
    		addr.setStateDefaultValue(addr, 'pfpj_for_billing', addr.state);
    	if (addr.state == 'all' || addr.state == 'shipping')
    		addr.setStateDefaultValue(addr, 'pfpj_for_shipping', addr.state);*/

		/*if (addr.state == 'billing') {
			var shippingName = addr.getFieldName(addr, 'pfpj_for_shipping');
			addr.fields[shippingName].checked = false;
			addr.fields[shippingName].value = 0;
		}

		if (addr.state == 'shipping') {
			var billingName = addr.getFieldName(addr, 'pfpj_for_billing');
			addr.fields[billingName].checked = false;
			addr.fields[billingName].value = 0;
		}*/

		addr.switchOptions(addr);
    },
    eventListenerSetFieldsValues : function (e, addr, addr_source, prefix_source, prefix_target, suffix_source, sufix_target) {
    	var trigger_el;
    	trigger_el = Event.element(e);
		if (trigger_el.checked) {
	    	addr.setFieldsValues(addr, addr_source, prefix_source, prefix_target, suffix_source, sufix_target);
		}
    },
    syncWithBilling : function (addr, addr_source, trigger, prefix_source, prefix_target, suffix_source, sufix_target) {
    	if ($(trigger)) {
    		addr.triggerSyncWithBilling = $(trigger);
    		Event.observe(addr.triggerSyncWithBilling,'click', addr.eventListenerSetFieldsValues.bindAsEventListener(addr, addr, addr_source, prefix_source, prefix_target, suffix_source, sufix_target));
    	}
    },
    eventListenerCheckFieldState : function(e, addr) {
    	var el = Event.element(e);
    	el.checked = true;
    	el.value = 1;
    	//Event.stop(e);
    }
}

// @TO DO: working validation messages with values replaced in it and use var Translator.
if(Validation) {

/**
 * Romanian CNP (Personal Identification Code | Cod Numeric Personal) validator:
 *  - the input must have 13 digits;
 *  - format: SYYMMDDRROOOC - S=sex(1-9) YY=year(00-99) MM=month(01-12) DD=day(01-31) OOO=ord number for the person(001-999) C=CRC number(0-9)
 *
 * Info about CNP:
 *
 * RO law: http://www.cdep.ro/pls/legis/legis_pck.htp_act_text?idt=4095
 * wiki: http://ro.wikipedia.org/wiki/Cod_numeric_personal
 * description of algorithm: http://www.validari.ro/cnp
 *
 */
Validation.add('validate-pfpj-cnp', 'CNP invalid.', function(v, elm) {
	if(Validation.get('IsEmpty').test(v)) return true;

	if (v.length != 13)
	    return false;

	var regex = /^([0-9])([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{3})([0-9])$/;
	var patt = new RegExp(regex);
	var matches = patt.exec(v);
	if(!matches)
	    return false;

	var sex = matches[1];
	var year = matches[2];
	var month = matches[3];
	var day = matches[4];
	var regionCode = matches[5];
	var ord = matches[6];
	var crc = matches[7];

	if (sex <= 0)
		return false;

	var validateDate = true;
	var yPrefix = "";
	if (sex == 1 || sex == 2)
		yPrefix = "19";
	else if (sex == 3 || sex == 4)
		yPrefix = "18";
	else if (sex == 5 || sex == 6)
		yPrefix = "20";
	else if (sex == 7 || sex == 8 || sex == 9)
		validateDate = false;

	if (month <= 0 || month > 12)
		return false;

	if (day <= 0 || day > 31)
		return false;

	if (validateDate) {
		var testDate = new Date(parseInt(yPrefix + year, 10), parseInt(month, 10) - 1, parseInt(day, 10), 0, 0, 0);

		if ((testDate.getFullYear() != parseInt(yPrefix + year, 10)) || (testDate.getMonth() + 1 != parseInt(month, 10)) || (testDate.getDate() != parseInt(day, 10))) {
			return false;
		} else {
			var today = new Date();
			if (today < testDate) {
				return false;
			}
		}
	}

	var regionsCodes = {
		'01':	'Alba',	'02': 'Arad', '03': 'Argeş', '04': 'Bacău', '05': 'Bihor', '06': 'Bistriţa-Năsăud', '07': 'Botoşani', '08': 'Braşov', '09': 'Brăila',
		'10': 'Buzău', '11': 'Caraş-Severin', '12': 'Cluj', '13': 'Constanţa', '14': 'Covasna', '15': 'Dâmboviţa', '16': 'Dolj', '17': 'Galaţi', '18': 'Gorj',
		'19': 'Harghita', '20': 'Hunedoara', '21': 'Ialomiţa', '22': 'Iaşi', '23': 'Ilfov', '24': 'Maramureş', '25': 'Mehedinţi', '26': 'Mureş', '27': 'Neamţ',
		'28': 'Olt', '29': 'Prahova', '30': 'Satu Mare', '31': 'Sălaj', '32': 'Sibiu', '33': 'Suceava', '34': 'Teleorman', '35': 'Timiş', '36': 'Tulcea',
		'37': 'Vaslui', '38': 'Vâlcea', '39': 'Vrancea', '40': 'Bucureşti', '41': 'Bucureşti S.1', '42': 'Bucureşti S.2', '43': 'Bucureşti S.3', '44': 'Bucureşti S.4',
		'45': 'Bucureşti S.5', '46': 'Bucureşti S.6', '51': 'Călăraşi', '52': 'Giurgiu'
	};

	if (regionsCodes[regionCode] == undefined)
		return false;

	if (ord <= 0)
		return false;

	var tk = '279146358279';
	var s = 0;
	for (var i = 0; i < 12; i++)
		s += v.charAt(i) * tk.charAt(i);
	var c = s % 11;
	if (!(c < 10))
		c = 1;

	if (crc != c) {
		return false;
	}

	return true;
});

/**
 * Romanian CIF (Tax Identification Number | Cod de Identificare Fiscala) validator:
 *  - the input must have 13 digits;
 *  - format: can have as prefix 'RO' or 'RO ' if the fiscal entity pays TVA(VAT) tax.
 *  - format: ZZZZZZZZZC - ZZZZZZZZZ=code digits(max 9 digits in length) C=CRC number(0-9)
 *
 * Info about CIF:
 *
 * http://ro.wikipedia.org/wiki/Cod_fiscal
 * http://ro.wikipedia.org/wiki/Cod_de_identificare_fiscal%C4%83
 * description of algorithm: http://www.validari.ro/cif
 *
 */
Validation.add('validate-pfpj-cif', 'CIF invalid.', function(v, elm) {
	if(Validation.get('IsEmpty').test(v)) return true;

	var prefix = "";
	if (v.indexOf("RO") == 0) {
    	prefix = "RO";
    	v = v.replace(/ro\s*/i, "");
    }

	if (v.length > 10)
	    return false;

	var regex = /^([0-9]{1,9})([0-9])$/;
	var patt = new RegExp(regex);
	var matches = patt.exec(v);
	if(!matches)
	    return false;

	var code = matches[1];
	var crc = matches[2];

	v = v.split("").reverse().join("").substr(1);
	var tk = "753217532".split("").reverse().join("");
	var s = 0;
	for (var i = 0; i < v.length; i++)
		s += v.charAt(i) * tk.charAt(i);
	var c = s * 10 % 11;
	if (!(c < 10))
		c = 0;

	if (crc != c) {
		return false;
	}

	return true;
});

}