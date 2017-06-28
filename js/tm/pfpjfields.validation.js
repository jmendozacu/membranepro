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
        '01':   'Alba', '02': 'Arad', '03': 'Argeş', '04': 'Bacău', '05': 'Bihor', '06': 'Bistriţa-Năsăud', '07': 'Botoşani', '08': 'Braşov', '09': 'Brăila',
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

Validation.add('validate-termeni-conditii', 'Acesta este un camp cerut.', function(v, elm) {
    
    if (v != 1) {
        console.log(v);
        return false;
    } else {
        return true;
    }
    
    
});






