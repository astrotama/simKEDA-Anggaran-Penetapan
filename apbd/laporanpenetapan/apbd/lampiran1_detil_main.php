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
	
	$jenis = arg(4);		//** BARU
	$kodeparent = arg(5);
	
	if ($jenis=='') $jenis = 'belanja_jenis_uk';
	
	//drupal_set_message($kodeparent);
		
	//$output = drupal_get_form('lampiran1_detil_form');
	
	$title = 'Detil Lampiran I APBD';
	if ($jenis=='belanja_jenis_uk') {
		$sql = sprintf('select uraian from {jenis} where kodej=\'%s\'', $kodeparent);
		$result = db_query($sql);
		if ($data = db_fetch_object($result)) {
			$title = $data->uraian;
		}
		
		drupal_set_title($title . ' per SKPD');
		
		$output = GenReportFormContent3($kodeparent);
		
	} else if ($jenis=='belanja_obyek_uk') {
		$sql = sprintf('select uraian from {obyek} where kodeo=\'%s\'', $kodeparent);
		$result = db_query($sql);
		if ($data = db_fetch_object($result)) {
			$title = $data->uraian;
		}
		
		drupal_set_title($title . ' per SKPD');
		
		$output = GenReportFormContent4($kodeparent);

	} else if ($jenis=='belanja_rincian_uk') {
		$sql = sprintf('select uraian from {rincianobyek} where kodero=\'%s\'', $kodeparent);
		$result = db_query($sql);
		if ($data = db_fetch_object($result)) {
			$title = $data->uraian;
		}
		
		drupal_set_title($title . ' per SKPD');
		
		$output = GenReportFormContent5($kodeparent);

	} else if ($jenis=='pendapatan_jenis_uk') {
		$sql = sprintf('select uraian from {jenis} where kodej=\'%s\'', $kodeparent);
		$result = db_query($sql);
		if ($data = db_fetch_object($result)) {
			$title = $data->uraian;
		}
		
		drupal_set_title($title . ' per SKPD');
		
		$output = GenReportFormContent3_Pendapatan($kodeparent);
		
	} else if ($jenis=='pendapatan_obyek_uk') {
		$sql = sprintf('select uraian from {obyek} where kodeo=\'%s\'', $kodeparent);
		$result = db_query($sql);
		if ($data = db_fetch_object($result)) {
			$title = $data->uraian;
		}
		
		drupal_set_title($title . ' per SKPD');
		
		$output = GenReportFormContent4_Pendapatan($kodeparent);

	} else if ($jenis=='pendapatan_rincian_uk') {
		$sql = sprintf('select uraian from {rincianobyek} where kodero=\'%s\'', $kodeparent);
		$result = db_query($sql);
		if ($data = db_fetch_object($result)) {
			$title = $data->uraian;
		}
		
		drupal_set_title($title . ' per SKPD');
		
		$output = GenReportFormContent5_Pendapatan($kodeparent);		
	}
	//$output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
	//$output .= GenReportForm();
	return $output;

}

