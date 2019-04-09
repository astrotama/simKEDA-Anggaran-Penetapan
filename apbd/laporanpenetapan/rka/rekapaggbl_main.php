<?php
function rekapaggbl_main() {
$h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
$h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
drupal_set_html_head($h);
drupal_add_css('files/css/kegiatancam.css');
	
	$kodeuk = arg(4);
	$kodek = arg(5);
	$topmargin = arg(6);
	$exportpdf = arg(7);
	if ($topmargin=='') $topmargin=10;

	//drupal_set_message($exportpdf);
	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		//require_once('test.php');
		//myt();
		
		$pdfFile = 'rka-skpd-rekapaggbl-' . $kodeuk . '.pdf';
 
		//$htmlContent = GenReportForm(1);
		//apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);

		$htmlHeader = GenReportFormHeader(1);
		$htmlContent = GenReportFormContent($kodeuk, $kodek);
		$htmlFooter = GenReportFormFooter($kodeuk);
		
		apbd_ExportPDF3P($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, $pdfFile);
		
	} else {
		$url = 'apbd/laporanpenetapan/rka/rekapaggbl/'. $kodeuk . '/' . $topmargin . "/pdf";
		$output = drupal_get_form('rekapaggbl_form');
		//$output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
		$output .= GenReportForm();
		return $output;
	}

}

