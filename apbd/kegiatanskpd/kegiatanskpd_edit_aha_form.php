<?php
function kegiatanskpd_edit_form(&$form_state, $my_values = array()) {
    
    
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
				k.totalsesudah, k.waktupelaksanaan, k.sumberdana1, k.sumberdana2, k.sumberdana1rp, 
				k.sumberdana2rp, k.latarbelakang, k.dispensasi, k.kelompoksasaran, k.adminok, p.program, k.unlockpptk 
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
				
				$sumberdana1 = $data->sumberdana1;
				$sumberdana2 = $data->sumberdana2;
				$sumberdana1rp = $data->sumberdana1rp;
				$sumberdana2rp = $data->sumberdana2rp;

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
		//'#description'  => 'kodekeg', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		////'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
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
		//'#description'  => 'kodepro', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		////'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodepro, 
	);
	
	/*
	$form['kegiatanx']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Kegiatan', 
		//'#description'  => 'kegiatanx', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		////'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kegiatan, 
	);
	*/

	if (isSuperuser()) {
		$form['program'] = array (
			'#type'		=> 'textfield',
			'#title'	=> 'Program',
			'#maxlength'    => 255, 
			'#size'         => 90, 
			//'#maxlength'    => 255, 
			//'#size'         => 60, 
			//'#disabled'     => true, 
			'#default_value' => $program,
		);
		$form['program-val']= array(
			'#type'         => 'hidden', 
			'#default_value'=> $program, 
		);
	
	} else {
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
	}
		
	$pquery = "select kodeuk, namasingkat from {unitkerja} where aktif=1 order by namauk" ;
	$pres = db_query($pquery);
	$skpd = array();
	//$dinas[''] = '--- pilih dinas teknis---';
	while ($data = db_fetch_object($pres)) {
		$skpd[$data->kodeuk] = $data->namasingkat;
		
	}
	$pquery = sprintf('select namasuk from {subunitkerja} where kodesuk=\'%s\'', $kodesuk);
	$pres = db_query($pquery);
	
	//$dinas[''] = '--- pilih dinas teknis---';
	while ($data = db_fetch_object($pres)) {
		$subbidang = $data->namasuk;
		//drupal_set_message($subbidang);
	}
	
	$pquery = sprintf('select namapa from {PelakuAktivitas} where kodepa=\'%s\'', $kodepa);
	$pres = db_query($pquery);
	
	//$dinas[''] = '--- pilih dinas teknis---';
	while ($data = db_fetch_object($pres)) {
		$namapa = $data->namapa;
		//drupal_set_message($namapa);
	}
	$skpdtype = 'hidden';
	if (isSuperuser() || isAdministrator())
		$skpdtype='select';

	$form['kodeuk']= array(
		'#type'         => 'hidden', 
		'#title'        => 'SKPD',
		'#options'		=> $skpd,
		'#description'  => 'SKPD pelaksana kegiatan', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		////'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeuk, 
	);  
//inisial default value ahah

	if(isSuperUser()){
		$first_ahah=$skpd[$kodeuk];
		$second_ahah=$subbidang;
	}
	else{
		$first_ahah=$subbidang;
		$second_ahah=$namapa;
	}

