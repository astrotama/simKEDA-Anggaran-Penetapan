<?php

function apbdbintang_post_form() {
    drupal_add_css('files/css/kegiatancam.css');	

	$form['submitinit'] = array(
		'#type' => 'submit',
		//'#suffix' => "&nbsp;<a href='/apbd/kegiatanrevisi' class='btn_green' style='color: white'>Tutup</a>",
		'#value' => '1. Posting Init',
	);
	$form['submitdetil'] = array(
		'#type' => 'submit',
		//'#suffix' => "&nbsp;<a href='/apbd/kegiatanrevisi' class='btn_green' style='color: white'>Tutup</a>",
		'#value' => '2. Posting Detil',
	);
	$form['submitdetil'] = array(
		'#type' => 'submit',
		//'#suffix' => "&nbsp;<a href='/apbd/kegiatanrevisi' class='btn_green' style='color: white'>Tutup</a>",
		'#value' => '2. Posting Detil',
	);
	$form['submitrekening'] = array(
		'#type' => 'submit',
		//'#suffix' => "&nbsp;<a href='/apbd/kegiatanrevisi' class='btn_green' style='color: white'>Tutup</a>",
		'#value' => '3. Posting Rekening',
	);
	$form['submitkegiatan'] = array(
		'#type' => 'submit',
		//'#suffix' => "&nbsp;<a href='/apbd/kegiatanrevisi' class='btn_green' style='color: white'>Tutup</a>",
		'#value' => '4. Posting Kegiatan',
	);

	$form['submittunda1'] = array(
		'#type' => 'submit',
		//'#suffix' => "&nbsp;<a href='/apbd/kegiatanrevisi' class='btn_green' style='color: white'>Tutup</a>",
		'#value' => '5.1. Tunda 1',
	);
	$form['submittunda2'] = array(
		'#type' => 'submit',
		//'#suffix' => "&nbsp;<a href='/apbd/kegiatanrevisi' class='btn_green' style='color: white'>Tutup</a>",
		'#value' => '5.2. Tunda 2',
	);
	$form['submittunda3'] = array(
		'#type' => 'submit',
		//'#suffix' => "&nbsp;<a href='/apbd/kegiatanrevisi' class='btn_green' style='color: white'>Tutup</a>",
		'#value' => '5.3. Tunda 3',
	);
	$form['submittunda4'] = array(
		'#type' => 'submit',
		//'#suffix' => "&nbsp;<a href='/apbd/kegiatanrevisi' class='btn_green' style='color: white'>Tutup</a>",
		'#value' => '5.4. Tunda 4',
	);
	$form['submittunda5'] = array(
		'#type' => 'submit',
		//'#suffix' => "&nbsp;<a href='/apbd/kegiatanrevisi' class='btn_green' style='color: white'>Tutup</a>",
		'#value' => '5.5. Tunda 5',
	);
	$form['submittunda6'] = array(
		'#type' => 'submit',
		//'#suffix' => "&nbsp;<a href='/apbd/kegiatanrevisi' class='btn_green' style='color: white'>Tutup</a>",
		'#value' => '5.6. Tunda 6',
	);
	$form['submittunda7'] = array(
		'#type' => 'submit',
		//'#suffix' => "&nbsp;<a href='/apbd/kegiatanrevisi' class='btn_green' style='color: white'>Tutup</a>",
		'#value' => '5.7. Tunda 7',
	);
	$form['submittunda8'] = array(
		'#type' => 'submit',
		//'#suffix' => "&nbsp;<a href='/apbd/kegiatanrevisi' class='btn_green' style='color: white'>Tutup</a>",
		'#value' => '5.8. Tunda 8',
	);
	$form['submittunda9'] = array(
		'#type' => 'submit',
		//'#suffix' => "&nbsp;<a href='/apbd/kegiatanrevisi' class='btn_green' style='color: white'>Tutup</a>",
		'#value' => '5.9. Tunda 9',
	);
	$form['submittunda10'] = array(
		'#type' => 'submit',
		//'#suffix' => "&nbsp;<a href='/apbd/kegiatanrevisi' class='btn_green' style='color: white'>Tutup</a>",
		'#value' => '5.10. Tunda 10',
	);
	$form['submittunda11'] = array(
		'#type' => 'submit',
		//'#suffix' => "&nbsp;<a href='/apbd/kegiatanrevisi' class='btn_green' style='color: white'>Tutup</a>",
		'#value' => '5.11. Tunda 11',
	);
	$form['submittunda12'] = array(
		'#type' => 'submit',
		//'#suffix' => "&nbsp;<a href='/apbd/kegiatanrevisi' class='btn_green' style='color: white'>Tutup</a>",
		'#value' => '5.12. Tunda 12',
	);

	$form['submittunda13'] = array(
		'#type' => 'submit',
		//'#suffix' => "&nbsp;<a href='/apbd/kegiatanrevisi' class='btn_green' style='color: white'>Tutup</a>",
		'#value' => '5.13. Tunda 13',
	);
	$form['submittunda14'] = array(
		'#type' => 'submit',
		//'#suffix' => "&nbsp;<a href='/apbd/kegiatanrevisi' class='btn_green' style='color: white'>Tutup</a>",
		'#value' => '5.14. Tunda 14',
	);
	$form['submittunda15'] = array(
		'#type' => 'submit',
		//'#suffix' => "&nbsp;<a href='/apbd/kegiatanrevisi' class='btn_green' style='color: white'>Tutup</a>",
		'#value' => '5.15. Tunda 15',
	);
	$form['submittunda16'] = array(
		'#type' => 'submit',
		//'#suffix' => "&nbsp;<a href='/apbd/kegiatanrevisi' class='btn_green' style='color: white'>Tutup</a>",
		'#value' => '5.16. Tunda 16',
	);
	$form['submittunda17'] = array(
		'#type' => 'submit',
		//'#suffix' => "&nbsp;<a href='/apbd/kegiatanrevisi' class='btn_green' style='color: white'>Tutup</a>",
		'#value' => '5.17. Tunda 17',
	);

	$form['submitpostinguk'] = array(
		'#type' => 'submit',
		//'#suffix' => "&nbsp;<a href='/apbd/kegiatanrevisi' class='btn_green' style='color: white'>Tutup</a>",
		'#value' => 'Resume UK',
	);

	$form['submitposting'] = array(
		'#type' => 'submit',
		//'#suffix' => "&nbsp;<a href='/apbd/kegiatanrevisi' class='btn_green' style='color: white'>Tutup</a>",
		'#value' => 'Posting',
	);
	
	return $form;
}

