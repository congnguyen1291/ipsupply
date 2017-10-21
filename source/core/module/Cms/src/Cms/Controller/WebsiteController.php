<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 6/23/14
 * Time: 2:07 PM
 */
namespace Cms\Controller;
use Cms\Form\WebsiteForm;
use Cms\Model\Websites;
use Zend\View\Model\ViewModel;
use Cms\Lib\Paging;

use Assetic\AssetManager;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Assetic\Asset\GlobAsset;
use Assetic\Filter\LessFilter;
use Assetic\Filter\Yui;
use Assetic\Factory\AssetFactory;
use Assetic\Util\TraversableString;
use Assetic\Filter\Yui\JsCompressorFilter as YuiCompressorFilter;
use Assetic\Filter\GoogleClosure\CompilerApiFilter;
use Assetic\Filter\GoogleClosure\CompilerJarFilter;
use Assetic\Filter\GssFilter;
use Assetic\Asset\HttpAsset;
use Assetic\FilterManager;
use Assetic\Filter\GoogleClosure\ApiFilter as ClosureFilter;
use Assetic\AssetWriter;
use Assetic\Filter\CssImportFilter;
use Assetic\Filter\CssMinFilter;
use Assetic\Filter\CleanCssFilter;
use Assetic\Filter\CssRewriteFilter;
use Assetic\Filter\JSMinFilter;

