<?php
namespace Application\View\Helper;
use Application\View\Helper\App;

class Datas extends App
{	
	protected  $IDataHashMap = array();

    public function getIDataHashMap()
    {
        return $this->IDataHashMap;
    }

    public function clearIDataHashMap()
    {
        $this->IDataHashMap = array();
        return $this->IDataHashMap;
    }

    public function pushIDataHashMap($key, $data)
    {
        if( !empty($key) ){
            $this->IDataHashMap[$key] = $data;
            return $this->IDataHashMap;
        }
        return FALSE;
    }

    public function hasElementOnIDataHashMap($key)
    {
        if( !empty($key) && isset($this->IDataHashMap[$key]) ){
            return TRUE;
        }
        return FALSE;
    }

    public function popIDataHashMap($key)
    {
        if( !empty($key) && isset($this->IDataHashMap[$key]) ){
            return $this->IDataHashMap[$key];
        }
        return NULL;
    }
}
