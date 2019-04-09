<?php
function kegiatanskpd_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    drupal_set_html_head($h);
	drupal_add_css('files/css/kegiatancam.css');
	drupal_add_js('files/js/kegiatancam.js');
	$qlike='';
	$limit = 15;
	
	$kodesuk = '';
	$tahun = variable_get('apbdtahun', 0);
	$ntitle = 'Belanja';
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
				$statusisi = arg(5);
				$kodesuk = arg(6);
				$statustw = arg(7);
				$statusinaktif = arg(8);
				$jenis = arg(9);
				$statusbintang = arg(10);
				$exportpdf = arg(11);

				break;

			case "rekapskpd":	
				$pdfFile = 'Rekap Belanja SKPD.pdf';
				
				$htmlContent = genReportBelanjaSKPD('2');
				
				apbd_ExportPDF2('L', 'F4', '', $htmlContent, $pdfFile);
				break;
				
			case 'excel':
				$kodeuk = arg(3);
				kegiatanskpd_exportexcel($kodeuk);
				break;

			case 'excelplafon':
				$kodeuk = arg(3);
				kegiatanskpd_exportexcelplafon($kodeuk);
				break;
				
			case 'excellpse':
				$kodeuk = arg(3);
				kegiatanskpd_exportexcel_bl($kodeuk);
				break;
			
			case 'csv':
				$kodeuk = arg(3);
				kegiatanskpd_export_csv($kodeuk);
				break;
			
			default:
				drupal_access_denied();
				break;
		}
	} else {
		$tahun = variable_get('apbdtahun', 0);
		$sumberdana = $_SESSION['sumberdana'];
		$statusisi = $_SESSION['statusisi'];
		$statustw = $_SESSION['statustw'];	
		$statusinaktif = $_SESSION['statusinaktif'];
		$jenis = $_SESSION['jenis'];
		$statusbintang = $_SESSION['statusbintang'];
		
		/*
		if (isSuperuser() || isVerifikator()) {
			$kodeuk = $_SESSION['kodeuk'];
			if ($kodeuk == '') 	$kodeuk = '00';
			
			
		} else {
			$kodeuk = apbd_getuseruk();
			if (isUserKecamatan())
				$kodesuk = apbd_getusersuk();
			else
				$kodesuk = $_SESSION['kodesuk'];
		}
		*/
	}

	if (isSuperuser() || isVerifikator()) {
		$kodeuk = $_SESSION['kodeuk'];
		if ($kodeuk == '') 	$kodeuk = '00';
		
		
	} else {
		$kodeuk = apbd_getuseruk();
		if (isUserKecamatan()) {
			$kodesuk = apbd_getusersuk();
			//drupal_set_message($kodesuk);
		} else
			$kodesuk = $_SESSION['kodesuk'];
	}	
	if (isSuperuser() || isVerifikator()) {
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
		$qlike .= sprintf(' and k.inaktif=0 and k.plafon>0 and k.kodeuk=\'%s\' ', $kodeuk);
		if ($kodesuk != '') {
			$qlike .= sprintf(' and (k.kodesuk=\'%s\' ', $kodesuk);
			$qlike .= " or k.kodesuk='' or k.kodeuk is null)";
		}
		
		$adminok = true;
	}

	//keg cari
	//if (strlen($kegcari)>0) {
	//	$qlike .= sprintf(" and lower(k.kegiatan) like lower('%%%s%%') ", db_escape_string($kegcari));
	//}
	 
	//STATUS PENGISIAN
	$wherekoreksi = '';
	if ($statusisi=='sudah') {
		$qlike .= sprintf(' and (k.total=k.plafon) and (k.plafon>0)');
	} elseif ($statusisi=='sebagian') {
		$qlike .= sprintf(' and (k.total>0) and (k.total<k.plafon) and (k.plafon>0) ');
	} elseif ($statusisi=='belum') {
		$qlike .= sprintf(' and (k.total=0 or k.total is null) and (k.plafon>0) ');
	} elseif ($statusisi=='lebih') {
		$qlike .= sprintf(' and (k.total>k.plafon) ');
	} elseif ($statusisi=='adakoreksi') {
		$wherekoreksi = ' and (k.kodekeg in (select kodekeg from {kegiatanverifikasi} where persetujuan<>1) or 
			k.kodekeg in (select kodekeg from {anggperkegverifikasi}}))';
	}

	//STATUS TW
	if ($statustw=='sudah') {
		$qlike .= sprintf(' and k.total>0 and (k.total=(k.tw1+k.tw2+k.tw3+k.tw4)) ');
	} elseif ($statustw=='belum') {
		$qlike .= sprintf(' and k.total>0 and (k.total<>(k.tw1+k.tw2+k.tw3+k.tw4)) ');
	}
	

	//STATUS INAKTIF
	if ($statusinaktif=='0') {
		$qlike .= sprintf(' and k.inaktif=0 ');
	} elseif ($statusinaktif=='1') {
		$qlike .= sprintf(' and (k.inaktif=1 or k.plafon=0) ');
	} elseif ($statusinaktif=='2') {
		$qlike .= sprintf(' and k.dispensasi=1 ');
	} elseif ($statusinaktif=='3') {
		$qlike .= sprintf(' and k.unlockrincianrekening=0 ');
	} elseif ($statusinaktif=='4') {
		$qlike .= sprintf(' and k.unlockrincianrekening=1 ');
	} elseif ($statusinaktif=='5') {
		$qlike .= sprintf(' and k.total<>k.anggaran ');
	}

	//STATUS INAKTIF
	if ($jenis=='gaji') {
		$qlike .= sprintf(' and k.jenis=1 and k.isppkd=0 ');
	} elseif ($jenis=='langsung') {
		$qlike .= sprintf(' and k.jenis=2 ');
	} elseif ($jenis=='ppkd') {
		$qlike .= sprintf(' and k.jenis=1 and k.isppkd=1 ');
	}
	
	//STATUS BINTAG
	if ($statusbintang == 'rek') {
		$qlike .= ' and k.kodekeg in (select b.kodekeg from {bintangrekening} b ) ';		
	} elseif ($statusbintang == 'det') {
		$qlike .= ' and k.kodekeg in (select b.kodekeg from {bintangdetil} b ) ';		
	} elseif ($statusbintang == 'pot') {
		$qlike .= ' and k.kodekeg in (select b.kodekeg from {bintangtunda} b ) ';
	} elseif ($statusbintang == 'keg') {	
		$qlike .= ' and (k.bintang=1) ';
	}		
	//SUMBER DANA
	if ($sumberdana != '') {
		$qlike .= sprintf(' and (k.sumberdana1=\'%s\'  or k.sumberdana2=\'%s\') ', $sumberdana, $sumberdana);
		$ntitle .= ' ' . $sumberdana;
	}
			
	if (isVerifikator()) {
		global $user;
		$username =  $user->name;		
		
		$sql_v_join = 'inner join {userskpd} us on k.kodeuk=us.kodeuk ';
		$qlike .= sprintf(' and us.username=\'%s\' ', $username);
		
		
	} 	

	
	$customwhere = sprintf(' and k.tahun=%s ', $tahun);
	//if (!isSuperuser()) {
	//	$customwhere .= sprintf(' and k.kodeuk=\'%s\' ', $kodeuk);	
	//}	
    $where = ' where true' . $customwhere . $qlike . $wherekoreksi;

	//drupal_set_message($where);
	$pquery = 'select sum(total) jumlahx from {kegiatanskpd} k ' . $sql_v_join . $where;
	$pres = db_query($pquery);	
	if ($data = db_fetch_object($pres))
		$ntitle .= ', Jumlah Anggaran : ' . apbd_fn($data->jumlahx);
	
	//drupal_set_title($ntitle);	

	
	$sql = "select k.kodekeg,k.nomorkeg,k.tahun,k.kodepro,k.kodeuk,k.kegiatan,k.jenis, k.lokasi,k.programsasaran, k.programtarget, k.jenis, 
			k.keluaransasaran, k.totalsebelum, keluarantarget, k.hasilsasaran, k.hasiltarget,k.total,k.plafon,u.namasingkat, k.isppkd,  k.adminok, 
			k.sumberdana1 sumberdana, k.inaktif,k.dispensasi, k.bintang, 
			((k.tw1+k.tw2+k.tw3+k.tw4)=k.plafon) twok, tw1, tw2, tw3, tw4, k.anggaran,k.lastupdate from {kegiatanskpd} k inner join {unitkerja} u on ( k.kodeuk=u.kodeuk) left join {program} p on (k.kodepro = p.kodepro) " . $sql_v_join . $where;
	//$fsql = sprintf($sql, addslashes($nama));
	//drupal_set_message($fsql);
    $countsql = "select count(*) as cnt from {kegiatanskpd} k " . $sql_v_join . $where;
    //$fcountsql = sprintf($countsql, addslashes($nama));

	if (isset($exportpdf))   {
		if ($exportpdf=='pdf') {
			$pdfFile = 'Daftar_Kegiatan_Dicari.pdf';
			
			$htmlHeader = GenDataHeader($kodeuk);
			$htmlContent = GenDataPrint($kodeuk, $sql);
			
			apbd_ExportPDF2('L', 'F4', $htmlHeader, $htmlContent, $pdfFile);
			
			return $htmlContent;
			
			break;

		} else if ($exportpdf=='rekapbl') {
			$pdfFile = 'Rekap Belanja SKPD.pdf';
			
			$htmlContent = genReportBelanjaSKPD('2');
			
			apbd_ExportPDF2('L', 'F4', '', $htmlContent, $pdfFile);
			break;

		} else if ($exportpdf=='rekapbtl') {
			$pdfFile = 'Rekap Belanja SKPD.pdf';
			
			$htmlContent = genReportBelanjaSKPD('1');
			
			apbd_ExportPDF2('L', 'F4', '', $htmlContent, $pdfFile);
			break;
			
		} else {
			//kegiatanrevisiperubahan_exportexcel($sql);
		}	
		
	} else {
		
		//$output .= drupal_get_form('kegiatanskpd_transfer_form');
		$output .= drupal_get_form('kegiatanskpd_main_form');
		$output .= GetDataView($sql, $countsql, $kodeuk , $sumberdana , $statusisi , $kodesuk , $statustw, $statusinaktif, $jenis, $statusbintang);
		return $output;
	}	
}