///ahah
	$initial_options = _ahah_example_get_first_dropdown_options($kodeuk);

 
  // if we have a value for the first dropdown from $form_state['values'] we use
  // this both as the default value for the first dropdown and also as a
  // parameter to pass to the function that retrieves the options for the
  // second dropdown.
  if (isSuperUser()) {
	  $master_selection = !empty($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $skpd[$kodeuk];
	  //drupal_set_message($kodesuk);
	  $form['skpd'] = array(
		'#type' => 'select',
		'#title' => 'SKPD pelaksana kegiatan',
		'#description'  => 'SKPD pelaksana kegiatan', 
		'#options' => $initial_options,
		'#default_value' => $first_ahah,
		'#ahah' => array(
		  'path' => 'apbd/kegiatanskpd/edit/callback',
		  'wrapper' => 'dependent-dropdown-wrapper',
		  // 'event' => 'change', // default value: does not need to be set explicitly.
		),
		'#attributes' => array('class' => 'skpd'),
		'#validated' => TRUE,
		//'#DANGEROUS_SKIP_CHECK'=>true
	  );

	  // The CSS for this module hides this next button if JS is enabled.
	  

	  $form['dependent_dropdown_holder'] = array(
		'#tree' => TRUE,
		'#prefix' => '<div id="dependent-dropdown-wrapper">',
		'#suffix' => '</div>',
	  );

	  $form['dependent_dropdown_holder']['dependent_dropdown'] = array(
		'#type' => 'select',
		'#title' => 'Bidang',

		// when the form is rebuilt during processing (either AJAX or multistep),
		// the $master_selction variable will now have the new value and so the
		// options will change.
		'#options' => _ahah_example_get_second_dropdown_options($master_selection),
		'#default_value' => $second_ahah,
		'#validated' => TRUE,
		//'#DANGEROUS_SKIP_CHECK'=>true
	  );
	}
	else{
	  $master_selection = !empty($form_state['values']['skpd']) ? $form_state['values']['skpd'] : $first_ahah;
	  //drupal_set_message($kodesuk);
	  
	  $form['skpd'] = array(
		'#type' => 'select',
		'#title' => 'Bidang',
		'#description'  => 'Bidang pelaksana kegiatan', 
		'#options' => $initial_options,
		'#default_value' => $first_ahah,
		'#ahah' => array(
		  'path' => 'apbd/kegiatanskpd/edit/callback',
		  'wrapper' => 'dependent-dropdown-wrapper',
		  // 'event' => 'change', // default value: does not need to be set explicitly.
		),
		'#attributes' => array('class' => 'skpd'),
		'#validated' => TRUE,
	  );

	  // The CSS for this module hides this next button if JS is enabled.
	  

	  $form['dependent_dropdown_holder'] = array(
		'#tree' => TRUE,
		'#prefix' => '<div id="dependent-dropdown-wrapper">',
		'#suffix' => '</div>',
	  );

	  $form['dependent_dropdown_holder']['dependent_dropdown'] = array(
		'#type' => 'select',
		'#title' => 'Seksi',

		// when the form is rebuilt during processing (either AJAX or multistep),
		// the $master_selction variable will now have the new value and so the
		// options will change.
		'#options' => _ahah_example_get_second_dropdown_options($master_selection),
		'#default_value' => $namapa,
		'#validated' => TRUE,
	  );
	}
  
//end ahah .........
	
	if (isSuperuser()){
		$form['kodesuk']= array(
			'#type'         => 'hidden', 
			'#title'        => 'Sub SKPD',
			'#default_value'=> $kodesuk, 
		); 			
		
	} else {
		
		//$qlike .= sprintf(' and k.kodeuk=\'%s\' ', $kodeuk);
		$pquery = sprintf('select kodesuk, namasuk from {subunitkerja} where kodeuk=\'%s\' order by kodesuk', $kodeuk);
		
		//drupal_set_message($pquery);
		
		$pres = db_query($pquery);
		$subskpd = array();
		$subskpd[''] = '- Pilih Bidang -';
		while ($data = db_fetch_object($pres)) {
			$subskpd[$data->kodesuk] = $data->namasuk;
		}
		
		/*
		$form['kodesuk']= array(
			'#type'         => 'select', 
			'#title'        => 'Bidang/Bagian',
			'#options'		=> $subskpd,
			//'#description'  => 'kodesuk', 
			//'#maxlength'    => 60, 
			//'#size'         => 20, 
			////'#required'     => !$disabled, 
			//'#disabled'     => $disabled, 
			'#default_value'=> $kodesuk, 
		); 	
		*/		
	}

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

	$pquery = "select sumberdana from {sumberdanalt} order by nomor" ;
	$pres = db_query($pquery);
	$sumberdana = array();
	$sumberdana[''] = '-Pilih Sumber Dana-';
	while ($data = db_fetch_object($pres)) {
		$sumberdana[$data->sumberdana] = $data->sumberdana;
	}
	
	$sd_type = 'hidden';
	if (isSuperuser()) {

		$form['sumberdana'] = array (
			'#type' => 'fieldset',
			'#title'=> 'Sumber Dana',
			'#collapsible' => true,
			'#collapsed' => false,        
		);	
		$form['sumberdana']['sumberdana1']= array(
			'#type'         => 'select', 
			'#title'        => 'Sumber Dana', 
			'#description'  => 'Sumber dana kegiatan',
			'#options'		=> $sumberdana,
			'#width'         => 30, 
			'#default_value'=> $sumberdana1, 
		); 
		$form['sumberdana']['sumberdana1rp']= array(
			'#type'         => 'textfield', 
			'#title'        => 'Jumlah',
			'#description'  => 'Nominal (nilai rupiah) sumber dana kegiatan',
			'#attributes'	=> array('style' => 'text-align: right'),
			'#size'         => 30, 
			'#default_value'=> $sumberdana1rp, 
		); 
	} else {
		$form['sumberdana1']= array(
			'#type'         => 'hidden', 
			'#default_value'=> $sumberdana1, 
		); 
		$form['sumberdana1rp']= array(
			'#type'         => 'hidden', 
			'#default_value'=> $sumberdana1rp, 
		); 
		
	}
	/*
	$form['sumberdana']['sumberdana2']= array(
		'#type'         => 'select', 
		'#options'		=> $sumberdana,
		'#description'  => 'Sumber dana kedua, untuk kegiatan yang didanai oleh dua sumber dana',
		'#title'        => 'Sumber Dana #2', 
		'#width'         => 30, 
		'#default_value'=> $sumberdana2, 
	); 
	$form['sumberdana']['sumberdana2rp']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Sumber Dana #2 Rp',
		'#description'  => 'Nomnal (nilai rupiah) sumber dana kedua',
		'#attributes'	=> array('style' => 'text-align: right'),
		'#size'         => 30, 
		'#default_value'=> $sumberdana2rp, 
	); 
	*/
	 		
    $form['e_kodekeg']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $kodekeg, 
    ); 
	
    $form['e_kodepro']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $kodepro, 
    ); 

    $form['e_kodeuk']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $kodeuk, 
    ); 
    $form['e_nomorkeg']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $nomorkeg, 
    ); 

	if ($adminok) {
		if ($lockrek==false) {
			$form['submitrek'] = array (
				'#type' => 'submit',
				'#value' => 'Rekening',
				//'#weight' => 23,
			);
		}

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
			'#suffix' => "&nbsp;<a href='/apbd/kegiatanskpd' class='btn_blue' style='color: white'>Tutup</a>",
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
}

