<?php

namespace App\Controllers;

use App\Models\VWInvoiceModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Dompdf\Dompdf;
use NumberFormatter;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

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
        try {

            $formatter = new NumberFormatter('id_ID', NumberFormatter::CURRENCY);
            $filename = 'rekap_all_data_invoice_' . date('Y-m-d') . '.xlsx';
            $spreadsheet = new Spreadsheet();
            $activeWorksheet = $spreadsheet->getActiveSheet();
    
            $spreadsheet->getActiveSheet()->mergeCells('A1:D1');
            $spreadsheet->getActiveSheet()->mergeCells('A3:B3');
            $spreadsheet->getActiveSheet()->mergeCells('C3:E3');
    
            $activeWorksheet->setCellValue('A1', 'Rekap All Data Invoice');
            $activeWorksheet->setCellValue('A3', 'Tanggal Rekap');
            $activeWorksheet->setCellValue('C3', date('Y-m-d'));
            $activeWorksheet->setCellValue('A4', '#');
            $activeWorksheet->setCellValue('B4', 'Tanggal');
            $activeWorksheet->setCellValue('C4', 'Nama Pelanggan');
            $activeWorksheet->setCellValue('D4', 'Alamat');
            $activeWorksheet->setCellValue('E4', 'Telepon');
            $activeWorksheet->setCellValue('F4', 'Nama Produk');
            $activeWorksheet->setCellValue('G4', 'Harga/pcs');
            $activeWorksheet->setCellValue('H4', 'Quantity');
            $activeWorksheet->setCellValue('I4', 'Subtotal');
            $activeWorksheet->setCellValue('J4', 'Total');
    
            $activeWorksheet->getStyle('A1:J4')->getFont()->setBold(true);
    
            foreach (range('A', 'J') as $columnID) {
                $activeWorksheet->getColumnDimension($columnID)->setWidth(80, 'pt');
            }
    
            $rows = 5;
            $rowsj = 5;
    
            $all_data_invoice = $this->model->get_All();
            $grandtotal = $this->model->grandtotals();

            // var_dump(json_encode($grandtotal));

            // if (isset($grandtotal[$no])) {
            //     dd("Missing index: $no", $grandtotal, $all_data_invoice);
            // }
            
    
            // dd($grandtotal[5]['grandtotal']);
    
            // foreach ($all_data_invoice as $user_data) {
    
            //     // dd($user_data);
            //     $activeWorksheet->setCellValue('A' . $rows, $user_data['id']);
            //     $activeWorksheet->setCellValue('B' . $rows, $user_data['tanggal']);
            //     $activeWorksheet->setCellValue('C' . $rows, $user_data['nama_pelanggan']);
            //     $activeWorksheet->setCellValue('D' . $rows, $user_data['alamat']);
            //     $activeWorksheet->setCellValue('E' . $rows, $user_data['telepon']);
            //     $activeWorksheet->setCellValue('F' . $rows, $user_data['nama_produk']);
            //     $activeWorksheet->setCellValue('G' . $rows, $formatter->formatCurrency($user_data['harga'], 'IDR'));
            //     $activeWorksheet->setCellValue('H' . $rows, $user_data['quantity']);
            //     $activeWorksheet->setCellValue('I' . $rows, $formatter->formatCurrency($user_data['subtotal'], 'IDR'));
            //     $activeWorksheet->getStyle('A1' . ($rows))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            //     $activeWorksheet->getStyle('B1:J' . ($rows))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            //     $rows++;
            // }
            
            // foreach ($grandtotal as $gt) {
            //     var_dump($gt['grandtotal']);
            //     // dd($gt['grandtotal']);
            //     // $activeWorksheet->setCellValue('J' . $rowsj, $formatter->formatCurrency($gt['grandtotal'], 'IDR'));
            // }

            var_dump($grandtotal);
    
    
            // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            // header("Content-Disposition: attachment;filename=$filename");
            // header('Cache-Control: max-age=0');
    
            // $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            // $writer = new Xlsx($spreadsheet);
            // $writer->save('php://output');
        } catch(\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    public function createExcelByDate($startDate, $endDate) 
    {

        // $startDate = $this->request->getGet('start_date');
        // $endDate = $this->request->getGet('end_date');

        if (!$startDate || !$endDate) {
            $response = [
                'message' => $this->failValidationErrors('Both start_date and end_date are required.')
            ];

            return $this->respond($response);
        }

        try {

            $formatter = new NumberFormatter('id_ID', NumberFormatter::CURRENCY);
            $filename = 'rekap_all_data_invoice_by_date' . date('Y-m-d') . '.xlsx';
            $spreadsheet = new Spreadsheet();
            $activeWorksheet = $spreadsheet->getActiveSheet();
    
            $spreadsheet->getActiveSheet()->mergeCells('A1:D1');
            $spreadsheet->getActiveSheet()->mergeCells('A3:B3');
            $spreadsheet->getActiveSheet()->mergeCells('C3:E3');
    
            $activeWorksheet->setCellValue('A1', 'Rekap All Data Invoice By Date');
            $activeWorksheet->setCellValue('A3', 'Tanggal Rekap');
            $activeWorksheet->setCellValue('C3', date('Y-m-d'));
            $activeWorksheet->setCellValue('A4', '#');
            $activeWorksheet->setCellValue('B4', 'ID Penjualan');
            $activeWorksheet->setCellValue('C4', 'Tanggal');
            $activeWorksheet->setCellValue('D4', 'ID Pelanggan');
            $activeWorksheet->setCellValue('E4', 'Nama Pelanggan');
            $activeWorksheet->setCellValue('F4', 'Alamat');
            $activeWorksheet->setCellValue('G4', 'Telepon');
            $activeWorksheet->setCellValue('H4', 'ID Produk');
            $activeWorksheet->setCellValue('I4', 'Nama Produk');
            $activeWorksheet->setCellValue('J4', 'Harga');
            $activeWorksheet->setCellValue('K4', 'Quantity');
            $activeWorksheet->setCellValue('L4', 'Subtotal');
            $activeWorksheet->setCellValue('M4', 'Subtotal');
    
            $activeWorksheet->getStyle('A1:M4')->getFont()->setBold(true);
    
            foreach (range('A', 'M') as $columnID) {
                $activeWorksheet->getColumnDimension($columnID)->setWidth(120, 'pt');
            }
    
            $rows = 5;
    
            $all_data_invoice = $this->model->get_All_by_Date($startDate, $endDate);
    
            // dd($all_data_invoice);
    
            foreach ($all_data_invoice as $user_data) {
    
                // dd($user_data);
                $activeWorksheet->setCellValue('A' . $rows, $user_data['id']);
                $activeWorksheet->setCellValue('B' . $rows, $user_data['id_penjualan']);
                $activeWorksheet->setCellValue('C' . $rows, $user_data['tanggal']);
                $activeWorksheet->setCellValue('D' . $rows, $user_data['id_pelanggan']);
                $activeWorksheet->setCellValue('E' . $rows, $user_data['nama_pelanggan']);
                $activeWorksheet->setCellValue('F' . $rows, $user_data['alamat']);
                $activeWorksheet->setCellValue('G' . $rows, $user_data['telepon']);
                $activeWorksheet->setCellValue('H' . $rows, $user_data['id_produk']);
                $activeWorksheet->setCellValue('I' . $rows, $user_data['nama_produk']);
                $activeWorksheet->setCellValue('J' . $rows, $formatter->formatCurrency($user_data['harga'], 'IDR'));
                $activeWorksheet->setCellValue('K' . $rows, $user_data['quantity']);
                $activeWorksheet->setCellValue('L' . $rows, $formatter->formatCurrency($user_data['subtotal'], 'IDR'));
                $activeWorksheet->setCellValue('M' . $rows, $formatter->formatCurrency($user_data['subtotal'], 'IDR'));
                $activeWorksheet->getStyle('A1:L' . ($rows))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $rows++;
            }
    
    
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment;filename=$filename");
            header('Cache-Control: max-age=0');
    
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        } catch(\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    public function createPDF($id)
    {
        try {
            //
            $dompdf = new Dompdf();
            $options = $dompdf->getOptions();
            $options->setDefaultFont('DejaVuSansMono-Bold');
            $dompdf->setOptions($options);

            $data = [
                'pelanggan' => $this->model->filterById($id),
                'grandtotal' => $this->model->grandtotal($id)
            ];

            $dompdf->loadHtml(view('view_invoice', $data));
            $dompdf->setPaper('A4', 'potrait');
            // $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            $dompdf->stream('invoice pelanggan ' . date('Y-m-d') . '.pdf', array(
                'Attacthment' => false
            ));
            
        } catch(\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    public function pdfView($id)
    {
        $user_data = $this->model->filterById($id);
        $grandtotal = $this->model->grandtotal($id);

        // dd($grandtotal);

        $data = [
            'pelanggan' => $user_data,
            'grandtotal' => $grandtotal
        ];
        // foreach ($data as $key => $value) {
        //     # code...
        //     dd($value[0]);
        // }
        return view('view_invoice', $data);
    }
}
