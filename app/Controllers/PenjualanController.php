<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

/**
 * @OA\Tag(
 *     name="Penjualan",
 *     description="Endpoint Penjualan"
 * )
 */
class PenjualanController extends ResourceController
{
    protected $modelName = 'App\Models\PenjualanModel';
    protected $format = 'json';

    /**
     * @OA\Get(
     *     path="/api/penjualan",
     *     summary="Mendapatkan daftar semua penjualan",
     *     tags={"Penjualan"},
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mendapatkan daftar penjualan",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="data_penjualan", 
     *                 type="array", 
     *                 @OA\Items(
     *                     @OA\Property(property="id_penjualan", type="integer"),
     *                     @OA\Property(property="tanggal", type="string", format="date"),
     *                     @OA\Property(property="id_pelanggan", type="integer")
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
            'data_penjualan' => $this->model->orderBy('id_penjualan', 'DESC')->findAll()
        ];

        return $this->respond($response, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/penjualan/{id}",
     *     summary="Mendapatkan detail penjualan berdasarkan ID",
     *     tags={"Penjualan"},
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
     *         description="Penjualan tidak ditemukan"
     *     )
     * )
     */
    public function show($id = null)
    {
        $penjualan = $this->model->find($id);
        
        if (!$penjualan) {
            return $this->failNotFound('Penjualan tidak ditemukan');
        }
        
        return $this->respond([
            'message' => 'success',
            'data_penjualan' => $penjualan
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/penjualan",
     *     summary="Membuat penjualan baru",
     *     tags={"Penjualan"},
     *     @OA\RequestBody(
     *         description="Data penjualan baru",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="tanggal", type="string", format="date", example="2023-12-04"),
     *             @OA\Property(property="id_pelanggan", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Berhasil membuat penjualan baru"
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
            'tanggal' => 'required',
            'id_pelanggan' => 'required',
        ]);

        if (!$rules) {
            $response = [
                'message' => $this->validator->getErrors()
            ];

            return $this->failValidationErrors($response);
        }

        $this->model->insert([
            'tanggal' => esc($this->request->getVar('tanggal')),
            'id_pelanggan' => esc($this->request->getVar('id_pelanggan'))
        ]);

        $response = [
            'message' => 'Data penjualan berhasil dibuat!'
        ];

        return $this->respondCreated($response);
    }

    /**
     * @OA\Put(
     *     path="/api/penjualan/{id}",
     *     summary="Memperbarui data penjualan",
     *     tags={"Penjualan"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID penjualan",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         description="Data penjualan yang diperbarui",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="tanggal", type="string", format="date", example="2023-12-05"),
     *             @OA\Property(property="id_pelanggan", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil memperbarui data penjualan"
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
            'tanggal' => 'required',
            'id_pelanggan' => 'required',
        ]);

        if (!$rules) {
            $response = [
                'message' => $this->validator->getErrors()
            ];

            return $this->failValidationErrors($response);
        }

        $this->model->update($id, [
            'tanggal' => esc($this->request->getVar('tanggal')),
            'id_pelanggan' => esc($this->request->getVar('id_pelanggan'))
        ]);

        $response = [
            'message' => 'Data penjualan berhasil diupdate!'
        ];

        return $this->respondUpdated($response);
    }

    /**
     * @OA\Delete(
     *     path="/api/penjualan/{id}",
     *     summary="Menghapus penjualan",
     *     tags={"Penjualan"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID penjualan",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil menghapus penjualan"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Penjualan tidak ditemukan"
     *     )
     * )
     */
    public function delete($id = null)
    {
        $penjualan = $this->model->find($id);
        
        if (!$penjualan) {
            return $this->failNotFound('Penjualan tidak ditemukan');
        }
        
        $this->model->delete($id);

        $response = [
            'message' => 'Data penjualan berhasil dihapus!'
        ];

        return $this->respondDeleted($response);
    }
}