function GetDataView($fsql, $fcountsql, $kodeuk , $sumberdana , $statusisi , $kodesuk , $statustw, $statusinaktif, $jenis, $statusbintang) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    drupal_set_html_head($h);
	drupal_add_css('files/css/kegiatancam.css');
	drupal_add_js('files/js/kegiatancam.js');
	
	$arr_user = array(); 
	$sql = 'select distinct o.username,o.nama from {apbdop} o inner join {userskpd} s on o.username=s.username';
	//drupal_set_message($sql);
	$res_usr = db_query($sql);
	if ($res_usr) {
		while ($data_user = db_fetch_object($res_usr)) {
			//drupal_set_message($data_user->nama);
			$arr_user[$data_user->username] = $data_user->nama;
		}
	}				
	
	
	$limit = 15;
	$seribu = 1000;

	if (isSuperuser() || isVerifikator() ) {
		$header = array (
			array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
			array('data' => '', 'width' => '5px', 'valign'=>'top'),
			array('data' => '', 'width' => '5px', 'valign'=>'top'),
			array('data' => 'SKPD', 'field'=> 'namasingkat', 'valign'=>'top'),
			array('data' => 'Kegiatan', 'field'=> 'kegiatan', 'valign'=>'top'),
			array('data' => 'Sumberdana', 'field'=> 'sumberdana', 'valign'=>'top'),
			array('data' => 'Plafon', 'field'=> 'plafon','width' => '90px', 'valign'=>'top'),
			array('data' => 'Anggaran', 'field'=> 'total','width' => '90px', 'valign'=>'top'),
			array('data' => '', 'width' => '25px',  'valign'=>'top'),
			array('data' => 'Catatan', 'valign'=>'top'),
			array('data' => 'UT', 'field'=> 'lastupdate','valign'=>'top'),
			array('data' => '', 'width' => '40px', 'valign'=>'top'),
		);
	}
	else if(isUserview()){
		$url='apbd/belanjadpa';
		drupal_goto($url);
		$header = array (
			array('data' => 'Nomor', 'width' => '10px', 'valign'=>'top'),
			array('data' => '', 'width' => '5px', 'valign'=>'top'),
			array('data' => '', 'width' => '5px', 'valign'=>'top'),
			array('data' => 'Kegiatan', 'field'=> 'kegiatan', 'valign'=>'top'),
			array('data' => 'Kegiatan', 'field'=> 'kegiatan', 'valign'=>'top'),
			array('data' => 'Plafon', 'field'=> 'plafon','width' => '90px', 'valign'=>'top'),
			array('data' => 'Anggaran', 'field'=> 'total', 'width' => '90px','valign'=>'top'),
			array('data' => '', 'width' => '25px',  'valign'=>'top'),
			array('data' => '', 'width' => '25px',  'valign'=>'top'),
			array('data' => 'Catatan', 'valign'=>'top'),
			array('data' => 'UT', 'field'=> 'lastupdate','valign'=>'top'),
			array('data' => '', 'width' => '40px', 'valign'=>'top'),
			);
		
		
	
	}
	else {
		$header = array (
			array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
			array('data' => '', 'width' => '5px', 'valign'=>'top'),
			array('data' => '', 'width' => '5px', 'valign'=>'top'),
			array('data' => 'Kegiatan', 'field'=> 'kegiatan', 'valign'=>'top'),
			array('data' => 'Kegiatan', 'field'=> 'kegiatan', 'valign'=>'top'),
			array('data' => 'Plafon', 'field'=> 'plafon','width' => '90px', 'valign'=>'top'),
			array('data' => 'Anggaran', 'field'=> 'total', 'width' => '90px','valign'=>'top'),
			array('data' => '', 'width' => '25px',  'valign'=>'top'),
			array('data' => 'Catatan', 'valign'=>'top'),
			array('data' => 'UT', 'field'=> 'lastupdate','valign'=>'top'),
			array('data' => '', 'width' => '40px', 'valign'=>'top'),
		);
	}
	$tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by kodekeg';
    }	
	
	//drupal_set_message($fsql);
    $result = pager_query($fsql . $tablesort, $limit, 0, $fcountsql);
	
	$allowedit = (batastgl() || (isSuperuser()));		
	
	
	//CEK TAHUN
	//$allowedit = ($allowedit and ($tahun == variable_get('apbdtahun', 0)));
    
    $no=0;
    $page = $_GET['page'];
    if (isset($page)) {
        $no = $page * $limit;
    } else {
        $no = 0;
    }

	if (isVerifikator()) {
		global $user;
		$username = $user->name;		
	}
	
    if ($result) {
        while ($data = db_fetch_object($result)) {
			$editlink = '';
			
			$catatan_rek = '';
			
			//$strperpanjangan = '';
			//if ($data->dispensasi) $strperpanjangan = ' ***/Perpanjangan RKA\***';
			
			if (user_access('kegiatanskpd edit')) {
				//$kegname = l($data->kegiatan, 'apbd/kegiatanskpd/edit/' . $data->kodekeg , array('attributes' => array('target' => '_blank'), 'html' =>TRUE));
				if (isSuperuser())
					$kegname = l($data->kegiatan, 'apbd/kegiatanskpd/edit/' . $data->kodekeg , array('html' =>TRUE));
				else
					$kegname = l($data->kegiatan, 'apbd/kegiatanskpd/editskpd/' . $data->kodekeg , array('html' =>TRUE));
					
			} else {
				$kegname = $data->kegiatan;
			}
			
			$ket_tuk = '';
			if ($data->jenis=='2') {
				if (($data->programsasaran=='') or ($data->programtarget=='')) {
					$ket_tuk = 'Tolok ukur program belum diisi; ';
				} 
				if (($data->keluaransasaran=='') or ($data->keluarantarget=='')) {
					$ket_tuk .= 'Tolok ukur keluaran belum diisi; ';
				} 
				if (($data->hasilsasaran=='') or ($data->hasiltarget=='')) {
					$ket_tuk .= 'Tolok ukur hasil belum diisi; ';
				}
				if ($data->lokasi=='') {
					$ket_tuk .= 'Lokasi belum diisi; ';
				}
			}
			
			if ($allowedit) {
				if (isVerifikator()) {
					if ($data->total==0) {
						$editlink = 'Rek';
					} else {
						//Catatan Rekening
						$rek = 'Rek';
						$sql = sprintf('select jawaban from {anggperkegverifikasi} where kodekeg=\'%s\'',$data->kodekeg);
						//drupal_set_message($sql);
						$res_cek = db_query($sql);
						if ($res_cek) {
							while ($data_cek = db_fetch_object($res_cek)) {
								$rek = '<font color="Red">Rek</font>';
								$catatan_rek .= $data_cek->jawaban . '; ';
							}							
						}		
					
						$editlink =l($rek, 'apbd/kegiatanskpd/rekening/' . $data->kodekeg, array('html'=>TRUE));
					}

				} else {
					if ($ket_tuk=='') {
					
			
						//Catatan Rekening
						$rek = 'Rek';
						$sql = sprintf('select jawaban from {anggperkegverifikasi} where kodekeg=\'%s\'',$data->kodekeg);
						//drupal_set_message($sql);
						
						
						$res_cek = db_query($sql);	
						if ($res_cek) {
							while ($data_cek = db_fetch_object($res_cek)) {
								$rek = '<font color="Red">Rek</font>';
								$catatan_rek .= $data_cek->jawaban . '; ';
							}
						}		
					
						if ($data->twok) {
							$editlink =l($rek, 'apbd/kegiatanskpd/rekening/' . $data->kodekeg, array('html'=>TRUE));
							$editlink .= "&nbsp;" .  l('TW', 'apbd/kegiatanskpd/triwulan/' . $data->kodekeg, array('html'=>TRUE));	
							
						} else {
							
							$rek = '<font color="Red">Rek</font>';
							if (isSuperuser())
								$editlink =l($rek, 'apbd/kegiatanskpd/rekening/' . $data->kodekeg, array('html'=>TRUE));
							else
								$editlink = $rek;
							
							$editlink .= "&nbsp;" .  l('<font color="Red">TW</font>', 'apbd/kegiatanskpd/triwulan/' . $data->kodekeg, array('html'=>TRUE));						
							$ket_tuk .= 'Triwulan agar diisi dengan benar; ';
							
						}
							
							
					
					} else {
						$editlink = '<font color="Red">Rek</font>';
						//$catatan_rek = '<font color="Red">Rekening agar diperkisa</font>';
						$editlink .= "&nbsp;" . 'TW';
						
					}
				}
				
			}	else {


				//Catatan Rekening
				$rek = 'Rek';
				$sql = sprintf('select jawaban from {anggperkegverifikasi} where kodekeg=\'%s\'',$data->kodekeg);
				//drupal_set_message($sql);
				$res_cek = db_query($sql);
				if ($res_cek) {
					while ($data_cek = db_fetch_object($res_cek)) {
						$rek = '<font color="Red">Rek</font>';
						$catatan_rek .= $data_cek->jawaban . '; ';
					}
				}				
				
				if ($data->twok) {
					$editlink =l($rek, 'apbd/kegiatanskpd/rekening/' . $data->kodekeg, array('html'=>TRUE));
					$editlink .= "&nbsp;" .  l('TW', 'apbd/kegiatanskpd/triwulan/' . $data->kodekeg, array('html'=>TRUE));

				} else {
					$editlink = $rek;
					
					$editlink .= "&nbsp;" .  l('<font color="Red">TW</font>', 'apbd/kegiatanskpd/triwulan/' . $data->kodekeg, array('html'=>TRUE));						
					$ket_tuk .= 'Triwulan agar diisi dengan benar; ';	
				}
			}
			
			if (isSuperuser()) {
				$editlink .= "&nbsp;" . l('Edit', 'apbd/kegiatanskpd/editadmin/' . $data->kodekeg, array('html'=>TRUE));
				$editlink .= "&nbsp;" . l('Hapus', 'apbd/kegiatanskpd/delete/' . $data->kodekeg, array('html'=>TRUE));
				
			} else {
				//Cetak
				$editlink .= "&nbsp;" . l('Cetak', 'apbd/kegiatanskpd/print/' . $data->kodekeg , array('html'=>TRUE)) ;
				
			}
			if (isVerifikator()) {
				$editlink .= "&nbsp;" . l('Verifikasi', 'apbdverifikasi/' . $data->kodekeg, array('html'=>TRUE)) ;
				
			}
            
			$no++;

			//status kegiatan (kol 1)
			if ($data->inaktif) 
				//$inaktif = 'x';
				$statuskegiatan = "<img src='/files/inaktif.png'>";
			
			else {
				if ($data->dispensasi) 
					$statuskegiatan = "<img src='/files/revisi16.jpg'>";
				else
					$statuskegiatan ='';
			}
			
			if ($data->bintang)  $statuskegiatan = "<img src='/files/bintang.png'>";
			
			//status pengisian (kol 2) 
			if ($data->total > $data->plafon)
				$statuspengisian = "<img src='/files/limitt.gif'>";
			else if (($data->total < $data->plafon) or ($data->total==0))
				$statuspengisian = "<img src='/files/icon-unfinished.png'>";
			else
				$statuspengisian = "<img src='/files/icon-finished.png'>";
			
			//VERIFIKASI
			$num_ver = 0;
			$str_ver = '';
			$catatan = '';
			$sql_r = sprintf("select username,persetujuan,jawaban from {kegiatanverifikasi} where kodekeg='%s'", db_escape_string($data->kodekeg));
			$res_r = db_query($sql_r);
			while ($data_r = db_fetch_object($res_r)) {
				$num_ver ++;
				if ($data_r->persetujuan==1)
					$str_ver .= "<img src='/files/verify/fer_ok.png' title='" . $arr_user[$data_r->username] . "'>";
				else if ($data_r->persetujuan==2)
					$str_ver .= "<img src='/files/verify/fer_warning.png' title='" . $arr_user[$data_r->username] . "'>";
				else
					$str_ver .= "<img src='/files/verify/fer_no.png' title='" . $arr_user[$data_r->username] . "'>";
				
				if ($username==$data_r->username) $kegname .= '<font color="red">**</font>';	

				if ($data_r->persetujuan==0) {
					$color = 'red';
				} else if ($data_r->persetujuan==2) {
					$color = 'orange';
				} else {
					$color = 'green';
				}
			
				if ($num_ver==1)
					$catatan .= '(' . $arr_user[$data_r->username] . ')<font color="' . $color . '"> ' . $data_r->jawaban . ';</font>';
				else 
					$catatan .= '<p>(' . $arr_user[$data_r->username] . ')<font color="' . $color . '"> ' . $data_r->jawaban . ';</font></p>';
			} 
			for ($x = $num_ver+1; $x <= 3; $x++) {
				$str_ver .= "<img src='/files/verify/fer_belum.png'>";
			}			
			
			if ($catatan_rek!='') $catatan_rek = '<p><font color="Red" size="1px"><strong>Catatan Rekening : </strong>' . $catatan_rek . '</font></p>';
			if ($ket_tuk!='') $ket_tuk = '<p><font color="Red" size="1px">' . $ket_tuk . '</font></p>';
			
			if ($catatan!='') $catatan = '<font size="1px">' . $catatan . '</font>';
			
			//if (!isAdministrator()) {
			//	$kegname = $data->kegiatan;
			//}
			
			//$str_agg = '';
			$str_agg = '<p><font color="Orange">' . apbd_fn($data->totalsebelum) . '</font></p>';
			
			if (isSuperuser() || isVerifikator()) { 
				
				$rows[] = array (
					array('data' => $no, 'align' => 'right', 'valign'=>'top'),
					array('data' => $statuskegiatan, 'align' => 'center', 'color' => 'red', 'valign'=>'top'),
					array('data' => $statuspengisian, 'align' => 'center', 'color' => 'red', 'valign'=>'top'),
					array('data' => $data->namasingkat, 'align' => 'left', 'valign'=>'top'),
					array('data' => $kegname . $ket_tuk, 'align' => 'left', 'valign'=>'top'),
					//array('data' => $data->programtarget, 'align' => 'left', 'valign'=>'top'),
					//array('data' => str_replace('||',', ', $data->lokasi), 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->sumberdana, 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fn($data->plafon) . $str_agg, 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->total), 'align' => 'right', 'valign'=>'top'),
					array('data' => $str_ver, 'align' => 'left', 'valign'=>'top'),
					array('data' => $catatan . $catatan_rek, 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fdt($data->lastupdate), 'align' => 'left', 'valign'=>'top'),
					array('data' => $editlink, 'align' => 'left', 'valign'=>'top'),
				);
			} else {
				$rows[] = array (
					array('data' => $no, 'align' => 'right', 'valign'=>'top'),
					array('data' => $statuskegiatan, 'align' => 'center', 'color' => 'red', 'valign'=>'top'),
					array('data' => $statuspengisian, 'align' => 'center', 'color' => 'red', 'valign'=>'top'),
					
					array('data' => $kegname . $ket_tuk, 'align' => 'left', 'valign'=>'top'),
					//array('data' => str_replace('||',', ', $data->lokasi), 'align' => 'left', 'valign'=>'top'),
					//array('data' => $data->programtarget, 'align' => 'left', 'valign'=>'top'),
					//array('data' => str_replace('||',', ', $data->lokasi), 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->sumberdana, 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fn($data->plafon)  . $str_agg, 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->total), 'align' => 'right', 'valign'=>'top'),
					array('data' => $str_ver, 'align' => 'left', 'valign'=>'top'),
					array('data' => $catatan . $catatan_rek, 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fdt($data->lastupdate), 'align' => 'left', 'valign'=>'top'),
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
	if ($allowedit)
		//
		if (isSuperuser() || isVerifikator()) {
			$btn .= l('Baru', 'apbd/kegiatanskpd/editadmin/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) . "&nbsp;";
		}

	$status = 0;
	$record = 0;
	//$btn .= l('Cetak', 'apbd/laporan/rka/rekapaggbelanja/' . $kodeuk , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));
	//drupal_set_message($statusbintang);
	$btn .= l('Cetak', 'apbd/kegiatanskpd/filter/' . $kodeuk . '/' . $sumberdana . '/' . $statusisi . '/' . $kodesuk . '/'. $statustw . '/' . $statusinaktif . '/' . $jenis . '/' . $statusbintang . '/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));
	
    $btn .= "&nbsp;" . l("Cari", 'apbd/kegiatanskpd/find/' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;
	
	
	if (isSuperuser() || isVerifikator()) {
		$btn .= "&nbsp;" . l('Rekap BL', 'analisabl' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));

		$btn .= "&nbsp;" . l('Rekap BTL', 'analisabtl' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));

		$btn .= "&nbsp;" . l('Analisa', 'analisa' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));
		
		$btn .= "&nbsp;" . l('Excel Plafon', 'apbd/kegiatanskpd/excelplafon/' . $kodeuk , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));	
		$btn .= "&nbsp;" . l('Excel Rinci', 'apbd/kegiatanskpd/excel/' . $kodeuk , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));	
		
	}
	if ($kodeuk!='00') {
		$btn .= "&nbsp;" . l('Excel LPSE', 'apbd/kegiatanskpd/excellpse/' . $kodeuk , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));	
		$btn .= "&nbsp;" . l('CSV LPSE', 'apbd/kegiatanskpd/csv/' . $kodeuk , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));	
	}	
	
    $output = $btn . theme_box('', theme_table($header, $rows)) . $btn;

	
    $output .= theme ('pager', NULL, $limit, 0);
    return $output;
}


