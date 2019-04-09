<?php
function pembiayaan_print_main() {
$h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
$h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
drupal_set_html_head($h);
drupal_add_css('files/css/kegiatancam.css');
	
	$topmargin = '20';
	$kodek = arg(3);
	$tipedok = arg(4);
	
	//drupal_set_message($tipedok);
	
	$exportpdf = arg(6);

	if (isset($topmargin)) $topmargin = arg(5);
	if ($tipedok=='') $tipedok = 'rka';

	drupal_set_title(strtoupper($tipedok) . '-PPKD Pembiayaan');
	
	//drupal_set_message($topmargin);
	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		//require_once('test.php');
		//myt();
		
		$pdfFile = 'rka-skpd-pembiayaan-' . $kodek . '.pdf';

		//$htmlContent = GenReportForm(1);
		//apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);

		$htmlHeader = GenReportFormHeader($tipedok);
		$htmlContent = GenReportFormContent($kodek);
		$htmlFooter = GenReportFormFooter($tipedok);
		
		apbd_ExportPDF3($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, $pdfFile);
		
	} else {
		$url = 'apbd/pembiayaan/print/'. $kodek . '/' . $topmargin . "/pdf";
		$output = drupal_get_form('pembiayaan_print_form');
		//$output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
		$output .= GenReportFormContent($kodek);
		return $output;
	}

}

