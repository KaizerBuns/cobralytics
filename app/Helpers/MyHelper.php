<?php
namespace App\Helpers;

class MyHelper {

	public static function print_rf($data, $die = false) {
		echo "<pre>".print_r($data, true)."</pre>";
		if($die) {
			die('---end');
		}
	}

	public static function get_controller_action() {
		$request = $_SERVER['REQUEST_URI'];
		$request = parse_url($request);
		return $request['path'];
	}

	public static function page_url($tablemap = false) {
		$request = $_SERVER['REQUEST_URI'];
		$request = parse_url($request);
		
		$path = $request['path'];
		$string = $request['query'];
		parse_str($string, $query);
		unset($query['page']);

		if($tablemap) {
			if(isset($query['sort'])) {
				unset($query['sort']);
				unset($query['order']);
			}
		}

		$url_page = $path .'?'.http_build_query($query);
		return $url_page;
	}

	/**
	 * 0 - ASC
	 * 1 - DESC 
	 * @param unknown_type $data
	 * @param unknown_type $member
	 * @param unknown_type $direction
	 * @return unknown
	 */
	public static function sort_object($data, $member, $direction) 
	{
		//reset array;
		sort($data);
		
		for ($i = count($data) - 1; $i >= 0; $i--) 
		{
			$swapped = false;
			
			for ($j = 0; $j < $i; $j++)
			{
				if ($direction == 1)
				{
					if ( $data[$j]->{$member} < $data[$j + 1]->{$member} ) 
					{

						$tmp = $data[$j];
		                $data[$j] = $data[$j + 1];
		                $data[$j + 1] = $tmp;
		                $swapped = true;
					}
				}
				else 
				{
					if ( $data[$j]->{$member} > $data[$j + 1]->{$member} ) 
					{
						$tmp = $data[$j];
		                $data[$j] = $data[$j + 1];
		                $data[$j + 1] = $tmp;
		                $swapped = true;
					}
				}
			}
			
			if (!$swapped) 
			{
				return $data;
			}
		}
	}

	/**
	 * 0 - ASC
	 * 1 - DESC
	 * @param unknown_type $data
	 * @param unknown_type $member
	 * @param unknown_type $direction
	 * @return unknown
	 */

	public static function sort_array($data, $member, $direction) 
	{
		//reset array;
		sort($data);
		for ($i = count($data) - 1; $i >= 0; $i--) 
		{
			$swapped = false;
			
			for ($j = 0; $j < $i; $j++) 
			{
				if ($direction == 1)
				{
					if ( $data[$j][$member] < $data[$j + 1][$member] ) 
					{
						$tmp = $data[$j];
		                $data[$j] = $data[$j + 1];
		                $data[$j + 1] = $tmp;
		                $swapped = true;
					}
				}
				else 
				{
					if ( $data[$j][$member] > $data[$j + 1][$member] ) 
					{
						$tmp = $data[$j];
		                $data[$j] = $data[$j + 1];
		                $data[$j + 1] = $tmp;
		                $swapped = true;
					}
				}
			}
			if (!$swapped) 
			{
				return $data;
			}
		}
	}

	public static function array_remove_empty($arr){
	    $narr = array();
	    while(list($key, $val) = each($arr)){
	        if (is_array($val)){
	            $val = array_remove_empty($val);
	            // does the result array contain anything?
	            if (count($val)!=0){
	                // yes :-)
	                $narr[$key] = $val;
	            }
	        }
	        else {
	            if (trim($val) != ""){
	                $narr[$key] = $val;
	            }
	        }
	    }
	    unset($arr);
	    return $narr;
	}

	public static function implode_on_field($delimiter, Array $objects, $field)
	{
		$d = array();
		foreach ($objects as $ob) {
			if(is_object($ob)) {
				$d[] = $ob->{$field};
			} else {
				$d[] = $ob[$field];
			}
		}
		return implode($delimiter, $d);
	}

