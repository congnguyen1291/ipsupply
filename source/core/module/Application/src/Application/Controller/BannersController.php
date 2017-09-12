<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 7/16/14
 * Time: 2:00 PM
 */
namespace Application\Controller;

use Zend\View\Model\ViewModel;

class BannersController extends FrontEndController{

    public function indexAction(){
        $position = $this->params()->fromQuery('p',NULL);
        $width = $this->params()->fromQuery('w', NULL);
        $height = $this->params()->fromQuery('h', NULL);
        if(!$position || !$width || !$height){
            die('Access restrict');
        }
        try{
            $banners = $this->getModelTable('BannersTable')->getBanners($position, $width, $height);
            if(count($banners) == 0){
                throw new \Exception('Access restrict');
            }
            $index = 0;
            if(count($banners) > 1){
                $index = rand(0, count($banners) - 1);
            }
            $banner = $banners[$index];
            $view = new ViewModel();
            $view->setTerminal(true);
            $view->setVariables(array(
                'banner' => $banner,
            ));
            return $view;
        }catch(\Exception $ex){
            die();
        }
    }

}