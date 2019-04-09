<?php
function perdabup_main() {
$h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
$h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
drupal_set_html_head($h);
drupal_add_css('files/css/kegiatancam.css');
	$revisi = arg(4);
	$kodeuk = arg(5);
	$topmargin = arg(6);
	$hal1 = arg(7);
	$perdabup = arg(8);
	$lampiran = arg(9);
	$judul = arg(10);
	$ttd = arg(11);
	$exportpdf = arg(12);

	if ($topmargin=='') $topmargin = 10;
	if ($hal1=='') $hal1 = 1;

	//drupal_set_message($ttd);
	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		//require_once('test.php');
		//myt();
		
		$pdfFile = 'apbd-perdabup-' . $kodeuk . '.pdf';

		//$htmlContent = GenReportForm(1);
		//apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);

		$htmlHeader = GenReportFormHeader($kodeuk, $perdabup, $lampiran, $judul);
		$htmlContent = GenReportFormContent($kodeuk, $perdabup,$revisi);
		$htmlFooter = GenReportFormFooter($ttd);
		
		apbd_ExportPDF3_CF($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, false, $pdfFile, $hal1);
		
	} else {
		//$url = 'apbd/laporan/apbd/perdabup/'. $kodeuk . '/' . $topmargin . '/' . $hal1 . '/' . $perdabup . "/pdf";
		$output = drupal_get_form('perdabup_form');
		//$output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;

		$output .= GenReportFormHeader($kodeuk, $perdabup, $lampiran, $judul);
		$output .= GenReportFormContent($kodeuk, $perdabup,$revisi);
		$output .= GenReportFormFooter($ttd);
		
		//$output .= GenReportForm($kodeuk, $perdabup, $lampiran, $judul);
		return $output;
	}

}

