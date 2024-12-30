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
     * @OA\GET(
     *     path="/api/invoice/excel/create",
     *     summary="Membuat Excel Invoice Pelanggan dengan semua data",
     *     tags={"Invoice Pelanggan"},
     *     @OA\Response(
     *         response=200,
     *         description="Invoice pelanggan dengan ID tertentu berhasil dibuat"
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
            $all_data_invoice = $this->model->get_All();
            $grandtotal = $this->model->grandtotals();

            // Sort data by date
            usort($all_data_invoice, function ($a, $b) {
                return strtotime($a['tanggal']) - strtotime($b['tanggal']);
            });

            $formatter = new NumberFormatter('id_ID', NumberFormatter::CURRENCY);
            $filename = 'rekap_all_data_invoice_' . date('Y-m-d') . '.xlsx';
            $spreadsheet = new Spreadsheet();
            $activeWorksheet = $spreadsheet->getActiveSheet();

            // Set page orientation and size
            $activeWorksheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $activeWorksheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

            // Initial header setup
            $activeWorksheet->mergeCells('A1:J1');
            $activeWorksheet->mergeCells('A3:B3');
            $activeWorksheet->mergeCells('C3:E3');

            // Title Styling
            $activeWorksheet->getStyle('A1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 16,
                    'color' => ['rgb' => '000000']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E8E8E8']
                ]
            ]);
            $activeWorksheet->getRowDimension(1)->setRowHeight(30);

            // Header Row Styling
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ];

            // Group data by id_pelanggan and tanggal
            $grouped_data = [];
            foreach ($all_data_invoice as $item) {
                $key = $item['id_pelanggan'] . '_' . $item['tanggal'];
                if (!isset($grouped_data[$key])) {
                    $grouped_data[$key] = [
                        'rows' => [],
                        'id_pelanggan' => $item['id_pelanggan'],
                        'tanggal' => $item['tanggal']
                    ];
                }
                $grouped_data[$key]['rows'][] = $item;
            }

            $rows = 5;
            $final_total = 0;

            // Data styling
            $dataStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ],
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ]
            ];

            // Write data
            foreach ($grouped_data as $key => $group) {
                foreach ($group['rows'] as $value) {
                    $activeWorksheet->setCellValue('A' . $rows, $value['id']);
                    $activeWorksheet->setCellValue('B' . $rows, $value['tanggal']);
                    $activeWorksheet->setCellValue('C' . $rows, $value['nama_pelanggan']);
                    $activeWorksheet->setCellValue('D' . $rows, $value['alamat']);
                    $activeWorksheet->setCellValue('E' . $rows, $value['telepon']);
                    $activeWorksheet->setCellValue('F' . $rows, $value['nama_produk']);
                    $activeWorksheet->setCellValue('G' . $rows, $formatter->formatCurrency($value['harga'], 'IDR'));
                    $activeWorksheet->setCellValue('H' . $rows, $value['quantity']);
                    $activeWorksheet->setCellValue('I' . $rows, $formatter->formatCurrency($value['subtotal'], 'IDR'));

                    // Calculate final total
                    $final_total += $value['subtotal'];

                    // Apply zebra striping
                    if ($rows % 2 == 0) {
                        $activeWorksheet->getStyle('A' . $rows . ':I' . $rows)->applyFromArray([
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F5F5F5']
                            ]
                        ]);
                    }

                    $rows++;
                }
            }

            // Add total row
            $total_row = $rows;
            $activeWorksheet->mergeCells('A' . $total_row . ':H' . $total_row);
            $activeWorksheet->setCellValue('A' . $total_row, 'Total :');
            $activeWorksheet->setCellValue('I' . $total_row, $formatter->formatCurrency($final_total, 'IDR'));

            // Style the total row
            $totalStyle = [
                'font' => [
                    'bold' => true
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ]
            ];
            $activeWorksheet->getStyle('A' . $total_row . ':I' . $total_row)->applyFromArray($totalStyle);

            // Apply styles to the data range
            $activeWorksheet->getStyle('A5:I' . ($rows - 1))->applyFromArray($dataStyle);

            // Set headers
            $activeWorksheet->setCellValue('A1', 'REKAP ALL DATA INVOICE');
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

            // Apply header styling
            $activeWorksheet->getStyle('A4:I4')->applyFromArray($headerStyle);
            $activeWorksheet->getRowDimension(4)->setRowHeight(25);

            // Column widths
            $columnWidths = [
                'A' => 40,  // #
                'B' => 80,  // Tanggal
                'C' => 120, // Nama Pelanggan
                'D' => 150, // Alamat
                'E' => 100, // Telepon
                'F' => 120, // Nama Produk
                'G' => 100, // Harga/pcs
                'H' => 60,  // Quantity
                'I' => 100  // Subtotal
            ];

            foreach ($columnWidths as $column => $width) {
                $activeWorksheet->getColumnDimension($column)->setWidth($width, 'pt');
            }

            // Align numeric columns
            $activeWorksheet->getStyle('G5:I' . ($rows))->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            // Set print area
            $activeWorksheet->getPageSetup()->setPrintArea('A1:I' . $total_row);

            // Output the file
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment;filename=$filename");
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * @OA\GET(
     *     path="/api/invoice/excel/bydate/create/{startDate}/{endDate}",
     *     summary="Membuat Excel Invoice Pelanggan berdasarkan rentang tanggal",
     *     tags={"Invoice Pelanggan"},
     *      @OA\Parameter(
     *         name="startDate",
     *         in="path",
     *         description="ID Pelanggan",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *      @OA\Parameter(
     *         name="endDate",
     *         in="path",
     *         description="ID Pelanggan",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Invoice pelanggan dengan ID tertentu berhasil dibuat"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error occurred while generating the Excel file."
     *     )
     * )
     */
    public function createExcelByDate($startDate, $endDate)
    {
        if (!$startDate || !$endDate) {
            $response = [
                'message' => $this->failValidationErrors('Both start_date and end_date are required.')
            ];

            return $this->respond($response);
        }

        try {
            $all_data_invoice = $this->model->get_All_by_Date($startDate, $endDate);
            $grandtotal = $this->model->grandtotals();

            // Sort data by date
            usort($all_data_invoice, function ($a, $b) {
                return strtotime($a['tanggal']) - strtotime($b['tanggal']);
            });

            $formatter = new NumberFormatter('id_ID', NumberFormatter::CURRENCY);
            $filename = 'rekap_all_data_invoice_by_date_' . date('Y-m-d') . '.xlsx';
            $spreadsheet = new Spreadsheet();
            $activeWorksheet = $spreadsheet->getActiveSheet();

            // Set page orientation and size
            $activeWorksheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            $activeWorksheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);

            // Initial header setup
            $activeWorksheet->mergeCells('A1:J1');
            $activeWorksheet->mergeCells('A3:B3');
            $activeWorksheet->mergeCells('C3:E3');

            // Title Styling
            $activeWorksheet->getStyle('A1')->applyFromArray([
                'font' => [
                    'bold' => true,
                    'size' => 16,
                    'color' => ['rgb' => '000000']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E8E8E8']
                ]
            ]);
            $activeWorksheet->getRowDimension(1)->setRowHeight(30);

            // Header Row Styling
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4']
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ];

            // Group data by id_pelanggan and tanggal
            $grouped_data = [];
            foreach ($all_data_invoice as $item) {
                $key = $item['id_pelanggan'] . '_' . $item['tanggal'];
                if (!isset($grouped_data[$key])) {
                    $grouped_data[$key] = [
                        'rows' => [],
                        'id_pelanggan' => $item['id_pelanggan'],
                        'tanggal' => $item['tanggal']
                    ];
                }
                $grouped_data[$key]['rows'][] = $item;
            }

            $rows = 5;
            $final_total = 0;

            // Data styling
            $dataStyle = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ],
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ]
            ];

            // Write data
            foreach ($grouped_data as $key => $group) {
                foreach ($group['rows'] as $value) {
                    $activeWorksheet->setCellValue('A' . $rows, $value['id']);
                    $activeWorksheet->setCellValue('B' . $rows, $value['tanggal']);
                    $activeWorksheet->setCellValue('C' . $rows, $value['nama_pelanggan']);
                    $activeWorksheet->setCellValue('D' . $rows, $value['alamat']);
                    $activeWorksheet->setCellValue('E' . $rows, $value['telepon']);
                    $activeWorksheet->setCellValue('F' . $rows, $value['nama_produk']);
                    $activeWorksheet->setCellValue('G' . $rows, $formatter->formatCurrency($value['harga'], 'IDR'));
                    $activeWorksheet->setCellValue('H' . $rows, $value['quantity']);
                    $activeWorksheet->setCellValue('I' . $rows, $formatter->formatCurrency($value['subtotal'], 'IDR'));

                    // Calculate final total
                    $final_total += $value['subtotal'];

                    // Apply zebra striping
                    if ($rows % 2 == 0) {
                        $activeWorksheet->getStyle('A' . $rows . ':I' . $rows)->applyFromArray([
                            'fill' => [
                                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F5F5F5']
                            ]
                        ]);
                    }

                    $rows++;
                }
            }

            // Add total row
            $total_row = $rows;
            $activeWorksheet->mergeCells('A' . $total_row . ':H' . $total_row);
            $activeWorksheet->setCellValue('A' . $total_row, 'Total :');
            $activeWorksheet->setCellValue('I' . $total_row, $formatter->formatCurrency($final_total, 'IDR'));

            // Style the total row
            $totalStyle = [
                'font' => [
                    'bold' => true
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                ]
            ];
            $activeWorksheet->getStyle('A' . $total_row . ':I' . $total_row)->applyFromArray($totalStyle);

            // Apply styles to the data range
            $activeWorksheet->getStyle('A5:I' . ($rows - 1))->applyFromArray($dataStyle);

            // Set headers
            $activeWorksheet->setCellValue('A1', 'REKAP ALL DATA INVOICE');
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

            // Apply header styling
            $activeWorksheet->getStyle('A4:I4')->applyFromArray($headerStyle);
            $activeWorksheet->getRowDimension(4)->setRowHeight(25);

            // Column widths
            $columnWidths = [
                'A' => 40,  // #
                'B' => 80,  // Tanggal
                'C' => 120, // Nama Pelanggan
                'D' => 150, // Alamat
                'E' => 100, // Telepon
                'F' => 120, // Nama Produk
                'G' => 100, // Harga/pcs
                'H' => 60,  // Quantity
                'I' => 100  // Subtotal
            ];

            foreach ($columnWidths as $column => $width) {
                $activeWorksheet->getColumnDimension($column)->setWidth($width, 'pt');
            }

            // Align numeric columns
            $activeWorksheet->getStyle('G5:I' . ($rows))->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            // Set print area
            $activeWorksheet->getPageSetup()->setPrintArea('A1:I' . $total_row);

            // Output the file
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment;filename=$filename");
            header('Cache-Control: max-age=0');

            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            exit;
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * @OA\GET(
     *     path="/api/pdf/create/{id}",
     *     summary="Membuat Invoice Pelanggan",
     *     tags={"Invoice Pelanggan"},
     *      @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID Pelanggan",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Invoice pelanggan dengan ID tertentu berhasil dibuat"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error occurred while generating the Excel file."
     *     )
     * )
     */
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
        } catch (\Exception $e) {
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

        return view('view_invoice', $data);
    }
}
