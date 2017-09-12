<?php
namespace Application\View\Helper;
use Application\View\Helper\App;
use Ubench\Ubench;

class Neo extends App
{
	protected $PDatas = array();


    public function getProductDatas()
    {
        return $this->PDatas;
    }

    public function getProductKeys()
    {
        return array_keys($this->PDatas);
    }

    public function getProductData( $key )
    {
        $key = md5($key);
        $product = array();
        if( !empty($this->PDatas[$key]) ){
            $product = $this->PDatas[$key];
        }
        return $product;
    }

    public function setProductData( $value, $key )
    {
        $key = md5($key);
    	$this->PDatas[$key] = $value;
        return $this;
    }

    public function getNData()
    {
        $bench = new Ubench;
        return $bench;
    }

    public function getUbench()
    {
        $bench = new Ubench;
        return $bench;
    }

}
