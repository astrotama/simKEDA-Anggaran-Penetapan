<?php

function lampiran2_bintang_main() {
	
		$exportpdf = arg(4);
		if (isset($exportpdf))  {
		if($exportpdf =='pdf'){
			$pdfFile = 'rka-skpd-lampiran2.pdf';
			
			$htmlContent = lampiran2_bintang_content();
			apbd_ExportPDF3P_CF(10, 10, NULL, $htmlContent, NULL, $pdfFile, 1);
		}else {
			lampiran2_bintang_content_excel();
		}
		}else{
		$output = drupal_get_form('lampiran2_bintang_form');
		$output .= lampiran2_bintang_content();
		return $output;
		}

}

function lampiran2_bintang_content() {
	//drupal_set_message($tingkat);
$header[] = array (
	array('data' => 'No.',  'width'=> '30px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
	array('data' => 'SKPD',  'width' => '230x', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
	array('data' => 'Anggaran',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
	array('data' => 'Tersedia',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
	array('data' => 'Selisih',  'width' => '80px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

);


		
$result_uk = db_query('select kodeuk,namasingkat from {unitkerja} order by kodedinas');

$no = 0;
$t_anggaran =0; $t_tersedia = 0;
while ($datauk = db_fetch_object($result_uk)) {
	$no++;
	
	$anggaran =0; $tersedia = 0;
	$sql = 'select sum(a.jumlah) jumlahx,sum(a.anggaran) anggaranx from {anggperkeg} a inner join {kegiatanskpd} k on a.kodekeg=k.kodekeg where k.inaktif=0 and k.kodeuk=\'%s\'';
	$fsql = sprintf($sql, db_escape_string($datauk->kodeuk));
	$res = db_query($fsql);
	if ($data = db_fetch_object($res)) {
		$anggaran = $data->jumlahx; 
		$tersedia = $data->anggaranx;
	}
	$t_anggaran += $anggaran; $t_tersedia += $tersedia;
	
	$row[] = array (
		array('data' => $no,  'width'=> '30px', 'style' => 'border-right: 1px solid black;  border-left: 1px solid black; text-align:left;'),
		array('data' => $datauk->namasingkat,  'width' => '230px', 'style' => ' border-right: 1px solid black; text-align:left;'),
		array('data' => apbd_fn($anggaran),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
		array('data' => apbd_fn($tersedia),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
		array('data' => apbd_fn($anggaran -  $tersedia),  'width' => '80px', 'style' => ' border-right: 1px solid black; text-align:right;'),
		);
								 
}

$row[] = array (
	array('data' => '',  'width'=> '30px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black;border-bottom: 1px solid black;'),
	array('data' => 'TOTAL',  'width' => '230px', 'style' => 'border-top: 1px solid black; border-right: 1px solid black; text-align:right; font-weight:bold;border-bottom: 1px solid black;'),
	array('data' => apbd_fn($t_anggaran),  'width' => '90px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
	array('data' => apbd_fn($t_tersedia),  'width' => '90px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
	array('data' => apbd_fn($t_anggaran - $t_tersedia),  'width' => '80px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
);
	
$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

$output .= theme_box('', apbd_theme_table($header, $row, $opttbl));
	
$output .= $toutput;
	
return $output;
	
}

function lampiran2_bintang_content_excel() {
error_reporting(E_ALL);

set_time_limit(0);
ini_set('memory_limit', '640M');

ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
//date_default_timezone_set('Europe/London');

if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once 'files/PHPExcel/Classes/PHPExcel.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
// Set document properties
$objPHPExcel->getProperties()->setCreator("SIPKD Anggaran Online")
							 ->setLastModifiedBy("SIPKD Anggaran Online")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Excel document generated from SIPKD Anggaran Online.")
							 ->setKeywords("office 2007 SIPKD Anggaran Online openxml php")
							 ->setCategory("SIPKD Anggaran Analisa");
// Add Header
$row = 1;
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $row ,'SKPD')
			->setCellValue('B' . $row ,'ANGGARAN')
			->setCellValue('C' . $row ,'TERSEDIA')
			->setCellValue('D' . $row ,'SELISIH');
			
			
			
			


$result_uk = db_query('select kodeuk,namasingkat from {unitkerja} order by kodedinas');

$no = 0;
$t_anggaran =0; $t_tersedia = 0;
while ($datauk = db_fetch_object($result_uk)) {
	$no++;
	
	$anggaran =0; $tersedia = 0;
	$sql = 'select sum(a.jumlah) jumlahx,sum(a.anggaran) anggaranx from {anggperkeg} a inner join {kegiatanskpd} k on a.kodekeg=k.kodekeg where k.inaktif=0 and k.kodeuk=\'%s\'';
	$fsql = sprintf($sql, db_escape_string($datauk->kodeuk));
	$res = db_query($fsql);
	if ($data = db_fetch_object($res)) {
		$anggaran = $data->jumlahx; 
		$tersedia = $data->anggaranx;
	}
	$t_anggaran += $anggaran; $t_tersedia += $tersedia;
			
		
	$row++;
	
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $row, $datauk->namasingkat)
				->setCellValue('B' . $row, apbd_fn($anggaran))
				->setCellValue('C' . $row, apbd_fn($tersedia))
				->setCellValue('D' . $row, apbd_fn($anggaran -  $tersedia));
}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('LAMPIRAN II APBD');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
$fname = 'Lampiran_II_APBD.xlsx';
header('Content-Disposition: attachment;filename=' . $fname);
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
}

			
			

function lampiran2_bintang_form () {
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Cetak Excel dan Pdf',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);
	$form['formdata']['excel'] = array (
		'#type' => 'submit',
		'#value' => 'Excel',
		'#weight' => 9,
	); 
	$form['formdata']['submit'] = array (
		'#type' => 'submit',
		'#value' => 'Cetak',
		'#weight' => 10,
	); 
	
	return $form;
}

function lampiran2_bintang_form_submit($form, &$form_state) {

	if($form_state['clicked_button']['#value'] == $form_state['values']['tampilkan']) 
        $uri = 'apbd/laporanpenetapan/apbd/lampiran2bintang/';
	else if($form_state['clicked_button']['#value'] == $form_state['values']['excel']) 
		$uri = 'apbd/laporanpenetapan/apbd/lampiran2bintang/excel' ;
	else
		$uri = 'apbd/laporanpenetapan/apbd/lampiran2bintang/pdf' ;
	
	drupal_goto($uri);
	
}
?>