<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/3/14
 * Time: 11:26 AM
 */

namespace Cms\Form;


class ProductForm extends GeneralForm{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('products', 'products_id');
        $this->add(array(
            'name' => 'products_code',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'products_code',
                'placeholder' => 'Mã sản phẩm',
                'class'       => 'form-control input-sm',
            ),

        ));
        
        /*$this->add(array(
            'name' => 'categories_id',
            'type' => 'Select',
            'filters'  => array(
                array('name' => 'Int'),
            ),
            'attributes' => array(
                'id'          => 'categories_id',
                'class'       => 'form-control input-sm',
                'onchange'    => 'javascript:load_feature_by_cat(this.value);',
            ),
        ));
		$this->add(array(
            'name' => 'categories_id_list', 
            'type' => 'Select',
            'attributes' => array(
                'id'          => 'categories_id_list',
                'class'       => 'form-control input-sm list_category_multi',
				'multiple'       => 'multiple',
            ),
        ));*/
		
        $this->add(array(
            'name' => 'manufacturers_id',
            'type' => 'Select',
            'filters'  => array(
                array('name' => 'Int'),
            ),
            'attributes' => array(
                'id'          => 'manufacturers_id',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'users_id',
            'type' => 'Hidden',
            'filters'  => array(
                array('name' => 'Int'),
            ),
            'attributes' => array(
                'value'          => '1',
            ),
        ));
        $this->add(array(
            'name' => 'users_fullname',
            'type' => 'Hidden',
            'attributes' => array(
                'value'          => 'VietnamEcom',
            ),
        ));
        $this->add(array(
            'name' => 'products_title',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'products_title',
                'placeholder' => 'Tên sản phẩm',
                'class'       => 'form-control input-sm',
                'onblur'      => 'javascript:locdau(this.value, \'.products_alias\');',
            ),
        ));
        $this->add(array(
            'name' => 'seo_keywords',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'seo_keywords',
                'placeholder' => 'SEO Keywords',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'bao_hanh',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'bao_hanh',
                'placeholder' => 'Bảo hành',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'seo_description',
            'type' => 'Textarea',
            'attributes' => array(
                'id'          => 'seo_description',
                'placeholder' => 'SEO Description',
                'class'       => 'form-control input-sm',
                'rows'        => 4
            ),
        ));
        $this->add(array(
            'name' => 'seo_title',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'seo_title',
                'placeholder' => 'SEO Title',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'youtube_video',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'youtube_video',
                'placeholder' => 'ID video youtube',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'promotion',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'promotion',
                'placeholder' => '',
                'class'       => 'form-control input-sm numberInput',
            ),
        ));
        $this->add(array(
            'name' => 'promotion_ordering',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'promotion_ordering',
                'placeholder' => '',
                'class'       => 'form-control input-sm numberInput',
            ),
        ));
        $this->add(array(
            'name' => 'promotion_description',
            'type' => 'Textarea',
            'attributes' => array(
                'id'          => 'promotion_description',
                'placeholder' => '',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'promotion1',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'promotion1',
                'placeholder' => '',
                'class'       => 'form-control input-sm numberInput',
            ),
        ));
        $this->add(array(
            'name' => 'promotion1_ordering',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'promotion1_ordering',
                'placeholder' => '',
                'class'       => 'form-control input-sm numberInput',
            ),
        ));
        $this->add(array(
            'name' => 'promotion1_description',
            'type' => 'Textarea',
            'attributes' => array(
                'id'          => 'promotion1_description',
                'placeholder' => '',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'promotion2',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'promotion2',
                'placeholder' => '',
                'class'       => 'form-control input-sm numberInput',
            ),
        ));
        $this->add(array(
            'name' => 'promotion2_ordering',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'promotion2_ordering',
                'placeholder' => '',
                'class'       => 'form-control input-sm numberInput',
            ),
        ));
        $this->add(array(
            'name' => 'promotion2_description',
            'type' => 'Textarea',
            'attributes' => array(
                'id'          => 'promotion2_description',
                'placeholder' => '',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'promotion3',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'promotion3',
                'placeholder' => '',
                'class'       => 'form-control input-sm numberInput',
            ),
        ));
        $this->add(array(
            'name' => 'promotion3_ordering',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'promotion3_ordering',
                'placeholder' => '',
                'class'       => 'form-control input-sm numberInput',
            ),
        ));
        $this->add(array(
            'name' => 'promotion3_description',
            'type' => 'Textarea',
            'attributes' => array(
                'id'          => 'promotion3_description',
                'placeholder' => '',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'products_alias',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'products_alias',
                'placeholder' => 'Url',
                'class'       => 'form-control input-sm products_alias',

            ),
        ));
        $this->add(array(
            'name' => 'products_description',
            'type' => 'Textarea',
            'attributes' => array(
                'id'          => 'products_description',
                'placeholder' => 'Mô tả:',
                'class'       => 'form-control input-sm ckeditor',
                'rows'         => 3,
            ),
        ));
        $this->add(array(
            'name' => 'products_longdescription',
            'type' => 'Textarea',
            'attributes' => array(
                'id'          => 'products_longdescription',
                'placeholder' => 'Mô tả:',
                'class'       => 'form-control input-sm',
                'rows'         => 3,
            ),
        ));
        $this->add(array(
            'name' => 'products_more',
            'type' => 'Textarea',
            'attributes' => array(
                'id'          => 'products_more',
                'placeholder' => 'Sản phẩm kèm theo',
                'class'       => 'form-control input-sm',
                'rows'         => 3,
            ),
        ));
        $this->add(array(
            'name' => 'is_published',
            'type' => 'Checkbox',
            'attributes' => array(
                'id'          => 'is_published',
                'class'       => 'form-control input-sm',

            ),
        ));
        $this->add(array(
            'name' => 'is_new',
            'type' => 'checkbox',
            'attributes' => array(
                'id'          => 'is_new',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'is_available',
            'type' => 'checkbox',
            'attributes' => array(
                'id'          => 'is_available',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'is_hot',
            'type' => 'checkbox',
            'attributes' => array(
                'id'          => 'is_hot',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'is_goingon',
            'type' => 'checkbox',
            'attributes' => array(
                'id'          => 'is_goingon',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'is_sellonline',
            'type' => 'checkbox',
            'attributes' => array(
                'id'          => 'is_sellonline',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'is_viewed',
            'type' => 'Hidden',
            'attributes' => array(
                'id'          => 'is_viewed',
                'class'       => 'form-control input-sm',
				'value'		  => 1,
            ),
        ));
        /*$this->add(array(
            'name' => 'position_view',
            'type' => 'Select',
            'attributes' => array(
                'id'          => 'position_view',
                'class'       => 'form-control input-sm',
            ),
			'options' => array(
					'value_options' => array(
						'0' => 'Không hiển thị',
						'1' => 'Vị trí 1 (338 x 340)',
						'2' => 'Vị trí 2 (300 x 200)',
						'3' => 'Vị trí 3 (300 x 200)',
						'4' => 'Vị trí 4 (300 x 200)',
						'5' => 'Vị trí 5 (300 x 580)',
						'6' => 'Vị trí 6 (300 x 200)',
						'7' => 'Vị trí 7 (300 x 580)',
						'8' => 'Vị trí 8 (760 x 180)',
					),
             )
        ));*/
        $this->add(array(
            'name' => 'tra_gop',
            'type' => 'checkbox',
            'attributes' => array(
                'id'          => 'tra_gop',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'ordering',
            'type' => 'Text',
            'filters'  => array(
                array('name' => 'Int'),
            ),
            'attributes' => array(
                'id'          => 'ordering',
                'class'       => 'form-control input-sm numberInput',
                'value'       => '0',
                'style'       => 'width:50px;text-align:center;display:inline;margin-left:5px',
            ),
        ));
        $this->add(array(
            'name' => 'hide_price',
            'type' => 'Checkbox',
            'attributes' => array(
                'id'          => 'hide_price',
                'class'       => 'form-control input-sm',

            ),
        ));
        $this->add(array(
            'name' => 'wholesale',
            'type' => 'Checkbox',
            'attributes' => array(
                'id'          => 'wholesale',
                'class'       => 'form-control input-sm',

            ),
        ));
        $this->add(array(
            'name' => 'price',
            'type' => 'Text',
            'filters'  => array(
                array('name' => 'Int'),
            ),
            'attributes' => array(
                'id'          => 'price',
                'class'       => 'form-control input-sm moneyInput',
            ),
        ));
        $this->add(array(
            'name' => 'price_sale',
            'type' => 'Text',
            'filters'  => array(
                array('name' => 'Int'),
            ),
            'attributes' => array(
                'id'          => 'price_sale',
                'class'       => 'form-control input-sm moneyInput',
            ),
        ));
        $this->add(array(
            'name' => 'quantity',
            'type' => 'Text',
            'filters'  => array(
                array('name' => 'Int'),
            ),
            'attributes' => array(
                'id'          => 'quantity',
                'class'       => 'form-control input-sm numberInput',
            ),
        ));
//        $this->add(array(
//            'name' => 'thumb_image',
//            'type' => '',
//            'attributes' => array(
//                'id'          => 'thumb_image',
//                'class'       => 'form-control input-sm',
//            ),
//        ));
        $this->add(array(
            'name' => 'vat',
            'type' => 'Text',
            'filters'  => array(
                array('name' => 'Int'),
            ),
            'attributes' => array(
                'id'          => 'vat',
                'class'       => 'form-control input-sm numberInput',
            ),
        ));
        $this->add(array(
            'name' => 'tags',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'tags',
                'class'       => 'form-control input-sm tags',
            ),
        ));
        /*$this->add(array(
            'name' => 'type_view',
            'type' => 'Select',
            'attributes' => array(
                'id'          => 'type_view',
                'class'       => 'form-control input-sm type_view',
            ),
        ));*/
        $this->add(array(
            'name' => 'publisher_id',
            'type' => 'Select',
            'attributes' => array(
                'id'          => 'country',
                'class'       => 'form-control input-sm select-multiple-publisher',
                'multiple'       => 'multiple',
            ),
        ));
		/*$this->add(array(
            'name' => 'country_id',
            'type' => 'Select',
            'attributes' => array(
                'id'          => 'country',
                'class'       => 'form-control input-sm select-multiple-country',
                'multiple'       => 'multiple',
            ),
        ));
        $this->add(array(
            'name' => 'cities_id',
            'type' => 'Select',
            'attributes' => array(
                'id'          => 'cities',
                'class'       => 'form-control input-sm select-multiple-cities',
                'multiple'       => 'multiple',
            )
        ));
        $this->add(array(
            'name' => 'districts_id',
            'type' => 'Select',
            'attributes' => array(
                'id'          => 'districts',
                'class'       => 'form-control input-sm list_districts_multi',
				'multiple'       => 'multiple',
            ),
			'options' => array(
					'value_options' => array(
						'0' => 'Tất cả'
					),
             )
        ));
        $this->add(array(
            'name' => 'wards_id',
            'type' => 'Select',
            'attributes' => array(
                'id'          => 'wards',
                'class'       => 'form-control input-sm',
            ),
			'options' => array(
					'value_options' => array(
						'0' => 'Tất cả'
					),
             )
        ));*/
    }
} 