function apbdbintang_post_form_validate($form, &$form_state) {
}

function apbdbintang_post_form_submit($form, &$form_state) {
	if($form_state['clicked_button']['#value'] == $form_state['values']['submitinit'])
		proses_anggaran_init();
	
	else if($form_state['clicked_button']['#value'] == $form_state['values']['submitdetil'])
	
		proses_anggaran_detil();
	
	else if($form_state['clicked_button']['#value'] == $form_state['values']['submitrekening'])
		
		proses_anggaran_rekening();
	
	else if($form_state['clicked_button']['#value'] == $form_state['values']['submitkegiatan'])
		
		proses_anggaran_kegiatan();
	
	else if($form_state['clicked_button']['#value'] == $form_state['values']['submittunda1'])
		proses_anggaran_tunda_step(1);

	else if($form_state['clicked_button']['#value'] == $form_state['values']['submittunda2'])
		proses_anggaran_tunda_step(2);

	else if($form_state['clicked_button']['#value'] == $form_state['values']['submittunda3'])
		proses_anggaran_tunda_step(3);

	else if($form_state['clicked_button']['#value'] == $form_state['values']['submittunda4'])
		proses_anggaran_tunda_step(4);

	else if($form_state['clicked_button']['#value'] == $form_state['values']['submittunda5'])
		proses_anggaran_tunda_step(5);

	else if($form_state['clicked_button']['#value'] == $form_state['values']['submittunda6'])
		proses_anggaran_tunda_step(6);
	else if($form_state['clicked_button']['#value'] == $form_state['values']['submittunda7'])
		proses_anggaran_tunda_step(7);
	else if($form_state['clicked_button']['#value'] == $form_state['values']['submittunda8'])
		proses_anggaran_tunda_step(8);
	else if($form_state['clicked_button']['#value'] == $form_state['values']['submittunda9'])
		proses_anggaran_tunda_step(9);
	else if($form_state['clicked_button']['#value'] == $form_state['values']['submittunda10'])
		proses_anggaran_tunda_step(10);
	else if($form_state['clicked_button']['#value'] == $form_state['values']['submittunda11'])
		proses_anggaran_tunda_step(11);
	else if($form_state['clicked_button']['#value'] == $form_state['values']['submittunda12'])
		proses_anggaran_tunda_step(12);
	else if($form_state['clicked_button']['#value'] == $form_state['values']['submittunda13'])
		proses_anggaran_tunda_step(13);
	else if($form_state['clicked_button']['#value'] == $form_state['values']['submittunda14'])
		proses_anggaran_tunda_step(14);
	else if($form_state['clicked_button']['#value'] == $form_state['values']['submittunda15'])
		proses_anggaran_tunda_step(15);
	else if($form_state['clicked_button']['#value'] == $form_state['values']['submittunda16'])
		proses_anggaran_tunda_step(16);
	else if($form_state['clicked_button']['#value'] == $form_state['values']['submittunda17'])
		proses_anggaran_tunda_step(17);
	else if($form_state['clicked_button']['#value'] == $form_state['values']['submitpostinguk'])
		proses_anggaran_tunda_uk('81');
	else 
		posting_data();
	
	/*	
	$function = 'bintang_batch';		// . $values['batch'];
	$batch = $function();
	batch_set($batch);
	batch_process('apbdbintangpost');
	*/
}

