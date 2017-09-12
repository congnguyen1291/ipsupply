<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/25/14
 * Time: 2:51 PM
 */
namespace Cms\Model;

use Zend\Db\Sql\Expression;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

use Cms\Model\AppTable;

class FeatureTable extends AppTable
{
    public function fetchAll( $params = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('*', 'number_childrens_active'=> new Expression('(SELECT COUNT(*)
            FROM `feature` AS `t1`
            LEFT JOIN `feature` AS `t2` ON `t2`.`parent_id` = `t1`.`feature_id`
            LEFT JOIN `feature` AS `t3` ON `t3`.`parent_id` = `t2`.`feature_id`
            LEFT JOIN `feature` AS `t4` ON `t4`.`parent_id` = `t3`.`feature_id`
            WHERE `t1`.`parent_id` = `feature`.`feature_id` 
            AND (`t1`.`is_published` = 1 OR `t2`.`is_published` = 1 OR `t3`.`is_published` = 1 OR `t4`.`is_published` = 1))'), 'total_childrens_active'=> new Expression('(SELECT COUNT(*)
            FROM `feature` AS `t1`
            LEFT JOIN `feature` AS `t2` ON `t2`.`parent_id` = `t1`.`feature_id`
            LEFT JOIN `feature` AS `t3` ON `t3`.`parent_id` = `t2`.`feature_id`
            LEFT JOIN `feature` AS `t4` ON `t4`.`parent_id` = `t3`.`feature_id`
            WHERE `t1`.`parent_id` = `feature`.`feature_id`)')));
        $select->from('feature');

        if( isset($params['parent_id']) ){
            $select->where(array(
                'feature.parent_id' => $params['parent_id']
            ));
        }

        if( isset($params['feature_title']) ){
            $feature_title = $this->toAlias($params['feature_title']);
            $select->where->like('feature_alias', "%{$feature_title}%");
        }

        if( isset($params['feature_color']) ){
            $select->where->like('feature_color', "%{$params['feature_color']}%");
        }

        if( $this->hasPaging($params) ){
            $select->offset($this->getOffsetPaging($params['page'], $params['limit']));
            $select->limit($params['limit']);
        }
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $result = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $result = $result->toArray();
            return $result;
        }catch (\Exception $ex){}
        return array();
    }

