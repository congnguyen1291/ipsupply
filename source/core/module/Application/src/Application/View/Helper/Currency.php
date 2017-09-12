<?php
namespace Application\View\Helper;
use Application\View\Helper\App;

class Currency extends App
{
    public function getCurrencySymbol()
    {
        return $_SESSION['website']['website_currency'];
    }

    public function getPositionCurrencySymbol()
    {
        $str ='';
        switch ($_SESSION['website']['website_currency']) {
            case 'USD':
            case 'EUR':
            case 'GBP':
            case 'JPY':
            case 'SGD':
            case 'KRW':
            case 'THB':
                $str = 'left';
                break;
            case 'VND':
            case 'CNY':
                $str = 'right';
                break;
            
            default:
                $str = 'right';
                break;
        }

        return $str;
    }

    public function getDataCurrency()
    {
        $decimals=2;
        $dec_point='.';
        $separator=',';
        if ($_SESSION['website']['website_currency_decimals'] != '') {
            $decimals = $_SESSION['website']['website_currency_decimals'];
        }
        if (!empty($_SESSION['website']['website_currency_decimalpoint'])) {
            $dec_point = $_SESSION['website']['website_currency_decimalpoint'];
        }
        if (!empty($_SESSION['website']['website_currency_separator'])) {
            $separator = $_SESSION['website']['website_currency_separator'];
        }

        return array('decimals' => $decimals, 'dec_point' => $dec_point, 'separator' => $separator, 'symbol' => $this->getCurrencySymbol());
    }

    public function fomatCurrency($number,$str_zero = 'txt_zero_price')
    {
        $translator = $this->getTranslator ();
        $str ='';
        $decimals = 1;
        $decimalpoint='.';
        $separator=',';
        if ($_SESSION['website']['website_currency_decimals'] != '') {
            $decimals = $_SESSION['website']['website_currency_decimals'];
        }
        if (!empty($_SESSION['website']['website_currency_decimalpoint'])) {
            $decimalpoint = $_SESSION['website']['website_currency_decimalpoint'];
        }
        if (!empty($_SESSION['website']['website_currency_separator'])) {
            $separator = $_SESSION['website']['website_currency_separator'];
        }
		//if($number>0 || ($number==0 && empty($str_zero)) ){		
			switch ($_SESSION['website']['website_currency']) {
				case 'VND':
					$str = number_format($number,$decimals,$decimalpoint,$separator). ' đ';
					break;
				case 'CNY':
					$str = number_format($number,$decimals,$decimalpoint,$separator). ' yuan';
					break;
				case 'USD':
					$str = '$'.number_format($number,$decimals,$decimalpoint,$separator);
					break;
				case 'EUR':
					$str = '€'.number_format($number,$decimals,$decimalpoint,$separator);
					break;
				case 'GBP':
					$str = '£'.number_format($number,$decimals,$decimalpoint,$separator);
					break;
				case 'JPY':
					$str = '¥'.number_format($number,$decimals,$decimalpoint,$separator);
					break;
				case 'SGD':
					$str = 'S$'.number_format($number,$decimals,$decimalpoint,$separator);
					break;
				case 'KRW':
					$str = '₩'.number_format($number,$decimals,$decimalpoint,$separator);
					break;
				case 'THB':
					$str = '฿'.number_format($number,$decimals,$decimalpoint,$separator);
					break;
				default:
					$str = number_format($number,$decimals,$decimalpoint,$separator);
					break;
			}
		/*}else{
			$str= $translator->translate($str_zero);
		}*/
        return $str;
    }

    public function exchangerates($from,$to,$amount =1){
        if( $from != $to ){
            $rate_exchange= 0;
            $from = strtoupper($from);
            $to = strtoupper($to);
            try{
                $url = "http://www.google.com/finance/converter?a=$amount&from=$from&to=$to";
                $request = curl_init();
                $timeOut = 0;
                curl_setopt ($request, CURLOPT_URL, $url);
                curl_setopt ($request, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt ($request, CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
                curl_setopt ($request, CURLOPT_CONNECTTIMEOUT, $timeOut);
                $response = curl_exec($request);
                curl_close($request);
                $regex  = '#\<span class=bld\>(.+?)\<\/span\>#s';
                preg_match($regex, $response, $converted);
                
                if( !empty($converted) ){
                    $result = $converted[0];
                    $list = explode ( ' ', $result );
                    $dot = count ( $list );
                    unset($list [$dot - 1]);
                    $string = implode(' ', $list);
                    $string = trim($string);
                    $string = str_replace(' ', '-', $string);
                    $string = preg_replace('/[^0-9\.]/', '', $string);
                    $rate_exchange  = (float)$string;
                }
            }catch(\Exception $e){
                $rate_exchange = 0;
            }

            if( empty($rate_exchange) ){
                try{
                    $keyMoney = $from.$to;
                    $yql = "http://query.yahooapis.com/v1/public/yql";
                    $yql_query = "select * from yahoo.finance.xchange where pair in (\"$keyMoney\")";
                    $yql_query_url = $yql . "?q=" . urlencode($yql_query) . "&env=store://datatables.org/alltableswithkeys&format=json";
                    $session = curl_init($yql_query_url);
                    curl_setopt($session, CURLOPT_RETURNTRANSFER,true);
                    $json = curl_exec($session);
                    $data =  json_decode($json);

                    if( !empty($data)
                        && !empty($data->query)
                        && !empty($data->query->results) ){
                        $results = $data->query->results;
                        foreach($results as $rate) {
                            $id = $rate->id;
                            if( $id == $keyMoney ){
                                $ten = $rate->Name;
                                $rate_exchange = $rate->Rate;
                            }
                        }
                    }
                }catch(\Exception $e){
                    $rate_exchange = 0;
                }
            }
            return (!empty($rate_exchange) ? $rate_exchange : 1);
        }else{
            return 1;
        }
    }
	
}