function kegiatanskpd_edit_form_submit($form, &$form_state) {
	drupal_set_message('Hello'); 
	
    if($form_state['clicked_button']['#value'] == $form_state['values']['submitrek']) {
       $e_kodekeg = $form_state['values']['e_kodekeg'];
	   $form_state['redirect'] = 'apbd/kegiatanskpd/rekening/' . $e_kodekeg ;
		//drupal_set_message('Next');
		
    } else if ($form_state['clicked_button']['#value'] == $form_state['values']['submitprint']) {
       $e_kodekeg = $form_state['values']['e_kodekeg'];
		//drupal_set_message('Next');
		
	} else if ($form_state['clicked_button']['#value'] == $form_state['values']['submit']) {
		drupal_set_message('apa kabar'); 
		
		$e_kodekeg = $form_state['values']['e_kodekeg'];
		$e_kodeuk = $form_state['values']['e_kodeuk'];
		$e_kodepro = $form_state['values']['e_kodepro'];
		$skpd = $form_state['values']['skpd'];
		$ahah_second = $form_state['values']['dependent_dropdown_holder']['dependent_dropdown'];
		drupal_set_message('second = '.$ahah_second);
		$o_kodepa='';
		if(isSuperUser()){
			$pquery = sprintf('select kodeuk from {unitkerja} where namasingkat=\'%s\'', $skpd);
			$pres = db_query($pquery);
			while ($data = db_fetch_object($pres)) {
				//$o_kodeuk = '';
				$e_kodeuk = $data->kodeuk;
			}
			//$pquery = sprintf('select kodesuk from {subunitkerja} where kodeuk=\'%s\' and namasuk=\'%s\'', $e_kodeuk, $ahah_second);
			$pquery = sprintf('select kodesuk from {subunitkerja} where kodeuk=\'%s\' and namasuk=\'%s\'', $e_kodeuk, $ahah_second);
			$pres = db_query($pquery);
			while ($data = db_fetch_object($pres)) {
				//$o_kodeuk = '';
				$o_kodesuk = $data->kodesuk;
			}
			
		}
		else {
			
			$e_kodeuk = apbd_getuseruk();
			
			$pquery = sprintf('select kodesuk from {subunitkerja} where kodeuk=\'%s\' and namasuk=\'%s\'', $e_kodeuk, $skpd);
			//$pquery = sprintf('select kodesuk from {subunitkerja} where kodeuk=\'%s\' and namasuk=\'%s\'', $e_kodeuk, $ahah_second);
			
			drupal_set_message($pquery);
			
			$pres = db_query($pquery);
			while ($data = db_fetch_object($pres)) {
				//$o_kodeuk = '';
				$o_kodesuk = $data->kodesuk;
			}
			$pquery = sprintf('select kodepa from {PelakuAktivitas} where kodesuk=\'%s\' and namapa=\'%s\'', $o_kodesuk, $ahah_second);
			$pres = db_query($pquery);
			while ($data = db_fetch_object($pres)) {
				$o_kodepa = $data->kodepa;
			}
			
		}

		drupal_set_message($e_kodeuk);
		drupal_set_message($o_kodesuk);
		drupal_set_message($o_kodepa);
		
		//$o_kodesuk = $kodeuk . substr($o_kodesuk,-2);
		$kodepro = $form_state['values']['kodepro'];
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
		
		$sumberdana1 = $form_state['values']['sumberdana1'];
		$sumberdana2 = '';		//$form_state['values']['sumberdana2'];
		$sumberdana1rp = $form_state['values']['sumberdana1rp'];
		$sumberdana2rp = 0;		//$form_state['values']['sumberdana2rp'];

		//drupal_set_message($sumberdana1rp);
		//drupal_set_message($sumberdana2rp);
		//drupal_set_message('ini total : ' . $total);
		
		if (($sumberdana1 . $sumberdana2) == '') $sumberdana1 = 'DAU';
		if (($sumberdana1rp + $sumberdana2rp) == '') $sumberdana1rp = $total;
		
		$waktupelaksanaan = $form_state['values']['waktupelaksanaan'];
		$latarbelakang  = $form_state['values']['latarbelakang'];
		$kelompoksasaran = $form_state['values']['kelompoksasaran'];

		if ($allowedit) {

 
			$kodeberubah=false;
			if ($e_kodepro == $kodepro){// && ($e_kodeuk == $kodeuk)) 
			
				$kodekeg = $e_kodekeg;
				
			} else {
				$tahun = variable_get('apbdtahun', 0);
				$kodekeg = $tahun . $kodeuk . $kodepro;			
				$kodekeg .= apbd_getcounterkegiatan($kodekeg);
				
				$kodeberubah=true;
			}		
			
			if (isSuperUser())
				$sql = sprintf("update {kegiatanskpd} set lokasi='%s', programsasaran='%s', programtarget='%s', masukansasaran='%s', masukantarget='%s', hasilsasaran='%s', hasiltarget='%s', keluaransasaran='%s', keluarantarget='%s', totalsebelum='%s', totalsesudah='%s', sumberdana1='%s', sumberdana1rp='%s', sumberdana2='%s', sumberdana2rp='%s', waktupelaksanaan='%s', latarbelakang='%s', kelompoksasaran='%s', kodeuk='%s', kodesuk='%s', kodepro='%s', kodekeg='%s' where kodekeg='%s'",
				db_escape_string($lokasi), db_escape_string($programsasaran),
				db_escape_string($programtarget), db_escape_string($masukansasaran),					  
				db_escape_string($masukantarget), db_escape_string($hasilsasaran),
				db_escape_string($hasiltarget), db_escape_string($keluaransasaran),
				db_escape_string($keluarantarget), db_escape_string($totalsebelum),					  
				db_escape_string($totalsesudah), db_escape_string($sumberdana1),					  
				db_escape_string($sumberdana1rp), db_escape_string($sumberdana2),					  
				db_escape_string($sumberdana2rp), db_escape_string($waktupelaksanaan),
				db_escape_string($latarbelakang), db_escape_string($kelompoksasaran),
				db_escape_string($e_kodeuk), db_escape_string($o_kodesuk),
				db_escape_string($kodepro), db_escape_string($kodekeg),
				$e_kodekeg);		
				
			else 
				$sql = sprintf("update {kegiatanskpd} set lokasi='%s', programsasaran='%s', programtarget='%s', masukansasaran='%s', masukantarget='%s', hasilsasaran='%s', hasiltarget='%s', keluaransasaran='%s', keluarantarget='%s', totalsebelum='%s', totalsesudah='%s', sumberdana1='%s', sumberdana1rp='%s', sumberdana2='%s', sumberdana2rp='%s', waktupelaksanaan='%s', latarbelakang='%s', kelompoksasaran='%s', kodesuk='%s', kodepro='%s', kodekeg='%s', kodepa='%s' where kodekeg='%s'",
				db_escape_string($lokasi), db_escape_string($programsasaran),
				db_escape_string($programtarget), db_escape_string($masukansasaran),					  
				db_escape_string($masukantarget), db_escape_string($hasilsasaran),
				db_escape_string($hasiltarget), db_escape_string($keluaransasaran),
				db_escape_string($keluarantarget), db_escape_string($totalsebelum),					  
				db_escape_string($totalsesudah), db_escape_string($sumberdana1),					  
				db_escape_string($sumberdana1rp), db_escape_string($sumberdana2),					  
				db_escape_string($sumberdana2rp), db_escape_string($waktupelaksanaan),
				db_escape_string($latarbelakang), db_escape_string($kelompoksasaran),
				db_escape_string($o_kodesuk), db_escape_string($kodepro), 
				db_escape_string($kodekeg), db_escape_string($o_kodepa), $e_kodekeg);					
			$res = db_query($sql);
			//drupal_set_message($sql);		
			
		
		} else {
				$sql = sprintf("update {kegiatanskpd} set kodesuk='%s', kodepa='%s' where kodekeg='%s'",
								db_escape_string($o_kodesuk),
								db_escape_string($o_kodepa),
								$e_kodekeg);		
				$res = db_query($sql);
				//drupal_set_message($sql);				
		}
		
		if ($res) {
			
			if ($kodeberubah) {
				//Update anggperkeg
				$sql = sprintf("update {anggperkeg} set kodekeg='%s' where kodekeg='%s'",
								db_escape_string($kodekeg),
								$e_kodekeg);		
				$res = db_query($sql);

				//Update anggperkegdetil
				$sql = sprintf("update {anggperkegdetil} set kodekeg='%s' where kodekeg='%s'",
								db_escape_string($kodekeg),
								$e_kodekeg);		
				$res = db_query($sql);
				
			}
			
			drupal_set_message('Penyimpanan data berhasil dilakukan');		
			drupal_goto('apbd/kegiatanskpd');    
		}
		else {
			//drupal_set_message($sql);		
			drupal_set_message($e_kodekeg.'Penyimpanan data tidak berhasil dilakukan'.$o_kodesuk);
		}
		
	} else {
		drupal_set_message('apa kabarnya'); 
	}
	

}


