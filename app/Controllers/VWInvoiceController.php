<?php

namespace App\Controllers;

use App\Models\VWInvoiceModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use NumberFormatter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

use function App\Helpers\formatRupiah;

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

    /**
     * @OA\Get(
     *     path="/api/invoice/excel/create",
     *     summary="Generate Excel file containing invoice data",
     *     description="This endpoint generates and downloads an Excel file that includes a summary of all invoice data.",
     *     tags={"Invoice Pelanggan"},
     *     @OA\Response(
     *         response=200,
     *         description="Excel file successfully generated and downloaded.",
     *         @OA\Header(
     *             header="Content-Type",
     *             description="The MIME type of the returned file (application/vnd.openxmlformats-officedocument.spreadsheetml.sheet)",
     *             @OA\Schema(type="string", example="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet")
     *         ),
     *         @OA\Header(
     *             header="Content-Disposition",
     *             description="Attachment header with the filename",
     *             @OA\Schema(type="string", example="attachment; filename=rekap_all_data_invoice_2024-06-14.xlsx")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error occurred while generating the Excel file."
     *     )
     * )
     */
    public function createExcel()
    {

        $formatter = new NumberFormatter('id_ID', NumberFormatter::CURRENCY);
        $filename = 'rekap_all_data_invoice_' . date('Y-m-d') . '.xlsx';
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        // $activeWorksheet->setCellValue('A1', 'Hello World !');

        $spreadsheet->getActiveSheet()->mergeCells('A1:D1');
        $spreadsheet->getActiveSheet()->mergeCells('A3:B3');
        $spreadsheet->getActiveSheet()->mergeCells('C3:E3');

        $activeWorksheet->setCellValue('A1', 'Rekap All Data Invoice');
        $activeWorksheet->setCellValue('A3', 'Tanggal');
        $activeWorksheet->setCellValue('C3', date('Y-m-d'));
        $activeWorksheet->setCellValue('A4', 'ID');
        $activeWorksheet->setCellValue('B4', 'ID Penjualan');
        $activeWorksheet->setCellValue('C4', 'Tanggal');
        $activeWorksheet->setCellValue('D4', 'ID Pelanggan');
        $activeWorksheet->setCellValue('E4', 'Alamat');
        $activeWorksheet->setCellValue('F4', 'Telepon');
        $activeWorksheet->setCellValue('G4', 'ID Produk');
        $activeWorksheet->setCellValue('H4', 'Nama Produk');
        $activeWorksheet->setCellValue('I4', 'Harga');
        $activeWorksheet->setCellValue('J4', 'Quantity');
        $activeWorksheet->setCellValue('K4', 'Subtotal');

        $activeWorksheet->getStyle('A1:K4')->getFont()->setBold(true);

        foreach (range('A', 'K') as $columnID) {
            $activeWorksheet->getColumnDimension($columnID)->setWidth(120, 'pt');
        }

        $rows = 5;
        $no = 1;

        $all_data_invoice = $this->model->get_All();

        // dd($all_data_invoice);

        foreach ($all_data_invoice as $user_data) {

            // dd($user_data);
            $activeWorksheet->setCellValue('A' . $rows, $user_data['id']);
            $activeWorksheet->setCellValue('B' . $rows, $user_data['id_penjualan']);
            $activeWorksheet->setCellValue('C' . $rows, $user_data['tanggal']);
            $activeWorksheet->setCellValue('D' . $rows, $user_data['id_pelanggan']);
            $activeWorksheet->setCellValue('E' . $rows, $user_data['alamat']);
            $activeWorksheet->setCellValue('F' . $rows, $user_data['telepon']);
            $activeWorksheet->setCellValue('G' . $rows, $user_data['id_produk']);
            $activeWorksheet->setCellValue('H' . $rows, $user_data['nama_produk']);
            $activeWorksheet->setCellValue('I' . $rows, $formatter->formatCurrency($user_data['harga'], 'IDR'));
            $activeWorksheet->setCellValue('J' . $rows, $user_data['quantity']);
            $activeWorksheet->setCellValue('K' . $rows, $formatter->formatCurrency($user_data['subtotal'], 'IDR'));
            $activeWorksheet->getStyle('A1:K' . ($rows))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $rows++;
        }


        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=$filename");
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
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