    public function countAll(  $params = array() )
    {
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->columns(array('total' => new Expression("COUNT(feature.feature_id)")));
        $select->from('feature');

        if( isset($params['parent_id']) ){
            $select->where(array(
                'feature.parent_id' => $params['parent_id']
            ));
        }

        if( isset($params['feature_title']) ){
            $feature_title = $this->toAlias($params['feature_title']);
            $select->where->like('feature_alias', "%{$feature_title}%");
        }

        if( isset($params['feature_color']) ){
            $select->where->like('feature_color', "%{$params['feature_color']}%");
        }
        
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->current();
            return $results->total;
        }catch (\Exception $ex){}
        return 0;
    }


    public function getById($id)
    {
        $id = (int)$id;
        $rowset = $this->tableGateway->select(array('feature_id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function getFeature($id)
    {
        return $this->getById($id);
    }

    public function fetchAllAndSort()
    {
        $rows = $this->fetchAll();
        $listFeature = array();
        if(COUNT($rows)>0){
            foreach ($rows as $item ) {
                $parent_id = $item['parent_id'];
                if (isset($listFeature[$parent_id]) && !empty($listFeature[$parent_id]) ) {
                    $listFeature[$parent_id][] = $item;
                } else {
                    $listFeature[$parent_id] = array($item);
                }
            }
        }
        return $listFeature;
    }

    public function getByCatId($catid)
    {
        if( !empty($catid) ){
            $adapter = $this->tableGateway->getAdapter();
            $sql = new Sql($adapter);
            $select = $sql->select();
            $select->from('feature');
            $select->join('categories_feature', '`categories_feature`.feature_id=`feature`.feature_id', array());
            $select->where(array(
                'feature.is_delete' => 0,
                'feature.is_published' => 1,
                'feature.parent_id' => $catid,
            ));
            try{
                $selectString = $sql->getSqlStringForSqlObject($select);
                $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
                $results = $results->toArray();
                return $results;
            }catch (\Exception $ex){}
        }
        return array();
    }

    public function getFeatureByAlias($feature_alias)
    {
        $sql = "SELECT `feature`.*
                FROM `feature`
                WHERE `feature`.`feature_alias` LIKE '{$feature_alias}' AND feature.is_delete=0 AND feature.is_published=1 AND feature.website_id='{$this->getWebsiteId()}'";
        $adapter = $this->tableGateway->getAdapter();
        $result = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
        $result = $result->toArray();
        return $result;
    }

    public function saveFeature(Feature $feat)
    {
        try {
            $data = array(
                'website_id' => $this->getWebsiteId(),
                'parent_id' => $feat->parent_id,
                'feature_title' => $feat->feature_title,
                'feature_alias' => $feat->feature_alias,
                'is_published' => $feat->is_published,
                'is_value' => $feat->is_value,
                'is_delete' => $feat->is_delete,
                'date_create' => $feat->date_create,
                'date_update' => $feat->date_update,
                'ordering' => $feat->ordering,
                'feature_type' => $feat->feature_type,
                'feature_color' => $feat->feature_color,
                'feature_file' => $feat->feature_file,
                'ordering' => $feat->ordering,
            );
            $id = (int) $feat->feature_id;
            if ( empty($id) ) {
                $this->tableGateway->insert($data);
                $id = $this->getLastestId();
            } else {
                if ( $this->getFeature( $id )) {
                    $this->tableGateway->update($data, array('feature_id' => $id));
                } else {
                    throw new \Exception('Row does not exist');
                }
            }
            return $id;
        } catch (\Exception $e ) {}
        return 0;
    }

    public function addCategoryFeature($data)
    {
        try {
            $row = array(
                'website_id' => $this->getWebsiteId(),
                'parent_id' => $data['parent_id'],
                'feature_title' => $data['feature_title'],
                'feature_alias' => $data['feature_alias'],
                'is_published' => $data['is_published'],
                'is_value' => $data['is_value'],
                'is_delete' => $data['is_delete'],
                'date_create' => $data['date_create'],
                'date_update' => $data['date_update'],
                'ordering' => $data['ordering'],
                'feature_type' => $data['feature_type'],
                'feature_color' => $data['feature_color'],
                'feature_file' => $data['feature_file'],
                'ordering' => $data['ordering'],
                'is_delete' => 0,
                'date_create' => date('Y-m-d H:m:s'),
            );
            $categories_id = $data['categories_id'];
            $this->tableGateway->insert($row);
            $id = $this->getLastestId();
            $row['feature_id'] = $id;
            $sql = "INSERT INTO categories_feature(`categories_id`,`feature_id`) VALUES ({$categories_id}, {$id})";
            try{
                $adapter = $this->tableGateway->getAdapter();
                $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
            }catch(\Exception $e){
                return array();
            }
            return $row;
        } catch (\Exception $e) {
            return array();
        }
    }

    public function deleteFeature($id)
    {
        $this->tableGateway->delete(array('feature_id' => (int)$id));
    }

    public function getHtmlFeature_($data, $checked, $parentid = 0)
    {
        $html = '';
        foreach ($data as $row) {
            if ($row['parent_id'] == $parentid) {
                if ($row['children'] > 0) {

                    if ($row['is_value'] == 0) {
                        $html .= '<div class="panel panel-default panel-checkbox-' . $row['feature_id'] . '">';
                        $html .= '<div class="panel-heading" style="overflow:hidden">
                                    <div class="pull-left">
                                    <label><input type="checkbox" name="featureid[]" value="' . $row['feature_id'] . '"' . (array_key_exists($row['feature_id'], $checked) ? ' checked ' : '') . ' /> ' . $row['feature_title'] . '</label>
                                    </div>
                                    <div class="pull-right"><a href="javascript:;" class="btn btn-default btn-sm" onclick="javascript:showup(this);">+</a></div>
                                 </div>';
                        $html .= '<div class="panel-body" style="display:none">';
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

    public function getHtmlFeature($data, $checked, $parentid = 0)
    {
        $html = '';
        foreach ($data as $key => $feature) {
            if ($feature['parent_id'] == $parentid) {
                if ( $parentid == 0 ) {
                    $html .= '<li class="li_'.$feature['feature_id'].'" >
                                <div class="clearfix" >
                                    <div class="pull-left" >
                                        <label>
                                            <input type="checkbox" name="featureid[]" value="'.$feature['feature_id'].'" ' . (array_key_exists($feature['feature_id'], $checked) ? ' checked ' : '') . ' class="checkall" >
                                            '   .$feature['feature_title']. '
                                        </label>
                                    </div>
                                    <div class="pull-right" >
                                        <a data-toggle="collapse" data-parent="#accordion1" data-target="#features_'.$feature['feature_id'].'" >
                                            <i class="fa fa-chevron-down" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                </div>
                                <div id="features_'.$feature['feature_id']. '" class="collapse" >

                                    <ul class="list-child-features children_'.$key.'" >
                                        ' .$this->getHtmlFeature($data, $checked, $feature['feature_id']). '
                                        <li>
                                            <div class="add-future-ajax" >
                                                <h4>Thêm thuộc tính cho <b>' .$feature['feature_title']. '</b></h4>
                                                <div class="row">
                                                    <div class="col-xs-12" id="box-primary-form-1" data-row="1">
                                                        <div class="row" >
                                                            <input type="hidden" name="feature[' .$key. '][parent_id]" id="parent_id_' .$key. '" value="' .$feature['feature_id']. '">
                                                            <input type="hidden" name="feature[' .$key. '][feature_type]" id="feature_type_' .$key. '" value="' .$feature['feature_type']. '">
                                                            <div class="col-sm-12" >
                                                                <div class="row" >
                                                                    <div class="col-md-3 col-sm-6" >
                                                                        <div class="form-group">
                                                                            <label for="feature_title"> 
                                                                                Tên
                                                                            </label>
                                                                            <input type="text" name="feature[' .$key. '][feature_title]" id="feature_title_' .$key. '" placeholder="Tiêu đề" class="form-control" onblur="javascript:locdau(this.value, \'.feature_alias_' .$key. '\');" value="">
                                                                        </div>
                                                                    </div>

                                                                    <!--ADD CHILDREN-->
                                                                    <div class="col-md-2 col-sm-6" id="is_value_' .$key. '" '. ($feature['feature_type'] == 1 ? "style='display: none'" : '').' >
                                                                        <div class="form-group">
                                                                            <label for="is_value">
                                                                                Ban chon
                                                                            </label>
                                                                            <select name="feature[' .$key. '][is_value]" id="is_value_feature_'.$key. '"  data-row="' .$key. '"  class="form-control is_value_feature select-commom-feature">
                                                                                <option value="3" selected="selected">Color</option>
                                                                                <option value="4">Icon</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-3 col-sm-6 color-feature-picker" id="color-picker-' .$key. '" '. ($feature['feature_type'] == 1 ? "style='display: none'" : '').' >
                                                                        <label for="feature_color"> 
                                                                            feature_color
                                                                        </label>
                                                                        <div class="input-group colorpicker-component">
                                                                            <span class="input-group-addon"><i></i></span>
                                                                            <input type="text" name="feature[' .$key. '][feature_color]" id="feature_color_' .$key. '" class="form-control feature_file" value="#00aabb">
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-3 col-sm-6" id="file_feature_' .$key. '" style="display: none;" >
                                                                        <div class="form-group">
                                                                            <label for="feature_file_' .$key. '"> 
                                                                                feature file
                                                                            </label>
                                                                            <input type="text" name="feature[' .$key. '][feature_file]" id="feature_file_' .$key. '" placeholder="Đường dẫn hình" class="form-control feature_file" value="" style="display: none;">
                                                                            <img id="img-file-feature_' .$key. '" src="" class="img-thumbnail " width="80" height="80"  style="display: none;">
                                                                            <button type="button" data-row="' .$key. '" class="btn btn-link btn-upload-attr" width="80" height="80">Images</button>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-4 col-sm-6" >
                                                                        <div class="row" >
                                                                            <!--END ADD CHILDREN-->
                                                                            <div class="col-md-3 col-sm-6" >
                                                                                <div class="form-group">
                                                                                    <label for="is_published">
                                                                                        active
                                                                                        <input type="checkbox" name="feature[' .$key. '][is_published]" id="is_published_' .$key. '" class="form-control" checked="checked" value="1" style="position: absolute; opacity: 0;">
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-3 col-sm-6" >
                                                                                <div class="form-group">
                                                                                    <label for="ordering"> 
                                                                                        position
                                                                                    </label>
                                                                                    <input type="text" name="feature[' .$key. '][ordering]" id="ordering_' .$key. '" class="form-control numberInput" style="width:50px;text-align:center;display:inline;margin-left:5px" value="0">
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-3 col-sm-6" >
                                                                                <label>
                                                                                    <a href="javascript:void(0)" onclick="save_feature_category(' .$key. ')"  title="save" class="btn glyphicon glyphicon-download-alt save_feature_' .$key. '"></a>

                                                                                </label>
                                                                            </div>
                                                                            <input type="text" name="feature[' .$key. '][feature_alias]" id="feature_alias_' .$key. '" placeholder="Đường dẫn" class="feature_alias feature_alias_' .$key. '" style="display:none" value="" >
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </li>';
                } else {
                    $html .= '<li class="li_'.$feature['feature_id']. '">
                                    <label>
                                        <input type="checkbox" name="featureid[]" value="' .$feature['feature_id']. '" ' . (array_key_exists($feature['feature_id'], $checked) ? ' checked ' : '') . ' class="checksub" >
                                        ' .$feature['feature_title'].'
                                        '. ( ($feature['is_value'] == 4 && $feature['feature_type'] == 2) ? '<span style="width:20px;height:20px;display:inline-block;background:url('.$feature['feature_file'].') no-repeat;vertical-align: middle;border: 1px solid #ccc;margin-left: 10px;" ></span>' : '' ). '
                                        '. ( ($feature['is_value'] == 3 && $feature['feature_type'] == 2) ? '<input type="color" name="favcolor" value="' .$feature['feature_color']. '" style="margin-left: 10px;" >' : '' ). '
                                    </label>
                                </li>';
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
                                    <div class="pull-right"><a href="javascript:;" class="btn btn-default btn-sm" onclick="javascript:showup(this);">+</a></div>
                                  </div>';
                        $html .= '<div class="panel-body" style="display:none">';
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

    public function getAllFeatureByParentFeatureId($id)
    {
        $sql = "SELECT `feature`.*
                FROM `feature`
                WHERE `feature`.`parent_id` LIKE '{$id}' AND feature.is_delete=0 AND feature.is_published=1 AND feature.website_id='{$this->getWebsiteId()}'";
        $adapter = $this->tableGateway->getAdapter();
        $result = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
        $result = $result->toArray();
        return $result;
    }
    protected function getIdCol()
    {
        return 'feature_id';
    }

    protected function getOrderCol()
    {
        return 'ordering';
    }
}