function GenReportForm($print=0) {
	
	$kodeuk = arg(4);
	$kodek = arg(5);
	//drupal_set_message($kodeuk);

	$skpd = '';
	$pimpinannama='';
	$pimpinannip='';
	$pimpinanjabatan='';
	$pquery = sprintf("select uk.kodedinas, uk.namauk, uk.pimpinannama, uk.pimpinannip, uk.pimpinanjabatan, 
				u.kodeu, u.urusan from {unitkerja} uk inner join {ukurusan} uku on uk.kodeuk=uku.kodeuk inner
				join {urusan} u on uku.kodeu=u.kodeu where uk.kodeuk='%s'", db_escape_string($kodeuk)) ;
	//drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$kodedinas = $data->kodedinas;
		$urusan = $data->kodeu . ' - ' . $data->urusan;
		$skpd = $kodedinas . ' - ' . $data->namauk;
		$pimpinannama=$data->pimpinannama;
		$pimpinannip=$data->pimpinannip;
		$pimpinanjabatan=$data->pimpinanjabatan;
	}

	$where = ' where k.kodeuk=\'%s\'';


	if ($kodek=='51') $strjudul = 'TIDAK ';
	$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
	$rows= array();
	$rowsjudul[] = array (array ('data'=>'REKAPITULASI ANGGARAN BELANJA ' . $strjudul . 'LANGSUNG', 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	
 
	$rowskegiatan[]= array (
						 array('data' => 'Urusan Pemerintahan',  'width'=> '150px', 'style' => 'border:none; text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'border:none; text-align:right;'),
						 array('data' => $urusan, 'width' => '370px', 'colspan'=>'5',  'style' => 'border:none;text-align:left;'),
						 );
	$rowskegiatan[]= array (
						 array('data' => 'Organisasi',  'width'=> '150px', 'style' => ' text-align:left;'),
						 array('data' => ':',  'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $skpd,  'width' => '370px', 'colspan'=>'5',  'style' => ' text-align:left;'),
						 );

	$headersrek[] = array (
						 //array('data' => 'Kode',  'width'=> '75px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 
						 array('data' => 'Kode',  'width'=> '60px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Uraian',  'width' => '375px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Jumlah',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						  

						 );

	$total = 0;
	//JENIS
	$sql = 'select r.kodej,r.uraian,sum(a.jumlah) jumlahx from {anggperkeg} a inner join {jenis} r on mid(a.kodero,1,3)=r.kodej inner join {kegiatanskpd} k on a.kodekeg=k.kodekeg where k.inaktif=0 and k.kodeuk=\'%s\' and mid(a.kodero,1,2)=\'%s\'';
	$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($kodek));
	$fsql .= ' group by r.kodej,r.uraian order by r.kodej';

	//drupal_set_message( $fsql);
	$resultjenis = db_query($fsql);
	if ($resultjenis) {
		while ($datajenis = db_fetch_object($resultjenis)) {
			$total += $datajenis->jumlahx;
			$rowsrek[] = array (
								 array('data' => ($datajenis->kodej),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $datajenis->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => apbd_fn($datajenis->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
								 );

			//OBYEK
			$sql = 'select r.kodeo,r.uraian,sum(a.jumlah) jumlahx from {anggperkeg} a inner join {obyek} r on mid(a.kodero,1,5)=r.kodeo inner join {kegiatanskpd} k on a.kodekeg=k.kodekeg where k.inaktif=0 and k.kodeuk=\'%s\' and mid(a.kodero,1,3)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datajenis->kodej));
			$fsql .= ' group by r.kodej, r.uraian order by r.kodeo';

			$resultobyek = db_query($fsql);
			if ($resultobyek) {
				while ($dataobyek = db_fetch_object($resultobyek)) {
					$rowsrek[] = array (
										 array('data' => apbd_format_rek_obyek($dataobyek->kodeo),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $dataobyek->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;'),
										 array('data' => apbd_fn($dataobyek->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
										 );
										 
					//REKENING
					$sql = 'select r.kodero,r.uraian,jumlah from {anggperkeg} a inner join {rincianobyek} r on a.kodero=r.kodero inner join {kegiatanskpd} k on a.kodekeg=k.kodekeg where k.inaktif=0 and k.kodeuk=\'%s\' and left(a.kodero,5)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($dataobyek->kodeo));
					$fsql .= ' group by r.kodero,r.uraian order by r.kodero';
					
					//drupal_set_message( $fsql);
					$result= db_query($fsql);
					if ($result) {
						while ($data = db_fetch_object($result)) {
							$rowsrek[] = array (
												 array('data' => apbd_format_rek_rincianobyek($data->kodero),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
												 array('data' => $data->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => apbd_fn($data->jumlah),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
												 );
						
						}
					}												 
				
				}
			}
		}
	}										 
								 			
		

	
	if ($kodek=='51') 
		$strbelanja = 'TIDAK ';	
	$rowsrek[] = array (
						 array('data' => 'TOTAL BELANJA ' . $strbelanja . 'LANGSUNG',  'width'=> '435px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($total),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 );	
	
	
	$rowsfooter[] = array (
						 array('data' => 'CATATAN',  'width'=> '335px',  'colspan'=>'2',  'style' => 'text-align:center'),
						 array('data' => 'KEPALA SKPD',  'width' => '200px', 'style' => 'text-align:center;'),
						 );
	$rowsfooter[] = array (
						 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'text-align:center'),
						 array('data' => '',  'width' => '200px', 'style' => 'text-align:right;'),
						 );
	$rowsfooter[] = array (
						 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'text-align:center'),
						 array('data' => $pimpinannama,  'width' => '200px', 'style' => 'text-align:center; text-decoration: underline;'),
						 );
	$rowsfooter[] = array (
						 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'text-align:center'),
						 array('data' => 'NIP. ' . $pimpinannip,  'width' => '200px', 'style' => 'text-align:center;'),
						 );

	$opttb0 = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0`');
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '1');
	$headerkosong = array();

	$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttb0));
	$output .= theme_box('', apbd_theme_table($headerkosong, $rowskegiatan, $opttb0));
	
	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	$output .= theme_box('', apbd_theme_table($headerkosong, $rowsfooter, $opttb0));
	
	$output .= $toutput;
	if ($limit >0)
		$output .= theme ('pager', NULL, $limit, 0);
	
	return $output;
	
	
}

function GenReportFormHeader($print=0) {
	
	$kodeuk = arg(4);
	$kodek = arg(5);
	
	$skpd = '';
	$pimpinannama='';
	$pimpinannip='';
	$pimpinanjabatan='';
	$pquery = sprintf("select uk.kodedinas, uk.namauk, uk.pimpinannama, uk.pimpinannip, uk.pimpinanjabatan, u.kodeu, u.urusan 
				from {unitkerja} uk inner join {ukurusan} uku on uk.kodeuk=uku.kodeuk inner join {urusan} u on uku.kodeu=u.kodeu 
				where uk.kodeuk='%s'", db_escape_string($kodeuk)) ;
	//drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$kodedinas = $data->kodedinas;
		$urusan = $data->kodeu . ' - ' . $data->urusan;
		$skpd = $kodedinas . ' - ' . $data->namauk;
		$pimpinannama=$data->pimpinannama;
		$pimpinannip=$data->pimpinannip;
		$pimpinanjabatan=$data->pimpinanjabatan;
	}


	$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
	$tahun = variable_get('apbdtahun', 0);
	$rows= array();
	//$rowsjudul[] = array (array ('data'=>'RENCANA KERJA DAN ANGGARAN SATUAN KERJA PERANGKAT DAERAH', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
  
	$rowskegiatan[]= array ( 
						 array('data' => 'PEMERINTAH KABUPATEN JEPARA',  'width'=> '200px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'RENCANA KERJA DAN ANGGARAN SATUAN KERJA PERANGKAT DAERAH', 'width' => '260px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => $tahun, 'width' => '75',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 );
	$rowskegiatan[]= array (
						 array('data' => 'Urusan Pemerintahan',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $urusan, 'width' => '370px',  'style' => 'border-right: 1px solid black; text-align:left;'),
						 );
	$rowskegiatan[]= array (
						 array('data' => 'Organisasi',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => ':',  'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $skpd,  'width' => '370px',  'style' => 'border-right: 1px solid black; text-align:left;'),					 
						);
	if ($kodek=='51') 
		$strbelanja = 'TIDAK ';
	$rowskegiatan[]= array (
						 array('data' => 'REKAPITULASI ANGGARAN BELANJA ' . $strbelanja . 'LANGSUNG SKPD',  'width'=> '535px', 'colspan'=>'3', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
						 );
						

	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	$output = theme_box('', apbd_theme_table($headerkosong, $rowskegiatan, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormContent($kodeuk, $kodek) {
	
	$headersrek[] = array (
						 //array('data' => 'Kode',  'width'=> '60px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 
						 array('data' => 'Kode',  'width'=> '60px', 'style' => 'border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Uraian',  'width' => '375px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Jumlah',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );

	$total=0;

	//JENIS
	$sql = 'select r.kodej,r.uraian,sum(a.jumlah) jumlahx from {anggperkeg} a inner join {jenis} r on mid(a.kodero,1,3)=r.kodej inner join {kegiatanskpd} k on a.kodekeg=k.kodekeg where k.jenis=2 and k.inaktif=0 and k.kodeuk=\'%s\' and mid(a.kodero,1,2)=\'%s\'';
	$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($kodek));
	$fsql .= ' group by r.kodej,r.uraian order by r.kodej';
	
	//drupal_set_message( $fsql);
	$resultjenis = db_query($fsql);
	if ($resultjenis) {
		while ($datajenis = db_fetch_object($resultjenis)) {
			$total += $datajenis->jumlahx;
			$rowsrek[] = array (
								 array('data' => ($datajenis->kodej),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $datajenis->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => apbd_fn($datajenis->jumlahx),  'width' => '100px', 'style' => 'border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
								 );

			//OBYEK
			$sql = 'select r.kodeo,r.uraian,sum(a.jumlah) jumlahx from {anggperkeg} a inner join {obyek} r on mid(a.kodero,1,5)=r.kodeo inner join {kegiatanskpd} k on a.kodekeg=k.kodekeg where k.jenis=2 and k.inaktif=0 and k.kodeuk=\'%s\' and mid(a.kodero,1,3)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datajenis->kodej));
			$fsql .= ' group by r.kodej, r.uraian order by r.kodeo';
			
			$resultobyek = db_query($fsql);
			if ($resultobyek) {
				while ($dataobyek = db_fetch_object($resultobyek)) {
					$rowsrek[] = array (
										 array('data' => apbd_format_rek_obyek($dataobyek->kodeo),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $dataobyek->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;'),
										 array('data' => apbd_fn($dataobyek->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
										 );
										 
					//REKENING
					$sql = 'select r.kodero,r.uraian,jumlah from {anggperkeg} a inner join {rincianobyek} r on a.kodero=r.kodero  inner join {kegiatanskpd} k on a.kodekeg=k.kodekeg where k.jenis=2 and k.inaktif=0 and k.kodeuk=\'%s\' and left(a.kodero,5)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($dataobyek->kodeo));
					$fsql .= ' group by r.kodero,r.uraian order by r.kodero';
					
					//drupal_set_message( $fsql);
					$result= db_query($fsql);
					if ($result) {
						while ($data = db_fetch_object($result)) {
							$rowsrek[] = array (
												 array('data' => apbd_format_rek_rincianobyek($data->kodero),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
												 array('data' => $data->uraian,  'width' => '375px', 'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => apbd_fn($data->jumlah),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
												 );
						
						}
					}												 
				
				}
			}
		}
	}										 
								 

	
	
	if ($kodek=='51') 
		$strbelanja = 'TIDAK ';	
	
	$rowsrek[] = array (
						 array('data' => 'TOTAL BELANJA ' . $strbelanja . 'LANGSUNG',  'width'=> '435px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($total),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 );	

	
						 
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormFooter($kodeuk) {
	
	$namauk = '';
	$pimpinannama='';
	$pimpinannip='';
	$pimpinanjabatan='';
	$pquery = sprintf("select kodedinas, namauk, pimpinannama, pimpinannip, pimpinanjabatan 
				from {unitkerja} where kodeuk='%s'", db_escape_string($kodeuk)) ;
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		
		$namauk = $data->namauk;
		$pimpinannama=$data->pimpinannama;
		$pimpinannip=$data->pimpinannip;
		$pimpinanjabatan=$data->pimpinanjabatan;
	}

	$rowsfooter[] = array (
						 array('data' => 'CATATAN',  'width'=> '335px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:center'),
						 array('data' => 'KEPALA SKPD',  'width' => '200px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 );
	$rowsfooter[] = array (
						 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),
						 array('data' => '',  'width' => '200px', 'style' => 'border-right: 1px solid black; text-align:right;'),
						 );
	$rowsfooter[] = array (
						 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),
						 array('data' => '',  'width' => '200px', 'style' => 'border-right: 1px solid black; text-align:right;'),
						 );
	$rowsfooter[] = array (
						 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),
						 array('data' => $pimpinannama,  'width' => '200px', 'style' => 'border-right: 1px solid black; text-align:center; text-decoration: underline;'),
						 );
	$rowsfooter[] = array (
						 array('data' => '',  'width'=> '335px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:center'),
						 array('data' => 'NIP. ' . $pimpinannip,  'width' => '200px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 );
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	$output = theme_box('', apbd_theme_table($headerkosong, $rowsfooter, $opttbl));
	
	$output .= $toutput;
	return $output;
	
}

function rekapaggbl_form () {
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Paramater Laporan dan Printer',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);

	$kodeuk = arg(4);
	$kodek = arg(5);
	$topmargin = arg(6);
	$exportpdf = arg(7);
	if ($topmargin=='') $topmargin=10;

	if (!isSuperuser()) {
		$type = 'hidden';
		$kodeuk = apbd_getuseruk();
		
	} else {
		$pquery = "select kodedinas, kodeuk, namasingkat, namauk from {unitkerja} where aktif=1 order by kodedinas" ;
		$pres = db_query($pquery);
		$dinas = array();        
		
		//$dinas['00'] ='00000 - SEMUA SKPD';
		while ($data = db_fetch_object($pres)) {
			$dinas[$data->kodeuk] = $data->kodedinas . ' - ' . $data->namasingkat;
		}
		$type='select';
	}
	 
	$form['formdata']['kodeuk']= array(
		'#type'         => $type, 
		'#title'        => 'SKPD',
		'#options'	=> $dinas,
		//'#description'  => 'kodeuktujuan', 
		//'#maxlength'    => 60, 
		'#width'         => 30, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeuk, 
	);
	
	$form['formdata']['kodek']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $kodek, 
	);
	
	$form['formdata']['topmargin']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Margin Atas', 
		'#attributes'	=> array('style' => 'text-align: right'),
		'#maxlength'    => 10, 
		'#size'         => 20, 
		//'#required'     => !$disabled, 
		'#disabled'     => false, 
		'#default_value'=> $topmargin, 
	);
	$form['formdata']['tampilkan'] = array (
		'#type' => 'submit',
		'#value' => 'Tampilkan',
	);
	$form['formdata']['submit'] = array (
		'#type' => 'submit',
		'#value' => 'Cetak'
	);
	
	return $form;
}
function rekapaggbl_form_submit($form, &$form_state) {
	//$kodeuk = $form_state['values']['kodeuk'];
	$kodeuk = $form_state['values']['kodeuk'];
	$kodek = $form_state['values']['kodek'];
	$topmargin = $form_state['values']['topmargin'];
	
	if($form_state['clicked_button']['#value'] == $form_state['values']['tampilkan'])
		$uri = 'apbd/laporanpenetapan/rka/rekapaggbl/' . $kodeuk . '/'. $kodek . '/'. $topmargin . '/' ;
	else
		$uri = 'apbd/laporanpenetapan/rka/rekapaggbl/' . $kodeuk . '/'. $kodek . '/'. $topmargin . '/pdf' ;
	drupal_goto($uri);
	
}
?>