function kegiatanskpd_main_form() {
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
		$statusisi = arg(5);
		$kodesuk = arg(6);
		$statustw = arg(7);
		$statusinaktif = arg(8);
		$jenis = arg(9);
		$statusbintang = arg(10);
		
	} else {
		$sumberdana = $_SESSION['sumberdana'];
		$statusisi = $_SESSION['statusisi'];
		$statustw = $_SESSION['statustw'];	
		$statusinaktif = $_SESSION['statusinaktif'];	
		$jenis = $_SESSION['jenis'];	
		$statusbintang = $_SESSION['statusbintang'];	

		if (isSuperuser() || isVerifikator()) 
			$kodeuk = $_SESSION['kodeuk'];
		else
			$kodesuk = $_SESSION['kodesuk'];
	}
	//drupal_set_message($filter);

	//if (isset($kodeuk)) {
	//    $form['formdata']['#collapsed'] = TRUE;
	//    //if (isUserKecamatan())
	//    //    if ($kodeuk != apbd_getuseruk())
	//    //        $form['formdata']['#collapsed'] = FALSE;
	//}
		   
	if (!isSuperuser() && !isVerifikator()) {
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
		
	} else if(isSuperuser()){
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
	else if (isVerifikator()) {

		if (isVerifikator()) {
			global $user;
			$username =  $user->name;		
			
			$where .= sprintf(' and us.username=\'%s\' ', $username);
		}
	
		$pquery = "select kodedinas, kodeuk, namasingkat, namauk from {unitkerja} where aktif=1 and kodeuk in (select k.kodeuk from {kegiatanskpd} k inner join {userskpd} us on k.kodeuk=us.kodeuk " . $where . ") order by kodedinas" ;
		$pres = db_query($pquery);
		$dinas = array();        
		//drupal_set_message($pquery);
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
 	
	$form['formdata']['statusisi']= array(
		'#type' => 'radios', 
		'#title' => t('Pengisian'), 
		'#default_value' => $statusisi,
		'#options' => array(	
			 '' => t('Semua'), 	
			 'sudah' => t('Selesai'), 	
			 'sebagian' => t('Sebagian'),
			 'belum' => t('Belum'),	
			 'lebih' => t('Lebih Plafon'),	
			 //'adakoreksi' => '<font color="red">Ada Koreksi</font>',	
		   ),
		'#weight' => 6,		
	);	

	$form['formdata']['ss1'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		'#weight' => 7,
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
	
	if (isSuperuser() || isVerifikator()) {
		$statusinaktiftype = 'radios';

		$form['formdata']['statusinaktif']= array(
			'#type' => $statusinaktiftype, 
			'#title' => t('Status'), 
			'#default_value' => $statusinaktif,
			'#options' => array(	
				 '' => t('Semua'), 	
				 '0' => t('Aktif'),	
				 '1' => t('Inaktif'), 	
				 '2' => t('Perpanjang'),
				 '3' => t('Detil Terkunci'),
				 '4' => t('Detil Terbuka'),
			   ),
			'#weight' => 10,		
		);		

		$form['formdata']['ss11'] = array (
			'#type' => 'item',
			'#value' => "<div style='clear:both;'></div>",
			'#weight' => 11,
		);		
		
		$form['formdata']['statusbintang']= array(
			'#type' => $statusinaktiftype, 
			'#title' => t('Bintang'), 
			'#default_value' => $statusbintang,
			'#options' => array(	
				 '' => t('Semua'), 	
				 'keg' => t('Kegiatan'), 	
				 'rek' => t('Rekening'),
				 'det' => t('Detil'),	
				 'pot' => t('Penundaan'),
			   ),
			'#weight' => 12,		
		);				
	} else {
		$statusinaktiftype = 'hidden';
		$statusinaktif = '0';

		$form['formdata']['statusinaktif']= array(
			'#type' => $statusinaktiftype, 
			'#title' => t('Status'), 
			'#default_value' => $statusinaktif,
			'#options' => array(	
				 '' => t('Semua'), 	
				 '0' => t('Aktif'),	
				 '1' => t('Inaktif'), 	
				 '2' => t('Perpanjang'),
			   ),
			'#weight' => 10,		
		);	

		$form['formdata']['statusbintang']= array(
			'#type' => 'hidden', 
			'#default_value' => '',
			'#weight' => 11,		
		);		
		
	}
	
	
	$form['formdata']['ss'] = array (
		'#type' => 'item',
		'#value' => "<div style='clear:both;'></div>",
		'#weight' => 13,
	);		
	$form['formdata']['submit'] = array (
		'#type' => 'submit',
		'#value' => 'Tampilkan',
		'#weight' => 14
	);
	
	return $form;
}
function kegiatanskpd_main_form_submit($form, &$form_state) {
	$sumberdana = $form_state['values']['sumberdana'];
	$kodeuk = $form_state['values']['kodeuk'];
	$kodesuk = $form_state['values']['kodesuk'];
	$statusisi = $form_state['values']['statusisi'];
	$statustw = $form_state['values']['statustw'];
	$statusinaktif = $form_state['values']['statusinaktif'];
	$jenis = $form_state['values']['jenis'];
	$statusbintang = $form_state['values']['statusbintang'];
	
	$tahun= $form_state['values']['tahun'];

	$_SESSION['sumberdana'] = $sumberdana;
	$_SESSION['statusisi'] = $statusisi;
	$_SESSION['statustw'] = $statustw;
	$_SESSION['statusinaktif'] = $statusinaktif;
	$_SESSION['jenis'] = $jenis;
	$_SESSION['statusbintang'] = $statusbintang;
	
	if (isSuperuser() || isVerifikator()) 
		$_SESSION['kodeuk'] = $kodeuk;
	else
		$_SESSION['kodesuk'] = $kodesuk;
	
	$uri = 'apbd/kegiatanskpd/filter/' . $kodeuk . '/' . $sumberdana . '/' . $statusisi . '/' . $kodesuk . '/'. $statustw . '/' . $statusinaktif . '/' . $jenis . '/' . $statusbintang;
	drupal_goto($uri);
	
}

function kegiatanskpd_transfer_form() {
	$form['formtransfer'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Transfer Data Dari MUSRENBANGCAM',
		'#collapsible' => TRUE,
		'#collapsed' => TRUE,        
	);
	$pquery = "select kodeuk, namauk, namasingkat from {unitkerja} where aktif=1 and iskecamatan=1 order by namasingkat" ;
	$pres = db_query($pquery);
	$dinas = array();
	$kodeuk = apbd_getuseruk();
	$typekodeuk = 'select';
	if (!isSuperuser())
		$typekodeuk='hidden';
	//$dinas[''] = '--- pilih dinas teknis---';
	while ($data = db_fetch_object($pres)) {
		$dinas[$data->kodeuk] = $data->namasingkat;
	}
	
	$form['formtransfer']['kodeuk']= array(
		'#type'         => 'select', 
		//'#title'        => 'Kecamatan',
		'#options'	=> $dinas,
		//'#description'  => 'kodeuktujuan', 
		//'#maxlength'    => 60, 
		'#width'         => 20, 
		//'#required'     => !$disabled, 
		//'#disabled'     => $disabled, 
		'#default_value'=> $kodeuk,
		'#attributes'	=> array('style' => 'margin-left: 20px;'),
	); 
	
	

	$musrenbang = l("<div class='boxp' >MUSRENBANGCAM</div>", 'apbd/kegiatancam', array('html'=> true));
	$renja= l("<div class='boxp'>RENJA SKPD</div>", 'apbd/kegiatanskpd', array('html'=>true));
	$proses = "<div class='boxproses' id='boxproses'><a href='#transfercamskpd' class='btn_blue' style='color: white;'>---Transfer---></a></div>";
	$document = "<div style='height: 50px; text-align:center;'>$musrenbang $proses $renja<div style='clear:both;'></div></div>";
	$form['formtransfer']['keterangan'] = array (
		'#type' => 'markup',
		'#value' => $document,
		'#weight' => 1,
	);
	return $form;
}

function kegiatanskpd_exportexcel_lama($kodeuk) {
error_reporting(E_ALL);

set_time_limit(0);
ini_set('memory_limit', '640M');

ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
//date_default_timezone_set('Europe/London');

if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once 'files/PHPExcel/Classes/PHPExcel.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
// Set document properties
$objPHPExcel->getProperties()->setCreator("SIPKD Anggaran Online")
							 ->setLastModifiedBy("SIPKD Anggaran Online")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Excel document generated from SIPKD Anggaran Online.")
							 ->setKeywords("office 2007 SIPKD Anggaran Online openxml php")
							 ->setCategory("SIPKD Anggaran Analisa");
// Add Header
$row = 1;
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $row ,'Fungsi')
			->setCellValue('B' . $row ,'Urusan')
            ->setCellValue('C' . $row ,'SKPD')
            ->setCellValue('D' . $row ,'Program')
			->setCellValue('E' . $row ,'Kegiatan')
			->setCellValue('F' . $row ,'Akun Utama')
			->setCellValue('G' . $row ,'Akun Kelompok')
			->setCellValue('H' . $row ,'Akun Jenis')
			->setCellValue('I' . $row ,'Akun Obyek')
			->setCellValue('J' . $row ,'Akun Rincian')
			->setCellValue('K' . $row ,'Jumlah');

//Open data							 
//$customwhere = sprintf(' and k.tahun=%s ', variable_get('apbdtahun', 0));
if ($kodeuk!='00') {
	$customwhere .= sprintf(' and kegiatanskpd.kodeuk=\'%s\' ', $kodeuk);	
}	
$where = ' where inaktif=0 ' . $customwhere;
	
$sql = "SELECT programurusanfungsi.namafungsi, programurusanfungsi.namaurusan, CONCAT_WS(' - ', unitkerja.kodedinas, unitkerja.namasingkat) skpd, programurusanfungsi.namaprogram, kegiatanskpd.kegiatan, rekeninglengkap.akunutama, rekeninglengkap.akunkelompok, rekeninglengkap.akunjenis, rekeninglengkap.akunobyek, rekeninglengkap.akunrincian,
anggperkeg.jumlah FROM unitkerja inner join kegiatanskpd ON unitkerja.kodeuk=kegiatanskpd.kodeuk INNER JOIN programurusanfungsi ON kegiatanskpd.kodepro=programurusanfungsi.kodepro INNER JOIN anggperkeg ON anggperkeg.kodekeg=kegiatanskpd.kodekeg INNER JOIN rekeninglengkap ON rekeninglengkap.kodero=anggperkeg.kodero " . $where;
$result = db_query($sql);
while ($data = db_fetch_object($result)) {
	$row++;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $row, $data->namafungsi)
				->setCellValue('B' . $row, $data->namaurusan)
				->setCellValue('C' . $row, $data->skpd)
				->setCellValue('D' . $row, $data->namaprogram)
				->setCellValue('E' . $row, $data->kegiatan)
				->setCellValue('F' . $row, $data->akunutama)
				->setCellValue('G' . $row, $data->akunkelompok)
				->setCellValue('H' . $row, $data->akunjenis)
				->setCellValue('I' . $row, $data->akunobyek)
				->setCellValue('J' . $row, $data->akunrincian)
				->setCellValue('K' . $row, $data->jumlah);
}
						

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Analisis Belanja SKPD');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
$fname = 'analisis_belanja_skpd_' . $kodeuk . '.xlsx';
header('Content-Disposition: attachment;filename=' . $fname);
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
}

