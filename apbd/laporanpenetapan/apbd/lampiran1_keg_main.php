<?php
function lampiran1_keg_main() {
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
	
	$koderek = arg(4);		//** BARU
	$kodeuk = arg(5);
	
	//$output = drupal_get_form('lampiran1_keg_form');
	
	$title = 'Detil Lampiran I APBD';
	if (strlen($koderek)==3) {
		$sql = sprintf('select uraian from {jenis} where kodej=\'%s\'', $koderek);
		$result = db_query($sql);
		if ($data = db_fetch_object($result)) {
			$title = $data->uraian;
		}
		
		
	} else if (strlen($koderek)==5) {
		$sql = sprintf('select uraian from {obyek} where kodeo=\'%s\'', $koderek);
		$result = db_query($sql);
		if ($data = db_fetch_object($result)) {
			$title = $data->uraian;
		}

	} else if (strlen($koderek)==8) {
		$sql = sprintf('select uraian from {rincianobyek} where kodero=\'%s\'', $koderek);
		$result = db_query($sql);
		if ($data = db_fetch_object($result)) {
			$title = $data->uraian;
		}
		
	}

	$sql = sprintf('select namasingkat from {unitkerja} where kodeuk=\'%s\'', $kodeuk);
	$result = db_query($sql);
	if ($data = db_fetch_object($result)) {
		$title .= ' (' . $data->namasingkat . ')';
	}
	
	drupal_set_title($title);
	
	$output = genReportBelanjaSKPD($koderek, $kodeuk);

	return $output;

}



function genReportBelanjaSKPD($koderek, $kodeuk) {
	//set_time_limit(0);
	//ini_set('memory_limit', '640M');
	
	$headersrek[] = array (
						 array('data' => 'NO',  'width'=> '25px','style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right;'),
						 array('data' => 'KEGIATAN', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
						 array('data' => 'REKENING', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
						 array('data' => 'JUMLAH',  'width' => '125px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
						 );
	
	$total=0;
	 
						 
	//REKENING
	if (strlen($koderek)==3) {
		$sql_rek = sprintf(' and left(k.kodero,3)=\'%s\'', $koderek);
		
	} else if (strlen($koderek)==5) {
		$sql_rek = sprintf(' and left(k.kodero,5)=\'%s\'', $koderek);

	} else if (strlen($koderek)==8) {
		$sql_rek = sprintf(' and k.kodero=\'%s\'', $koderek);
	
	}
	
	
	$sql = 'select keg.kodekeg, keg.kegiatan, keg.total, k.kodero, ro.uraian, k.jumlah from {anggperkeg} k inner join {kegiatanskpd} keg on k.kodekeg=keg.kodekeg inner join {rincianobyek} ro on k.kodero=ro.kodero where keg.inaktif=0 and keg.kodeuk=\'%s\'';
	$fsql = sprintf($sql, $kodeuk);
	$fsql .= $sql_rek . ' order by keg.kegiatan, ro.uraian';
	
	//drupal_set_message( $fsql);
	$result= db_query($fsql);
	$no = 0;
	if ($result) {
		while ($data = db_fetch_object($result)) {
			$total += $data->jumlah;
			$no++;
			//font-style: italic;
			
			//apbdkegrekening/201758025022/52101002
			
			//apbd/kegiatanskpd/rekening
			
			$kegiatan = l($data->kegiatan, 'apbd/kegiatanskpd/rekening/' . $data->kodekeg, array('html' =>TRUE));
			
			$uraian = l($data->uraian, 'apbdkegrekening/' . $data->kodekeg . '/' . $data->kodero , array('html' =>TRUE));
			
			$rowsrek[] = array (
							 array('data' => $no,  'width'=> '25px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
							 array('data' => $kegiatan , 'style' => ' border-right: 1px solid black; text-align:left;'),
							 array('data' => $uraian,  'style' => ' border-right: 1px solid black; text-align:left;'),
							 array('data' => apbd_fn($data->jumlah),  'width' => '125px', 'style' => ' border-right: 1px solid black; text-align:right;'),
							 );	
							 

		}	//end rekening
		
	}												 
						

	
	$rowsrek[] = array (
						 array('data' => 'TOTAL BELANJA',  'width'=> '775px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($total),  'width' => '125px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),

						 );	
						 
	

	
						 
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	return $output;
	
}




?>