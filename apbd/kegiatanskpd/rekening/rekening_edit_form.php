<?php
    
function rekening_edit_form(){
	drupal_add_css('files/css/kegiatancam.css');
	//drupal_add_js('files/js/rekeningbl.js'); 
	
	//drupal_add_js('files/js/kegiatanbtl.js');
	//drupal_add_js('files/js/kegiatancam.js');
	
    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Rekening Kegiatan',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
	$kodekeg=arg(4);
    $kodero = arg(5);
	
	$title = 'Rekening Kegiatan ';
	if (isset($kodekeg)) {
        $sql = 'select kegiatan, jenis from {kegiatanskpd} where {kodekeg}=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array ($kodekeg));
        if ($res) {
            $data = db_fetch_object($res);
			if ($data) 
				$title .= $data->kegiatan;
				$jenis = $data->jenis;
		
		}
		
	} 
	
	if ($jenis==2) 
		drupal_add_js('files/js/kegiatancam.js');
	else
		drupal_add_js('files/js/kegiatanbtl.js');
	
	$jumlah=0;
	$jumlahsebelum = 0;
	$jumlahsesudah = 0;	

	//$title =l($title, 'apbd/kegiatanskpd/rekening/' . $kodekeg, array('html'=>true));
	drupal_set_title($title);
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 200px; float: left;}</style>');
    $disabled = FALSE;
    if (isset($kodero))
    {
        if (!user_access('kegiatanskpd edit'))
            drupal_access_denied();
			
        $sql = 'select kodekeg,kodero,uraian,jumlah,jumlahsebelum,jumlahsesudah from {anggperkeg} where kodekeg=\'%s\' and kodero=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array ($kodekeg, $kodero));
        if ($res) {
            $data = db_fetch_object($res);
			if ($data) {
                
				//$kodekeg = $data->kodekeg;
				$kodero = $data->kodero;
				$uraian = $data->uraian;
				$jumlah = $data->jumlah;
				$jumlahsebelum = $data->jumlahsebelum;
				$jumlahsesudah = $data->jumlahsesudah;
                $disabled =TRUE;
			} else {
				$kodero = '';
			}
        } else {
			$kodero = '';
		}
    } else {
		if (!user_access('kegiatanskpd tambah'))
			drupal_access_denied();
		$form['formdata']['#title'] = 'Tambah Rekening Kegiatan';
		$kodero = '';
	}
    
	
	$form['formdata']['nk']= array(
		'#type'         => 'hidden', 
		'#title'        => 'kodero', 
		//'#description'  => 'id', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodero, 
	); 
	
    $form['formdata']['kodekeg']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $kodekeg, 
    ); 

	$form['formdata']['kegiatanxx']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Rekening', 
		//'#description'  => 'Rekening belanja', 
		'#maxlength'    => 255, 
		'#size'         => 70, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		//'#autocomplete_path' => 'apbd/kegiatanrkpd/utils_auto/uraian',
		'#default_value'=> $uraian, 
	); 
	/*
	$form['formdata']['keterangan'] = array (
		'#type' => 'markup',
		'#value' => "<span><font size='1'>Isi rekening dengan memilih menggunakan tombol Pilih</font></span>",
	);
*/	
	$form['formdata']['jumlah']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Jumlah',
		'#attributes'	=> array('style' => 'text-align: right'),
		'#disabled'     => true, 
		'#description'  => 'Jumlah anggaran, jumlahnya akan terisi secara otomatis saat detilnya diisi', 
		//'#maxlength'    => 60, 
		'#size'         => 30, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $jumlah, 
	); 
	$form['formdata']['jumlahsebelum']= array(
		'#type'         => 'hidden', 
		'#title'        => 'Tahun Lalu',
		'#attributes'	=> array('style' => 'text-align: right'),
		'#description'  => 'Jumlah anggaran tahun lalu, bila ada silahkan diisi', 
		//'#maxlength'    => 60, 
		'#size'         => 30, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $jumlahsebelum, 
	); 
	$form['formdata']['jumlahsesudah']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Tahun Depan',
		'#attributes'	=> array('style' => 'text-align: right'),
		'#description'  => 'Jumlah perkiraan anggaran tahun depan, diisi sesuai perkiraan',  
		//'#maxlength'    => 60, 
		'#size'         => 30, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $jumlahsesudah, 
	); 

	$jawabantype = 'hidden';
	$username = '';
	$jawaban = '';
	$jawabanada = 0;
	if (isVerifikator()) {

		global $user;
		$username = $user->name;
	
		$jawabantype = 'textarea';

		$pquery = sprintf("select jawaban from {anggperkegverifikasi} where kodekeg='%s' and username='%s' and kodero='%s'", db_escape_string($kodekeg), db_escape_string($username), db_escape_string($kodero));
		$pres = db_query($pquery);	
		if ($data = db_fetch_object($pres)) {
			$jawaban = $data->jawaban;
			$jawabanada = 1;
		}		
	}
	
	$form['formdata']['jawaban']= array(
		'#type'         => $jawabantype, 
		'#title'        => 'Catatan Verifikator',
		'#description'  => '<font color="red">Catatan/masukan dari verifikator</font>', 
		//'#maxlength'    => 60, 
		'#size'         => 90, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $jawaban, 
	);

    $form['formdata']['submit'] = array (
        '#type' => 'submit',
		'#suffix' => "&nbsp;<a href='/apbd/kegiatanskpd/rekening/" . $kodekeg . "' class='btn_blue' style='color: white'>Tutup</a>",
		//'#suffix' => "&nbsp;<a href='/apbd/kegiatanskpd/rekening/' class='btn_blue' style='color: white'>Tutup</a>",
        '#value' => 'Simpan Catatan Verifikasi'
    );
	
	//GenReportFormContent($kodekeg, $kodero) 
	$form['formdata']['preview']= array(
		'#type'         => 'markup', 
		'#value'=> GenReportFormContent($kodekeg, $kodero), 
	);

	$form['formdata']['username']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $username, 
	); 		
	$form['formdata']['jawabanada']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $jawabanada, 
	); 
	
	
    $form['formdata']['e_kodero']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $kodero, 
    ); 

	

    
    return $form;
}
function rekening_edit_form_validate($form, &$form_state) {

}