class WebsiteController extends BackEndController
{
    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'website';
    }

    private $list_crop =    array( 0=>'Crop hình', 
                                1=>'Đặt hình vào trong khung'
                            );
    private $list_version_cart = array('0'=>'01', '02'=>'02');

    private $list_timezone = array("Dateline Standard Time"=>"(UTC-12:00) International Date Line West",
        "UTC-11"=>"(UTC-11:00) Coordinated Universal Time -11",
        "Hawaiian Standard Time"=>"(UTC-10:00) Hawaii",
        "Alaskan Standard Time"=>"(UTC-09:00) Alaska",
        "Pacific Standard Time"=>"(UTC-08:00) Pacific Time (US and Canada)",
        "Pacific Standard Time (Mexico)"=>"(UTC-08:00)Baja California",
        "Mountain Standard Time"=>"(UTC-07:00) Mountain Time (US and Canada)",
        "Mountain Standard Time (Mexico)"=>"(UTC-07:00) Chihuahua, La Paz, Mazatlan",
        "US Mountain Standard Time"=>"(UTC-07:00) Arizona",
        "Canada Central Standard Time"=>"(UTC-06:00) Saskatchewan",
        "Central America Standard Time"=>"(UTC-06:00) Central America",
        "Central Standard Time"=>"(UTC-06:00) Central Time (US and Canada)",
        "Central Standard Time (Mexico)"=>"((UTC-06:00) Guadalajara, Mexico City, Monterrey",
        "Eastern Standard Time"=>"(UTC-05:00) Eastern Time (US and Canada)",
        "SA Pacific Standard Time"=>"(UTC-05:00) Bogota, Lima, Quito",
        "US Eastern Standard Time"=>"(UTC-05:00) Indiana (East)",
        "Atlantic Standard Time"=>"(UTC-04:00) Atlantic Time (Canada)",
        "Central Brazilian Standard Time"=>"(UTC-04:00) Cuiaba",
        "Pacific SA Standard Time"=>"(UTC-04:00) Santiago",
        "SA Western Standard Time"=>"(UTC-04:00) Georgetown, La Paz, Manaus, San Juan",
        "Venezuela Standard Time"=>"(UTC-04:30) Caracas",
        "Paraguay Standard Time"=>"(UTC-04:00) Asuncion",
        "Newfoundland Standard Time"=>"(UTC-03:30) Newfoundland",
        "E. South America Standard Time"=>"(UTC-03:00) Brasilia",
        "Greenland Standard Time"=>"(UTC-03:00) Greenland",
        "Montevideo Standard Time"=>"(UTC-03:00) Montevideo",
        "SA Eastern Standard Time"=>"(UTC-03:00) Cayenne, Fortaleza",
        "Argentina Standard Time"=>"(UTC-03:00) Buenos Aires",
        "Mid-Atlantic Standard Time"=>"(UTC-02:00) Mid-Atlantic",
        "UTC-2"=>"(UTC-02:00) Coordinated Universal Time -02",
        "Azores Standard Time"=>"(UTC-01:00) Azores",
        "Cabo Verde Standard Time"=>"(UTC-01:00) Cabo Verde Is.",
        "GMT Standard Time"=>"(UTC) Dublin, Edinburgh, Lisbon, London",
        "Greenwich Standard Time"=>"(UTC) Monrovia, Reykjavik",
        "Morocco Standard Time"=>"(UTC) Casablanca",
        "UTC"=>"(UTC) Coordinated Universal Time",
        "Central Europe Standard Time"=>"(UTC+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague",
        "Central European Standard Time"=>"(UTC+01:00) Sarajevo, Skopje, Warsaw, Zagreb",
        "Romance Standard Time"=>"(UTC+01:00) Brussels, Copenhagen, Madrid, Paris",
        "W. Central Africa Standard Time"=>"(UTC+01:00) West Central Africa",
        "W. Europe Standard Time"=>"(UTC+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna",
        "Namibia Standard Time"=>"(UTC+01:00) Windhoek",
        "E. Europe Standard Time"=>"(UTC+02:00) Minsk",
        "Egypt Standard Time"=>"(UTC+02:00) Cairo",
        "FLE Standard Time"=>"(UTC+02:00) Helsinki, Kyiv, Riga, Sofia, Tallinn, Vilnius",
        "GTB Standard Time"=>"(UTC+02:00) Athens, Bucharest",
        "Israel Standard Time"=>"(UTC+02:00) Jerusalem",
        "Jordan Standard Time"=>"(UTC+02:00) Amman",
        "Middle East Standard Time"=>"(UTC+02:00) Beirut",
        "South Africa Standard Time"=>"(UTC+02:00) Harare, Pretoria",
        "Syria Standard Time"=>"(UTC+02:00) Damascus",
        "Turkey Standard Time"=>"(UTC+02:00) Istanbul",
        "Arab Standard Time"=>"(UTC+03:00) Kuwait, Riyadh",
        "Arabic Standard Time"=>"(UTC+03:00) Baghdad",
        "E. Africa Standard Time"=>"(UTC+03:00) Nairobi",
        "Kaliningrad Standard Time"=>"(UTC+03:00) Kaliningrad",
        "Russian Standard Time"=>"(UTC+04:00) Moscow, St. Petersburg, Volgograd",
        "Iran Standard Time"=>"(UTC+03:30) Tehran",
        "Arabian Standard Time"=>"(UTC+04:00) Abu Dhabi, Muscat",
        "Azerbaijan Standard Time"=>"(UTC+04:00) Baku",
        "Caucasus Standard Time"=>"(UTC+04:00) Yerevan",
        "Afghanistan Standard Time"=>"(UTC+04:30) Kabul",
        "Georgian Standard Time"=>"(UTC+04:00) Tbilisi",
        "Mauritius Standard Time"=>"(UTC+04:00) Port Louis",
        "Ekaterinburg Standard Time"=>"(UTC+06:00) Ekaterinburg",
        "West Asia Standard Time"=>"(UTC+05:00) Tashkent",
        "India Standard Time"=>"(UTC+05:30) Chennai, Kolkata, Mumbai, New Delhi",
        "Sri Lanka Standard Time"=>"(UTC+05:30) Sri Jayawardenepura",
        "Nepal Standard Time"=>"(UTC+05:45) Kathmandu",
        "Pakistan Standard Time"=>"(UTC+05:00) Islamabad, Karachi",
        "Central Asia Standard Time"=>"(UTC+06:00) Astana",
        "N. Central Asia Standard Time"=>"(UTC+07:00) Novosibirsk",
        "Myanmar Standard Time"=>"(UTC+06:30) Yangon (Rangoon)",
        "Bangladesh Standard Time"=>"(UTC+06:00) Dhaka",
        "North Asia Standard Time"=>"(UTC+08:00) Krasnoyarsk",
        "SE Asia Standard Time"=>"(UTC+07:00) Bangkok, Hanoi, Jakarta",
        "China Standard Time"=>"(UTC+08:00) Beijing, Chongqing, Hong Kong, Urumqi",
        "North Asia East Standard Time"=>"(UTC+09:00) Irkutsk",
        "Singapore Standard Time"=>"(UTC+08:00) Kuala Lumpur, Singapore",
        "Taipei Standard Time"=>"(UTC+08:00) Taipei",
        "W. Australia Standard Time"=>"(UTC+08:00) Perth",
        "Ulaanbaatar Standard Time"=>"(UTC+08:00) Ulaanbaatar",
        "Korea Standard Time"=>"(UTC+09:00) Seoul",
        "Tokyo Standard Time"=>"(UTC+09:00) Osaka, Sapporo, Tokyo",
        "Yakutsk Standard Time"=>"(UTC+10:00) Yakutsk",
        "AUS Central Standard Time"=>"(UTC+09:30) Darwin",
        "Cen. Australia Standard Time"=>"(UTC+09:30) Adelaide",
        "AUS Eastern Standard Time"=>"(UTC+10:00) Canberra, Melbourne, Sydney",
        "E. Australia Standard Time"=>"(UTC+10:00) Brisbane",
        "Tasmania Standard Time"=>"(UTC+10:00) Hobart",
        "Vladivostok Standard Time"=>"(UTC+11:00) Vladivostok",
        "West Pacific Standard Time"=>"(UTC+10:00) Guam, Port Moresby",
        "Central Pacific Standard Time"=>"(UTC+11:00) Solomon Is., New Caledonia",
        "Magadan Standard Time"=>"(UTC+12:00) Magadan",
        "Fiji Standard Time"=>"(UTC+12:00) Fiji",
        "New Zealand Standard Time"=>"(UTC+12:00) Auckland, Wellington",
        "UTC+12"=>"(UTC+12:00) Coordinated Universal Time +12",
        "Tonga Standard Time"=>"(UTC+13:00) Nuku'alofa",
        "Samoa Standard Time"=>"(UTC-11:00)Samoa");
    private $currencies_bk = array(
      'AED' => array(
        'display_name' => 'UAE Dirham',
        'numeric_code' => 784,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'AFN' => array(
        'display_name' => 'Afghani',
        'numeric_code' => 971,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'ALL' => array(
        'display_name' => 'Lek',
        'numeric_code' => 8,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'AMD' => array(
        'display_name' => 'Armenian Dram',
        'numeric_code' => 51,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'ANG' => array(
        'display_name' => 'Netherlands Antillean Guilder',
        'numeric_code' => 532,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'AOA' => array(
        'display_name' => 'Kwanza',
        'numeric_code' => 973,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'ARS' => array(
        'display_name' => 'Argentine Peso',
        'numeric_code' => 32,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'AUD' => array(
        'display_name' => 'Australian Dollar',
        'numeric_code' => 36,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'AWG' => array(
        'display_name' => 'Aruban Florin',
        'numeric_code' => 533,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'AZN' => array(
        'display_name' => 'Azerbaijanian Manat',
        'numeric_code' => 944,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'BAM' => array(
        'display_name' => 'Convertible Mark',
        'numeric_code' => 977,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'BBD' => array(
        'display_name' => 'Barbados Dollar',
        'numeric_code' => 52,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'BDT' => array(
        'display_name' => 'Taka',
        'numeric_code' => 50,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'BGN' => array(
        'display_name' => 'Bulgarian Lev',
        'numeric_code' => 975,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'BHD' => array(
        'display_name' => 'Bahraini Dinar',
        'numeric_code' => 48,
        'default_fraction_digits' => 3,
        'sub_unit' => 1000,
      ),
      'BIF' => array(
        'display_name' => 'Burundi Franc',
        'numeric_code' => 108,
        'default_fraction_digits' => 0,
        'sub_unit' => 100,
      ),
      'BMD' => array(
        'display_name' => 'Bermudian Dollar',
        'numeric_code' => 60,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'BND' => array(
        'display_name' => 'Brunei Dollar',
        'numeric_code' => 96,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'BOB' => array(
        'display_name' => 'Boliviano',
        'numeric_code' => 68,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'BOV' => array(
        'display_name' => 'Mvdol',
        'numeric_code' => 984,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'BRL' => array(
        'display_name' => 'Brazilian Real',
        'numeric_code' => 986,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'BSD' => array(
        'display_name' => 'Bahamian Dollar',
        'numeric_code' => 44,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'BTN' => array(
        'display_name' => 'Ngultrum',
        'numeric_code' => 64,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'BWP' => array(
        'display_name' => 'Pula',
        'numeric_code' => 72,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'BYR' => array(
        'display_name' => 'Belarussian Ruble',
        'numeric_code' => 974,
        'default_fraction_digits' => 0,
        'sub_unit' => 100,
      ),
      'BZD' => array(
        'display_name' => 'Belize Dollar',
        'numeric_code' => 84,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'CAD' => array(
        'display_name' => 'Canadian Dollar',
        'numeric_code' => 124,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'CDF' => array(
        'display_name' => 'Congolese Franc',
        'numeric_code' => 976,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'CHE' => array(
        'display_name' => 'WIR Euro',
        'numeric_code' => 947,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'CHF' => array(
        'display_name' => 'Swiss Franc',
        'numeric_code' => 756,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'CHW' => array(
        'display_name' => 'WIR Franc',
        'numeric_code' => 948,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'CLF' => array(
        'display_name' => 'Unidades de fomento',
        'numeric_code' => 990,
        'default_fraction_digits' => 0,
        'sub_unit' => 100,
      ),
      'CLP' => array(
        'display_name' => 'Chilean Peso',
        'numeric_code' => 152,
        'default_fraction_digits' => 0,
        'sub_unit' => 100,
      ),
      'CNY' => array(
        'display_name' => 'Yuan Renminbi',
        'numeric_code' => 156,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'COP' => array(
        'display_name' => 'Colombian Peso',
        'numeric_code' => 170,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'COU' => array(
        'display_name' => 'Unidad de Valor Real',
        'numeric_code' => 970,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'CRC' => array(
        'display_name' => 'Costa Rican Colon',
        'numeric_code' => 188,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'CUC' => array(
        'display_name' => 'Peso Convertible',
        'numeric_code' => 931,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'CUP' => array(
        'display_name' => 'Cuban Peso',
        'numeric_code' => 192,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'CVE' => array(
        'display_name' => 'Cape Verde Escudo',
        'numeric_code' => 132,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'CZK' => array(
        'display_name' => 'Czech Koruna',
        'numeric_code' => 203,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'DJF' => array(
        'display_name' => 'Djibouti Franc',
        'numeric_code' => 262,
        'default_fraction_digits' => 0,
        'sub_unit' => 100,
      ),
      'DKK' => array(
        'display_name' => 'Danish Krone',
        'numeric_code' => 208,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'DOP' => array(
        'display_name' => 'Dominican Peso',
        'numeric_code' => 214,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'DZD' => array(
        'display_name' => 'Algerian Dinar',
        'numeric_code' => 12,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'EGP' => array(
        'display_name' => 'Egyptian Pound',
        'numeric_code' => 818,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'ERN' => array(
        'display_name' => 'Nakfa',
        'numeric_code' => 232,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'ETB' => array(
        'display_name' => 'Ethiopian Birr',
        'numeric_code' => 230,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'EUR' => array(
        'display_name' => 'Euro',
        'numeric_code' => 978,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'FJD' => array(
        'display_name' => 'Fiji Dollar',
        'numeric_code' => 242,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'FKP' => array(
        'display_name' => 'Falkland Islands Pound',
        'numeric_code' => 238,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'GBP' => array(
        'display_name' => 'Pound Sterling',
        'numeric_code' => 826,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'GEL' => array(
        'display_name' => 'Lari',
        'numeric_code' => 981,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'GHS' => array(
        'display_name' => 'Ghana Cedi',
        'numeric_code' => 936,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'GIP' => array(
        'display_name' => 'Gibraltar Pound',
        'numeric_code' => 292,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'GMD' => array(
        'display_name' => 'Dalasi',
        'numeric_code' => 270,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'GNF' => array(
        'display_name' => 'Guinea Franc',
        'numeric_code' => 324,
        'default_fraction_digits' => 0,
        'sub_unit' => 100,
      ),
      'GTQ' => array(
        'display_name' => 'Quetzal',
        'numeric_code' => 320,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'GYD' => array(
        'display_name' => 'Guyana Dollar',
        'numeric_code' => 328,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'HKD' => array(
        'display_name' => 'Hong Kong Dollar',
        'numeric_code' => 344,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'HNL' => array(
        'display_name' => 'Lempira',
        'numeric_code' => 340,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'HRK' => array(
        'display_name' => 'Croatian Kuna',
        'numeric_code' => 191,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'HTG' => array(
        'display_name' => 'Gourde',
        'numeric_code' => 332,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'HUF' => array(
        'display_name' => 'Forint',
        'numeric_code' => 348,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'IDR' => array(
        'display_name' => 'Rupiah',
        'numeric_code' => 360,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'ILS' => array(
        'display_name' => 'New Israeli Sheqel',
        'numeric_code' => 376,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'INR' => array(
        'display_name' => 'Indian Rupee',
        'numeric_code' => 356,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'IQD' => array(
        'display_name' => 'Iraqi Dinar',
        'numeric_code' => 368,
        'default_fraction_digits' => 3,
        'sub_unit' => 1000,
      ),
      'IRR' => array(
        'display_name' => 'Iranian Rial',
        'numeric_code' => 364,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'ISK' => array(
        'display_name' => 'Iceland Krona',
        'numeric_code' => 352,
        'default_fraction_digits' => 0,
        'sub_unit' => 100,
      ),
      'JMD' => array(
        'display_name' => 'Jamaican Dollar',
        'numeric_code' => 388,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'JOD' => array(
        'display_name' => 'Jordanian Dinar',
        'numeric_code' => 400,
        'default_fraction_digits' => 3,
        'sub_unit' => 100,
      ),
      'JPY' => array(
        'display_name' => 'Yen',
        'numeric_code' => 392,
        'default_fraction_digits' => 0,
        'sub_unit' => 100,
      ),
      'KES' => array(
        'display_name' => 'Kenyan Shilling',
        'numeric_code' => 404,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'KGS' => array(
        'display_name' => 'Som',
        'numeric_code' => 417,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'KHR' => array(
        'display_name' => 'Riel',
        'numeric_code' => 116,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'KMF' => array(
        'display_name' => 'Comoro Franc',
        'numeric_code' => 174,
        'default_fraction_digits' => 0,
        'sub_unit' => 100,
      ),
      'KPW' => array(
        'display_name' => 'North Korean Won',
        'numeric_code' => 408,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'KRW' => array(
        'display_name' => 'Won',
        'numeric_code' => 410,
        'default_fraction_digits' => 0,
        'sub_unit' => 100,
      ),
      'KWD' => array(
        'display_name' => 'Kuwaiti Dinar',
        'numeric_code' => 414,
        'default_fraction_digits' => 3,
        'sub_unit' => 1000,
      ),
      'KYD' => array(
        'display_name' => 'Cayman Islands Dollar',
        'numeric_code' => 136,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'KZT' => array(
        'display_name' => 'Tenge',
        'numeric_code' => 398,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'LAK' => array(
        'display_name' => 'Kip',
        'numeric_code' => 418,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'LBP' => array(
        'display_name' => 'Lebanese Pound',
        'numeric_code' => 422,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'LKR' => array(
        'display_name' => 'Sri Lanka Rupee',
        'numeric_code' => 144,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'LRD' => array(
        'display_name' => 'Liberian Dollar',
        'numeric_code' => 430,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'LSL' => array(
        'display_name' => 'Loti',
        'numeric_code' => 426,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'LTL' => array(
        'display_name' => 'Lithuanian Litas',
        'numeric_code' => 440,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'LVL' => array(
        'display_name' => 'Latvian Lats',
        'numeric_code' => 428,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'LYD' => array(
        'display_name' => 'Libyan Dinar',
        'numeric_code' => 434,
        'default_fraction_digits' => 3,
        'sub_unit' => 1000,
      ),
      'MAD' => array(
        'display_name' => 'Moroccan Dirham',
        'numeric_code' => 504,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'MDL' => array(
        'display_name' => 'Moldovan Leu',
        'numeric_code' => 498,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'MGA' => array(
        'display_name' => 'Malagasy Ariary',
        'numeric_code' => 969,
        'default_fraction_digits' => 2,
        'sub_unit' => 5,
      ),
      'MKD' => array(
        'display_name' => 'Denar',
        'numeric_code' => 807,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'MMK' => array(
        'display_name' => 'Kyat',
        'numeric_code' => 104,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'MNT' => array(
        'display_name' => 'Tugrik',
        'numeric_code' => 496,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'MOP' => array(
        'display_name' => 'Pataca',
        'numeric_code' => 446,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'MRO' => array(
        'display_name' => 'Ouguiya',
        'numeric_code' => 478,
        'default_fraction_digits' => 2,
        'sub_unit' => 5,
      ),
      'MUR' => array(
        'display_name' => 'Mauritius Rupee',
        'numeric_code' => 480,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'MVR' => array(
        'display_name' => 'Rufiyaa',
        'numeric_code' => 462,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'MWK' => array(
        'display_name' => 'Kwacha',
        'numeric_code' => 454,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'MXN' => array(
        'display_name' => 'Mexican Peso',
        'numeric_code' => 484,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'MXV' => array(
        'display_name' => 'Mexican Unidad de Inversion (UDI)',
        'numeric_code' => 979,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'MYR' => array(
        'display_name' => 'Malaysian Ringgit',
        'numeric_code' => 458,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'MZN' => array(
        'display_name' => 'Mozambique Metical',
        'numeric_code' => 943,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'NAD' => array(
        'display_name' => 'Namibia Dollar',
        'numeric_code' => 516,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'NGN' => array(
        'display_name' => 'Naira',
        'numeric_code' => 566,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'NIO' => array(
        'display_name' => 'Cordoba Oro',
        'numeric_code' => 558,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'NOK' => array(
        'display_name' => 'Norwegian Krone',
        'numeric_code' => 578,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'NPR' => array(
        'display_name' => 'Nepalese Rupee',
        'numeric_code' => 524,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'NZD' => array(
        'display_name' => 'New Zealand Dollar',
        'numeric_code' => 554,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'OMR' => array(
        'display_name' => 'Rial Omani',
        'numeric_code' => 512,
        'default_fraction_digits' => 3,
        'sub_unit' => 1000,
      ),
      'PAB' => array(
        'display_name' => 'Balboa',
        'numeric_code' => 590,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'PEN' => array(
        'display_name' => 'Nuevo Sol',
        'numeric_code' => 604,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'PGK' => array(
        'display_name' => 'Kina',
        'numeric_code' => 598,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'PHP' => array(
        'display_name' => 'Philippine Peso',
        'numeric_code' => 608,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'PKR' => array(
        'display_name' => 'Pakistan Rupee',
        'numeric_code' => 586,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'PLN' => array(
        'display_name' => 'Zloty',
        'numeric_code' => 985,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'PYG' => array(
        'display_name' => 'Guarani',
        'numeric_code' => 600,
        'default_fraction_digits' => 0,
        'sub_unit' => 100,
      ),
      'QAR' => array(
        'display_name' => 'Qatari Rial',
        'numeric_code' => 634,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'RON' => array(
        'display_name' => 'New Romanian Leu',
        'numeric_code' => 946,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'RSD' => array(
        'display_name' => 'Serbian Dinar',
        'numeric_code' => 941,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'RUB' => array(
        'display_name' => 'Russian Ruble',
        'numeric_code' => 643,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'RWF' => array(
        'display_name' => 'Rwanda Franc',
        'numeric_code' => 646,
        'default_fraction_digits' => 0,
        'sub_unit' => 100,
      ),
      'SAR' => array(
        'display_name' => 'Saudi Riyal',
        'numeric_code' => 682,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'SBD' => array(
        'display_name' => 'Solomon Islands Dollar',
        'numeric_code' => 90,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'SCR' => array(
        'display_name' => 'Seychelles Rupee',
        'numeric_code' => 690,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'SDG' => array(
        'display_name' => 'Sudanese Pound',
        'numeric_code' => 938,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'SEK' => array(
        'display_name' => 'Swedish Krona',
        'numeric_code' => 752,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'SGD' => array(
        'display_name' => 'Singapore Dollar',
        'numeric_code' => 702,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'SHP' => array(
        'display_name' => 'Saint Helena Pound',
        'numeric_code' => 654,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'SLL' => array(
        'display_name' => 'Leone',
        'numeric_code' => 694,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'SOS' => array(
        'display_name' => 'Somali Shilling',
        'numeric_code' => 706,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'SRD' => array(
        'display_name' => 'Surinam Dollar',
        'numeric_code' => 968,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'SSP' => array(
        'display_name' => 'South Sudanese Pound',
        'numeric_code' => 728,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'STD' => array(
        'display_name' => 'Dobra',
        'numeric_code' => 678,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'SVC' => array(
        'display_name' => 'El Salvador Colon',
        'numeric_code' => 222,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'SYP' => array(
        'display_name' => 'Syrian Pound',
        'numeric_code' => 760,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'SZL' => array(
        'display_name' => 'Lilangeni',
        'numeric_code' => 748,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'THB' => array(
        'display_name' => 'Baht',
        'numeric_code' => 764,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'TJS' => array(
        'display_name' => 'Somoni',
        'numeric_code' => 972,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'TMT' => array(
        'display_name' => 'Turkmenistan New Manat',
        'numeric_code' => 934,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'TND' => array(
        'display_name' => 'Tunisian Dinar',
        'numeric_code' => 788,
        'default_fraction_digits' => 3,
        'sub_unit' => 1000,
      ),
      'TOP' => array(
        'display_name' => 'Pa’anga',
        'numeric_code' => 776,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'TRY' => array(
        'display_name' => 'Turkish Lira',
        'numeric_code' => 949,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'TTD' => array(
        'display_name' => 'Trinidad and Tobago Dollar',
        'numeric_code' => 780,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'TWD' => array(
        'display_name' => 'New Taiwan Dollar',
        'numeric_code' => 901,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'TZS' => array(
        'display_name' => 'Tanzanian Shilling',
        'numeric_code' => 834,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'UAH' => array(
        'display_name' => 'Hryvnia',
        'numeric_code' => 980,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'UGX' => array(
        'display_name' => 'Uganda Shilling',
        'numeric_code' => 800,
        'default_fraction_digits' => 0,
        'sub_unit' => 100,
      ),
      'USD' => array(
        'display_name' => 'US Dollar',
        'numeric_code' => 840,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'USN' => array(
        'display_name' => 'US Dollar (Next day)',
        'numeric_code' => 997,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'USS' => array(
        'display_name' => 'US Dollar (Same day)',
        'numeric_code' => 998,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'UYI' => array(
        'display_name' => 'Uruguay Peso en Unidades Indexadas (URUIURUI)',
        'numeric_code' => 940,
        'default_fraction_digits' => 0,
        'sub_unit' => 100,
      ),
      'UYU' => array(
        'display_name' => 'Peso Uruguayo',
        'numeric_code' => 858,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'UZS' => array(
        'display_name' => 'Uzbekistan Sum',
        'numeric_code' => 860,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'VEF' => array(
        'display_name' => 'Bolivar',
        'numeric_code' => 937,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'VND' => array(
        'display_name' => 'Dong',
        'numeric_code' => 704,
        'default_fraction_digits' => 0,
        'sub_unit' => 10,
      ),
      'VUV' => array(
        'display_name' => 'Vatu',
        'numeric_code' => 548,
        'default_fraction_digits' => 0,
        'sub_unit' => 1,
      ),
      'WST' => array(
        'display_name' => 'Tala',
        'numeric_code' => 882,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'XAF' => array(
        'display_name' => 'CFA Franc BEAC',
        'numeric_code' => 950,
        'default_fraction_digits' => 0,
        'sub_unit' => 100,
      ),
      'XAG' => array(
        'display_name' => 'Silver',
        'numeric_code' => 961,
        'default_fraction_digits' => 0,
        'sub_unit' => 100,
      ),
      'XAU' => array(
        'display_name' => 'Gold',
        'numeric_code' => 959,
        'default_fraction_digits' => 0,
        'sub_unit' => 100,
      ),
      'XBA' => array(
        'display_name' => 'Bond Markets Unit European Composite Unit (EURCO)',
        'numeric_code' => 955,
        'default_fraction_digits' => 0,
        'sub_unit' => 100,
      ),
      'XBB' => array(
        'display_name' => 'Bond Markets Unit European Monetary Unit (E.M.U.-6)',
        'numeric_code' => 956,
        'default_fraction_digits' => 0,
        'sub_unit' => 100,
      ),
      'XBC' => array(
        'display_name' => 'Bond Markets Unit European Unit of Account 9 (E.U.A.-9)',
        'numeric_code' => 957,
        'default_fraction_digits' => 0,
        'sub_unit' => 100,
      ),
      'XBD' => array(
        'display_name' => 'Bond Markets Unit European Unit of Account 17 (E.U.A.-17)',
        'numeric_code' => 958,
        'default_fraction_digits' => 0,
        'sub_unit' => 100,
      ),
      'XCD' => array(
        'display_name' => 'East Caribbean Dollar',
        'numeric_code' => 951,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'XDR' => array(
        'display_name' => 'SDR (Special Drawing Right)',
        'numeric_code' => 960,
        'default_fraction_digits' => 0,
        'sub_unit' => 100,
      ),
      'XFU' => array(
        'display_name' => 'UIC-Franc',
        'numeric_code' => 958,
        'default_fraction_digits' => 0,
        'sub_unit' => 100,
      ),
      'XOF' => array(
        'display_name' => 'CFA Franc BCEAO',
        'numeric_code' => 952,
        'default_fraction_digits' => 0,
        'sub_unit' => 100,
      ),
      'XPD' => array(
        'display_name' => 'Palladium',
        'numeric_code' => 964,
        'default_fraction_digits' => 0,
        'sub_unit' => 100,
      ),
      'XPF' => array(
        'display_name' => 'CFP Franc',
        'numeric_code' => 953,
        'default_fraction_digits' => 0,
        'sub_unit' => 100,
      ),
      'XPT' => array(
        'display_name' => 'Platinum',
        'numeric_code' => 962,
        'default_fraction_digits' => 0,
        'sub_unit' => 100,
      ),
      'XSU' => array(
        'display_name' => 'Sucre',
        'numeric_code' => 994,
        'default_fraction_digits' => 0,
        'sub_unit' => 100,
      ),
      'XTS' => array(
        'display_name' => 'Codes specifically reserved for testing purposes',
        'numeric_code' => 963,
        'default_fraction_digits' => 0,
        'sub_unit' => 100,
      ),
      'XUA' => array(
        'display_name' => 'ADB Unit of Account',
        'numeric_code' => 965,
        'default_fraction_digits' => 0,
        'sub_unit' => 100,
      ),
      'XXX' => array(
        'display_name' => 'The codes assigned for transactions where no currency is involved',
        'numeric_code' => 999,
        'default_fraction_digits' => 0,
        'sub_unit' => 100,
      ),
      'YER' => array(
        'display_name' => 'Yemeni Rial',
        'numeric_code' => 886,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'ZAR' => array(
        'display_name' => 'Rand',
        'numeric_code' => 710,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'ZMW' => array(
        'display_name' => 'Zambian Kwacha',
        'numeric_code' => 967,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      ),
      'ZWL' => array(
        'display_name' => 'Zimbabwe Dollar',
        'numeric_code' => 932,
        'default_fraction_digits' => 2,
        'sub_unit' => 100,
      )
    );
    private $list_curency = array(
            "VND"=>"Việt Nam đồng (VND)",
            "USD"=>"United States Dollars (USD)",
            "EUR"=>"Euro (EUR)",
            "GBP"=>"United Kingdom Pounds (GBP)",
            "JPY"=>"Japanese Yen (JPY)",
            "SGD"=>"Singapore Dollars (SGD)",
            "KRW"=>"South Korean Won (KRW)",
            "THB"=>"Thai baht (THB)",
            "CNY"=>"Chinese (Simplified, China)",
            "AED"=>"AED",
            "AFN"=>"AFN",
            "ALL"=>"ALL",
            "AMD"=>"AMD",
            "ANG"=>"ANG",
            "AOA"=>"AOA",
            "ARS"=>"ARS",
            "AUD"=>"AUD",
            "AWG"=>"AWG",
            "AZN"=>"AZN",
            "BAM"=>"BAM",
            "BBD"=>"BBD",
            "BDT"=>"BDT",
            "BGN"=>"BGN",
            "BHD"=>"BHD",
            "BIF"=>"BIF",
            "BMD"=>"BMD",
            "BND"=>"BND",
            "BOB"=>"BOB",
            "BOV"=>"BOV",
            "BRL"=>"BRL",
            "BSD"=>"BSD",
            "BTN"=>"BTN",
            "BWP"=>"BWP",
            "BYN"=>"BYN",
            "BYR"=>"BYR",
            "BZD"=>"BZD",
            "CAD"=>"CAD",
            "CDF"=>"CDF",
            "CHE"=>"CHE",
            "CHF"=>"CHF",
            "CHW"=>"CHW",
            "CLF"=>"CLF",
            "CLP"=>"CLP",
            "COP"=>"COP",
            "COU"=>"COU",
            "CRC"=>"CRC",
            "CUC"=>"CUC",
            "CUP"=>"CUP",
            "CVE"=>"CVE",
            "CZK"=>"CZK",
            "DJF"=>"DJF",
            "DKK"=>"DKK",
            "DOP"=>"DOP", 
            "DZD"=>"DZD", 
            "EGP"=>"EGP", 
            "ERN"=>"ERN",
            "ETB"=>"ETB", 
            "FJD"=>"FJD", 
            "FKP"=>"FKP",
            "GEL"=>"GEL", 
            "GHS"=>"GHS", 
            "GIP"=>"GIP", 
            "GMD"=>"GMD", 
            "GNF"=>"GNF", 
            "GTQ"=>"GTQ", 
            "GYD"=>"GYD",
            "HKD"=>"HKD", 
            "HNL"=>"HNL", 
            "HRK"=>"HRK", 
            "HTG"=>"HTG", 
            "HUF"=>"HUF", 
            "IDR"=>"IDR", 
            "ILS"=>"ILS", 
            "INR"=>"INR", 
            "IQD"=>"IQD", 
            "IRR"=>"IRR", 
            "ISK"=>"ISK", 
            "JMD"=>"JMD",
            "JOD"=>"JOD", 
            "KES"=>"KES", 
            "KGS"=>"KGS", 
            "KHR"=>"KHR", 
            "KMF"=>"KMF", 
            "KPW"=>"KPW", 
            "KWD"=>"KWD", 
            "KYD"=>"KYD", 
            "KZT"=>"KZT", 
            "LAK"=>"LAK",
            "LBP"=>"LBP", 
            "LKR"=>"LKR", 
            "LRD"=>"LRD", 
            "LSL"=>"LSL", 
            "LYD"=>"LYD", 
            "MAD"=>"MAD", 
            "MDL"=>"MDL", 
            "MGA"=>"MGA", 
            "MKD"=>"MKD", 
            "MMK"=>"MMK", 
            "MNT"=>"MNT", 
            "MOP"=>"MOP",
            "MRO"=>"MRO", 
            "MUR"=>"MUR", 
            "MVR"=>"MVR", 
            "MWK"=>"MWK", 
            "MXN"=>"MXN", 
            "MXV"=>"MXV", 
            "MYR"=>"MYR", 
            "MZN"=>"MZN", 
            "NAD"=>"NAD", 
            "NGN"=>"NGN", 
            "NIO"=>"NIO", 
            "NOK"=>"NOK",
            "NPR"=>"NPR", 
            "NZD"=>"NZD",
            "OMR"=>"OMR", 
            "PAB"=>"PAB", 
            "PEN"=>"PEN", 
            "PGK"=>"PGK", 
            "PHP"=>"PHP", 
            "PKR"=>"PKR", 
            "PLN"=>"PLN", 
            "PYG"=>"PYG", 
            "QAR"=>"QAR", 
            "RON"=>"RON",
            "RSD"=>"RSD", 
            "RUB"=>"RUB", 
            "RWF"=>"RWF", 
            "SAR"=>"SAR", 
            "SBD"=>"SBD",
            "SCR"=>"SCR", 
            "SDG"=>"SDG", 
            "SEK"=>"SEK", 
            "SHP"=>"SHP", 
            "SLL"=>"SLL", 
            "SOS"=>"SOS",
            "SRD"=>"SRD", 
            "SSP"=>"SSP", 
            "STD"=>"STD", 
            "SYP"=>"SYP", 
            "SZL"=>"SZL", 
            "TJS"=>"TJS", 
            "TMT"=>"TMT", 
            "TND"=>"TND", 
            "TOP"=>"TOP", 
            "TRY"=>"TRY", 
            "TTD"=>"TTD",
            "TWD"=>"TWD", 
            "TZS"=>"TZS", 
            "UAH"=>"UAH", 
            "UGX"=>"UGX", 
            "USN"=>"USN", 
            "UYI"=>"UYI", 
            "UYU"=>"UYU", 
            "UZS"=>"UZS", 
            "VEF"=>"VEF", 
            "VUV"=>"VUV",
            "WST"=>"WST", 
            "XAF"=>"XAF", 
            "XAG"=>"XAG", 
            "XAU"=>"XAU", 
            "XBA"=>"XBA", 
            "XBB"=>"XBB", 
            "XBC"=>"XBC", 
            "XBD"=>"XBD", 
            "XCD"=>"XCD", 
            "XDR"=>"XDR", 
            "XFU"=>"XFU", 
            "XOF"=>"XOF",
            "XPD"=>"XPD", 
            "XPF"=>"XPF", 
            "XPT"=>"XPT", 
            "XSU"=>"XSU",
            "XTS"=>"XTS",
            "XUA"=>"XUA",
            "XXX"=>"XXX",
            "YER"=>"YER",
            "ZAR"=>"ZAR", 
            "ZMW"=>"ZMW",
        );

    private $type_buy = array(
                            "0"=>"Tất cả",
                            "1"=>"Giao hàng tận nơi",
                            "2"=>"Nhận hàng tại của hàng"
                        );

    public function indexAction()
    {
        $form = new WebsiteForm();
        $form->get('submit')->setValue('Cập nhật');
        $form->get('website_timezone')->setOptions(array(
            'options' => $this->list_timezone
        ));
        $form->get('website_currency')->setOptions(array(
            'options' => $this->list_curency
        ));
        $form->get('version_cart')->setOptions(array(
            'options' => $this->list_version_cart
        ));
        $form->get('type_crop_image')->setOptions(array(
            'options' => $this->list_crop
        ));
        $contries = $this->getModelTable('CountryTable')->getContries();
        $options_contries = array();
        foreach ($contries as $key => $contry) {
            $options_contries[$contry['id']] = $contry['title'];
        }
        $form->get('website_contries')->setOptions(array(
            'options' => $options_contries
        ));
        $form->get('type_buy')->setOptions(array(
            'options' => $this->type_buy
        ));
        $form->bind($this->website);
        $_website_contries = explode(',', $this->website->website_contries);
        $form->get('website_contries')->setValue($_website_contries);

        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            $website = new Websites();
            $form->setInputFilter($website->getInputFilter());
            $form->setData($request->getPost());
            $domain = $_SESSION['website']['website_domain'];
            if($form->isValid()){
                $picture_id = $request->getPost('picture_id', '');
                if(!empty($picture_id)){
                    $picture = $this->getModelTable('PictureTable')->getPicture($picture_id);
                    if(!empty($picture)){
                        $data['logo'] = $picture->folder.'/'.$picture->name.'.'.$picture->type;
                    }
                }
                $data['website_domain'] = $domain;
                if( !empty($data['website_contries']) 
                    && is_array($data['website_contries']) ){
                    $data['website_contries'] = implode(',', $data['website_contries']);
                }
                $website->exchangeArray($data);
                $this->getModelTable('WebsitesTable')->saveWebsite($website);
                return $this->redirect()->toRoute('cms/website');
            }else{
                //print_r($form->getMessages());die();
            }

        }
        $this->data_view['form'] = $form;
        return $this->data_view;
    }

    public function editAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('cms/website', array(
                'action' => 'list'
            ));
        }
        try {
            $website = $this->getModelTable('WebsitesTable')->getWebsiteWithId($id);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/website', array(
                'action' => 'list'
            ));
        }
        $form = new WebsiteForm();
        $form->get('submit')->setValue('Cập nhật');
        $form->get('website_timezone')->setOptions(array(
            'options' => $this->list_timezone
        ));
        $form->get('version_cart')->setOptions(array(
            'options' => $this->list_version_cart
        ));
        $form->get('type_crop_image')->setOptions(array(
            'options' => $this->list_crop
        ));
        $form->get('website_currency')->setOptions(array(
            'options' => $this->list_curency
        ));
        $form->get('type_buy')->setOptions(array(
            'options' => $this->type_buy
        ));
        $contries = $this->getModelTable('CountryTable')->getContries();
        $options_contries = array();
        foreach ($contries as $key => $contry) {
            $options_contries[$contry['id']] = $contry['title'];
        }
        $form->get('website_contries')->setOptions(array(
            'options' => $options_contries
        ));
        $form->bind($website);
        $_website_contries = explode(',', $website->website_contries);
        $form->get('website_contries')->setValue($_website_contries);

        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            $website = new Websites();
            $form->setInputFilter($website->getInputFilter());
            $form->setData($request->getPost());
            if($form->isValid()){
                $picture_id = $request->getPost('picture_id', '');
                if(!empty($picture_id)){
                    $picture = $this->getModelTable('PictureTable')->getPicture($picture_id);
                    if(!empty($picture)){
                        $data['logo'] = $picture->folder.'/'.$picture->name.'.'.$picture->type;
                    }
                }
                if( !empty($data['website_contries']) 
                    && is_array($data['website_contries']) ){
                    $data['website_contries'] = implode(',', $data['website_contries']);
                }
                $website->exchangeArray($data);
                $modules = $request->getPost('modules');
                $this->getModelTable('WebsitesTable')->saveWebsiteForSupperWeb($website, $modules);

                /*strigger change namespace cached*/
                $this->updateNamespaceCached();

                return $this->redirect()->toRoute('cms/website', array(
                    'action' => 'list'
                ));
            }else{
                print_r($form->getMessages());die();
            }

        }
        $modules = $this->getModelTable('ModulesTable')->fetchAll('', array('date_create' => 'ASC', 'date_update' => 'ASC'), 0, 1000);
        $website_modules = $this->getModelTable('ModulesTable')->getWebsiteModule($website->website_id);

        $module_access = array();
        foreach($website_modules as $module){
            $module_access[] = $module['module_id'];
        }
        $this->data_view['id'] = $website->website_id;
        $this->data_view['mywebsite'] = $website;
        $this->data_view['form'] = $form;
        $this->data_view['modules'] = $modules;
        $this->data_view['module_access'] = $module_access;
        return $this->data_view;
    }

    public function listAction()
    {
        $params = array();
        $link = '';
        $page = $this->params()->fromQuery('page', 0);
        $order_type = $this->params()->fromQuery('order_type','desc');
        $order_by = $this->params()->fromQuery('order','website_id');

        $website_name = $this->params()->fromQuery('website_name', NULL);
        if($website_name){
            $params['website_name'] = $website_name;
            $link .= '&website_name='.$website_name;
        }
        $date_create = $this->params()->fromQuery('date_create', NULL);
        if($date_create){
            $params['date_create'] = $date_create;
            $link .= '&date_create='.$date_create;
        }
        $is_try = $this->params()->fromQuery('is_try', NULL);
        if($is_try){
            $params['is_try'] = $is_try;
            $link .= '&is_try='.$is_try;
        }
        $is_published = $this->params()->fromQuery('is_published', NULL);
        if($is_published){
            $params['is_published'] = $is_published;
            $link .= '&is_published='.$is_published;
        }


        $order = array(
            $order_by => $order_type,
        );
        $order_link = $link;

        $total = $this->getModelTable('WebsitesTable')->countAll($params);
        $this->intPage = $page;
        $page_size = $this->intPageSize;
        $objPage = new Paging( $total, $page, $page_size, $link );
        $paging = $objPage->getListFooter ( $link );
        $websites = $this->getModelTable('WebsitesTable')->getList($params, $order, $this->intPage, $this->intPageSize);
        if(!$order_link){
            $order_link = FOLDERWEB.'/cms/website';
            if(isset($_GET['page'])){
                $order_link .= '?page='.$_GET['page'].'&';
            }else{
                $order_link .= '?';
            }
        }else{
            $order_link = FOLDERWEB.'/cms/website?'.trim($order_link,'&');
            if(isset($_GET['page'])){
                $order_link .= '&page='.$_GET['page'].'&';
            }else{
                $order_link .= '&';
            }
        }
        $this->data_view['websites'] = $websites;
        $this->data_view['paging'] = $paging;
        $this->data_view['order_link'] = $order_link;
        return $this->data_view;
    }

    public function deleteAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $websites = $this->getModelTable('WebsitesTable')->getListWebsiteWithId($ids);
            foreach ($websites as $key => $website) {
                $this->getModelTable('WebsitesTable')->deleteWebsite($website);

                /*strigger change namespace cached*/
                $this->updateNamespaceCached();

            }
        }
        return $this->redirect()->toRoute('cms/website', array('action' => 'list'));
    }

    public function singleTryAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if ($id) {
            $ids = array($id);
            $data = array(
                'is_try' => 1
            );
            $this->getModelTable('WebsitesTable')->updateWebsiteWithId($data, $ids);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

        }
        return $this->redirect()->toRoute('cms/website', array('action' => 'list'));
    }

    public function singleNotTryAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if ($id) {
            $ids = array($id);
            $data = array(
                'is_try' => 0
            );
            $this->getModelTable('WebsitesTable')->updateWebsiteWithId($data, $ids);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

        }
        return $this->redirect()->toRoute('cms/website', array('action' => 'list'));
    }

    public function singlepublishAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if ($id) {
            $ids = array($id);
            $data = array(
                'is_published' => 1
            );
            $this->getModelTable('WebsitesTable')->updateWebsiteWithId($data, $ids);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

        }
        return $this->redirect()->toRoute('cms/website', array('action' => 'list'));
    }

    public function singleunpublishAction()
    {
        $id = (int)$this->params()->fromRoute('id', 0);
        if ($id) {
            $ids = array($id);
            $data = array(
                'is_published' => 0
            );
            $this->getModelTable('WebsitesTable')->updateWebsiteWithId($data, $ids);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();

        }
        return $this->redirect()->toRoute('cms/website', array('action' => 'list'));
    }

    public function publishAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 1
            );
            $this->getModelTable('WebsitesTable')->updateWebsiteWithId($data, $ids);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();


        }
        return $this->redirect()->toRoute('cms/website', array('action' => 'list'));
    }

    public function unpublishAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_published' => 0
            );
            if (count($ids) > 0) {
                $this->getModelTable('WebsitesTable')->updateWebsiteWithId($data, $ids);

                /*strigger change namespace cached*/
                $this->updateNamespaceCached();

            }
        }
        return $this->redirect()->toRoute('cms/website', array('action' => 'list'));
    }

    public function tryAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_try' => 1
            );
            $this->getModelTable('WebsitesTable')->updateWebsiteWithId($data, $ids);

            /*strigger change namespace cached*/
            $this->updateNamespaceCached();


        }
        return $this->redirect()->toRoute('cms/website', array('action' => 'list'));
    }

    public function notTryAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $ids = $request->getPost('cid');
            $data = array(
                'is_try' => 0
            );
            if (count($ids) > 0) {
                $this->getModelTable('WebsitesTable')->updateWebsiteWithId($data, $ids);

                /*strigger change namespace cached*/
                $this->updateNamespaceCached();

            }
        }
        return $this->redirect()->toRoute('cms/website', array('action' => 'list'));
    }

    public function compressCss( $files )
    {
        $name_folder = $this->website['websites_folder'];
        try{
            if( !empty($files) ){
                $am = new AssetManager();
                $fm = new FilterManager();
                $fm->set('ImportFilter', new CssImportFilter());
                $fm->set('RewriteFilter', new CssRewriteFilter());
                $fm->set('CssMinFilter', new CssMinFilter());

                $factory = new AssetFactory(FOLDERWEB.'/templates/Websites/'.$name_folder.'/styles/');
                $factory->setAssetManager($am);
                $factory->setFilterManager($fm);
                $factory->setDebug(true);
                $factory->setDefaultOutput('/minify/css/');

                $cssAsset = $factory->createAsset($files, array(
                    'ImportFilter',
                    'RewriteFilter',
                    //'CssMinFilter',
                ));
                $urlComCs = '/styles/minify/css/'.$this->randText(10).date('dmYHms').'.css';
                file_put_contents(PATH_BASE_ROOT.'/templates/Websites/'.$name_folder.$urlComCs, $cssAsset->dump());
                return $urlComCs;
            }
        }catch(\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
        return '';
    }

    public function compressJS( $files )
    {
        $name_folder = $this->website['websites_folder'];
        try{
            if( !empty($files) ){
                $am = new AssetManager();
                $fm = new FilterManager();
                $fm->set('CompilerJarFilter', new CompilerJarFilter(PATH_BASE_ROOT.'/cdn/closure-compiler/closure-compiler.jar'));
                $fm->set('JSMinFilter', new JSMinFilter());
                $factory = new AssetFactory(FOLDERWEB.'/templates/Websites/'.$name_folder.'/styles/');
                $factory->setAssetManager($am);
                $factory->setFilterManager($fm);
                $factory->setDebug(true);
                $factory->setDefaultOutput('/minify/css/');
                $jsAsset = $factory->createAsset($files, array(
                    'JSMinFilter',
                    //'CompilerJarFilter',
                ));
                $urlComCs = '/styles/minify/js/'.$this->randText(10).date('dmYHms').'.js';
                file_put_contents(PATH_BASE_ROOT.'/templates/Websites/'.$name_folder.$urlComCs, $jsAsset->dump());
                return $urlComCs;
            }
        }catch(\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
        return '';
    }


    public function compressAction()
    {
        $result = array('flag' => FALSE, 'msg' => '');
        if(!$this->isMasterPage()){
            try{
                $ListFileAsset = array();
                $name_folder = $this->website['websites_folder'];
                $url_config = PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/config.json';
                if(is_file($url_config)){
                    $urlCom = '/templates/Websites/'.$name_folder.'/styles/minify';
                    if(!is_dir(PATH_BASE_ROOT.$urlCom)){
                        @mkdir ( PATH_BASE_ROOT.$urlCom, 0777 );
                    }
                    $urlComCs = $urlCom.'/css';
                    if(!is_dir(PATH_BASE_ROOT.$urlComCs)){
                        @mkdir ( PATH_BASE_ROOT.$urlComCs, 0777 );
                    }
                    $urlComJs = $urlCom.'/js';
                    if(!is_dir(PATH_BASE_ROOT.$urlComJs)){
                        @mkdir ( PATH_BASE_ROOT.$urlComJs, 0777 );
                    }
                    $config = file_get_contents($url_config);
                    $config = json_decode($config, true);
                    
                    $listCssFileRunTime = array();
                    $listCssFile = array();
                    $listCss = array();
                    $listCssOnly = array();
                    if(!empty($config['css'])){
                        foreach ($config['css'] as $kcss => $iCss) {
                            if( !$this->isUrlHttp($iCss['href']) ){
                                $iCss['href'] = 'https://static.coz.vn/'.$name_folder.'/'.trim($iCss['href'],'/');
                            }
                            if( empty($iCss['only']) ){
                                $href = $iCss['href'];
                                $listCssFileRunTime[] = $href;
                            }else{
                                if( !empty($listCssFileRunTime) ){
                                    if( count($listCssFileRunTime)>1 ){
                                        $urlComCs = $this->compressCss($listCssFileRunTime);
                                        $listCssFile[] = array('href'=>$urlComCs, 'rel' => 'stylesheet', 'type' => ' text/css');
                                    }else{
                                        $listCssFile[] = array('href'=>$listCssFileRunTime[0], 'rel' => 'stylesheet', 'type' => ' text/css');
                                    }
                                    $listCssFileRunTime = array();
                                }
                                $listCssFile[] = $iCss;
                            }

                            /*if( empty($iCss['only'])
                                && (strpos( $iCss['href'], FOLDERWEB ) !== false 
                                || strpos( $iCss['href'], 'static.coz.vn' ) !== false 
                                || strpos( $iCss['href'], 'cdn.coz.vn' ) !== false) ){
                                $href = $iCss['href'];
                                $listCssFileRunTime[] = $href;
                                continue;
                            }else {
                                if( !empty($listCssFileRunTime) ){
                                    if( count($listCssFileRunTime)>1 ){
                                        $urlComCs = $this->compressCss($listCssFileRunTime);
                                        $listCssFile[] = array('href'=>$urlComCs, 'rel' => 'stylesheet', 'type' => ' text/css');
                                    }else{
                                        $listCssFile[] = array('href'=>str_replace(FOLDERWEB.'/templates/Websites/'.$name_folder, '', $listCssFileRunTime[0]), 'rel' => 'stylesheet', 'type' => ' text/css');
                                    }
                                    $listCssFileRunTime = array();
                                }
                                $listCssFile[] = $iCss;
                            }*/
                            
                            /*$only = array();
                            if( !empty($iCss['only']) ){
                                $only = explode(',', $iCss['only']);
                            }
                            if( !empty($iCss) && !empty($iCss['href']) ){
                                $href = '';
                                if( !$this->isUrlHttp($iCss['href']) ){
                                    $href = FOLDERWEB.'/templates/Websites/'.$name_folder.'/'.trim($iCss['href'],'/');
                                }else{
                                    $href = $iCss['href'];
                                }
                                if( empty($only) ){
                                    $listCss[] = $href;
                                    foreach ($listCssOnly as $lok => $lco) {
                                        $lco[] = $href;
                                    }
                                }else{
                                    foreach ($only as $lk => $ol) {
                                        if( empty($listCssOnly[$ol]) ){
                                            $listCssOnly[$ol] = array();
                                        }
                                        $listCssOnly[$ol][] = $href;
                                    }
                                }
                            }*/
                        }

                        if( !empty($listCssFileRunTime) ){
                            if( count($listCssFileRunTime)>1 ){
                                $urlComCs = $this->compressCss($listCssFileRunTime);
                                $listCssFile[] = array('href'=>$urlComCs, 'rel' => 'stylesheet', 'type' => 'text/css');
                            }else{
                                $listCssFile[] = array('href'=>$listCssFileRunTime[0], 'rel' => 'stylesheet', 'type' => 'text/css');
                            }
                            $listCssFileRunTime = array();
                        }
                    }

                    $listJSFileRunTime = array();
                    $listJSFile = array();
                    $listJs = array();
                    $listJsOnly = array();
                    if(!empty($config['js'])){
                        foreach ($config['js'] as $kjs => $iJs) {
                            if( !$this->isUrlHttp($iJs['src']) ){
                                $iJs['src'] = 'https://static.coz.vn/'.$name_folder.'/'.trim($iJs['src'],'/');
                            }
                            if( empty($iJs['only']) ){
                                $src = $iJs['src'];
                                $listJSFileRunTime[] = $src;
                            }else {
                                if( !empty($listJSFileRunTime) ){
                                    if( count($listJSFileRunTime)>1 ){
                                        $urlComJS = $this->compressJS($listJSFileRunTime);
                                        $listJSFile[] = array('src'=>$urlComJS, 'type' => ' text/javascript');
                                    }else{
                                        $listJSFile[] = array('src'=>$listJSFileRunTime[0], 'type' => ' text/javascript');
                                    }
                                    $listJSFileRunTime = array();
                                }
                                $listJSFile[] = $iJs;
                            }
                            /*if( empty($iJs['only'])
                                && (strpos( $iJs['src'], FOLDERWEB ) !== false 
                                || strpos( $iJs['src'], 'static.coz.vn' ) !== false 
                                || strpos( $iJs['src'], 'cdn.coz.vn' ) !== false) ){
                                $src = $iJs['src'];
                                $listJSFileRunTime[] = $src;
                                continue;
                            }else {
                                if( !empty($listJSFileRunTime) ){
                                    if( count($listJSFileRunTime)>1 ){
                                        $urlComJS = $this->compressJS($listJSFileRunTime);
                                        $listJSFile[] = array('src'=>$urlComJS, 'type' => ' text/javascript');
                                    }else{
                                        $listJSFile[] = array('src'=>$listJSFileRunTime[0]['src'], 'type' => ' text/javascript');
                                    }
                                    $listJSFileRunTime = array();
                                }
                                $listJSFile[] = $iJs;
                            }*/
                        }

                        if( !empty($listJSFileRunTime) ){
                            if( count($listJSFileRunTime)>1 ){
                                $urlComJS = $this->compressJS($listJSFileRunTime);
                                $listJSFile[] = array('src'=>$urlComJS, 'type' => 'text/javascript');
                            }else{
                                $listJSFile[] = array('src'=>$listJSFileRunTime[0], 'type' => 'text/javascript');
                            }
                            $listJSFileRunTime = array();
                        }
                    }

                    if( !empty($config['minify'])
                        && !empty($config['minify']['css']) ){
                        foreach ($config['minify']['css'] as $kcsm => $csMin) {
                            if( !$this->isUrlHttp($csMin['href']) ){
                                $pathCss = PATH_BASE_ROOT.'/templates/Websites/'.$name_folder.'/'.trim($csMin['href'],'/');
                                unset($pathCss);
                            }
                        }
                    }

                    if( !empty($config['minify'])
                        && !empty($config['minify']['js']) ){
                        foreach ($config['minify']['js'] as $kjsm => $jsMin) {
                            if( !$this->isUrlHttp($jsMin['src']) ){
                                $pathJs = PATH_BASE_ROOT.'/templates/Websites/'.$name_folder.'/'.trim($jsMin['src'],'/');
                                unset($pathJs);
                            }
                        }
                    }
                    $config['isMinify'] = TRUE;
                    $config['minify'] = array('css'=>$listCssFile, 'js' => $listJSFile);
                    $str = "{".$this->getStringFomatJsonFriently($config, "\t", TRUE)."}";
                    $fp = fopen($url_config, 'w+');
                    fwrite($fp, $str);
                    fclose($fp);
                    $result = array('flag' => TRUE, 'msg' => 'Thành công');

                }
            }catch(\Exception $ex){
                print_r($ex->getMessage());die();
                $result = array('flag' => FALSE, 'msg' => $ex->getMessage());
            }
        }
        echo json_encode($result);die();
    }

    public function toggleCompressAction()
    {
        $result = array('flag' => FALSE, 'msg' => '');
        if(!$this->isMasterPage()){
            try{
                $ListFileAsset = array();
                $name_folder = $this->website['websites_folder'];
                $url_config = PATH_BASE_ROOT . '/templates/Websites/'.$name_folder.'/config.json';
                if(is_file($url_config)){
                    $config = file_get_contents($url_config);
                    $config = json_decode($config, true);
                    if( !empty($config['minify'])
                        && (!empty($config['minify']['css']) || !empty($config['minify']['js'])) ){
                        if( empty($config['isMinify']) ){
                            $config['isMinify'] = TRUE;
                        }else{
                            $config['isMinify'] = FALSE;
                        }
                        $str = "{".$this->getStringFomatJsonFriently($config, "\t", TRUE)."}";
                        $fp = fopen($url_config, 'w+');
                        fwrite($fp, $str);
                        fclose($fp);
                        $result = array('flag' => TRUE, 'msg' => 'Thành công');
                    }
                }
            }catch(\Exception $ex){
                $result = array('flag' => FALSE, 'msg' => $ex->getMessage());
            }
        }
        echo json_encode($result);die();
    }

}