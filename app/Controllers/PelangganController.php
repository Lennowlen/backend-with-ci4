<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;


 /**
 * @OA\Info(
 *     title="Dokumentasi API Penjualan",
 *     version="1.0.0",
 *     description="API untuk manajemen data penjualan",
 *     @OA\Contact(
 *         email="gibran.devv@email.com",
 *         name="Muhammad Gibran"
 *     )
 * )
 * @OA\Tag(
 *     name="Pelanggan",
 *     description="Endpoint Pelanggan"
 * )
 */
class PelangganController extends ResourceController
{
    protected $modelName = 'App\Models\PelangganModel';
    protected $format = 'json';

    /**
     * @OA\Get(
     *     path="/api/pelanggan",
     *     summary="Mendapatkan daftar semua pelanggan",
     *     tags={"Pelanggan"},
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mendapatkan daftar pelanggan",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="data_pelanggan", 
     *                 type="array", 
     *                 @OA\Items(
     *                     @OA\Property(property="id_pelanggan", type="integer"),
     *                     @OA\Property(property="nama_pelanggan", type="string"),
     *                     @OA\Property(property="alamat", type="string"),
     *                     @OA\Property(property="telepon", type="string")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        $data = [
            'message' => 'success',
            'data_pelanggan' => $this->model->orderBy('id_pelanggan', 'DESC')->findAll()
        ];
        
        return $this->respond($data, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/pelanggan/{id}",
     *     summary="Mendapatkan detail pelanggan berdasarkan ID",
     *     tags={"Pelanggan"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID pelanggan",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mendapatkan detail pelanggan"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pelanggan tidak ditemukan"
     *     )
     * )
     */
    public function show($id = null)
    {
        $pelanggan = $this->model->find($id);
        
        if (!$pelanggan) {
            return $this->failNotFound('Pelanggan tidak ditemukan');
        }
        
        return $this->respond([
            'message' => 'success',
            'data_pelanggan' => $pelanggan
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/pelanggan",
     *     summary="Membuat pelanggan baru",
     *     tags={"Pelanggan"},
     *     @OA\RequestBody(
     *         description="Data pelanggan baru",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nama_pelanggan", type="string", example="John Doe"),
     *             @OA\Property(property="alamat", type="string", example="Jl. Contoh No.123"),
     *             @OA\Property(property="telepon", type="string", example="081234567890")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Berhasil membuat pelanggan baru"
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
            'nama_pelanggan' => 'required',
            'alamat' => 'required',
            'telepon' => 'required'
        ]);

        if(!$rules) {
            $response = [
                'message' => $this->validator->getErrors()
            ];

            return $this->failValidationErrors($response);
        }

        $this->model->insert([
            'nama_pelanggan' => esc($this->request->getVar('nama_pelanggan')),
            'alamat' => esc($this->request->getVar('alamat')),
            'telepon' => esc($this->request->getVar('telepon'))
        ]);

        $response = [
            'message'=> 'Data pelanggan berhasil dibuat!'
        ];

        return $this->respondCreated($response);
    }

    /**
     * @OA\Put(
     *     path="/api/pelanggan/{id}",
     *     summary="Memperbarui data pelanggan",
     *     tags={"Pelanggan"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID pelanggan",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         description="Data pelanggan yang diperbarui",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nama_pelanggan", type="string", example="John Doe Updated"),
     *             @OA\Property(property="alamat", type="string", example="Jl. Contoh Diperbarui No.456"),
     *             @OA\Property(property="telepon", type="string", example="087654321098")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil memperbarui data pelanggan"
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
            'nama_pelanggan' => 'required',
            'alamat' => 'required',
            'telepon' => 'required'
        ]);

        if(!$rules) {
            $response = [
                'message' => $this->validator->getErrors()
            ];

            return $this->failValidationErrors($response);
        }

        $this->model->update($id, [
            'nama_pelanggan' => esc($this->request->getVar('nama_pelanggan')),
            'alamat' => esc($this->request->getVar('alamat')),
            'telepon' => esc($this->request->getVar('telepon')),
        ]);

        $response = [
            'message' => 'Data berhasil diupdate!'
        ];

        return $this->respondUpdated($response);
    }

    /**
     * @OA\Delete(
     *     path="/api/pelanggan/{id}",
     *     summary="Menghapus pelanggan",
     *     tags={"Pelanggan"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID pelanggan",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil menghapus pelanggan"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Pelanggan tidak ditemukan"
     *     )
     * )
     */
    public function delete($id = null)
    {
        $pelanggan = $this->model->find($id);
        
        if (!$pelanggan) {
            return $this->failNotFound('Pelanggan tidak ditemukan');
        }
        
        $this->model->delete($id);

        $response = [
            'message' => 'Data berhasil dihapus!'
        ];

        return $this->respondDeleted($response);
    }
}