<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

/**
 * @OA\Tag(
 *     name="Produk",
 *     description="Endpoint Produk"
 * )
 */
class ProdukController extends ResourceController
{
    protected $modelName = 'App\Models\ProdukModel';
    protected $format = 'json';

    /**
     * @OA\Get(
     *     path="/api/produk",
     *     summary="Mendapatkan daftar semua produk",
     *     tags={"Produk"},
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mendapatkan daftar produk",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="data_produk", 
     *                 type="array", 
     *                 @OA\Items(
     *                     @OA\Property(property="id_produk", type="integer"),
     *                     @OA\Property(property="nama_produk", type="string"),
     *                     @OA\Property(property="harga", type="number"),
     *                     @OA\Property(property="stok", type="integer"),
     *                     @OA\Property(property="deskripsi", type="string")
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
            'data_produk' => $this->model->orderBy('id_produk', 'DESC')->findAll()
        ];

        return $this->respond($response, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/produk/{id}",
     *     summary="Mendapatkan detail produk berdasarkan ID",
     *     tags={"Produk"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID produk",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mendapatkan detail produk"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Produk tidak ditemukan"
     *     )
     * )
     */
    public function show($id = null)
    {
        $produk = $this->model->find($id);
        
        if (!$produk) {
            return $this->failNotFound('Produk tidak ditemukan');
        }
        
        return $this->respond([
            'message' => 'success',
            'data_produk' => $produk
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/produk",
     *     summary="Membuat produk baru",
     *     tags={"Produk"},
     *     @OA\RequestBody(
     *         description="Data produk baru",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nama_produk", type="string", example="Laptop Gaming"),
     *             @OA\Property(property="harga", type="number", example="12999000"),
     *             @OA\Property(property="stok", type="integer", example="50"),
     *             @OA\Property(property="deskripsi", type="string", example="Laptop gaming dengan spesifikasi tinggi")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Berhasil membuat produk baru"
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
            'nama_produk' => 'required',
            'harga' => 'required',
            'stok' => 'required',
            'deskripsi' => 'required'
        ]);

        if(!$rules) {
            $response = [
                'message' => $this->validator->getErrors()
            ];

            return $this->failValidationErrors($response);
        }

        $this->model->insert([
            'nama_produk' => esc($this->request->getVar('nama_produk')),
            'harga' => esc($this->request->getVar('harga')),            
            'stok' => esc($this->request->getVar('stok')),
            'deskripsi' => esc($this->request->getVar('deskripsi'))
        ]);

        $response = [
            'message'=> 'Data produk berhasil dibuat!'
        ];

        return $this->respondCreated($response);
    }

    /**
     * @OA\Put(
     *     path="/api/produk/{id}",
     *     summary="Memperbarui data produk",
     *     tags={"Produk"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID produk",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         description="Data produk yang diperbarui",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="nama_produk", type="string", example="Laptop Gaming Terbaru"),
     *             @OA\Property(property="harga", type="number", example="13999000"),
     *             @OA\Property(property="stok", type="integer", example="40"),
     *             @OA\Property(property="deskripsi", type="string", example="Laptop gaming dengan spesifikasi terkini")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil memperbarui data produk"
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
            'nama_produk' => 'required',
            'harga' => 'required',
            'stok' => 'required',
            'deskripsi' => 'required'
        ]);

        if(!$rules) {
            $response = [
                'message' => $this->validator->getErrors()
            ];

            return $this->failValidationErrors($response);
        }
        
        $this->model->update($id, [
            'nama_produk' => esc($this->request->getVar('nama_produk')),
            'harga' => esc($this->request->getVar('harga')),            
            'stok' => esc($this->request->getVar('stok')),
            'deskripsi' => esc($this->request->getVar('deskripsi'))
        ]);

        $response = [
            'message'=> 'Data produk berhasil diupdate!'
        ];

        return $this->respondUpdated($response);
    }

    /**
     * @OA\Delete(
     *     path="/api/produk/{id}",
     *     summary="Menghapus produk",
     *     tags={"Produk"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID produk",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil menghapus produk"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Produk tidak ditemukan"
     *     )
     * )
     */
    public function delete($id = null)
    {
        $produk = $this->model->find($id);
        
        if (!$produk) {
            return $this->failNotFound('Produk tidak ditemukan');
        }
        
        $this->model->delete($id);

        $response = [
            'message' => 'Data produk berhasil dihapus!'
        ];

        return $this->respondDeleted($response);
    }
}