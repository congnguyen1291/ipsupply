<?php
namespace Application\View\Helper;
use Application\View\Helper\App;

class Faq  extends App
{
	public function getId( $fqa )
    {
        if( !empty($fqa)
            && !empty($fqa['id']) ){
            return $fqa['id'];
        }
        return '';
    }

    public function getAvatar( $image )
    {
        if( !empty($image)
            && !empty($image['avatar']) ){
            return $image['avatar'];
        }
        return '/styles/dataimages/no-avatar.jpg';
    }

    public function getTitle( $fqa )
    {
        if( !empty($fqa)
            && !empty($fqa['tieu_de']) ){
            return $fqa['tieu_de'];
        }
        return '';
    }

    public function getContent( $fqa )
    {
        if( !empty($fqa)
            && !empty($fqa['noi_dung']) ){
            return $fqa['noi_dung'];
        }
        return '';
    }
}
