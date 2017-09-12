<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/6/14
 * Time: 11:06 AM
 */
namespace Cms\Form;

class ArticlesForm extends GeneralForm{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct('articles', 'articles_id');
        $this->add(array(
            'name' => 'categories_articles_id',
            'type' => 'Select',
            'filters'  => array(
                array('name' => 'Int'),
            ),
            'attributes' => array(
                'id'          => 'categories_articles_id',
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
                'value'          => 'Tien TV',
            ),
        ));

        $this->add(array(
            'name' => 'thumb_images',
            'type' => 'Hidden'
        ));

        $this->add(array(
            'name' => 'articles_title',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'articles_title',
                'placeholder' => 'Tiêu đề',
                'class'       => 'form-control input-sm',
                'onblur'      => 'javascript:locdau(this.value, \'.articles_alias\');',
            ),
        ));
        $this->add(array(
            'name' => 'articles_alias',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'articles_alias',
                'placeholder' => 'Tiêu đề',
                'class'       => 'form-control articles_alias input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'title_seo',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'title_seo',
                'placeholder' => 'SEO keyword',
                'class'       => 'form-control input-sm',

            ),
        ));
        $this->add(array(
            'name' => 'keyword_seo',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'keyword_seo',
                'placeholder' => 'SEO keyword',
                'class'       => 'form-control input-sm',

            ),
        ));
        $this->add(array(
            'name' => 'description_seo',
            'type' => 'Textarea',
            'attributes' => array(
                'id'          => 'description_seo',
                'placeholder' => 'Description SEO',
                'class'       => 'form-control input-sm',
                'rows'        => '3',

            ),
        ));

        $this->add(array(
            'name' => 'articles_sub_content',
            'type' => 'Textarea',
            'attributes' => array(
                'id'          => 'articles_sub_content',
                'placeholder' => 'Mô tả:',
                'class'       => 'form-control input-sm',
                'rows'         => 3,
            ),
        ));

        $this->add(array(
            'name' => 'articles_content',
            'type' => 'Textarea',
            'attributes' => array(
                'id'          => 'articles_content',
                'placeholder' => 'Mô tả:',
                'class'       => 'form-control ckeditor input-sm',
                'rows'         => 3,
            ),
        ));

        $this->add(array(
            'name' => 'tags',
            'type' => 'Text',
            'attributes' => array(
                'id'          => 'tags',
                'class'       => 'form-control tags input-sm',
            ),
        ));
        $this->add(array(
            'name' => 'is_new',
            'type' => 'Checkbox',
            'attributes' => array(
                'id'          => 'is_new',
                'class'       => 'form-control input-sm',

            ),
        ));
        $this->add(array(
            'name' => 'is_hot',
            'type' => 'Checkbox',
            'attributes' => array(
                'id'          => 'is_hot',
                'class'       => 'form-control input-sm',

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
            'name' => 'is_faq',
            'type' => 'Checkbox',
            'attributes' => array(
                'id'          => 'is_faq',
                'class'       => 'form-control input-sm',

            ),
        ));
        $this->add(array(
            'name' => 'is_static',
            'type' => 'Checkbox',
            'attributes' => array(
                'id'          => 'is_static',
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
                'class'       => 'form-control input-sm numberInput input-ordering',
                'value'       => '0'
            ),
        ));

    }
}