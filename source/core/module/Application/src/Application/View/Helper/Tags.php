<?php
namespace Application\View\Helper;
use Application\View\Helper\App;

class Tags extends App
{
    public function getUrlTagsProducts($tag)
    {
        if( !empty($tag) 
            && !empty($tag['tags_alias']) ){
            return FOLDERWEB.$this->getUrlPrefixLang().'/tags/'.$tag['tags_alias'];
        }
        return '';
    }

    public function getName($tag)
    {
        if( !empty($tag) 
            && !empty($tag['tags_name']) ){
            return $tag['tags_name'];
        }
        return '';
    }
}
