<?php
namespace Application\View\Helper;
use Application\View\Helper\App;

class Banners extends App
{
    public function getBannerWithPositionAlias($position, $size = '')
    {
        $banners = $this->getModelTable('BannersTable')->getBannerWithPositionAlias($position, $size = '');
        return $banners;
    }

    public function getLink( $image )
    {
        if( !empty($image)
            && !empty($image['link']) ){
            return $image['link'];
        }
        return '';
    }

    public function getTitle( $image )
    {
        if( !empty($image)
            && !empty($image['banners_title']) ){
            return $image['banners_title'];
        }
        return '';
    }

    public function getImage( $image )
    {
        if( !empty($image)
            && !empty($image['file']) ){
            return $image['file'];
        }
        return '';
    }

    public function getHtml( $banner, $params=array() )
    {
        $own_properties = array('inner', 'wrap', 'href');
        $properties = array();
        $params['class'] = ( !empty($params['class']) ? $params['class'].' coz-link-banner' : 'coz-link-banner'  );
        foreach ($params as $key => $property ) {
            if( !in_array($key, $own_properties) ){
                $properties[] = $key.'="'.$property.'"';
            }
        }

        $html = '<div class="coz-banner clearfix" >
                    <ins class="coz-ins-banner" />
                        <ins class="coz-inas-banner" />
                            <a href="'.$this->getLink($banner).'" '.implode(' ', $properties).' >
                                <img src="'.$this->view->Images()->getUrlImage($this->getImage($banner)).'" alt="'.$this->getTitle($banner).'" class="coz-image-banner coz-reponsive-banner" />
                                '. (!empty($params['inner']) ? $params['inner'] : '') .'
                            </a>
                        </ins>
                    </ins>
                </div>';
        return $html;
    }
	
}