function ahah_example_dropdown_callback() {
  $form = ahah_example_callback_helper();

  $changed_elements = $form['dependent_dropdown_holder'];

  // Prevent duplicate wrappers.
  //unset($changed_elements['#prefix'], $changed_elements['#suffix']);

  $output = theme('status_messages') . drupal_render($changed_elements);

  
  drupal_json(array(
    'status'   => TRUE,
    'data'     => $output,
  ));
}

/**
* Submit handler for 'continue_to_dependent_dropdown'.
*/


/**
 * Default submit handler for form. This one happens when the main submit
 * button is pressed.
 */


/**
 * Helper function to populate the first dropdown. This would normally be
 * pulling data from the database.
 *
 * @return array of options
 */
function _ahah_example_get_first_dropdown_options($kodeuk) {
  // drupal_map_assoc() just makes an array('Strings' => 'Strings'...).
  if(isSuperUser()){
	 $query="select * from unitkerja"; 
	 $result=db_query($query);
	  $n=0;
	  $arr =array();
	 if ($result) {
		  while ($data = db_fetch_object($result)) {
			  $arr[$data->kodeuk]=$data->namasingkat;
			  $n++;
		  }
	  }
  }
  else{
	  $query = sprintf('select * from {subunitkerja} where kodeuk=\'%s\'', $kodeuk);
	  $result=db_query($query);
	  $n=0;
	  $arr =array();
	  $arr[''] = '-Pilih Bidang-';
	 if ($result) {
		  while ($data = db_fetch_object($result)) {
			  $arr[$data->kodesuk]=$data->namasuk;
			  $n++;
		  }
	  }
  }
  
  
  //$arr =array(t('Strings'), t('Woodwinds'), t('Brass'), t('Percussion'));
  return drupal_map_assoc($arr);
}