	public static function trim_value($value) {
		$value = trim($value);
		return $value;
	}

	public static function makeUserAgentDropDown($user_agent = null) {
		$user_agent = trim($user_agent);
		$dropdown = '';
		$list = array(
			'ie' => 'Internet Explorer',
			'firefox' => 'Firefox',
			'chrome' => 'Chrome',
			'safari' => 'Safari',
			'opera' => 'Opera',
			'iphone' => 'Iphone',
			'android' => 'Android',
			'blackberry' => 'Blackberry',
			'windows' => 'Windows Mobile'
		);
		foreach($list as $key=>$val) {
			$selected = "";
			if(strtolower($key) == strtolower($user_agent) || strtolower($val) == strtolower($user_agent)) {
				$selected = " selected='selected'";
			}
			$dropdown .= "<option value=\"$key\"$selected>$val</option>";
		}
		return $dropdown;
	}

	public static function makeCountryDropDown($country='', $return_array = false) {
		$country = trim($country);
		$dropdown = "";
		$list = array(
				'US'=>'United States',
				'CA'=>'Canada',
				'AF'=>'Afghanistan',
				'AL'=>'Albania',
				'DZ'=>'Algeria',
				'AS'=>'American Samoa',
				'AD'=>'Andorra',
				'AO'=>'Angola',
				'AI'=>'Anguilla',
				'AQ'=>'Antarctica',
				'AG'=>'Antigua And Barbuda',
				'AR'=>'Argentina',
				'AM'=>'Armenia',
				'AW'=>'Aruba',
				'AU'=>'Australia',
				'AT'=>'Austria',
				'AZ'=>'Azerbaijan',
				'BS'=>'Bahamas',
				'BH'=>'Bahrain',
				'BD'=>'Bangladesh',
				'BB'=>'Barbados',
				'BY'=>'Belarus',
				'BE'=>'Belgium',
				'BZ'=>'Belize',
				'BJ'=>'Benin',
				'BM'=>'Bermuda',
				'BT'=>'Bhutan',
				'BO'=>'Bolivia',
				'BA'=>'Bosnia And Herzegovina',
				'BW'=>'Botswana',
				'BV'=>'Bouvet Island',
				'BR'=>'Brazil',
				'IO'=>'British Indian Ocean Territory',
				'BN'=>'Brunei',
				'BG'=>'Bulgaria',
				'BF'=>'Burkina Faso',
				'BI'=>'Burundi',
				'KH'=>'Cambodia',
				'CM'=>'Cameroon',
				'CV'=>'Cape Verde',
				'KY'=>'Cayman Islands',
				'CF'=>'Central African Republic',
				'TD'=>'Chad',
				'CL'=>'Chile',
				'CN'=>'China',
				'CX'=>'Christmas Island',
				'CC'=>'Cocos (Keeling) Islands',
				'CO'=>'Columbia',
				'KM'=>'Comoros',
				'CG'=>'Congo',
				'CK'=>'Cook Islands',
				'CR'=>'Costa Rica',
				'CI'=>'Cote D\'Ivorie (Ivory Coast)',
				'HR'=>'Croatia (Hrvatska)',
				'CU'=>'Cuba',
				'CY'=>'Cyprus',
				'CZ'=>'Czech Republic',
				'CD'=>'Democratic Republic Of Congo (Zaire)',
				'DK'=>'Denmark',
				'DJ'=>'Djibouti',
				'DM'=>'Dominica',
				'DO'=>'Dominican Republic',
				'TP'=>'East Timor',
				'EC'=>'Ecuador',
				'EG'=>'Egypt',
				'SV'=>'El Salvador',
				'GQ'=>'Equatorial Guinea',
				'ER'=>'Eritrea',
				'EE'=>'Estonia',
				'ET'=>'Ethiopia',
				'FK'=>'Falkland Islands (Malvinas)',
				'FO'=>'Faroe Islands',
				'FJ'=>'Fiji',
				'FI'=>'Finland',
				'FR'=>'France',
				'FX'=>'France, Metropolitan',
				'GF'=>'French Guinea',
				'PF'=>'French Polynesia',
				'TF'=>'French Southern Territories',
				'GA'=>'Gabon',
				'GM'=>'Gambia',
				'GE'=>'Georgia',
				'DE'=>'Germany',
				'GH'=>'Ghana',
				'GI'=>'Gibraltar',
				'GR'=>'Greece',
				'GL'=>'Greenland',
				'GD'=>'Grenada',
				'GP'=>'Guadeloupe',
				'GU'=>'Guam',
				'GT'=>'Guatemala',
				'GN'=>'Guinea',
				'GW'=>'Guinea-Bissau',
				'GY'=>'Guyana',
				'HT'=>'Haiti',
				'HM'=>'Heard And McDonald Islands',
				'HN'=>'Honduras',
				'HK'=>'Hong Kong',
				'HU'=>'Hungary',
				'IS'=>'Iceland',
				'IN'=>'India',
				'ID'=>'Indonesia',
				'IR'=>'Iran',
				'IQ'=>'Iraq',
				'IE'=>'Ireland',
				'IL'=>'Israel',
				'IT'=>'Italy',
				'JM'=>'Jamaica',
				'JP'=>'Japan',
				'JO'=>'Jordan',
				'KZ'=>'Kazakhstan',
				'KE'=>'Kenya',
				'KI'=>'Kiribati',
				'KW'=>'Kuwait',
				'KG'=>'Kyrgyzstan',
				'LA'=>'Laos',
				'LV'=>'Latvia',
				'LB'=>'Lebanon',
				'LS'=>'Lesotho',
				'LR'=>'Liberia',
				'LY'=>'Libya',
				'LI'=>'Liechtenstein',
				'LT'=>'Lithuania',
				'LU'=>'Luxembourg',
				'MO'=>'Macau',
				'MK'=>'Macedonia',
				'MG'=>'Madagascar',
				'MW'=>'Malawi',
				'MY'=>'Malaysia',
				'MV'=>'Maldives',
				'ML'=>'Mali',
				'MT'=>'Malta',
				'MH'=>'Marshall Islands',
				'MQ'=>'Martinique',
				'MR'=>'Mauritania',
				'MU'=>'Mauritius',
				'YT'=>'Mayotte',
				'MX'=>'Mexico',
				'FM'=>'Micronesia',
				'MD'=>'Moldova',
				'MC'=>'Monaco',
				'MN'=>'Mongolia',
				'MS'=>'Montserrat',
				'MA'=>'Morocco',
				'MZ'=>'Mozambique',
				'MM'=>'Myanmar (Burma)',
				'NA'=>'Namibia',
				'NR'=>'Nauru',
				'NP'=>'Nepal',
				'NL'=>'Netherlands',
				'AN'=>'Netherlands Antilles',
				'NC'=>'New Caledonia',
				'NZ'=>'New Zealand',
				'NI'=>'Nicaragua',
				'NE'=>'Niger',
				'NG'=>'Nigeria',
				'NU'=>'Niue',
				'NF'=>'Norfolk Island',
				'KP'=>'North Korea',
				'MP'=>'Northern Mariana Islands',
				'NO'=>'Norway',
				'OM'=>'Oman',
				'PK'=>'Pakistan',
				'PW'=>'Palau',
				'PA'=>'Panama',
				'PG'=>'Papua New Guinea',
				'PY'=>'Paraguay',
				'PE'=>'Peru',
				'PH'=>'Philippines',
				'PN'=>'Pitcairn',
				'PL'=>'Poland',
				'PT'=>'Portugal',
				'PR'=>'Puerto Rico',
				'QA'=>'Qatar',
				'RE'=>'Reunion',
				'RO'=>'Romania',
				'RU'=>'Russia',
				'RW'=>'Rwanda',
				'SH'=>'Saint Helena',
				'KN'=>'Saint Kitts And Nevis',
				'LC'=>'Saint Lucia',
				'PM'=>'Saint Pierre And Miquelon',
				'VC'=>'Saint Vincent And The Grenadines',
				'SM'=>'San Marino',
				'ST'=>'Sao Tome And Principe',
				'SA'=>'Saudi Arabia',
				'SN'=>'Senegal',
				'SC'=>'Seychelles',
				'SL'=>'Sierra Leone',
				'SG'=>'Singapore',
				'SK'=>'Slovak Republic',
				'SI'=>'Slovenia',
				'SB'=>'Solomon Islands',
				'SO'=>'Somalia',
				'ZA'=>'South Africa',
				'GS'=>'South Georgia And South Sandwich Islands',
				'KR'=>'South Korea',
				'ES'=>'Spain',
				'LK'=>'Sri Lanka',
				'SD'=>'Sudan',
				'SR'=>'Suriname',
				'SJ'=>'Svalbard And Jan Mayen',
				'SZ'=>'Swaziland',
				'SE'=>'Sweden',
				'CH'=>'Switzerland',
				'SY'=>'Syria',
				'TW'=>'Taiwan',
				'TJ'=>'Tajikistan',
				'TZ'=>'Tanzania',
				'TH'=>'Thailand',
				'TG'=>'Togo',
				'TK'=>'Tokelau',
				'TO'=>'Tonga',
				'TT'=>'Trinidad And Tobago',
				'TN'=>'Tunisia',
				'TR'=>'Turkey',
				'TM'=>'Turkmenistan',
				'TC'=>'Turks And Caicos Islands',
				'TV'=>'Tuvalu',
				'UG'=>'Uganda',
				'UA'=>'Ukraine',
				'AE'=>'United Arab Emirates',
				'GB'=>'United Kingdom',
				'UM'=>'United States Minor Outlying Islands',
				'UY'=>'Uruguay',
				'UZ'=>'Uzbekistan',
				'VU'=>'Vanuatu',
				'VA'=>'Vatican City (Holy See)',
				'VE'=>'Venezuela',
				'VN'=>'Vietnam',
				'VG'=>'Virgin Islands (British)',
				'VI'=>'Virgin Islands (US)',
				'WF'=>'Wallis And Futuna Islands',
				'EH'=>'Western Sahara',
				'WS'=>'Western Samoa',
				'YE'=>'Yemen',
				'YU'=>'Yugoslavia',
				'ZM'=>'Zambia',
				'ZW'=>'Zimbabwe');
		foreach($list as $key=>$val) {
			$selected = "";
			if(strtolower($key) == strtolower($country) || strtolower($val) == strtolower($country)) {
				$selected = " selected='selected'";
			}
			$dropdown .= "<option value=\"$key\"$selected>$val</option>";
		}
		return $dropdown;
	}


