<?php
    /*
     * To change this license header, choose License Headers in Project Properties.
     * To change this template file, choose Tools | Templates
     * and open the template in the editor.
     */

    /**
     * @package Createpdf :  CodeIgniter Create PDF
     *
     * @author Kshitija Swami
     *
     * @email  kshitijaswami@gmail.com
     *   
     * Description of Createpdf Controller
     */
    if (!defined('BASEPATH'))
        exit('No direct script access allowed');

      require APPPATH . 'libraries/REST_Controller.php';


    class Export extends REST_Controller {

        public function __construct() {
            parent::__construct();
            $this->load->model('Export_model', 'Export');
          $this->load->library("excel");
        }
        public function index() {   
            
            $this->load->view('pdf/index');
            
        }    
       // generate PDF File
         public function ExportData_post() {         
            $data1 =$this->post();
            $type = $data1['type'];
            $tableName = $data1['tableName'];
            $title = $data1['title'];
            $p_id = $data1['project_id'];
            $path="";
            $htmlContent='';
            $update = ["daily_update","event_update","story_update"];
            if(in_array($tableName, $update))
             $result= $this->Export->getUpdates($tableName,$p_id);
            else
            $result= $this->Export->getContent($tableName);
            $tableHead=[];
                        foreach ($result as $key => $value) {
                            if($key===0){
                                foreach ($value as $key1 => $value1) {
                                array_push($tableHead, $key1)  ;      
                            }
                            } 
                        }

                        if($type=="pdf"){
                          $path=$this->exportPDF($tableHead,$title,$result);
                        }
                        else{
                          $this->exportExcel($tableHead,$title,$result);
                        }

          

          $this->response($path, REST_Controller::HTTP_OK);
         }

         public function exportPDF($tableHead,$title,$result){
          $data["tablehead"]=$tableHead;
          $data["title"]=$title;
          $data["result"]=$result;
      $htmlContent = $this->load->view('pdf/virtual', $data, TRUE);       
          $createPDFFile = $title.date('d-M-y').time().'.pdf';
          $this->createPDF(FCPATH."assets/Pdf/".$createPDFFile, $htmlContent,$tableHead);
         return base_url()."assets/Pdf/".$createPDFFile;
         }

        // create pdf file 
        public function createPDF($fileName,$html,$tableHead) {
            ob_start(); 
            // Include the main TCPDF library (search for installation path).
            $size=sizeof($tableHead);
            $orientation=$size>4?'L':'P';
            $this->load->library('Pdf');
            // create new PDF document
            $pdf = new TCPDF( $orientation, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            // set document information
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('RSB FOUNDATION');
            $pdf->SetTitle('RSB Report');

            // set default header data
            $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

            // set header and footer fonts
            $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
            
            $pdf->SetPrintHeader(false);
            $pdf->SetPrintFooter(false);

            // set default monospaced font
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

            // set margins
            $pdf->SetMargins(PDF_MARGIN_LEFT, 0, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(10);
            $pdf->SetFooterMargin(10);

            // set auto page breaks
            //$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
            $pdf->SetAutoPageBreak(TRUE, 0);

            // set image scale factor
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

            // set some language-dependent strings (optional)
            if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
                require_once(dirname(__FILE__).'/lang/eng.php');
                $pdf->setLanguageArray($l);
            }     

            //  set image
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

            // set font
            $pdf->SetFont('freesans', '', 10);

            // add a page
            $pdf->AddPage();

            // output the HTML content
            $pdf->writeHTML($html, true, false, true, false, '');

            // reset pointer to the last page
            $pdf->lastPage();       
            ob_end_clean();
            //Close and output PDF document
            $pdf->Output($fileName, 'F');        
        }

        public function exportExcel($tableHead,$title,$result)
        {
         $object = new PHPExcel();

 $object->setActiveSheetIndex(0);
 $sheet=$object->getActiveSheet();
 $ColumnLength = (int)sizeof($tableHead);
 $alphabet = range('A', 'Z');
 $start_letter = 0;
 $rowno = 2;
 $mergeCell;
 for ($i = 0; $i < $ColumnLength  ; $i++)  // merge the cells
 {
  $sheet->mergeCells($alphabet[$start_letter] . $rowno . ':' . ($alphabet[$start_letter +$i]) . $rowno);
  $mergeCell =$alphabet[$start_letter] . $rowno . ':' . ($alphabet[$start_letter +$i]) . $rowno;
  $mergecol =$alphabet[$start_letter] . $rowno;
  $mergerow = $alphabet[$start_letter+$i] . $rowno ;
}

 $a = $ColumnLength/2;
 $b = $alphabet[$start_letter-1 + $a];

//echo "A".$a."B".$b;
 $sheet->setTitle($title);
$sheet->setCellValueByColumnAndRow(0, 2, "RSB FOUNDATION");
$sheet->getStyle($b)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


$column = 0;
$POSITION=8;

foreach($tableHead as $key => $columnData) // set table heading
{
  $heading =ucfirst($columnData);
 $sheet->setCellValueByColumnAndRow($column, $POSITION, $heading);
 $column++;
}

foreach($alphabet as $columnID) // set the column auto size
{
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

foreach($result  as $key => $ExcelData)
{
  $excel_row = 0;   
   foreach ($ExcelData as $Ex => $rowData) // set the row data 
  {
    $fetchData = ucfirst($rowData);
 $sheet->setCellValueByColumnAndRow($excel_row,$POSITION+1, $fetchData);
 $excel_row++;

}
$POSITION++;
 
}
 //$this->load->view("Excel/excel_export_view", $object);
  $object_writer = PHPExcel_IOFactory::createWriter($object, 'Excel5');
  header('Content-Type: application/vnd.ms-excel');
  header('Content-Disposition: attachment;filename="'$title.'-'.date("d-M-y").time().'.xls"');
  $object_writer->save('php://output');
}
        

        public function deleteAllFiles(){
            $files = glob(FCPATH.'assets/Pdf/*');
  foreach ($files as $file) {
    if (is_file($file)) {
      
        unlink($file);
    }
  }
        }
    }
?>