<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class PenjualanController extends ResourceController
{
    protected $modelName = 'App\Models\PenjualanModel';
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
            'data_penjualan' => $this->model->orderBy('id_penjualan', 'DESC')->findAll()
            // 'data_pelanggan' => $this->model->orderBy('id_penjualan', 'DESC')->findAll()
        ];

        return $this->respond($response, 200);
    }

    public function home()
    {
        echo "hallo";
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
            'message' => 'Data penjualan berhasil dihapus!'
        ];

        return $this->respondDeleted($response);
    }
}
