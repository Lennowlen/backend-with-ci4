<?php

namespace App\Models;

use CodeIgniter\Model;

class VWInvoiceModel extends Model
{
    protected $table            = 'vw_invoice';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    public function filterByDate($startDate, $endDate)
    {

        return $this->where('tanggal >=', $startDate)
            ->where('tanggal <=', $endDate)
            ->findAll();
    }

    public function get_All()
    {
        return $this->db->table('penjualan')
            ->select("
            ROW_NUMBER() OVER (ORDER BY penjualan.id_pelanggan, detail_penjualan.id_penjualan) AS id,
            penjualan.id_penjualan,
            penjualan.tanggal,
            penjualan.id_pelanggan,
            pelanggan.nama_pelanggan,
            pelanggan.alamat,
            pelanggan.telepon,
            produk.id_produk,
            produk.nama_produk,
            produk.harga,
            detail_penjualan.quantity,
            detail_penjualan.subtotal
            ")
            ->join('pelanggan', 'penjualan.id_pelanggan = pelanggan.id_pelanggan', 'inner')
            ->join('detail_penjualan', 'penjualan.id_penjualan = detail_penjualan.id_penjualan', 'inner')
            ->join('produk', 'detail_penjualan.id_produk = produk.id_produk')
            ->get()
            ->getResultArray();
    }

    public function get_All_by_Date($startDate, $endDate)
    {

        return $this->db->table('penjualan')
            ->select("
            ROW_NUMBER() OVER (ORDER BY penjualan.id_pelanggan, detail_penjualan.id_penjualan) AS id,
            penjualan.id_penjualan,
            penjualan.tanggal,
            penjualan.id_pelanggan,
            pelanggan.nama_pelanggan,
            pelanggan.alamat,
            pelanggan.telepon,
            produk.id_produk,
            produk.nama_produk,
            produk.harga,
            detail_penjualan.quantity,
            detail_penjualan.subtotal
            ")
            ->join('pelanggan', 'penjualan.id_pelanggan = pelanggan.id_pelanggan', 'inner')
            ->join('detail_penjualan', 'penjualan.id_penjualan = detail_penjualan.id_penjualan', 'inner')
            ->join('produk', 'detail_penjualan.id_produk = produk.id_produk')
            ->where('tanggal >=', $startDate)
            ->where('tanggal <=', $endDate)
            ->get()
            ->getResultArray();
    }

    public function filterById($id)
    {
        return $this->db->table('penjualan')
            ->select("
        ROW_NUMBER() OVER (ORDER BY penjualan.id_pelanggan, detail_penjualan.id_penjualan) AS id,
        pelanggan.nama_pelanggan,
        penjualan.tanggal,
        produk.nama_produk,
        produk.harga,
        detail_penjualan.quantity,
        detail_penjualan.subtotal
        ")
            ->join('pelanggan', 'penjualan.id_pelanggan = pelanggan.id_pelanggan', 'inner')
            ->join('detail_penjualan', 'penjualan.id_penjualan = detail_penjualan.id_penjualan', 'inner')
            ->join('produk', 'detail_penjualan.id_produk = produk.id_produk')
            ->where('pelanggan.id_pelanggan', $id)
            ->get()
            ->getResultArray();
    }

    public function grandtotals()
    {
        return $this->db->table('penjualan')
            ->select("
                pelanggan.nama_pelanggan,
                penjualan.tanggal,
                SUM(detail_penjualan.subtotal) AS grandtotal
            ")
            ->join('pelanggan', 'pelanggan.id_pelanggan = penjualan.id_pelanggan', 'inner')
            ->join('detail_penjualan', 'penjualan.id_penjualan = detail_penjualan.id_penjualan', 'inner')
            ->join('produk', 'detail_penjualan.id_produk = produk.id_produk', 'inner')
            ->groupBy('pelanggan.id_pelanggan, penjualan.tanggal')
            ->get()
            ->getResultArray();
    }


    public function grandtotal($id)
    {
        return $this->db->table('penjualan')
            ->select("
        SUM(detail_penjualan.subtotal) AS total
        ")
            ->join('pelanggan', 'penjualan.id_pelanggan = pelanggan.id_pelanggan', 'inner')
            ->join('detail_penjualan', 'penjualan.id_penjualan = detail_penjualan.id_penjualan', 'inner')
            ->join('produk', 'detail_penjualan.id_produk = produk.id_produk')
            ->where('pelanggan.id_pelanggan', $id)
            ->get()
            ->getResultArray();
    }
}
