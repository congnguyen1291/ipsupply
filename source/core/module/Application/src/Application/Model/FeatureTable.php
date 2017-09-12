<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/25/14
 * Time: 2:51 PM
 */
namespace Application\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\ResultSet\ResultSet;
use Zend\Filter\File\LowerCase;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Validator\GreaterThan;

use Application\Model\AppTable;

class FeatureTable extends AppTable {

    public function fetchAll()
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':FeatureTable:fetchAll');
        $results = $cache->getItem($key);
        if(!$results){
            try {
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->from('feature');
                $select->where(array(
                    'website_id'=>$this->getWebsiteId(),
					'is_delete'=>0,
					'is_published'=>1
                ));
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getFeatureByID($feature_id)
    {
        $stri_key = $this->createKeyCacheFromArray($feature_id);
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':FeatureTable:getFeatureByID('.$stri_key.')');
        $results = $cache->getItem($key);
        if(!$results){
            try{
                $adapter = $this->tableGateway->getAdapter();
                $sql = new Sql($adapter);
                $select = $sql->select();
                $select->from('feature');
                $select->where(array(
                    'feature_id'=>$feature_id,
                    'website_id'=>$this->getWebsiteId(),
					'is_delete'=>0,
					'is_published'=>1
                ));
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getFeatureByIDV2($feature_id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':FeatureTable:getFeatureByIDV2('.$feature_id.')');
        $results = $cache->getItem($key);
        if(!$results){
            try{
                $features = $this->fetchAll();
                $results = array();
                foreach ($features as $key => $feature) {
                    if($feature_id == $feature['feature_id']){
                        $results = $feature;
                        break;
                    }
                }
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getByCatId($catid)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':FeatureTable:getByCatId('.(is_array($catid)? implode('-',$catid) : $catid).')');
        $results = $cache->getItem($key);
        if(!$results){
            try{
                $sql = "SELECT `feature`.*
                        FROM `feature`
                        INNER JOIN `categories_feature` ON `categories_feature`.feature_id=`feature`.feature_id
                        WHERE `categories_feature`.`categories_id`={$catid} AND feature.website_id = {$this->getWebsiteId()} ";
                $adapter = $this->tableGateway->getAdapter();
                $results = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                $cache->setItem($key,$results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getAllFeatureOfCategoryAndSort($categories_id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':FeatureTable:getAllFeatureOfCategoryAndSort('.$categories_id.')');
        $results = $cache->getItem($key);
        if(!$results){
            try {
                $features = $this->getByCatId($categories_id);
                $results = array();
                $map = array();
                foreach ($features as $key => $feature) {
                    $parent_id = $feature['parent_id'];
                    if( isset($map['map'][$parent_id]) ){
                        $map['map'][$feature['feature_id']] =  $map['map'][$parent_id];
                        $map['map'][$feature['feature_id']][] =  $parent_id;
                    }else{
                        $map['map'][$feature['feature_id']] =  array($parent_id);
                    }

                    $feature['map'] = $map['map'][$feature['feature_id']];

                    if(isset($results[$parent_id])){
                        $results[$parent_id][] = $feature;
                    }else{
                        $results[$parent_id] = array($feature);
                    }
                }
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getAllFeatureAndSort()
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':FeatureTable:getAllFeatureAndSort');
        $results = $cache->getItem($key);
        if(!$results){
            try {
                $features = $this->fetchAll();
                $results = array();
                $map = array();
                foreach ($features as $key => $feature) {
                    $parent_id = $feature['parent_id'];
                    
                    if( isset($map['map'][$parent_id]) ){
                        $map['map'][$feature['feature_id']] =  $map['map'][$parent_id];
                        $map['map'][$feature['feature_id']][] =  $parent_id;
                    }else{
                        $map['map'][$feature['feature_id']] =  array($parent_id);
                    }

                    $feature['map'] = $map['map'][$feature['feature_id']];
                    if( isset($results[$parent_id]) ){
                        $results[$parent_id][] = $feature;
                    }else{
                        $results[$parent_id] = array($feature);
                    }
                }
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function getAllFeatureWithCateAndSort($categories_id)
    {
        $cache = $this->getServiceLocator()->get('cache');
        $key = md5($this->getNamspaceCached().':FeatureTable:getAllFeatureWithCateAndSort('.$categories_id.')');
        $results = $cache->getItem($key);
        if(!$results){
            try {
                $features = $this->getByCatId($categories_id);
                $results = array();
                foreach ($features as $key => $feature) {
                    $parent_id = $feature['parent_id'];
                    if(isset($results[$parent_id])){
                        $results[$parent_id][] = $feature;
                    }else{
                        $results[$parent_id] = array($feature);
                    }
                }
                $cache->setItem($key, $results);
            }catch(\Exception $ex){
                $results = array();
            }
        }
        return $results;
    }

    public function saveFeature(Feature $feat)
    {
        $data = array(
            'website_id'=>$this->getWebsiteId(),
            'parent_id' => $feat->parent_id,
            'feature_title' => $feat->feature_title,
            'feature_alias' => $feat->feature_alias,
            'is_published' => $feat->is_published,
            'is_value' => $feat->is_value,
            'is_delete' => $feat->is_delete,
            'date_create' => $feat->date_create,
            'date_update' => $feat->date_update,
            'ordering' => $feat->ordering,
        );

        $id = (int)$feat->feature_id;
        $this->saveData($data, $id);
    }

    public function deleteFeature($id)
    {
        $this->tableGateway->delete(array('feature_id' => (int)$id, 'website_id'=>$this->getWebsiteId() ));
    }

    public function getHtmlFeature($data, $checked, $parentid = 0)
    {
        $html = '';
        foreach ($data as $row) {
			$html.="<h2>Đặc tính sản phẩm</h2>";
            if ($row['parent_id'] == $parentid) {
                if ($row['children'] > 0) {
					
                    if ($row['is_value'] == 0) {
                        $html .= '<div class="panel panel-default panel-checkbox-' . $row['feature_id'] . '">';
                        $html .= '<div class="panel-heading" style="overflow:hidden">
                                    <div class="pull-left">
                                    <label><input type="hidden" name="featureid[]" value="' . $row['feature_id'] . '" /> ' . $row['feature_title'] . '</label>
                                    </div>
                                    <div class="pull-right"><a href="javascript:;" class="open btn btn-default btn-sm" onclick="javascript:showup(this);">-</a></div>
                                 </div>';
                        $html .= '<div class="panel-body">';
                        $html .= '<div class="row">
                            <div style="padding:10px;">';
                        $html .= $this->getHtmlFeature($data, $checked, $row['feature_id']);
                        $html .= "</div></div></div></div>";
                    } else {
                        $html .= '<div class="panel panel-default panel-checkbox-' . $row['feature_id'] . '">';
                        $html .= '<div class="panel-body">';
                        $html .= '<div class="col-xs-12"><label><input type="checkbox" style="display:none" name="featureid[]" class="childcheck normal" rel="' . $row['parent_id'] . '" value="' . $row['feature_id'] . '"' . ((array_key_exists($row['feature_id'], $checked)) ? ' checked ' : '') . ' />' . $row['feature_title'] . '</label>: <input type="text" name="feature_val[' . $row['feature_id'] . ']" id="" value="'.((array_key_exists($row['feature_id'], $checked)) ? $checked[$row['feature_id']] : '').'" onkeyup="javascript:change_state(this);" /></div>';
                        $html .= '</div></div>';
                    }
                } else {
                    if ($row['parent_id'] != 0) {
                        if($row['is_value'] == 0){
                            $html .= '<div class="col-xs-12"><label><input type="checkbox" name="featureid[]" class="childcheck" rel="' . $row['parent_id'] . '" value="' . $row['feature_id'] . '"' . ((array_key_exists($row['feature_id'], $checked)) ? ' checked ' : '') . ' />  ' . $row['feature_title'] . '</label></div>';
                        }else{
                            $html .= '<div class="col-xs-12"><label><input type="checkbox" style="display:none" name="featureid[]" class="childcheck normal" rel="' . $row['parent_id'] . '" value="' . $row['feature_id'] . '"' . ((array_key_exists($row['feature_id'], $checked)) ? ' checked ' : '') . ' />' . $row['feature_title'] . '</label>: <input type="text" name="feature_val[' . $row['feature_id'] . ']" id="" value="'.((array_key_exists($row['feature_id'], $checked)) ? $checked[$row['feature_id']] : '').'" onkeyup="javascript:change_state(this);" /></div>';
                        }
                    } else {

                        if($row['is_value'] != 0){
                        $html .= '<div class="panel panel-default panel-checkbox-' . $row['feature_id'] . '">';
                        $html .= '<div class="panel-body">';
                            $html .= '<div class="col-xs-12"><label><input type="checkbox" style="display:none" name="featureid[]" class="childcheck normal" rel="' . $row['parent_id'] . '" value="' . $row['feature_id'] . '"' . ((array_key_exists($row['feature_id'], $checked)) ? ' checked ' : '') . ' />' . $row['feature_title'] . '</label>: <input type="text" name="feature_val[' . $row['feature_id'] . ']" id="" value="'.((array_key_exists($row['feature_id'], $checked)) ? $checked[$row['feature_id']] : '').'" onkeyup="javascript:change_state(this);" /></div>';
                        $html .= '</div></div>';
                        }else{
                            $html .= '<div class="panel panel-default panel-checkbox-' . $row['feature_id'] . '">';
                            $html .= '<div class="panel-body">';
                            $html .= '<div class="col-xs-12"><label><input type="checkbox" name="featureid[]" class="childcheck" rel="' . $row['parent_id'] . '" value="' . $row['feature_id'] . '"' . ((array_key_exists($row['feature_id'], $checked)) ? ' checked ' : '') . ' />  ' . $row['feature_title'] . '</label></div>';
                            $html .= '</div></div>';
                        }
                    }
                }
            }
        }
        return $html;
    }

    public function getHtmlFeatureChoose($data, $checked, $parentid = 0)
    {
        $html = '';
        foreach ($data as $row) {
            if ($row['parent_id'] == $parentid) {
                if ($row['children'] > 0) {
                    if ($row['is_value'] == 0) {
                        $html .= '<div class="panel panel-default panel-checkbox-' . $row['feature_id'] . '">';
                        $html .= '<div class="panel-heading" style="overflow:hidden">
                                    <div class="pull-left">
                                    <label><input type="checkbox" name="featureid[]" class="parentcheck" rel="' . $row['feature_id'] . '" value="' . $row['feature_id'] . '"' . (in_array($row['feature_id'], $checked) ? ' checked ' : '') . ' /> ' . $row['feature_title'] . ($row['is_value'] != 0 ? '(Tự nhập)' : '') . '</label>
                                    </div>
                                    <div class="pull-right"><a href="javascript:;" class="open btn btn-default btn-sm" onclick="javascript:showup(this);">-</a></div>
                                  </div>';
                        $html .= '<div class="panel-body">';
                        $html .= '<div class="row">
                                                    <div style="padding:10px;">';
                        $html .= $this->getHtmlFeatureChoose($data, $checked, $row['feature_id']);
                        $html .= "</div></div></div></div>";
                    } else {
                        $html .= '<div class="panel panel-default panel-checkbox-' . $row['feature_id'] . '">';
                        $html .= '<div class="panel-body">';
                        $html .= '<div class="col-xs-12"><label><input type="checkbox" name="featureid[]" class="childcheck" rel="' . $row['parent_id'] . '" value="' . $row['feature_id'] . '"' . (in_array($row['feature_id'], $checked) ? ' checked ' : '') . ' />  ' . $row['feature_title'] . ($row['is_value'] != 0 ? '(Tự nhập)' : '') . '</label></div>';
                        $html .= '</div></div>';
                    }
                } else {
                    if ($row['parent_id'] != 0) {
                        $html .= '<div class="col-xs-12"><label><input type="checkbox" name="featureid[]" class="childcheck" rel="' . $row['parent_id'] . '" value="' . $row['feature_id'] . '"' . (in_array($row['feature_id'], $checked) ? ' checked ' : '') . ' />  ' . $row['feature_title'] . ($row['is_value'] != 0 ? '(Tự nhập)' : '') . '</label></div>';
                    } else {
                        $html .= '<div class="panel panel-default panel-checkbox-' . $row['feature_id'] . '">';
                        $html .= '<div class="panel-body">';
                        $html .= '<div class="col-xs-12"><label><input type="checkbox" name="featureid[]" class="childcheck" rel="' . $row['parent_id'] . '" value="' . $row['feature_id'] . '"' . (in_array($row['feature_id'], $checked) ? ' checked ' : '') . ' />  ' . $row['feature_title'] . ($row['is_value'] != 0 ? '(Tự nhập)' : '') . '</label></div>';
                        $html .= '</div></div>';
                    }
                }
            }
        }
        return $html;
    }

    public function getAllFeatureOfWebsite($website_id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('feature');
        $select->where(array(
            'website_id'=>$website_id,
            'is_delete'=>0,
            'is_published'=>1
        ));
        $selectString = $sql->getSqlStringForSqlObject($select);
        $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
        $results = $results->toArray();
        return $results;
    }

    public function getAllFeatureOfWebsiteAndSort($website_id)
    {
        $features = $this->getAllFeatureOfWebsite($website_id);
        $results = array();
        foreach ($features as $key => $feature) {
            $parent_id = $feature['parent_id'];
            if(isset($results[$parent_id])){
                $results[$parent_id][] = $feature;
            }else{
                $results[$parent_id] = array($feature);
            }
        }
        return $results;
    }

    public function insertFeatures($data)
    {
        $this->tableGateway->insert($data);
        return $this->getLastestId();
    }

    public function removeAllFeaturesOfWebsite($website_id)
    {
        $this->tableGateway->delete(array('website_id' => $website_id));
    }
    
}