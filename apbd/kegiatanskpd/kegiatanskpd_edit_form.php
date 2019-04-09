<?php
function kegiatanskpd_edit_form(&$form_state, $my_values = array()) {
    
    $referer = $_SERVER['HTTP_REFERER'];
	if (strpos($referer, 'kegiatanskpd/edit/')>0)
		$referer = $_SESSION["kegiatanskpd_lastpage"];
	else
		$_SESSION["kegiatanskpd_lastpage"] = $referer;	
	
	
    $kodekeg = arg(3);
	$kodeuk = apbd_getuseruk();
	if (isSuperuser()) {
		$kodeuk = $_SESSION['kodeuk'];
		$adminok = true;
	} else
		$adminok = false;
	
	//FILTER TAHUN-----
    $tahun = variable_get('apbdtahun', 0);
	$jenis = 2;
	
	//drupal_add_js('files/js/common.js');
	drupal_add_js('files/js/kegiatancam.js');
	drupal_add_css('files/css/kegiatancam.css');
    $disabled = FALSE;
    if (isset($kodekeg))
    {
        if (!user_access('kegiatanskpd edit'))
            drupal_access_denied();
		

			
        $sql = 'select k.kodepa, k.kodekeg, k.nomorkeg, k.tahun, k.kodepro, k.kodeuk, k.jenis, k.kodesuk, k.kegiatan, k.lokasi, 
				k.programsasaran, k.programtarget, k.masukansasaran, k.masukantarget, k.keluaransasaran,
				k.keluarantarget, k.hasilsasaran,  k.hasiltarget, k.total, k.plafon, k.totalsebelum, 
				k.totalsesudah, k.waktupelaksanaan, k.latarbelakang, k.dispensasi, k.kelompoksasaran, k.adminok, p.program, k.unlockpptk 
				from {kegiatanskpd} k left join {program} p on (k.kodepro = p.kodepro) where k.kodekeg=\'%s\'' ;
				
		//drupal_set_message($sql . $kodekeg);
        $res = db_query(db_rewrite_sql($sql), array ($kodekeg));
        if ($res) {
			$data = db_fetch_object($res);
			if ($data) {    
				$kodekeg = $data->kodekeg;
				$nomorkeg = $data->nomorkeg;
				$tahun = $data->tahun;
				$kodepro = $data->kodepro;
				$program = $data->program;
				$kodeuk = $data->kodeuk;
				$kodepa = $data->kodepa;
				$kodesuk = $data->kodesuk;
				$kegiatan = $data->kegiatan ;
				$lokasi = $data->lokasi;
				$jenis = $data->jenis;

				$programsasaran = $data->programsasaran;
				$programtarget = $data->programtarget;
				$masukansasaran = $data->masukansasaran;
				//$masukantarget = $data->masukantarget;
				$masukantarget = 'Rp ' . apbd_fn($data->total);
				$hasilsasaran = $data->hasilsasaran;
				$hasiltarget = $data->hasiltarget;
				$keluaransasaran = $data->keluaransasaran;
				$keluarantarget = $data->keluarantarget;
				
				$lockrek = (($data->lokasi=='') or ($data->programsasaran=='') or ($data->programtarget=='') or ($data->keluaransasaran=='') or ($data->keluarantarget=='') or ($data->hasilsasaran=='') or ($data->hasiltarget==''));
				
				$total = $data->total;
				$plafon = $data->plafon;
				$totalsebelum = $data->totalsebelum;
				$totalsesudah = $data->totalsesudah;
				
				$waktupelaksanaan = $data->waktupelaksanaan;
				$latarbelakang  = $data->latarbelakang;
				$kelompoksasaran = $data->kelompoksasaran;
				
				$adminok = ($adminok or $data->adminok);
				
				$dispensasi = $data->dispensasi;
				$unlockpptk = $data->unlockpptk;
				
				//$disabled =TRUE;
			} else {
				$kodekeg = '';
				$kegiatan = 'Kegiatan Baru';
			}
        } else {
			$kodekeg = '';
			$kegiatan = 'Kegiatan Baru';
		}
    } else {
		//$form['#title'] = 'Tambah Data Kegiatan Renja SKPD';
		$kodekeg = '';
		$kegiatan = 'Kegiatan Baru';
	
		if (!user_access('kegiatanskpd tambah'))
			drupal_access_denied();
    }

	drupal_set_title($kegiatan);
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 200px; float: left;}</style>');
	
	//Jam,Menit,Detik,Bulan,Hari,Tahun
	//'$batas = mktime(20, 0, 0, 3, 8, variable_get('apbdtahun', 0)) ;
	
	$allowedit = batastgl() || isSuperuser();//(($selisih>0) || (isSuperuser()));
	
	
	if ($allowedit==false) $allowedit = $dispensasi;
	if ($allowedit==false) {
		//dispensasirenja
        $sql = 'select dispensasibelanja from {unitkerja} where kodeuk=\'%s\'';
		$res = db_query(db_rewrite_sql($sql), array ($kodeuk));
		if ($res) {
			$data = db_fetch_object($res);
			if ($data) {  		
				$allowedit = $data->dispensasibelanja;
			}
		}
		
		if ($unlockpptk == 0) $unlockpptk = variable_get("unlockpptk", 0);
	}
	
	//TIDAK BOLEH MENGEDIT BILA BUKAN TAHUN AKTIF
	//$allowedit = ($allowedit and ($tahun == variable_get('apbdtahun', 0)));
	
	//if ($allowedit) {
		$disabled = 'false';
	//} else {
	//	$disabled = 'true';
	//}
	
	$form['kodekeg']= array(
		'#type'         => 'hidden', 
		'#title'        => 'kodekeg', 
		'#default_value'=> $kodekeg, 
	);

	$form['allowedit']= array(
		'#type'         => 'hidden', 
		'#title'        => 'kodekeg', 
		//'#description'  => 'kodekeg', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		////'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $allowedit, 
	);	
	$form['tahun']= array( 
		'#type'         => 'hidden', 
		'#title'        => 'tahun',  
		//'#description'  => 'tahun', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		////'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $tahun, 
	); 
	$form['kodepro']= array(
		'#type'         => 'hidden', 
		'#title'        => 'kodepro', 
		'#default_value'=> $kodepro, 
	);
	$form['kodeuk']= array(
		'#type'         => 'hidden', 
		'#title'        => 'SKPD',
		'#description'  => 'SKPD pelaksana kegiatan', 
		'#default_value'=> $kodeuk, 
	);  
	



	$form['programx']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Program', 
		'#description'  => 'Program kegiatan, ditentukan saat penyusunan RKPD dan KUA/PPA', 
		'#maxlength'    => 255, 
		'#size'         => 90, 
		////'#required'     => !$disabled, 
		//'#disabled'     => true, 
		'#default_value'=> $program, 
	);
		
	if ($jenis==1) {
		//drupal_set_message('x');
		//$tuvisibe = 'fieldset';
		
		$tuvisibe = 'hidden';
		$tureq=false;
		
		$programsasaran = '-';
		$programtarget = $programsasaran;
		
		$masukantarget = $programsasaran;
		$masukansasaran = $programsasaran;
		
		$keluarantarget = $programsasaran;
		$keluaransasaran = $programsasaran;

		$hasiltarget = $programsasaran;
		$hasilsasaran = $programsasaran;
		
	} else {
		$tuvisibe = 'fieldset';
		$tureq = true;
	}
	//TUK Program
	$form['tukprogram'] = array (
		'#type' => $tuvisibe,
		'#title'=> 'Program',
		'#collapsible' => true,
		'#collapsed' => true,     
	);
	$form['tukprogram']['programsasaran']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Tolok Ukur Kinerja', 
		'#description'  => 'Tolok ukur kinerja program, diisi untuk mendukung kinerja program sesuai dengan RPJMD',
		'#maxlength'    => 255, 
		'#size'         => 89, 
		//'#required'     => $tureq, 
		//'#disabled'     => $disabled,
		'#default_value'=> $programsasaran, 
	); 
	$form['tukprogram']['programtarget']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Target Kinerja', 
		'#description'  => 'Target kinerja program, diisi untuk memenuhi target pencapaian RPJMD',
		//attributes'	=> array('style' => 'text-align: right'),
		//size'         => 30, 
		'#maxlength'    => 255, 
		'#size'         => 89, 
		//'#required'     => $tureq, 
		//'#disabled'     => $disabled,
		'#default_value'=> $programtarget, 
	); 
	//TUK Masukan
	$form['tukmasukan'] = array (
		'#type' => $tuvisibe,
		'#title'=> 'Masukan (Input)',
		'#collapsible' => true,
		'#collapsed' => true,     
	);
	$form['tukmasukan']['masukansasaran']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Tolok Ukur Kinerja', 
		'#maxlength'    => 255, 
		'#size'         => 89, 
		//'#required'     => $tureq, 
		//'#disabled'     => $disabled,
		'#default_value'=> $masukansasaran, 
	); 
	$form['tukmasukan']['masukantarget']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Target Kinerja', 
		//attributes'	=> array('style' => 'text-align: right'),
		//size'         => 30, 
		'#maxlength'    => 255, 
		'#size'         => 89, 
		//'#required'     => $tureq, 
		//'#disabled'     => $disabled,
		'#default_value'=> $masukantarget, 
	); 
	//TUK Keluaran
	$form['tukkeluaran'] = array (
		'#type' => $tuvisibe,
		'#title'=> 'Keluaran (Output)',
		'#collapsible' => true,
		'#collapsed' => true,     
	);
	$form['tukkeluaran']['keluaransasaran']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Tolok Ukur Kinerja', 
		'#maxlength'    => 255,
		'#size'         => 89, 
		//'#required'     => $tureq, 
		//'#disabled'     => $disabled,
		'#default_value'=> $keluaransasaran, 
	); 
	$form['tukkeluaran']['keluarantarget']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Target Kinerja', 
		//attributes'	=> array('style' => 'text-align: right'),
		//size'         => 30, 
		'#maxlength'    => 255, 
		'#size'         => 89, 
		//'#required'     => $tureq, 
		//'#disabled'     => $disabled,
		'#default_value'=> $keluarantarget, 
	); 
	//TUK hasil
	$form['tukhasil'] = array (
		'#type' => $tuvisibe,
		'#title'=> 'Hasil (Outcome)',
		'#collapsible' => true,
		'#collapsed' => true,     
	);
	$form['tukhasil']['hasilsasaran']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Tolok Ukur Kinerja', 
		'#maxlength'    => 255, 
		'#size'         => 89, 
		//'#required'     => $tureq, 
		//'#disabled'     => $disabled,
		'#default_value'=> $hasilsasaran, 
	); 
	$form['tukhasil']['hasiltarget']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Target Kinerja', 
		//attributes'	=> array('style' => 'text-align: right'),
		//size'         => 30, 
		'#maxlength'    => 255, 
		'#size'         => 89, 
		//'#required'     => $tureq, 
		//'#disabled'     => $disabled,
		'#default_value'=> $hasiltarget, 
	); 
	
	$form['waktupelaksanaan']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Waktu Pelaksanaan', 
		'#description'  => 'Waktu pelaksana kegiatan, misalnya 3 bulan, 6 bulan atau 1 tahun',
		'#maxlength'    => 255, 
		'#size'         => 90, 		
		//'#required'     => true, 
		//'#disabled'     => $disabled,
		'#default_value'=> $waktupelaksanaan, 
	);	
	$form['latarbelakang']= array(
		'#type'         => 'hidden', 
		'#title'        => 'Latar Belakang', 
		'#maxlength'    => 255, 
		'#size'         => 90, 		
		//'#required'     => false, 
		//'#disabled'     => $disabled,
		'#default_value'=> $latarbelakang, 
	);	
	$form['kelompoksasaran']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Kelompok Sasaran', 
		'#maxlength'    => 255, 
		'#size'         => 90, 		
		//'#required'     => true, 
		//'#disabled'     => $disabled,
		'#default_value'=> $kelompoksasaran, 
	);	

	$form['lokasi'] = array (
		'#type'	=>'hidden',
		'#default_value'=>$lokasi
	);
	$form['lokasilabel']= array(
		'#type'         => 'item', 
		'#title'        => 'Lokasi',
		'#disabled'     => $disabled,
		'#description'  => 'Lokasi kegiatan, anda bisa mengisi beberapa lokasi untuk satu kegiatan',
		'#value'		=> "<div id='lokasi' style='float:left'><span id='lokasilabel'></span><div id='btnTambah' style='float:left;'><a href='#bds' class='btn_blue' style='color:#ffffff'>Tambah Lokasi</a></div></div><div style='clear:both'></div>"
		//'#cols'		=> '40',
		//'#rows'		=> '3',
		//'#disabled'     => true, 
		//'#description'  => 'lokasi', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		////'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		//'#default_value'=> $lokasi, 
		
	);

	
	$form['anggaran'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Isian Anggaran',
		'#collapsible' => true,
		'#collapsed' => false,        
	);
	
	$form['anggaran']['totalx']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Jumlah', 
		'#description'  => 'Jumlah anggaran tahun ini, akan terisi otomatis pada saat pengisian rekening',
		'#attributes'	=> array('style' => 'text-align: right'),
		'#size'         => 30, 
		//'#disabled'     => true, 
		'#default_value'=> $total, 
	); 
	$form['anggaran']['total']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $total, 
	); 
	$form['anggaran']['plafon']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Alokasi',
		'#description'  => 'Alokasi plafon yang sediakan untuk kegiatan ini, anggaran yang disusun tidak boleh melebihi batas plafon',
		'#attributes'	=> array('style' => 'text-align: right'),
		'#size'         => 30, 
		//'#disabled'     => true, 
		'#default_value'=> $plafon, 
	); 
	 $form['anggaran']['totalsebelum']= array(
		 '#type'         => 'textfield', 
		 '#title'        => 'Jumlah Tahun ' . ($tahun-1), 
		 '#description'  => 'Jumlah anggaran tahun lalu, bila ada',
		 '#attributes'	=> array('style' => 'text-align: right'),
		 '#size'         => 30, 
		 '#default_value'=> $totalsebelum, 
	 );
	 $form['anggaran']['totalsesudah']= array(
		 '#type'         => 'textfield', 
		 '#title'        => 'Jumlah Tahun ' . ($tahun+1), 
		 '#description'  => 'Perkiraan jumlah anggaran tahun depan, bila diperkirakan ada',
		 '#attributes'	=> array('style' => 'text-align: right'),
		 '#size'         => 30, 
		 '#default_value'=> $totalsesudah, 
	 );

	if ($adminok) {
		/*
		if ($lockrek==false) {
			$form['submitrek'] = array (
				'#type' => 'submit',
				'#value' => 'Rekening',
				//'#weight' => 23,
			);
		}
		*/

		$form['submitprint'] = array (
			'#type' => 'submit',
			'#value' => 'Preview RKA',
			//'#tree' => TRUE,
			//'#weight' => 23,
		);
	}
	
	if (($allowedit) or ($unlockpptk))
		$form['submit'] = array (
			'#type' => 'submit',
			'#suffix' => "&nbsp;<a href='" . $referer . "' class='btn_blue' style='color: white'>Tutup</a>",
			'#value' => 'Simpan'
		);
	
    return $form;
	
}
function kegiatanskpd_edit_form_validate($form, &$form_state) {

    //if ($form_state['values']['nk']=='') {
	//	form_set_error('', 'Kegiatan harus dipilih dari daftar kegiatan yang telah disediakan.');
    //}

	
	$e_kodekeg = $form_state['values']['e_kodekeg'];
	
	/*
	if ($e_kodekeg <> '') {

		$sql = sprintf("select sum(total) as totalsub from {kegiatanskpdsub} where kodekeg='%s'",
					   $e_kodekeg
					   );
		$result = db_query($sql);
		if ($data = db_fetch_object($result)) {
			
			$totalsub = $data->totalsub;
			$totalsub 	= is_numeric($totalsub) ? $totalsub : 0;
			if (($total <> $totalsub) and ($totalsub > 0)) {
				form_set_error('kegiatanx', 'Jumlah usulan sub kegiatan tidak sama [' . $total . ' : ' . $totalsub . ']' );
			}
			


		}
	}
	//END VALIDATE KODE
	*/
	
	/*

	if($form_state['values']['programsasaran'] == ''){  
		form_set_error('programsasaran', 'masih ada yang belum diisi' );
	};
	if($form_state['values']['programtarget'] == ''){
		form_set_error('programtarget', 'masih ada yang belum diisi' );
	}
	if($form_state['values']['masukansasaran'] == ''){  
		form_set_error('masukansasaran', 'masih ada yang belum diisi' );
	}
	if($form_state['values']['masukantarget'] == ''){  
		form_set_error('masukantarget', 'masih ada yang belum diisi' );
	}
	if($form_state['values']['keluaransasaran'] == ''){  
		form_set_error('keluaransasaran', 'masih ada yang belum diisi' );
	}
	if($form_state['values']['keluarantarget'] == ''){
		form_set_error('keluarantarget', 'masih ada yang belum diisi' );
	}
	if($form_state['values']['hasilsasaran'] == ''){
		form_set_error('hasilsasaran', 'masih ada yang belum diisi' );
	}
	if($form_state['values']['hasiltarget'] == ''){
		form_set_error('hasiltarget', 'masih ada yang belum diisi' );
	}
	if($form_state['values']['waktupelaksanaan'] == ''){ 
		form_set_error('waktupelaksanaan', 'Waktu Pelaksanaan belum diisi' );
	}
	/*if($form_state['values']['latarbelakang'] == ''){
		form_set_error('latarbelakang', 'Waktu Pelaksanaan belum diisi' );
	}*/
	/*
	if($form_state['values']['kelompoksasaran'] == ''){
		form_set_error('kelompoksasaran', 'Kelompok Sasaran belum diisi' );
	}
	*/
	/*
	$total = $form_state['values']['total'];
	$sumberdana1rp = $form_state['values']['sumberdana1rp'];
	$sumberdana2rp = $form_state['values']['sumberdana2rp'];
	if ($total != ($sumberdana1rp+$sumberdana2rp)) {
		form_set_error('sumberdana1rp', 'Isian sumber dana tidak sama dengan jumlah anggaran' );
	}
	*/
	
	$field = array('programsasaran','lokasi', 'programtarget', 'masukansasaran', 'masukantarget', 'hasilsasaran', 'hasiltarget', 'keluaransasaran', 'keluarantarget');
	$error = false;
	foreach($field as $data){
		if(preg_match('/[<@>]/', $form_state['values'][$data])){
			$error=true;
		}
	}
	if($error == true){
		form_set_error("akses",'Tidak boleh menggunakan < @ >');
	}
}