function kegiatanskpd_exportexcel_rinci($kodeuk) {
error_reporting(E_ALL);

set_time_limit(0);
ini_set('memory_limit', '640M');

ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
//date_default_timezone_set('Europe/London');

if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once 'files/PHPExcel/Classes/PHPExcel.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
// Set document properties
$objPHPExcel->getProperties()->setCreator("SIPKD Anggaran Online")
							 ->setLastModifiedBy("SIPKD Anggaran Online")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Excel document generated from SIPKD Anggaran Online.")
							 ->setKeywords("office 2007 SIPKD Anggaran Online openxml php")
							 ->setCategory("SIPKD Anggaran Analisa");
// Add Header
$row = 1;
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $row ,'Fungsi')
			->setCellValue('B' . $row ,'Urusan')
            ->setCellValue('C' . $row ,'SKPD')
            ->setCellValue('D' . $row ,'Program')
			->setCellValue('E' . $row ,'Kegiatan')
			->setCellValue('F' . $row ,'Akun Utama')
			->setCellValue('G' . $row ,'Akun Kelompok')
			->setCellValue('H' . $row ,'Akun Jenis')
			->setCellValue('I' . $row ,'Akun Obyek')
			->setCellValue('J' . $row ,'Akun Rincian')
			->setCellValue('K' . $row ,'Jumlah');

