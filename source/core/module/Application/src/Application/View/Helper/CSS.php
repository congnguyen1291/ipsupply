<?php
namespace Application\View\Helper;
use Application\View\Helper\App;

class CSS  extends App
{
    private  $headLink = NULL;

    public function setHeadLink( $datas=array() )
    {
        $this->getContentview()->headLink($datas);
        return $this;
    }

    public function getHeadLink()
    {
        return $this->getContentview()->headLink();
    }

    public function appendStylesheet($value,$media = 'screen',$conditional = true ,$attrs = array() )
    {
        $this->getContentview()->headLink()->appendStylesheet($value,$media,$conditional,$attrs);
        return $this;
    }

    public function addVersionToLink( $link, $version = ''){
        if( !empty($link) && !empty($version) ){
            if ( strpos($link, '?') !== false) {
                $link .= '&coz='. $version;
            }else{
                $link .= '?coz='. $version;
            }
        }
        return $link;
    }

    public function getStylesheet()
    {
        if( !empty($this->getDatas()) && !empty($this->getDatas()->css) ){
            foreach ($this->getDatas()->css as $key => $value) {
                if( !empty($value['href']) ){
                    $media = 'all';
                    $conditional = true;
                    if(!empty($value['media'])){
                        $media = $value['media'];
                    }
                    if(!empty($value['conditional'])){
                        $conditional = $value['conditional'];
                    }
                    $attrs = array();
                    if( is_array($value) ){
                        $attrs = $value;
                        unset($attrs['href']);
                    }
                    $version = '';
                    if( !empty($_SESSION['config'])
                        && !empty($_SESSION['config']['cached']) 
                        && !empty($_SESSION['config']['cached']['namespace']) ){
                        $version = $_SESSION['config']['cached']['namespace'];
                        $value['href'] = $this->addVersionToLink($value['href'], $version);
                    }
                    $this->getContentview()->headLink()->appendStylesheet($value['href'],$media,$conditional,$attrs);
                }
            }
        }
        return $this;
    }

    public function getClassConten()
    {
        $class = '';
        if( !empty($this->getDatas()) ){
            $c_module = $this->getDatas()->c_module;
            $c_controller = $this->getDatas()->c_controller;
            $c_action = $this->getDatas()->c_action;
            $class = $c_module .'-'. $c_controller .'-'. $c_action;
        }
        return strtolower($class);
    }

}
