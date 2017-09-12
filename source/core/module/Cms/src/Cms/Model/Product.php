<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/3/14
 * Time: 10:49 AM
 */

namespace Cms\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Product implements InputFilterAwareInterface
{
    public $products_id;
    public $website_id;
    public $products_code;
    public $categories_id;
    public $manufacturers_id;
    public $users_id;
    public $transportation_id;
    public $users_fullname;
    public $products_title;
    public $products_alias;
    public $products_description;
    public $products_longdescription;
    public $products_longdescription_2;
    public $bao_hanh;
    public $promotion;
    public $promotion_description;
    public $promotion_ordering;
    public $promotion1;
    public $promotion1_description;
    public $promotion1_ordering;
    public $promotion2;
    public $promotion2_description;
    public $promotion2_ordering;
    public $promotion3;
    public $promotion3_description;
    public $promotion3_ordering;
    public $seo_keywords;
    public $seo_description;
    public $seo_title;
    public $products_more;
    public $is_published;
    public $is_delete;
    public $is_new;
    public $is_available;
    public $is_hot;
    public $is_goingon;
    public $is_sellonline;
    public $is_viewed;
    public $position_view;
    public $tra_gop;
    public $date_create;
    public $date_update;
    public $hide_price;
    public $wholesale;
    public $price;
    public $price_sale;
    public $ordering;
    public $quantity;
    public $thumb_image;
    public $list_thumb_image;
    public $number_views;
    public $vat;
    public $youtube_video;
    public $tags;
    public $type_view;
	public $categories_id_list;
	
	public $cities_id;
    public $country_id;
    public $districts_id;
	public $ward_id;
    public $language;
	public $publisher_id;
    protected $inputFilter;