//Open data							 
//$customwhere = sprintf(' and k.tahun=%s ', variable_get('apbdtahun', 0));
if ($kodeuk!='00') {
	$customwhere .= sprintf(' and kegiatanskpd.kodeuk=\'%s\' ', $kodeuk);	
}	
$where = ' where inaktif=0 ' . $customwhere;
	
$sql = "SELECT programurusanfungsi.namafungsi, programurusanfungsi.namaurusan, CONCAT_WS(' - ', unitkerja.kodedinas, unitkerja.namasingkat) skpd, programurusanfungsi.namaprogram, kegiatanskpd.kegiatan, kegiatanskpd.kodekeg FROM unitkerja inner join kegiatanskpd ON unitkerja.kodeuk=kegiatanskpd.kodeuk INNER JOIN programurusanfungsi ON kegiatanskpd.kodepro=programurusanfungsi.kodepro " . $where;
$result = db_query($sql);
while ($data = db_fetch_object($result)) {
	
	$sql = "SELECT rekeninglengkap.akunutama, rekeninglengkap.akunkelompok, rekeninglengkap.akunjenis, rekeninglengkap.akunobyek, rekeninglengkap.akunrincian, anggperkeg.jumlah FROM anggperkeg INNER JOIN rekeninglengkap ON rekeninglengkap.kodero=anggperkeg.kodero WHERE anggperkeg.kodekeg='" . $data->kodekeg . "'";
	$res_rek = db_query($sql);
	while ($data_rek = db_fetch_object($res_rek)) {
		$row++;
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A' . $row, $data->namafungsi)
					->setCellValue('B' . $row, $data->namaurusan)
					->setCellValue('C' . $row, $data->skpd)
					->setCellValue('D' . $row, $data->namaprogram)
					->setCellValue('E' . $row, $data->kegiatan)
					->setCellValue('F' . $row, $data_rek->akunutama)
					->setCellValue('G' . $row, $data_rek->akunkelompok)
					->setCellValue('H' . $row, $data_rek->akunjenis)
					->setCellValue('I' . $row, $data_rek->akunobyek)
					->setCellValue('J' . $row, $data_rek->akunrincian)
					->setCellValue('K' . $row, $data_rek->jumlah);
	}
}
						

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Analisis Belanja SKPD');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
$fname = 'analisis_belanja_skpd_' . $kodeuk . '.xlsx';
header('Content-Disposition: attachment;filename=' . $fname);
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
}

