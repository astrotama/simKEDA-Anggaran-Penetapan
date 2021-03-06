<?php
function kegiatanrevisi_triwulan_form() {
    $form['formdata'] = array (
        '#type' => 'fieldset',
        //'#title'=> 'Edit Data Kegiatan',
        '#collapsible' => TRUE,
        '#collapsed' => FALSE,        
    );
    
    $kodekeg = arg(3);
	//$revisi = arg(4);
	//$sumber = arg(4);
	
	//$kodeuk = apbd_getuseruk();
	if (isSuperuser())
		$kodeuk = $_SESSION['kodeuk'];
	//FILTER TAHUN-----
    $tahun = variable_get('apbdtahun', 0);
	$revisi = variable_get('apbdrevisi', 0);
	
	//drupal_set_message($revisi);
	$total = 0;
	
	//drupal_add_js('files/js/common.js');
	drupal_add_css('files/css/kegiatancam.css');
    $disabled = FALSE;
	
	$twdesc1 = 'Alokasi belanja kegiatan pada tri wulan #1';	
	$twdesc2 = 'Alokasi belanja kegiatan pada tri wulan #2';	
	$twdesc3 = 'Alokasi belanja kegiatan pada tri wulan #3';	
	$twdesc4 = 'Alokasi belanja kegiatan pada tri wulan #4';	
    if (isset($kodekeg))
    {
        if (!user_access('kegiatanskpd edit'))
            drupal_access_denied();
		

		//SEBELUMNYA
		if ($revisi==1) {
			$sql = 'select tw1, tw2, tw3, tw4 from {kegiatanskpd} where kodekeg=\'%s\'' ;
			$res = db_query(db_rewrite_sql($sql), array ($kodekeg));
			if ($res) {
				$data = db_fetch_object($res);
				if ($data) {    
					$twdesc1 = 'Penetapan Triwulan #1: ' . apbd_fn($data->tw1);
					$twdesc2 = 'Penetapan Triwulan #2: ' . apbd_fn($data->tw2);
					$twdesc3 = 'Penetapan Triwulan #3: ' . apbd_fn($data->tw3);
					$twdesc4 = 'Penetapan Triwulan #4: ' . apbd_fn($data->tw4);
				}
			}
		
		} else {
			$sql = 'select tw1p, tw2p, tw3p, tw4p from {kegiatanperubahan} where kodekeg=\'%s\'' ;
			
			//drupal_set_message($sql);
			$res = db_query(db_rewrite_sql($sql), array ($kodekeg));
			if ($res) {
				$data = db_fetch_object($res);
				if ($data) {    
					$twdesc1 = 'Revisi sebelumnya, Triwulan #1: ' . apbd_fn($data->tw1p);
					$twdesc2 = 'Revisi sebelumnya, Triwulan #2: ' . apbd_fn($data->tw2p);
					$twdesc3 = 'Revisi sebelumnya, Triwulan #3: ' . apbd_fn($data->tw3p);
					$twdesc4 = 'Revisi sebelumnya, Triwulan #4: ' . apbd_fn($data->tw4p);
				}
			}
		}
		$sql = 'select kodekeg, tahun, kodeuk, kegiatan, total, tw1, tw2, tw3, tw4 from {kegiatanrevisi} where kodekeg=\'%s\'' ;
		
        $res = db_query(db_rewrite_sql($sql), array ($kodekeg));
        if ($res) {
			$data = db_fetch_object($res);
			if ($data) {    
				$kodekeg = $data->kodekeg;
				$nomorkeg = $data->nomorkeg;
				$tahun = $data->tahun;
				$kodeuk = $data->kodeuk;
				$kegiatan = $data->kegiatan ;

				$total = $data->total;
				
				$tw1 = $data->tw1;
				$tw2 = $data->tw2;
				$tw3 = $data->tw3;
				$tw4 = $data->tw4;
				
				$disabled =TRUE;
				
			} else {
				drupal_access_denied();
			}
        } else {
			drupal_access_denied();
		}
    } else {
		drupal_access_denied();
    }

	drupal_set_title($kegiatan . ', ' . apbd_fn($total));
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 200px; float: left;}</style>');
	
	//Jam,Menit,Detik,Bulan,Hari,Tahun
	//'$batas = mktime(20, 0, 0, 3, 8, variable_get('apbdtahun', 0)) ;
	$batas = mktime(0, 0, 1, 1, 6, 2016) ;
	$sekarang = time () ;
	$selisih =($batas-$sekarang) ;
	$allowedit = (($selisih>0) || (isSuperuser()));
	
	//TIDAK BOLEH MENGEDIT BILA BUKAN TAHUN AKTIF
	$allowedit = ($allowedit and ($tahun == variable_get('apbdtahun', 0)));
	
	
	$form['formdata']['revisi']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $revisi, 
	);
	$form['formdata']['kodekeg']= array(
		'#type'         => 'hidden', 
		'#title'        => 'kodekeg', 
		//'#description'  => 'kodekeg', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodekeg, 
	);

	$form['formdata']['tahun']= array( 
		'#type'         => 'hidden', 
		'#title'        => 'tahun',  
		//'#description'  => 'tahun', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $tahun, 
	); 
	$form['formdata']['kodeuk']= array( 
		'#type'         => 'hidden', 
		'#title'        => 'tahun',  
		//'#description'  => 'tahun', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeuk, 
	); 

	/*
	$form['formdata']['kegiatanx']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Kegiatan', 
		'#description'  => 'Nama kegiatan', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kegiatan, 
	);
	*/
	
	$form['formdata']['totalx']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Jumlah Anggaran', 
		'#description'  => 'Jumlah anggaran kegiatan, jumlah total triwulan tidak boleh melebih jumlah anggaran', 
		'#attributes'	=> array('style' => 'text-align: right'),
		'#size'         => 30, 
		//'#disabled'     => true, 
		'#default_value'=> $total, 
	); 
	$form['formdata']['total']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $total, 
	); 	
	$form['formdata']['tw1']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Triwulan #1',
		'#description'  => $twdesc1, 
		'#attributes'	=> array('style' => 'text-align: right'),
		'#size'         => 30, 
		'#disabled'     => false, 
		'#default_value'=> $tw1, 
	); 
	$form['formdata']['tw2']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Triwulan #2',
		'#description'  => $twdesc2, 
		'#attributes'	=> array('style' => 'text-align: right'),
		'#size'         => 30, 
		'#disabled'     => false, 
		'#default_value'=> $tw2, 
	); 

	$form['formdata']['tw3']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Triwulan #3',
		'#description'  => $twdesc3, 
		'#attributes'	=> array('style' => 'text-align: right'),
		'#size'         => 30, 
		'#disabled'     => false, 
		'#default_value'=> $tw3, 
	); 

	$form['formdata']['tw4']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Triwulan #4',
		'#description'  => $twdesc4, 
		'#attributes'	=> array('style' => 'text-align: right'),
		'#size'         => 30, 
		'#disabled'     => false, 
		'#default_value'=> $tw4, 
	); 


	
	$form['formdata']['submit'] = array (
		'#type' => 'submit',
		'#suffix' => "&nbsp;<a href='/apbd/kegiatanskpdperubahan' class='btn_blue' style='color: white'>Tutup</a>",
		'#value' => 'Simpan'
	);
    return $form;
	
}
function kegiatanrevisi_triwulan_form_validate($form, &$form_state) {

	//  
	$total = $form_state['values']['total'];
	$tw1 = $form_state['values']['tw1'];
	$tw2 = $form_state['values']['tw2'];
	$tw3 = $form_state['values']['tw3'];
	$tw4 = $form_state['values']['tw4'];	

	if ($total > ($tw1+$tw2+$tw3+$tw4)) {
		form_set_error('', 'Total isian tri wulan kurang dari jumlah anggaran' );
	}
	
	if ($total < ($tw1+$tw2+$tw3+$tw4)) {
		form_set_error('', 'Total isian tri wulan melebihi jumlah anggaran' );
	}
} 

