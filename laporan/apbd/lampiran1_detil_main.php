<?php
function lampiran1_detil_main() {
$h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
$h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
drupal_set_html_head($h);
drupal_add_css('files/css/kegiatancam.css');
	
	/*
	$kodeuk = arg(4);
	$tingkat = arg(5);
	$topmargin = arg(6);

	$hal1 = arg(7);
	$exportpdf = arg(8);
	*/
	
	$revisi = arg(4);		//** BARU
	$kodeparent = arg(5);

		
	$output = drupal_get_form('lampiran1_detil_form');

	//$output .= GenReportFormHeader($kodeuk, $tingkat);
	$output .= GenReportFormContent($kodeuk, $tingkat, $revisi);
	$output .= GenReportFormFooter($kodeuk);

	//$output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
	//$output .= GenReportForm();
	return $output;

}

function GenReportFormContent4($kodej, $revisi) {
	//drupal_set_message($tingkat);

	set_time_limit(0);
	ini_set('memory_limit', '640M');
	
	if ($revisi=='9')
		$str_table = '';
	else
		$str_table = $revisi;
	
	$tahun = variable_get('apbdtahun', 0);
	$pquery = sprintf("select uraian from {jenis} where kodej='%s'", db_escape_string($kodej)) ;
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$juduluk = $data->namauk;
	}
	

	
	$rowsjudul[] = array (array ('data'=>'RINGKASAN PERUBAHAN ANGGARAN PENDAPATAN DAN BELANJA DAERAH', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=> $juduluk, 'width'=>'875px', 'colspan'=>'7', 'style' =>'border:none; text-align:center;'));
	$rowsjudul[] = array (array ('data'=> 'TAHUN ANGGARAN ' . $tahun, 'width'=>'875px', 'colspan'=>'7', 'style' =>'border:none; text-align:center;'));
	
	$headersrek[] = array (
						 //array('data' => 'Kode',  'width'=> '75px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 
						 array('data' => 'NOMOR', 'rowspan'=>'2', 'width'=> '60px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'URAIAN', 'rowspan'=>'2', 'width' => '415px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'JUMLAH (Rp)', 'colspan'=>'2', 'width' => '220px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'BERTAMBAH/ BERKURANG', 'colspan'=>'2', 'width' => '180px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),

						 );
	
	$headersrek[] = array (
						 array('data' => 'Sebelum Perubahan',  'width' => '110px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'Setelah Perubahan',  'width' => '110px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'Rupiah',  'width' => '110px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'Persen',  'width' => '70px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 );
						 
	//****PENDAPATAN
	$totalp = 0;$totalpp = 0;$totalpt = 0;
	if ($kodeuk!='00') {
		
		$where = ' where k.kodeuk=\'%s\'';
		$sql = 'select a.kodea,a.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp  from {anggperuk} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea ' . $where;
		$fsql = sprintf($sql, db_escape_string($kodeuk));
		$fsql .= ' group by a.kodea,a.uraian order by a.kodea';
	
	} else {
		$sql = 'select mid(k.kodero,1,1) kodea,a.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp  from {anggperuk} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea ';
		$fsql = $sql . ' group by a.kodea,a.uraian order by a.kodea';
	}
	//drupal_set_message( $fsql);
	$resultakun = db_query($fsql);
	if ($resultakun) {
		while ($dataakun = db_fetch_object($resultakun)) {
			$totalp += $dataakun->jumlahx;
			$totalpp += $dataakun->jumlahxp;
			$totalpt += $dataakun->jumlahxp-$dataakun->jumlahx;
			/*
			$rowsrek[] = array (
								 array('data' => $dataakun->kodea,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $dataakun->uraian,  'width' => '415px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahx),  'width' => '110px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahxp),  'width' => '110px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($dataakun->jumlahxp-$dataakun->jumlahx),  'width' => '110px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn2(apbd_hitungpersen($dataakun->jumlahx, $dataakun->jumlahxp)),  'width' => '70px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
								 array('data' => '',  'width' => '170px', 'style' => ' border-right: 1px solid black;'),
								 );
			*/		
			$rowsrek[] = array (
								 array('data' => $dataakun->kodea,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $dataakun->uraian,  'width' => '415px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => '',  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => '',  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => '',  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => '',  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 );
			//KELOMPOK
			if ($kodeuk!='00') {
				$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperuk} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek where kodeuk=\'%s\' and mid(k.kodero,1,1)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($dataakun->kodea));
				$fsql .= ' group by x.kodek,x.uraian order by x.kodek';
				
			} else {
				$sql = 'select mid(k.kodero,1,2) kodek,x.uraian,sum(jumlah) jumlahx,sum(jumlahp) jumlahxp from {anggperuk} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek where mid(k.kodero,1,1)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($dataakun->kodea));
				$fsql .= ' group by x.kodek,x.uraian order by x.kodek';
			}

			//drupal_set_message( $fsql);
			$resultkel = db_query($fsql);
			if ($resultkel) {
				while ($datakel = db_fetch_object($resultkel)) {
					$rowsrek[] = array (
										 array('data' => $datakel->kodek,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datakel->uraian,  'width' => '415px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahx),  'width' => '110px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black;text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahxp),  'width' => '110px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahxp-$datakel->jumlahx),  'width' => '110px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn2(apbd_hitungpersen($datakel->jumlahx, $datakel->jumlahxp)),  'width' => '70px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
										 );		


					//JENIS
					if ($kodeuk!='00') {
						$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx ,sum(jumlahp) jumlahxp from {anggperuk} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej where kodeuk=\'%s\' and mid(k.kodero,1,2)=\'%s\'';
						$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datakel->kodek));
						$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
						
					} else {
						$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx ,sum(jumlahp) jumlahxp from {anggperuk} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej where mid(k.kodero,1,2)=\'%s\'';
						$fsql = sprintf($sql, db_escape_string($datakel->kodek));
						$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
					}
					
					//drupal_set_message( $fsql);
					$result = db_query($fsql);
					if ($result) {
						while ($data = db_fetch_object($result)) {
							
							if ($tingkat==3) {
								$uraianj = ucfirst(strtolower($data->uraian));
								$bold ='';
							} else {
								$uraianj = $data->uraian;
								$bold ='font-weight:bold;';
							}
							
							$rowsrek[] = array (
												 array('data' => ($data->kodej),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
												 array('data' => $uraianj,  'width' => '415px', 'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => apbd_fn($data->jumlahx),  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;' . $bold),
												 array('data' => apbd_fn($data->jumlahxp),  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;' . $bold),
												 array('data' => apbd_fn($data->jumlahxp-$data->jumlahx),  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;' . $bold),
												 array('data' => apbd_fn2(apbd_hitungpersen($data->jumlahx, $data->jumlahxp)),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;' . $bold),
												 );
												 
							//OBYEK
							if ($tingkat>=4) {
								if ($kodeuk!='00') {
									$sql = 'select r.kodeo,r.uraian,sum(jumlah) jumlahx ,sum(jumlahp) jumlahxp from {anggperuk} k inner join {obyek} r on mid(k.kodero,1,5)=r.kodeo where kodeuk=\'%s\' and mid(k.kodero,1,3)=\'%s\'';
									$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($data->kodej));
									$fsql .= ' group by r.kodeo,r.uraian order by r.kodeo';
									
								} else {
									$sql = 'select r.kodeo,r.uraian,sum(jumlah) jumlahx ,sum(jumlahp) jumlahxp from {anggperuk} k inner join {obyek} r on mid(k.kodero,1,5)=r.kodeo where mid(k.kodero,1,3)=\'%s\'';
									$fsql = sprintf($sql, db_escape_string($data->kodej));
									$fsql .= ' group by r.kodeo,r.uraian order by r.kodeo';
								}
								
								$resulto = db_query($fsql);
								if ($resulto) {
									while ($datao = db_fetch_object($resulto)) {
										$rowsrek[] = array (
															 array('data' => apbd_format_rek_obyek($datao->kodeo),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
															 array('data' => $datao->uraian,  'width' => '415px', 'style' => ' border-right: 1px solid black; text-align:left;'),
															 array('data' => apbd_fn($datao->jumlahx),  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;'),
															 array('data' => apbd_fn($datao->jumlahxp),  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;'),
															 array('data' => apbd_fn($datao->jumlahxp-$datao->jumlahx),  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;'),
															 array('data' => apbd_fn2(apbd_hitungpersen($datao->jumlahx, $datao->jumlahxp)),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;'),
															 );	
										
										//RINCIAN OBYEK
										if ($tingkat>=5) {
											if ($kodeuk!='00') {
												$sql = 'select r.kodero,r.uraian,sum(jumlah) jumlahx ,sum(jumlahp) jumlahxp from {anggperuk} k inner join {rincianobyek} r on k.kodero=r.kodero where kodeuk=\'%s\' and mid(k.kodero,1,5)=\'%s\'';
												$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datao->kodeo));
												$fsql .= ' order by r.kodero';
												
											} else {
												$sql = 'select r.kodero,r.uraian,sum(jumlah) jumlahx ,sum(jumlahp) jumlahxp from {anggperuk} k inner join {rincianobyek} r on k.kodero=r.kodero where mid(k.kodero,1,5)=\'%s\'';
												$fsql = sprintf($sql, db_escape_string($datao->kodeo));
												$fsql .= ' group by r.kodero,r.uraian order by r.kodero';
											}
											
											$resultro = db_query($fsql);
											if ($resultro) {
												while ($dataro = db_fetch_object($resultro)) {
													$rowsrek[] = array (
																		 array('data' => apbd_format_rek_rincianobyek($dataro->kodero),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																		 array('data' => $dataro->uraian,  'width' => '415px', 'style' => ' border-right: 1px solid black; text-align:left;font-style: italic;'),
																		 array('data' => apbd_fn($dataro->jumlahx),  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																		 array('data' => apbd_fn($dataro->jumlahxp),  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																		 array('data' => apbd_fn($dataro->jumlahxp-$dataro->jumlahx),  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																		 array('data' => apbd_fn2(apbd_hitungpersen($dataro->jumlahx, $dataro->jumlahxp)),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																		 );	
													
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
								 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; '),
								 array('data' => 'JUMLAH PENDAPATAN',  'width' => '415px', 'style' => ' border-right: 1px solid black; text-align:right; font-weight:bold;'),
								 array('data' => apbd_fn($totalp),  'width' => '110px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($totalpp),  'width' => '110px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($totalpt),  'width' => '110px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
								array('data' => apbd_fn2(apbd_hitungpersen($totalp, $totalpp)),  'width' => '70px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
								 );
			

			$rowsrek[] = array (
								 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; '),
								 array('data' => '',  'width' => '415px', 'style' => ' border-right: 1px solid black; text-align:right; font-weight:bold;'),
								 array('data' => '',  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => '',  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => '',  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
								array('data' => '',  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
								 );
			
		}
		
	}
	
	
	
	//****BELANJA
	$totalb = 0;$totalbp = 0;$totalbt = 0;
	if ($kodeuk!='00') {
		$where = ' and g.kodeuk=\'%s\'';
		$sql = 'select a.kodea,a.uraian,sum(jumlah) jumlahx, sum(jumlahp) jumlahxp from {anggperkegperubahan' . $str_table . '} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea inner join {kegiatanperubahan' . $str_table . '} g on k.kodekeg=g.kodekeg where g.inaktif=0 ' . $where;
		$fsql = sprintf($sql, db_escape_string($kodeuk));
		$fsql .= ' group by a.kodea,a.uraian order by a.kodea';
	
	} else {
		$sql = 'select a.kodea,a.uraian,sum(jumlah) jumlahx, sum(jumlahp) jumlahxp from {anggperkegperubahan' . $str_table . '} k  inner join {anggaran} a on mid(k.kodero,1,1)=a.kodea inner join {kegiatanperubahan' . $str_table . '} g on k.kodekeg=g.kodekeg ';
		$fsql = $sql . ' where g.inaktif=0 group by a.kodea,a.uraian order by a.kodea';
		
	}
	
	//drupal_set_message( $sql);
	$resultakun = db_query($fsql);
	if ($resultakun) {
		while ($dataakun = db_fetch_object($resultakun)) {
			$totalb += $dataakun->jumlahx;
			$totalbp += $dataakun->jumlahxp;
			$totalbt += $dataakun->jumlahxp-$dataakun->jumlahx;

			$rowsrek[] = array (
								 array('data' => $dataakun->kodea,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $dataakun->uraian,  'width' => '415px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => '',  'width' => '110px', 'style' => ' border-right: 1px solid black;'),
								 array('data' => '',  'width' => '110px', 'style' => ' border-right: 1px solid black;'),
								 array('data' => '',  'width' => '110px', 'style' => ' border-right: 1px solid black;'),
								 array('data' => '',  'width' => '70px', 'style' => ' border-right: 1px solid black;'),
								 );
			//KELOMPOK
			if ($kodeuk!='00') {
				$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx, sum(jumlahp) jumlahxp from {anggperkegperubahan' . $str_table . '} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek inner join {kegiatanperubahan' . $str_table . '} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.kodeuk=\'%s\' and mid(k.kodero,1,1)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($dataakun->kodea));
				$fsql .= ' group by x.kodek,x.uraian order by x.kodek';
			
			} else {
				$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx, sum(jumlahp) jumlahxp from {anggperkegperubahan' . $str_table . '} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek inner join {kegiatanperubahan' . $str_table . '} g on k.kodekeg=g.kodekeg where g.inaktif=0 and mid(k.kodero,1,1)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($dataakun->kodea));
				$fsql .= ' group by x.kodek,x.uraian order by  x.kodek';
			}
			//drupal_set_message( $fsql);
			$resultkel = db_query($fsql);
			if ($resultkel) {
				while ($datakel = db_fetch_object($resultkel)) {
					$rowsrek[] = array (
										 array('data' => $datakel->kodek,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datakel->uraian,  'width' => '415px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahx),  'width' => '110px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahxp),  'width' => '110px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn($datakel->jumlahxp-$datakel->jumlahx),  'width' => '110px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
										 array('data' => apbd_fn2(apbd_hitungpersen($datakel->jumlahx, $datakel->jumlahxp)),  'width' => '70px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
										 );		


					//JENIS
					if ($kodeuk!='00') { 
						$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx, sum(jumlahp) jumlahxp from {anggperkegperubahan' . $str_table . '} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej  inner join {kegiatanperubahan' . $str_table . '} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.kodeuk=\'%s\' and mid(k.kodero,1,2)=\'%s\'';
						$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datakel->kodek));
						$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
					
					} else {
						$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx, sum(jumlahp) jumlahxp from {anggperkegperubahan' . $str_table . '} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej  inner join {kegiatanperubahan' . $str_table . '} g on k.kodekeg=g.kodekeg where g.inaktif=0 and mid(k.kodero,1,2)=\'%s\'';
						$fsql = sprintf($sql, db_escape_string($datakel->kodek));
						$fsql .= ' group by j.kodej,j.uraian order by mid(k.kodero,1,3)';
					}
					//drupal_set_message( $fsql);
					$result = db_query($fsql);
					if ($result) {
						while ($data = db_fetch_object($result)) {
							if ($tingkat==3) {
								$uraianj = ucfirst(strtolower($data->uraian));
								$bold ='';
							} else {
								$uraianj = $data->uraian;
								$bold ='font-weight:bold;';
							}
							
							$rowsrek[] = array (
												 array('data' => ($data->kodej),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
												 array('data' => $uraianj,  'width' => '415px', 'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => apbd_fn($data->jumlahx),  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;' . $bold),
												 array('data' => apbd_fn($data->jumlahxp),  'width' => '110px', 'style' => 'border-right: 1px solid black; text-align:right;' . $bold),
												 array('data' => apbd_fn($data->jumlahxp-$data->jumlahx),  'width' => '110px', 'style' => 'border-right: 1px solid black; text-align:right;' . $bold),
												 array('data' => apbd_fn2(apbd_hitungpersen($data->jumlahx, $data->jumlahxp)),  'width' => '70px', 'style' => 'border-right: 1px solid black; text-align:right;' . $bold),
												 );
							
							//OBYEK
							if ($tingkat>=4) {
								if ($kodeuk!='00') { 
									$sql = 'select r.kodeo,r.uraian,sum(jumlah) jumlahx, sum(jumlahp) jumlahxp from {anggperkegperubahan' . $str_table . '} k inner join {obyek} r on mid(k.kodero,1,5)=r.kodeo inner join {kegiatanperubahan' . $str_table . '} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.kodeuk=\'%s\' and mid(k.kodero,1,3)=\'%s\'';
									$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($data->kodej));
									$fsql .= ' group by r.kodeo,r.uraian order by r.kodeo';
								
								} else {
									$sql = 'select r.kodeo,r.uraian,sum(jumlah) jumlahx, sum(jumlahp) jumlahxp from {anggperkegperubahan' . $str_table . '} k inner join {obyek} r on mid(k.kodero,1,5)=r.kodeo inner join {kegiatanperubahan' . $str_table . '} g on k.kodekeg=g.kodekeg where g.inaktif=0 and mid(k.kodero,1,3)=\'%s\'';
									$fsql = sprintf($sql, db_escape_string($data->kodej));
									$fsql .= ' group by r.kodeo,r.uraian order by r.kodeo';
								}
								$resulto = db_query($fsql);
								if ($resulto) {
									while ($datao = db_fetch_object($resulto)) {
										$rowsrek[] = array (
															 array('data' => apbd_format_rek_obyek($datao->kodeo),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
															 array('data' => $datao->uraian,  'width' => '415px', 'style' => ' border-right: 1px solid black; text-align:left;'),
															 array('data' => apbd_fn($datao->jumlahx),  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;'),
															 array('data' => apbd_fn($datao->jumlahxp),  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;'),
															 array('data' => apbd_fn($datao->jumlahxp-$datao->jumlahx),  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;'),
															 array('data' => apbd_fn2(apbd_hitungpersen($datao->jumlahx, $datao->jumlahxp)),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;'),
															 );	

										//RINCIAN OBYEK 
										if ($tingkat>=5) {
											if ($kodeuk!='00') { 
												$sql = 'select r.kodero,r.uraian,sum(jumlah) jumlahx, sum(jumlahp) jumlahxp from {anggperkegperubahan' . $str_table . '} k inner join {rincianobyek} r on k.kodero=r.kodero inner join {kegiatanperubahan' . $str_table . '} g on k.kodekeg=g.kodekeg where g.inaktif=0 and g.kodeuk=\'%s\' and mid(k.kodero,1,5)=\'%s\'';
												$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($datao->kodeo));
												$fsql .= ' group by r.kodero,r.uraian order by r.kodero';
											
											} else {
												$sql = 'select r.kodero,r.uraian,sum(jumlah) jumlahx, sum(jumlahp) jumlahxp from {anggperkegperubahan' . $str_table . '} k inner join {rincianobyek} r on k.kodero=r.kodero inner join {kegiatanperubahan' . $str_table . '} g on k.kodekeg=g.kodekeg where g.inaktif=0 and mid(k.kodero,1,5)=\'%s\'';
												$fsql = sprintf($sql, db_escape_string($datao->kodeo));
												$fsql .= ' group by r.kodero,r.uraian order by r.kodero';
											} 
											//drupal_set_message($fsql);
											$resultro = db_query($fsql);
											if ($resultro) {
												while ($dataro = db_fetch_object($resultro)) {
													$rowsrek[] = array (
																		 array('data' => apbd_format_rek_rincianobyek($dataro->kodero),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																		 array('data' => $dataro->uraian,  'width' => '415px', 'style' => ' border-right: 1px solid black; text-align:left;font-style: italic;'),
																		 array('data' => apbd_fn($dataro->jumlahx),  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																		 array('data' => apbd_fn($dataro->jumlahxp),  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																		 array('data' => apbd_fn($dataro->jumlahxp-$dataro->jumlahx),  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																		 array('data' => apbd_fn2(apbd_hitungpersen($dataro->jumlahx, $dataro->jumlahxp)),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																		 );	
													
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
		}
		
		
		$rowsrek[] = array (
							 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; '),
							 array('data' => 'JUMLAH BELANJA',  'width' => '415px', 'style' => ' border-right: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn($totalb),  'width' => '110px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
							 array('data' => apbd_fn($totalbp),  'width' => '110px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
							 array('data' => apbd_fn($totalbt),  'width' => '110px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
							 array('data' => apbd_fn2(apbd_hitungpersen($totalb, $totalbp)),  'width' => '70px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
							 );
		
	}
	$rowsrek[] = array (
						 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; '),
						 array('data' => 'SURPLUS / DEFISIT',  'width' => '415px', 'style' => ' border-right: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($totalp-$totalb),  'width' => '110px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
						 array('data' => apbd_fn($totalpp-$totalbp),  'width' => '110px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
						 array('data' => apbd_fn($totalpt-$totalbt),  'width' => '110px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
						 array('data' => apbd_fn2(apbd_hitungpersen($totalp-$totalb, $totalpp-$totalbp)),  'width' => '70px', 'style' => 'border-right: 1px solid black; border-top: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
						 );

	$rowsrek[] = array (
						 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; '),
						 array('data' => '',  'width' => '415px', 'style' => ' border-right: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => '',  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
						 array('data' => '',  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
						 array('data' => '',  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
						array('data' => '',  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
						 );
						 
	
	//PEMBIAYAAN
	if ($kodeuk=='00') {

		//KELOMPOK
		$rowsrek[] = array (
							 array('data' => '6',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => 'PEMBIAYAAN',  'width' => '415px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
							 array('data' => '',  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
							 array('data' => '',  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
							 array('data' => '',  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
							 array('data' => '',  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
							 );
	
	
		$sql = 'select x.kodek,x.uraian,sum(jumlah) jumlahx, sum(jumlahp) jumlahxp from {anggperda} k inner join {kelompok} x on mid(k.kodero,1,2)=x.kodek';
		$sql .= ' group by x.kodek,x.uraian order by x.kodek';
					
		//drupal_set_message( $sql);
		$resultkel = db_query($sql);
		if ($resultkel) {
			while ($datakel = db_fetch_object($resultkel)) {
				if ($datakel->kodek=='61')
					{
						$totalpm = $datakel->jumlahx;
						$totalpm2 = $datakel->jumlahxp;
						$totalpm3 = $datakel->jumlahxp-$datakel->jumlahx;
					}
				else
				{
					$totalpk = $datakel->jumlahx;
					$totalpk2 = $datakel->jumlahxp;
					$totalpk3 = $datakel->jumlahxp-$datakel->jumlahx;
				}
					
				
				$rowsrek[] = array (
									 array('data' => $datakel->kodek,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
									 array('data' => $datakel->uraian,  'width' => '415px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
									 array('data' => apbd_fn($datakel->jumlahx),  'width' => '110px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
									 array('data' => apbd_fn($datakel->jumlahxp),  'width' => '110px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
									 array('data' => apbd_fn($datakel->jumlahxp-$datakel->jumlahx),  'width' => '110px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
									 array('data' => apbd_fn2(apbd_hitungpersen($datakel->jumlahx, $datakel->jumlahxp)),  'width' => '70px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
									 );		


				//JENIS
				$sql = 'select j.kodej,j.uraian,sum(jumlah) jumlahx, sum(jumlahp) jumlahxp from {anggperda} k inner join {jenis} j on mid(k.kodero,1,3)=j.kodej where mid(k.kodero,1,2)=\'%s\'';
				$fsql = sprintf($sql, db_escape_string($datakel->kodek));
				$fsql .= ' group by j.kodej,j.uraian order by j.kodej';
				
				//drupal_set_message( $fsql);
				$result = db_query($fsql);
				if ($result) {
					while ($data = db_fetch_object($result)) {
						if ($tingkat==3) {
							$uraianj = ucfirst(strtolower($data->uraian));
							$bold ='';
						} else {
							$uraianj = $data->uraian;
							$bold ='font-weight:bold;';
						}						
						$rowsrek[] = array (
											 array('data' => ($data->kodej),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
											 array('data' => $uraianj,  'width' => '415px', 'style' => ' border-right: 1px solid black; text-align:left;'),
											 array('data' => apbd_fn($data->jumlahx),  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;' . $bold),
											 array('data' => apbd_fn($data->jumlahxp),  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;' . $bold),
											 array('data' => apbd_fn($data->jumlahxp-$data->jumlahx),  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;' . $bold),
											 array('data' => apbd_fn2(apbd_hitungpersen($data->jumlahx, $data->jumlahxp)),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;' . $bold),
											 );

						//OBYEK
						if ($tingkat>=4) {
							$sql = 'select r.kodeo,r.uraian,sum(jumlah) jumlahx, sum(jumlahp) jumlahxp from {anggperda} k inner join {obyek} r on mid(k.kodero,1,5)=r.kodeo where mid(k.kodero,1,3)=\'%s\'';
							$fsql = sprintf($sql, db_escape_string($data->kodej));
							$fsql .= ' group by r.kodeo,r.uraian order by r.kodeo';
							
							$resulto = db_query($fsql);
							if ($resulto) {
								while ($datao = db_fetch_object($resulto)) {
									$rowsrek[] = array (
														 array('data' => apbd_format_rek_obyek($datao->kodeo),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
														 array('data' => $datao->uraian,  'width' => '415px', 'style' => ' border-right: 1px solid black; text-align:left;'),
														 array('data' => apbd_fn($datao->jumlahx),  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;'),
														 array('data' => apbd_fn($datao->jumlahxp),  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;'),
														 array('data' => apbd_fn($datao->jumlahxp-$datao->jumlahx),  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;'),
														 array('data' => apbd_fn2(apbd_hitungpersen($datao->jumlahx, $datao->jumlahxp)),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;'),
														 );	
									
									//RINCIAN OBYEK
									if ($tingkat>=5) {
										$sql = 'select r.kodero,r.uraian,sum(jumlah) jumlahx, sum(jumlahp) jumlahxp from {anggperda} k inner join {rincianobyek} r on k.kodero=r.kodero where mid(k.kodero,1,5)=\'%s\'';
										$fsql = sprintf($sql, db_escape_string($datao->kodeo));
										$fsql .= ' group by r.kodero,r.uraian order by r.kodero';
										
										$resultro = db_query($fsql);
										if ($resultro) {
											while ($dataro = db_fetch_object($resultro)) {

												$rowsrek[] = array (
																	 array('data' => apbd_format_rek_rincianobyek($dataro->kodero),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																	 array('data' => $dataro->uraian,  'width' => '415px', 'style' => ' border-right: 1px solid black; text-align:left;font-style: italic;'),
																	 array('data' => apbd_fn($dataro->jumlahx),  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																	 array('data' => apbd_fn($dataro->jumlahxp),  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																	 array('data' => apbd_fn($dataro->jumlahxp-$dataro->jumlahx),  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																	 array('data' => apbd_fn2(apbd_hitungpersen($dataro->jumlahx, $dataro->jumlahxp)),  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
																	 );	
														
											}
											
										}
										
									}
								}
							}
						}
											 
					}		//END JENIS
				}										 
									 
			////////
			}
		}			

		$rowsrek[] = array (
							 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; '),
							 array('data' => 'PEMBIAYAAN NETTO',  'width' => '415px', 'style' => ' border-right: 1px solid black; text-align:right; font-weight:bold;'),
							 array('data' => apbd_fn($totalpm-$totalpk),  'width' => '110px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
							 array('data' => apbd_fn($totalpm2-$totalpk2),  'width' => '110px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
							 array('data' => apbd_fn($totalpm3-$totalpk3),  'width' => '110px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
							 array('data' => apbd_fn2(apbd_hitungpersen($totalpm-$totalpk, $totalpm2-$totalpk2)),  'width' => '70px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
							 );

		$rowsrek[] = array (
							 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; '),
							 array('data' => 'SISA LEBIH ANGGARAN TAHUN BERKENAAN',  'width' => '415px', 'style' => ' border-right: 1px solid black; font-weight:bold;'),
							 array('data' => apbd_fn($totalp-$totalb+$totalpm-$totalpk),  'width' => '110px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
							 array('data' => apbd_fn($totalpp-$totalbp+$totalpm2-$totalpk2),  'width' => '110px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
							 array('data' => apbd_fn($totalpt-$totalbt+$totalpm3-$totalpk3),  'width' => '110px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
							 array('data' => apbd_fn2(apbd_hitungpersen($totalp-$totalb+$totalpm-$totalpk, $totalpp-$totalbp+$totalpm2-$totalpk2)),  'width' => '70px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
							 );
							 
							 
	}	//END PEMBIAYAAN	
	
	$rowsrek[] = array (
						 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; '),
						 array('data' => '',  'width' => '415px', 'style' => ' border-right: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => '',  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
						 array('data' => '',  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
						 array('data' => '',  'width' => '110px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
						array('data' => '',  'width' => '70px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
						 );
						 
	$opttb0 = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0`');
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '1');
	$headerkosong = array();
	
	if ($kodeuk=='00') $output = theme_box('', apbd_theme_table($headerkosong, $rowslampiran, $opttb0));
	$output .= theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttb0));
	
	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	if ($limit >0)
		$output .= theme ('pager', NULL, $limit, 0);
	
	return $output;
	
	
}


function lampiran1_detil_form () {
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Paramater Laporan',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);
	

	
	$revisi = arg(4);		//** BARU
	$kodeparent = arg(5);

	$form['formdata']['revisi']= array(
		'#type'         => 'value', 
		'#value'        => $revisi,
	);
	
	$form['formdata']['kodeuk']= array(
		'#type'         => $type, 
		'#title'        => 'SKPD',
		'#options'	=> $dinas,
		'#description'  => 'SKPD yang akan ditampilkan/dicetak', 
		//'#maxlength'    => 60, 
		'#width'         => 30, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeuk, 
		'#weight' => 2,
	);
	

	$form['formdata']['tampilkan'] = array (
		'#type' => 'submit',
		'#value' => 'Tampilkan',
		'#weight' => 8,
	);
	$form['formdata']['submit'] = array (
		'#type' => 'submit',
		'#value' => 'Cetak',
		'#weight' => 9,
	); 
	
	return $form;
}

function lampiran1_detil_form_submit($form, &$form_state) {
	//$kodeuk = $form_state['values']['kodeuk'];
	
	$revisi = $form_state['values']['revisi'];		//BARU
	$kodeuk = $form_state['values']['kodeuk'];
	
	drupal_goto($uri);
	
}
?>