function kegiatanskpd_exportexcel($kodeuk) {
error_reporting(E_ALL);

set_time_limit(0);
ini_set('memory_limit', '640M');

ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
//date_default_timezone_set('Europe/London');

if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once 'files/PHPExcel/Classes/PHPExcel.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
// Set document properties
$objPHPExcel->getProperties()->setCreator("SIPKD Anggaran Online")
							 ->setLastModifiedBy("SIPKD Anggaran Online")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Excel document generated from SIPKD Anggaran Online.")
							 ->setKeywords("office 2007 SIPKD Anggaran Online openxml php")
							 ->setCategory("SIPKD Anggaran Analisa");
// Add Header
$row = 1;
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $row ,'Fungsi')
			->setCellValue('B' . $row ,'Urusan')
            ->setCellValue('C' . $row ,'SKPD')
            ->setCellValue('D' . $row ,'Program')
			->setCellValue('E' . $row ,'Kegiatan')
			->setCellValue('F' . $row ,'Akun Utama')
			->setCellValue('G' . $row ,'Akun Kelompok')
			->setCellValue('H' . $row ,'Akun Jenis')
			->setCellValue('I' . $row ,'Akun Obyek')
			->setCellValue('J' . $row ,'Jumlah');

//Open data							 
//$customwhere = sprintf(' and k.tahun=%s ', variable_get('apbdtahun', 0));
if ($kodeuk!='00') {
	$customwhere .= sprintf(' and kegiatanskpd.kodeuk=\'%s\' ', $kodeuk);	
}	
$where = ' where inaktif=0 ' . $customwhere;
	
$sql = "SELECT programurusanfungsi.namafungsi, programurusanfungsi.namaurusan, CONCAT_WS(' - ', unitkerja.kodedinas, unitkerja.namasingkat) skpd, programurusanfungsi.namaprogram, kegiatanskpd.kegiatan, kegiatanskpd.kodekeg FROM unitkerja inner join kegiatanskpd ON unitkerja.kodeuk=kegiatanskpd.kodeuk INNER JOIN programurusanfungsi ON kegiatanskpd.kodepro=programurusanfungsi.kodepro " . $where;
$result = db_query($sql);
while ($data = db_fetch_object($result)) {
	
	$sql = "SELECT rekeninglengkap.akunutama, rekeninglengkap.akunkelompok, rekeninglengkap.akunjenis, rekeninglengkap.akunobyek, sum(anggperkeg.jumlah) as total FROM anggperkeg INNER JOIN rekeninglengkap ON rekeninglengkap.kodero=anggperkeg.kodero WHERE anggperkeg.kodekeg='" . $data->kodekeg . "' GROUP BY rekeninglengkap.akunutama, rekeninglengkap.akunkelompok, rekeninglengkap.akunjenis, rekeninglengkap.akunobyek";
	$res_rek = db_query($sql);
	while ($data_rek = db_fetch_object($res_rek)) {
		$row++;
		$objPHPExcel->setActiveSheetIndex(0)
					->setCellValue('A' . $row, $data->namafungsi)
					->setCellValue('B' . $row, $data->namaurusan)
					->setCellValue('C' . $row, $data->skpd)
					->setCellValue('D' . $row, $data->namaprogram)
					->setCellValue('E' . $row, $data->kegiatan)
					->setCellValue('F' . $row, $data_rek->akunutama)
					->setCellValue('G' . $row, $data_rek->akunkelompok)
					->setCellValue('H' . $row, $data_rek->akunjenis)
					->setCellValue('I' . $row, $data_rek->akunobyek)
					->setCellValue('J' . $row, $data_rek->total);
	}
}
						

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Analisis Belanja SKPD');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
$fname = 'analisis_belanja_skpd_' . $kodeuk . '.xlsx';
header('Content-Disposition: attachment;filename=' . $fname);
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
}



function kegiatanskpd_exportexcel_bl($kodeuk) {
error_reporting(E_ALL);

set_time_limit(0);
ini_set('memory_limit', '640M');

ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
//date_default_timezone_set('Europe/London');

if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once 'files/PHPExcel/Classes/PHPExcel.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
// Set document properties
$objPHPExcel->getProperties()->setCreator("SIPKD Anggaran Online")
							 ->setLastModifiedBy("SIPKD Anggaran Online")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Excel document generated from SIPKD Anggaran Online.")
							 ->setKeywords("office 2007 SIPKD Anggaran Online openxml php")
							 ->setCategory("SIPKD Anggaran Analisa");
// Add Header
$row = 1;
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $row ,'SKPD')
			->setCellValue('B' . $row ,'KEGIATAN')
			->setCellValue('C' . $row ,'SUMBER DANA')
			->setCellValue('D' . $row ,'LOKASI')
			->setCellValue('E' . $row ,'ANGGARAN');

//Open data							 
//$customwhere = sprintf(' and k.tahun=%s ', variable_get('apbdtahun', 0));
if ($kodeuk!='00') {
	$customwhere .= sprintf(' and kegiatanskpd.kodeuk=\'%s\' ', $kodeuk);	
}	
$where = ' where kegiatanskpd.inaktif=0 and kegiatanskpd.jenis=2 and kegiatanskpd.total>0 ' . $customwhere;
	
$sql = "SELECT CONCAT_WS(' - ', unitkerja.kodedinas, unitkerja.namauk) skpd, kegiatanskpd.kegiatan, 
		kegiatanskpd.sumberdana1, kegiatanskpd.lokasi, kegiatanskpd.total 
		FROM unitkerja inner join kegiatanskpd ON unitkerja.kodeuk=kegiatanskpd.kodeuk " . $where . " order by unitkerja.kodedinas, kegiatanskpd.kegiatan";
