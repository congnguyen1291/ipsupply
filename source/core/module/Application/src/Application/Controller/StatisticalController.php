<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */


namespace Application\Controller;

use Application\Model\Traffic;

class StatisticalController extends FrontEndController
{

    public function extract_css_urls( $text )
    {
        $urls = array( );

        $url_pattern     = '(([^\\\\\'", \(\)]*(\\\\.)?)+)';
        $urlfunc_pattern = 'url\(\s*[\'"]?' . $url_pattern . '[\'"]?\s*\)';
        $pattern         = '/(' .
             '(@import\s*[\'"]' . $url_pattern     . '[\'"])' .
            '|(@import\s*'      . $urlfunc_pattern . ')'      .
            '|('                . $urlfunc_pattern . ')'      .  ')/iu';
        if ( !preg_match_all( $pattern, $text, $matches ) )
            return $urls;

        // @import '...'
        // @import "..."
        foreach ( $matches[3] as $match )
            if ( !empty($match) )
                $urls['import'][] = 
                    preg_replace( '/\\\\(.)/u', '\\1', $match );

        // @import url(...)
        // @import url('...')
        // @import url("...")
        foreach ( $matches[7] as $match )
            if ( !empty($match) )
                $urls['import'][] = 
                    preg_replace( '/\\\\(.)/u', '\\1', $match );

        // url(...)
        // url('...')
        // url("...")
        foreach ( $matches[11] as $match )
            if ( !empty($match) )
                $urls['property'][] = 
                    preg_replace( '/\\\\(.)/u', '\\1', $match );

        return $urls;
    }

    public function indexAction()
    {
        $css = file_get_contents('http://static.coz.vn/a5121837308fa812988143eb51e97737/styles/minify/style.css');
        $url = $this->extract_css_urls($css);
        echo '<pre>';
        print_r($url);
        die();
    }

    public function vistedAction()
    {
        $sd = date("Y-m-d H:i:s");
        $ip = $_SERVER['REMOTE_ADDR'];
        $second = 5; //5 giay
        $online = array();
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':Statistical:visted');
        $online = $cache->getItem($key);
        if( !empty($online) ){
            if( !empty($online[$ip]) 
                && !empty($online[$ip]['time']) ){
                $time_sync = $online[$ip]['time'];
                $date_from = strtotime($time_sync);
                $date_to = strtotime('now - '.$second.' second');
                if($date_to >= $date_from){
                    unset($online[$ip]);
                    //$cache->setItem($key, $online, $second);
                    $cache->setItem($key, $online);
                }
            }else{
                $online[$ip] =array('ip'=>$ip, 'time' => $sd);
                $cache->setItem($key, $online);
            }
        }

        if( empty($online) ){
            $online = array($ip=>array('ip'=>$ip, 'time' => $sd));
            //$cache->setItem($key, $online, $second);
            $cache->setItem($key, $online);
        }
        //$cache->getOptions()->setTtl( $second );
        //$cache->setItem($key, $online, time() + 60);
        echo json_encode($online);
        die();
    }

    public function visitAction()
    {
        $item = array('flag' => FALSE);
        if( empty($_SESSION['VISITED']) ){
            $session_id = session_id();
            $tf = $this->getModelTable('TrafficTable')->getTraffic( array('session_id' => $session_id) );
            $member = isset($_SESSION['MEMBER']) ? $_SESSION['MEMBER'] : array();
            if( empty($tf) ){
                $traffic = new Traffic();
                $traffic->session_id = $session_id;
                $traffic->users_id = (!empty($member['users_id']) ? $member['users_id'] : 0);
                $traffic->email = (!empty($member['user_name']) ? $member['user_name'] : '');
                $traffic->date_create = date('Y-m-d H:i:s');
                $id = $this->getModelTable('TrafficTable')->register( $traffic );
                if( !empty($id) ){
                    $_SESSION['VISITED'] = TRUE;
                    $item = array('flag' => TRUE, 'id' => $id);
                }
            }else{
                if( (empty($tf->users_id) || empty($tf->user_name)) && !empty($member) ){
                    $row = array(
                            'users_id' => $member['users_id'],
                            'email' => $member['user_name'],
                        );
                    $this->getModelTable('TrafficTable')->updateTraffic( $row, $tf->traffic_id );
                    $_SESSION['VISITED'] = TRUE;
                    $item = array('flag' => TRUE, 'id' => $tf->traffic_id);
                }
                $date = date('Y-m-d H:i:s');
                $date1 = new \DateTime($date);
                $date2 = new \DateTime($tf->date_create);
                $interval = $date1->diff($date2);
                if( $interval->h >= 3){//ghi lại khi sesion hon 3h
                    session_regenerate_id();
                    $session_id = session_id();
                    $traffic = new Traffic();
                    $traffic->session_id = $session_id;
                    $traffic->users_id = (!empty($member['users_id']) ? $member['users_id'] : 0);
                    $traffic->email = (!empty($member['user_name']) ? $member['user_name'] : '');
                    $traffic->date_create = date('Y-m-d H:i:s');
                    $id = $this->getModelTable('TrafficTable')->register( $traffic );
                    if( !empty($id) ){
                        $_SESSION['VISITED'] = TRUE;
                        $item = array('flag' => TRUE, 'id' => $id);
                    }
                }
            }
        }
        echo json_encode($item);
        die();
    }

}