function kegiatanskpd_edit_form_submit($form, &$form_state) {
	
    if($form_state['clicked_button']['#value'] == $form_state['values']['submitrek']) {
       $kodekeg = $form_state['values']['kodekeg'];
	   $form_state['redirect'] = 'apbd/kegiatanskpd/rekening/' . $kodekeg ;
		//drupal_set_message('Next');
		
    } else if ($form_state['clicked_button']['#value'] == $form_state['values']['submitprint']) {
       $kodekeg = $form_state['values']['kodekeg'];
		$form_state['redirect'] = 'apbd/kegiatanskpd/print/' . $kodekeg;
		
	} else if ($form_state['clicked_button']['#value'] == $form_state['values']['submit']) {

		//$o_kodesuk = $kodeuk . substr($o_kodesuk,-2);
		$kodekeg = $form_state['values']['kodekeg'];

		$lokasi = $form_state['values']['lokasi'];
		//drupal_set_message($lokasi);

		$programsasaran = $form_state['values']['programsasaran'];
		$programtarget = $form_state['values']['programtarget'];
		$masukansasaran = $form_state['values']['masukansasaran'];
		$masukantarget = $form_state['values']['masukantarget'];
		$hasilsasaran = $form_state['values']['hasilsasaran'];
		$hasiltarget = $form_state['values']['hasiltarget'];
		$keluaransasaran = $form_state['values']['keluaransasaran'];
		$keluarantarget = $form_state['values']['keluarantarget'];
		
		$allowedit = $form_state['values']['allowedit'];

		
		//$total = $form_state['values']['total'];
		//$plafon = $form_state['values']['plafon'];
		$totalsebelum = $form_state['values']['totalsebelum'];
		$totalsesudah = $form_state['values']['totalsesudah'];
		
		$waktupelaksanaan = $form_state['values']['waktupelaksanaan'];
		$latarbelakang  = $form_state['values']['latarbelakang'];
		$kelompoksasaran = $form_state['values']['kelompoksasaran'];

		if ($allowedit) {

 
			$sql = sprintf("update {kegiatanskpd} set lokasi='%s', programsasaran='%s', programtarget='%s', masukansasaran='%s', masukantarget='%s', hasilsasaran='%s', hasiltarget='%s', keluaransasaran='%s', keluarantarget='%s', totalsebelum='%s', totalsesudah='%s', waktupelaksanaan='%s', latarbelakang='%s', kelompoksasaran='%s' where kodekeg='%s'",
			db_escape_string($lokasi), db_escape_string($programsasaran),
			db_escape_string($programtarget), db_escape_string($masukansasaran),					  
			db_escape_string($masukantarget), db_escape_string($hasilsasaran),
			db_escape_string($hasiltarget), db_escape_string($keluaransasaran),
			db_escape_string($keluarantarget), db_escape_string($totalsebelum),					  
			db_escape_string($totalsesudah), db_escape_string($waktupelaksanaan),
			db_escape_string($latarbelakang), db_escape_string($kelompoksasaran),
			$kodekeg);		
				
			$res = db_query($sql);
		} 
		
		if ($res) {
			
			
			drupal_set_message('Penyimpanan data berhasil dilakukan');	
			$referer = $_SESSION["kegiatanskpd_lastpage"];			
			drupal_goto($referer);    
		}
		else {
			//drupal_set_message($sql);		
			drupal_set_message($e_kodekeg.'Penyimpanan data tidak berhasil dilakukan'.$o_kodesuk);
		}
		
	} else {
		drupal_set_message('apa kabarnya'); 
	}
	

}

?>