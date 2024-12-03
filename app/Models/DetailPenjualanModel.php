<?php

namespace App\Models;

use CodeIgniter\Model;

class DetailPenjualanModel extends Model
{
    protected $table            = 'detail_penjualan';
    protected $allowedFields    = ['id_penjualan', 'id_produk', 'quantity', 'subtotal'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