function rekening_edit_form_submit($form, &$form_state) {
	
	$kodekeg = $form_state['values']['kodekeg'];

    if($form_state['clicked_button']['#value'] == $form_state['values']['submitnext']) {
		$nextkode = $form_state['values']['nextkode'];
        $form_state['redirect'] = 'apbd/kegiatanskpd/rekening/edit/' . $kodekeg . '/' . $nextkode ;
		//drupal_set_message('Next');

	} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitprev']) {
		$prevkode = $form_state['values']['prevkode'];
        $form_state['redirect'] = 'apbd/kegiatanskpd/rekening/edit/' . $kodekeg . '/' . $prevkode ;
		//drupal_set_message('Next');

	} else if($form_state['clicked_button']['#value'] == $form_state['values']['submitlist']) {
        $form_state['redirect'] = 'apbd/kegiatanskpd/rekening/' . $kodekeg  ;
		//drupal_set_message('Next');

	} else {
		
		$e_kodero = $form_state['values']['e_kodero'];	
		
		$jawaban = $form_state['values']['jawaban'];
		$jawabanada = $form_state['values']['jawabanada'];
		$username = $form_state['values']['username'];

		
		if ($jawaban=='') {
			
			if ($jawabanada) {
				$sql = 'delete from {anggperkegverifikasi} where kodekeg=\'%s\' and kodero=\'%s\' and username=\'%s\'';
				$res = db_query(db_rewrite_sql($sql), array ($kodekeg, $e_kodero, $username));
				
			}
			
		} else {
			
			if ($jawabanada) {
				
				$sql = 'update {anggperkegverifikasi} set jawaban=\'%s\' where kodekeg=\'%s\' and kodero=\'%s\' and username=\'%s\'';
				
				
				drupal_set_message(db_rewrite_sql($sql), array ($jawaban, $kodekeg, $e_kodero, $username));
				
				$res = db_query(db_rewrite_sql($sql), array ($jawaban, $kodekeg, $e_kodero, $username));
				
			} else {
				$sql = 'insert into {anggperkegverifikasi} (kodekeg,kodero,username,jawaban) 
					   values (\'%s\', \'%s\', \'%s\', \'%s\')';        
				$res = db_query(db_rewrite_sql($sql), array($kodekeg, $e_kodero,$username, $jawaban));
			}
		}	

		
		if ($res)
			drupal_set_message('Penyimpanan data berhasil dilakukan');
		else
			drupal_set_message('Penyimpanan data tidak berhasil dilakukan');
		drupal_goto('apbd/kegiatanskpd/rekening/' . $kodekeg);    

	}
}

