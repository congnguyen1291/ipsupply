<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/4/14
 * Time: 10:53 AM
 */

namespace Cms\Controller;

use Cms\Form\ApiForm;
use Cms\Lib\Paging;
use Cms\Model\Api;
use Zend\View\Model\ViewModel;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Http\Client as HttpClient;
use Zend\Dom\Query;

use Cms\Model\Product;

class SiriController extends BackEndController
{
    public function __construct()
    {
        parent::__construct();
        $this->data_view['current'] = 'siri';
    }

    public function indexAction()
    {
        return $this->data_view;
    }

    public function fixImageAction()
    {
        $url = '';
        $request = $this->getRequest();
        if ($request->isPost()) {
            $url = $request->getPost('url', '');
            $this->data_view['msg'] = 'Không thành công, vui lòng kiểm tra lại';
            if( !empty($url) && $this->isImage($url) ){
                $path = '';
                if ( substr($url, 0, 1) == '/' && substr($url, 0, 2) != '//' ){
                    $path = PATH_BASE_ROOT.$url;
                }else{
                    $url = trim($url, '/');
                    $list = explode ( '/', $url );
                    $dm = $list[0];
                    unset($list[0]);
                    $ul = implode('/', $list);
                    if( stripos($dm,'photos.coz.vn') === true ){
                        $path = PATH_BASE_ROOT . '/custom/domain_1/'.$ul;
                    }else{
                        $path = PATH_BASE_ROOT .'/'.$ul;
                    }
                }
                if( is_file($path) ){
                    @unlink($path);
                    $this->data_view['msg'] = 'Thành công';
                }else{
                    $this->data_view['msg'] = 'File hình đã được xoá, hiện tại không có trên server';
                }
            }
        }
        $this->data_view['url'] = $url;
        return $this->data_view;
    }

