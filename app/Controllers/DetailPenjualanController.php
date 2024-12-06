<?php

namespace App\Controllers;

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
    protected $modelName = 'App\Models\DetailPenjualanModel';
    protected $format = 'json';
    
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
            'data_detail_penjualan' => $this->model->findAll()
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
        $detailPenjualan = $this->model->where('id_penjualan', $id)->first();
        
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
     *             @OA\Property(property="id_penjualan", type="integer", example=1),
     *             @OA\Property(property="id_produk", type="integer", example=1),
     *             @OA\Property(property="quantity", type="integer", example=5),
     *             @OA\Property(property="subtotal", type="number", example=100000)
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
        $rules = $this->validate([
            'id_penjualan' => 'required',
            'id_produk' => 'required',
            'quantity' => 'required',
            'subtotal' => 'required',
        ]);

        if(!$rules) {
            $response = [
                'message' => $this->validator->getErrors()
            ];

            return $this->failValidationErrors($response);
        }

        $this->model->insert([
            'id_penjualan' => esc($this->request->getVar('id_penjualan')),
            'id_produk' => esc($this->request->getVar('id_produk')),
            'quantity' => esc($this->request->getVar('quantity')),
            'subtotal' => esc($this->request->getVar('subtotal')),
        ]);

        $response = [
            'message' => 'Data detail penjualan berhasil dibuat!'  
        ];

        return $this->respondCreated($response);
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

        if(!$rules) {
            $response = [
                'message' => $this->validator->getErrors()
            ];

            return $this->failValidationErrors($response);
        }

        $this->model->update($id, [
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
        $detailPenjualan = $this->model->find($id);
        
        if (!$detailPenjualan) {
            return $this->failNotFound('Detail penjualan tidak ditemukan');
        }
        
        $this->model->delete($id);

        $response = [
            'message' => 'Data detail penjualan berhasil dihapus!'
        ];

        return $this->respondDeleted($response);
    }
}