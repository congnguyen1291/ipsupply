<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 6/19/14
 * Time: 4:08 PM
 */

namespace Cms\Controller;

use Cms\Form\MenuForm;
use Cms\Form\SocialNetworkForm;
use Cms\Model\Menu;
use Cms\Model\SocialNetwork;

class SettingController extends BackEndController{
    public function __construct(){
        parent::__construct();
        $this->data_view['current'] = 'setting';
    }

    public function indexAction(){
        $configs = $this->getModelTable('SettingTable')->getAllConfig();
        $this->data_view['configs'] = $configs;
        return $this->data_view;
    }

    public function changePostAction(){
        $name = $this->params()->fromRoute('name');
        if (!$name) {
            return $this->redirect()->toRoute('cms/setting', array(
                'action' => 'index',
            ));
        }
        try {
            $config = $this->getModelTable('SettingTable')->getConfig($name);
        } catch (\Exception $ex) {
            return $this->redirect()->toRoute('cms/setting', array(
                'action' => 'index',
            ));
        }
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
            if($this->getModelTable('SettingTable')->saveConfig($name, $data)){

            	/*strigger change namespace cached*/
                $this->updateNamespaceCached();

                return $this->redirect()->toRoute('cms/setting');
            }
        }
        $this->data_view['config'] = $config;
        return $this->data_view;
    }
	
	public function changeFooterAction(){
		$folder = PATH_BASE_ROOT . "/cache/";
		$fileName = 'footer' . '.xml';
		$filePath = $folder . $fileName;
		$config = array();
		$request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost();
			$xml = new \DOMDocument();
			$x_footer = $xml->createElement("footer");
			
			$x_help = $xml->createElement("help");
			$x_help->nodeValue=$this->transportationImages($data['help']);
			$x_aboutus = $xml->createElement("aboutus");
			$x_aboutus->nodeValue=$this->transportationImages($data['about']);
			$x_lien = $xml->createElement("lien");
			$x_lien->nodeValue=$this->transportationImages($data['lien']);
			$x_linkmak = $xml->createElement("linkmak");
			$x_linkmak->nodeValue=$this->transportationImages($data['linkmk']);
			$x_payment = $xml->createElement("payment");
			$x_payment->nodeValue=$this->transportationImages($data['payment']);
			$x_transportation = $xml->createElement("transportation");
			$x_transportation->nodeValue=$this->transportationImages($data['transportation']);
			
			$x_footer->appendChild($x_help);
			$x_footer->appendChild($x_aboutus);
			$x_footer->appendChild($x_lien);
			$x_footer->appendChild($x_linkmak);
			$x_footer->appendChild($x_payment);
			$x_footer->appendChild($x_transportation);
			$xml->appendChild( $x_footer );			
			$xml->save($filePath);
			$config['help'] = $data['help'];
			$config['aboutus'] = $data['about'];
			$config['lien'] = $data['lien'];
			$config['linkmk'] = $data['linkmk'];
			$config['payment'] = $data['payment'];
			$config['transportation'] = $data['transportation'];
			//print_r($config);die('het');
            $this->data_view['config'] = $config;
			return $this->data_view;
        }else{			
			if(is_file($filePath)){
				$doc = new \DOMDocument();
				$doc->load($filePath);
				$x_help = $doc->getElementsByTagName( "help" );
				$x_aboutus = $doc->getElementsByTagName( "aboutus" );
				$x_lien = $doc->getElementsByTagName( "lien" );
				$x_linkmak = $doc->getElementsByTagName( "linkmak" );				
				$x_payment = $doc->getElementsByTagName( "payment" );				
				$x_transportation = $doc->getElementsByTagName( "transportation" );				
				if ($x_help->length > 0) {
					foreach( $x_help as $i => $node ){
						$config['help'] = $node->nodeValue;
						break;
					}
				}
				if ($x_aboutus->length > 0) {
					foreach( $x_aboutus as $i => $node ){
						$config['aboutus'] = $node->nodeValue;
						break;
					}
				}
				if ($x_lien->length > 0) {
					foreach( $x_lien as $i => $node ){
						$config['lien'] =$node->nodeValue;
						break;
					}
				}
				if ($x_linkmak->length > 0) {
					foreach( $x_linkmak as $i => $node ){
						$config['linkmk'] = $node->nodeValue;
						break;
					}
				}
				if ($x_payment->length > 0) {
					foreach( $x_payment as $i => $node ){
						$config['payment'] = $node->nodeValue;
						break;
					}
				}
				if ($x_transportation->length > 0) {
					foreach( $x_transportation as $i => $node ){
						$config['transportation'] = $node->nodeValue;
						break;
					}
				}
			}
            $this->data_view['config'] = $config;
			return $this->data_view;
		}
    }
	
	public function transportationImages($html_){
		$folder = $_POST['folder'];
		$urlFolder = PATH_BASE_ROOT . "/hinhtam/" . $folder;
		$urlFolderTran = PATH_BASE_ROOT . '/custom/domain_1/footer';
		if(!is_dir($urlFolderTran)){
			@mkdir ( $urlFolderTran."/", 0777 );
		}
		$html = mb_convert_encoding($html_, 'HTML-ENTITIES', "UTF-8");
		$dom = new \DOMDocument ();
		$dom->encoding = 'utf-8';
		@$dom->loadHTML($html);
		$imgs = $dom->getElementsByTagName('img');
		if (count($imgs) > 0) {
			foreach ($imgs as $img) {
				$src = $img->getAttribute('src');
				$slipFile = explode('/', $src);
				$fileName_= end($slipFile);					
				if(!empty($fileName_) && is_file($urlFolder.'/'.$fileName_)){
					//chuyen hinh-tam
					try{
						copy($urlFolder.'/'.$fileName_, $urlFolderTran.'/'.$fileName_);
						$posFirst = strpos($src, 'hinhtam');
						$posLast = strlen($src);
						if(($posLast-$posFirst)>0){
							$strRe = substr($src,$posFirst,($posLast-$posFirst));
							$src = str_replace($strRe, 'custom/domain_1/footer/'.$fileName_, $src);
							$img->setAttribute('src', $src);
						}else{
							$img->parentNode->removeChild($img);
						}	
					} catch (\Exception $ex) {
						//$img->parentNode->removeChild($img);
					}						
				}else{
					//$img->parentNode->removeChild($img);
				}	
			}
		}			
		$html = $dom->saveXML($dom->getElementsByTagName('body')->item(0));
		$result_ = preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $html);
		$result_ = html_entity_decode(htmlentities($result_, ENT_QUOTES, 'UTF-8'));
		return htmlspecialchars($result_);
	}
	
	public function footerListAction(){
		$configs = $this->getModelTable('SettingTable')->getAllConfig();
		$request = $this->getRequest();
		if($request->isPost()){
			$config = $request->getPost('config');
			foreach($config as $key => $value){
				$config[$key] = htmlentities($value, ENT_QUOTES, 'UTF-8');
			}
			try{
				$this->getModelTable('SettingTable')->saveSetting($config);
				return $this->redirect()->toRoute('cms/setting');
			}catch(\Exception $ex){
				die($ex->getMessage());
			}
		}
        $this->data_view['configs'] = $configs;
		return $this->data_view;
	}
	
	public function socialManagerAction(){
		try{
			$rows = $this->getModelTable('SettingTable')->getAllSocialNetworks();
            $this->data_view['rows'] = $rows;
			return $this->data_view;
		}catch(\Exception $ex){
			
		}
	}
	
	public function addNetworkAction(){
		$form = new SocialNetworkForm();
		$request = $this->getRequest();
		if($request->isPost()){
			$post = array_merge_recursive(
				$request->getPost()->toArray(),
				$request->getFiles()->toArray()
			);
			$sn = new SocialNetwork();
			$form->setInputFilter($sn->getInputFilter());
			$form->setData($post);
			if($form->isValid()){
				try{
					$this->getModelTable('SettingTable')->addNetwork($form->getData());
					return $this->redirect()->toRoute('cms/setting',array('action' => 'social-manager'));
				}catch(\Exception $ex){
				
				}
			}
		}
        $this->data_view['form'] = $form;
		return $this->data_view;
	}
	
	public function editNetworkAction(){
		$id = $this->params()->fromRoute('id', NULL);
		if(!$id){
			return $this->redirect()->toRoute('cms/setting', array('action' => 'social-manager'));
		}
		try{
			$nw = $this->getModelTable('SettingTable')->getNetworkById($id);
		}catch(\Exception $ex){
			return $this->redirect()->toRoute('cms/setting', array('action' => 'social-manager'));
		}
		$form = new SocialNetworkForm();
		$form->bind($nw);
		$request = $this->getRequest();
		if($request->isPost()){
			$post = array_merge_recursive(
				$request->getPost()->toArray(),
				$request->getFiles()->toArray()
			);
			$objNetwork = new SocialNetwork();
			$form->setInputFilter($objNetwork->getInputFilter());
			$form->setData($post);
			if($form->isValid()){
				try{
					$this->getModelTable('SettingTable')->editNetwork($form->getData());
					return $this->redirect()->toRoute('cms/setting', array('action' => 'social-manager'));
				}catch(\Exception $ex){
				
				}
			}
		}
        $this->data_view['id'] = $id;
        $this->data_view['form'] = $form;
		return $this->data_view;
	}

	public function deleteNetworkAction(){
		$request = $this->getRequest();
		$ids = array();
		if($request->isPost()){
			$ids = $request->getPost('cid');
		}else{
			$id = $this->params()->fromRoute('id', NULL);
			if(!$id){
				return $this->redirect()->toRoute('cms/setting', array('action' => 'social-manager'));
			}
			$ids = array($id);
		}
		try{
			$this->getModelTable('SettingTable')->delNetwork($ids);
		}catch(\Exception $ex){
			
		}
		return $this->redirect()->toRoute('cms/setting', array('action' => 'social-manager'));
	}

    public function menuManagerAction(){
        $menu_dir = './module/menus';
        if(!is_dir($menu_dir)){
            @mkdir($menu_dir,0777);
        }
        $menu_dir .= '/menus.data';
        $menus = array();
        if(is_file($menu_dir)){
            $menus_string = file_get_contents($menu_dir);
            $menus = json_decode($menus_string, TRUE);
        }
        usort($menus, function($a, $b){
            if($a['ordering'] == $b['ordering']){
                return 0;
            }
            return $a['ordering'] > $b['ordering'] ? 1 : -1;
        });
        $this->data_view['menus'] = $menus;
        return $this->data_view;
    }

    public function addMenuAction(){
        $form = new MenuForm();
        $request = $this->getRequest();
        if($request->isPost()){
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );

            $sn = new Menu();
            $form->setInputFilter($sn->getInputFilter());
            $form->setData($post);
            if($form->isValid()){
                try{
                    $this->getModelTable('SettingTable')->addMenu($form->getData());
                    return $this->redirect()->toRoute('cms/setting',array('action' => 'menu-manager'));
                }catch(\Exception $ex){

                }
            }
        }
        $this->data_view['form'] = $form;
        return $this->data_view;
    }

    public function editMenuAction(){
        $id = $this->params()->fromRoute('id', NULL);
        if(!$id){
            return $this->redirect()->toRoute('cms/setting', array('action' => 'menu-manager'));
        }
        try{
            $nw = $this->getModelTable('SettingTable')->getMenuById($id);
        }catch(\Exception $ex){
            return $this->redirect()->toRoute('cms/setting', array('action' => 'menu-manager'));
        }
        $form = new MenuForm();
        //$form->bind($nw);
        $form->get('id')->setAttribute('value', $nw['id']);
        $form->get('title')->setAttribute('value', $nw['title']);
        $form->get('link')->setAttribute('value', $nw['link']);
        $form->get('icon')->setAttribute('value', $nw['icon']);
        $form->get('ordering')->setAttribute('value', $nw['ordering']);
        if(isset($nw['is_hot'])){
            $form->get('is_hot')->setAttribute('value', $nw['is_hot']);
        }
        if(isset($nw['is_new'])){
            $form->get('is_new')->setAttribute('value', $nw['is_new']);
        }
        if(isset($nw['in_page'])){
            $form->get('in_page')->setAttribute('value', $nw['in_page']);
        }
        $request = $this->getRequest();
        if($request->isPost()){
            $post = array_merge_recursive(
                $request->getPost()->toArray(),
                $request->getFiles()->toArray()
            );
            $obj = new Menu();
            $form->setInputFilter($obj->getInputFilter());
            $form->setData($post);
            if($form->isValid()){
                try{
                    $this->getModelTable('SettingTable')->editMenu($form->getData());
                    return $this->redirect()->toRoute('cms/setting', array('action' => 'menu-manager'));
                }catch(\Exception $ex){

                }
            }
        }
        $this->data_view['menu'] = $nw;
        $this->data_view['form'] = $form;
        $this->data_view['id'] = $id;
        return $this->data_view;
    }

    public function editConfigAction(){
        $request = $this->getRequest();
        if($request->isPost()){
            $data = $request->getPost('data');
            try{
                foreach($data as $key => $c){
                    $d = array(
                        'value' => $c,
                    );
                    $this->getModelTable('SettingTable')->saveConfig($key, $d);
                }
            }catch (\Exception $ex){

            }
        }
        $configs = $this->getModelTable('SettingTable')->getAllConfig();
        $this->data_view['configs'] = $configs;
        return $this->data_view;
    }

    public function deleteMenuCacheAction(){
        $path = "./module/menu_cached";
        if(is_dir($path)){
            $files = glob($path.'/*.cache');
            if(count($files)) {
                foreach ($files as $file) {
                    @unlink($file);
                }
            }
        }
        return $this->redirect()->toRoute('cms/setting');
    }

}