$result = db_query($sql);
while ($data = db_fetch_object($result)) {
	$row++;
	////array('data' => str_replace('||',', ', $data->lokasi), 'align' => 'left', 'valign'=>'top'),
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $row, $data->skpd)
				->setCellValue('B' . $row, $data->kegiatan)
				->setCellValue('C' . $row, $data->sumberdana1)
				->setCellValue('D' . $row, str_replace('||',', ', $data->lokasi))
				->setCellValue('E' . $row, $data->total);
}
						

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('DAFTAR BELANJA LANGSUNG');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
$fname = 'Daftar_Kegiatan_Belanja_Langsung_' . $kodeuk . '.xlsx';
header('Content-Disposition: attachment;filename=' . $fname);
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
}


function kegiatanskpd_exportexcelplafon($kodeuk) {
error_reporting(E_ALL);

set_time_limit(0);
ini_set('memory_limit', '640M');

ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
//date_default_timezone_set('Europe/London');

if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once 'files/PHPExcel/Classes/PHPExcel.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
// Set document properties
$objPHPExcel->getProperties()->setCreator("SIPKD Anggaran Online")
							 ->setLastModifiedBy("SIPKD Anggaran Online")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Excel document generated from SIPKD Anggaran Online.")
							 ->setKeywords("office 2007 SIPKD Anggaran Online openxml php")
							 ->setCategory("SIPKD Anggaran Analisa");
// Add Header
$row = 1;
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A' . $row ,'Fungsi')
			->setCellValue('B' . $row ,'Urusan')
            ->setCellValue('C' . $row ,'SKPD')
            ->setCellValue('D' . $row ,'Program')
			->setCellValue('E' . $row ,'Kegiatan')
			->setCellValue('F' . $row ,'Plafon')
			->setCellValue('G' . $row ,'Anggaran');

//Open data							 
//$customwhere = sprintf(' and k.tahun=%s ', variable_get('apbdtahun', 0));
if ($kodeuk!='00') {
	$customwhere .= sprintf(' and kegiatanskpd.kodeuk=\'%s\' ', $kodeuk);	
}	
$where = ' where inaktif=0 ' . $customwhere;
	
$sql = "SELECT programurusanfungsi.namafungsi, programurusanfungsi.namaurusan, CONCAT_WS(' - ', unitkerja.kodedinas, unitkerja.namasingkat) skpd, programurusanfungsi.namaprogram, kegiatanskpd.kegiatan, kegiatanskpd.plafon, kegiatanskpd.total FROM unitkerja inner join kegiatanskpd ON unitkerja.kodeuk=kegiatanskpd.kodeuk INNER JOIN programurusanfungsi ON kegiatanskpd.kodepro=programurusanfungsi.kodepro " . $where;
$result = db_query($sql);
while ($data = db_fetch_object($result)) {
	$row++;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $row, $data->namafungsi)
				->setCellValue('B' . $row, $data->namaurusan)
				->setCellValue('C' . $row, $data->skpd)
				->setCellValue('D' . $row, $data->namaprogram)
				->setCellValue('E' . $row, $data->kegiatan)
				->setCellValue('F' . $row, $data->plafon)
				->setCellValue('G' . $row, $data->total);
}
						

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Plafon dan Belanja SKPD');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
$fname = 'plafon_dan_belanja_skpd_' . $kodeuk . '.xlsx';
header('Content-Disposition: attachment;filename=' . $fname);
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
}

