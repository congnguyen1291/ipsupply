<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */


namespace Cms\Controller;

class TrafficController extends BackEndController
{

    public function reportAction()
    {
        $in_data = array();
        $user_cart = array();
        $user_buy = array();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $date_from = $request->getPost('_from', '');
            $date_to = $request->getPost('_to', date("m/d/Y H:i:s"));
            if( empty($date_from) ){
                $yesterday_timestamp =  strtotime('today - 10 day');
                $date_from = date("m/d/Y H:i:s",$yesterday_timestamp);
            }

            $myDateTime = \DateTime::createFromFormat('m/d/Y H:i:s', $date_from);
            if ( false===$myDateTime ) {
                $myDateTime = \DateTime::createFromFormat('m/d/Y', $date_from);
            }
            if ( !(false===$myDateTime) ) {
                $date_from = $myDateTime->format('Y-m-d H:i:s');
            }else{
                $date_from = '';
            }

            $myDateTime = \DateTime::createFromFormat('m/d/Y H:i:s', $date_to);
            if ( false===$myDateTime ) {
                $myDateTime = \DateTime::createFromFormat('m/d/Y', $date_to);
            }

            if ( !(false===$myDateTime) ) {
                $date_to = $myDateTime->format('Y-m-d H:i:s');
            }else{
                $date_to = '';
            }

            if( !empty($date_from) && !empty($date_to) ){
                $traffics = $this->getModelTable('TrafficTable')->getTrafficByDay($date_from, $date_to);
                $user_start_buy = $this->getModelTable('UserTable')->getLogsByDay($date_from, $date_to, array('step_sign' =>1));
                $user_end_buy = $this->getModelTable('UserTable')->getLogsByDay($date_from, $date_to, array('step_sign' =>4));
                
                $daterange = new \DatePeriod(
                     new \DateTime($date_from),
                     new \DateInterval('P1D'),
                     new \DateTime($date_to)
                );
                foreach($daterange as $dr){
                    $date = $dr->format('Y-m-d');
                    $in_data[$date] =  array('total' => 0, 'day' => $date );
                    $user_cart[$date] =  array('total' => 0, 'day' => $date );
                    $user_buy[$date] =  array('total' => 0, 'day' => $date );
                }
                
                foreach ($traffics as $key => $traffic) {
                    $time = strtotime($traffic['date_simple']);
                    $date = date("Y-m-d", $time);
                    if( !empty($in_data[$date]) ){
                        $in_data[$date]['total'] +=  $traffic['total'];
                    }else{
                        $in_data[$date] =  array('total' => $traffic['total'], 'day' => $date );
                    }
                }

                foreach ($user_start_buy as $key => $value) {
                    $time = strtotime($value['date_simple']);
                    $date = date("Y-m-d", $time);
                    if( !empty($user_cart[$date]) ){
                        $user_cart[$date]['total'] +=  $value['total'];
                    }else{
                        $user_cart[$date] =  array('total' => $value['total'], 'day' => $date );
                    }
                }

                foreach ($user_end_buy as $key => $value) {
                    $time = strtotime($value['date_simple']);
                    $date = date("Y-m-d", $time);
                    if( !empty($user_buy[$date]) ){
                        $user_buy[$date]['total'] +=  $value['total'];
                    }else{
                        $user_buy[$date] =  array('total' => $value['total'], 'day' => $date );
                    }
                }
            }
        }
        
        echo json_encode(array(
            'traffics_by_day' => $in_data,
            'user_cart' => $user_cart,
            'user_buy' => $user_buy,
            'date_from' => $date_from,
            'date_to' => $date_to,
        ));
        die();
    }

}