function GenReportFormHeader($tipedok) {
	
	$kodek = arg(3);

	$skpd = '';
	$pimpinannama='';
	$pimpinannip='';
	$pimpinanjabatan='BENDAHARA UMUM DAERAH';
	$kodeuk = '81';
	$pquery = sprintf("select uk.kodedinas, uk.namauk, uk.pimpinannama, uk.pimpinannip, uk.pimpinanjabatan, u.kodeu, u.urusan 
				from {unitkerja} uk inner join {urusan} u on uk.kodeu=u.kodeu 
				where uk.kodeuk='%s'", db_escape_string($kodeuk)) ;
	//drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$kodedinas = $data->kodedinas;
		$urusan = $data->kodeu . ' - ' . $data->urusan;
		$skpd = '40400 - PEJABAT PENGELOLA KEUANGAN DAERAH';
		$pimpinannama=$data->pimpinannama;
		$pimpinannip=$data->pimpinannip;
		//$pimpinanjabatan=$data->pimpinanjabatan;
	}

	$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
	$tahun = variable_get('apbdtahun', 0);
	
	$rows= array();
	//$rowsjudul[] = array (array ('data'=>'RENCANA KERJA DAN ANGGARAN SATUAN KERJA PERANGKAT DAERAH', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
  
	/*
	$rowskegiatan[]= array ( 
						 array('data' => 'PEMERINTAH KABUPATEN JEPARA',  'width'=> '250px', 'colspan'=>'3', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
						 array('data' => 'RENCANA KERJA DAN ANGGARAN SATUAN KERJA PERANGKAT DAERAH', 'width' => '500px', 'colspan'=>'3', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
						 array('data' => $tahun, 'width' => '125',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
						 );
	*/
	
	if ($tipedok=='rka')
		$judul = 'RENCANA KERJA DAN ANGGARAN';
	else
		$judul = 'DOKUMEN PELAKSANAAN ANGGARAN';
	
	$rowskegiatan[]= array ( 
						 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
						 array('data' => $judul, 'width' => '310px', 'colspan'=>'3', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
						 array('data' => 'RINCIAN ANGGARAN', 'width' => '300px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
						 array('data' => 'FORMULIR', 'width' => '175',  'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1em; text-align:center;'),
						 );
	
	if ($kodek=='61') {
		$strjenis = 'PENERIMAAN ';
		$strkode = '.1';
	} elseif ($kodek=='62') {
		$strjenis = 'PENGELUARAN ';
		$strkode = '.2';
	}
	$rowskegiatan[]= array ( 
						 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
						 array('data' => 'PEJABAT PENGELOLA KEUANGAN DAERAH', 'width' => '310px', 'colspan'=>'3', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
						 array('data' => $strjenis . 'PEMBIAYAAN', 'width' => '300px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
						 array('data' => strtoupper($tipedok) . '-PPKD 3' . $strkode, 'width' => '175',  'style' => 'border-right: 1px solid black; font-size:1em; text-align:center;'),
						 );
	$rowskegiatan[]= array ( 
						 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
						 array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '310px', 'colspan'=>'3', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; font-size:1.3em; text-align:center;'),
						 array('data' => '', 'width' => '300px', 'colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
						 array('data' => 'TAHUN ' . $tahun, 'width' => '175',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size:1em; text-align:center;'),	
						 );	
	$rowskegiatan[]= array (
						 array('data' => 'Urusan Pemerintahan',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $urusan, 'width' => '710', 'colspan'=>'5',  'style' => 'border-right: 1px solid black; text-align:left;'),
						 );
	$rowskegiatan[]= array (
						 array('data' => 'Organisasi',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => ':',  'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $skpd,  'width' => '710', 'colspan'=>'5',  'style' => 'border-right: 1px solid black; text-align:left;'),					 
						);

	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	$output = theme_box('', apbd_theme_table($headerkosong, $rowskegiatan, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormContent($kodek) {
	
	$total=0;
	$headersrek[] = array (
						 array('data' => 'KODE',  'width'=> '75px', 'rowspan'=>'2','style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 
						 array('data' => 'URAIAN',  'width' => '400x','rowspan'=>'2','colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'RINCIAN PERHITUNGAN', 'width' => '300px','colspan'=>'3','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'JUMLAH TOTAL',  'width' => '100px', 'rowspan'=>'2','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 );
	$headersrek[] = array (

						 array('data' => 'Satuan', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'Volume', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'Harga Satuan',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 );

	//JENIS
	//PERUBAHAN	

	
	if ($kodek !='')
		$where = sprintf(' where j.kodek=\'%s\'', $kodek);
	
	$sql = 'select mid(k.kodero,1,3) kodej,j.uraian,sum(jumlah) jumlahx from {anggperda} k  left join {jenis} j on mid(k.kodero,1,3)=j.kodej ' . $where;
	$fsql = $sql;
	$fsql .= ' group by mid(k.kodero,1,3),j.uraian order by mid(k.kodero,1,3)';
	
	//drupal_set_message( $fsql);
	$resultjenis = db_query($fsql);
	$pertama = true;
	$penerimaansudah = false;
	if ($resultjenis) {
		while ($datajenis = db_fetch_object($resultjenis)) {
			
			$in=0;
			if ($pertama == false) {
				if ($tagjenis != substr($datajenis->kodej, 0, 2)) {
					//Draw Here
					$rowsrek[] = array (
						 array('data' => 'TOTAL PEMBIAYAAN PENERIMAAN',  'width'=> '475x',  'colspan'=>'3',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => '', 'width' => '100px', 'style' => '  border-bottom: 1px solid black;border-top: 1px solid black;text-align:center;'),
						 array('data' => '', 'width' => '100px', 'style' => ' border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;'),
						 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;'),
						 array('data' => apbd_fn($total),  'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:right;'),


						 );
					$total = 0;$totalp = 0;$in++;
					//$totalp = 0;
					$penerimaansudah = true;

				}
			}			
			
			$total += $datajenis->jumlahx;
			
			$pertama = false;
			$tagjenis = substr($datajenis->kodej, 0, 2);$totalp += $jenis[$in];
			$rowsrek[] = array (
										 array('data' => ($datajenis->kodej),  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datajenis->uraian,  'width' => '400x', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;'),
										 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
										 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
										 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
										 array('data' => apbd_fn($datajenis->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),


										 );
			
			    
			//OBYEK

			
			//.............................
			$sql = 'select mid(k.kodero,1,5) kodeo,o.uraian,sum(jumlah) jumlahx from {anggperda} k  left join {obyek} o on mid(k.kodero,1,5)=o.kodeo where mid(k.kodero,1,3)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($datajenis->kodej));
			$fsql .= ' group by mid(k.kodero,1,5),o.uraian order by mid(k.kodero,1,5)';
			
			//drupal_set_message( $fsql);
			$resultobyek = db_query($fsql);
			if ($resultobyek) {$ino=0;
				while ($dataobyek = db_fetch_object($resultobyek)) {
					$rowsrek[] = array (
												 array('data' => apbd_format_rek_obyek($dataobyek->kodeo),  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
												 array('data' => $dataobyek->uraian,  'width' => '400x', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
												 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
												 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
												 array('data' => apbd_fn($dataobyek->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),


												 );	
	

					//REKENING

					
					//.............................
					$sql = 'select kodero,uraian,jumlah from {anggperda} k where mid(k.kodero,1,5)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($dataobyek->kodeo));
					
					//drupal_set_message( $fsql);
					$fsql .= ' order by k.kodero';
					$result = db_query($fsql);
					if ($result) {$inr=0;
						while ($data = db_fetch_object($result)) {
						$rowsrek[] = array (
														 array('data' => apbd_format_rek_rincianobyek($data->kodero),  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
														 array('data' => $data->uraian,  'width' => '400x', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;'),
														 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
														 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
														 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
														 array('data' => apbd_fn($data->jumlah),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),


													 );
						
							//DETIL
							//................
							$sql = 'select uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan,harga,total from {anggperdadetil} where kodero=\'%s\' order by iddetil';
							$fsql = sprintf($sql, db_escape_string($data->kodero));
							//drupal_set_message($fsql);
							
							$resultdetil = db_query($fsql);
							if ($resultdetil) {$in=0;
								while ($datadetil = db_fetch_object($resultdetil)) {
									$rowsrek[] = array (
																 array('data' => '',  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																 array('data' => '- ' . $datadetil->uraian,  'width' => '400x', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;font-style: italic;'),
																 array('data' => $datadetil->unitjumlah . ' ' . $datadetil->unitsatuan, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
																 array('data' => $datadetil->volumjumlah . ' ' . $datadetil->volumsatuan, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datadetil->harga),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
																 array('data' => apbd_fn($datadetil->total),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),

																 );
									
								}
							}
						}
					}
										 
				////////
				}
			}
		}
	}

	if ($kodek == '')
		if ($penerimaansudah)
			$strtotal = 'PENGELUARAN';
		else
			$strtotal = 'PENERIMAAN';
	else
		if ($kodek=='62')
			$strtotal = 'PENGELUARAN';
		else
			$strtotal = 'PENERIMAAN';
		
	$rowsrek[] = array (
						 array('data' => 'TOTAL PEMBIAYAAN ' . $strtotal,  'width'=> '475x',  'colspan'=>'3',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => '', 'width' => '100px', 'style' => '  border-bottom: 1px solid black;border-top: 1px solid black;text-align:center;'),
						 array('data' => '', 'width' => '100px', 'style' => ' border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;'),
						 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;'),
						 array('data' => apbd_fn($total),  'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:right;'),

						 );
		
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}
 

function GenReportFormFooter($tipedok) {
	$kodek = arg(3);

	$namauk = '';
	$pimpinannama='';
	$pimpinannip='';
	$pimpinanjabatan='';
	
	if ($tipedok=='dpa') {

		$where = ' where left(k.kodero,2)=\'%s\'';
		$sql = 'select sum(k.jumlah) jumlahx from {anggperda} k inner join {rincianobyek} r on k.kodero=r.kodero ' . $where;
		$fsql = sprintf($sql, $kodek);
		$pres = db_query($fsql);
		if ($data = db_fetch_object($pres)) {
			
			$total = $data->jumlahx;
			$tw = round(($data->jumlahx/1000)/4,0);
		}
		
		$tw = $tw * 1000;
		
		$tw1 = $tw;
		$tw2 = $tw;
		$tw3 = $tw;
		$tw4 = $total - (3*$tw);		
		
		$namauk = 'PEJABAT PENGELOLA KEUANGAN DAERAH';

		$pquery = sprintf("select dpatgl, budnama, budnip, budjabatan from {setupapp} where tahun='%s'", variable_get('apbdtahun', 0)) ;
		////drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$pimpinannama = $data->budnama;
			$pimpinannip = $data->budnip;
			$pimpinanjabatan = $data->budjabatan;
			$dpatgl = $data->dpatgl;
		}
		
		$rowsfooter[] = array (
							 array('data' => 'RENCANA TRIWULAN',  'width'=> '475px',  'colspan'=>'5',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black;border-right: 1px solid black; text-align:center'),
							 array('data' => '',  'width'=> '100px',   'style' => 'text-align:center'),
							 array('data' => 'Jepara, ' . $dpatgl,  'width' => '300px', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'TRIWULAN',  'width'=> '275px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black;border-bottom: 1px solid black; text-align:center'),
							 array('data' => 'JUMLAH',  'width'=> '100px',   'style' => 'border-left: 1px solid black;border-bottom: 1px solid black; text-align:center'),
							 array('data' => 'KETERANGAN',  'width'=> '100px',   'style' => 'border-left: 1px solid black;border-bottom: 1px solid black; border-right: 1px solid black; text-align:center'),

							 array('data' => '',  'width'=> '100px',   'style' => 'text-align:center'),
							 array('data' => 'Mengesahkan,',  'width' => '300px', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'TRIWULAN I',  'width'=> '275px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black;  text-align:left'),
							 array('data' => apbd_fn($tw1),  'width'=> '100px',   'style' => 'border-left: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '100px',   'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),

							 array('data' => '',  'width'=> '100px',   'style' => 'text-align:center'),
							 array('data' => $pimpinanjabatan,  'width' => '300px', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'TRIWULAN II',  'width'=> '275px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black;  text-align:left'),
							 array('data' => apbd_fn($tw2),  'width'=> '100px',   'style' => 'border-left: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '100px',   'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),

							 array('data' => '',  'width'=> '100px',   'style' => 'text-align:center'),
							 array('data' => '',  'width' => '300px', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'TRIWULAN III',  'width'=> '275px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black;  text-align:left'),
							 array('data' => apbd_fn($tw3),  'width'=> '100px',   'style' => 'border-left: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '100px',   'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),

							 array('data' => '',  'width'=> '100px',   'style' => 'text-align:center'),
							 array('data' => '',  'width' => '300px', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'TRIWULAN IV',  'width'=> '275px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black;  text-align:left'),
							 array('data' => apbd_fn($tw4),  'width'=> '100px',   'style' => 'border-left: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '100px',   'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),

							 array('data' => '',  'width'=> '100px',   'style' => 'text-align:center'),
							 array('data' => $pimpinannama,  'width' => '300px', 'style' => 'border-right: 1px solid black; text-align:center;text-decoration: underline;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '275px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; text-align:left'),
							 array('data' => '',  'width'=> '100px',   'style' => 'border-left: 1px solid black; border-bottom: 1px solid black;text-align:right'),
							 array('data' => '',  'width'=> '100px',   'style' => 'border-left: 1px solid black; border-bottom: 1px solid black;border-right: 1px solid black; text-align:center'),

							 array('data' => '',  'width'=> '100px',   'style' => 'border-bottom: 1px solid black;text-align:center'),
							 array('data' => 'NIP. ' . $pimpinannip,  'width' => '300px', 'style' => 'border-right: 1px solid black; border-bottom: 1px solid black;text-align:center;'),
							 );

		
	} else {
		$namauk = '';
		$pimpinannama='';
		$pimpinannip='';
		$pimpinanjabatan='BENDAHARA UMUM DAERAH';
		$kodeuk = '81';
		$pquery = sprintf("select kodedinas, namauk, pimpinannama, pimpinannip, pimpinanjabatan 
					from {unitkerja} where kodeuk='%s'", db_escape_string($kodeuk)) ;
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			
			$namauk = $data->namauk;
			$pimpinannama=$data->pimpinannama;
			$pimpinannip=$data->pimpinannip;
			//$pimpinanjabatan=$data->pimpinanjabatan;
		}

		$rowsfooter[] = array (
							 array('data' => 'CATATAN',  'width'=> '675px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => 'Pejabat Pengelola Keuangan Daerah',  'width' => '200px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '675px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => '',  'width' => '200px', 'style' => 'border-right: 1px solid black; text-align:right;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '675px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => '',  'width' => '200px', 'style' => 'border-right: 1px solid black; text-align:right;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '675px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => $pimpinannama,  'width' => '200px', 'style' => 'border-right: 1px solid black; text-align:center; text-decoration: underline;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '675px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => 'NIP. ' . $pimpinannip,  'width' => '200px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
							 );
	}
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'0', 'border' => '0');
	$headerkosong = array();

	//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	$output = theme_box('', apbd_theme_table($headerkosong, $rowsfooter, $opttbl));
	
	$output .= $toutput;
	return $output;
	
}

function pembiayaan_print_form () {
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Setting Printer',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);
	
	$kodek = arg(3);
	$topmargin = arg(4);

	$kodek = arg(3);
	$tipedok = arg(4);
	$topmargin = arg(5);
	
	if ($topmargin=='') $topmargin = 10;
	if ($tipedok=='') $tipedok = 'rka';	
	
	$form['formdata']['kodek']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $kodek, 
	);
	$form['formdata']['tipedok']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $tipedok, 
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
	$form['formdata']['submit'] = array (
		'#type' => 'submit',
		'#value' => 'Cetak'
	);
	
	return $form;
}
function pembiayaan_print_form_submit($form, &$form_state) {
	$kodek = $form_state['values']['kodek'];
	$topmargin = $form_state['values']['topmargin'];
	$tipedok = $form_state['values']['tipedok'];
	
	$uri = 'apbd/pembiayaan/print/' . $kodek . '/' . $tipedok . '/'. $topmargin . '/pdf' ;
	drupal_goto($uri);
	
}
?>