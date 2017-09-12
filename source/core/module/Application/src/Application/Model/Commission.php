<?php
/**
 * Created by PhpStorm.
 * User: viet
 * Date: 6/3/14
 * Time: 10:49 AM
 */

namespace Application\Model;

class Commission
{
    public $commission_id;
    public $agents_id;
    public $rate;
    public $start_date;
    public $end_date;

    public function exchangeArray($data)
    {
        $this->commission_id   = (!empty($data['commission_id'])) ? $data['commission_id'] : NULL;
        $this->agents_id = (!empty($data['agents_id'])) ? $data['agents_id'] : 0;
        $this->rate   = (!empty($data['rate'])) ? $data['rate'] : 0;
        $this->start_date   = (!empty($data['start_date'])) ? $data['start_date'] : date('Y-m-d');
        $this->end_date   = (!empty($data['end_date'])) ? $data['end_date'] : date('Y-m-d');
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

}