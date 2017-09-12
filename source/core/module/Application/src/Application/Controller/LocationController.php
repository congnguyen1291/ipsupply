<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */


namespace Application\Controller;


class LocationController extends FrontEndController
{
    public function indexAction()
    {
        $item = array('flag' => TRUE, 'msg' => 'txt_not_found');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $_country_id = $request->getPost('country_id', 0);
            $_cities_id = $request->getPost('cities_id', 0);
            $_districts_id = $request->getPost('districts_id', 0);
            $_wards_id = $request->getPost('wards_id', 0);
            $city = $request->getPost('city', '');
            $zipcode = $request->getPost('zipcode', '');
            $categories_id = $request->getPost('categories_id', 0);

            $error = array();
            $country_id = 0;
            $cities_id = 0;
            $districts_id = 0;
            $wards_id = 0;
            $address = '';
            if( !empty($_country_id) ){
                $country = $this->getModelTable('CountryTable')->getOne($_country_id);
                if( !empty($country) ){
                    $address .= $country->title;
                    $country_id = $_country_id;
                    if( !empty($_cities_id) ){
                        $cities = $this->getModelTable('CitiesTable')->getRow($_cities_id);
                        if( !empty($cities) ){
                            $address = $cities->cities_title.', '.$address;
                            $cities_id = $_cities_id;
                            if( !empty($_districts_id) ){
                                $districts = $this->getModelTable('DistrictsTable')->getRow($_districts_id);
                                if( !empty($districts) ){
                                    $address = $districts->districts_title.', '.$address;
                                    $districts_id = $_districts_id;
                                    if( !empty($_wards_id) ){
                                        $wards = $this->getModelTable('WardsTable')->getRow($_wards_id);
                                        if( !empty($districts) ){
                                            $address = $wards->wards_title.', '.$address;
                                            $wards_id = $_wards_id;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if( empty($error) ){
                $_SESSION['LOCATION'] = array(
                        'country_id' => $country_id,
                        'cities_id' => $cities_id,
                        'districts_id' => $districts_id,
                        'wards_id' => $wards_id,
                        'address' => $address,
                        'city' => $city,
                        'zipcode' => $zipcode,
                        'categories_id' => $categories_id,
                    );
                $item = array('flag' => TRUE, 'location' => $_SESSION['LOCATION'], 'msg' => 'txt_cap_nhat_vi_tri_thanh_cong');
            }
        }
        echo json_encode($item);
        die();
    }

}