function GenDataHeader($kodeuk) {
	
	if ($kodeuk!='00') {
		$sql = "select namauk from {unitkerja} where kodeuk='" . $kodeuk . "'" ;
		$res = db_query($sql);
		if ($data = db_fetch_object($res)) {
			$skpd = ' ' . $data->namauk;
		}
	}
	
	$rowsjudul[] = array (array ('data'=>'DAFTAR KEGIATAN' . $skpd, 'width'=>'875px', 'colspan'=>'3', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=>'', 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=>'___________________________________________', 'width'=>'875px', 'colspan'=>'3', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=>'', 'width'=>'535px', 'colspan'=>'3', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	

	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	
	return $output;
	
}

function kegiatanskpd_export_csv($kodeuk) {

	$fname = 'RKA SKPD - ' . $kodeuk . '.csv';
	
	drupal_set_header('Content-Type: text/csv');
	drupal_set_header('Content-Disposition: attachment; filename=' . $fname);

	$header = array ('Nomor', 'Kode Dinas', 'Nama Dinas', 'Kode Kegiatan', 'Nama Kegiatan', 'Lokasi', 'Sumber Dana', 'Sasaran', 'Target', 'Anggaran');
  
	print implode(';', $header) ."\r\n";
	
	$where = ' where k.jenis=2 and k.inaktif=0 and k.total>0 and k.kodeuk=\'%s\'';
	$pquery = sprintf('select k.kodekeg, right(k.kodekeg,3) nomorkeg, k.tahun, k.kodepro, k.kodeuk, k.kodesuk, 
				k.kegiatan, k.jenis, k.lokasi, k.programsasaran, k.programtarget, k.masukansasaran, k.masukantarget,
				k.keluaransasaran, k.keluarantarget, k.hasilsasaran,  k.hasiltarget, k.total, k.plafon, 
				k.totalsebelum, k.totalsesudah, k.waktupelaksanaan, k.sumberdana1, k.sumberdana2, 
				k.sumberdana1rp, k.sumberdana2rp, k.latarbelakang, k.kelompoksasaran, p.program,
				p.kodepro, p.kodeu, u.urusan, u.kodef, u.fungsi, k.tw1, k.tw2, k.tw3, k.tw4, uk.kodedinas, uk.namauk from {kegiatanskpd} k left join {program} p 
				on (k.kodepro = p.kodepro) left join {urusan} u on p.kodeu=u.kodeu inner join {unitkerja} uk on k.kodeuk=uk.kodeuk ' . $where, db_escape_string($kodeuk));
	$pquery .= ' order by k.kegiatan';
	////drupal_set_message($pquery);
	$i = 0;
	$pres = db_query($pquery);
	while ($data = db_fetch_object($pres)) {
		
		$i++;
		
		$kodedinas = $data->kodedinas;
		$namadinas = $data->namauk;
		$kode = $kodedinas . '.' . $data->kodeu . '.' . $data->kodepro . '.' . $data->nomorkeg;
		$kegiatan = $data->kegiatan;
		
		$lokasi = str_replace('||',', ', $data->lokasi);
		
		$keluaransasaran = $data->keluaransasaran;
		$keluarantarget = $data->keluarantarget;
		$total = $data->total;
		
		$sumberdana1 = $data->sumberdana1;

		$values = array ($i, $kodedinas, $namadinas, $kode, $kegiatan, $lokasi, $sumberdana1, $keluaransasaran, $keluarantarget, $total);
		print implode(';', $values) ."\r\n";
		unset($values);
		
	}	
	exit;
	
	/*
	$count = mysql_num_fields($result);  
	for($i = 0; $i < $count; $i++){
		$header[] = mysql_field_name($result, $i);
	}
	*/

	
	/*
	while($row = db_fetch_array($result)){
		foreach($row as $value){
			$values[] = '"' . str_replace('"', '""', decode_entities(strip_tags($value))) . '"'; 
		}
		print implode(',', $values) ."\r\n";
		unset($values);
	}
	*/
	
	//$values = array ($kode, $kegiatan, $lokasi, $sumberdana1, $keluaransasaran, $keluarantarget, $total);
	//print implode(',', $values) ."\r\n";
}


function GenDataPrint($kodeuk, $fsql) {
	set_time_limit(0);
	ini_set('memory_limit', '640M');
	
	//drupal_set_message($fsql);
	
	$totalF =0;
	$totalA =0;
	$headersrek[] = array (
						 
						 array('data' => 'No.',  'width'=> '25px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Kegiatan',  'width' => '265px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Target',  'width' => '165px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Lokasi',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Sumberdana',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Plafon',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Anggaran',  'width' => '90px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Ket',  'width' => '50px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 
					);


	$result = db_query($fsql);
	
	if ($result) {
		while ($data = db_fetch_object($result)) {
			$no += 1;
			
			$totalF += $data->plafon;
			$totalA += $data->total;
			
			$str_plafon='';					

			if ($data->inaktif) $str_plafon .= "*)";

			if ($data->dispensasi) $str_plafon .= "D";
			
			if ($kodeuk=='00')
				$kegnama = $data->kegiatan . ' (' . $data->namasingkat . ')';
			else
				$kegnama = $data->kegiatan;
			
			$rowsrek[] = array (
								 array('data' => $no,  'width'=> '25px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:right;'),
								 array('data' => $kegnama,  'width' => '265px', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => $data->programtarget, 'width' => '165px', 'align' => 'left', 'valign'=>'top', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => str_replace('||',', ', $data->lokasi), 'width' => '100px', 'align' => 'left', 'valign'=>'top', 'style' => ' border-right: 1px solid black; text-align:left;'),								 
								 array('data' => $data->sumberdana,  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => apbd_fn($data->plafon),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
								 array('data' => apbd_fn($data->total),  'width' => '90px', 'style' => ' border-right: 1px solid black; text-align:right;'),
								 array('data' => $str_plafon,  'width' => '50px', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 );				

		}
	}										 
								 			
	$rowsrek[] = array (
						 array('data' => '',  'width'=> '25px', 'style' => 'border-left: 1px solid black;  border-top: 2px solid black; border-bottom: 1px solid black; text-align:right;'),
						 array('data' => 'TOTAL',  'width' => '265px', 'style' => ' border-top: 2px solid black; border-bottom: 1px solid black; text-align:left;'),
						 array('data' => '',  'width' => '165px', 'style' => ' border-top: 2px solid black; border-bottom: 1px solid black; text-align:left;'),
						 array('data' => '',  'width' => '100px', 'style' => ' border-top: 2px solid black; border-bottom: 1px solid black; text-align:left;'),
						 array('data' => '',  'width' => '90px', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; border-bottom: 1px solid black; text-align:left;'),
						 array('data' => apbd_fn($totalF),  'width' => '90px', 'style' => ' border-right: 1px solid black; border-top: 2px solid black; border-bottom: 1px solid black; text-align:right;'),
						 array('data' => apbd_fn($totalA),  'width' => '90px', 'style' => ' border-right: 1px solid black; border-top: 2px solid black; border-bottom: 1px solid black; text-align:right;'),
						 array('data' => '',  'width' => '50px', 'style' => ' border-right: 1px solid black; border-top: 2px solid black; border-bottom: 1px solid black; text-align:left;'),
						 );				

	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '1');
	$output = theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	return $output;	
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
	$sql = 'select mid(k.kodero,1,2) kodek,x.uraian,sum(jumlah) jumlahx from {anggperkeg} k left join {kelompok} x on mid(k.kodero,1,2)=x.kodek inner join {kegiatanskpd} keg on k.kodekeg=keg.kodekeg where keg.inaktif=0 and keg.jenis=' . $jenis;
	$fsql = $sql;
	$fsql .= ' group by mid(k.kodero,1,2),x.uraian order by mid(k.kodero,1,2)';

	//drupal_set_message( $fsql);
	$resultkel = db_query($fsql);
	if ($resultkel) {
		while ($datakel = db_fetch_object($resultkel)) {
			//$total += $datakel->jumlahx;
			$total= $datakel->jumlahx;
			$totalp += ($datakel->jumlahxp- $datakel->jumlahx);
			
			$rowsrek[] = array (
								 array('data' => $datakel->kodek,  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $datakel->uraian, 'width' => '600px', 'colspan'=>'5',  'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => '', 'width' => '90px',  'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => apbd_fn($datakel->jumlahx),  'width' => '125px', 'style' => ' border-right: 1px solid black; border-bottom: 3px solid black; text-align:right;font-weight:bold;'),
								 );


			//JENIS
			$sql = 'select mid(k.kodero,1,3) kodej,j.uraian,sum(jumlah) jumlahx from {anggperkeg} k left join {jenis} j on mid(k.kodero,1,3)=j.kodej  inner join {kegiatanskpd} keg on k.kodekeg=keg.kodekeg where keg.inaktif=0 and mid(k.kodero,1,2)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($datakel->kodek));
			$fsql .= ' group by mid(k.kodero,1,3),j.uraian order by mid(k.kodero,1,3)';				

			//drupal_set_message( $fsql);
			$resultjenis = db_query($fsql);
			if ($resultjenis) {
				while ($datajenis = db_fetch_object($resultjenis)) {
					
					$rowsrek[] = array (
									 array('data' => ($datajenis->kodej),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
									 array('data' => $datajenis->uraian, 'width' => '600px', 'colspan'=>'5',  'style' => ' border-right: 1px solid black; text-align:left;'),
									 array('data' => '', 'width' => '90px',  'style' => ' border-right: 1px solid black; text-align:left;'),
									 array('data' => apbd_fn($datajenis->jumlahx),  'width' => '125px', 'style' => ' border-right: 1px solid black; border-bottom: 2px solid black; text-align:right;font-weight:bold;'),

									 );

					//OBYEK
					$sql = 'select mid(k.kodero,1,5) kodeo,j.uraian,sum(jumlah) jumlahx from {anggperkeg} k left join {obyek} j on mid(k.kodero,1,5)=j.kodeo  inner join {kegiatanskpd} keg on k.kodekeg=keg.kodekeg where keg.inaktif=0 and mid(k.kodero,1,3)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($datajenis->kodej));
					$fsql .= ' group by mid(k.kodero,1,5),j.uraian order by j.kodeo';
				
					$resultobyek = db_query($fsql);
					if ($resultobyek) {
						while ($dataobyek = db_fetch_object($resultobyek)) {
							
							
							$rowsrek[] = array (
										 array('data' => apbd_format_rek_obyek($dataobyek->kodeo),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $dataobyek->uraian, 'width' => '600px', 'colspan'=>'5',  'style' => ' border-right: 1px solid black; text-align:left;'),
										 array('data' => '', 'width' => '90px',  'style' => ' border-right: 1px solid black; text-align:left;'),
										 array('data' => apbd_fn($dataobyek->jumlahx),  'width' => '125px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
										 );		 
												 
							//REKENING
							$sql = 'select k.kodero,r.uraian,sum(jumlah) jumlahx from {anggperkeg} k left join {rincianobyek} r on k.kodero=r.kodero  inner join {kegiatanskpd} keg on k.kodekeg=keg.kodekeg where keg.inaktif=0 and  left(k.kodero,5)=\'%s\'';
							$fsql = sprintf($sql, db_escape_string($dataobyek->kodeo));
							$fsql .= ' group by k.kodero,r.uraian order by k.kodero';
								
							
							//drupal_set_message( $fsql);
							$result= db_query($fsql);
							if ($result) {
								while ($data = db_fetch_object($result)) {
									
									//font-style: italic;
									$rowsrek[] = array (
													 array('data' => apbd_format_rek_rincianobyek($data->kodero),  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
													 array('data' => $data->uraian, 'width' => '600px', 'colspan'=>'5',  'style' => ' border-right: 1px solid black; text-align:left;'),
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

											$rowsrek[] = array (
															 array('data' => '',  'width'=> '60px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
															 array('data' => $no . '.',  'width'=> '50px', 'style' => 'text-align:right;'),
															 array('data' => $datadetil->namauk, 'width' => '550px', 'colspan'=>'4',  'style' => ' border-right: 1px solid black; text-align:left;font-style: italic;'),
															 array('data' => $numkeg, 'width' => '90px',  'style' => ' border-right: 1px solid black; text-align:right;'),
															 array('data' => apbd_fn($datadetil->jumlahx),  'width' => '125px', 'style' => ' border-right: 1px solid black; text-align:right;font-style: italic;'),
														);												
										}	//end detil skpd		
									}
									
								}	//end rekening
								
							}												 
						
						}	//end obyek
					}
				}
			}										 
								 
		////////
		}
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