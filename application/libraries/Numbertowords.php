<?php
if (!defined('BASEPATH'))
  exit('No direct script access allowed');

class Numbertowords {

	function convert_number_old($number) {
		if (($number < 0) || ($number > 999999999)) {
			throw new Exception("Number is out of range");
		}

		$Gn = floor($number / 1000000);
		/* Millions (giga) */
		$number -= $Gn * 1000000;
		$kn = floor($number / 1000);
		/* Thousands (kilo) */
		$number -= $kn * 1000;
		$Hn = floor($number / 100);
		/* Hundreds (hecto) */
		$number -= $Hn * 100;
		$Dn = floor($number / 10);
		/* Tens (deca) */
		$n = $number % 10;
		/* Ones */

		$res = "";

		if ($Gn) {
			$res .= $this->convert_number($Gn) .  "Million";
		}

		if ($kn) {
			$res .= (empty($res) ? "" : " ") .$this->convert_number($kn) . " Thousand";
		}

		if ($Hn) {
			$res .= (empty($res) ? "" : " ") .$this->convert_number($Hn) . " Hundred";
		}

		$ones = array("", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen", "Nineteen");
		$tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty", "Seventy", "Eigthy", "Ninety");

		if ($Dn || $n) {
			if (!empty($res)) {
				$res .= " and ";
			}

			if ($Dn < 2) {
				$res .= $ones[$Dn * 10 + $n];
			} else {
				$res .= $tens[$Dn];

				if ($n) {
					$res .= "-" . $ones[$n];
				}
			}
		}

		if (empty($res)) {
			$res = "zero";
		}

		return $res;
	}

	function convert_number1($number) {

	    $hyphen      = '-';
	    $conjunction = ' and ';
	    $separator   = ', ';
	    $negative    = 'negative ';
	    $decimal     = ' point ';
	    $dictionary  = array(
	        0                   => 'zero',
	        1                   => 'one',
	        2                   => 'two',
	        3                   => 'three',
	        4                   => 'four',
	        5                   => 'five',
	        6                   => 'six',
	        7                   => 'seven',
	        8                   => 'eight',
	        9                   => 'nine',
	        10                  => 'ten',
	        11                  => 'eleven',
	        12                  => 'twelve',
	        13                  => 'thirteen',
	        14                  => 'fourteen',
	        15                  => 'fifteen',
	        16                  => 'sixteen',
	        17                  => 'seventeen',
	        18                  => 'eighteen',
	        19                  => 'nineteen',
	        20                  => 'twenty',
	        30                  => 'thirty',
	        40                  => 'fourty',
	        50                  => 'fifty',
	        60                  => 'sixty',
	        70                  => 'seventy',
	        80                  => 'eighty',
	        90                  => 'ninety',
	        100                 => 'hundred',
	        1000                => 'thousand',
	        100000             => 'lakh',
	        10000000          => 'crore'
	    );

	    if (!is_numeric($number)) {
	        return false;
	    }

	    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
	        // overflow
	        trigger_error(
	            'convert_number only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
	            E_USER_WARNING
	        );
	        return false;
	    }

	    if ($number < 0) {
	        return $negative . $this->convert_number(abs($number));
	    }

	    $string = $fraction = null;

	    if (strpos($number, '.') !== false) {
	        list($number, $fraction) = explode('.', $number);
	    }

	    switch (true) {
	        case $number < 21:
	            $string = $dictionary[$number];
	            break;
	        case $number < 100:
	            $tens   = ((int) ($number / 10)) * 10;
	            $units  = $number % 10;
	            $string = $dictionary[$tens];
	            if ($units) {
	                $string .= $hyphen . $dictionary[$units];
	            }
	            break;
	        case $number < 1000:
	            $hundreds  = $number / 100;
	            $remainder = $number % 100;
	            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
	            if ($remainder) {
	                $string .= $conjunction . $this->convert_number($remainder);
	            }
	            break;
	        case $number < 100000:
	            $thousands   = ((int) ($number / 1000));
	            $remainder = $number % 1000;

	            $thousands = $this->convert_number($thousands);

	            $string .= $thousands . ' ' . $dictionary[1000];
	            if ($remainder) {
	                $string .= $separator . $this->convert_number($remainder);
	            }
	            break;
	        case $number < 10000000:
	            $lakhs   = ((int) ($number / 100000));
	            $remainder = $number % 100000;

	            $lakhs = $this->convert_number($lakhs);

	            $string = $lakhs . ' ' . $dictionary[100000];
	            if ($remainder) {
	                $string .= $separator . $this->convert_number($remainder);
	            }
	            break;
	        case $number < 1000000000:
	            $crores   = ((int) ($number / 10000000));
	            $remainder = $number % 10000000;

	            $crores = $this->convert_number($crores);

	            $string = $crores . ' ' . $dictionary[10000000];
	            if ($remainder) {
	                $string .= $separator . $this->convert_number($remainder);
	            }
	            break;
	        default:
	            $baseUnit = pow(1000, floor(log($number, 1000)));
	            $numBaseUnits = (int) ($number / $baseUnit);
	            $remainder = $number % $baseUnit;
	            $string = $this->convert_number($numBaseUnits) . ' ' . $dictionary[$baseUnit];
	            if ($remainder) {
	                $string .= $remainder < 100 ? $conjunction : $separator;
	                $string .= $this->convert_number($remainder);
	            }
	            break;
	    }

	    if (null !== $fraction && is_numeric($fraction) && $fraction > 0) {
	        $string .= $decimal;
	        $words = array();
	        foreach (str_split((string) $fraction) as $number) {
	            $words[] = $dictionary[$number];
	        }
	        $words = array_slice($words, 0, 2);
	        $string .= implode(' ', $words);
	    }

	    return $string;
	}

