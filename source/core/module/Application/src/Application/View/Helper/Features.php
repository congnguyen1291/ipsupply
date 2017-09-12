<?php
namespace Application\View\Helper;
use Application\View\Helper\App;

class Features extends App
{
    public function getAllFeatureAndSort()
    {
        $features = $this->getModelTable('FeatureTable')->getAllFeatureAndSort();
        $this->setJavascriptFeaturesSort($features, 'getAllFeatureAndSort');
        return $features;
    }

    public function getFeatureByID($feature_id)
    {
        $features = $this->getModelTable('FeatureTable')->getFeatureByID($feature_id);
        return $features;
    }

    public function getFeatureByIDV2($feature_id)
    {
        $features = $this->getModelTable('FeatureTable')->getFeatureByIDV2($feature_id);
        return $features;
    }

    public function getAllFeatureOfCategoryAndSort($categories_id)
    {
        $features = $this->getModelTable('FeatureTable')->getAllFeatureOfCategoryAndSort($categories_id);
        $this->setJavascriptFeaturesSort($features, 'getAllFeatureOfCategoryAndSort');
        return $features;
    }

    public function getFeatureInFeatureSort($feature, $features)
    {
        $result = array();
        if( !empty($feature) && !empty($feature['map'])
            && !empty($features)){
            $tmp = $features;
            foreach ($feature['map'] as $key => $value) {
                if( !empty($tmp[$value]) ){
                    $tmp = $tmp[$value];
                }else{
                    $tmp = array();
                    break;
                }
            }
            $result = $tmp;
        }
        return $result;
    }

    public function getRootInFeatureSort($features)
    {
        $result = array();
        if( !empty($features) && !empty($features[0]) ){
            $result = $features[0];
        }
        return $result;
    }

    public function getLeafFeatureInFeatureSort($feature_id, $features)
    {
        $result = array();
        if( !empty($features) && !empty($features[$feature_id]) ){
            $result = $features[$feature_id];
        }
        return $result;
    }

    public function getID($feature)
    {
        $result = '';
        if( !empty($feature) && !empty($feature['feature_id']) ){
            $result = $feature['feature_id'];
        }
        return $result;
    }

    public function getName($feature)
    {
        $result = '';
        if( !empty($feature) && !empty($feature['feature_title']) ){
            $result = $feature['feature_title'];
        }
        return $result;
    }

    public function getColor($feature)
    {
        $result = 'transparent';
        if( !empty($feature) && !empty($feature['feature_color']) ){
            $result = $feature['feature_color'];
        }
        return $result;
    }

    public function getPattern($feature)
    {
        $result = '';
        if( !empty($feature) && !empty($feature['feature_file']) ){
            $result = $feature['feature_file'];
        }
        return $result;
    }

    public function hasChoose($feature)
    {
        $fea = $this->view->Params()->fromQuery('feature', array());
        if( !is_array($fea) )
            $fea = explode( ';', $fea);
        if( is_array($fea)
            && !empty($feature) 
            && !empty($feature['feature_id']) ){
            return in_array($feature['feature_id'], $fea);
        }
        return FALSE;
    }

    public function isColor($feature)
    {
        if( !empty($feature) 
            && !empty($feature['feature_type']) && !empty($feature['is_value'])
            && $feature['feature_type'] == 2 && $feature['is_value'] == 3 ){
            return TRUE;
        }
        return FALSE;
    }

    public function isPattern($feature)
    {
        if( !empty($feature) 
            && !empty($feature['feature_type']) && !empty($feature['is_value'])
            && $feature['feature_type'] == 2 && $feature['is_value'] == 4 ){
            return TRUE;
        }
        return FALSE;
    }

    public function isNormal($feature)
    {
        if( !$this->isColor($feature) && !$this->isPattern($feature) ){
            return TRUE;
        }
        return FALSE;
    }
	
}
