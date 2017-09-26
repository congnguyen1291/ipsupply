<?php
/**
 * Created by PhpStorm.
 * User: Kent
 * Date: 6/19/14
 * Time: 4:30 PM
 */

namespace Cms\Model;


use Zend\Db\Sql\Predicate\Expression;
use Zend\Db\Sql\Sql;
use Zend\Db\TableGateway\TableGateway;

use Cms\Model\AppTable;

class SettingTable extends AppTable{

    public function getAllConfig(){
        $sql = "SELECT settings.id,settings.name,settings.value,settings.description
                FROM settings WHERE website_id={$this->getWebsiteId()}";
        try{
            $adapter = $this->tableGateway->getAdapter();
            $results = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
            $config = array();
            foreach($results as $result){
                $config[$result->name] = $result->value;
            }
            return $config;
        }catch(\Exception $ex){
            throw new \Exception($ex);
        }
    }

    public function getConfig($name){
        $sql = "SELECT settings.id,settings.name,settings.value,settings.description
                FROM settings
                WHERE settings.name LIKE '{$name}' AND website_id={$this->getWebsiteId()} ";
        try{
            $adapter = $this->tableGateway->getAdapter();
            $results = $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
            return $results->current();
        }catch(\Exception $ex){
            throw new \Exception($ex);
        }
    }

    public function saveConfig($name, $data){
        $sql = "UPDATE `settings` SET `value`='{$data['value']}' WHERE `name` LIKE '{$name}' AND website_id={$this->getWebsiteId()}";
        try{
            $adapter = $this->tableGateway->getAdapter();
            $adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
            return TRUE;
        }catch(\Exception $ex){
            throw new \Exception($ex);
        }
    }