	function convert_number($number,$unit ='Rupees',$minor='Paise') {
		$no = floor($number);
		$point = round($number - $no, 2) * 100;
		$hundred = null;
		$digits_1 = strlen($no);
		$i = 0;
		$str = array();
		$words = array('0' => '', '1' => 'one', '2' => 'two',
		'3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
		'7' => 'seven', '8' => 'eight', '9' => 'nine',
		'10' => 'ten', '11' => 'eleven', '12' => 'twelve',
		'13' => 'thirteen', '14' => 'fourteen',
		'15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
		'18' => 'eighteen', '19' =>'nineteen', '20' => 'twenty',
		'30' => 'thirty', '40' => 'forty', '50' => 'fifty',
		'60' => 'sixty', '70' => 'seventy',
		'80' => 'eighty', '90' => 'ninety');
		$digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
		while ($i < $digits_1) {
			$divider = ($i == 2) ? 10 : 100;
			$number = floor($no % $divider);
			$no = floor($no / $divider);
			$i += ($divider == 10) ? 1 : 2;
			if ($number) {
				$plural = (($counter = count($str)) && $number > 9) ? 's' : null;
				$hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
				$str [] = ($number < 21) ? $words[$number] ." " . $digits[$counter] . $plural . " " . $hundred : $words[floor($number / 10) * 10]. " " . $words[$number % 10] . " ". $digits[$counter] . $plural . " " . $hundred;
			} else $str[] = null;
		}
		$str = array_reverse($str);
		$result = implode('', $str);

		/* point calculation */
		$j = 0;
		$str_point = array();
		$hundred_p = $str_point_WORD = null;

		if($point){
			$point_len = strlen($point);
			while ($j < $point_len) {
				$divider_1 = ($j == 2) ? 10 : 100;
				$number_p = floor($point % $divider_1);
				$point = floor($point / $divider_1);
				$j += ($divider_1 == 10) ? 1 : 2;

				if ($number_p) {
					$plural_p = (($counter_p = count($str_point)) && $number_p > 9) ? 's' : null;
					$hundred_p = ($counter_p == 1 && $str_point[0]) ? ' and ' : null;
					$str_point[] = ($number_p < 21) ? $words[$number_p] ." " . $digits[$counter_p] . $plural_p . " " . $hundred_p : $words[floor($number_p / 10) * 10]. " " . $words[$number_p % 10] . " ". $digits[$counter_p] . $plural_p . " " . $hundred_p;
				} else $str_point[] = null;
			}
			$str_point = array_reverse($str_point);
			$str_point_WORD = implode('', $str_point);
		}

		/*$points = ($point) ?
		" " . $words[$point / 10] . " " . 
		$words[$point = $point % 10] : '';*/

		$points = ($str_point_WORD) ? " and " .$str_point_WORD . " ".$minor: '';

		$string =  $result . $unit."  " . $points ;
		return $string;
	}
	
	function formatInr($input){
        $dec = "";
        $pos = strpos($input, ".");
        if ($pos === FALSE){
            //no decimals
           
        }else{
            //decimals
            $dec   = substr(precise_amount(substr($input, $pos), 2), 1);
            $input = substr($input, 0, $pos);
        }
        $num   = substr($input, -3);    // get the last 3 digits
        $input = substr($input, 0, -3); // omit the last 3 digits already stored in $num
        // loop the process - further get digits 2 by 2
        while (strlen($input) > 0)
        {
            $num   = substr($input, -2).",".$num;
            $input = substr($input, 0, -2);
        }
        if($dec == ""){
            $dec = '.00';
        }
        return $num.$dec;
    }
}
?>