    public function exchangeArray($data)
    {
        $this->products_id              = (!empty($data['products_id'])) ? $data['products_id'] : NULL;
        $this->website_id               = (!empty($data['website_id'])) ? $data['website_id'] : $_SESSION['CMSMEMBER']['website_id'];
        $this->products_code            = (!empty($data['products_code'])) ? $data['products_code'] : NULL;
        $this->categories_id            = (!empty($data['categories_id'])) ? $data['categories_id'] : NULL;
        $this->manufacturers_id         = (!empty($data['manufacturers_id'])) ? $data['manufacturers_id'] : NULL;
        $this->users_id                 = (!empty($data['users_id'])) ? $data['users_id'] : NULL;
        $this->transportation_id        = (!empty($data['transportation_id'])) ? $data['transportation_id'] : 0;
        $this->users_fullname           = (!empty($data['users_fullname'])) ? $data['users_fullname'] : "Vietnam Ecom";
        $this->products_title           = (!empty($data['products_title'])) ? $data['products_title'] : NULL;
        $this->products_alias           = (!empty($data['products_alias'])) ? $data['products_alias'] : NULL;
        $this->products_description     = (!empty($data['products_description'])) ? $data['products_description'] : NULL;
        $this->products_longdescription = (!empty($data['products_longdescription'])) ? $data['products_longdescription'] : NULL;
        $this->bao_hanh                 = (!empty($data['bao_hanh'])) ? $data['bao_hanh'] : 0;
        $this->promotion                = (!empty($data['promotion']))  ? $data['promotion']  : NULL;
        $this->promotion_description    = (!empty($data['promotion_description']))  ? $data['promotion_description']  : NULL;
        $this->promotion_ordering       = (!empty($data['promotion_ordering']))  ? $data['promotion_ordering']  : NULL;
        $this->promotion1               = (!empty($data['promotion1'])) ? $data['promotion1'] : NULL;
        $this->promotion1_description   = (!empty($data['promotion1_description'])) ? $data['promotion1_description'] : NULL;
        $this->promotion1_ordering      = (!empty($data['promotion1_ordering']))  ? $data['promotion1_ordering']  : NULL;
        $this->promotion2               = (!empty($data['promotion2'])) ? $data['promotion2'] : NULL;
        $this->promotion2_description   = (!empty($data['promotion2_description'])) ? $data['promotion2_description'] : NULL;
        $this->promotion2_ordering      = (!empty($data['promotion2_ordering']))  ? $data['promotion2_ordering']  : NULL;
        $this->promotion3               = (!empty($data['promotion3'])) ? $data['promotion3'] : NULL;
        $this->promotion3_description   = (!empty($data['promotion3_description'])) ? $data['promotion3_description'] : NULL;
        $this->promotion3_ordering      = (!empty($data['promotion3_ordering']))  ? $data['promotion3_ordering']  : NULL;
        $this->seo_title                = (!empty($data['seo_title']))  ? $data['seo_title']  : NULL;
        $this->seo_keywords             = (!empty($data['seo_keywords']))  ? $data['seo_keywords']  : NULL;
        $this->seo_description          = (!empty($data['seo_description']))  ? $data['seo_description']  : NULL;
        $this->products_more            = (!empty($data['products_more']))  ? $data['products_more']  : NULL;
        $this->is_published             = (!empty($data['is_published'])) ? $data['is_published'] : 0;
        $this->is_delete                = (!empty($data['is_delete'])) ? $data['is_delete'] : 0;
        $this->is_new                   = (!empty($data['is_new'])) ? $data['is_new'] : 0;
        $this->is_available             = (!empty($data['is_available'])) ? $data['is_available'] : 0;
        $this->is_hot                   = (!empty($data['is_hot'])) ? $data['is_hot'] : 0;
        $this->is_goingon               = (!empty($data['is_goingon'])) ? $data['is_goingon'] : 0;
        $this->is_sellonline            = (!empty($data['is_sellonline'])) ? $data['is_sellonline'] : 0;
        $this->is_viewed                = (!empty($data['is_viewed'])) ? $data['is_viewed'] : 0;
        $this->position_view            = (!empty($data['position_view'])) ? $data['position_view'] : 0;
        $this->tra_gop                  = (!empty($data['tra_gop'])) ? $data['tra_gop'] : 0;
        $this->date_create              = (!empty($data['date_create'])) ? $data['date_create'] : date('Y-m-d H:i:s');
        $this->date_update              = (!empty($data['date_update'])) ? $data['date_update'] : NULL;
        $this->hide_price               = (!empty($data['hide_price'])) ? $data['hide_price'] : 0;
        $this->wholesale               = (!empty($data['wholesale'])) ? $data['wholesale'] : 0;
        $this->price                    = (!empty($data['price'])) ? str_replace(',', '',$data['price']) : 0;
        $this->price_sale               = (!empty($data['price_sale'])) ? str_replace(',', '',$data['price_sale']) : 0;
        $this->ordering                 = (!empty($data['ordering'])) ? $data['ordering'] : 0;
        $this->quantity                 = (!empty($data['quantity'])) ? $data['quantity'] : 0;
        $this->thumb_image              = (!empty($data['thumb_image'])) ? $data['thumb_image'] : NULL;
        $this->list_thumb_image         = (!empty($data['list_thumb_image'])) ? $data['list_thumb_image'] : NULL;
        $this->number_views             = (!empty($data['number_views'])) ? $data['number_views'] : 0;
        $this->vat                      = (!empty($data['vat'])) ? $data['vat'] : NULL;
        $this->youtube_video            = (!empty($data['youtube_video'])) ? $data['youtube_video'] : NULL;
        $this->tags                     = (!empty($data['tags'])) ? $data['tags'] : '';
        $this->type_view                = (!empty($data['type_view'])) ? $data['type_view'] : 0;
		$this->categories_id_list            = (!empty($data['categories_id_list'])) ? $data['categories_id_list'] : NULL;
		
		$this->country_id            = (!empty($data['country_id'])) ? $data['country_id'] : NULL;
        $this->cities_id                     = (!empty($data['cities_id'])) ? $data['cities_id'] : NULL;
        $this->districts_id                = (!empty($data['districts_id'])) ? $data['districts_id'] : NULL;
		$this->ward_id            = (!empty($data['ward_id'])) ? $data['ward_id'] : NULL;
        $this->language            = (!empty($data['language'])) ? $data['language'] : 1;
		$this->publisher_id            = (!empty($data['publisher_id'])) ? $data['publisher_id'] : 1;
    }
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $inputFilter->add(array(
                'name' => 'products_id',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'categories_id',
                'required' => FALSE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'manufacturers_id',
                'required' => FALSE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'users_id',
                'required' => FALSE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'products_code',
                'required' => FALSE,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 100,
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'users_fullname',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 256,
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'products_title',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 256,
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'products_alias',
                'required' => TRUE,
                'filters' => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name' => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min' => 1,
                            'max' => 256,
                        ),
                    ),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'products_description',
                'required' => FALSE,
            ));
            $inputFilter->add(array(
                'name' => 'products_longdescription',
                'required' => FALSE,
            ));
            $inputFilter->add(array(
                'name' => 'bao_hanh',
                'required' => FALSE,
            ));
            $inputFilter->add(array(
                'name' => 'price',
                'required' => FALSE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'price_sale',
                'required' => FALSE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'ordering',
                'required' => FALSE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
			 $inputFilter->add(array(
                'name' => 'list_category_multi_data',
                'required' => FALSE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'quantity',
                'required' => FALSE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name' => 'thumb_image',
                'required' => FALSE,
            ));
            $inputFilter->add(array(
                'name' => 'vat',
                'required' => FALSE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
			$inputFilter->add(array(
                'name'     => 'country_id',
                'required' => FALSE,
				'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
			$inputFilter->add(array(
                'name'     => 'cities_id',
                'required' => FALSE,
				'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
			$inputFilter->add(array(
                'name'     => 'districts_id',
                'required' => FALSE,
				'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
			$inputFilter->add(array(
                'name'     => 'ward_id',
                'required' => FALSE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name'     => 'publisher_id',
                'required' => FALSE,
                'filters' => array(
                    array('name' => 'Int'),
                ),
            ));
            $inputFilter->add(array(
                'name'     => 'categories_id_list',
                'required' => FALSE
            ));
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}