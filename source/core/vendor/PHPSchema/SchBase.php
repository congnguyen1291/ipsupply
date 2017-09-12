<?php

/**
 * Project : classifiedengine
 * User: thuytien
 * Date: 10/23/2014
 * Time: 11:15 AM
 */
namespace Schema;
use Schema\SchBaseInterface;

abstract class SchBase implements SchBaseInterface
{
    /**
     * @var array
     */
    protected $properties = array();
    /**
     * @var string
     */
    protected $protocol = 'http';
    /**
     * @var string
     */
    protected $namespace = '';

    /**
     * @return static
     */
    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new static();
        }
        return $instance;
    }

    /**
     * @return string
     */
    public function scope()
    {
        return sprintf("itemscope=\"itemscope\" itemtype=\"%1s\"", $this->getURL());
    }

    /**
     * @param $prop
     * @return string
     */
    public function prop($prop, $val = null)
    {
        $attr = '';
        if (!empty($prop) ) {
            if (property_exists($this, $prop)) {
                $props = explode(",", $this->$prop);
                $parentName = "Schema\Sch" . $props[0];
                if ( class_exists($parentName) ) {
                    $attr .= $parentName::getInstance()->scope();
                }
                $attr = sprintf(" itemprop=\"%1s\" ", $prop) . $attr;
            }
        }

        if (isset($val)) {
            $attr .= $this->wrapPropVals($prop, $val);
        }
        if( empty($attr) ){
            $attr = $this->scope();
        }
        return trim($attr);
    }
    function wrapPropVals($prop, $val)
    {
        return ' content="' . $val . '"';
    }
    /**
     * @return string
     */
    private function getURL()
    {
        return $this->protocol . "://schema.org/" . $this->namespace;
    }
}