	public static function makeStateDropDown($country='US',$state='', $return_array = false) {

		$state = trim($state);
		$dropdown = "";
		$states = array(
		"CA" => array(
			"Alberta" => "AB",
			"British Columbia" => "BC",
			"Manitoba" => "MB",
			"New Brunswick" => "NB",
			"Newfoundland and Labrador" => "NL",
			"Northwest Territories" => "NT",
			"Nova Scotia" => "NS",
			"Nunavut" => "NU",
			"Ontario" => "ON",
			"Prince Edward Island" => "PE",
			"Quebec" => "QC",
			"Saskatchewan" => "SK",
			"Yukon" => "YT"	
			),
		"US" => array(
			"Alabama" => "AL",
			"Alaska" => "AK",
			"Arizona" => "AZ",
			"Arkansas" => "AR",
			"California" => "CA",
			"Colorado" => "CO",
			"Connecticut" => "CT",
			"Delaware" => "DE",
			"Florida" => "FL",
			"Georgia" => "GA",
			"Hawaii" => "HI",
			"Idaho" => "ID",
			"Illinois" => "IL",
			"Indiana" => "IN",
			"Iowa" => "IA",
			"Kansas" => "KS",
			"Kentucky" => "KY",
			"Louisiana" => "LA",
			"Maine" => "ME",
			"Maryland" => "MD",
			"Massachusetts" => "MA",
			"Michigan" => "MI",
			"Minnesota" => "MN",
			"Mississippi" => "MS",
			"Missouri" => "MO",
			"Montana" => "MT",
			"Nebraska" => "NE",
			"Nevada" => "NV",
			"New Hampshire" => "NH",
			"New Jersey" => "NJ",
			"New Mexico" => "NM",
			"New York" => "NY",
			"North Carolina" => "NC",
			"North Dakota" => "ND",
			"Ohio" => "OH",
			"Oklahoma" => "OK",
			"Oregon" => "OR",
			"Pennsylvania" => "PA",
			"Rhode Island" => "RI",
			"South Carolina" => "SC",
			"South Dakota" => "SD",
			"Tennessee" => "TN",
			"Texas" => "TX",
			"Utah" => "UT",
			"Vermont" => "VT",
			"Virginia" => "VA",
			"Washington" => "WA",
			"Washington DC" => "DC",
			"West Virginia" => "WV",
			"Wisconsin" => "WI",
			"Wyoming" => "WY"
		));
		
		if ($country == 'US' || $country == 'CA')
			$states = $states[$country];
		else
			$states = array();
			
		foreach((array)$states as $key=>$val) {

			$selected = "";
			if(strtolower($key) == strtolower($state) || strtolower($val) == strtolower($state)) {
				$selected = " selected";
			}
			$dropdown .= "<option value=\"$val\"$selected>$key</option>";

		}
		return $dropdown;
	}

