<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class DetailPenjualanController extends ResourceController
{

    protected $modelName = 'App\Models\DetailPenjualanModel';
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
            'data_detail_penjualan' => $this->model->findAll()
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
            'message' => 'Data detail penjualan berhasil dihapus!'
        ];

        return $this->respondDeleted($response);
    }
}
