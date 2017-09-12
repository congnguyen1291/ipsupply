<?php
namespace Application\View\Helper;
use Application\View\Helper\App;

class Branches extends App
{
    public function getAllBranches()
    {
        $branches = $this->getModelTable('BranchesTable')->getAllBranches();
        return $branches;
    }

    public function getBranches()
    {
        $branches = $this->getModelTable('BranchesTable')->getRows();
        return $branches;
    }

    public function getMainBranches()
    {
        $branches = $this->getModelTable('BranchesTable')->getMainBranches();
        return $branches;
    }

    public function getLatitude($bran)
    {
        if( !empty($bran) && !empty($bran['latitude']) ){
            return $bran['latitude'];
        }
        return 0;
    }

    public function getLongitude($bran)
    {
        if( !empty($bran) && !empty($bran['longitude']) ){
            return $bran['longitude'];
        }
        return 0;
    }
}