/**
 * Helper function to populate the second dropdown. This would normally be
 * pulling data from the database.
 *
 * @param key. This will determine which set of options is returned.
 *
 * @return array of options
 */
function _ahah_example_get_second_dropdown_options($key = '') {
  //SELECT  s.namasuk , u.kodeuk FROM `subunitkerja` as s inner join unitkerja as u on s.kodeuk=u.kodeuk
  if(isSuperUser()){
	  $query="SELECT  s.namasuk , u.namauk,u.namasingkat FROM `subunitkerja` as s inner join unitkerja as u on s.kodeuk=u.kodeuk";
	  $result=db_query($query);
	  $n=0;
	  $options =array();
	 if ($result) {
		  while ($data = db_fetch_object($result)) {
			  $options[$data->namasingkat][$data->namasuk]=$data->namasuk;
			  $n++;
		  }
	  }
	}
	else{
		$query="SELECT  s.namapa , u.namasuk FROM `PelakuAktivitas` as s inner join subunitkerja as u on s.kodesuk=u.kodesuk";
		  $result=db_query($query);
		  $n=0;
		  $options =array();
		 if ($result) {
			  while ($data = db_fetch_object($result)) {
				  $options[$data->namasuk][$data->namapa]=$data->namapa;
				  $n++;
			  }
		  }
	}
	
  
  
  
 
  
  if (isset($options[$key])) {
    return $options[$key];
  }
  else {
    return array();
  }
}

?>