function GenReportFormContent3($kodej) {
	//drupal_set_message($tingkat);
	$headersrek[] = array (
						 array('data' => 'No',  'width'=> '20px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'SKPD',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Jumlah',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );
	
	
	//****BELANJA
	$totalb = 0;
	$i = 0;
	$sql = sprintf('select u.kodeuk, u.namauk,sum(a.jumlah) jumlahx from {anggperkeg} a inner join {kegiatanskpd} k on a.kodekeg=k.kodekeg inner join {unitkerja} u on k.kodeuk=u.kodeuk where k.inaktif=0 and left(a.kodero,3)=\'%s\' group by u.namauk order by u.namauk', $kodej);
	 
	//drupal_set_message( $sql);
	$result = db_query($sql);
	if ($result) {
		while ($data = db_fetch_object($result)) {
			$i++;
			$totalb += $data->jumlahx;
			
			$namauk = l($data->namauk, 'apbd/laporanpenetapan/apbd/lampiran1keg/' . $kodej . '/' . $data->kodeuk, array('html'=>TRUE));
			
			$rowsrek[] = array (
								 array('data' => $i,  'width'=> '20px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $namauk,  'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => apbd_fn($data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
								 );
				
		}


	}
	$rowsrek[] = array (
						 array('data' => '',  'width'=> '20px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black;border-bottom: 2px solid black;border-top: 1px solid black;'),
						 array('data' => 'JUMLAH',  'style' => ' border-right: 1px solid black; text-align:right; border-bottom: 2px solid black; font-weight:bold;border-top: 1px solid black;'),
						 array('data' => apbd_fn($totalb),  'width' => '90px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
						 );

	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormContent4($kodeo) {
	//drupal_set_message($tingkat);
	$headersrek[] = array (
						 array('data' => 'No',  'width'=> '20px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'SKPD',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Jumlah',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );
	
	
	//****BELANJA
	$totalb = 0;
	$i = 0;
	$sql = sprintf('select u.kodeuk, u.namauk,sum(a.jumlah) jumlahx from {anggperkeg} a inner join {kegiatanskpd} k on a.kodekeg=k.kodekeg inner join {unitkerja} u on k.kodeuk=u.kodeuk where k.inaktif=0 and left(a.kodero,5)=\'%s\' group by u.namauk order by u.namauk', $kodeo);
	
	//drupal_set_message( $sql);
	$result = db_query($sql);
	if ($result) {
		while ($data = db_fetch_object($result)) {
			$i++;
			$totalb += $data->jumlahx;

			$namauk = l($data->namauk, 'apbd/laporanpenetapan/apbd/lampiran1keg/' . $kodeo . '/' . $data->kodeuk, array('html'=>TRUE));
			
			$rowsrek[] = array (
								 array('data' => $i,  'width'=> '20px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $namauk,  'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => apbd_fn($data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
								 );
				
		}


	}
	$rowsrek[] = array (
						 array('data' => '',  'width'=> '20px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black;border-bottom: 2px solid black;border-top: 1px solid black;'),
						 array('data' => 'JUMLAH',  'style' => ' border-right: 1px solid black; text-align:right; border-bottom: 2px solid black; font-weight:bold;border-top: 1px solid black;'),
						 array('data' => apbd_fn($totalb),  'width' => '90px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
						 );

	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormContent5($kodero) {
	//drupal_set_message($tingkat);
	$headersrek[] = array (
						 array('data' => 'No',  'width'=> '20px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'SKPD',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Jumlah',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );
	
	
	//****BELANJA
	$totalb = 0;
	$i = 0;
	$sql = sprintf('select u.kodeuk,u.namauk,sum(a.jumlah) jumlahx from {anggperkeg} a inner join {kegiatanskpd} k on a.kodekeg=k.kodekeg inner join {unitkerja} u on k.kodeuk=u.kodeuk where k.inaktif=0 and a.kodero=\'%s\' group by u.namauk order by u.namauk', $kodero);
	
	//drupal_set_message( $sql);
	$result = db_query($sql);
	if ($result) { 
		while ($data = db_fetch_object($result)) {
			$i++;
			$totalb += $data->jumlahx;
			$namauk = l($data->namauk, 'apbd/laporanpenetapan/apbd/lampiran1keg/' . $kodero . '/' . $data->kodeuk, array('html'=>TRUE));
			$rowsrek[] = array (
								 array('data' => $i,  'width'=> '20px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $namauk,  'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => apbd_fn($data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
								 );
				
		}


	}
	$rowsrek[] = array (
						 array('data' => '',  'width'=> '20px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black;border-bottom: 2px solid black;border-top: 1px solid black;'),
						 array('data' => 'JUMLAH',  'style' => ' border-right: 1px solid black; text-align:right; border-bottom: 2px solid black; font-weight:bold;border-top: 1px solid black;'),
						 array('data' => apbd_fn($totalb),  'width' => '90px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
						 );

	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}


function GenReportFormContent3_Pendapatan($kodej) {
	//drupal_set_message($tingkat);
	$headersrek[] = array (
						 array('data' => 'No',  'width'=> '20px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'SKPD',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Jumlah',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );
	
	
	//****PENDAPATAN
	$totalb = 0;
	$i = 0;
	$sql = sprintf('select u.kodeuk, u.namauk,sum(a.jumlah) jumlahx from {anggperuk} a inner join {unitkerja} u on a.kodeuk=u.kodeuk where left(a.kodero,3)=\'%s\' group by u.namauk order by u.namauk', $kodej);
	 
	//drupal_set_message( $sql);
	$result = db_query($sql);
	if ($result) {
		while ($data = db_fetch_object($result)) {
			$i++;
			$totalb += $data->jumlahx;
			
			$namauk = l($data->namauk, 'apbd/laporanpenetapan/apbd/lampiran1keg/' . $kodej . '/' . $data->kodeuk, array('html'=>TRUE));
			
			$rowsrek[] = array (
								 array('data' => $i,  'width'=> '20px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $namauk,  'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => apbd_fn($data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
								 );
				
		}


	}
	$rowsrek[] = array (
						 array('data' => '',  'width'=> '20px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black;border-bottom: 2px solid black;border-top: 1px solid black;'),
						 array('data' => 'JUMLAH',  'style' => ' border-right: 1px solid black; text-align:right; border-bottom: 2px solid black; font-weight:bold;border-top: 1px solid black;'),
						 array('data' => apbd_fn($totalb),  'width' => '90px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
						 );

	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormContent4_Pendapatan($kodeo) {
	//drupal_set_message($tingkat);
	$headersrek[] = array (
						 array('data' => 'No',  'width'=> '20px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'SKPD',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Jumlah',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );
	
	
	//****PENDAPATAN
	$totalb = 0;
	$i = 0;
	$sql = sprintf('select u.kodeuk, u.namauk,sum(a.jumlah) jumlahx from {anggperuk} a inner join {unitkerja} u on a.kodeuk=u.kodeuk where left(a.kodero,5)=\'%s\' group by u.namauk order by u.namauk', $kodeo);
	 
	//drupal_set_message( $sql);
	$result = db_query($sql);
	if ($result) {
		while ($data = db_fetch_object($result)) {
			$i++;
			$totalb += $data->jumlahx;
			
			$namauk = l($data->namauk, 'apbd/laporanpenetapan/apbd/lampiran1keg/' . $kodej . '/' . $data->kodeuk, array('html'=>TRUE));
			
			$rowsrek[] = array (
								 array('data' => $i,  'width'=> '20px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $namauk,  'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => apbd_fn($data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
								 );
				
		}


	}
	$rowsrek[] = array (
						 array('data' => '',  'width'=> '20px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black;border-bottom: 2px solid black;border-top: 1px solid black;'),
						 array('data' => 'JUMLAH',  'style' => ' border-right: 1px solid black; text-align:right; border-bottom: 2px solid black; font-weight:bold;border-top: 1px solid black;'),
						 array('data' => apbd_fn($totalb),  'width' => '90px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
						 );

	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormContent5_Pendapatan($kodero) {
	//drupal_set_message($tingkat);
	$headersrek[] = array (
						 array('data' => 'No',  'width'=> '20px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'SKPD',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Jumlah',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );
	
	
	//****PENDAPATAN
	$totalb = 0;
	$i = 0;
	$sql = sprintf('select u.kodeuk, u.namauk,sum(a.jumlah) jumlahx from {anggperuk} a inner join {unitkerja} u on a.kodeuk=u.kodeuk where a.kodero=\'%s\' group by u.namauk order by u.namauk', $kodero);
	 
	//drupal_set_message( $sql);
	$result = db_query($sql);
	if ($result) {
		while ($data = db_fetch_object($result)) {
			$i++;
			$totalb += $data->jumlahx;
			
			//$namauk = l($data->namauk, 'apbd/laporanpenetapan/apbd/lampiran1keg/' . $kodej . '/' . $data->kodeuk, array('html'=>TRUE));
			
			$rowsrek[] = array (
								 array('data' => $i,  'width'=> '20px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $data->namauk,  'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => apbd_fn($data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
								 );
				
		}


	}
	$rowsrek[] = array (
						 array('data' => '',  'width'=> '20px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black;border-bottom: 2px solid black;border-top: 1px solid black;'),
						 array('data' => 'JUMLAH',  'style' => ' border-right: 1px solid black; text-align:right; border-bottom: 2px solid black; font-weight:bold;border-top: 1px solid black;'),
						 array('data' => apbd_fn($totalb),  'width' => '90px', 'style' => ' border-right: 1px solid black; border-top: 1px solid black;border-bottom: 2px solid black; text-align:right;font-weight:bold;'),
						 );

	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}


?>