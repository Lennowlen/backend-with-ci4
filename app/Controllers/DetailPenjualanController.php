<?php

namespace App\Controllers;

use App\Models\DetailPenjualanModel;
use App\Models\ProdukModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

/**
 * @OA\Tag(
 *     name="Detail Penjualan",
 *     description="Endpoint Detail Penjualan"
 * )
 */
class DetailPenjualanController extends ResourceController
{
    protected $modelProduk;
    protected $modelDetailPenjualan;

    public function __construct()
    {
        // protected $modelName = 'App\Models\DetailPenjualanModel';
        // protected $modelName2 = 'App\Models\ProdukModel';
        // protected $format = 'json';

        $this->modelProduk = new ProdukModel();
        $this->modelDetailPenjualan = new DetailPenjualanModel();
    }

    /**
     * @OA\Get(
     *     path="/api/detail-penjualan",
     *     summary="Mendapatkan daftar semua detail penjualan",
     *     tags={"Detail Penjualan"},
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mendapatkan daftar detail penjualan",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="data_detail_penjualan", 
     *                 type="array", 
     *                 @OA\Items(
     *                     @OA\Property(property="id_detail_penjualan", type="integer"),
     *                     @OA\Property(property="id_penjualan", type="integer"),
     *                     @OA\Property(property="id_produk", type="integer"),
     *                     @OA\Property(property="quantity", type="integer"),
     *                     @OA\Property(property="subtotal", type="number")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $response = [
            'message' => 'success',
            'data_detail_penjualan' => $this->modelDetailPenjualan->findAll()
        ];

        return $this->respond($response, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/detail-penjualan/{id}",
     *     summary="Mendapatkan detail penjualan berdasarkan ID",
     *     tags={"Detail Penjualan"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID penjualan",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mendapatkan detail penjualan"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Detail penjualan tidak ditemukan"
     *     )
     * )
     */
    public function show($id = null)
    {
        // $detailPenjualan = $this->model->find($id);
        $detailPenjualan = $this->modelDetailPenjualan->where('id_penjualan', $id)->findAll();

        if (!$detailPenjualan) {
            return $this->failNotFound('Detail penjualan tidak ditemukan');
        }

        return $this->respond([
            'message' => 'success',
            'data_detail_penjualan' => $detailPenjualan
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/detail-penjualan/create",
     *     summary="Membuat detail penjualan baru",
     *     tags={"Detail Penjualan"},
     *     @OA\RequestBody(
     *         description="Data detail penjualan baru",
     *         required=true,
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                  type="object",
     *                  @OA\Property(property="id_penjualan", type="integer", example=1),
     *                  @OA\Property(property="id_produk", type="integer", example=1),
     *                  @OA\Property(property="quantity", type="integer", example=5)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Berhasil membuat detail penjualan baru"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validasi gagal"
     *     )
     * )
     */
    public function create()
    {
        // $rules = $this->validate([
        //     'id_penjualan' => 'required',
        //     'id_produk' => 'required',
        //     'quantity' => 'required',
        //     'subtotal' => 'required'
        // ]);

        // if(!$rules) {
        //     $response = [
        //         'message' => $this->validator->getErrors()
        //     ];

        //     return $this->failValidationErrors($response);
        // }

        // $this->modelDetailPenjualan->insertBatch([
        //     'id_penjualan' => esc($this->request->getVar('id_penjualan')),
        //     'id_produk' => esc($this->request->getVar('id_produk')),
        //     'quantity' => esc($this->request->getVar('quantity')),
        //     'subtotal' => esc($this->request->getVar('subtotal')),
        //     // 'subtotal' => $this->modelProduk->where('id_produk', esc($this->request->getVar('id_produk')))->first()->harga * esc($this->request->getVar('quantity')),
        // ]);

        try {

            $data = $this->request->getJSON(true);

            if (!$data || !is_array($data)) {
                return $this->failValidationErrors('Data harus berupa array.');
            }

            $detailData = [];
            foreach ($data as $item) {
                // Validasi input
                if (!isset($item['id_produk'], $item['quantity'], $item['id_penjualan'])) {
                    return $this->failValidationErrors('Data harus memiliki id_produk, quantity, dan id_penjualan.');
                }

                $produk = $this->modelProduk->find($item['id_produk']);
                if (!$produk) {
                    return $this->failNotFound("Produk dengan ID {$item['id_produk']} tidak ditemukan.");
                }

                // Cek stok produk
                if ($produk['stok'] < $item['quantity']) {
                    return $this->failValidationErrors("Stok untuk produk ID {$item['id_produk']} tidak mencukupi. Tersedia: {$produk['stok']}");
                }

                // Pengurangan stok produk
                $produk['stok'] -= $item['quantity'];
                $this->modelProduk->update($item['id_produk'], ['stok' => $produk['stok']]);

                // subtotal
                $subtotal = $produk['harga'] * $item['quantity'];

                // insertBatch
                $detailData[] = [
                    'id_penjualan' => $item['id_penjualan'],
                    'id_produk' => $item['id_produk'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal,
                ];
            }

            // Insert data ke database
            $this->modelDetailPenjualan->insertBatch($detailData);

            return $this->respondCreated(['message' => 'Data detail penjualan berhasil dibuat!']);
        } catch (\Exception $e) {

            log_message('error', 'Exception: ' . $e->getMessage() . 'on line ' . $e->getLine() . 'on file ' . $e->getFile());

            return $this->respond([
                'status' => 500,
                'error' => 'Internal Server Error',
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/detail-penjualan/{id}",
     *     summary="Memperbarui data detail penjualan",
     *     tags={"Detail Penjualan"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID detail penjualan",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         description="Data detail penjualan yang diperbarui",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="id_penjualan", type="integer", example=1),
     *             @OA\Property(property="id_produk", type="integer", example=2),
     *             @OA\Property(property="quantity", type="integer", example=7),
     *             @OA\Property(property="subtotal", type="number", example=150000)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil memperbarui data detail penjualan"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validasi gagal"
     *     )
     * )
     */
    public function update($id = null)
    {
        $rules = $this->validate([
            'id_penjualan' => 'required',
            'id_produk' => 'required',
            'quantity' => 'required',
            'subtotal' => 'required',
        ]);

        if (!$rules) {
            $response = [
                'message' => $this->validator->getErrors()
            ];

            return $this->failValidationErrors($response);
        }

        $this->modelDetailPenjualan->update($id, [
            'id_penjualan' => esc($this->request->getVar('id_penjualan')),
            'id_produk' => esc($this->request->getVar('id_produk')),
            'quantity' => esc($this->request->getVar('quantity')),
            'subtotal' => esc($this->request->getVar('subtotal')),
        ]);

        $response = [
            'message' => 'Data detail penjualan berhasil diupdate!'
        ];

        return $this->respondUpdated($response);
    }

    /**
     * @OA\Delete(
     *     path="/api/detail-penjualan/{id}",
     *     summary="Menghapus detail penjualan",
     *     tags={"Detail Penjualan"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID detail penjualan",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil menghapus detail penjualan"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Detail penjualan tidak ditemukan"
     *     )
     * )
     */
    public function delete($id = null)
    {
        $detailPenjualan = $this->modelDetailPenjualan->find($id);

        if (!$detailPenjualan) {
            return $this->failNotFound('Detail penjualan tidak ditemukan');
        }

        $this->modelDetailPenjualan->delete($id);

        $response = [
            'message' => 'Data detail penjualan berhasil dihapus!'
        ];

        return $this->respondDeleted($response);
    }
}
