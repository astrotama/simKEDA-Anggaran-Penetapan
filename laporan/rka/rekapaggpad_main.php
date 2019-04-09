<?php
function rekapaggpad_main() {
$h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
$h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
drupal_set_html_head($h);
drupal_add_css('files/css/kegiatancam.css');
	$kodeuk = arg(4);
	$topmargin = arg(5);
	$exportpdf = arg(6);

	if ($topmargin=='') $topmargin = 10;

	//drupal_set_message($exportpdf);
	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		//require_once('test.php');
		//myt();
		
		$pdfFile = 'rka-skpd-rekapaggpad-' . $kodeuk . '.pdf';
 
		//$htmlContent = GenReportForm(1);
		//apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);

		$htmlHeader = GenReportFormHeader($kodeuk);
		if ($kodeuk=='00')
			$htmlContent = GenReportFormContent();
		else
			$htmlContent = GenReportFormContentRKA($kodeuk);
		$htmlFooter = GenReportFormFooter($kodeuk);
		apbd_ExportPDF3_CF($topmargin,$topmargin, $htmlHeader, $htmlContent,$htmlFooter, false, $pdfFile,1);
		//apbd_ExportPDF3P($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, $pdfFile);
		
	} else {
		$output = drupal_get_form('rekapaggpad_form');
		//$output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
		if ($kodeuk=='00')
			$htmlContent = GenReportFormContent();
		else
			$htmlContent = GenReportFormContentRKA($kodeuk);
		$output .= $htmlContent;
		return $output;
	}

}

