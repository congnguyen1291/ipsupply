<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/26/14
 * Time: 9:32 AM
 */

namespace Cms\Form;

class WebsiteForm extends GeneralForm
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('websites', 'website_id');
        $this->add(array(
            'name' => 'template_id',
            'type' => 'hidden'
        ));
        $this->add(array(
            'name' => 'website_name',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'website_name',
                'class'       => 'form-control input-sm',
            ),
        ));
        $this->add(array(
            'name'       => 'website_domain',
            'type'       => 'hidden',
        ));
        $this->add(array(
            'name'       => 'website_domain_refer',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'website_domain_refer',
                'class'       => 'form-control input-sm',
            ),
        ));
        
        $this->add(array(
            'name'       => 'logo',
            'type'       => 'file',
            'attributes' => array(
                'id'          => 'logo',
                'class'       => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name'       => 'facebook_id',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'facebook_id',
                'class'       => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name'       => 'google_client_id',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'google_client_id',
                'class'       => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name'       => 'seo_title',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'seo_title',
                'class'       => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name'       => 'seo_keywords',
            'type'       => 'Textarea',
            'attributes' => array(
                'id'          => 'seo_keywords',
                'class'       => 'form-control input-sm',
                'rows'        => '10',
            ),
        ));

        $this->add(array(
            'name'       => 'seo_description',
            'type'       => 'Textarea',
            'attributes' => array(
                'id'          => 'seo_description',
                'class'       => 'form-control input-sm',
                'rows'        => '10',
            ),
        ));

        $this->add(array(
            'name'       => 'seo_description',
            'type'       => 'Textarea',
            'attributes' => array(
                'id'          => 'seo_description',
                'class'       => 'form-control input-sm',
                'rows'        => '10',
            ),
        ));
        
        $this->add(array(
            'name'       => 'website_email_admin',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'website_email_admin',
                'class'       => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name'       => 'website_email_customer',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'website_email_customer',
                'class'       => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name'       => 'website_name_business',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'website_name_business',
                'class'       => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name'       => 'website_phone',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'website_phone',
                'class'       => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name'       => 'website_address',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'website_address',
                'class'       => 'form-control input-sm',
            ),
        ));

        /*$this->add(array(
            'name' => 'website_city',
            'type' => 'Select',
            'attributes' => array(
                'id'          => 'website_city',
                'class'       => 'form-control input-sm'
            ),
        ));
        */
        $this->add(array(
            'name' => 'website_contries',
            'type' => 'Select',
            'attributes' => array(
                'id'          => 'website_contries',
                'class'       => 'form-control input-sm multichoice',
                'multiple'       => 'multiple'
            ),
        ));

        $this->add(array(
            'name'       => 'website_city_name',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'website_city_name',
                'class'       => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name'       => 'website_contries_name',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'website_contries_name',
                'class'       => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name' => 'website_timezone',
            'type' => 'Select',
            'filters'  => array(
                array('name' => 'Int'),
            ),
            'attributes' => array(
                'id'          => 'website_timezone',
                'class'       => 'form-control input-sm'
            ),
        ));

        $this->add(array(
            'name' => 'website_currency',
            'type' => 'Select',
            'filters'  => array(
                array('name' => 'Int'),
            ),
            'attributes' => array(
                'id'          => 'website_currency',
                'class'       => 'form-control input-sm'
            ),
        ));

        $this->add(array(
            'name'       => 'website_currency_format',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'website_currency_format',
                'class'       => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name'       => 'website_currency_decimals',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'website_currency_decimals',
                'class'       => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name'       => 'website_currency_decimalpoint',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'website_currency_decimalpoint',
                'class'       => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name'       => 'website_currency_separator',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'website_currency_separator',
                'class'       => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name'       => 'website_order_code_prefix',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'website_order_code_prefix',
                'class'       => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name'       => 'website_order_code_suffix',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'website_order_code_suffix',
                'class'       => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name'       => 'website_ga_code',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'website_ga_code',
                'class'       => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name'       => 'javascript',
            'type'       => 'Textarea',
            'attributes' => array(
                'id'          => 'javascript',
                'class'       => 'form-control input-sm',
                'rows'        => '10',
            ),
        ));

        $this->add(array(
            'name'       => 'css',
            'type'       => 'Textarea',
            'attributes' => array(
                'id'          => 'css',
                'class'       => 'form-control input-sm',
                'rows'        => '10',
            ),
        ));

        $this->add(array(
            'name'       => 'url_twister',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'url_twister',
                'class'       => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name'       => 'url_google_plus',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'url_google_plus',
                'class'       => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name'       => 'url_facebook',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'url_facebook',
                'class'       => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name'       => 'url_pinterest',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'url_pinterest',
                'class'       => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name'       => 'url_houzz',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'url_houzz',
                'class'       => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name'       => 'url_instagram',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'url_instagram',
                'class'       => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name'       => 'url_rss',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'url_rss',
                'class'       => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name'       => 'url_youtube',
            'type'       => 'Text',
            'attributes' => array(
                'id'          => 'url_youtube',
                'class'       => 'form-control input-sm',
            ),
        ));

        $this->add(array(
            'name' => 'is_local',
            'type' => 'Checkbox',
            'attributes' => array(
                'id'          => 'is_local',
                'class'       => 'form-control input-sm',

            ),
        ));

        $this->add(array(
            'name' => 'ship',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'ship',
                'class'       => 'form-control input-sm',

            ),
        ));

        $this->add(array(
            'name' => 'website_min_value_slider',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'website_min_value_slider',
                'class'       => 'form-control input-sm website_min_value_slider moneyInput',

            ),
        ));

        $this->add(array(
            'name' => 'website_max_value_slider',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'website_max_value_slider',
                'class'       => 'form-control input-sm website_max_value_slider moneyInput',

            ),
        ));

        $this->add(array(
            'name' => 'type_buy',
            'type' => 'Select',
            'attributes' => array(
                'id'          => 'type_buy',
                'class'       => 'form-control input-sm',

            ),
        ));

        /*$this->add(array(
            'name' => 'is_multilanguage',
            'type' => 'Checkbox',
            'attributes' => array(
                'id'          => 'is_multilanguage',
                'class'       => 'form-control input-sm',

            ),
        ));*/

        $this->add(array(
            'name' => 'has_login_facebook',
            'type' => 'Checkbox',
            'attributes' => array(
                'id'          => 'has_login_facebook',
                'class'       => 'form-control input-sm',

            ),
        ));

        $this->add(array(
            'name' => 'has_login_google',
            'type' => 'Checkbox',
            'attributes' => array(
                'id'          => 'has_login_google',
                'class'       => 'form-control input-sm',

            ),
        ));

        $this->add(array(
            'name' => 'has_login_twister',
            'type' => 'Checkbox',
            'attributes' => array(
                'id'          => 'has_login_twister',
                'class'       => 'form-control input-sm',

            ),
        ));

        $this->add(array(
            'name' => 'version_cart',
            'type' => 'Select',
            'attributes' => array(
                'id'          => 'version_cart',
                'class'       => 'form-control input-sm',

            ),
        ));

        $this->add(array(
            'name' => 'type_crop_image',
            'type' => 'Select',
            'attributes' => array(
                'id'          => 'type_crop_image',
                'class'       => 'form-control input-sm',

            ),
        ));

        $this->add(array(
            'name' => 'confirm_location',
            'type' => 'Checkbox',
            'attributes' => array(
                'id'          => 'confirm_location',
                'class'       => 'form-control input-sm',

            ),
        ));


    }
}