    public function crawlAction()
    {
        die('end');
        $request = $this->getRequest();
        $url = 'http://dienhoavietnam.com/index.php?main_page=index&cPath=229_233&sort=20a&page=9';
        $website_id = $this->website->website_id;
        $domain= md5($_SESSION['website']['website_domain'].':'.$website_id);
        $websiteFolder = PATH_BASE_ROOT . "/custom/domain_1/" . $domain;
        if(!is_dir($websiteFolder)){
            @mkdir ( $websiteFolder, 0777 );
        }

        $client = new HttpClient();
        $client->setAdapter('Zend\Http\Client\Adapter\Curl');
        $client->setUri($url);
        $result = $client->send();
        $body   = $result->getBody();
        $dom = new Query($body);
        $title = $dom->execute('.img_product');
        $results = array();
        foreach($title as $key=>$r)
        {
            //per h2 NodeList, has element with tagName = 'a'
            //DOMElement get Element with tagName = 'a'
            $ielement     = $r->getElementsByTagName("img")->item(0);
            $aelement     = $r->getElementsByTagName("a")->item(0);
            //$aelement     = $r;
            if ( $aelement->hasAttributes() ) {
                $url = $aelement->getAttributeNode('href')->nodeValue;
                if( !empty($url) ){
                    $client->setUri($url);
                    $result = $client->send();
                    $body   = $result->getBody();
                    $dom = new Query($body);

                    $products_title = '';
                    $products_alias = '';
                    $seo_keywords = '';
                    $seo_description = '';
                    $seo_title = '';
                    $code = '';
                    $products_description = '';
                    $price = 0;
                    $thumb_image = '';
                    $list_thumb_image = array();
                    $list_image = array();

                    $productName = $dom->execute('#productName');
                    if ( !empty($productName[0]) ) {
                        $products_title = $productName[0]->textContent;
                    }

                    $info = $dom->execute('h1.info');
                    if ( !empty($info[0]) ) {
                        $code = $info[0]->textContent;
                    }

                    $productDescription = $dom->execute('#productDescription');
                    if ( !empty($productDescription[0]) ) {
                        $products_description = $productDescription[0]->nodeValue;
                    }

                    $price_product_black = $dom->execute('p.price_product_black');
                    
                    if ( !empty($price_product_black[0]) && $price_product_black[0]->hasAttributes() ) {
                        $spelement     = $price_product_black[0]->getElementsByTagName("span")->item(0);
                        $price = $spelement->textContent;
                        $price = str_replace ( '.', '', $price );
                        $price = str_replace ( ',', '', $price );
                        $price = (int)$price;
                    }

                    if ( $ielement->hasAttributes() ) {
                        $httpImg = $ielement->getAttributeNode('src')->nodeValue;
                        if( !empty($httpImg) ){
                            $list = explode ( '/', $httpImg );
                            $filename = end($list);
                            $list[count($list)-1] = rawurlencode($filename);
                            $httpImg = implode('/', $list);
                            $name = $this->file_name ( $filename );
                            $name = strtolower($this->toAlias($name));
                            $extention = $this->file_extension ( $filename );
                            $upload_url = $websiteFolder. "/" . $name.'.'.$extention;
                            if(is_file($upload_url)){
                                $name = $name.'-'.date("YmdHis");
                                $upload_url = $websiteFolder. "/" . $name.'.'.$extention;
                            }
                            try{
                                $thumb_image = 'http://dienhoavietnam.com/'.trim($httpImg, '/');
                                copy($thumb_image, $upload_url);
                                chmod ( $upload_url, 0777 );
                                $row = array('website_id' => 1,
                                                'users_id' => 0,
                                                'id_album' => 0,
                                                'full_name' => $name.'.'.$extention,
                                                'name' => $name,
                                                'string_data' => '',
                                                'folder' => '/custom/domain_1/' . $domain,
                                                'caption' => $name,
                                                'type' => $extention,
                                                'order' => 0,
                                                'detector' => 0,
                                                'number_comment' => 0);
                                $picture = $this->getModelTable('PictureTable')->savePicture($row);
                                $thumb_image = '/custom/domain_1/' . $domain. '/' . $name.'.'.$extention;
                                $list_thumb_image[] = array(
                                                        'order' => 0,
                                                        'src' => $thumb_image 
                                                    );
                                $list_image[] = $thumb_image;
                            }catch(\Exception $ex){

                            }
                        }
                    }

                    $categories_name_list = array(4167);//ke hoa
                    $categories_id = $categories_name_list[0];

                    $products_alias = $this->toAlias($products_title);
                    $same = $this->getModelTable('ProductTable')->getProductsByAlias($products_alias);
                    if( !empty($same) ){
                        $products_alias .= '-'.date('YmdGis');
                    }
                    $row = array(
                            'products_code' => $code,
                            'categories_id' => $categories_id,
                            'manufacturers_id' => 1,
                            'users_id' => 0,
                            'transportation_id' => 0,
                            'users_fullname' => '',
                            'products_title' => $products_title,
                            'products_alias' => $products_alias,
                            'products_description' => htmlentities($products_description, ENT_QUOTES, 'UTF-8'),
                            'products_longdescription' => '',
                            'bao_hanh' => '',
                            'promotion' => '',
                            'promotion_description' => '',
                            'promotion_ordering' => 0,
                            'promotion1' => '',
                            'promotion1_description' => '',
                            'promotion1_ordering' => 0,
                            'promotion2' => '',
                            'promotion2_description' => '',
                            'promotion2_ordering' => '',
                            'promotion3' => '',
                            'promotion3_description' => '',
                            'promotion3_ordering' => 0,
                            'seo_keywords' => $products_title,
                            'seo_description' => $products_title,
                            'seo_title' => $products_title,
                            'products_more' => '',
                            'is_published' => 1,
                            'is_delete' => 0,
                            'is_new' => 1,
                            'is_available' => 0,
                            'is_hot' => 1,
                            'is_goingon' => 0,
                            'is_sellonline' => 0,
                            'is_viewed' => 0,
                            'position_view' => 0,
                            'tra_gop' => '',
                            'date_create' => date('Y-m-d H:m:s'),
                            'hide_price' => 0,
                            'wholesale' => 0,
                            'price' => $price,
                            'price_sale' => $price,
                            'ordering' => 0,
                            'quantity' => 0,
                            'thumb_image' => $thumb_image,
                            'list_thumb_image' => json_encode($list_thumb_image),
                            'number_views' => 0,
                            'vat' => '',
                            'type_view' => 0,
                            'youtube_video' => '',
                            'publisher_id' => 0,
                            'language' => 1
                        );
                    $p = new Product();
                    $p->exchangeArray($row);
                    $result = $this->getModelTable('ProductTable')->saveProduct($p, $request);
                    if( !empty($result['productid']) ){
                        $id = $result['productid'];
                        $this->getModelTable('ProductTable')->saveProductTranslate($p, $id);

                        if( !empty($categories_name_list) ){
                           $this->getModelTable('ProductTable')->addCategoryProduct($id, array_unique($categories_name_list));
                        }

                        if ( !empty($list_image) ) {
                            $this->getModelTable('ProductTable')->insertImages($id, $list_image);
                        }
                    }
                    $results[] = $row;
                }
            }
        }
        print_r($results);die();
        return $this->data_view;
    }

}