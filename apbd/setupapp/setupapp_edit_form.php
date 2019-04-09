<?php
    
function setupapp_edit_form(){
	drupal_set_title('Konfigurasi Aplikasi');
    drupal_add_css('files/css/kegiatancam.css');		

    $form['formdata'] = array (
        '#type' => 'fieldset',
        '#title'=> '',
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,        
    );
    $tahun = arg(3);
	$revisi = 0;
	$uraian = variable_get("apbdkegiatan", 0);
	
	$cetakverifikasirka = variable_get("cetakverifikasirka", 0);
	$unlockrincianrekening = variable_get("unlockrincianrekening", 0);
	$unlockrincianpendapatan = variable_get("unlockrincianpendapatan", 0);
	$unlockpptk = variable_get("unlockpptk", 0);
	
	drupal_set_html_head('<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>');
    $disabled = FALSE;
    if (isset($tahun))
    {
        if (!user_access('setupapp edit'))
            drupal_access_denied();
			
        $sql = 'select tahun, revisi, uraian, tglbatasrka, tglbatasdpa, tglbatasrevisi, perdano, perdatgl, perbupno, perbuptgl, dpatgl, budnama, budnip, budjabatan, setdanama, setdanip, setdajabatan, dpabtlformat, dpablformat, dpapenformat, dpappkdpformat, dpappkdbformat from {setupapp} where tahun=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array ($tahun));
        if ($res) {
            $data = db_fetch_object($res);
			if ($data) {
                $tahun = $data->tahun;
				$revisi = $data->revisi;
                $tgltgl		=	$data->tglbatasdpa;
                $tglbatasrka = strtotime($data->tglbatasrka);
                $tglbatasdpa = strtotime($data->tglbatasdpa); 
                $tglbatasrevisi = strtotime($data->tglbatasrevisi); 

				$uraian = $data->uraian;
				
				$perdano = $data->perdano;
				$perdatgl = $data->perdatgl;
				
				$perbupno = $data->perbupno;
				$perbuptgl = $data->perbuptgl;
				
				$dpatgl = $data->dpatgl;
				$dpabtlformat = $data->dpabtlformat; 
				$dpablformat = $data->dpablformat; 
				$dpapenformat = $data->dpapenformat; 
				$dpappkdpformat = $data->dpappkdpformat; 
				$dpappkdbformat = $data->dpappkdbformat;
				
				$budnama = $data->budnama;
				$budnip = $data->budnip; 
				$budjabatan = $data->budjabatan;
				$setdanama = $data->setdanama;
				$setdanip = $data->setdanip;
				$setdajabatan = $data->setdajabatan;
				
                $disabled =TRUE;
			} else {
				$tahun = '';
			}
        } else {
			$tahun = '';

		}
    } else {
		if (!user_access('setupapp tambah'))
			drupal_access_denied();
		$form['formdata']['#title'] = 'Tambah Tahun Anggaran';
		$tahun = '';

        $tglbatasrka = '1420045200';
        $tglbatasdpa = $tglbatasrka; 
        $tglbatasrevisi = $tglbatasrka; 

	}
    
	//drupal_set_message($tglbatasrka);
	$form['formdata']['tahun']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Tahun', 
		//'#description'  => 'tahun', 
		'#maxlength'    => 4, 
		'#size'         => 6, 
		'#attributes'	=> array('style' => 'text-align: right'), 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#weight'     => -1, 
		'#default_value'=> $tahun, 
	); 
	$form['formdata']['revisi']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Revisi', 
		//'#description'  => 'tahun', 
		'#maxlength'    => 4, 
		'#size'         => 6, 
		'#attributes'	=> array('style' => 'text-align: right'), 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#weight'     => 0, 
		'#default_value'=> $revisi, 
	); 
	$form['formdata']['e_revisi']= array(
		'#type'         => 'value', 
		'#value'=> $revisi, 
	); 
	$form['formdata']['uraian']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Uraian', 
		//'#description'  => 'program', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $uraian, 
	); 

    $form['formdata']['perda'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Peraturan Daerah',
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,        
    );
	$form['formdata']['perda']['perdano']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Nomor Perda', 
		//'#description'  => 'program', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $perdano, 
	); 
	$form['formdata']['perda']['perdatgl']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Tgl. Perda', 
		//'#description'  => 'program', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $perdatgl, 
	); 


    $form['formdata']['perbub'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Peraturan Daerah',
        '#collapsible' => FALSE,
        '#collapsed' => FALSE,        
    );
	$form['formdata']['perbub']['perbupno']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Nomor Perbup', 
		//'#description'  => 'program', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $perbupno, 
	); 
	$form['formdata']['perbub']['perbuptgl']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Tgl. Perbup', 
		//'#description'  => 'program', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $perbuptgl, 
	); 

	//DPA
    $form['formdata']['dpaskpd'] = array (
        '#type' => 'fieldset',
        '#title'=> 'Format Nomor DPA',
        '#collapsible' => TRUE,
        '#collapsed' => ($revisi!=0),        
    );
	$form['formdata']['dpaskpd']['dpatgl']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Tgl. DPA', 
		//'#description'  => 'program', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $dpatgl, 
	); 

	$form['formdata']['dpaskpd']['dpapenformat']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Pendapatan', 
		//'#description'  => 'program', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $dpapenformat, 
	); 
	$form['formdata']['dpaskpd']['dpabtlformat']= array(
		'#type'         => 'textfield', 
		'#title'        => 'BTL SKPD', 
		//'#description'  => 'program', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $dpabtlformat, 
	); 
	$form['formdata']['dpaskpd']['dpablformat']= array(
		'#type'         => 'textfield', 
		'#title'        => 'BL SKPD', 
		//'#description'  => 'program', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $dpablformat, 
	); 

	$form['formdata']['dpaskpd']['dpappkdpformat']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Pendapatan PPKD', 
		//'#description'  => 'program', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $dpappkdpformat, 
	); 
	$form['formdata']['dpaskpd']['dpappkdbformat']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Belanja PPKD', 
		//'#description'  => 'program', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $dpappkdbformat, 
	); 	


	//TAPBD
	$form['formdata']['tapbd'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Pejabat TAPD',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);
	$form['formdata']['tapbd']['budnama']= array(
		'#type'         => 'textfield', 
		'#title'        => 'BUD Nama', 
		//'#description'  => 'program', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $budnama, 
	); 
	$form['formdata']['tapbd']['budjabatan']= array(
		'#type'         => 'textfield', 
		'#title'        => 'BUD Jabatan', 
		//'#description'  => 'program', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $budjabatan, 
	); 
	$form['formdata']['tapbd']['budnip']= array(
		'#type'         => 'textfield', 
		'#title'        => 'BUD NIP', 
		//'#description'  => 'program', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $budnip, 
	); 		
	
	$form['formdata']['tapbd']['setdanama']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Setda Nama', 
		//'#description'  => 'program', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $setdanama, 
	); 
	$form['formdata']['tapbd']['setdanip']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Setda NIP', 
		//'#description'  => 'program', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $setdanip, 
	); 
	$form['formdata']['tapbd']['setdajabatan']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Setda Jabatan', 
		//'#description'  => 'program', 
		'#maxlength'    => 255, 
		'#size'         => 60, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $setdajabatan, 
	); 	
	
	//RPTK
	$form['formdata']['penyusunan'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Penyusunan RKA-SKPD',
		'#collapsible' => true,
		'#collapsed' => false,     
		'#weight'     => 3,    
	);
	$form['formdata']['penyusunan']['tglbatasrka']= array(
		'#type'         => 'date', 
		'#title'        => 'Tgl. Penutupan RKA',
		//'#description'  => 'Tanggal dimulai pengisian usulan kegiatan', 
		'#default_value'=> array(
			'year' => format_date($tglbatasrka, 'custom', 'Y'),
			'month' => format_date($tglbatasrka, 'custom', 'n'), 
			'day' => format_date($tglbatasrka, 'custom', 'j'), 
		  ), 
	); 	
	$form['formdata']['penyusunan']['tglbatasdpa']= array(
		'#type'         => 'date', 
		'#title'        => 'Tgl. Penutupan DPA',
		//'#description'  => 'Batas akhir pengisian kegiatan, setelah tanggal ini sudah tidak bisa menambah/mengubah/menghapus kegiatan', 
		'#default_value'=> array(
			'year' => format_date($tglbatasdpa, 'custom', 'Y'),
			'month' => format_date($tglbatasdpa, 'custom', 'n'), 
			'day' => format_date($tglbatasdpa, 'custom', 'j'), 
		  ), 
	); 	
	$form['formdata']['penyusunan']['tglbatasrevisi']= array(
		'#type'         => 'date', 
		'#title'        => 'Tgl. Penutupan Revisi',
		//'#description'  => 'Batas akhir pengisian plafon anggaran, setelah tanggal ini sudah tidak bisa mengubah plafon anggaran', 
		'#default_value'=> array(
			'year' => format_date($tglbatasrevisi, 'custom', 'Y'),
			'month' => format_date($tglbatasrevisi, 'custom', 'n'), 
			'day' => format_date($tglbatasrevisi, 'custom', 'j'), 
		  ), 
	); 	
	
	$form['formdata']['penyusunan']['tglbatasrevisi']= array(
		'#type'         => 'date', 
		'#title'        => 'Tgl. Penutupan Revisi',
		//'#description'  => 'Batas akhir pengisian plafon anggaran, setelah tanggal ini sudah tidak bisa mengubah plafon anggaran', 
		'#default_value'=> array(
			'year' => format_date($tglbatasrevisi, 'custom', 'Y'),
			'month' => format_date($tglbatasrevisi, 'custom', 'n'), 
			'day' => format_date($tglbatasrevisi, 'custom', 'j'), 
		  ), 
	); 	

	
	$form['formdata']['penyusunan']['cetakverifikasirka']= array(
		'#type' => 'radios', 
		'#title' => t('Cetak Verifikasi RKA'), 
		//'#description'  => 'Jenis belanja',
		'#default_value' => $cetakverifikasirka,
		'#options' => array(	
			 '0' => t('Tidak'), 	
			 '1' => t('Cetak'),	
		   ), 
	);	

	$form['formdata']['penyusunan']['unlockrincianrekeningss1'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
	);	
	
	$form['formdata']['penyusunan']['unlockrincianrekening']= array(
		'#type' => 'radios', 
		'#title' => t('Unlock Rincian'), 
		//'#description'  => 'Jenis belanja',
		'#default_value' => $unlockrincianrekening,
		'#options' => array(	
			 '0' => t('Tidak'), 	
			 '1' => t('Unlock'),	
		   ), 
	);	

	$form['formdata']['penyusunan']['unlockrincianpendapatan1'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
	);	
	
	$form['formdata']['penyusunan']['unlockrincianpendapatan']= array(
		'#type' => 'radios', 
		'#title' => t('Unlock Rincian Pendapatan'), 
		//'#description'  => 'Jenis belanja',
		'#default_value' => $unlockrincianpendapatan,
		'#options' => array(	
			 '0' => t('Tidak'), 	
			 '1' => t('Unlock'),	
		   ), 
	);	

	$form['formdata']['penyusunan']['unlockpptkss1'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
	);	
	
	$form['formdata']['penyusunan']['unlockpptk']= array(
		'#type' => 'radios', 
		'#title' => t('Unlock PPTK'), 
		//'#description'  => 'Jenis belanja',
		'#default_value' => $unlockpptk,
		'#options' => array(	
			 '0' => t('Tidak'), 	
			 '1' => t('Unlock'),	
		   ), 
	);	


    $form['formdata']['e_tahun']= array( 
        '#type'         => 'hidden', 
        '#default_value'=> $tahun, 
    ); 
	
    $form['formdata']['submit'] = array (
        '#type' => 'submit',
		'#weight'     => 99, 
		'#suffix' => "&nbsp;<a href='/apbd/setupapp' class='btn_blue' style='color: white'>Batal</a>",
		'#value' => 'Simpan'
    );
    
    return $form;
}
function setupapp_edit_form_validate($form, &$form_state) {
	$revisi = $form_state['values']['revisi'];
	if ($revisi > 5 ) {
		form_set_error('', 'Revisi maksimal sampai dengan 5 (lima)');
	}            
}