function bintang_batch() {
	$operations = array();
	
	$operations[] = array('prosesbintang', array('proses_anggaran_detil', 'Inisialisasi'));
	
	/*
	$sql = 'select kp.id, kp.kodekeg, kp.jenisrevisi, kr.kegiatan from {kegiatanrevisiperubahan} kp inner join {kegiatanrevisi} kr on kp.kodekeg=kr.kodekeg where kp.status=1 order by kp.jenisrevisi, kr.kegiatan' ;		
	
	$res = db_query($sql); 
	while ($data = db_fetch_object($res)) {
		//drupal_set_message($data->kodekeg . ' - ' . $data->id . ' - ' . $data->jenisrevisi);
		$operations[] = array('prosesbintang', array($data->kodekeg, $data->id, $data->jenisrevisi, $data->kegiatan));
	}
	*/
	
	
	$batch = array(
		'operations' => $operations,
		'finished' => 'apbdbintang_post_finished',
		// We can define custom messages instead of the default ones.
		'title' => t('Penetapan bintang'),
		'init_message' => t('Penetapan dimulai..'),
		'progress_message' => t('Penetapan @current dari @total kegiatan'),
		'error_message' => t('Terjadi kesalahan.'),
	);
	return $batch;
}

function apbdbintang_post_finished($success, $results, $operations) {
  if ($success) {
    // Here we could do something meaningful with the results.
    // We just display the number of nodes we processed...
    //$message = count($results) . ' kegiatan sudah disahkan menjadi DPPA-SKPD.';
	$message = 'Semua kegiatan bintang disahkan.';
  }
  else {
    // An error occurred.
    // $operations contains the operations that remained unprocessed.
    $error_operation = reset($operations);
    $message = 'An error occurred while processing ' . $error_operation[0] . ' with arguments :' . print_r($error_operation[0], TRUE);
  }
  drupal_set_message($message);
  
}



function prosesbintang($proses, &$context) {	
	
	if ($proses=='proses_anggaran_detil') {
		proses_anggaran_detil();
	} else {
	}	
}

function proses_anggaran_init() {
	//detil
	$sql = 'update {anggperkegdetil} set anggaran=total';
	$res = db_query($sql);
	$sql = 'update {anggperkegdetil} set anggaran=0 where bintang=1';
	$res = db_query($sql);
	
	//rek
	$sql = 'update {anggperkeg} set anggaran=jumlah';
	$res = db_query($sql);
	$sql = 'update {anggperkeg} set anggaran=0 where bintang=1';
	$res = db_query($sql);

	//kegiatan
	$sql = 'update {kegiatanskpd} set anggaran=total';
	$res = db_query($sql);
	$sql = 'update {kegiatanskpd} set anggaran=0 where bintang=1';
	$res = db_query($sql);
	
	drupal_set_message('Init selesai');
}	

