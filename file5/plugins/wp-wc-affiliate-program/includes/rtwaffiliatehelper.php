<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {exit; }


/**
 * Class Helper
 */
class RtwAffiliateHelper
{
	/**
     * Currencies
	 * @since 2.1.3
     */
	public function RtwWwapCurrencies()
	{
		$rtwwwap_currencies = array(
	        'AED' => array( 'rtwwwap_curr_name' => esc_html__( 'United Arab Emirates dirham', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#1583;.&#1573;' ),
	        'AFN' => array( 'rtwwwap_curr_name' => esc_html__( 'Afghan afghani', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#65;&#102;' ),
	        'ALL' => array( 'rtwwwap_curr_name' => esc_html__( 'Albanian lek', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#76;&#101;&#107;' ),
	        'ANG' => array( 'rtwwwap_curr_name' => esc_html__( 'Netherlands Antillean guilder', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&&#402;' ),
	        'AOA' => array( 'rtwwwap_curr_name' => esc_html__( 'Angolan kwanza', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#75;&#122;' ),
	        'ARS' => array( 'rtwwwap_curr_name' => esc_html__( 'Argentine peso', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#36;' ),
	        'AUD' => array( 'rtwwwap_curr_name' => esc_html__( 'Australian dollar', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#36;' ),
	        'AWG' => array( 'rtwwwap_curr_name' => esc_html__( 'Aruban florin', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#402;' ),
	        'AZN' => array( 'rtwwwap_curr_name' => esc_html__( 'Azerbaijani manat', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#1084;&#1072;&#1085;' ),
	        'BAM' => array( 'rtwwwap_curr_name' => esc_html__( 'Bosnia and Herzegovina convertible mark', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#75;&#77;' ),
	        'BBD' => array( 'rtwwwap_curr_name' => esc_html__( 'Barbadian dollar', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#36;' ),
	        'BDT' => array( 'rtwwwap_curr_name' => esc_html__( 'Bangladeshi taka', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#2547;' ),
	        'BGN' => array( 'rtwwwap_curr_name' => esc_html__( 'Bulgarian lev', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#1083;&#1074;' ),
	        'BHD' => array( 'rtwwwap_curr_name' => esc_html__( 'Bahraini dinar', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '.&#1583;.&#1576;' ),
	        'BIF' => array( 'rtwwwap_curr_name' => esc_html__( 'Burundian franc', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#70;&#66;&#117;' ),
	        'BMD' => array( 'rtwwwap_curr_name' => esc_html__( 'Bermudian dollar', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#36;' ),
	        'BND' => array( 'rtwwwap_curr_name' => esc_html__( 'Brunei dollar', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#36;' ),
	        'BOB' => array( 'rtwwwap_curr_name' => esc_html__( 'Bolivian boliviano', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#36;&#98;' ),
	        'BRL' => array( 'rtwwwap_curr_name' => esc_html__( 'Brazilian real', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#82;&#36;' ),
	        'BSD' => array( 'rtwwwap_curr_name' => esc_html__( 'Bahamian dollar', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#36;' ),
	        'BTN' => array( 'rtwwwap_curr_name' => esc_html__( 'Bhutanese ngultrum', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#78;&#117;&#46;' ),
	        'BWP' => array( 'rtwwwap_curr_name' => esc_html__( 'Botswana pula', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#80;' ),
	        'BYR' => array( 'rtwwwap_curr_name' => esc_html__( 'Belarusian ruble (old)', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#112;&#46;' ),
	        'BZD' => array( 'rtwwwap_curr_name' => esc_html__( 'Belize dollar', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#66;&#90;&#36;' ),
	        'CAD' => array( 'rtwwwap_curr_name' => esc_html__( 'Canadian dollar', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#36;' ),
	        'CDF' => array( 'rtwwwap_curr_name' => esc_html__( 'Congolese franc', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#70;&#67;' ),
	        'CHF' => array( 'rtwwwap_curr_name' => esc_html__( 'Swiss franc', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#67;&#72;&#70;' ),
	        'CLP' => array( 'rtwwwap_curr_name' => esc_html__( 'Chilean peso', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#36;' ),
	        'CNY' => array( 'rtwwwap_curr_name' => esc_html__( 'Chinese yuan', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#165;' ),
	        'COP' => array( 'rtwwwap_curr_name' => esc_html__( 'Colombian peso', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#36;' ),
	        'CRC' => array( 'rtwwwap_curr_name' => esc_html__( 'Costa Rican col&oacute;n', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#8353;' ),
	        'CUP' => array( 'rtwwwap_curr_name' => esc_html__( 'Cuban peso', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#8396;' ),
	        'CVE' => array( 'rtwwwap_curr_name' => esc_html__( 'Cape Verdean escudo', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#36;' ),
	        'CZK' => array( 'rtwwwap_curr_name' => esc_html__( 'Czech koruna', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#75;&#269;' ),
	        'DJF' => array( 'rtwwwap_curr_name' => esc_html__( 'Djiboutian franc', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#70;&#100;&#106;' ),
	        'DKK' => array( 'rtwwwap_curr_name' => esc_html__( 'Danish krone', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#107;&#114;' ),
	        'DOP' => array( 'rtwwwap_curr_name' => esc_html__( 'Dominican peso', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#82;&#68;&#36;' ),
	        'DZD' => array( 'rtwwwap_curr_name' => esc_html__( 'Algerian dinar', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#1583;&#1580;' ),
	        'EGP' => array( 'rtwwwap_curr_name' => esc_html__( 'Egyptian pound', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#163;' ),
	        'ETB' => array( 'rtwwwap_curr_name' => esc_html__( 'Ethiopian birr', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#66;&#114;' ),
	        'EUR' => array( 'rtwwwap_curr_name' => esc_html__( 'Euro', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#8364;' ),
	        'FJD' => array( 'rtwwwap_curr_name' => esc_html__( 'Fijian dollar', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#36;' ),
	        'FKP' => array( 'rtwwwap_curr_name' => esc_html__( 'Falkland Islands pound', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#163;' ),
	        'GBP' => array( 'rtwwwap_curr_name' => esc_html__( 'Pound sterling', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#163;' ),
	        'GEL' => array( 'rtwwwap_curr_name' => esc_html__( 'Georgian lari', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#4314;' ),
	        'GHS' => array( 'rtwwwap_curr_name' => esc_html__( 'Ghana cedi', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#162;' ),
	        'GIP' => array( 'rtwwwap_curr_name' => esc_html__( 'Gibraltar pound', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#163;' ),
	        'GMD' => array( 'rtwwwap_curr_name' => esc_html__( 'Gambian dalasi', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#68;' ),
	        'GNF' => array( 'rtwwwap_curr_name' => esc_html__( 'Guinean franc', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#70;&#71;' ),
	        'GTQ' => array( 'rtwwwap_curr_name' => esc_html__( 'Guatemalan quetzal', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#81;' ),
	        'GYD' => array( 'rtwwwap_curr_name' => esc_html__( 'Guyanese dollar', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#36;' ),
	        'HKD' => array( 'rtwwwap_curr_name' => esc_html__( 'Hong Kong dollar', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#36;' ),
	        'HNL' => array( 'rtwwwap_curr_name' => esc_html__( 'Honduran lempira', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#76;' ),
	        'HRK' => array( 'rtwwwap_curr_name' => esc_html__( 'Croatian kuna', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#107;&#110;' ),
	        'HTG' => array( 'rtwwwap_curr_name' => esc_html__( 'Haitian gourde', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#71;' ),
	        'HUF' => array( 'rtwwwap_curr_name' => esc_html__( 'Hungarian forint', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#70;&#116;' ),
	        'IDR' => array( 'rtwwwap_curr_name' => esc_html__( 'Indonesian rupiah', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#82;&#112;' ),
	        'ILS' => array( 'rtwwwap_curr_name' => esc_html__( 'Israeli new shekel', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#8362;' ),
	        'INR' => array( 'rtwwwap_curr_name' => esc_html__( 'Indian rupee', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#8377;' ),
	        'IQD' => array( 'rtwwwap_curr_name' => esc_html__( 'Iraqi dinar', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#1593;.&#1583;' ),
	        'IRR' => array( 'rtwwwap_curr_name' => esc_html__( 'Iranian rial', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#65020;' ),
	        'ISK' => array( 'rtwwwap_curr_name' => esc_html__( 'Icelandic kr&oacute;na', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#107;&#114;' ),
	        'JEP' => array( 'rtwwwap_curr_name' => esc_html__( 'Jersey pound', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#163;' ),
	        'JMD' => array( 'rtwwwap_curr_name' => esc_html__( 'Jamaican dollar', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#74;&#36;' ),
	        'JOD' => array( 'rtwwwap_curr_name' => esc_html__( 'Jordanian dinar', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#74;&#68;' ),
	        'JPY' => array( 'rtwwwap_curr_name' => esc_html__( 'Japanese yen', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#165;' ),
	        'KES' => array( 'rtwwwap_curr_name' => esc_html__( 'Kenyan shilling', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#75;&#83;&#104;' ),
	        'KGS' => array( 'rtwwwap_curr_name' => esc_html__( 'Kyrgyzstani som', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#1083;&#1074;' ),
	        'KHR' => array( 'rtwwwap_curr_name' => esc_html__( 'Cambodian riel', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#6107;' ),
	        'KMF' => array( 'rtwwwap_curr_name' => esc_html__( 'Comorian franc', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#67;&#70;' ),
	        'KPW' => array( 'rtwwwap_curr_name' => esc_html__( 'North Korean won', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#8361;' ),
	        'KRW' => array( 'rtwwwap_curr_name' => esc_html__( 'South Korean won', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#8361;' ),
	        'KWD' => array( 'rtwwwap_curr_name' => esc_html__( 'Kuwaiti dinar', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#1583;.&#1603;' ),
	        'KYD' => array( 'rtwwwap_curr_name' => esc_html__( 'Cayman Islands dollar', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#36;' ),
	        'KZT' => array( 'rtwwwap_curr_name' => esc_html__( 'Kazakhstani tenge', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#1083;&#1074;' ),
	        'LAK' => array( 'rtwwwap_curr_name' => esc_html__( 'Lao kip', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#8365;' ),
	        'LBP' => array( 'rtwwwap_curr_name' => esc_html__( 'Lebanese pound', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#163;' ),
	        'LKR' => array( 'rtwwwap_curr_name' => esc_html__( 'Sri Lankan rupee', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#8360;' ),
	        'LRD' => array( 'rtwwwap_curr_name' => esc_html__( 'Liberian dollar', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#36;' ),
	        'LYD' => array( 'rtwwwap_curr_name' => esc_html__( 'Libyan dinar', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#1604;.&#1583;' ),
	        'MAD' => array( 'rtwwwap_curr_name' => esc_html__( 'Moroccan dirham', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#1583;.&#1605;.' ),
	        'MDL' => array( 'rtwwwap_curr_name' => esc_html__( 'Moldovan leu', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#76;' ),
	        'MGA' => array( 'rtwwwap_curr_name' => esc_html__( 'Malagasy ariary', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#65;&#114;' ),
	        'MKD' => array( 'rtwwwap_curr_name' => esc_html__( 'Macedonian denar', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#1076;&#1077;&#1085;' ),
	        'MMK' => array( 'rtwwwap_curr_name' => esc_html__( 'Burmese kyat', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#75;' ),
	        'MNT' => array( 'rtwwwap_curr_name' => esc_html__( 'Mongolian t&ouml;gr&ouml;g', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#8366;' ),
	        'MOP' => array( 'rtwwwap_curr_name' => esc_html__( 'Macanese pataca', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#77;&#79;&#80;&#36;' ),
	        'MRO' => array( 'rtwwwap_curr_name' => esc_html__( 'Mauritanian ouguiya', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#85;&#77;' ),
	        'MUR' => array( 'rtwwwap_curr_name' => esc_html__( 'Mauritian rupee', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#8360;' ),
	        'MVR' => array( 'rtwwwap_curr_name' => esc_html__( 'Maldivian rufiyaa', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '.&#1923;' ),
	        'MWK' => array( 'rtwwwap_curr_name' => esc_html__( 'Malawian kwacha', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#77;&#75;' ),
	        'MXN' => array( 'rtwwwap_curr_name' => esc_html__( 'Mexican peso', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#36;' ),
	        'MYR' => array( 'rtwwwap_curr_name' => esc_html__( 'Malaysian ringgit', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#82;&#77;' ),
	        'MZN' => array( 'rtwwwap_curr_name' => esc_html__( 'Mozambican metical', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#77;&#84;' ),
	        'NAD' => array( 'rtwwwap_curr_name' => esc_html__( 'Namibian dollar', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#36;' ),
	        'NGN' => array( 'rtwwwap_curr_name' => esc_html__( 'Nigerian naira', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#8358;' ),
	        'NIO' => array( 'rtwwwap_curr_name' => esc_html__( 'Nicaraguan c&oacute;rdoba', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#67;&#36;' ),
	        'NOK' => array( 'rtwwwap_curr_name' => esc_html__( 'Norwegian krone', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#107;&#114;' ),
	        'NPR' => array( 'rtwwwap_curr_name' => esc_html__( 'Nepalese rupee', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#8360;' ),
	        'NZD' => array( 'rtwwwap_curr_name' => esc_html__( 'New Zealand dollar', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#36;' ),
	        'OMR' => array( 'rtwwwap_curr_name' => esc_html__( 'Omani rial', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#65020;' ),
	        'PAB' => array( 'rtwwwap_curr_name' => esc_html__( 'Panamanian balboa', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#66;&#47;&#46;' ),
	        'PEN' => array( 'rtwwwap_curr_name' => esc_html__( 'Sol', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#83;&#47;&#46;' ),
	        'PGK' => array( 'rtwwwap_curr_name' => esc_html__( 'Papua New Guinean kina', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#75;' ),
	        'PHP' => array( 'rtwwwap_curr_name' => esc_html__( 'Philippine peso', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#8369;' ),
	        'PKR' => array( 'rtwwwap_curr_name' => esc_html__( 'Pakistani rupee', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#8360;' ),
	        'PLN' => array( 'rtwwwap_curr_name' => esc_html__( 'Polish z&#x142;oty', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#122;&#322;' ),
	        'PRB' => array( 'rtwwwap_curr_name' => esc_html__( 'Transnistrian ruble', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#36;' ),
	        'PYG' => array( 'rtwwwap_curr_name' => esc_html__( 'Paraguayan guaran&iacute;', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#71;&#115;' ),
	        'QAR' => array( 'rtwwwap_curr_name' => esc_html__( 'Qatari riyal', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#65020;' ),
	        'RON' => array( 'rtwwwap_curr_name' => esc_html__( 'Romanian leu', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#108;&#101;&#105;' ),
	        'RSD' => array( 'rtwwwap_curr_name' => esc_html__( 'Serbian dinar', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#1044;&#1080;&#1085;&#46;' ),
	        'RUB' => array( 'rtwwwap_curr_name' => esc_html__( 'Russian ruble', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#1088;&#1091;&#1073;' ),
	        'RWF' => array( 'rtwwwap_curr_name' => esc_html__( 'Rwandan franc', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#1585;.&#1587;' ),
	        'SAR' => array( 'rtwwwap_curr_name' => esc_html__( 'Saudi riyal', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#65020;' ),
	        'SBD' => array( 'rtwwwap_curr_name' => esc_html__( 'Solomon Islands dollar', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#36;' ),
	        'SCR' => array( 'rtwwwap_curr_name' => esc_html__( 'Seychellois rupee', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#8360;' ),
	        'SDG' => array( 'rtwwwap_curr_name' => esc_html__( 'Sudanese pound', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#163;' ),
	        'SEK' => array( 'rtwwwap_curr_name' => esc_html__( 'Swedish krona', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#107;&#114;' ),
	        'SGD' => array( 'rtwwwap_curr_name' => esc_html__( 'Singapore dollar', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#36;' ),
	        'SHP' => array( 'rtwwwap_curr_name' => esc_html__( 'Saint Helena pound', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#163;' ),
	        'SLL' => array( 'rtwwwap_curr_name' => esc_html__( 'Sierra Leonean leone', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#76;&#101;' ),
	        'SOS' => array( 'rtwwwap_curr_name' => esc_html__( 'Somali shilling', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#83;' ),
	        'SRD' => array( 'rtwwwap_curr_name' => esc_html__( 'Surinamese dollar', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#36;' ),
	        'STD' => array( 'rtwwwap_curr_name' => esc_html__( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe dobra', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#68;&#98;' ),
	        'SYP' => array( 'rtwwwap_curr_name' => esc_html__( 'Syrian pound', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#163;' ),
	        'SZL' => array( 'rtwwwap_curr_name' => esc_html__( 'Swazi lilangeni', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#76;' ),
	        'THB' => array( 'rtwwwap_curr_name' => esc_html__( 'Thai baht', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#3647;' ),
	        'TJS' => array( 'rtwwwap_curr_name' => esc_html__( 'Tajikistani somoni', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#84;&#74;&#83;' ),
	        'TMT' => array( 'rtwwwap_curr_name' => esc_html__( 'Turkmenistan manat', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#109;' ),
	        'TND' => array( 'rtwwwap_curr_name' => esc_html__( 'Tunisian dinar', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#1583;.&#1578;' ),
	        'TOP' => array( 'rtwwwap_curr_name' => esc_html__( 'Tongan pa&#x2bb;anga', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#84;&#36;' ),
	        'TRY' => array( 'rtwwwap_curr_name' => esc_html__( 'Turkish lira', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#8356;' ),
	        'TTD' => array( 'rtwwwap_curr_name' => esc_html__( 'Trinidad and Tobago dollar', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#36;' ),
	        'TWD' => array( 'rtwwwap_curr_name' => esc_html__( 'New Taiwan dollar', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#78;&#84;&#36;' ),
	        'UAH' => array( 'rtwwwap_curr_name' => esc_html__( 'Ukrainian hryvnia', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#8372;' ),
	        'UGX' => array( 'rtwwwap_curr_name' => esc_html__( 'Ugandan shilling', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#85;&#83;&#104;' ),
	        'USD' => array( 'rtwwwap_curr_name' => esc_html__( 'United States (US) dollar', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#36;' ),
	        'UYU' => array( 'rtwwwap_curr_name' => esc_html__( 'Uruguayan peso', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#36;&#85;' ),
	        'UZS' => array( 'rtwwwap_curr_name' => esc_html__( 'Uzbekistani som', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#1083;&#1074;' ),
	        'VEF' => array( 'rtwwwap_curr_name' => esc_html__( 'Venezuelan bol&iacute;var', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#66;&#115;' ),
	        'VND' => array( 'rtwwwap_curr_name' => esc_html__( 'Vietnamese &#x111;&#x1ed3;ng', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#8363;' ),
	        'VUV' => array( 'rtwwwap_curr_name' => esc_html__( 'Vanuatu vatu', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#86;&#84;' ),
	        'WST' => array( 'rtwwwap_curr_name' => esc_html__( 'Samoan t&#x101;l&#x101;', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#87;&#83;&#36;' ),
	        'XAF' => array( 'rtwwwap_curr_name' => esc_html__( 'Central African CFA franc', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#70;&#67;&#70;&#65;' ),
	        'XCD' => array( 'rtwwwap_curr_name' => esc_html__( 'East Caribbean dollar', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#36;' ),
	        'XPF' => array( 'rtwwwap_curr_name' => esc_html__( 'CFP franc', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#70;' ),
	        'YER' => array( 'rtwwwap_curr_name' => esc_html__( 'Yemeni rial', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#65020;' ),
	        'ZAR' => array( 'rtwwwap_curr_name' => esc_html__( 'South African rand', 'rtwwwap-wp-wc-affiliate-program' ), 'rtwwwap_curr_symbol' => '&#82;' )
        );

		return $rtwwwap_currencies;
	}

	/**
     * To get Currency symbol
	 * @since 2.1.3
     */
	public function rtwwwap_curr_symbol( $rtwwwap_currency = 'USD' )
	{
		$rtwwwap_all_curr 	= $this->RtwWwapCurrencies();
		$rtwwwap_symbol 	= $rtwwwap_all_curr[ $rtwwwap_currency ];
		$rtwwwap_symbol 	= $rtwwwap_symbol[ 'rtwwwap_curr_symbol' ];

		return $rtwwwap_symbol;
	}
}