	public static function json_encode2($param) {
	    if (is_object($param) || is_array($param)) {
	        $param = object_to_array($param);
	    }
	    return json_encode($param);
	}
	
	public static function object_to_array($var) {
	    $result = array();
	    $references = array();

	    // loop over elements/properties
	    foreach ($var as $key => $value) {
	        // recursively convert objects
	        if (is_object($value) || is_array($value)) {
	            // but prevent cycles
	            if (!in_array($value, $references)) {
	                $result[$key] = object_to_array($value);
	                $references[] = $value;
	            }
	        } else {
	            // simple values are untouched
	            $result[$key] = $value;
	        }
	    }
	    return $result;
	}

	/**
	 * Generates a short url that must contain letters and numbers and must be the size of numAlpha
	 *
	 * Can be used to create MiniURLs or JumplinkIDs
	 * 
	 * @param unknown_type $numAlpha
	 * @return unknown
	*/
	public static function generate_numAlpha($numAlpha = 6)
	{
		$listAlpha = 'aAbBcCdDeEfFgGhHiIjJkKlLmMnNoOpPqQrRsStTuUvVwWxXyYzZ0123456789';
	  		
		do{
			$short_url = str_shuffle(substr(str_shuffle($listAlpha),0,$numAlpha));
		}while(!preg_match("/^.*(?=.{$numAlpha})(?=.*\d)(?=.*[a-zA-Z]).*$/",trim($short_url)));
		   		
		return $short_url;
	}