function proses_anggaran_detil() {
	
	//Rekening
	$sql = 'select distinct kodekeg,kodero from {anggperkegdetil} where bintang=1';
	$res = db_query($sql);
	while ($data = db_fetch_object($res)) {
		
		drupal_set_message($data->kodekeg . ', ' . $data->kodero);
		
        $sql = 'select sum(anggaran) as totalanggaran from {anggperkegdetil} where kodekeg=\'%s\' and kodero=\'%s\'';
        $res_rek = db_query(db_rewrite_sql($sql), array($data->kodekeg, $data->kodero));
            if ($data_rek = db_fetch_object($res_rek)) {
				
				
				$sql = 'update {anggperkeg} set anggaran=\'%s\' where kodekeg=\'%s\' and kodero=\'%s\'';
				$res_x = db_query(db_rewrite_sql($sql), array($data_rek->totalanggaran, $data->kodekeg, $data->kodero));			
				
				
			}
			
	}	
	
	//Kegiatan
	$sql = 'select distinct kodekeg from {anggperkegdetil} where bintang=1';
	$res = db_query($sql);
	while ($data = db_fetch_object($res)) {
		
		
		drupal_set_message($data->kodekeg);
		
        $sql = 'select sum(anggaran) as totalanggaran from {anggperkeg} where kodekeg=\'%s\'';
        $res_rek = db_query(db_rewrite_sql($sql), array($data->kodekeg));
            if ($data_rek = db_fetch_object($res_rek)) {

				$sql = 'update {kegiatanskpd} set anggaran=\'%s\' where kodekeg=\'%s\'';
				$res_x = db_query(db_rewrite_sql($sql), array($data_rek->totalanggaran, $data->kodekeg));			
            
			}
			
	}	
	
	drupal_set_message('Deil selesai');
}	

function proses_anggaran_rekening() {
	
	//Kegiatan
	$sql = 'select distinct kodekeg from {anggperkeg} where bintang=1';
	$res = db_query($sql);
	while ($data = db_fetch_object($res)) {
		
		
		drupal_set_message($data->kodekeg);
		
        $sql = 'select sum(anggaran) as totalanggaran from {anggperkeg} where kodekeg=\'%s\'';
        $res_rek = db_query(db_rewrite_sql($sql), array($data->kodekeg));
            if ($data_rek = db_fetch_object($res_rek)) {

				$sql = 'update {kegiatanskpd} set anggaran=\'%s\' where kodekeg=\'%s\'';
				$res_x = db_query(db_rewrite_sql($sql), array($data_rek->totalanggaran, $data->kodekeg));			
            
			}
			
	}	
	
	drupal_set_message('Rekening selesai');
	
}	

function proses_anggaran_kegiatan() {
	
	$sql = 'update {anggperkeg} set anggaran=0 where kodekeg in (select kodekeg from {kegiatanskpd} where bintang=1)';
	$res_x = db_query($sql);			
	
	drupal_set_message('Kegiatan selesai');
            
}	

function proses_anggaran_tunda() {
	
	//Rekening
	$sql = 'select distinct a.kodekeg,a.kodero from {anggperkeg} a inner join {anggperkegdetil} d on a.kodekeg=d.kodekeg and a.kodero=d.kodero where d.total<0 and a.anggaran>0';
	$res = db_query($sql);
	while ($data = db_fetch_object($res)) {
		
		drupal_set_message($data->kodekeg . ', ' . $data->kodero);
		
        $sql = 'select sum(total) as totalanggaran from {anggperkegdetil} where kodekeg=\'%s\' and kodero=\'%s\'';
        $res_rek = db_query(db_rewrite_sql($sql), array($data->kodekeg, $data->kodero));
            if ($data_rek = db_fetch_object($res_rek)) {
				
				
				$sql = 'update {anggperkeg} set anggaran=\'%s\' where anggaran>0 and kodekeg=\'%s\' and kodero=\'%s\'';
				$res_x = db_query(db_rewrite_sql($sql), array($data_rek->totalanggaran, $data->kodekeg, $data->kodero));			
				
				
			}
			
	}	
	
	//Kegiatan
	$sql = 'select distinct a.kodekeg from {anggperkeg} a inner join {anggperkegdetil} d on a.kodekeg=d.kodekeg where d.total<0 and a.anggaran>0';
	$res = db_query($sql);
	while ($data = db_fetch_object($res)) {
		
		
		drupal_set_message($data->kodekeg);
		
        $sql = 'select sum(anggaran) as totalanggaran from {anggperkeg} where kodekeg=\'%s\'';
        $res_rek = db_query(db_rewrite_sql($sql), array($data->kodekeg));
            if ($data_rek = db_fetch_object($res_rek)) {

				$sql = 'update {kegiatanskpd} set anggaran=\'%s\' where anggaran>0 and kodekeg=\'%s\'';
				$res_x = db_query(db_rewrite_sql($sql), array($data_rek->totalanggaran, $data->kodekeg));			
            
			}
			
	}	
	
}	

