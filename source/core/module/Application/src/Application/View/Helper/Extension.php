<?php
namespace Application\View\Helper;
use Application\View\Helper\App;

class Extension extends App
{
	public function getId( $extension ){
        $id = '';
        if( !empty($extension)
        	&& !empty($extension['id']) ){
    	    $id = $extension['id'];
        }
        return $id;
    }

    public function getName( $extension ){
        $name = '';
        if( !empty($extension)
        	&& !empty($extension['ext_name']) ){
    	    $name = $extension['ext_name'];
        }
        return $name;
    }

    public function getImage( $extension ){
        $image = '';
        if( !empty($extension)
        	&& !empty($extension['icons']) ){
    	    $image = $extension['icons'];
        }
        return $image;
    }

    public function getRequire( $extension ){
        $require = '';
        if( !empty($extension)
        	&& !empty($extension['ext_require']) ){
    	    $require = $extension['ext_require'];
        }
        return $require;
    }

    public function getPrice( $extension ){
        $price = 0;
        if( !empty($extension)
        	&& !empty($extension['price']) ){
    	    $price = $extension['price'];
        }
        return $price;
    }

    public function getDescription( $extension ){
        $description = '';
        if( !empty($extension)
        	&& !empty($extension['ext_description']) ){
    	    $description = $extension['ext_description'];
        }
        return $description;
    }

    public function getQuantity( $extension ){
        $quantity = 0;
        if( !empty($extension)
        	&& !empty($extension['quantity']) ){
    	    $quantity = $extension['quantity'];
        }
        return $quantity;
    }

    public function getIsAvailable( $extension ){
        $is_available = FALSE;
        if( !empty($extension)
        	&& !empty($extension['is_available']) ){
    	    $is_available = TRUE;
        }
        return $is_available;
    }

    public function getIsAlways( $extension ){
        $is_always = FALSE;
        if( !empty($extension)
        	&& !empty($extension['is_always']) ){
    	    $is_always = TRUE;
        }
        return $is_always;
    }

    public function getType( $extension ){
        $type = 0;
        if( !empty($extension)
        	&& !empty($extension['type']) ){
    	    $type = $extension['type'];
        }
        return $type;
    }

    public function getReferProductId( $extension ){
        $refer_product_id = 0;
        if( !empty($extension)
        	&& !empty($extension['refer_product_id']) ){
    	    $refer_product_id = $extension['refer_product_id'];
        }
        return $refer_product_id;
    }

    public function getProductId( $extension ){
        $products_id = 0;
        if( !empty($extension)
        	&& !empty($extension['products_id']) ){
    	    $products_id = $extension['products_id'];
        }
        return $products_id;
    }

}