function GenReportForm($print=0) {
	
	$kodeuk = arg(5);

	$skpd = '';
	$pimpinannama='';
	$pimpinannip='';
	$pimpinanjabatan='';
	if ($kodeuk!='00') {
		$pquery = sprintf("select uk.kodedinas, uk.namauk, uk.pimpinannama, uk.pimpinannip, uk.pimpinanjabatan, u.kodeu, u.urusan 
					from {unitkerja} uk inner join {ukurusan} uku on uk.kodeuk=uku.kodeuk inner join {urusan} u on uku.kodeu=u.kodeu 
					where uk.kodeuk='%s'", db_escape_string($kodeuk)) ;
	} else {
		$pquery = sprintf("select '00000' kodedinas, 'KABUPATEN JEPARA' namauk, pimpinannama, pimpinannip, 'BENDAHARA UMUM DAERAH' pimpinanjabatan, '000' kodeu, 'SEMUA URUSAN' urusan 
					from {unitkerja} where kodeuk='%s'", db_escape_string('81')) ;
	}	//drupal_set_message($pquery);
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



	$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
	$rows= array();
	$rowsjudul[] = array (array ('data'=>'REKAPITULASI ANGGARAN PENDAPATAN', 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	
 
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
						 array('data' => 'KODE',  'width'=> '60px','rowspan'=>'2', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
						 array('data' => 'URAIAN', 'width' => '465px','rowspan'=>'2',  'colspan'=>'2',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
						 array('data' => 'JUMLAH (Rp)',  'width' => '200px','colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
						 array('data' => 'BERTAMBAH/ BERKURANG',  'width' => '150px','colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
						 );
	$headersrek[] = array (
						 array('data' => 'Sebelum Perubahan',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'Setelah Perubahan',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'Rupiah',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'Persen',  'width' => '50px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),

						 );

	$where = ' where k.kodeuk=\'%s\'';
	
	$total=0;
	//KELOMPOK
	if ($kodeuk!='00') {
		$sql = 'select mid(k.kodero,1,2) kodek,x.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperuk} k left join {kelompok} x on mid(k.kodero,1,2)=x.kodek where kodeuk=\'%s\' ';
		$fsql = sprintf($sql, db_escape_string($kodeuk));
		$fsql .= ' group by mid(k.kodero,1,2),x.uraian order by mid(k.kodero,1,2)';
	} else {
		$sql = 'select mid(k.kodero,1,2) kodek,x.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperuk} k left join {kelompok} x on mid(k.kodero,1,2)=x.kodek ';
		$fsql = $sql;
		$fsql .= ' group by mid(k.kodero,1,2),x.uraian order by mid(k.kodero,1,2)';
	}	
	//drupal_set_message( $fsql);
	$resultkel = db_query($fsql);
	if ($resultkel) {
		while ($datakel = db_fetch_object($resultkel)) {
			//$total += $datakel->jumlahx;
			$total= $datakel->jumlahx;
			$totalp += ($datakel->jumlahxp - $datakel->jumlahx);
			
			$rowsrek[] = array (
								 array('data' => $datakel->kodek,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $datakel->uraian, 'width' => '465px', 'colspan'=>'2',  'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($datakel->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($datakel->jumlahxp - $datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn1(apbd_hitungpersen($datakel->jumlahx,$datakel->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
								 );


			//JENIS
			if ($kodeuk!='00') {
				$sql = 'select mid(k.kodero,1,3) kodej,j.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperuk} k left join {jenis} j on mid(k.kodero,1,3)=j.kodej where kodeuk=\'%s\' and mid(k.kodero,1,2)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datakel->kodek));
				$fsql .= ' group by mid(k.kodero,1,3),j.uraian order by mid(k.kodero,1,3)';
			} else {
				$sql = 'select mid(k.kodero,1,3) kodej,j.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperuk} k left join {jenis} j on mid(k.kodero,1,3)=j.kodej where mid(k.kodero,1,2)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($datakel->kodek));
				$fsql .= ' group by mid(k.kodero,1,3),j.uraian order by mid(k.kodero,1,3)';				
			}

			//drupal_set_message( $fsql);
			$resultjenis = db_query($fsql);
			if ($resultjenis) {
				while ($datajenis = db_fetch_object($resultjenis)) {
					
					$rowsrek[] = array (
									 array('data' => ($datajenis->kodej),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
									 array('data' => $datajenis->uraian, 'width' => '465px', 'colspan'=>'2',  'style' => ' border-right: 1px solid black; text-align:left;'),
									 array('data' => apbd_fn($datajenis->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;' . $bold),
									 array('data' => apbd_fn($datajenis->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;' . $bold),
									 array('data' => apbd_fn($datajenis->jumlahxp - $datajenis->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;' . $bold),
									 array('data' => apbd_fn1(apbd_hitungpersen($datajenis->jumlahx, $datajenis->jumlahxp)),  'style' => ' border-right: 1px solid black; text-align:right;' . $bold),
									 );

					//OBYEK
					if ($kodeuk!='00') {
						$sql = 'select mid(k.kodero,1,5) kodeo,j.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperuk} k left join {obyek} j on mid(k.kodero,1,5)=j.kodeo where kodeuk=\'%s\' and mid(k.kodero,1,3)=\'%s\'';
						$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datajenis->kodej));
						$fsql .= ' group by mid(k.kodero,1,5),j.uraian order by j.kodeo';
					} else {
						$sql = 'select mid(k.kodero,1,5) kodeo,j.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperuk} k left join {obyek} j on mid(k.kodero,1,5)=j.kodeo where mid(k.kodero,1,3)=\'%s\'';
						$fsql = sprintf($sql, db_escape_string($datajenis->kodej));
						$fsql .= ' group by mid(k.kodero,1,5),j.uraian order by j.kodeo';
					}
					
					$resultobyek = db_query($fsql);
					if ($resultobyek) {
						while ($dataobyek = db_fetch_object($resultobyek)) {
							
							
							$rowsrek[] = array (
										 array('data' => apbd_format_rek_obyek($dataobyek->kodeo),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $dataobyek->uraian, 'width' => '465px', 'colspan'=>'2',  'style' => ' border-right: 1px solid black; text-align:left;'),
										 array('data' => apbd_fn($dataobyek->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
										 array('data' => apbd_fn($dataobyek->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
										 array('data' => apbd_fn($dataobyek->jumlahxp - $dataobyek->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
										 array('data' => apbd_fn1(apbd_hitungpersen($dataobyek->jumlahx, $dataobyek->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; text-align:right;'),
										 );		 
												 
							//REKENING
							if ($kodeuk!='00') {
								$sql = 'select k.kodero,r.uraian,jumlah,jumlahp from {anggperuk} k left join {rincianobyek} r on k.kodero=r.kodero where k.kodeuk=\'%s\' and left(k.kodero,5)=\'%s\'';
								$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($dataobyek->kodeo));
								$fsql .= ' group by k.kodero,r.uraian order by k.kodero';
							} else {
								$sql = 'select k.kodero,r.uraian,jumlah,jumlahp from {anggperuk} k left join {rincianobyek} r on k.kodero=r.kodero where left(k.kodero,5)=\'%s\'';
								$fsql = sprintf($sql, db_escape_string($dataobyek->kodeo));
								$fsql .= ' group by k.kodero,r.uraian order by k.kodero';
								
							}
							
							//drupal_set_message( $fsql);
							$result= db_query($fsql);
							if ($result) {
								while ($data = db_fetch_object($result)) {
									
									
									$rowsrek[] = array (
													 array('data' => apbd_format_rek_rincianobyek($data->kodero),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
													 array('data' => $data->uraian, 'width' => '465px', 'colspan'=>'2',  'style' => ' border-right: 1px solid black; text-align:left;font-style: italic;'),
													 array('data' => apbd_fn($data->jumlah),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
													 array('data' => apbd_fn($data->jumlahp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
													 array('data' => apbd_fn($data->jumlahp - $data->jumlah),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
													 array('data' => apbd_fn1(apbd_hitungpersen($data->jumlah, $data->jumlahp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
													 
													 );	
								
								}
							}												 
						
						}
					}
				}
			}										 
								 
		////////
		}
	}				
		

	
	
	$rowsrek[] = array (
						 array('data' => 'TOTAL PENDAPATAN',  'width'=> '525px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($total),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($totalp),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($totalp - $total),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn1(apbd_hitungpersen($total, $totalp)),  'width' => '50px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 );	
	
	
	
		$rowsfooter[] = array (
							 array('data' => 'CATATAN',  'width'=> '375px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => 'KEPALA SKPD',  'width' => '200px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '675px',  'colspan'=>'2',  'style' => ''),
							 array('data' => '',  'width' => '200px', 'style' => ''),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '675px',  'colspan'=>'2',  'style' => ' text-align:center'),
							 array('data' => '',  'width' => '200px', 'style' => 'text-align:right;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '675px',  'colspan'=>'2',  'style' => ' text-align:center'),
							 array('data' => $pimpinannama,  'width' => '200px', 'style' => 'text-align:center; text-decoration: underline;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '675px',  'colspan'=>'2',  'style' => 'text-align:center'),
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

function GenReportFormHeader($kodeuk) {
	
	$skpd = '';
	$pimpinannama='';
	$pimpinannip='';
	$pimpinanjabatan='';
	if ($kodeuk!='00') {
		$pquery = sprintf("select uk.kodedinas, uk.namauk, uk.pimpinannama, uk.pimpinannip, uk.pimpinanjabatan, u.kodeu, u.urusan 
					from {unitkerja} uk inner join {urusan} u on uk.kodeu=u.kodeu 
					where uk.kodeuk='%s'", db_escape_string($kodeuk)) ;
	} else {
		$pquery = sprintf("select '00000' kodedinas, 'KABUPATEN JEPARA' namauk, pimpinannama, pimpinannip, 'BENDAHARA UMUM DAERAH' pimpinanjabatan, '000' kodeu, 'SEMUA URUSAN' urusan 
					from {unitkerja} where kodeuk='%s'", db_escape_string('81')) ;
	}
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
	//$rowsjudul[] = array (array ('data'=>'RENCANA KERJA DAN ANGGARAN SATUAN KERJA PERANGKAT DAERAH', 'width'=>'875px', 'colspan'=>'6', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
  
	$rowskegiatan[]= array ( 
						 array('data' => 'P E M E R I N T A H',  'width'=> '350px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'RENCANA KERJA DAN ANGGARAN', 'width' => '350px','colspan'=>'6', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'TAHUN', 'width' => '125',  'style' => 'border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 );
	$rowskegiatan[]= array ( 
						 array('data' => 'K A B U P A T E N   J E P A R A',  'width'=> '350px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'SATUAN KERJA PERANGKAT DAERAH', 'width' => '350px','colspan'=>'6', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => $tahun, 'width' => '125',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 );
	$rowskegiatan[]= array (
					 array('data' => 'Urusan Pemerintahan',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
					 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
					 array('data' => $urusan, 'width' => '660px',  'colspan'=>'6', 'style' => 'border-right: 1px solid black; text-align:left;'),
					 );
	$rowskegiatan[]= array (
					 array('data' => 'Organisasi',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
					 array('data' => ':',  'width' => '15px', 'style' => 'text-align:right;'),
					 array('data' => $skpd,  'width' => '660px', 'colspan'=>'6', 'style' => 'border-right: 1px solid black; text-align:left;'),					 
					);
	
	$rowskegiatan[]= array (
					 array('data' => 'REKAPITULASI ANGGARAN PENDAPATAN SKPD',  'width'=> '825px', 'colspan'=>'8', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
					 );	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	$output = theme_box('', apbd_theme_table($headerkosong, $rowskegiatan, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormContent() {
	
	$headersrek[] = array (
						 array('data' => 'KODE',  'width'=> '60px','style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
						 array('data' => 'URAIAN', 'width' => '640px', 'colspan'=>'6',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
						 array('data' => 'JUMLAH (Rp)',  'width' => '125px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
						 );

	$where = ' where k.kodeuk=\'%s\'';
	
	$total=0;
	$sql = 'select mid(k.kodero,1,2) kodek,x.uraian,sum(jumlah) jumlahx from {anggperuk} k left join {kelompok} x on mid(k.kodero,1,2)=x.kodek ';
	$fsql = $sql;
	$fsql .= ' group by mid(k.kodero,1,2),x.uraian order by mid(k.kodero,1,2)';

	//drupal_set_message( $fsql);
	$resultkel = db_query($fsql);
	if ($resultkel) {
		while ($datakel = db_fetch_object($resultkel)) {
			//$total += $datakel->jumlahx;
			$total += $datakel->jumlahx;
			
			$rowsrek[] = array (
								 array('data' => $datakel->kodek,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $datakel->uraian, 'width' => '640px', 'colspan'=>'6',  'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => apbd_fn($datakel->jumlahx),  'width' => '125px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
								 );


			//JENIS
			$sql = 'select mid(k.kodero,1,3) kodej,j.uraian,sum(jumlah) jumlahx from {anggperuk} k left join {jenis} j on mid(k.kodero,1,3)=j.kodej where mid(k.kodero,1,2)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($datakel->kodek));
			$fsql .= ' group by mid(k.kodero,1,3),j.uraian order by mid(k.kodero,1,3)';				

			//drupal_set_message( $fsql);
			$resultjenis = db_query($fsql);
			if ($resultjenis) {
				while ($datajenis = db_fetch_object($resultjenis)) {
					
					$rowsrek[] = array (
									 array('data' => ($datajenis->kodej),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
									 array('data' => $datajenis->uraian, 'width' => '640px', 'colspan'=>'6',  'style' => ' border-right: 1px solid black; text-align:left;'),
									 array('data' => apbd_fn($datajenis->jumlahx),  'width' => '125px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),

									 );

					//OBYEK
					$sql = 'select mid(k.kodero,1,5) kodeo,j.uraian,sum(jumlah) jumlahx from {anggperuk} k left join {obyek} j on mid(k.kodero,1,5)=j.kodeo where mid(k.kodero,1,3)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($datajenis->kodej));
					$fsql .= ' group by mid(k.kodero,1,5),j.uraian order by j.kodeo';
				
					$resultobyek = db_query($fsql);
					if ($resultobyek) {
						while ($dataobyek = db_fetch_object($resultobyek)) {
							
							
							$rowsrek[] = array (
										 array('data' => apbd_format_rek_obyek($dataobyek->kodeo),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $dataobyek->uraian, 'width' => '640px', 'colspan'=>'6',  'style' => ' border-right: 1px solid black; text-align:left;'),
										 array('data' => apbd_fn($dataobyek->jumlahx),  'width' => '125px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
										 );		 
												 
							//REKENING
							$sql = 'select k.kodero,r.uraian,sum(jumlah) jumlahx from {anggperuk} k left join {rincianobyek} r on k.kodero=r.kodero where left(k.kodero,5)=\'%s\'';
							$fsql = sprintf($sql, db_escape_string($dataobyek->kodeo));
							$fsql .= ' group by k.kodero,r.uraian order by k.kodero';
								
							
							//drupal_set_message( $fsql);
							$result= db_query($fsql);
							if ($result) {
								while ($data = db_fetch_object($result)) {
									
									//font-style: italic;
									$rowsrek[] = array (
													 array('data' => apbd_format_rek_rincianobyek($data->kodero),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
													 array('data' => $data->uraian, 'width' => '640px', 'colspan'=>'6',  'style' => ' border-right: 1px solid black; text-align:left;'),
													 array('data' => apbd_fn($data->jumlahx),  'width' => '125px', 'style' => ' border-right: 1px solid black; text-align:right;'),
													 );	
													 
									//DETIL SKPD 
									if (substr($data->kodero,0,2) == '41') {
										$no = 0;
										$sql = 'select u.namauk,k.jumlah from {anggperuk} k inner join {unitkerja} u on k.kodeuk=u.kodeuk where k.kodero=\'%s\'';
										$fsql = sprintf($sql, db_escape_string($data->kodero));
										$fsql .= ' order by u.namauk'; 
										//drupal_set_message($fsql);
										$resdetil= db_query($fsql);
										if ($resdetil) {
											while ($datadetil = db_fetch_object($resdetil)) {
												$no++;
												$rowsrek[] = array (
																 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																 array('data' => $no . '.',  'width'=> '20px', 'style' => 'text-align:right;'),
																 array('data' => $datadetil->namauk, 'width' => '620px', 'colspan'=>'5',  'style' => ' border-right: 1px solid black; text-align:left;font-style: italic;'),
																 array('data' => apbd_fn($datadetil->jumlah),  'width' => '125px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
															);												
											}	//end detil skpd		
										}
									}
									
								}	//end rekening
								
							}												 
						
						}	//end obyek
					}
				}
			}										 
								 
		////////
		}
	}	
	
	$rowsrek[] = array (
						 array('data' => 'TOTAL PENDAPATAN',  'width'=> '700px',  'colspan'=>'7',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($total),  'width' => '125px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),

						 );	
						 
	

	
						 
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}


function GenReportFormContentRKA($kodeuk) {
	
	$headersrek[] = array (
						 array('data' => 'KODE',  'width'=> '60px','rowspan'=>'2','style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
						 array('data' => 'URAIAN', 'width' => '375px', 'rowspan'=>'2','colspan'=>'3',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
						 array('data' => 'RINCIAN PERHITUNGAN', 'width' => '280px', 'colspan'=>'3',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
						 array('data' => 'JUMLAH (Rp)',  'width' => '110px', 'rowspan'=>'2','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
						 );
	$headersrek[] = array (
						 array('data' => 'Satuan',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'Volume',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => '@Harga',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),

						 );						 

	$where = ' where k.kodeuk=\'%s\'';
	
	$total=0;
	//KELOMPOK
	$sql = 'select mid(k.kodero,1,2) kodek,x.uraian,sum(jumlah) jumlahx from {anggperuk} k left join {kelompok} x on mid(k.kodero,1,2)=x.kodek where kodeuk=\'%s\' ';
	$fsql = sprintf($sql, db_escape_string($kodeuk));
	$fsql .= ' group by mid(k.kodero,1,2),x.uraian order by mid(k.kodero,1,2)';

	//drupal_set_message( $fsql);
	$resultkel = db_query($fsql);
	if ($resultkel) {
		while ($datakel = db_fetch_object($resultkel)) {
			//$total += $datakel->jumlahx;
			$total += $datakel->jumlahx;
			
			$rowsrek[] = array (
								 array('data' => $datakel->kodek,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $datakel->uraian, 'width' => '375px', 'colspan'=>'3',  'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => '', 'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => '', 'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => apbd_fn($datakel->jumlahx),  'width' => '110px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
								 );


			//JENIS
			$sql = 'select mid(k.kodero,1,3) kodej,j.uraian,sum(jumlah) jumlahx from {anggperuk} k left join {jenis} j on mid(k.kodero,1,3)=j.kodej where kodeuk=\'%s\' and mid(k.kodero,1,2)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datakel->kodek));
			$fsql .= ' group by mid(k.kodero,1,3),j.uraian order by mid(k.kodero,1,3)';

			//drupal_set_message( $fsql);
			$resultjenis = db_query($fsql);
			if ($resultjenis) {
				while ($datajenis = db_fetch_object($resultjenis)) {
					
					$rowsrek[] = array (
									 array('data' => ($datajenis->kodej),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
									 array('data' => $datajenis->uraian, 'width' => '375px', 'colspan'=>'3',  'style' => ' border-right: 1px solid black; text-align:left;'),
									 array('data' => '', 'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
									 array('data' => '', 'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
									 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
									 array('data' => apbd_fn($datajenis->jumlahx),  'width' => '110px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),

									 );

					//OBYEK
					$sql = 'select mid(k.kodero,1,5) kodeo,j.uraian,sum(jumlah) jumlahx from {anggperuk} k left join {obyek} j on mid(k.kodero,1,5)=j.kodeo where kodeuk=\'%s\' and mid(k.kodero,1,3)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datajenis->kodej));
					$fsql .= ' group by mid(k.kodero,1,5),j.uraian order by j.kodeo';
					
					$resultobyek = db_query($fsql);
					if ($resultobyek) {
						while ($dataobyek = db_fetch_object($resultobyek)) {
							
							
							$rowsrek[] = array (
										 array('data' => apbd_format_rek_obyek($dataobyek->kodeo),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $dataobyek->uraian, 'width' => '375px', 'colspan'=>'3',  'style' => ' border-right: 1px solid black; text-align:left;'),
										 array('data' => '', 'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
										 array('data' => '', 'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
										 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
										 array('data' => apbd_fn($dataobyek->jumlahx),  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
										 );		 
												 
							//REKENING
							$sql = 'select k.kodero,r.uraian,jumlah from {anggperuk} k left join {rincianobyek} r on k.kodero=r.kodero where k.kodeuk=\'%s\' and left(k.kodero,5)=\'%s\'';
							$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($dataobyek->kodeo));
							$fsql .= ' group by k.kodero,r.uraian order by k.kodero';
							
							//drupal_set_message( $fsql);
							$result= db_query($fsql);
							if ($result) {
								while ($data = db_fetch_object($result)) {
									
									
									$rowsrek[] = array (
													 array('data' => apbd_format_rek_rincianobyek($data->kodero),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
													 array('data' => $data->uraian, 'width' => '375px', 'colspan'=>'3',  'style' => ' border-right: 1px solid black; text-align:left;'),
													 array('data' => '', 'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
													 array('data' => '', 'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
													 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
													 array('data' => apbd_fn($data->jumlah),  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;'),

													 
													 );	
									//$bullet = '•';
									//DETIL
									$sql = 'select iddetil,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan,harga,total, pengelompokan from {anggperukdetil} where kodeuk=\'%s\' and kodero=\'%s\' order by nourut asc,iddetil';
									$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($data->kodero));
									$res_detil = db_query($fsql);
									if ($res_detil) {
										while ($datadetil = db_fetch_object($res_detil)) {

											if ($datadetil->pengelompokan) {
												$unitjumlah = '';
												$volumjumlah = '';
												$hargasatuan = '';
												$bullet = '#';
												
											} else {
												$unitjumlah = $datadetil->unitjumlah . ' ' . $datadetil->unitsatuan;
												$volumjumlah = $datadetil->volumjumlah . ' ' . $datadetil->volumsatuan;
												$hargasatuan = apbd_fn($datadetil->harga);
												$bullet = '•';
												
											}
										
											$rowsrek[] = array (
															 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
															 array('data' => $bullet, 'width' => '15px',  'style' => ' text-align:center;font-style: italic;'),
															 array('data' => $datadetil->uraian, 'width' => '360px', 'colspan'=>'2',  'style' => ' border-right: 1px solid black; text-align:left;font-style: italic;'),
															 array('data' => $unitjumlah, 'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:center;font-style: italic;'),
															 array('data' => $volumjumlah, 'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:center;font-style: italic;'),
															 array('data' => $hargasatuan, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
															 array('data' => apbd_fn($datadetil->total),  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
														);	
															 
											//SUB DETIL
											if ($datadetil->pengelompokan) {
												//SUB DETIL
												$sql = 'select idsub,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan, harga,total from {anggperukdetilsub} where iddetil=\'%s\' order by nourut asc,idsub';
												$fsql = sprintf($sql, db_escape_string($datadetil->iddetil));
												////drupal_set_message($fsql);
												
												//$no = 0;
												$resultsub = db_query($fsql);
												if ($resultsub) {
													while ($datasub = db_fetch_object($resultsub)) {
														//$no += 1;
														$rowsrek[] = array (
																		 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																		 array('data' => '', 'width' => '15px',  'style' => ' text-align:center;font-style: italic;'),
																		 array('data' => '-', 'width' => '15px',  'style' => ' text-align:center;font-style: italic;'),
																		 array('data' => $datasub->uraian, 'width' => '345px', 'style' => ' border-right: 1px solid black; text-align:left;font-style: italic;'),
																		 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:center;font-style: italic;'),
																		 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:center;font-style: italic;'),
																		 array('data' => apbd_fn($datasub->harga), 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																		 array('data' => apbd_fn($datasub->total),  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																	);												
														//$$$
													}
												}
												
												//###
											}
														
											
										}
									}
									
								}
							}												 
						
						}
					}
				}
			}										 
								 
		////////
		}
	}	
	
	$rowsrek[] = array (
						 array('data' => 'TOTAL PENDAPATAN',  'width'=> '435px',  'colspan'=>'4',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => '',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => '',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => '',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($total),  'width' => '110px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),

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
	if ($kodeuk=='00')
		$pquery = "select '12000' kodedinas, 'BENDAHARA UMUM DAERAH' namauk, pimpinannama, pimpinannip, 'BENDAHARA UMUM DAERAH' pimpinanjabatan 
					from {unitkerja} where kodeuk='81'";
	else
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
							 array('data' => 'CATATAN',  'width'=> '615px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => $pimpinanjabatan,  'width' => '210px','colspan'=>'2',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '615px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => '',  'width' => '210px','colspan'=>'2',  'style' => 'border-right: 1px solid black; text-align:right;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '615px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => '',  'width' => '210px','colspan'=>'2',  'style' => 'border-right: 1px solid black; text-align:right;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '615px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => $pimpinannama,  'width' => '210px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; text-align:center; text-decoration: underline;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '615px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => 'NIP. ' . $pimpinannip,  'width' => '210px','colspan'=>'2',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
							 );
							 
	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	$output = theme_box('', apbd_theme_table($headerkosong, $rowsfooter, $opttbl));
	
	$output .= $toutput;
	return $output;
	
}

function rekapaggpad_form () {
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Paramater Laporan dan Printer',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);
	$kodeuk = arg(4);
	$topmargin = arg(5);
	
	if ($topmargin=='') $topmargin=10;

	if (!isSuperuser()) {
		$type = 'hidden';
		$kodeuk = apbd_getuseruk();
		
	} else {
		$pquery = "select kodedinas, kodeuk, namasingkat, namauk from {unitkerja} where aktif=1 order by kodedinas" ;
		$pres = db_query($pquery);
		$dinas = array();        
		
		$dinas['00'] ='00000 - SEMUA SKPD';
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
		'#weight' => 2,
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
		'#weight' => 3,
	);

	$form['formdata']['ss1'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		'#weight' => 4,
	);		
	$form['formdata']['tampilkan'] = array (
		'#type' => 'submit',
		'#value' => 'Tampilkan',
		'#weight' => 5,
	);
	$form['formdata']['submit'] = array (
		'#type' => 'submit',
		'#value' => 'Cetak',
		'#weight' => 6,
	); 
	
	return $form;
}
function rekapaggpad_form_submit($form, &$form_state) {
	$kodeuk = $form_state['values']['kodeuk'];
	$topmargin = $form_state['values']['topmargin'];
	
	if($form_state['clicked_button']['#value'] == $form_state['values']['tampilkan']) 
		$uri = 'apbd/laporan/rka/rekapaggpad/'. $kodeuk . '/'. $topmargin . '/' ;
	else
		$uri = 'apbd/laporan/rka/rekapaggpad/' . $kodeuk . '/'. $topmargin . '/pdf' ;
	drupal_goto($uri);
	
}
?>