function setupapp_edit_form_submit($form, &$form_state) {
    //clean_up_nol_x();
	
	
    $e_tahun = $form_state['values']['e_tahun'];
    
	$tahun = $form_state['values']['tahun'];
	$revisi = $form_state['values']['revisi'];
	$e_revisi = $form_state['values']['e_revisi'];
	$wilayah = 'KABUPATEN JEPARA';
	$uraian= $form_state['values']['uraian'];
	
	$perdano = $form_state['values']['perdano'];
	$perdatgl= $form_state['values']['perdatgl'];
	
	$perbupno  = $form_state['values']['perbupno'];
	$perbuptgl = $form_state['values']['perbuptgl'];
	
	//DPA
	$dpatgl = $form_state['values']['dpatgl'];
	
	$dpapenformat = $form_state['values']['dpapenformat'];
	$dpabtlformat = $form_state['values']['dpabtlformat'];
	$dpablformat = $form_state['values']['dpablformat'];

	$dpappkdpformat = $form_state['values']['dpappkdpformat'];
	$dpappkdbformat = $form_state['values']['dpappkdbformat'];

	
	//drupal_set_message($e_revisi);
	//drupal_set_message($revisi);
	

	
	//READ VARIABLE
    $tglbatasrka = $form_state['values']['tglbatasrka'];
    $tglbatasdpa = $form_state['values']['tglbatasdpa'];
    $tglbatasrevisi = $form_state['values']['tglbatasrevisi'];
	
	$cetakverifikasirka = $form_state['values']['cetakverifikasirka'];
	$unlockpptk = $form_state['values']['unlockpptk'];
	$unlockrincianrekening = $form_state['values']['unlockrincianrekening'];
	$unlockrincianpendapatan = $form_state['values']['unlockrincianpendapatan'];

    //FORMAT TANGGAL
    $tglbatasrkasql = $tglbatasrka['year'] . '-' . $tglbatasrka['month'] . '-' . $tglbatasrka['day'];
    $tglbatasdpasql = $tglbatasdpa['year'] . '-' . $tglbatasdpa['month'] . '-' . $tglbatasdpa['day'];
    $tglbatasrevisisql = $tglbatasrevisi['year'] . '-' . $tglbatasrevisi['month'] . '-' . $tglbatasrevisi['day'];

    if ($e_tahun=='') 
    {
        $sql = 'insert into {setupapp} (tahun, wilayah, uraian, tglbatasrka, tglbatasdpa, tglbatasrevisi) 
				values(%s, \'%s\', \'%s\', \'%s\', \'%s\', \'%s\')';        
				$res = db_query(db_rewrite_sql($sql), array($tahun, strtoupper($wilayah), $uraian, $tglbatasrkasql, $tglbatasdpasql, $tglbatasrevisisql));
    } else {

		//TAPD
		$budnama = $form_state['values']['budnama'];
		$budnip = $form_state['values']['budnip'];
		$budjabatan = $form_state['values']['budjabatan'];
		
		$setdanama = $form_state['values']['setdanama'];
		$setdanip = $form_state['values']['setdanip'];
		$setdajabatan = $form_state['values']['setdajabatan'];
	
		
		$res = db_query(db_rewrite_sql('update {setupapp} set tahun=%s, revisi=%s, uraian=\'%s\',tglbatasrka=\'%s\', 
				tglbatasdpa=\'%s\', tglbatasrevisi=\'%s\', perdano=\'%s\', perdatgl=\'%s\', perbupno=\'%s\', perbuptgl=\'%s\', dpatgl=\'%s\', dpabtlformat=\'%s\', dpablformat=\'%s\', dpapenformat=\'%s\', dpappkdpformat=\'%s\', dpappkdbformat=\'%s\', budnama=\'%s\', budnip=\'%s\', budjabatan=\'%s\', setdanama=\'%s\', setdanip=\'%s\', setdajabatan=\'%s\' where tahun=\'%s\''), array($tahun, $revisi, $uraian, $tglbatasrkasql, $tglbatasdpasql, 		
				$tglbatasrevisisql, $perdano, $perdatgl, $perbupno, $perbuptgl, $dpatgl, $dpabtlformat, $dpablformat,$dpapenformat, $dpappkdpformat, $dpappkdbformat, $budnama, $budnip, $budjabatan,$setdanama, $setdanip, $setdajabatan, $e_tahun));

					
    }
    if ($res) {
        drupal_set_message('Penyimpanan data berhasil dilakukan');
		if ($tahun == variable_get('apbdtahun', 0)) {
			variable_set('apbdrevisi', $revisi);
			variable_set('apbdwilayah', $wilayah);
			variable_set('apbdkegiatan', $uraian);
			
			//cetakverifikasirka
			variable_set('cetakverifikasirka', $cetakverifikasirka);
			variable_set('unlockpptk', $unlockpptk);
			variable_set('unlockrincianrekening', $unlockrincianrekening);
			variable_set('unlockrincianpendapatan', $unlockrincianpendapatan);
		}
    }
    else
        drupal_set_message('Penyimpanan data tidak berhasil dilakukan');
    drupal_goto('apbd/setupapp');    
	
}

function clean_up_nol_x() { 
	$sql = "select kodekeg,kodero,jumlah from {anggperkeg} where jumlah=0";
	$res = db_query($sql); 
	$i = 0;
	if ($res) {
		while ($data = db_fetch_object($res)) {
			$i++;	
			drupal_set_message($data->jumlah);
			$sql = "delete from {anggperkegdetil} where kodekeg='" . $data->kodekeg . "' and kodero='" . $data->kodero . "'";
			drupal_set_message($sql);
			//$res_x = db_query($sql);
			
		}
	}	
	drupal_set_message($i);
	$sql = "delete from {anggperkeg} where jumlah=0";
	drupal_set_message($sql);
	$res = db_query($sql); 
	
}
?>
