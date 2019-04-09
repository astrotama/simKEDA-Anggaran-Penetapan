<?php
    
function importpendapatan_post_form(){
    drupal_add_css('files/css/kegiatancam.css');		

    $kodero = arg(2);
	$kodeuk = arg(3);
	
	$sql = 'select uraian from {rincianobyek} where kodero=\'%s\'';
	$res = db_query(db_rewrite_sql($sql), array ($kodero));
	if ($res) {
		$data = db_fetch_object($res);
		if ($data) {
			$rekening = $data->uraian;

		} 
	} 
	$sql = 'select uraian from {rincianobyek} where kodero=\'%s\'';

	drupal_set_title('Import RKA-SKPD ');


	$form['kodeuk']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $kodeuk, 
	); 
	$form['kodero']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $kodero, 
	); 

	$form['kegiatan']= array(
		'#type'         => 'markup', 
		'#value'		=> '<p>Import RKA-SKPD Rekening <strong style="font-size:15px">' . $rekening . '</strong> dari tahun sebelumnya.</p>', 
	); 
	$form['peringatan']= array(
		'#type'         => 'markup', 
		'#value'		=> '<p style="color:red">Perhatian : Proses import akan menghapus <strong>SEMUA</strong> isian RKA yang sudah diisikan sebelumnya.</p>', 
	); 
	
	if ($kodero=='AA') {
		$form['kegiatanrek']= array(
			'#type'         => 'markup', 
			'#value'		=> GenReportFormContentAll($kodeuk), 
		); 
	} else {
		$form['kegiatanrek']= array(
			'#type'         => 'markup', 
			'#value'		=> GenReportFormContent($kodero, $kodeuk), 
		); 
	}
	
    $form['submit'] = array (
        '#type' => 'submit',
		//'#weight'     => 99, 
		'#suffix' => "&nbsp;<a href='/apbd/pendapatan' class='btn_blue' style='color: white'>Tutup</a>",
		'#value' => 'Import Rekening'
    );
    
    return $form;
}

function importpendapatan_post_form_submit($form, &$form_state) {

	//if($form_state['clicked_button']['#value'] == $form_state['values']['submitnext']) {
		
	$kodero = $form_state['values']['kodero'];
	$kodeuk = $form_state['values']['kodeuk'];
	
	if ($kodero=='AA') {
		$fsql = sprintf('select kodero from {anggperukperubahan} where kodeuk=\'%s\'', db_escape_string($kodeuk));
		$result = db_query($fsql);
		if ($result) {
			while ($data = db_fetch_object($result)) {
				importRekening($kodeuk, $data->kodero);
			}
		}
		
	} else {
		importRekening($kodeuk, $kodero);
	}
	
	
    drupal_goto('apbd/pendapatan');    
}