function GenReportFormHeader($kodeuk, $perdabup, $lampiran, $judul) {
	

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

	//$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
	$tahun = variable_get('apbdtahun', 0);
	$rows= array();
	//$rowsjudul[] = array (array ('data'=>'RENCANA KERJA DAN ANGGARAN SATUAN KERJA PERANGKAT DAERAH', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));

	if ($lampiran==1) {
		if ($perdabup=='perda') {
			
			$strlamp = 'DAERAH KABUPATEN';
			$rowslampiran[]= array (
								 array('data' => '',  'width'=> '575px', 'style' => 'border:none; text-align:left;'),
								 array('data' => 'LAMPIRAN', 'width' => '50px', 'style' => 'border:none; text-align:right;font-size: 75%;'),
								 array('data' => 'PERATURAN ' . $strlamp . ' JEPARA', 'width' => '250px', 'colspan'=>'2',  'style' => 'border:none;text-align:left;font-size: 75%;'),
								 );

			$query = sprintf("select perdano,perdatgl from {setupapp} where tahun='%s'", variable_get('apbdtahun', 0)) ;
			$res = db_query($query);
			if ($data = db_fetch_object($res)) {
				$perdano = $data->perdano;
				$perdatgl = $data->perdatgl;
			}
								 
		
		} else {
			$strlamp = 'BUPATI';
			$rowslampiran[]= array (
							 array('data' => '',  'width'=> '575px', 'style' => 'border:none; text-align:left;'),
							 array('data' => 'LAMPIRAN II', 'width' => '50px', 'style' => 'border:none; text-align:right;font-size: 75%;'),
							 array('data' => 'PERATURAN ' . $strlamp . ' JEPARA', 'width' => '250px', 'colspan'=>'2',  'style' => 'border:none;text-align:left;font-size: 75%;'),
							 );
							 
			$query = sprintf("select perbupno,perbuptgl from {setupapp} where tahun='%s'", variable_get('apbdtahun', 0)) ;
			$res = db_query($query);
			if ($data = db_fetch_object($res)) {
				$perdano = $data->perbupno;
				$perdatgl = $data->perbuptgl;
			}							 

		}
		$rowslampiran[]= array (
							 array('data' => '',  'width'=> '525px', 'style' => 'border:none; text-align:left;'),
							 array('data' => '', 'width' => '100px', 'style' => 'border:none; text-align:right;'),
							 array('data' => 'Nomor', 'width' => '50px',  'style' => 'border:none;text-align:left;font-size: 75%;'),
							 array('data' => ': ' . $perdano, 'width' => '200px', 'style' => 'border:none; text-align:left;font-size: 75%;'),
							 );
		$rowslampiran[]= array (
							 array('data' => '',  'width'=> '575px', 'style' => 'border:none; text-align:left;'),
							 array('data' => '', 'width' => '50px', 'style' => 'border-bottom: 1px solid black; text-align:right;'),
							 array('data' => 'Tanggal', 'width' => '50px',  'style' => 'border-bottom: 1px solid black; text-align:left;font-size: 75%;'),
							 array('data' => ': ' . $perdatgl, 'width' => '200px', 'style' => 'border-bottom: 1px solid black;  text-align:left;font-size: 75%;'),
							 );
	}
	
	if ($judul==1) {
		$rowsjudul[] = array (array ('data'=>'PEMERINTAH KABUPATEN JEPARA', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1em; text-align:center;'));
		$rowsjudul[] = array (array ('data'=>'PENJABARAN PERUBAHAN ANGGARAN PENDAPATAN DAN BELANJA DAERAH', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
		$rowsjudul[] = array (array ('data'=>'TAHUN ANGGARAN 2016', 'width'=>'875px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1em; text-align:center;'));
	}
	
	//POTRAIT = 353
	//LANDSCAPE = 875
	$rowskegiatan[]= array (
						 array('data' => 'Urusan',  'width'=> '120px', 'style' => 'border:none; text-align:left;'),
						 array('data' => ':', 'width' => '10px', 'style' => 'border:none; text-align:center;'),
						 array('data' => $urusan, 'width' => '745px', 'colspan'=>'5',  'style' => 'border:none;text-align:left;'),
						 );
	$rowskegiatan[]= array (
						 array('data' => 'SKPD',  'width'=> '120px', 'style' => ' text-align:left;'),
						 array('data' => ':',  'width' => '10px', 'style' => 'text-align:center;'),
						 array('data' => $skpd,  'width' => '745px', 'colspan'=>'5',  'style' => ' text-align:left;'),
						 );

	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	if ($lampiran==1) 
		$output = theme_box('', apbd_theme_table($headerkosong, $rowslampiran, $opttbl));
	else
		$output ='';
	
	if ($judul==1) $output .= theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	$output .= theme_box('', apbd_theme_table($headerkosong, $rowskegiatan, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormContent($kodeuk, $perdabup, $lampiran,$revisi) {

	set_time_limit(0);
	ini_set('memory_limit', '640M');
	
	if ($revisi=='9')
		$str_table = '';
	else
		$str_table = $revisi;
	
	$skpd = '';
	$pimpinannama='';
	$pimpinannip='';
	$pimpinanjabatan='';
	
	$pquery = sprintf("select uk.kodedinas, uk.namauk, uk.pimpinannama, uk.pimpinannip, uk.pimpinanjabatan, u.kodeu, u.urusan 
				from {unitkerja} uk inner join {ukurusan} uku on uk.kodeuk=uku.kodeuk inner join {urusan} u on uku.kodeu=u.kodeu 
				where uk.kodeuk='%s'", db_escape_string($kodeuk)) ;
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$kodedinas = $data->kodedinas;
		$urusan = $data->kodeu . ' - ' . $data->urusan;
		$skpd = $kodedinas . ' - ' . $data->namauk;
		$pimpinannama=$data->pimpinannama;
		$pimpinannip=$data->pimpinannip;
		$pimpinanjabatan=$data->pimpinanjabatan;
	}


	$headersrek[] = array (
						 
						 array('data' => 'KODE', 'rowspan'=>'2',  'width'=> '125px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'URAIAN',  'rowspan'=>'2', 'width' => '250px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'JUMLAH (Rp)',  'colspan'=>'2', 'width' => '200px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'BERTAMBAH/ BERKURANG', 'colspan'=>'2',  'width' => '150px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'PENJELASAN',  'rowspan'=>'2', 'width' => '150px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );

	$headersrek[] = array (
						 
						 array('data' => 'Sebelum Perubahan',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'Setelah Perubahan',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'Rupiah',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'Persen',  'width' => '50px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 );
	
						 
	//****PENDAPATAN
	$totalp = 0;$totalpp=0;$totalpt=0;
		
	$where = ' where k.kodeuk=\'%s\'';
	$sql = 'select a.kodea,a.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperuk} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea ' . $where;
	$fsql = sprintf($sql, db_escape_string($kodeuk));
	$fsql .= ' group by a.kodea,a.uraian order by a.kodea';
	

	//drupal_set_message( $fsql);
	$resultakun = db_query($fsql);
	if ($resultakun) {
		while ($dataakun = db_fetch_object($resultakun)) {
			$totalp += $dataakun->jumlahx;
			$totalpp += $dataakun->jumlahxp;
			$totalpt += $dataakun->jumlahxp-$dataakun->jumlahx;
			$rowsrek[] = array (
								 array('data' => $kodedinas . '.'. $dataakun->kodea,  'width'=> '125px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $dataakun->uraian,  'width' => '250px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahxp-$dataakun->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn2(apbd_hitungpersen($dataakun->jumlahx, $dataakun->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
								 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
								 );
			
			//KELOMPOK
			$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp  from {anggperuk} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek where k.kodeuk=\'%s\' and mid(k.kodero,1,1)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($dataakun->kodea));
			$fsql .= ' group by x.kodek,x.uraian order by x.kodek';
				
			//drupal_set_message( $fsql);
			$resultkel = db_query($fsql);
			if ($resultkel) {
				while ($datakel = db_fetch_object($resultkel)) {
					$rowsrek[] = array (
										 array('data' => $kodedinas . '.000.000.' . $datakel->kodek,  'width'=> '125px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datakel->uraian,  'width' => '250px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black;text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black;text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahxp-$datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black;text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn2(apbd_hitungpersen($datakel->jumlahx, $datakel->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black;text-align:right;font-weight:bold;'),
										 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),									 
										);		

					//JENIS
					$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperuk} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej where k.kodeuk=\'%s\' and mid(k.kodero,1,2)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datakel->kodek));
					$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
					
					//drupal_set_message( $fsql);
					$resultj = db_query($fsql);
					if ($resultj) {
						while ($dataj = db_fetch_object($resultj)) {

							if ($perdabup == 'perda') {
								$strjenis = ucfirst(strtolower($dataj->uraian));
								$fontstyle = '';
							} else {
								$strjenis = strtoupper($dataj->uraian);
								$fontstyle = 'font-weight:bold;';
							}	
											
							$rowsrek[] = array (
												 array('data' => $kodedinas . '.000.000.' . ($dataj->kodej),  'width'=> '125px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
												 array('data' => $strjenis,  'width' => '250px', 'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => apbd_fn($dataj->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;' . $fontstyle),
												 array('data' => apbd_fn($dataj->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;' . $fontstyle),
												 array('data' => apbd_fn($dataj->jumlahxp-$dataj->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;' . $fontstyle),
												 array('data' => apbd_fn2(apbd_hitungpersen($dataj->jumlahx, $dataj->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; text-align:right;' . $fontstyle),
												 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
											 );
							if ($perdabup == 'perbup') {
								//OBYEK
								$sql = 'select o.kodeo,o.uraian,sum(jumlah) jumlahx, sum(jumlahp) jumlahxp from {anggperuk} k inner join {obyek} o on mid(k.kodero,1,5)=o.kodeo where k.kodeuk=\'%s\' and o.kodej=\'%s\'';
								$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($dataj->kodej));
								$fsql .= ' group by o.kodeo,o.uraian order by o.kodeo';
								//drupal_set_message( $fsql);
								$resulto = db_query($fsql);
								if ($resulto) {
									while ($datao = db_fetch_object($resulto)) {
										//$newstr = substr_replace($oldstr, $str_to_insert, $pos, 0);
										 
										$rowsrek[] = array (
															 array('data' => $kodedinas . '.000.000.' . substr_replace($datao->kodeo, '.', 3,0),  'width'=> '125px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
															 array('data' => $datao->uraian,  'width' => '250px', 'style' => ' border-right: 1px solid black; text-align:left;'),
															 array('data' => apbd_fn($datao->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
															 array('data' => apbd_fn($datao->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
															 array('data' => apbd_fn($datao->jumlahxp-$datao->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
															 array('data' => apbd_fn2(apbd_hitungpersen($datao->jumlahx, $datao->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; text-align:right;'),
															 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
														);	
										//RINCIAN OBYEK
										$sql = 'select ro.kodero,ro.uraian,jumlah jumlahx,jumlahp jumlahxp,k.ketrekening from {anggperuk} k inner join {rincianobyek} ro on k.kodero=ro.kodero where k.kodeuk=\'%s\' and ro.kodeo=\'%s\'';
										$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datao->kodeo));
										$fsql .= ' order by ro.kodero';
										//drupal_set_message( $fsql); 
										$result = db_query($fsql);
										if ($result) {
											while ($dataro = db_fetch_object($result)) {
												$rowsrek[] = array (
																	 array('data' => $kodedinas . '.000.000.' . substr_replace($dataro->kodero, '.', 3,0),  'width'=> '125px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																	 array('data' => $dataro->uraian,  'width' => '250px', 'style' => ' border-right: 1px solid black; text-align:left;font-style: italic;'),
																	 array('data' => apbd_fn($dataro->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																	 array('data' => apbd_fn($dataro->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																	 array('data' => apbd_fn($dataro->jumlahxp-$dataro->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																	 array('data' => apbd_fn2(apbd_hitungpersen($dataro->jumlahx, $dataro->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																	 array('data' => $dataro->ketrekening,  'width' => '150px', 'style' => ' border-right: 1px solid black;font-weight:lighter;font-style: italic;'),
																);												
											}
										}
										
									}
								}		//Obyek
											 
							}	//Perbupda					 
						}
					}										 
										 
				////////
				}
			}	
	
			//spasi
			$rowsrek[] = array (
								 array('data' => '',  'width'=> '125px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => '',  'width' => '250px', 'style' => ' border-right: 1px solid black; text-align:left;font-style: italic;'),
								 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
								 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
								 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
								 array('data' => '',  'width' => '50px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
								 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;font-weight:lighter;font-style: italic;'),
							);					
		}
	}
	
	
	
	//****BELANJA
	$totalb = 0;$totalbp=0;$totalbt=0;
	$where = ' and g.kodeuk=\'%s\'';
	$sql = 'select a.kodea,a.uraian,sum(k.jumlah) jumlahx,sum(k.jumlahp) jumlahxp from {anggperkegperubahan' . $str_table . '} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea inner join {kegiatanperubahan' . $str_table . '} g on k.kodekeg=g.kodekeg inner join {kegiatanperubahan' . $str_table . '} keg on k.kodekeg=keg.kodekeg where keg.inaktif=0 ' . $where;
	$fsql = sprintf($sql, db_escape_string($kodeuk));
	$fsql .= ' group by a.kodea,a.uraian order by a.kodea';
	
	//drupal_set_message( $fsql);
	$resultakun = db_query($fsql);
	if ($resultakun) {
		while ($dataakun = db_fetch_object($resultakun)) {
			$totalb += $dataakun->jumlahx;
			$totalbp += $dataakun->jumlahxp;
			$totalbt += $dataakun->jumlahxp-$dataakun->jumlahx;
			$rowsrek[] = array (
								 array('data' => $kodedinas . '.' . $dataakun->kodea,  'width'=> '125px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $dataakun->uraian,  'width' => '250px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahxp-$dataakun->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn2(apbd_hitungpersen($dataakun->jumlahx, $dataakun->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
								 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
							 );
				
			//KELOMPOK - BTL
			$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperkegperubahan' . $str_table . '} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek inner join {kegiatanperubahan' . $str_table . '} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.kodeuk=\'%s\' and left(k.kodero,2)=\'%s\' and mid(k.kodero,1,1)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string('51'), db_escape_string($dataakun->kodea));
			$fsql .= ' group by x.kodek,x.uraian order by x.kodek';
			
			//drupal_set_message( $fsql);
			$resultkel = db_query($fsql);
			if ($resultkel) {
				while ($datakel = db_fetch_object($resultkel)) {
					$rowsrek[] = array (
										 array('data' => $kodedinas . '.000.000.' . $datakel->kodek,  'width'=> '125px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datakel->uraian,  'width' => '250px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahxp-$datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn2(apbd_hitungpersen($datakel->jumlahx, $datakel->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
										 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
									 );		

					//JENIS
					$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperkegperubahan' . $str_table . '} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej  inner join {kegiatanperubahan' . $str_table . '} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.kodeuk=\'%s\' and j.kodek=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datakel->kodek));
					$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
					
					//drupal_set_message( $fsql);
					$resultj = db_query($fsql);
					if ($resultj) {
						while ($dataj = db_fetch_object($resultj)) {

							if ($perdabup == 'perda') {
								$strjenis = ucfirst(strtolower($dataj->uraian));
								$fontstyle = '';
							} else {
								$strjenis = strtoupper($dataj->uraian);
								$fontstyle = 'font-weight:bold;';
							}	
							
							$rowsrek[] = array (
												 array('data' => $kodedinas . '.000.000.' . ($dataj->kodej),  'width'=> '125px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
												 array('data' => $strjenis,  'width' => '250px', 'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => apbd_fn($dataj->jumlahx),  'width' => '100px', 'style' => 'border-right: 1px solid black; text-align:right;' . $fontstyle),
												 array('data' => apbd_fn($dataj->jumlahxp),  'width' => '100px', 'style' => 'border-right: 1px solid black; text-align:right;' . $fontstyle),
												 array('data' => apbd_fn($dataj->jumlahxp-$dataj->jumlahx),  'width' => '100px', 'style' => 'border-right: 1px solid black; text-align:right;' . $fontstyle),
												 array('data' => apbd_fn2(apbd_hitungpersen($dataj->jumlahx, $dataj->jumlahxp)),  'width' => '50px', 'style' => 'border-right: 1px solid black; text-align:right;' . $fontstyle),
												 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
											 );
							if ($perdabup == 'perbup') {
								//OBYEK
								$sql = 'select o.kodeo,o.uraian,sum(jumlah) jumlahx, sum(jumlahp) jumlahxp from {anggperkegperubahan' . $str_table . '} k inner join {obyek} o on mid(k.kodero,1,5)=o.kodeo inner join {kegiatanperubahan' . $str_table . '} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.kodeuk=\'%s\' and o.kodej=\'%s\'';
								$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($dataj->kodej));
								$fsql .= ' group by o.kodeo,o.uraian order by o.kodeo';
								
								//drupal_set_message( $fsql);
								$resulto = db_query($fsql);
								if ($resulto) {
									while ($datao = db_fetch_object($resulto)) {
										$rowsrek[] = array (
															 array('data' => $kodedinas . '.000.000.' . substr_replace($datao->kodeo, '.', 3,0),  'width'=> '125px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
															 array('data' => $datao->uraian,  'width' => '250px', 'style' => ' border-right: 1px solid black; text-align:left;'),
															 array('data' => apbd_fn($datao->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
															 array('data' => apbd_fn($datao->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
															 array('data' => apbd_fn($datao->jumlahxp-$datao->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
															 array('data' => apbd_fn2(apbd_hitungpersen($datao->jumlahx, $datao->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; text-align:right;'),
															 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
														 );		

										//RINCIAN OBYEK
										$sql = 'select ro.kodero,ro.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperkegperubahan' . $str_table . '} k inner join {rincianobyek} ro on k.kodero=ro.kodero inner join {kegiatanperubahan' . $str_table . '} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.kodeuk=\'%s\' and ro.kodeo=\'%s\'';
										$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datao->kodeo));
										$fsql .= ' group by ro.kodero,ro.uraian order by ro.kodero';
										
										$result = db_query($fsql);
										if ($result) {
											while ($dataro = db_fetch_object($result)) {
												$rowsrek[] = array (
																 array('data' => $kodedinas . '.000.000.' . substr_replace($dataro->kodero, '.', 3,0),  'width'=> '125px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																 array('data' => $dataro->uraian,  'width' => '250px', 'style' => ' border-right: 1px solid black; text-align:left;font-style: italic;'),
																 array('data' => apbd_fn($dataro->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																 array('data' => apbd_fn($dataro->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																 array('data' => apbd_fn($dataro->jumlahxp-$dataro->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																 array('data' => apbd_fn2(apbd_hitungpersen($dataro->jumlahx, $dataro->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
															 );		
												
												//detil rincian
												if (($dataj->kodej == 516) or ($dataj->kodej == 517) ) {
													//KEGIATAN
													$sql = 'select k.kodekeg,k.kegiatan,a.jumlah as jumlahx, a.jumlahp as jumlahxp from {kegiatanperubahan' . $str_table . '} k inner join {anggperkegperubahan' . $str_table . '} a on k.kodekeg=a.kodekeg where a.kodero=\'%s\' order by kegiatan';
													$fsql = sprintf($sql, db_escape_string($dataro->kodero));
													
													$resultdetil = db_query($fsql);
													if ($resultdetil) {
														while ($datadetil = db_fetch_object($resultdetil)) {
															$rowsrek[] = array (
																			 array('data' => '',  'width'=> '125px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																			 array('data' => '- ' . $datadetil->kegiatan,  'width' => '250px', 'style' => ' border-right: 1px solid black; text-align:left;font-style: italic;font-weight:100;'),
																			 array('data' => apbd_fn($datadetil->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;font-weight:100;'),
																			 array('data' => apbd_fn($datadetil->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;font-weight:100;'),
																			 array('data' => apbd_fn($datadetil->jumlahxp-$datadetil->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;font-weight:100;'),
																			 array('data' => apbd_fn2(apbd_hitungpersen($datadetil->jumlahx, $datadetil->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;font-weight:100;'),
																			 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
																		 );		
															
														}
													}
												}
												
											}
										}
										
									}
								}	//Obyek
						
							}	//Perdabup
						
						}	//while jenis
					}	//res jenis									 
										 
				}	//while kel
			}	//res kel	

			//KELOMPOK - BL
			$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx, sum(jumlahp) jumlahxp from {anggperkegperubahan' . $str_table . '} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek inner join {kegiatanperubahan' . $str_table . '} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.kodeuk=\'%s\' and left(k.kodero,2)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string('52'));
			$fsql .= ' group by x.kodek,x.uraian order by x.kodek';			
			$resultkel = db_query($fsql);
			if ($resultkel) {
				while ($datakel = db_fetch_object($resultkel)) {
					$rowsrek[] = array (
										 array('data' => $kodedinas . '.' . $datakel->kodek ,  'width'=> '125px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datakel->uraian,  'width' => '250px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black;text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black;text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahxp-$datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black;text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn2(apbd_hitungpersen($datakel->jumlahx, $datakel->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black;text-align:right;font-weight:bold;'),
										 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
									 );	
					
					//PROGRAM
					$sql = 'select k.kodepro, p.program uraian,sum(k.total) jumlahx, sum(k.totalp) jumlahxp from {kegiatanperubahan' . $str_table . '} k 
					inner join {program} p on k.kodepro=p.kodepro where k.inaktif=0 and k.kodeuk=\'%s\' and k.jenis=2';
					$fsql = sprintf($sql, db_escape_string($kodeuk));
					$fsql .= ' group by k.kodepro,p.program order by k.kodepro';			
					$resultpro = db_query($fsql);
					if ($resultpro) {
						while ($datapro = db_fetch_object($resultpro)) {
							$rowsrek[] = array (
												 array('data' => $kodedinas . '.' . $datapro->kodepro,  'width'=> '125px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
												 array('data' => strtoupper($datapro->uraian),  'width' => '250px', 'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => apbd_fn($datapro->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
												 array('data' => apbd_fn($datapro->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
												 array('data' => apbd_fn($datapro->jumlahxp-$datapro->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
												 array('data' => apbd_fn2(apbd_hitungpersen($datapro->jumlahx, $datapro->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
												 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
											 );	
							
							//KEGIATAN
							$sql = 'select kodekeg, nomorkeg, kegiatan uraian,sumberdana1, total jumlahx, totalp jumlahxp,lokasi from {kegiatanperubahan' . $str_table . '} k where k.inaktif=0 and k.kodeuk=\'%s\' and k.kodepro=\'%s\' and k.jenis=2';
							$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datapro->kodepro));
							$fsql .= ' order by k.kodekeg';			
							$resultkeg = db_query($fsql);
							if ($resultkeg) {
								while ($datakeg = db_fetch_object($resultkeg)) {
									$rowsrek[] = array (
														 array('data' => $kodedinas . '.' . $datapro->kodepro . '.' . substr($datakeg->kodekeg,3),  'width'=> '125px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
														 array('data' => strtoupper($datakeg->uraian),  'width' => '250px', 'style' => ' border-right: 1px solid black; text-align:left;font-style: italic;'),
														 array('data' => apbd_fn($datakeg->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:right;'),
														 array('data' => apbd_fn($datakeg->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:right;'),
														 array('data' => apbd_fn($datakeg->jumlahxp-$datakeg->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:right;'),
														 array('data' => apbd_fn2(apbd_hitungpersen($datakeg->jumlahx, $datakeg->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:right;'),
														 array('data' => 'Lokasi : ' . str_replace('||',', ', $datakeg->lokasi),  'width' => '150px', 'style' => ' border-right: 1px solid black;font-weight:lighter;font-style: italic;'),
													);	
									$sdindex =0; 
									//REK JENIS
									$sql = 'select j.kodej,j.uraian,sum(k.jumlah) jumlahx,sum(k.jumlahp) jumlahxp from {anggperkegperubahan' . $str_table . '} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej  inner join {kegiatanperubahan' . $str_table . '} g on k.kodekeg=g.kodekeg where g.kodeuk=\'%s\' and g.kodekeg=\'%s\'';
									$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datakeg->kodekeg));
									$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
									//drupal_set_message($fsql);
									$resultj = db_query($fsql);
									if ($resultj) {
										while ($dataj = db_fetch_object($resultj)) {

											if ($perdabup == 'perda') {
												$strjenis = ucfirst(strtolower($dataj->uraian));
												$fontstyle = '';
											} else {
												$strjenis = strtoupper($dataj->uraian);
												$fontstyle = 'font-weight:bold;';
											}											
											
											$sdstr = '';
											if ($sdindex==0) {
												if (($datakeg->sumberdana1 == 'DAK') or ($datakeg->sumberdana1 == 'BANPROV') or ($datakeg->sumberdana1 == 'DBH CHT')) {
													$sdstr = 'Sumber dana : ' . $datakeg->sumberdana1;
													$sdindex =1;
												}
												
											}
											
											$rowsrek[] = array (
																 array('data' => $kodedinas . '.' . $datapro->kodepro . '.' . substr($datakeg->kodekeg,3) . '.' . $dataj->kodej,  'width'=> '125px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																 array('data' => $strjenis,  'width' => '250px', 'style' => ' border-right: 1px solid black; text-align:left;'),
																 array('data' => apbd_fn($dataj->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;' . $fontstyle),
																 array('data' => apbd_fn($dataj->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;' . $fontstyle),
																 array('data' => apbd_fn($dataj->jumlahxp-$dataj->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;' . $fontstyle),
																 array('data' => apbd_fn2(apbd_hitungpersen($dataj->jumlahx, $dataj->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; text-align:right;' . $fontstyle),
																 array('data' => $sdstr,  'width' => '150px', 'style' => ' border-right: 1px solid black; font-weight:lighter;font-style: italic;'),
															);	
											if ($perdabup == 'perbup') {
												//OBYEK
												$sql = 'select o.kodeo,o.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperkegperubahan' . $str_table . '} k inner join {obyek} o on mid(k.kodero,1,5)=o.kodeo inner join {kegiatanperubahan' . $str_table . '} g on k.kodekeg=g.kodekeg where g.kodeuk=\'%s\' and g.kodekeg=\'%s\' and o.kodej=\'%s\'';
												$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datakeg->kodekeg), db_escape_string($dataj->kodej));
												$fsql .= ' group by o.kodeo,o.uraian order by o.kodeo';
												//drupal_set_message( $fsql);											
												$resulto = db_query($fsql);
												if ($resulto) {
													while ($datao = db_fetch_object($resulto)) {
														$rowsrek[] = array (
																			 array('data' => $kodedinas . '.' . $datapro->kodepro . '.' . substr($datakeg->kodekeg,3) . '.' . substr_replace($datao->kodeo, '.', 3,0),  'width'=> '125px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																			 array('data' => $datao->uraian,  'width' => '250px', 'style' => ' border-right: 1px solid black; text-align:left;'),
																			 array('data' => apbd_fn($datao->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black;text-align:right;'),
																			 array('data' => apbd_fn($datao->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black;text-align:right;'),
																			 array('data' => apbd_fn($datao->jumlahxp-$datao->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black;text-align:right;'),
																			 array('data' => apbd_fn2(apbd_hitungpersen($datao->jumlahx, $datao->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black;text-align:right;'),
																			 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
																		);	

														//RINCIAN OBYEK
														$sql = 'select ro.kodero,ro.uraian,jumlah jumlahx, jumlahp jumlahxp from {anggperkegperubahan' . $str_table . '} k inner join {rincianobyek} ro on k.kodero=ro.kodero inner join {kegiatanperubahan' . $str_table . '} g on k.kodekeg=g.kodekeg where g.kodeuk=\'%s\' and g.kodekeg=\'%s\' and ro.kodeo=\'%s\'';
														$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datakeg->kodekeg), db_escape_string($datao->kodeo));
														$fsql .= ' group by ro.kodero,ro.uraian order by ro.kodero';
														//drupal_set_message( $fsql);
														$result = db_query($fsql);
														if ($result) {
															while ($dataro = db_fetch_object($result)) {
																$rowsrek[] = array (
																					 array('data' => $kodedinas . '.' . $datapro->kodepro . '.' . substr($datakeg->kodekeg,3) . '.' . substr_replace($dataro->kodero, '.', 3,0),  'width'=> '125px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																					 array('data' => $dataro->uraian,  'width' => '250px', 'style' => ' border-right: 1px solid black; text-align:left;font-style: italic;'),
																					 array('data' => apbd_fn($dataro->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																					 array('data' => apbd_fn($dataro->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																					 array('data' => apbd_fn($dataro->jumlahxp-$dataro->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																					 array('data' => apbd_fn2(apbd_hitungpersen($dataro->jumlahx, $dataro->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																					 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
																				);															
															}
														}				
													
													}
												}				//Obyek	
											}	//Perdabup				
										}
									}
								}
							}
							
						}
					}
					
				}
			}
			
		}	//while belanja


	}
	
	
	
	//****PEMBIAYAAN
	$totalpm = 0;
	$totalpk = 0;
	$totalpmp = 0;
	$totalpkp = 0;

	if ($kodeuk=='81') {

		$rowsrek[] = array (
							 array('data' => '',  'width'=> '125px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => '',  'width' => '250px', 'style' => ' border-right: 1px solid black; text-align:left;font-style: italic;'),
							 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
							 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
							 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
							 array('data' => '',  'width' => '50px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
							 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
						);	
		$totalp += $dataakun->jumlahx;
		$rowsrek[] = array (
							 array('data' => $kodedinas . '.6',  'width'=> '125px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => 'PEMBIAYAAN',  'width' => '250px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
							 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
							 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
							 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
							 array('data' => '',  'width' => '50px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
							 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
							 );
		
		//KELOMPOK
		$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperda} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek group by x.kodek,x.uraian order by x.kodek';
			
		//drupal_set_message( $fsql);
		$resultkel = db_query($sql);
		if ($resultkel) {
			while ($datakel = db_fetch_object($resultkel)) {
				if ($datakel->kodek=='61') {
					$totalpm += $datakel->jumlahx;
					$totalpmp += $datakel->jumlahxp;
				} else {
					$totalpk += $datakel->jumlahx;
					$totalpkp += $datakel->jumlahxp;
				}
				
				$rowsrek[] = array (
									 array('data' => $kodedinas . '.000.000.' . $datakel->kodek,  'width'=> '125px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
									 array('data' => $datakel->uraian,  'width' => '250px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
									 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black;text-align:right;font-weight:bold;'),
									 array('data' => apbd_fn($datakel->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black;text-align:right;font-weight:bold;'),
									 array('data' => apbd_fn($datakel->jumlahx - $datakel->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black;text-align:right;font-weight:bold;'),
									 array('data' => apbd_fn2(apbd_hitungpersen($datakel->jumlahx, $datakel->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black;text-align:right;font-weight:bold;'),
									 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),									 
									);		

				//JENIS
				$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperda} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej where mid(k.kodero,1,2)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($datakel->kodek));
				$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
				
				//drupal_set_message( $fsql);
				$resultj = db_query($fsql);
				if ($resultj) {
					while ($dataj = db_fetch_object($resultj)) {

						if ($perdabup == 'perda') {
							$strjenis = ucfirst(strtolower($dataj->uraian));
							$fontstyle = '';
						} else {
							$strjenis = strtoupper($dataj->uraian);
							$fontstyle = 'font-weight:bold;';
						}	
										
						$rowsrek[] = array (
											 array('data' => $kodedinas . '.000.000.' . ($dataj->kodej),  'width'=> '125px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
											 array('data' => $strjenis,  'width' => '250px', 'style' => ' border-right: 1px solid black; text-align:left;'),
											 array('data' => apbd_fn($dataj->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;' . $fontstyle),
											 array('data' => apbd_fn($dataj->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;' . $fontstyle),
											 array('data' => apbd_fn($dataj->jumlahxp - $dataj->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;' . $fontstyle),
											 array('data' => apbd_fn2(apbd_hitungpersen($dataj->jumlahx, $dataj->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; text-align:right;' . $fontstyle),
											 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
										 );
						if ($perdabup == 'perbup') {
							//OBYEK
							$sql = 'select o.kodeo,o.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperda} k inner join {obyek} o on mid(k.kodero,1,5)=o.kodeo where o.kodej=\'%s\'';
							$fsql = sprintf($sql, db_escape_string($dataj->kodej));
							$fsql .= ' group by o.kodeo,o.uraian order by o.kodeo';
							//drupal_set_message( $fsql);
							$resulto = db_query($fsql);
							if ($resulto) {
								while ($datao = db_fetch_object($resulto)) {
									//$newstr = substr_replace($oldstr, $str_to_insert, $pos, 0);
									 
									$rowsrek[] = array (
														 array('data' => $kodedinas . '.000.000.' . substr_replace($datao->kodeo, '.', 3,0),  'width'=> '125px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
														 array('data' => ucfirst(strtolower($datao->uraian)),  'width' => '250px', 'style' => ' border-right: 1px solid black; text-align:left;'),
														 array('data' => apbd_fn($datao->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
														 array('data' => apbd_fn($datao->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
														 array('data' => apbd_fn($datao->jumlahxp - $datao->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
														 array('data' => apbd_fn2(apbd_hitungpersen($datao->jumlahx, $datao->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; text-align:right;'),
														 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),
													);	
									//RINCIAN OBYEK
									$sql = 'select ro.kodero,ro.uraian,jumlah jumlahx,jumlahp jumlahxp from {anggperda} k inner join {rincianobyek} ro on k.kodero=ro.kodero where ro.kodeo=\'%s\'';
									$fsql = sprintf($sql, db_escape_string($datao->kodeo));
									$fsql .= ' order by ro.kodero';
									//drupal_set_message( $fsql);
									$result = db_query($fsql);
									if ($result) {
										while ($dataro = db_fetch_object($result)) {
											$rowsrek[] = array (
																 array('data' => $kodedinas . '.000.000.' . substr_replace($dataro->kodero, '.', 3,0),  'width'=> '125px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																 array('data' => ucfirst(strtolower($dataro->uraian)),  'width' => '250px', 'style' => ' border-right: 1px solid black; text-align:left;font-style: italic;'),
																 array('data' => apbd_fn($dataro->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																 array('data' => apbd_fn($dataro->jumlahxp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																 array('data' => apbd_fn($dataro->jumlahxp - $dataro->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																 array('data' => apbd_fn2(apbd_hitungpersen($dataro->jumlahx, $dataro->jumlahxp)),  'width' => '50px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;font-weight:lighter;font-style: italic;'),
															);												
										}
									}
									
								}
							}		//Obyek
										 
						}	//Perbupda					 
					}
				}										 
									 
			////////
			}
		}	
		
		$netto = $totalpm - $totalpk;
		$nettop = $totalpmp - $totalpkp;
		
		$rowsrek[] = array (
							 array('data' => '',  'width'=> '125px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => 'PEMBIAYAAN NETTO',  'width' => '250px', 'style' => 'border-right: 1px solid black; border-top: 1px solid black; text-align:right;font-weight:bold;'),
							 array('data' => apbd_fn($netto),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;text-align:right;font-weight:bold;'),
							 array('data' => apbd_fn($nettop),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;text-align:right;font-weight:bold;'),
							 array('data' => apbd_fn($nettop - $netto),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;text-align:right;font-weight:bold;'),
							 array('data' => apbd_fn2(apbd_hitungpersen($netto, $nettop)),  'width' => '50px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;text-align:right;font-weight:bold;'),
							 array('data' => '',  'width' => '150px', 'style' => ' border-right: 1px solid black;'),									 
							);		
		
	}
	
	$surdes = $totalp - $totalb + $netto;
	$surdesp = $totalpp - $totalbp + $nettop;
	
	$rowsrek[] = array (
						 array('data' => 'SURPLUS / DEFISIT',  'width'=> '375px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($surdes),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($surdesp),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($surdesp - $surdes),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn2(apbd_hitungpersen($surdes, $surdesp)),  'width' => '50px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => '',  'width' => '150px', 'style' => 'border-bottom: 1px solid black;  border-right: 1px solid black;'),
					 );	
	
	
 
	$opttb0 = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0`');
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '1');
	$headerkosong = array();

	if ($lampiran) $output = theme_box('', apbd_theme_table($headerkosong, $rowslampiran, $opttb0));	
	if ($judul) $output .= theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttb0));
	$output .= theme_box('', apbd_theme_table($headerkosong, $rowskegiatan, $opttb0));
	
	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	$output .= theme_box('', apbd_theme_table($headerkosong, $rowsfooter, $opttb0));
	
	$output .= $toutput;
	if ($limit >0)
		$output .= theme ('pager', NULL, $limit, 0);
	
	return $output;	
}

function GenReportFormFooter($ttd) {
	if ($ttd==1) {
		$pimpinannama= 'IHWAN SUDRAJAT';
		$pimpinanjabatan= 'Plt. BUPATI JEPARA';


		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '635px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => '',  'width' => '200px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '635px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => $pimpinanjabatan,  'width' => '200px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '635px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => '',  'width' => '200px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '635px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => '',  'width' => '200px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '635px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => '',  'width' => '200px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '635px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => $pimpinannama,  'width' => '200px', 'style' => 'text-align:center;'),
							 );

		$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
		$headerkosong = array();

		//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
		$output = theme_box('', apbd_theme_table($headerkosong, $rowsfooter, $opttbl));		
	}
	return $output;
}

function perdabup_form () {
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Paramater Laporan dan Printer',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);
	$revisi = arg(4);
	$kodeuk = arg(5);
	$topmargin = arg(6);
	$hal1 = arg(7);
	$perdabup = arg(8);
	$lampiran = arg(9);
	$judul = arg(10);
	$ttd = arg(11);
	
	
	if ($topmargin=='') $topmargin=10;
	if ($hal1=='') $hal1=1;
	if ($perdabup=='') $perdabup='perda'; 

	$pquery = "select kodedinas, kodeuk, namasingkat, namauk from {unitkerja} where aktif=1 and kodeuk in (select kodeuk from {kegiatanrevisiperubahan} where status=1) order by kodedinas" ;
	$pres = db_query($pquery);
	$dinas = array();        
	
	$dinas['00'] = '--PILIH SKPD--';
	while ($data = db_fetch_object($pres)) {
		$dinas[$data->kodeuk] = $data->kodedinas . ' - ' . $data->namasingkat;
	}
	 
	$form['formdata']['kodeuk']= array(
		'#type'         => 'select', 
		'#title'        => 'SKPD',
		'#options'	=> $dinas,
		//'#description'  => 'kodeuktujuan', 
		//'#maxlength'    => 60, 
		'#width'         => 30, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeuk, 
		'#weight' => 1,
	);
	$form['formdata']['revisi']= array(
		'#type'         => 'value', 
		'#default_value'=> $revisi, 
		
	);

	$form['formdata']['perdabup']= array(
		'#type' => 'radios', 
		'#title' => t('Perda/Perbup'), 
		'#options' => array(	
			 'perda' => t('Perda'), 	
			 'perbup' => t('Perbup'),	
		   ),
		'#default_value' => $perdabup,
		'#weight' => 2,		
	);	

	$form['formdata']['ss0'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		'#weight' => 3,
	);	
	$form['formdata']['lampiran']= array(
		'#type' => 'radios', 
		'#title' => t('Cetak Lampiran'), 
		'#options' => array(	
			 '1' => t('Cetak'), 	
			 '' => t('Tidak'), 	
		   ),
		'#default_value' => $lampiran,
		'#weight' => 4,
	);	
	
	$form['formdata']['ssl'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		'#weight' => 5,
	);	

	$form['formdata']['judul']= array(
		'#type' => 'radios', 
		'#title' => t('Cetak Judul'), 
		'#options' => array(	
			 '1' => t('Cetak'), 	
			 '' => t('Tidak'), 	
		   ),
		'#default_value' => $judul,
		'#weight' => 6,
	);	
	
	$form['formdata']['ssttd'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		'#weight' => 7,
	);	

	$form['formdata']['ttd']= array(
		'#type' => 'radios', 
		'#title' => t('Cetak TTD'), 
		'#options' => array(	
			 '1' => t('Cetak'), 	
			 '' => t('Tidak'), 	
		   ),
		'#default_value' => $ttd,
		'#weight' => 8,
	);		
	$form['formdata']['ssjdl'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		'#weight' => 9,
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
		'#weight' => 10,
	);

	
	$form['formdata']['hal1']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Halaman #1', 
		'#attributes'	=> array('style' => 'text-align: right'),
		'#maxlength'    => 10, 
		'#size'         => 20, 
		//'#required'     => !$disabled, 
		'#disabled'     => false, 
		'#default_value'=> $hal1, 
		'#weight' => 11,
	);
	$form['formdata']['tampilkan'] = array (
		'#type' => 'submit',
		'#value' => 'Tampilkan',
		'#weight' => 12,
	);
	$form['formdata']['submit'] = array (
		'#type' => 'submit',
		'#value' => 'Cetak',
		'#weight' => 13,
	); 
	
	return $form;
}

function perdabup_form_submit($form, &$form_state) {
	$revisi = $form_state['values']['revisi'];
	$kodeuk = $form_state['values']['kodeuk'];
	$topmargin = $form_state['values']['topmargin'];
	$hal1 = $form_state['values']['hal1'];
	$perdabup = $form_state['values']['perdabup'];
	$lampiran = $form_state['values']['lampiran'];
	$judul = $form_state['values']['judul'];
	$ttd = $form_state['values']['ttd'];

	if($form_state['clicked_button']['#value'] == $form_state['values']['tampilkan']) 
        $uri = 'apbd/laporan/apbd/perdabup/'.$revisi.'/' . $kodeuk . '/' . $topmargin . '/' . $hal1 . '/' . $perdabup  . '/' . $lampiran . '/' . $judul . '/' . $ttd;
	else	
		$uri = 'apbd/laporan/apbd/perdabup/'.$revisi.'/' . $kodeuk . '/' . $topmargin . '/' . $hal1 . '/' . $perdabup  . '/' . $lampiran . '/' . $judul . '/' . $ttd . '/pdf' ;
	
	drupal_goto($uri);
	
}
?>