function GenReportFormContent($kodekeg, $kodero) {


	$total=0;
	/*
	$headersrek[] = array (
						 array('data' => 'Kode',  'width'=> '75px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 
						 array('data' => 'Uraian',  'width' => '400x','colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Satuan', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Volume', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Harga Satuan',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Total',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );
	*/

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
	$where = ' where k.kodekeg=\'%s\' and k.kodero=\'%s\'';
	$sql = 'select mid(k.kodero,1,3) kodej,j.uraian,sum(jumlah) jumlahx from {anggperkeg} k  left join {jenis} j on mid(k.kodero,1,3)=j.kodej ' . $where;
	$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($kodero));
	$fsql .= ' group by mid(k.kodero,1,3),j.uraian order by mid(k.kodero,1,3)';
	
	////drupal_set_message( $fsql);
	$resultjenis = db_query($fsql);
	if ($resultjenis) {
		while ($datajenis = db_fetch_object($resultjenis)) {
			$rowsrek[] = array (
								 array('data' => $datajenis->kodej,  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $datajenis->uraian,  'width' => '400x','colspan'=>'2',  'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
								 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
								 array('data' => apbd_fn($datajenis->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-weight:bold;'),
								 );
			$total += $datajenis->jumlahx;
			
			//OBYEK
			$sql = 'select mid(k.kodero,1,5) kodeo,o.uraian,sum(jumlah) jumlahx from {anggperkeg} k  left join {obyek} o on mid(k.kodero,1,5)=o.kodeo 
				   where k.kodekeg=\'%s\' and k.kodero=\'%s\' and mid(k.kodero,1,3)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodekeg), $kodero, db_escape_string($datajenis->kodej));
			$fsql .= ' group by mid(k.kodero,1,5),o.uraian order by mid(k.kodero,1,5)';
			
			//drupal_set_message( $fsql);
			$resultobyek = db_query($fsql);
			if ($resultobyek) {
				while ($dataobyek = db_fetch_object($resultobyek)) {
					$rowsrek[] = array (
										 array('data' => apbd_format_rek_obyek($dataobyek->kodeo),  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => strtoupper($dataobyek->uraian),  'width' => '400x', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;'),
										 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
										 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
										 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
										 array('data' => apbd_fn($dataobyek->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;text-align:right;font-weight:bold;'),
										 );		

					//REKENING
					$sql = 'select kodero,uraian,jumlah from {anggperkeg} k where kodekeg=\'%s\'  and k.kodero=\'%s\' and mid(k.kodero,1,5)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($kodekeg), $kodero, db_escape_string($dataobyek->kodeo));
					
					////drupal_set_message( $fsql);
					$fsql .= ' order by k.kodero';
					$result = db_query($fsql);
					if ($result) {
						while ($data = db_fetch_object($result)) {
						$rowsrek[] = array (
											 array('data' => apbd_format_rek_rincianobyek($data->kodero),  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
											 array('data' => $data->uraian,  'width' => '400x', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;'),
											 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
											 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
											 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
											 array('data' => apbd_fn($data->jumlah),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
											 );
							//DETIL
							$sql = 'select iddetil,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan,harga,total, pengelompokan from {anggperkegdetil} where kodekeg=\'%s\' and kodero=\'%s\' order by nourut asc,iddetil';
							$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($data->kodero));
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
										$unitjumlah = $datadetil->unitjumlah . ' ' . $datadetil->unitsatuan;
										$volumjumlah = $datadetil->volumjumlah . ' ' . $datadetil->volumsatuan;
										$hargasatuan = apbd_fn($datadetil->harga);
										$bullet = '•';
										
									}
									$rowsrek[] = array (
														 array('data' => '',  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
														 array('data' => $bullet,  'width' => '15px', 'style' => 'text-align:right;'),
														 array('data' => $datadetil->uraian,  'width' => '385px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;'),
														 array('data' => $unitjumlah, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;'),
														 array('data' => $volumjumlah, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;'),
														 array('data' => $hargasatuan,  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;'),
														 array('data' => apbd_fn($datadetil->total),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;'),
														 );
									if ($datadetil->pengelompokan) {
										//SUB DETIL
										$sql = 'select idsub,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan, harga,total from {anggperkegdetilsub} where iddetil=\'%s\' order by nourut asc,idsub';
										$fsql = sprintf($sql, db_escape_string($datadetil->iddetil));
										////drupal_set_message($fsql);
										
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
							}
						}
					}
										 
				////////
				}
			}
		}
	}
	
	$rowsrek[] = array (
						 array('data' => 'JUMLAH BELANJA',  'width'=> '775px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($total),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 );
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}


?>