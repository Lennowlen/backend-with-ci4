<?php

namespace App\Controllers;

use App\Models\VWInvoiceModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

/**
 * @OA\Tag(
 *     name="Invoice Pelanggan",
 *     description="Endpoint Invoice Pelanggan"
 * )
 */
class VWInvoiceController extends ResourceController
{

    protected $modelName = VWInvoiceModel::class;
    protected $format = 'json';

    /**
     * @OA\Get(
     *     path="/api/invoice",
     *     summary="Mendapatkan daftar semua ivoice pelanggan",
     *     tags={"Invoice Pelanggan"},
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mendapatkan daftar invoice pelanggan",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="data_view_invoice", 
     *                 type="array", 
     *                 @OA\Items(
     *                     @OA\Property(property="id_invoice", type="integer"),
     *                     @OA\Property(property="nama_pelanggan", type="string"),
     *                     @OA\Property(property="tanggal", type="string", format="date"),
     *                     @OA\Property(property="produk", type="string"),
     *                     @OA\Property(property="grand_total", type="number")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index()
    {
        //
        $response = [
            'message' => 'success',
            "data_view_invoice" => $this->model->orderBy('id', 'DESC')->findAll()
        ];

        return $this->respond($response, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/invoice/{id}",
     *     summary="Mendapatkan detail invoice pelanggan berdasarkan ID",
     *     tags={"Invoice Pelanggan"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID invoice pelanggan",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mendapatkan detail invoice pelanggan"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Invoice Pelanggan tidak ditemukan"
     *     )
     * )
     */
    public function showInvoice($id = null)
    {
        //
        $invoice = [
            'message' => 'success',
            'data_view_invoice' => $this->model->find($id)
        ];

        if (!$invoice) {
            return $this->failNotFound('Pelanggan tidak ditemukan');
        }

        return $this->respond($invoice, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/invoice/filter",
     *     summary="Mendapatkan data berdasarkan rentang tanggal",
     *     tags={"Invoice Pelanggan"},
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         description="Tanggal mulai untuk filter [format: YYYY-MM-DD]",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="date",
     *             example="2024-12-11"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         description="Tanggal akhir untuk filter [format: YYYY-MM-DD]",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             format="date",
     *             example="2024-12-12"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mendapatkan data yang difilter berdasarkan rentang tanggal"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Format tanggal tidak valid atau parameter tidak ditemukan"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Data tidak ditemukan untuk rentang tanggal yang diberikan"
     *     )
     * )
     */
    public function filterByDate()
    {
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        if (!$startDate || !$endDate) {
            $response = [
                'message' => $this->failValidationErrors('Both start_date and end_date are required.')
            ];

            return $this->respond($response);
        }

        try {

            $data = [
                'message' => 'success',
                'data_filter_by_date' => $this->model->orderBy('id', 'DESC')->filterByDate($startDate, $endDate)
            ];


            if (!$data) {
                $response = [
                    'message' => $this->failNotFound('Data not found!')
                ];

                return $this->respond($response);
            }

            return $this->respond($data, 200);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    public function filterById($id = null)
    {


        try {

            $data = [
                'message' => 'success',
                'data_filter_date_byid' => $this->model->filterById($id)
            ];

            if (!$data[1]) {
                $response = [
                    'message' => $this->failNotFound('Pelanggan tidak ditemukan')
                ];
                return $this->respond($response, 404);
            }

            return $this->respond($data, 200);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/invoice/detail",
     *     summary="Mendapatkan daftar detail semua ivoice pelanggan",
     *     tags={"Invoice Pelanggan"},
     *     @OA\Response(
     *         response=200,
     *         description="Berhasil mendapatkan daftar invoice pelanggan",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(
     *                 property="data_view_invoice", 
     *                 type="array", 
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="id_penjualan", type="integer"),
     *                     @OA\Property(property="tanggal", type="string", format="date"),
     *                     @OA\Property(property="id_pelanggan", type="integer"),
     *                     @OA\Property(property="nama_pelanggan", type="string"),
     *                     @OA\Property(property="alamat", type="string"),
     *                     @OA\Property(property="telepon", type="string"),
     *                     @OA\Property(property="id_produk", type="integer"),
     *                     @OA\Property(property="nama_produk", type="string"),
     *                     @OA\Property(property="harga", type="number"),
     *                     @OA\Property(property="quantity", type="integer"),
     *                     @OA\Property(property="subtotal", type="number")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function getAll() 
    {
        try {

            $data = [
                'message' => 'success',
                'data_detail_invoice_pelanggan' => $this->model->get_All()
            ];

            return $this->respond($data, 200);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }
    public function new()
    {
        //
    }

    public function create()
    {
        //
    }

    public function edit($id = null)
    {
        //
    }

    public function update($id = null)
    {
        //
    }

    public function delete($id = null)
    {
        //
    }
}