function kegiatanrevisi_triwulan_form_submit($form, &$form_state) {
    if($form_state['clicked_button']['#value'] == $form_state['values']['submitnext']) {
		$nextkode = $form_state['values']['nextkode'];
        $form_state['redirect'] = 'apbd/kegiatanrevisi/triwulan/' . $nextkode ;
		//drupal_set_message('Next');
		
    } elseif ($form_state['clicked_button']['#value'] == $form_state['values']['submitprev']) {
		$prevkode = $form_state['values']['prevkode'];
        $form_state['redirect'] = 'apbd/kegiatanrevisi/triwulan/' . $prevkode ;
		//drupal_set_message('Next');
		
	} else {
		$kodekeg = $form_state['values']['kodekeg'];
		
		$tw1 = $form_state['values']['tw1'];
		$tw2 = $form_state['values']['tw2'];
		$tw3 = $form_state['values']['tw3'];
		$tw4 = $form_state['values']['tw4'];
		
		$sql = sprintf("update {kegiatanrevisi} set tw1='%s', tw2='%s', tw3='%s', tw4='%s' where kodekeg='%s'",
						db_escape_string($tw1),					  
						db_escape_string($tw2),					  
						db_escape_string($tw3),					  
						db_escape_string($tw4),
						$kodekeg);		
		$res = db_query($sql);

		
		if ($res) {
			drupal_set_message('Penyimpanan data berhasil dilakukan');		
		}
		else {
			drupal_set_message('Penyimpanan data tidak berhasil dilakukan');
		}
		
		drupal_goto('apbd/kegiatanskpdperubahan');    
		
	}
}
?>