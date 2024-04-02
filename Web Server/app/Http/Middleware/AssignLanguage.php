<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class AssignLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response {

        // get language of user's country
        $language = $this->getCountryLanguage($request->country_code);

        # attach the user's language
        $request->merge(['language' => $language]);

        // Check if session key already exists
        if (!Session::has('user_country_code') || !Session::has('user_language_code')) {

            // Set session value
            Session::put('user_country_code', $request->country_code);
            Session::put('user_language_code', $language);
        }

        return $next($request);
    }

    private function getCountryLanguage($countryCode): string {
        // Mapping of ISO country codes to commonly spoken languages
        $countryLanguages = array(
            "AD" => "ca", // Andorra - Catalan
            "AE" => "ar", // United Arab Emirates - Arabic
            "AF" => "fa", // Afghanistan - Persian (Farsi)
            "AG" => "en", // Antigua and Barbuda - English
            "AI" => "en", // Anguilla - English
            "AL" => "sq", // Albania - Albanian
            "AM" => "hy", // Armenia - Armenian
            "AO" => "pt", // Angola - Portuguese
            "AQ" => "en",    // Antarctica - No official language
            "AR" => "es", // Argentina - Spanish
            "AS" => "en", // American Samoa - English
            "AT" => "de", // Austria - German
            "AU" => "en", // Australia - English
            "AW" => "nl", // Aruba - Dutch
            "AX" => "sv", // Åland Islands - Swedish
            "AZ" => "az", // Azerbaijan - Azerbaijani
            "BA" => "bs", // Bosnia and Herzegovina - Bosnian
            "BB" => "en", // Barbados - English
            "BD" => "bn", // Bangladesh - Bengali
            "BE" => "nl", // Belgium - Dutch
            "BF" => "fr", // Burkina Faso - French
            "BG" => "bg", // Bulgaria - Bulgarian
            "BH" => "ar", // Bahrain - Arabic
            "BI" => "fr", // Burundi - French
            "BJ" => "fr", // Benin - French
            "BL" => "fr", // Saint Barthélemy - French
            "BM" => "en", // Bermuda - English
            "BN" => "ms", // Brunei - Malay
            "BO" => "es", // Bolivia - Spanish
            "BQ" => "nl", // Bonaire, Sint Eustatius and Saba - Dutch
            "BR" => "pt", // Brazil - Portuguese
            "BS" => "en", // Bahamas - English
            "BT" => "dz", // Bhutan - Dzongkha
            "BV" => "",    // Bouvet Island - No official language
            "BW" => "en", // Botswana - English
            "BY" => "be", // Belarus - Belarusian
            "BZ" => "en", // Belize - English
            "CA" => "en", // Canada - English
            "CC" => "ms", // Cocos (Keeling) Islands - Malay
            "CD" => "fr", // Democratic Republic of the Congo - French
            "CF" => "fr", // Central African Republic - French
            "CG" => "fr", // Republic of the Congo - French
            "CH" => "de", // Switzerland - German
            "CI" => "fr", // Ivory Coast - French
            "CK" => "en", // Cook Islands - English
            "CL" => "es", // Chile - Spanish
            "CM" => "fr", // Cameroon - French
            "CN" => "zh", // China - Mandarin
            "CO" => "es", // Colombia - Spanish
            "CR" => "es", // Costa Rica - Spanish
            "CU" => "es", // Cuba - Spanish
            "CV" => "pt", // Cape Verde - Portuguese
            "CW" => "nl", // Curaçao - Dutch
            "CX" => "en", // Christmas Island - English
            "CY" => "el", // Cyprus - Greek
            "CZ" => "cs", // Czech Republic - Czech
            "DE" => "de", // Germany - German
            "DJ" => "fr", // Djibouti - French
            "DK" => "da", // Denmark - Danish
            "DM" => "en", // Dominica - English
            "DO" => "es", // Dominican Republic - Spanish
            "DZ" => "ar", // Algeria - Arabic
            "EC" => "es", // Ecuador - Spanish
            "EE" => "et", // Estonia - Estonian
            "EG" => "ar", // Egypt - Arabic
            "EH" => "ar", // Western Sahara - Arabic
            "ER" => "ti", // Eritrea - Tigrinya
            "ES" => "es", // Spain - Spanish
            "ET" => "am", // Ethiopia - Amharic
            "FI" => "fi", // Finland - Finnish
            "FJ" => "en", // Fiji - English
            "FK" => "en", // Falkland Islands - English
            "FM" => "en", // Micronesia - English
            "FO" => "fo", // Faroe Islands - Faroese
            "FR" => "fr", // France - French
            "GA" => "fr", // Gabon - French
            "GB" => "en", // United Kingdom - English
            "GD" => "en", // Grenada - English
            "GE" => "ka", // Georgia - Georgian
            "GF" => "fr", // French Guiana - French
            "GG" => "en", // Guernsey - English
            "GH" => "en", // Ghana - English
            "GI" => "en", // Gibraltar - English
            "GL" => "kl", // Greenland - Greenlandic
            "GM" => "en", // Gambia - English
            "GN" => "fr", // Guinea - French
            "GP" => "fr", // Guadeloupe - French
            "GQ" => "es", // Equatorial Guinea - Spanish
            "GR" => "el", // Greece - Greek
            "GS" => "en", // South Georgia and the South Sandwich Islands - English
            "GT" => "es", // Guatemala - Spanish
            "GU" => "en", // Guam - English
            "GW" => "pt", // Guinea-Bissau - Portuguese
            "GY" => "en", // Guyana - English
            "HK" => "zh", // Hong Kong - Cantonese
            "HM" => "",    // Heard Island and McDonald Islands - No official language
            "HN" => "es", // Honduras - Spanish
            "HR" => "hr", // Croatia - Croatian
            "HT" => "ht", // Haiti - Haitian Creole
            "HU" => "hu", // Hungary - Hungarian
            "ID" => "id", // Indonesia - Indonesian
            "IE" => "en", // Ireland - English
            "IL" => "he", // Israel - Hebrew
            "IM" => "en", // Isle of Man - English
            "IN" => "hi", // India - Hindi
            "IO" => "en", // British Indian Ocean Territory - English
            "IQ" => "ar", // Iraq - Arabic
            "IR" => "fa", // Iran - Persian (Farsi)
            "IS" => "is", // Iceland - Icelandic
            "IT" => "it", // Italy - Italian
            "JE" => "en", // Jersey - English
            "JM" => "en", // Jamaica - English
            "JO" => "ar", // Jordan - Arabic
            "JP" => "ja", // Japan - Japanese
            "KE" => "sw", // Kenya - Swahili
            "KG" => "ky", // Kyrgyzstan - Kyrgyz
            "KH" => "km", // Cambodia - Khmer
            "KI" => "en", // Kiribati - English
            "KM" => "ar", // Comoros - Arabic
            "KN" => "en", // Saint Kitts and Nevis - English
            "KP" => "ko", // North Korea - Korean
            "KR" => "ko", // South Korea - Korean
            "KW" => "ar", // Kuwait - Arabic
            "KY" => "en", // Cayman Islands - English
            "KZ" => "kk", // Kazakhstan - Kazakh
            "LA" => "lo", // Laos - Lao
            "LB" => "ar", // Lebanon - Arabic
            "LC" => "en", // Saint Lucia - English
            "LI" => "de", // Liechtenstein - German
            "LK" => "si", // Sri Lanka - Sinhala
            "LR" => "en", // Liberia - English
            "LS" => "en", // Lesotho - English
            "LT" => "lt", // Lithuania - Lithuanian
            "LU" => "lb", // Luxembourg - Luxembourgish
            "LV" => "lv", // Latvia - Latvian
            "LY" => "ar", // Libya - Arabic
            "MA" => "ar", // Morocco - Arabic
            "MC" => "fr", // Monaco - French
            "MD" => "ro", // Moldova - Romanian
            "ME" => "sr", // Montenegro - Serbian
            "MF" => "fr", // Saint Martin - French
            "MG" => "mg", // Madagascar - Malagasy
            "MH" => "mh", // Marshall Islands - Marshallese
            "MK" => "mk", // North Macedonia - Macedonian
            "ML" => "fr", // Mali - French
            "MM" => "my", // Myanmar - Burmese
            "MN" => "mn", // Mongolia - Mongolian
            "MO" => "zh", // Macau - Cantonese
            "MP" => "ch", // Northern Mariana Islands - Chamorro
            "MQ" => "fr", // Martinique - French
            "MR" => "ar", // Mauritania - Arabic
            "MS" => "en", // Montserrat - English
            "MT" => "mt", // Malta - Maltese
            "MU" => "en", // Mauritius - English
            "MV" => "dv", // Maldives - Dhivehi
            "MW" => "en", // Malawi - English
            "MX" => "es", // Mexico - Spanish
            "MY" => "ms", // Malaysia - Malay
            "MZ" => "pt", // Mozambique - Portuguese
            "NA" => "en", // Namibia - English
            "NC" => "fr", // New Caledonia - French
            "NE" => "fr", // Niger - French
            "NF" => "en", // Norfolk Island - English
            "NG" => "en", // Nigeria - English
            "NI" => "es", // Nicaragua - Spanish
            "NL" => "nl", // Netherlands - Dutch
            "NO" => "no", // Norway - Norwegian
            "NP" => "ne", // Nepal - Nepali
            "NR" => "en", // Nauru - English
            "NU" => "niu", // Niue - Niuean
            "NZ" => "en", // New Zealand - English
            "OM" => "ar", // Oman - Arabic
            "PA" => "es", // Panama - Spanish
            "PE" => "es", // Peru - Spanish
            "PF" => "fr", // French Polynesia - French
            "PG" => "en", // Papua New Guinea - English
            "PH" => "tl", // Philippines - Filipino
            "PK" => "ur", // Pakistan - Urdu
            "PL" => "pl", // Poland - Polish
            "PM" => "fr", // Saint Pierre and Miquelon - French
            "PN" => "en", // Pitcairn Islands - English
            "PR" => "es", // Puerto Rico - Spanish
            "PS" => "ar", // Palestine - Arabic
            "PT" => "pt", // Portugal - Portuguese
            "PW" => "en", // Palau - English
            "PY" => "es", // Paraguay - Spanish
            "QA" => "ar", // Qatar - Arabic
            "RE" => "fr", // Réunion - French
            "RO" => "ro", // Romania - Romanian
            "RS" => "sr", // Serbia - Serbian
            "RU" => "ru", // Russia - Russian
            "RW" => "rw", // Rwanda - Kinyarwanda
            "SA" => "ar", // Saudi Arabia - Arabic
            "SB" => "en", // Solomon Islands - English
            "SC" => "fr", // Seychelles - French
            "SD" => "ar", // Sudan - Arabic
            "SE" => "sv", // Sweden - Swedish
            "SG" => "en", // Singapore - English
            "SH" => "en", // Saint Helena - English
            "SI" => "sl", // Slovenia - Slovenian
            "SJ" => "no", // Svalbard and Jan Mayen - Norwegian
            "SK" => "sk", // Slovakia - Slovak
            "SL" => "en", // Sierra Leone - English
            "SM" => "it", // San Marino - Italian
            "SN" => "fr", // Senegal - French
            "SO" => "so", // Somalia - Somali
            "SR" => "nl", // Suriname - Dutch
            "SS" => "en", // South Sudan - English
            "ST" => "pt", // São Tomé and Príncipe - Portuguese
            "SV" => "es", // El Salvador - Spanish
            "SX" => "nl", // Sint Maarten - Dutch
            "SY" => "ar", // Syria - Arabic
            "SZ" => "en", // Eswatini - English
            "TC" => "en", // Turks and Caicos Islands - English
            "TD" => "fr", // Chad - French
            "TF" => "fr", // French Southern and Antarctic Lands - French
            "TG" => "fr", // Togo - French
            "TH" => "th", // Thailand - Thai
            "TJ" => "tg", // Tajikistan - Tajik
            "TK" => "tkl", // Tokelau - Tokelauan
            "TL" => "pt", // Timor-Leste - Portuguese
            "TM" => "tk", // Turkmenistan - Turkmen
            "TN" => "ar", // Tunisia - Arabic
            "TO" => "to", // Tonga - Tongan
            "TR" => "tr", // Turkey - Turkish
            "TT" => "en", // Trinidad and Tobago - English
            "TV" => "en", // Tuvalu - English
            "TW" => "zh", // Taiwan - Mandarin
            "TZ" => "sw", // Tanzania - Swahili
            "UA" => "uk", // Ukraine - Ukrainian
            "UG" => "en", // Uganda - English
            "UM" => "en", // United States Minor Outlying Islands - English
            "US" => "en", // United States - English
            "UY" => "es", // Uruguay - Spanish
            "UZ" => "uz", // Uzbekistan - Uzbek
            "VA" => "it", // Vatican City - Italian
            "VC" => "en", // Saint Vincent and the Grenadines - English
            "VE" => "es", // Venezuela - Spanish
            "VG" => "en", // British Virgin Islands - English
            "VI" => "en", // United States Virgin Islands - English
            "VN" => "vi", // Vietnam - Vietnamese
            "VU" => "bi", // Vanuatu - Bislama
            "WF" => "fr", // Wallis and Futuna - French
            "WS" => "sm", // Samoa - Samoan
            "XK" => "sq", // Kosovo - Albanian
            "YE" => "ar", // Yemen - Arabic
            "YT" => "fr", // Mayotte - French
            "ZA" => "zu", // South Africa - Zulu
            "ZM" => "en", // Zambia - English
            "ZW" => "en", // Zimbabwe - English
        );

        // Check if the country code is mapped to a language
        if (isset($countryLanguages[$countryCode])) {
            return $countryLanguages[$countryCode];
        } else {
            // If no mapping is found, return English as default
            return "en";
        }
    }
}