function importRekening($kodeuk, $kodero) {
	//delete first
	$fsql = sprintf('delete from {anggperukdetilsub} where iddetil in (select iddetil from {anggperukdetil} where kodero=\'%s\' and kodeuk=\'%s\')', db_escape_string($kodero), db_escape_string($kodeuk));
	//drupal_set_message($fsql);
	$res = db_query($fsql);	
	$fsql = sprintf('delete from {anggperukdetil} where kodero=\'%s\' and kodeuk=\'%s\'', db_escape_string($kodero), db_escape_string($kodeuk));
	//drupal_set_message($fsql);
	$res = db_query($fsql);
	$fsql = sprintf('delete from {anggperuk} where kodero=\'%s\' and kodeuk=\'%s\'', db_escape_string($kodero), db_escape_string($kodeuk));
	//drupal_set_message($fsql);
	$res = db_query($fsql);
	
	
	
	//anggperuk
	$sql = 'select r.kodero,r.uraian,k.jumlahp , k.ketrekening from {anggperukperubahan} k inner join {rincianobyek} r on k.kodero=r.kodero where k.kodero=\'%s\' and k.kodeuk=\'%s\'';
	$fsql = sprintf($sql, db_escape_string($kodero), db_escape_string($kodeuk));
	$result = db_query($fsql);
	if ($result) {
		while ($data = db_fetch_object($result)) {
			
			$sql = 'insert into {anggperuk} (kodeuk,kodero,uraian,jumlah,jumlahsebelum,jumlahsesudah, ketrekening) 
				   values (\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\')';        
			$res = db_query(db_rewrite_sql($sql), array($kodeuk, $data->kodero,$data->uraian, $data->jumlahp, $data->jumlah, $data->jumlahsesudah, $data->ketrekening));
		
		}
	}		
	
	//anggperukdetil
	$sql = 'select k.kodeuk, k.kodero, k.nourut, k.iddetil, k.uraian, k.uraianp, k.unitjumlahp, k.unitsatuanp, k.volumjumlahp, k.volumsatuanp, k.hargap, k.totalp, k.pengelompokan from {anggperukdetilperubahan} k inner join {rincianobyek} r on k.kodero=r.kodero where k.totalp>0 and k.kodero=\'%s\' and k.kodeuk=\'%s\' order by k.kodero';
	$fsql = sprintf($sql, db_escape_string($kodero), db_escape_string($kodeuk));	
	//drupal_set_message($fsql);
	$result = db_query($fsql);
	if ($result) {
		while ($data = db_fetch_object($result)) {
			
			//drupal_set_message($data->kodero);

			$uraian = $data->uraianp;
			if ($uraian=='') $uraian = $data->uraian;
			
			$sql = 'insert into {anggperukdetil} (kodeuk, kodero, nourut, uraian, unitjumlah, unitsatuan, volumjumlah, volumsatuan, harga, total, pengelompokan) values(\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\')';        
			$res = db_query(db_rewrite_sql($sql), array($kodeuk, $data->kodero, $data->nourut, $uraian, $data->unitjumlahp, $data->unitsatuanp, $data->volumjumlahp, $data->volumsatuanp, $data->hargap, $data->totalp, $data->pengelompokan));	
			
			$iddetil_new = db_last_insert_id('anggperukdetil', 'iddetil');
			
			////drupal_set_message($iddetil_new . '|' . $data->iddetil);
			
			//anggperukdetilsub
			if ($data->pengelompokan) {
				//SUB DETIL
				$sql = 'select idsub,nourut,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan, harga,total from {anggperukdetilsubperubahan} where total>0 and iddetil=\'%s\' order by nourut asc,idsub';
				$fsql = sprintf($sql, db_escape_string($data->iddetil));
				$res_sub = db_query($fsql);
				if ($res_sub) {
					while ($data_sub = db_fetch_object($res_sub)) {

						$sql = 'insert into {anggperukdetilsub} (iddetil, nourut, uraian, unitjumlah, unitsatuan, volumjumlah, volumsatuan, harga, total) values(\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\')';        
						$res = db_query(db_rewrite_sql($sql), array($iddetil_new, $data_sub->nourut, $data_sub->uraian, $data_sub->unitjumlah, $data_sub->unitsatuan, $data_sub->volumjumlah, $data_sub->volumsatuan, $data_sub->harga, $data_sub->total));												
					}
				}
				
			}
		
		}
	}			
}


function GenReportFormContent($kodero, $kodeuk) {
	
	
	$total=0;

	$headersrek[] = array (
						 array('data' => 'URAIAN',  'width' => '400x','rowspan'=>'2','colspan'=>'2', 'style' => 'border-left: 1px solid black;border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'RINCIAN PERHITUNGAN', 'width' => '300px','colspan'=>'3','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'JUMLAH TOTAL',  'width' => '100px', 'rowspan'=>'2','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 );
	$headersrek[] = array (

						 array('data' => 'Satuan', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'Volume', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'Harga Satuan',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 );
						 

	
	//DETIL
	$sql = 'select iddetil,uraian,uraianp,unitjumlahp,unitsatuanp,volumjumlahp,volumsatuanp,hargap,totalp,pengelompokan from {anggperukdetilperubahan} where totalp>0 and kodeuk=\'%s\' and kodero=\'%s\' order by nourut asc,iddetil';
	$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($kodero));
	////drupal_set_message($fsql);
	
	$resultdetil = db_query($fsql);
	if ($resultdetil) {
		while ($datadetil = db_fetch_object($resultdetil)) {
			
			$total += $datadetil->totalp;
		
			if ($datadetil->pengelompokan) {
				$unitjumlah = '';
				$volumjumlah = '';
				$hargasatuan = '';
				$bullet = '#';
				
			} else {
				$unitjumlah = $datadetil->unitjumlahp . ' ' . $datadetil->unitsatuanp;
				$volumjumlah = $datadetil->volumjumlahp . ' ' . $datadetil->volumsatuanp;
				$hargasatuan = apbd_fn($datadetil->hargap);
				$bullet = '•';
				
			}
			$uraian = $datadetil->uraianp;
			if ($uraian=='') $uraian = $datadetil->uraian;
			$rowsrek[] = array (
								 array('data' => $bullet,  'widt	h' => '15px', 'style' => 'border-left: 1px solid black;  text-align:right;'),
								 array('data' => $uraian ,  'width' => '385px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;'),
								 array('data' => $unitjumlah, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;'),
								 array('data' => $volumjumlah, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;'),
								 array('data' => $hargasatuan,  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;'),
								 array('data' => apbd_fn($datadetil->totalp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;'),
								 );
			if ($datadetil->pengelompokan) {
				//SUB DETIL
				$sql = 'select idsub,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan, harga,total from {anggperukdetilsubperubahan} where total>0 and iddetil=\'%s\' order by nourut asc,idsub';
				$fsql = sprintf($sql, db_escape_string($datadetil->iddetil));
				//////drupal_set_message($fsql);
				
				//$no = 0;
				$resultsub = db_query($fsql);
				if ($resultsub) {
					while ($datasub = db_fetch_object($resultsub)) {
						//$no += 1;
						
						
						$rowsrek[] = array (
								 array('data' => '',  'width' => '15px', 'style' => 'border-left: 1px solid black; text-align:right;'),
								 array('data' =>  '- ' . $datasub->uraian,  'width' => '385px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;font-style: italic;'),
								 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
								 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
								 array('data' => apbd_fn($datasub->harga),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
								 array('data' => apbd_fn($datasub->total),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
								 );												
						//$$$
					}
				}
				
				//###
			}
		}
	}	//DETIL
	




		
	
	
	$rowsrek[] = array (
						 array('data' => 'JUMLAH',  'width'=> '700px',  'colspan'=>'5',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($total),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 );
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormContentAll($kodeuk) {
	
	
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
						 
	//REKENING
	$sql = 'select kodero,uraian,jumlahp from {anggperukperubahan} k where kodeuk=\'%s\'';
	$fsql = sprintf($sql, db_escape_string($kodeuk));
	
	////drupal_set_message( $fsql);
	$fsql .= ' order by k.kodero';
	$result = db_query($fsql);
	if ($result) {
		while ($data = db_fetch_object($result)) {
			$total += $data->jumlahp;
			$rowsrek[] = array (
							 array('data' => apbd_format_rek_rincianobyek($data->kodero),  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => $data->uraian,  'width' => '400x', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;'),
							 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
							 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
							 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
							 array('data' => apbd_fn($data->jumlahp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
						);		
			//DETIL
			$sql = 'select iddetil,uraianp,unitjumlahp,unitsatuanp,volumjumlahp,volumsatuanp,hargap, totalp,pengelompokan from {anggperukdetilperubahan} where totalp>0 and kodeuk=\'%s\' and kodero=\'%s\' order by nourut asc,iddetil';
			$fsql = sprintf($sql, db_escape_string($kodeuk), db_escape_string($data->kodero));
			////drupal_set_message($fsql);
			
			$resultdetil = db_query($fsql);
			if ($resultdetil) {
			while ($datadetil = db_fetch_object($resultdetil)) {
				
				
			
				if ($datadetil->pengelompokan) {
					$unitjumlah = '';
					$volumjumlah = '';
					$hargasatuan = '';
					$bullet = '#';
					
				} else {
					$unitjumlah = $datadetil->unitjumlahp . ' ' . $datadetil->unitsatuanp;
					$volumjumlah = $datadetil->volumjumlahp . ' ' . $datadetil->volumsatuanp;
					$hargasatuan = apbd_fn($datadetil->hargap);
					$bullet = '•';
					
				}
				$uraian = $datadetil->uraianp;
				if ($uraian=='') $uraian = $datadetil->uraian;
				
				$rowsrek[] = array (
									 array('data' => '',  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
									 array('data' => $bullet,  'width' => '15px', 'style' => 'text-align:right;'),
									 array('data' => $uraian,  'width' => '385px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;'),
									 array('data' => $unitjumlah, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;'),
									 array('data' => $volumjumlah, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;'),
									 array('data' => $hargasatuan,  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;'),
									 array('data' => apbd_fn($datadetil->totalp),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;'),
									 );
				if ($datadetil->pengelompokan) {
					//SUB DETIL
					$sql = 'select idsub,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan, harga,total from {anggperukdetilsubperubahan} where total>0 and iddetil=\'%s\' order by nourut asc,idsub';
					$fsql = sprintf($sql, db_escape_string($datadetil->iddetil));
					//////drupal_set_message($fsql);
					
					//$no = 0;
					$resultsub = db_query($fsql);
					if ($resultsub) {
						while ($datasub = db_fetch_object($resultsub)) {
							//$no += 1;
							
							
							$rowsrek[] = array (
									 array('data' => '',  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
									 array('data' => '',  'width' => '15px', 'style' => 'text-align:right;'),
									 array('data' =>  '- ' . $datasub->uraian,  'width' => '385px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;font-style: italic;'),
									 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
									 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
									 array('data' => apbd_fn($datasub->harga),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
									 array('data' => apbd_fn($datasub->total),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
									 );												
							//$$$
						}
					}
					
					//###
				}
			}
		}	//DETIL
		

		}
	}	


		
	
	
	$rowsrek[] = array (
						 array('data' => 'JUMLAH',  'width'=> '700px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($total),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 );
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}


?>