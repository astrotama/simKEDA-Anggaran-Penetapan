<?php
function kegiatanskpd_analisa_main($arg=NULL, $nama=NULL) {
$h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
$h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
drupal_set_html_head($h);
drupal_add_css('files/css/kegiatancam.css');
	
	$exportpdf = arg(2);
	//drupal_set_message(arg(2));
	
	if (isset($exportpdf))  {
		if ($exportpdf=='pdf') {
			$topmargin = 10;
			$pdfFile = 'analisa-belanja.pdf';
			$htmlContent = genReportBelanjaSKPD('2');
			apbd_ExportPDF3P_CF($topmargin,$topmargin, '', $htmlContent, '', $pdfFile, '1');
			
		} else {
		}
			
		
	} else {
		$output = drupal_get_form('kegiatanskpd_analisa_form');

		//$output .= genReportBelanjaSKPD('2');
		return $output;
	}

}
 
function kegiatanskpd_analisa_form () {

	
	$form['formdata']['submit'] = array (
		'#type' => 'submit',
		'#value' => 'Cetak'
	);
	$form['formdata']['submitexcel'] = array (
		'#type' => 'submit',
		'#value' => 'Excel'
	);
	return $form;
}

function kegiatanskpd_analisa_form_submit($form, &$form_state) {
	//$kodeuk = $form_state['values']['kodeuk'];

	$uri = 'apbd/kegiatananalisa/pdf' ;
	
	if ($form_state['clicked_button']['#value'] == $form_state['values']['submit']) 
		$uri .= 'pdf';
	else
		$uri .= 'excel';
	
	drupal_goto($uri);
	
}

function genReportBelanjaSKPD($jenis) {
	set_time_limit(0);
	ini_set('memory_limit', '640M');
	
	$headersrek[] = array (
						 array('data' => 'KODE',  'width'=> '60px','style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
						 array('data' => 'URAIAN', 'width' => '600px', 'colspan'=>'5',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
						 array('data' => 'KEGIATAN', 'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
						 array('data' => 'JUMLAH (Rp)',  'width' => '125px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
						 );

	$where = ' where k.kodeuk=\'%s\'';
	
	$total=0;
	 
						 
	//REKENING
	$sql = 'select k.kodero,r.uraian,sum(jumlah) jumlahx from {anggperkeg} k left join {rincianobyek} r on k.kodero=r.kodero  inner join {kegiatanskpd} keg on k.kodekeg=keg.kodekeg where keg.inaktif=0  and keg.jenis=\'%s\'';
	$fsql = sprintf($sql, $jenis);
	$fsql .= ' group by k.kodero,r.uraian order by k.kodero';
		
	
	//drupal_set_message( $fsql);
	$result= db_query($fsql);
	if ($result) {
		while ($data = db_fetch_object($result)) {
			$total += $data->jumlahx;
			//font-style: italic;
			
			$uraian = l($data->uraian, 'node/299/' . $data->kodero , array('html' =>TRUE));
			
			$rowsrek[] = array (
							 array('data' => apbd_format_rek_rincianobyek($data->kodero),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => $uraian, 'width' => '600px', 'colspan'=>'5',  'style' => ' border-right: 1px solid black; text-align:left;'),
							 array('data' => '', 'width' => '90px',  'style' => ' border-right: 1px solid black; text-align:left;'),
							 array('data' => apbd_fn($data->jumlahx),  'width' => '125px', 'style' => ' border-right: 1px solid black; text-align:right;'),
							 );	
							 
			//DETIL SKPD 
			$no = 0;
			$sql = 'select u.kodeuk, u.namauk,sum(k.jumlah) jumlahx from {anggperkeg} k  inner join {kegiatanskpd} keg on k.kodekeg=keg.kodekeg inner join {unitkerja} u on keg.kodeuk=u.kodeuk where keg.inaktif=0 and k.kodero=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($data->kodero));
			$fsql .= ' group by u.kodeuk, u.namauk order by sum(k.jumlah) desc'; 
			//drupal_set_message($fsql);
			$resdetil= db_query($fsql);
			if ($resdetil) {
				while ($datadetil = db_fetch_object($resdetil)) {
					$no++;
					
					$numkeg = 0;
					$sql = 'select count(kodekeg) numkeg from {kegiatanskpd} where kodeuk=\'%s\' and inaktif=0 and kodekeg in (select kodekeg from {anggperkeg} where kodero=\'%s\')';
					$fsql = sprintf($sql, db_escape_string($datadetil->kodeuk), db_escape_string($data->kodero));
					$reskeg = db_query($fsql);
					if ($reskeg) {
						if ($datakeg = db_fetch_object($reskeg)) {
							$numkeg = $datakeg->numkeg;
							}
					}	
					
					$skpd = l($datadetil->namauk, 'node/299/' . $data->kodero . '/' . $datadetil->kodeuk , array('html' =>TRUE));
					
					$rowsrek[] = array (
									 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
									 array('data' => $no . '.',  'width'=> '50px', 'style' => 'text-align:right;'),
									 array('data' => $skpd, 'width' => '550px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;font-style: italic;'),
									 array('data' => $numkeg, 'width' => '90px',  'style' => ' border-right: 1px solid black; text-align:right;'),
									 array('data' => apbd_fn($datadetil->jumlahx),  'width' => '125px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
								);												
				}	//end detil skpd		
			}
			
		}	//end rekening
		
	}												 
						

	
	$rowsrek[] = array (
						 array('data' => 'TOTAL BELANJA',  'width'=> '750px',  'colspan'=>'7',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($total),  'width' => '125px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),

						 );	
						 
	

	
						 
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	return $output;
	
}


?>

