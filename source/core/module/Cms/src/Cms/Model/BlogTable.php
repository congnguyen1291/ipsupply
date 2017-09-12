<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 5/25/14
 * Time: 2:51 PM
 */
namespace Cms\Model;

use Zend\Db\TableGateway\TableGateway;
use Cms\Model\AppTable;

class BlogTable extends AppTable
{
    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getBlog($id)
    {
        $id  = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveBlog(Blog $blog)
    {
        $data = array(
            'title'         => $blog->title,
            'content'  => $blog->content,
            'catid'  => $blog->catid,
            'created'      => $blog->created,
            'updated'      => $blog->updated,
            'deleted'      => $blog->deleted,
            'actived'      => $blog->actived,
        );

        $id = (int) $blog->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
        } else {
            if ($this->getBlog($id)) {
                $this->tableGateway->update($data, array('id' => $id));
            } else {
                throw new \Exception('Category id does not exist');
            }
        }
    }

    public function deleteBlog($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
}