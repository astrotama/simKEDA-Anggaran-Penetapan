<?php
function dpa_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    drupal_set_html_head($h);
	drupal_add_css('files/css/kegiatancam.css');
	$qlike='';
	$limit = 15;
	
	$kodesuk = '';
	$tahun = variable_get('apbdtahun', 0);
	$ntitle = 'DPA-SKPD';
    if ($arg) {
		switch($arg) {
			case 'show':
				//$qlike = " and lower(k.kegiatan) like lower('%%%s%%')";    
				$qlike = sprintf(" and lower(k.kegiatan) like lower('%%%s%%') ", db_escape_string(arg(3)));	
				//drupal_set_message(arg(4));
				break;
			case 'filter':
				$nntitle ='';
				$kodeuk = arg(3);
				$sumberdana = arg(4);
				$kodesuk = arg(5);
				$jenis = arg(6);
				$statustw = arg(7);
				$bintang = arg(8);

				break;

			default:
				drupal_access_denied();
				break;
		}
	} else {
		$tahun = variable_get('apbdtahun', 0);
		$sumberdana = $_SESSION['sumberdana'];
		$jenis = $_SESSION['jenis'];
		$bintang = $_SESSION['bintang'];
		

	}
	
	//drupal_set_message('m . ' . $bintang);
	
	if (isSuperuser()) {
		$kodeuk = $_SESSION['kodeuk'];
		if ($kodeuk == '') 	$kodeuk = '00';
		
		
	} else {
		$kodeuk = apbd_getuseruk();
		if (isUserKecamatan())
			$kodesuk = apbd_getusersuk();
		else
			$kodesuk = $_SESSION['kodesuk'];
	}	
	if (isSuperuser()) {
		if ($kodeuk !='00') {
			$qlike .= sprintf(' and k.kodeuk=\'%s\' ', $kodeuk);
			$pquery = sprintf("select kodeuk, namasingkat from {unitkerja} where kodeuk='%s'", db_escape_string($kodeuk));
			$presult = db_query($pquery);
			if ($data=db_fetch_object($presult)) {
				$ntitle .= ' ' . $data->namasingkat;
			}
		} 
		$adminok = true;
							
	} else {
		$qlike .= sprintf(' and k.plafon>0 and k.kodeuk=\'%s\' ', $kodeuk);
		if ($kodesuk != '') {
			$qlike .= sprintf(' and (k.kodesuk=\'%s\' ', $kodesuk);
			$qlike .= " or k.kodesuk='')";
		}
		
		$adminok = false;
	}



	//STATUS TW
	if ($statustw=='sudah') {
		$qlike .= sprintf(' and k.total>0 and (k.total=(k.tw1+k.tw2+k.tw3+k.tw4)) ');
	} elseif ($statustw=='belum') {
		$qlike .= sprintf(' and k.total>0 and (k.total>(k.tw1+k.tw2+k.tw3+k.tw4)) ');
	}
	

	//STATUS GAJI
	if ($jenis=='gaji') {
		$qlike .= sprintf(' and k.jenis=1 and k.isppkd=0 ');
	} elseif ($jenis=='langsung') {
		$qlike .= sprintf(' and k.jenis=2 ');
	} elseif ($jenis=='ppkd') {
		$qlike .= sprintf(' and k.jenis=1 and k.isppkd=1 ');
	}
	
	//SUMBER DANA
	if ($sumberdana != '') {
		 
		$ntitle .= ' ' . $sumberdana;
	}
			
	//$output .= drupal_get_form('dpa_transfer_form');
	$output .= drupal_get_form('dpa_main_form');
	if (isSuperuser()) {
		$header = array (
			array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
			array('data' => '', 'width' => '5px', 'valign'=>'top'),
			array('data' => 'SKPD', 'field'=> 'namasingkat', 'valign'=>'top'),
			array('data' => 'Kegiatan', 'field'=> 'kegiatan', 'valign'=>'top'),
			array('data' => 'Anggaran', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'TW #1', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'TW #2', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'TW #3', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'TW #4', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => '', 'width' => '220px', 'valign'=>'top'),
		);
	} else {
		$header = array (
			array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
			array('data' => '', 'width' => '5px', 'valign'=>'top'),
			array('data' => 'Kegiatan', 'field'=> 'kegiatan', 'valign'=>'top'),
			array('data' => 'Anggaran', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'TW #1', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'TW #2', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'TW #3', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => 'TW #4', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => '', 'width' => '120px', 'valign'=>'top'),
		);
	}
	$tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by k.kegiatan';
    }
	
	
	if ($bintang == 'rek') {
		$qlike .= ' and k.kodekeg in (select b.kodekeg from {bintangrekening} b ) ';		
	} elseif ($bintang == 'det') {
		$qlike .= ' and k.kodekeg in (select b.kodekeg from {bintangdetil} b ) ';		
	} elseif ($bintang == 'pot') {
		$qlike .= ' and k.kodekeg in (select b.kodekeg from {bintangtunda} b ) ';
	} elseif ($bintang == 'keg') {	
		$qlike .= ' and (k.bintang=1) ';
	}	    

	$where = ' where k.inaktif=0 and k.total>0 ' . $qlike ;
	
	//drupal_set_message($where);
	$pquery = 'select sum(total) jumlahx from {kegiatanskpd} k ' . $where;
	$pres = db_query($pquery);	
	if ($data = db_fetch_object($pres))
		$ntitle .= ', Jumlah Anggaran : ' . apbd_fn($data->jumlahx);
	
	drupal_set_title($ntitle);	
	
	$fsql = "select k.kodekeg,k.nomorkeg,k.tahun,k.kodepro,k.kodeuk,k.kegiatan,k.jenis,
			k.total,k.anggaran,u.namasingkat, k.adminok,  k.tw1, k.tw2, k.tw3, k.tw4, k.bintang  from {kegiatanskpd} k left join {unitkerja} u on ( k.kodeuk=u.kodeuk) " . $where;
			
    //drupal_set_message($fsql);
    $countsql = "select count(*) as cnt from {kegiatanskpd} k" . $where;
    //$fcountsql = sprintf($countsql, addslashes($nama));
	$fcountsql = $countsql;
    $result = pager_query($fsql . $tablesort, $limit, 0, $fcountsql);

	//Jam,Menit,Detik,Bulan,Hari,Tahun
	//$batas = mktime(20, 0, 0, 6, 16, 2015) ;
	//$sekarang = time () ;
	//$selisih =($batas-$sekarang) ;
	$allowedit = true;		//(($selisih>0) || (isSuperuser()));
	
	//CEK TAHUN
	//$allowedit = ($allowedit and ($tahun == variable_get('apbdtahun', 0)));
    
    $no=0;
    $page = $_GET['page'];
    if (isset($page)) {
        $no = $page * $limit;
    } else {
        $no = 0;
    }
    if ($result) {
        while ($data = db_fetch_object($result)) {
			$editlink = '';
			if (isSuperuser()) {
				$editlink .=  l('Bintang', 'bintang/' . $data->kodekeg, array('html'=>TRUE));
				//$editlink .= 'Bintang';
			}
			
			//$strperpanjangan = '';
			//if ($data->dispensasi) $strperpanjangan = ' ***/Perpanjangan RKA\***';
			
			if (user_access('kegiatanskpd edit')) {
				//$kegname = l($data->kegiatan, 'apbd/kegiatanskpd/edit/' . $data->kodekeg , array('attributes' => array('target' => '_blank'), 'html' =>TRUE));
				$kegname = l($data->kegiatan, 'apbd/kegiatanskpd/edit/' . $data->kodekeg , array('html' =>TRUE));
			} else {
				$kegname = $data->kegiatan;
			}


			$editlink .= "&nbsp;" .  l('Triwulan', 'apbd/kegiatanskpd/triwulan/' . $data->kodekeg, array('html'=>TRUE));
			//Cetak
			
			if ($data->total==($data->tw1+$data->tw2+$data->tw3+$data->tw4)) {
				$editlink .= "&nbsp;" . l('Sampul', 'apbd/kegiatanskpd/print/' . $data->kodekeg . '/10/dpa/pdf/sampul' , array('html'=>TRUE)) ;
				if ($data->jenis==1) {
					$editlink .= "&nbsp;" . l('Cetak(2.1)', 'apbd/kegiatanskpd/print/' . $data->kodekeg . '/10/dpa' , array('html'=>TRUE)) ;
				} else {
					$editlink .= "&nbsp;" . l('Cetak(2.2.1)', 'apbd/kegiatanskpd/print/' . $data->kodekeg . '/10/dpa' , array('html'=>TRUE)) ;
				}
			} else
				$editlink .= "&nbsp;" . 'Sampul' . "&nbsp;" . 'Cetak';
				
            $no++;
			
			/*
			if  (($bintang == 'rek') or ($bintang == 'det') or ($bintang == 'pot') or ($bintang == 'keg'))  {
				$statuskegiatan = "<img src='/files/bintang.png'>";
				
			} else {
				if ($data->bintang==0) {
					$statuskegiatan = get_bintang($data->kodekeg);
					$str_agg = "";
				} else {
					$statuskegiatan = "<img src='/files/bintang.png'>";
					$str_agg = "<p align='right'><font color='red'>" . apbd_fn($data->anggaran) . "</font></p>";
				}
			}
			*/
			if ($data->bintang==0) {
				$statuskegiatan = '';
				$str_agg = "";
			} else {
				$statuskegiatan = "<img src='/files/bintang.png'>";
				//$str_agg = "<p align='right'><font color='red'>" . apbd_fn($data->anggaran) . "</font></p>";
				$str_agg = "";
			}
			
			if (isSuperuser()) { 
				
				$rows[] = array (
					array('data' => $no, 'align' => 'right', 'valign'=>'top'),
					array('data' => $statuskegiatan, 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->namasingkat, 'align' => 'left', 'valign'=>'top'),
					array('data' => $kegname, 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fn($data->total) . $str_agg, 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->tw1), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->tw2), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->tw3), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->tw4), 'align' => 'right', 'valign'=>'top'),
					array('data' => $editlink, 'align' => 'left', 'valign'=>'top'),
				);
			} else {
				$rows[] = array (
					array('data' => $no, 'align' => 'right', 'valign'=>'top'),
					array('data' => $statuskegiatan, 'align' => 'left', 'valign'=>'top'),
					array('data' => $kegname, 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fn($data->total), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->tw1), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->tw2), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->tw3), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->tw4), 'align' => 'right', 'valign'=>'top'),
					array('data' => $editlink, 'align' => 'left', 'valign'=>'top'),

				);
			}
		}
    } else {
        $rows[] = array (
            array('data' => 'data kosong, silahkan menambahkan', 'colspan'=>'3')
        );
    }
	$btn = "";

	if ($kodeuk != '00') {
		$btn .= l('Sampul Depan', 'apbd/kegiatanskpd/print/' . $kodeuk . '/10/dpa/pdf/sampuld', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));

		$btn .= "&nbsp;" . l('Ringkasan APBD (DPA-SKPD)', 'apbd/laporanpenetapan/rka/ringkasananggaran/' . $kodeuk . '/3/10/dpa', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;
		
		$btn .= "&nbsp;" . l('Sampul DPA Pendapatan', 'apbd/kegiatanskpd/print/' . $kodeuk . '/10/dpa/pdf/sampulp' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;

		$btn .= "&nbsp;" . l('Pendapatan (DPA-SKPD 1)', 'apbd/pendapatan/print/' . $kodeuk . '/10/dpa' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;

		$btn .= "&nbsp;" . l('Rekap Belanja Langsung (DPA-SKPD 2.2)', 'apbd/laporanpenetapan/rka/rekapaggblprogramtw/' . $kodeuk . '/10/dpa' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;
	}
    $output .= $btn . theme_box('', theme_table($header, $rows)) . $btn;

	
	//    $output .= theme_box('', theme_table($header, $rows));
//	if (user_access('kegiatanskpd tambah'))
//		$output .= l("<img src='/files/button-add.png' title='Tambah data baru'>", 'apbd/kegiatanskpd/edit/' , array('html'=>TRUE)) ;
//	if (user_access('kegiatanskpd pencarian'))		
//        $output .= l("<img src='/files/button-search.png' title='Pencarian data'>", 'apbd/kegiatanskpd/find/' , array('html'=>TRUE)) ;
    $output .= theme ('pager', NULL, $limit, 0);
    return $output;
}


function dpa_main_form() {
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Pilihan Data',
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);
	$filter = arg(2);
	if (isset($filter) && ($filter=='filter')) {
		$kodeuk = arg(3);
		$sumberdana = arg(4);
		$kodesuk = arg(5);
		$jenis = arg(6);
		$statustw = arg(7);
		$bintang = arg(8);
		
	} else {
		$sumberdana = $_SESSION['sumberdana'];
		$statustw = $_SESSION['statustw'];	
		$jenis = $_SESSION['jenis'];
		$bintang = $_SESSION['bintang'];		

		if (isSuperuser()) 
			$kodeuk = $_SESSION['kodeuk'];
		else
			$kodesuk = $_SESSION['kodesuk'];
	}
		   
	if (!isSuperuser()) {
		$typeuk = 'hidden';
		$kodeuk = apbd_getuseruk();
		
		$typesuk ='select';

		$qlike .= sprintf(' and k.kodeuk=\'%s\' ', $kodeuk);
		$pquery = sprintf('select kodesuk, namasuk from {subunitkerja} where kodeuk=\'%s\' order by kodesuk', $kodeuk);
		
		//drupal_set_message($pquery);
		
		$pres = db_query($pquery);
		$subskpd = array();
		$subskpd[''] = '- Pilih Bidang -';
		while ($data = db_fetch_object($pres)) {
			$subskpd[$data->kodesuk] = $data->namasuk;
		}

		if (isUserKecamatan()) {
			$typesuk='hidden';
			$kodesuk = apbd_getusersuk();
		} else
			$typesuk='select';
		
	} else {
		$pquery = "select kodedinas, kodeuk, namasingkat, namauk from {unitkerja} where aktif=1 order by kodedinas" ;
		$pres = db_query($pquery);
		$dinas = array();        
		
		$dinas['00'] ='00000 - SEMUA SKPD';
		while ($data = db_fetch_object($pres)) {
			$dinas[$data->kodeuk] = $data->kodedinas . ' - ' . $data->namasingkat;
		}
		
		$typeuk='select';
		$typesuk='hidden';
	}
	 
	$form['formdata']['kodeuk']= array(
		'#type'         => $typeuk, 
		'#title'        => 'SKPD',
		'#options'	=> $dinas,
		//'#description'  => 'kodeuktujuan', 
		//'#maxlength'    => 60, 
		'#width'         => 30, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeuk, 
		'#weight' => 2,
	);

	$form['formdata']['kodesuk']= array(
		'#type'         => $typesuk, 
		'#title'        => 'Bidang/Bagian',
		'#options'		=> $subskpd,
		//'#description'  => 'kodesuk', 
		//'#maxlength'    => 60, 
		//'#size'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodesuk, 
		'#weight' => 3,
	); 
	
	$pquery = "select sumberdana from {sumberdanalt} order by nomor" ;
	$pres = db_query($pquery);
	$sumberdanaotp = array();
	$sumberdanaotp[''] = '- SEMUA -';
	while ($data = db_fetch_object($pres)) {
		$sumberdanaotp[$data->sumberdana] = $data->sumberdana;
	}
	$form['formdata']['sumberdana']= array(
		'#type'         => 'select', 
		'#title'        => 'Sumber Dana', 
		'#options'		=> $sumberdanaotp,
		'#width'         => 30, 
		'#default_value'=> $sumberdana, 
		'#weight' => 4,
	);

	$form['formdata']['jenis']= array(
		'#type' => 'radios', 
		'#title' => t('Jenis'), 
		'#default_value' => $jenis,
		'#options' => array(	
			 '' => t('Semua'), 	
			 'gaji' => t('Gaji'), 	
			 'langsung' => t('Langsung'),
			 'ppkd' => t('PPKD'),	
		   ),
		'#weight' => 5,		
	);	
	
	$form['formdata']['ssj'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		'#weight' => 5,
	);		
 	
	
	$form['formdata']['statustw']= array(
		'#type' => 'radios', 
		'#title' => t('Tri Wulan'), 
		'#default_value' => $statustw,
		'#options' => array(	
			 '' => t('Semua'), 	
			 'sudah' => t('Sudah'), 	
			 'belum' => t('Belum'),	
		   ),
		'#weight' => 8,		
	);		
	$form['formdata']['ss2'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		'#weight' => 9,
	);		
	

	$form['formdata']['bintang']= array(
		'#type' => 'radios', 
		'#title' => t('Bintang'), 
		'#default_value' => $bintang,
		'#options' => array(	
			 '' => t('Semua Kegiatan'), 	
			 'keg' => t('Kegiatan'), 	
			 'rek' => t('Rekening'),
			 'det' => t('Detil'),	
			 'pot' => t('Penundaan'),	
		   ),
		'#weight' => 10,		
	);		
	$form['formdata']['ss3'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		'#weight' => 11,
	);		
	
	$form['formdata']['submit'] = array (
		'#type' => 'submit',
		'#value' => 'Tampilkan',
		'#weight' => 12
	);
	
	return $form;
}
function dpa_main_form_submit($form, &$form_state) {
	
	$sumberdana = $form_state['values']['sumberdana'];
	$kodeuk = $form_state['values']['kodeuk'];
	$kodesuk = $form_state['values']['kodesuk'];
	$statustw = $form_state['values']['statustw'];
	$bintang = $form_state['values']['bintang'];
	$jenis = $form_state['values']['jenis'];
	
	$tahun= $form_state['values']['tahun'];

	$_SESSION['sumberdana'] = $sumberdana;
	$_SESSION['statustw'] = $statustw;
	$_SESSION['jenis'] = $jenis;
	$_SESSION['bintang'] = $bintang;
	
	if (isSuperuser()) 
		$_SESSION['kodeuk'] = $kodeuk;
	else
		$_SESSION['kodesuk'] = $kodesuk;
	
	//drupal_set_message('s . ' . $bintang);
	
	$uri = 'apbd/dpa/filter/' . $kodeuk . '/' . $sumberdana . '/' . $kodesuk . '/'. $jenis . '/' . $statustw  . '/' . $bintang;
	drupal_goto($uri);
	
}

function get_bintang($kodekeg) {
	$x = 0;
	$sql = sprintf('select kodekeg from {bintangtunda} where kodekeg=\'%s\' ', $kodekeg);
	$res = db_query($sql);
	if  ($data = db_fetch_object($res)) {
		$x = 1;
	}
	
	if ($x==0) {
		$sql = sprintf('select kodekeg from {bintangrekening} where kodekeg=\'%s\' ', $kodekeg);
		$res = db_query($sql);
		if  ($data = db_fetch_object($res)) {
			$x = 1;
		}
	}

	if ($x==0) {
		$sql = sprintf('select kodekeg from {bintangdetil} where kodekeg=\'%s\' ', $kodekeg);
		$res = db_query($sql);
		if  ($data = db_fetch_object($res)) {
			$x = 1;
		}
	}
	
	if ($x==0)
		return '';
	else 
		return "<img src='/files/bintang.png'>";
}

?>