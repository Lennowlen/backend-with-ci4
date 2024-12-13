<?php

namespace App\Models;

use CodeIgniter\Model;

class VWInvoiceModel extends Model
{
    protected $table            = 'vw_invoice';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    public function filterByDate($startDate, $endDate) {

        return $this->where('tanggal >=', $startDate)
                    ->where('tanggal <=', $endDate)
                    ->findAll();
    }
}