function proses_anggaran_tunda_step($step) {
	set_time_limit(0);
	ini_set('memory_limit', '1024M');
	
	//sprintf(" and kodekeg='%s'", db_escape_string($kodekeg));
	if ($step=='1') {
		$where = sprintf(" and a.kodekeg in (select kodekeg from {kegiatanskpd} where kodeuk>='%s' and kodeuk<='%s' ) ", '00', '05');	
	} else if ($step=='2') {
		$where = sprintf(" and a.kodekeg in (select kodekeg from {kegiatanskpd} where kodeuk>='%s' and kodeuk<='%s' ) ", '06', '12');	
	
	} else if ($step=='3') {
		$where = sprintf(" and a.kodekeg in (select kodekeg from {kegiatanskpd} where kodeuk>='%s' and kodeuk<='%s' ) ", '13', '20');	

	} else if ($step=='4') {	
		$where = sprintf(" and a.kodekeg in (select kodekeg from {kegiatanskpd} where kodeuk>='%s' and kodeuk<='%s' ) ", '21', '26');	

	} else if ($step=='5') {	
		$where = sprintf(" and a.kodekeg in (select kodekeg from {kegiatanskpd} where kodeuk>='%s' and kodeuk<='%s' ) ", '27', '32');	
	} else if ($step=='6') {	
		$where = sprintf(" and a.kodekeg in (select kodekeg from {kegiatanskpd} where kodeuk>='%s' and kodeuk<='%s' ) ", '33', '40');	
	} else if ($step=='7') {	
		$where = sprintf(" and a.kodekeg in (select kodekeg from {kegiatanskpd} where kodeuk>='%s' and kodeuk<='%s' ) ", '41', '48');	
	} else if ($step=='8') {	
		$where = sprintf(" and a.kodekeg in (select kodekeg from {kegiatanskpd} where kodeuk>='%s' and kodeuk<='%s' ) ", '49', '55');	
	} else if ($step=='9') {	
		$where = sprintf(" and a.kodekeg in (select kodekeg from {kegiatanskpd} where kodeuk>='%s' and kodeuk<='%s' ) ", '56', '60');	
	} else if ($step=='10') {	
		$where = sprintf(" and a.kodekeg in (select kodekeg from {kegiatanskpd} where kodeuk>='%s' and kodeuk<='%s' ) ", '61', '70');	
	} else if ($step=='11') {	
		$where = sprintf(" and a.kodekeg in (select kodekeg from {kegiatanskpd} where kodeuk>='%s' and kodeuk<='%s' ) ", '71', '80');	
	} else if ($step=='12') {	
		$where = sprintf(" and a.kodekeg in (select kodekeg from {kegiatanskpd} where kodeuk>='%s' and kodeuk<='%s' ) ", '81', '90');	
	} else if ($step=='13') {	
		$where = sprintf(" and a.kodekeg in (select kodekeg from {kegiatanskpd} where kodeuk>='%s' and kodeuk<='%s' ) ", '91', 'A5');	
	} else if ($step=='14') {	
		$where = sprintf(" and a.kodekeg in (select kodekeg from {kegiatanskpd} where kodeuk>='%s' and kodeuk<='%s' ) ", 'A6', 'B5');	
	} else if ($step=='15') {	
		$where = sprintf(" and a.kodekeg in (select kodekeg from {kegiatanskpd} where kodeuk>='%s' and kodeuk<='%s' ) ", 'B6', 'C5');	
	} else if ($step=='16') {	
		$where = sprintf(" and a.kodekeg in (select kodekeg from {kegiatanskpd} where kodeuk>='%s' and kodeuk<='%s' ) ", 'C6', 'D5');	
	} else {
		$where = sprintf(" and a.kodekeg in (select kodekeg from {kegiatanskpd} where kodeuk>='%s') ", 'D6');	
		
	}
	
	//Rekening
	$sql = 'select distinct a.kodekeg,a.kodero from {anggperkeg} a inner join {anggperkegdetil} d on a.kodekeg=d.kodekeg and a.kodero=d.kodero where d.total<0 and a.anggaran>0 ' . $where . ' order by a.kodekeg,a.kodero';
	
	drupal_set_message($sql);
	
	$res = db_query($sql);
	while ($data = db_fetch_object($res)) {
		
		drupal_set_message($data->kodekeg . ', ' . $data->kodero);
		
        $sql = 'select sum(total) as totalanggaran from {anggperkegdetil} where kodekeg=\'%s\' and kodero=\'%s\'';
        $res_rek = db_query(db_rewrite_sql($sql), array($data->kodekeg, $data->kodero));
            if ($data_rek = db_fetch_object($res_rek)) {
				
				
				$sql = 'update {anggperkeg} set anggaran=\'%s\' where anggaran>0 and kodekeg=\'%s\' and kodero=\'%s\'';
				$res_x = db_query(db_rewrite_sql($sql), array($data_rek->totalanggaran, $data->kodekeg, $data->kodero));			
				
				
			}
			
	}	
	
	//Kegiatan
	$sql = 'select distinct a.kodekeg from {anggperkeg} a inner join {anggperkegdetil} d on a.kodekeg=d.kodekeg where d.total<0 and a.anggaran>0'  . $where . ' order by a.kodekeg';
	$res = db_query($sql);
	while ($data = db_fetch_object($res)) {
		
		
		drupal_set_message($data->kodekeg);
		
        $sql = 'select sum(anggaran) as totalanggaran from {anggperkeg} where kodekeg=\'%s\'';
        $res_rek = db_query(db_rewrite_sql($sql), array($data->kodekeg));
            if ($data_rek = db_fetch_object($res_rek)) {

				$sql = 'update {kegiatanskpd} set anggaran=\'%s\' where anggaran>0 and kodekeg=\'%s\'';
				$res_x = db_query(db_rewrite_sql($sql), array($data_rek->totalanggaran, $data->kodekeg));			
            
			}
			
	}	
	
	drupal_set_message('Tunda ' . $step .  ' selesai');
}	

