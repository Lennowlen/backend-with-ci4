<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class ProdukController extends ResourceController
{
    protected $modelName = 'App\Models\ProdukModel';
    protected $format = 'json';
    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function index()
    {
        //
        $response = [
            'message' => 'success',
            'data_produk' => $this->model->orderBy('id_produk', 'DESC')->findAll()
        ];

        return $this->respond($response, 200);
    }

    /**
     * Return the properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function show($id = null)
    {
        //
    }

    /**
     * Return a new resource object, with default properties.
     *
     * @return ResponseInterface
     */
    public function new()
    {
        //
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        //
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
     * Return the editable properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function edit($id = null)
    {
        //
    }

    /**
     * Add or update a model resource, from "posted" properties.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function update($id = null)
    {
        //
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
     * Delete the designated resource object from the model.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function delete($id = null)
    {
        //
        $this->model->delete($id);

        $response = [
            'message' => 'Data produk berhasil dihapus!'
        ];

        return $this->respondDeleted($response);
    }
}
