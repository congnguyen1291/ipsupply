<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

class ServiceController extends FrontEndController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function indexAction()
    {
        echo 'v.1.0';
        die();
    }

    public function exchangeRatesAction()
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5('service-controller-get-exchange-rates');
        $results = $cache->getItem($key);
        if(!$results){
            $Link = 'http://vietcombank.com.vn/ExchangeRates/ExrateXML.aspx';
            $content = @file_get_contents($Link);
            if($content!='' && preg_match_all('/Exrate CurrencyCode="(.*)" CurrencyName="(.*)" Buy="(.*)" Transfer="(.*)" Sell="(.*)"/',$content,$matches) and count($matches)>0){
                $exchange_rates=array(
                                'USD'=>array()
                                ,'EUR'=>array()
                                ,'GBP'=>array()
                                ,'HKD'=>array()
                                ,'JPY'=>array()
                                ,'CHF'=>array()
                                ,'AUD'=>array()
                                ,'CAD'=>array()
                                ,'SGD'=>array()
                                ,'THB'=>array()
                );
                foreach($matches[1] as $key=>$value){
                    if(isset($exchange_rates[$value])){
                        $exchange_rates[$value]=array(
                                        'id'=>$value
                                        ,'name'=>$matches[2][$key]
                                        ,'buy'=>$matches[3][$key]
                                        ,'transfer'=>$matches[4][$key]
                                        ,'sell'=>$matches[5][$key]
                        );
                    }
                }
                $cache->setItem($key, $exchange_rates);
                echo json_encode($exchange_rates);
                die();
            }
        }
        echo json_encode($results);
        die();
    }

	
}