function proses_anggaran_tunda_uk($kodeuk) {
	set_time_limit(0);
	ini_set('memory_limit', '512M');
	
	$where = sprintf(" and a.kodekeg in (select kodekeg from {kegiatanskpd} where kodeuk='%s') ", $kodeuk);	

	
	//Rekening
	$sql = 'select distinct a.kodekeg,a.kodero from {anggperkeg} a inner join {anggperkegdetil} d on a.kodekeg=d.kodekeg and a.kodero=d.kodero where d.total<0 and a.anggaran>0 ' . $where . ' order by a.kodekeg,a.kodero';
	
	drupal_set_message($sql);
	
	$res = db_query($sql);
	while ($data = db_fetch_object($res)) {
		
		drupal_set_message($data->kodekeg . ', ' . $data->kodero);
		
        $sql = 'select sum(total) as totalanggaran from {anggperkegdetil} where kodekeg=\'%s\' and kodero=\'%s\'';
        $res_rek = db_query(db_rewrite_sql($sql), array($data->kodekeg, $data->kodero));
            if ($data_rek = db_fetch_object($res_rek)) {
				
				
				$sql = 'update {anggperkeg} set anggaran=\'%s\' where anggaran>0 and kodekeg=\'%s\' and kodero=\'%s\'';
				$res_x = db_query(db_rewrite_sql($sql), array($data_rek->totalanggaran, $data->kodekeg, $data->kodero));			
				
				
			}
			
	}	
	
	//Kegiatan
	$sql = 'select distinct a.kodekeg from {anggperkeg} a inner join {anggperkegdetil} d on a.kodekeg=d.kodekeg where d.total<0 and a.anggaran>0'  . $where . ' order by a.kodekeg';
	$res = db_query($sql);
	while ($data = db_fetch_object($res)) {
		
		
		drupal_set_message($data->kodekeg);
		
        $sql = 'select sum(anggaran) as totalanggaran from {anggperkeg} where kodekeg=\'%s\'';
        $res_rek = db_query(db_rewrite_sql($sql), array($data->kodekeg));
            if ($data_rek = db_fetch_object($res_rek)) {

				$sql = 'update {kegiatanskpd} set anggaran=\'%s\' where anggaran>0 and kodekeg=\'%s\'';
				$res_x = db_query(db_rewrite_sql($sql), array($data_rek->totalanggaran, $data->kodekeg));			
            
			}
			
	}	
	
	drupal_set_message('Tunda ' . $step .  ' selesai');
}	


function posting_data () {
	$sql = 'update kegiatananggaran inner join kegiatanskpd on kegiatananggaran.kodekeg=kegiatanskpd.kodekeg set kegiatananggaran.anggaran=kegiatanskpd.anggaran';
	$num = db_query($sql);	
	
	drupal_set_message($num);
	
	$sql = 'update anggperkeganggaran inner join anggperkeg on anggperkeganggaran.kodekeg=anggperkeg.kodekeg and anggperkeganggaran.kodero=anggperkeg.kodero set anggperkeganggaran.anggaran=anggperkeg.anggaran';
	$num = db_query($sql);	
	
	drupal_set_message($num);
	
}

?>