	public static function array_multi_merge ($array1, $array2) {
	    if (is_array($array2) && count($array2)) {
	      foreach ($array2 as $k => $v) {
	        if (is_array($v) && count($v)) {
	          $array1[$k] = array_multi_merge($array1[$k], $v);
	        } else {
	          $array1[$k] = $v;
	        }
	      }
	    } else {
	      $array1 = $array2;
	    }

	    return $array1;
	}

	public static function get_time() {
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}

	public static function get_elapsed($prev_time) {
		$now = get_time();
		return round((($now - $prev_time)/60), 4);
	}

	public static function print_html($str) {
		$str = str_replace("<", "&lt;", $str);
		$str = str_replace(">", "&gt;", $str);
		//chrome hack because &reg turns into � and chrome sucks
		$str = str_replace("&reg", "&&#114;&#101;&#103;", $str);
		return $str;
	}

	/**
	 * Convert BR tags to nl
	 *
	 * @param string The string to convert
	 * @return string The converted string
	 */
	public static function br2nl($string)
	{
	    return preg_replace('/\<br(\s*)?\/?\>/i', "\n", $string);
	}

	public static function replace_links( $text ) {	
	    $text = preg_replace('#(script|about|applet|activex|chrome):#is', "\\1:", $text);
	     
	    $ret = ' ' . $text;
	    // Replace Links with http://
	    $ret = preg_replace("#(^|[\n ])([\w]+?://[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"\\2\" target=\"_blank\" rel=\"nofollow\">\\2</a>", $ret);
	     
	    // Replace Links without http://
	    $ret = preg_replace("#(^|[\n ])((www|ftp)\.[\w\#$%&~/.\-;:=,?@\[\]+]*)#is", "\\1<a href=\"http://\\2\" target=\"_blank\" rel=\"nofollow\">\\2</a>", $ret);
	     
	    // Replace Email Addresses
	    $ret = preg_replace("#(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $ret);
	    $ret = substr($ret, 1);
	     
	    return $ret;
	}



	/**
	 * Format YYYY-MM-DD
	 */
	public static function is_date($date) 
	{
		$tmp = explode("-", $date);
		if(count($tmp) != 3) {
			return false;
		}
		
		return checkdate($tmp[1], $tmp[2], $tmp[0]);
		
	}

	public static function get_real_ip()
	{
		//$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
		//$_SERVER['HTTP_X_FORWARDED_FOR'] = '10.208.4.38, 10.10.300.23, 127.0.0.1, 58.163.175.187';	
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
			$ip=$_SERVER['HTTP_CLIENT_IP'];
		} else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  //to check ip is pass from proxy
			$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip=$_SERVER['REMOTE_ADDR'];
		}
			