    public function getAllQuestion( $where = array() ){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('fqa');
        $select->join('users','fqa.users_id=users.users_id',array('full_name','user_name'),'left');
        $select->join('answer_questions', 'answer_questions.fqa_id=fqa.id',array('total_answer' => new Expression('count(answer_questions.id)')), 'left');
        $select->join('products','fqa.products_id=products.products_id', array('products_title','products_alias'));
        $select->where(array(
            'products.is_published' => 1,
            'products.is_delete' => 0,
            'products.website_id' => $this->getWebsiteId(),
        ));
        $select->group('fqa.id');
        $select->order(array(
            'date_create' => 'DESC'
        ));

        if( $this->hasPaging($where) ){
            $select->offset($this->getOffsetPaging($where['page'], $where['limit']));
            $select->limit($where['limit']);
        }

        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
        }catch (\Exception $ex){}
        return array();
    }

    public function countAllNewQuestion(){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select()->columns(array());
        $select->from('fqa');
        $select->join('users','fqa.users_id=users.users_id',array(),'left');
        $select->join('answer_questions', 'answer_questions.fqa_id=fqa.id',array('total_answer' => new Expression('count(answer_questions.id)')), 'left');
        $select->join('products','fqa.products_id=products.products_id', array());
        $select->order(array(
            'total_answer' => 'ASC',
        ));
        $select->where(array(
            'products.is_published' => 1,
            'products.is_delete' => 0,
            'products.website_id' => $this->getWebsiteId(),
        ));

        $select->having(array(
            'total_answer' => 0,
        ));
        $select->group('fqa.id');
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->count();
            return $results;
        }catch (\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    public function countAllQuestion( $where = array() ){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('fqa')->columns(array('total' => new Expression('count(fqa.id)')));
        $select->join('products','fqa.products_id=products.products_id', array());
        $select->where(array(
            'products.is_published' => 1,
            'products.is_delete' => 0,
            'products.website_id' => $this->getWebsiteId(),
        ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->current();
            return $results->total;
        }catch (\Exception $ex){}
        return 0;
    }
	
    public function getQuestion($id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('fqa');
        $select->join('users','fqa.users_id=users.users_id',array('full_name','user_name'),'left');
        $select->where(array(
            'id' => $id,
        ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = (array)$results->current();
            return $results;
        }catch (\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    public function getAnswers($id){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('answer_questions');
        $select->where(array(
            'fqa_id' => $id,
        ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
        }catch (\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    public function addAnswer($data = array()){
        $adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $insert = $sql->insert();
        $insert->columns(array(
            'fqa_id','users_id','answer_content','date_create','is_published',
        ));
        $insert->into('answer_questions');
        $insert->values(array(
            'fqa_id' => $data['fqa_id'],
            'users_id' => $data['users_id'],
            'answer_content' => $data['answer_content'],
            'date_create' => date('Y-m-d H:i:s'),
            'is_published' => $data['is_published'],
        ));
        try{
            $insertString = $sql->getSqlStringForSqlObject($insert);
            $adapter->query($insertString, $adapter::QUERY_MODE_EXECUTE);
        }catch (\Exception $ex){
            throw new \Exception($ex->getMessage());
        }

    }
	
	public function deleteQuestion($ids){
		$adapter = $this->tableGateway->getAdapter();
		$adapter->getDriver()->getConnection()->beginTransaction();
        try{
			$ids = implode(',', $ids);
			$deleteString = "DELETE FROM answer_questions WHERE fqa_id IN ({$ids})";
            $adapter->query($deleteString, $adapter::QUERY_MODE_EXECUTE);
			$deleteString = "DELETE FROM fqa WHERE id IN ({$ids})";
            $adapter->query($deleteString, $adapter::QUERY_MODE_EXECUTE);
			$adapter->getDriver()->getConnection()->commit();
        }catch (\Exception $ex){
			$adapter->getDriver()->getConnection()->rollback();
            throw new \Exception($ex->getMessage());
        }
	}
	
	public function getAnswer($id){
		$adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('answer_questions');
        $select->where(array(
            'id' => $id,
        ));
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = (array)$results->current();
            return $results;
        }catch (\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
	}
	
	public function deleteAnswer($id){
		try{
			$adapter = $this->tableGateway->getAdapter();
			$deleteString = "DELETE FROM answer_questions WHERE id={$id}";
			$adapter->query($deleteString, $adapter::QUERY_MODE_EXECUTE);
		}catch(\Exception $ex){
			throw new \Exception($ex->getMessage());
		}
	}
	
	public function softUpdateData($ids, $data){
		$adapter = $this->tableGateway->getAdapter();
		$sql = new Sql($adapter);
		$update = $sql->update('fqa');
		$update->set($data);
		$update->where(array(
			'id' => $ids,
		));
		try{
			$updateString = $sql->getSqlStringForSqlObject($update);
			$adapter->query($updateString, $adapter::QUERY_MODE_EXECUTE);
		}catch(\Exception $ex){
			throw new \Exception($ex->getMessage());
		}
	}
	
	public function filterQuestion($params = array()){
		$adapter = $this->tableGateway->getAdapter();
        $sql = new Sql($adapter);
        $select = $sql->select();
        $select->from('fqa');
        $select->join('users','fqa.users_id=users.users_id',array('full_name','user_name'),'left');
        $select->join('answer_questions', 'answer_questions.fqa_id=fqa.id',array('total_answer' => new Expression('count(answer_questions.id)')), 'left');
        $select->join('products','fqa.products_id=products.products_id', array('products_title','products_alias'));
        $select->where(array(
            'products.website_id' => $this->getWebsiteId(),
        ));
		if($params['products_id']){
			$where['fqa.products_id'] = $params['products_id'];
			$select->where(array(
				'fqa.products_id' => $params['products_id'],
			));
		}
		if($params['date_create']){
			$date_create = $params['date_create'];
			$date_create = explode(' - ', $date_create);
			if(count($date_create) == 2){
				$date_start = $date_create[0];
				$date_end = $date_create[1];
				$date_start = explode('/', $date_start);
				if(count($date_start) == 3){
					$date_start = $date_start[2].'-'.$date_start[0].'-'.$date_start[1].' 00:00:00';
					$date_end = explode('/', $date_end);
					if(count($date_end) == 3){
						$date_end = $date_end[2].'-'.$date_end[0].'-'.$date_end[1].' 00:00:00';
						$select->where->between('fqa.date_create', $date_start, $date_end);
					}
				}
			}
		}
		if($params['answer'] != -1){
			if($params['answer']){
				$select->having("total_answer > 0");
			}else{
				$select->having("total_answer = 0");
			}
		}
		
		if(isset($params['is_published'])){
			$select->where(array(
				'fqa.is_published' => 1
			));
		}
		
        $select->order(array(
            'total_answer' => 'ASC',
        ));
        $select->group('fqa.id');
		$select->limit(20);
        try{
            $selectString = $sql->getSqlStringForSqlObject($select);
            $results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
        }catch (\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
	}
	
	public function getAllSocialNetworks(){
		try{
			$adapter = $this->tableGateway->getAdapter();
			$sql = new Sql($adapter);
			$select = $sql->select();
			$select->from('socials_networks');
			$selectString = $sql->getSqlStringForSqlObject($select);
			$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->toArray();
            return $results;
		}catch(\Exception $ex){
			throw new \Exception($ex->getMessage());
		}
	}
	
	public function getNetworkById($id){
		try{
			$adapter = $this->tableGateway->getAdapter();
			$sql = new Sql($adapter);
			$select = $sql->select();
			$select->from('socials_networks');
			$select->where(array(
                'id' => $id,
				'website_id' => $this->getWebsiteId(),
			));
			$selectString = $sql->getSqlStringForSqlObject($select);
			$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
            $results = $results->current();
            return $results;
		}catch(\Exception $ex){
			throw new \Exception($ex->getMessage());
		}
	}
	
	public function addNetwork($data){
		$adapter = $this->tableGateway->getAdapter();
		$adapter->getDriver()->getConnection()->beginTransaction();
		try{
			$sql = new Sql($adapter);
			$insert = $sql->insert();
			$insert->into('socials_networks');
			$insert->columns(array('website_id','title','link','icon','is_published','date_create'));
			if(!$data['file_icon']['name']){
				throw new \Exception("Please choose file");
			}
			$file = $data['file_icon'];
			$upload_dir = PATH_BASE_ROOT.DS.'custom'.DS.'domain_1'.DS.'social_icons';
			if(!is_dir($upload_dir)){
				@mkdir($upload_dir, 0777);
			}
			$filename = $file['tmp_name'];
			$old_name = $file['name'];
			$tmp = @explode('.', $old_name);
			$ext = end($tmp);
			$name = 'social-'.$this->toAlias($data['title']).'-'.time().'.'.$ext;
			$data['icon'] = "/custom/domain_1/social_icons/".$name;
			$insert->values(array(
                'website_id' => $this->getWebsiteId(),
				'title' => $data['title'],
				'link' => $data['link'],
				'icon' => $data['icon'],
				'is_published' => $data['is_published'],
				'date_create' => date('Y-m-d H:i:s'),
			));
			$insertString = $sql->getSqlStringForSqlObject($insert);
			$adapter->query($insertString, $adapter::QUERY_MODE_EXECUTE);
			@move_uploaded_file($filename, $upload_dir.DS.$name);
			$adapter->getDriver()->getConnection()->commit();
		}catch(\Exception $ex){
			$adapter->getDriver()->getConnection()->rollback();
			throw new \Exception($ex->getMessage());
		}
	}
	
	public function editNetwork($data){
		$adapter = $this->tableGateway->getAdapter();
		$adapter->getDriver()->getConnection()->beginTransaction();
		try{
			$sql = new Sql($adapter);
			$update = $sql->update('socials_networks');
			//$insert->columns(array('title','link','icon','is_published','date_create'));
			if(!$data['file_icon']['name'] && !$data['icon']){
				throw new \Exception("Please choose file");
			}
			if($data['file_icon']['name']){
				$file = $data['file_icon'];
				$upload_dir = PATH_BASE_ROOT.DS.'custom'.DS.'domain_1'.DS.'social_icons';
				if(!is_dir($upload_dir)){
					@mkdir($upload_dir, 0777);
				}
				$filename = $file['tmp_name'];
				$old_name = $file['name'];
				$tmp = @explode('.', $old_name);
				$ext = end($tmp);
				$name = 'social-'.$this->toAlias($data['title']).'-'.time().'.'.$ext;
				$data['icon'] = "/custom/domain_1/social_icons/".$name;
			}
			$update->set(array(
				'title' => $data['title'],
				'link' => $data['link'],
				'icon' => $data['icon'],
				'is_published' => $data['is_published'],
				'date_create' => date('Y-m-d H:i:s'),
			));
			$updateString = $sql->getSqlStringForSqlObject($update);
			$adapter->query($updateString, $adapter::QUERY_MODE_EXECUTE);
			if($data['file_icon']['name']){
				@move_uploaded_file($filename, $upload_dir.DS.$name);
			}
			$adapter->getDriver()->getConnection()->commit();
		}catch(\Exception $ex){
			$adapter->getDriver()->getConnection()->rollback();
			throw new \Exception($ex->getMessage());
		}
	}
	
	public function delNetwork($ids){
		if(!count($ids)){
			return;
		}
		$adapter = $this->tableGateway->getAdapter();
		$adapter->getDriver()->getConnection()->beginTransaction();
		try{
			$sql = new Sql($adapter);
			$select = $sql->select();
			$select->from('socials_networks');
			$select->where(array(
				'id' => $ids,
			));
			$selectString = $sql->getSqlStringForSqlObject($select);
			$results = $adapter->query($selectString, $adapter::QUERY_MODE_EXECUTE);
			$results = $results->toArray();
			if(count($results)){
				foreach($results as $row){
				$filepath = PATH_BASE_ROOT.$row['icon'];
					if(is_file($filepath)){
						@unlink($filepath);
					}
				}
			}
			$delete = $sql->delete('socials_networks');
			$delete->where(array(
				'id' => $ids,
			));
			$deleteString = $sql->getSqlStringForSqlObject($delete);
			$adapter->query($deleteString, $adapter::QUERY_MODE_EXECUTE);
			$adapter->getDriver()->getConnection()->commit();
		}catch(\Exception $ex){
			die($ex->getMessage());
			$adapter->getDriver()->getConnection()->rollback();
		}
	}
	
	public function saveSetting($data){
		try{
			if(count($data)){
				$adapter = $this->tableGateway->getAdapter();
				$value = array();
				foreach($data as $key => $v){
					$value[] = "({$this->getWebsiteId()}, '{$key}', '{$v}')";
				}
				$value = implode(',',$value);
				$sql = "INSERT INTO settings (website_id,name,value) VALUES {$value} ON DUPLICATE KEY UPDATE value=VALUES(value);";
				$adapter->query($sql, $adapter::QUERY_MODE_EXECUTE);
			}
		}catch(\Exception $ex){
			throw new \Exception($ex->getMessage());
		}
	}

    public function getMenuById($id){
        $menu_dir = './module/menus';
        if(!is_dir($menu_dir)){
            @mkdir($menu_dir,0777);
        }
        $menu_dir .= '/menus.data';
        $menus = array();
        if(is_file($menu_dir)){
            $menus_string = file_get_contents($menu_dir);
            $menus = json_decode($menus_string, TRUE);
        }
        if(isset($menus[$id])){
            return $menus[$id];
        }
        throw new \Exception('Row not found');
    }

    public function addMenu($data){
        try{
            if(!$data['file_icon']['name']){
                $data['icon'] = '';
                //throw new \Exception("Please choose file");
            }else{
                $file = $data['file_icon'];
                $upload_dir = PATH_BASE_ROOT.DS.'custom'.DS.'domain_1'.DS.'menu_icons';
                if(!is_dir($upload_dir)){
                    @mkdir($upload_dir, 0777);
                }
                $filename = $file['tmp_name'];
                $old_name = $file['name'];
                $tmp = @explode('.', $old_name);
                $ext = end($tmp);
                if(in_array($ext, array('png','PNG','jpg','gif','jpeg'))){
                    $name = 'menu-'.$this->toAlias($data['title']).'-'.time().'.'.$ext;
                    $data['icon'] = "/custom/domain_1/menu_icons/".$name;
                    @move_uploaded_file($filename, $upload_dir.DS.$name);
                }else{
                    $data['icon'] = '';
                }
            }
            $id = time();
            $data['id'] = $id;
            unset($data['file_icon']);
            unset($data['submit']);
            unset($data['is_remove_icon']);
            $menu_dir = './module/menus';
            if(!is_dir($menu_dir)){
                @mkdir($menu_dir,0777);
            }
            $menu_dir .= '/menus.data';
            $menus = array();
            if(is_file($menu_dir)){
                $menus_string = file_get_contents($menu_dir);
                $menus = json_decode($menus_string, TRUE);
            }
            $menus[$id] = $data;
            $saveData = json_encode($menus);
            @file_put_contents($menu_dir, $saveData);
        }catch(\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
    }

    public function editMenu($data){
        try{
            $id = $data['id'];
            //$insert->columns(array('title','link','icon','is_published','date_create'));
            if(!$data['file_icon']['name']){
                if($data['is_remove_icon']){
                    $data['icon'] = '';
                }
            }else{
                $file = $data['file_icon'];
                $upload_dir = PATH_BASE_ROOT.DS.'custom'.DS.'domain_1'.DS.'menu_icons';
                if(!is_dir($upload_dir)){
                    @mkdir($upload_dir, 0777);
                }
                $filename = $file['tmp_name'];
                $old_name = $file['name'];
                $tmp = @explode('.', $old_name);
                $ext = end($tmp);
                if(in_array($ext, array('png','PNG','jpg','gif','jpeg'))){
                    $name = 'social-'.$this->toAlias($data['title']).'-'.time().'.'.$ext;
                    $data['icon'] = "/custom/domain_1/menu_icons/".$name;
                    @move_uploaded_file($filename, $upload_dir.DS.$name);
                }else{
                    $data['icon'] = '';
                }
            }
            unset($data['file_icon']);
            unset($data['submit']);
            unset($data['is_remove_icon']);
            $menu_dir = './module/menus';
            if(!is_dir($menu_dir)){
                @mkdir($menu_dir,0777);
            }
            $menu_dir .= '/menus.data';
            $menus = array();
            if(is_file($menu_dir)){
                $menus_string = file_get_contents($menu_dir);
                $menus = json_decode($menus_string, TRUE);
            }
            $menus[$id] = $data;
            $saveData = json_encode($menus);
            @file_put_contents($menu_dir, $saveData);
        }catch(\Exception $ex){
            throw new \Exception($ex->getMessage());
        }
    }

} 