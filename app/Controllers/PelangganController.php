<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class PelangganController extends ResourceController
{
    protected $modelName = 'App\Models\PelangganModel';
    protected $format = 'json';
    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function index()
    {
        //
        $data = [
            'message' => 'success',
            'data_pelanggan' => $this->model->orderBy('id_pelanggan', 'DESC')->findAll()
        ];
        
        return $this->respond($data, 200);
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
            'message' => 'Data berhasil dihapus!'
        ];

        return $this->respondDeleted($response);
    }
}