		$ips = explode(",", $ip);
		$real_ip = $_SERVER['REMOTE_ADDR']; //fail safe
		foreach($ips as $ip) {
			$ip = trim($ip);
			if(preg_match('/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/', $ip)) {
				if(!preg_match('/(^127\.0\.0\.1)|(^10\.)|(^172\.1[6-9]\.)|(^172\.2[0-9]\.)|(^172\.3[0-1]\.)|(^192\.168\.)/', $ip)) {
					$real_ip = $ip;
					break;
				}			
			}
		}
		
		return $real_ip;
	}

	public static function strip_html_tags( $text )
	{
		// PHP's strip_tags() public static function will remove tags, but it
		// doesn't remove scripts, styles, and other unwanted
		// invisible text between tags.  Also, as a prelude to
		// tokenizing the text, we need to insure that when
		// block-level tags (such as <p> or <div>) are removed,
		// neighboring words aren't joined.
		$text = preg_replace(
			array(
				// Remove invisible content
				'@<head[^>]*?>.*?</head>@siu',
				'@<style[^>]*?>.*?</style>@siu',
				'@<script[^>]*?.*?</script>@siu',
				'@<object[^>]*?.*?</object>@siu',
				'@<embed[^>]*?.*?</embed>@siu',
				'@<applet[^>]*?.*?</applet>@siu',
				'@<noframes[^>]*?.*?</noframes>@siu',
				'@<noscript[^>]*?.*?</noscript>@siu',
				'@<noembed[^>]*?.*?</noembed>@siu',

				// Add line breaks before & after blocks
				'@<((br)|(hr))@iu',
				'@</?((address)|(blockquote)|(center)|(del))@iu',
				'@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
				'@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
				'@</?((table)|(th)|(td)|(caption))@iu',
				'@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
				'@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
				'@</?((frameset)|(frame)|(iframe))@iu',
			),
			array(
				' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
				"\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
				"\n\$0", "\n\$0",
			),
			$text );

		// Remove all remaining tags and comments and return.
		return strip_tags( $text );
	}

	public static function get_time_ago($time_stamp)
	{
	    $time_difference = strtotime('now') - $time_stamp;

	    if ($time_difference >= 60 * 60 * 24 * 365.242199)
	    {
	        /*
	         * 60 seconds/minute * 60 minutes/hour * 24 hours/day * 365.242199 days/year
	         * This means that the time difference is 1 year or more
	         */
	        //return get_time_ago_string($time_stamp, 60 * 60 * 24 * 365.242199, 'year');
			return date("M d Y", $time_stamp);
	    }
	    elseif ($time_difference >= 60 * 60 * 24 * 30.4368499)
	    {
	        /*
	         * 60 seconds/minute * 60 minutes/hour * 24 hours/day * 30.4368499 days/month
	         * This means that the time difference is 1 month or more
	         */
	        //return get_time_ago_string($time_stamp, 60 * 60 * 24 * 30.4368499, 'month');
			return date("M d", $time_stamp);
	    }
	    elseif ($time_difference >= 60 * 60 * 24 * 7)
	    {
	        /*
	         * 60 seconds/minute * 60 minutes/hour * 24 hours/day * 7 days/week
	         * This means that the time difference is 1 week or more
	         */
	        //return get_time_ago_string($time_stamp, 60 * 60 * 24 * 7, 'week');
			return date("M d", $time_stamp);
	    }
	    elseif ($time_difference >= 60 * 60 * 24)
	    {
	        /*
	         * 60 seconds/minute * 60 minutes/hour * 24 hours/day
	         * This means that the time difference is 1 day or more
	         */
	        return date("M d", $time_stamp);//get_time_ago_string($time_stamp, 60 * 60 * 24, 'day');
	    }
	    elseif ($time_difference >= 60 * 60)
	    {
	        /*
	         * 60 seconds/minute * 60 minutes/hour
	         * This means that the time difference is 1 hour or more
	         */
	        return get_time_ago_string($time_stamp, 60 * 60, 'hour');
	    }
	    else
	    {
	        /*
	         * 60 seconds/minute
	         * This means that the time difference is a matter of minutes
	         */
	        return get_time_ago_string($time_stamp, 60, 'minute');
	    }
	}

	public static function get_time_ago_string($time_stamp, $divisor, $time_unit)
	{
	    $time_difference = strtotime("now") - $time_stamp;
	    $time_units      = floor($time_difference / $divisor);

	    settype($time_units, 'string');

	    if ($time_units === '0')
	    {
	        return 'less than 1 ' . $time_unit . ' ago';
	    }
	    elseif ($time_units === '1')
	    {
	        return '1 ' . $time_unit . ' ago';
	    }
	    else
	    {
	        /*
	         * More than "1" $time_unit. This is the "plural" message.
	         */
	        // TODO: This pluralizes the time unit, which is done by adding "s" at the end; this will not work for i18n!
	        return $time_units . ' ' . $time_unit . 's ago';
	    }
	}

	public static function nlclean($text) {
		$text = str_replace(array("\n","\r\n","\r"),"", $text);
		return $text;
	}

	public static function rekey_array($data, $column_key) {
		$new_data = array();
		foreach($data as $d) {
			$new_d = $d;
			if(is_object($new_d)) {
				$new_data[$new_d->$column_key] = $new_d;
			} else {
				$new_data[$new_d[$column_key]] = $new_d;
			}
		}
		return $new_data;
	}

	public static function clean_domain($name) 
	{
		$name = strtolower($name);
		$name = str_replace(array("http://","https://"), '', $name);
		$name = self::nlclean($name);
		return $name;
	}
}