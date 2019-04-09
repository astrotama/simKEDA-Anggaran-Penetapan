<?php
function rekening_main($arg=NULL, $nama=NULL) {
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
	
   if ($arg) {
		 
	//$qlike = sprintf(" and kodekeg='%s'", db_escape_string($arg));    
		$kodekeg = arg(3);
		$lama = arg(4);
		$qlike = sprintf(" and kodekeg='%s'", db_escape_string($kodekeg));
		
   } else
		//drupal_access_denied();

	drupal_set_message(arg(3));
	if ($lama)
		$opw = '110px';
	else
		$opw = '40px';
		
    $header = array (
        array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
        //array('data' => ucwords(strtolower('kodekeg')), 'field'=> 'kodekeg', 'valign'=>'top'),
		array('data' => '', 'width' => '5px', 'valign'=>'top'),
		array('data' => 'Kode', 'field'=> 'kodero', 'valign'=>'top'),
		array('data' => 'Uraian', 'field'=> 'uraian', 'valign'=>'top'),
		array('data' => 'Sebelumnya', 'field'=> 'jumlahsebelum', 'valign'=>'top', 'width'=>'90px'),
		array('data' => 'Anggaran', 'field'=> 'jumlah', 'valign'=>'top', 'width'=>'90px'),
		//array('data' => 'Tersedia', 'valign'=>'top', 'width'=>'90px'),
		array('data' => 'Catatan', 'valign'=>'top'),
		array('data' => 'UT', 'field'=> 'lastupdate','valign'=>'top'),
		array('data' => '', 'width' => $opw, 'valign'=>'top'),
    );
    $tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by kodero';
    }

	$allowedit = (batastgl() || (isSuperuser()));

	if ($allowedit==false) {
		//dispensasirenja
		//$sqluk = sprintf(' and k.kodeuk=\'%s\' ', apbd_getuseruk());	
        $sql = sprintf('select dispensasi,unlockrincianrekening from {kegiatanskpd} where kodekeg=\'%s\'', $kodekeg);
		$res = db_query($sql);
		if ($res) {
			$data = db_fetch_object($res);
			if ($data) {  		
				$allowedit = $data->dispensasi;
				$unlockrincianrekening = $data->unlockrincianrekening;
			}
		}
	}	
	
	if ($allowedit==false) {
		//dispensasirenja
		//$sqluk = sprintf(' and k.kodeuk=\'%s\' ', apbd_getuseruk());	
        $sql = sprintf('select dispensasibelanja from {unitkerja} where kodeuk=\'%s\'', apbd_getuseruk());
		$res = db_query($sql);
		if ($res) {
			$data = db_fetch_object($res);
			if ($data) {  		
				$allowedit = $data->dispensasibelanja;
			}
		}
	}	
	
    //$customwhere = ' and appkey=\'%s\'';
	$customwhere = ' ';
    $where = ' where true' . $customwhere . $qlike ;

    $sql = 'select kodekeg,kodero,uraian,jumlahsebelum,jumlah,jumlahsesudah,bintang,anggaran,lastupdate from {anggperkeg}' . $where . $tablesort;
    $fsql = sprintf($sql, addslashes($nama));
    $no=0;
    
	//$limit = 15;
    //$countsql = "select count(*) as cnt from {anggperkeg}" . $where;
    //$fcountsql = sprintf($countsql, addslashes($nama));
    //$result = pager_query($fsql . $tablesort, $limit, 0, $fcountsql);
    
    
	//$page = $_GET['page'];
    //if (isset($page)) {
    //   $no = $page * $limit;
    //} else {
    //   $no = 0;
    //}
	
	//NO PAGER
	$result = db_query($fsql);
    if ($result) {
        while ($data = db_fetch_object($result)) {
			$editlink = '';
			if (user_access('kegiatanskpd edit')) {
				//apbdkegrekening/
				
				//$uraian = l($data->uraian, 'apbd/kegiatanskpd/rekening/edit/' . $data->kodekeg . "/" . $data->kodero , array('attributes' => array('target' => '_blank'), 'html' =>TRUE));
				 
				if (isVerifikator()) {
					
					//NEW TAB
					//$uraian = l($data->uraian, 'apbd/kegiatanskpd/rekening/edit/' . $data->kodekeg . "/" . $data->kodero , array('attributes' => array('target' => '_blank'), 'html' =>TRUE));

					$uraian = l($data->uraian, 'apbd/kegiatanskpd/rekening/edit/' . $data->kodekeg . "/" . $data->kodero , array('html' =>TRUE));
					
					//$editlink .= l('Detil', 'apbd/kegiatanskpd/rekening/detil/' . $data->kodekeg . "/" . $data->kodero, array('html'=>TRUE)) . '&nbsp;';
					//$editlink .= l('Edit', 'apbd/kegiatanskpd/rekening/edit/' . $data->kodekeg . "/" . $data->kodero, array('html'=>TRUE)) . '&nbsp;';
				} else {
					$uraian = l($data->uraian, 'apbdkegrekening/' . $data->kodekeg . "/" . $data->kodero , array('html' =>TRUE));					
				}
				
			} else
				$uraian = $data->uraian;
			
			if (user_access('kegiatanskpd penghapusan') and $allowedit)
                $editlink .=l('Hapus', 'apbd/kegiatanskpd/rekening/delete/' . $data->kodekeg . "/" . $data->kodero, array('html'=>TRUE));
			else
                $editlink .='Hapus';
			
			//catatan verifikator
			$catatan = '';	
			$sql_r = "select username,jawaban from {anggperkegverifikasi} where kodekeg='" . $data->kodekeg . "' and kodero='" . $data->kodero . "' order by username";
			$res_r = db_query($sql_r);
			
			$i=0;
			while ($data_r = db_fetch_object($res_r)) {
				if ($data_r->jawaban!='') {
					if ($i==0)
						$catatan .= '<img src="/files/icon/info_red_16.png">' . $arr_user[$data_r->username] . ': <font color="Chocolate">' . $data_r->jawaban . '</font>';
					else
						$catatan .= '<p><img src="/files/icon/info_red_16.png">' . $arr_user[$data_r->username] . ': <font color="Chocolate">' . $data_r->jawaban . '</font></p>';
					
					$i++;
				}
			}
			
			if ($unlockrincianrekening) {
				$sql = sprintf("select sum(total) as jumlahsub from {anggperkegdetil} where kodekeg='%s' and kodero='%s'", $data->kodekeg, $data->kodero);
				$res_detil = db_query($sql);
				if ($data_detil = db_fetch_object($res_detil)) {		
					$jumlahsub = $data_detil->jumlahsub;
					
				}
				
				if ($jumlahsub != $data->jumlah) {
					$catatan .= '<p><img src="/files/icon/info_red_16.png"><font color="Red">Jumlah detil [' . apbd_fn($jumlahsub) . '] tidak sama dengan rekening</font></p>';
					
				}
				
			}
			
			//BINTANG
			$statusrek = '';
			
			if ($data->bintang) {
				$str_agg = '<font color="Red">' . apbd_fn($data->anggaran) . '</font>';
				$statusrek = "<img src='/files/bintang.png'>";
				
			} else {
				$str_agg = apbd_fn($data->jumlah);
			} 
			
			
            $no++;
            $rows[] = array (
                array('data' => $no, 'align' => 'right', 'valign'=>'top'),
                
				//array('data' => $data->id, 'align' => 'left', 'valign'=>'top'),
				array('data' => $statusrek, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data->kodero, 'align' => 'left', 'valign'=>'top'),
				array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($data->jumlahsebelum), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($data->jumlah), 'align' => 'right', 'valign'=>'top'),
				//array('data' => $str_agg, 'align' => 'right', 'valign'=>'top'),
				array('data' => $catatan, 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fdt($data->lastupdate), 'align' => 'left', 'valign'=>'top'),
                array('data' => $editlink, 'align' => 'left', 'valign'=>'top'),
            );
        }
    } else {
        $rows[] = array (
            array('data' => 'Akses/data error, hubungi administrator', 'colspan'=>'6')
        );
    }
	
	//Kosong
	if ($no==0) {
		$linknew = l('<span style="font-size:20px">Rekening Baru</span>', 'apbdkegrekening/' . db_escape_string($arg), array('html' =>TRUE));	
		$importlink = l('<span style="font-size:25px">Import RKA</span>', 'importrkalalu/pilihkegiatan/' . db_escape_string($arg), array('html'=>TRUE));
		
		$rows[] = array (
			array('data' => 'Rekening belum diisikan, klik ' . $linknew . ' untuk menambahkan atau klik ' . $importlink . ' untuk meng-import RKA-SKPD tahun sebelumnya', 'colspan'=>'9')
		);
	}

	$pquery = sprintf("select kegiatan,jenis, total, anggaran, plafon, bintang  from {kegiatanskpd} k left join {unitkerja} u on ( k.kodeuk=u.kodeuk) left join {program} p on (k.kodepro = p.kodepro)  where kodekeg='%s'", db_escape_string($arg));
	$pres = db_query($pquery);	
	if ($data = db_fetch_object($pres)) {
		$ptitle = $data->kegiatan;
		
		$jenis = $data->jenis;
		
		$ptitle =l($ptitle, 'apbd/kegiatanskpd/edit/' . $arg, array('html'=>true));	
		$output .= theme_box('', theme_table($header, $rows));

		//Top Keterangan
		$anggaran = ($data->bintang==0 ? $data->total : $data->anggaran);
		$rows1[] = array (
			array('data' => 'Plafon: ' . apbd_fn($data->plafon) . ', Anggaran: ' . apbd_fn($data->total) . ', Tersedia: ' . apbd_fn($anggaran), 'colspan'=>'6', 'align' => 'right', 'valign'=>'top')
		);	
		$output1 = theme_box('', theme_table('', $rows1));	
		//$output1 = 'Plafon: ' . apbd_fn($data->plafon) . ', Anggaran: ' . apbd_fn($data->total);
		//$output1 .= theme ('pager', NULL, $limit, 0);
		
		drupal_set_title($ptitle);
		
		if (user_access('kegiatanskpd tambah') and $allowedit) {
			if ($lama)
				$output2 = l('Rekening Baru', 'apbd/kegiatanskpd/rekening/edit/' . db_escape_string($arg) , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;
			else
				$output2 = l('Rekening Baru', 'apbdkegrekening/' . db_escape_string($arg) , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;

			$output2 .= "&nbsp;" . l('Import RKA', 'importrkalalu/pilihkegiatan/' . db_escape_string($arg), array ('html' => true, 'attributes'=> array ('class'=>'btn_green', 'style'=>'color:white;')));

			$output2 .= "&nbsp;" . l('Hapus Semua', 'apbd/kegiatanskpd/rekening/delete/' . db_escape_string($arg) . '/all' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;
			
			
		}
		
		//$output2 .= "&nbsp;" .  l('Triwulan', 'apbd/kegiatanskpd/triwulan/' . db_escape_string($arg) , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;

			//Cetak
		$output2 .="&nbsp;" .  l('Preview RKA', 'apbd/kegiatanskpd/print/' . db_escape_string($arg) , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;

		if ($jenis=='2') {
			$output2 .="&nbsp;" .  l('Simpan Excel', 'apbd/kegiatanskpd/print/' . $kodekeg . '/excel' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;
			//$output2 .="&nbsp;" .  l('Simpan CSV', 'apbd/kegiatanskpd/print/' . $kodekeg . '/csv' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;
		}		
		
		//Tombol ke Kegiatan
		$output2 .= "&nbsp;" . l('Buka Kegiatan', 'apbd/kegiatanskpd/edit/' . db_escape_string($arg) , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));	
		
		/*
		if ($allowedit) {
			if ($lama) 
				$output2 .= "&nbsp;" . l('Tampilan Baru', 'apbd/kegiatanskpd/rekening/' . $kodekeg  , array ('html' => true, 'attributes'=> array ('class'=>'btn_green', 'style'=>'color:white;')));	
			else
				$output2 .= "&nbsp;" . l('Tampilan Lama', 'apbd/kegiatanskpd/rekening/' . $kodekeg . '/1'  , array ('html' => true, 'attributes'=> array ('class'=>'btn_green', 'style'=>'color:white;')));	
		}
		*/
		
	//	if (user_access('kegiatanskpd pencarian'))		
	//        $output .= l("<img src='/files/button-search.png' title='Pencarian data'>", 'apbd/kegiatanskpd/subkegiatan/find/' , array('html'=>TRUE)) ;
		$output .= theme ('pager', NULL, $limit, 0);

	} else
		drupal_access_denied(); 
	
	if (isVerifikator()) {
		$toutput = GenReportFormContent($kodekeg);
	}
	
	if ($no<=10)
		return $output1 . $output2 . $output . $toutput;
	else
		return $output1 . $output2 . $output . $output2 . $toutput;
}

function GenReportFormContent($kodekeg) {


	$total=0;


	$headersrek[] = array (
						 array('data' => 'PREVIEW',  'width'=> '875px', 'colspan'=>'7','style' => 'text-align:center;'),
						 );
	
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
	$where = ' where k.kodekeg=\'%s\'';
	$sql = 'select mid(k.kodero,1,3) kodej,j.uraian,sum(jumlah) jumlahx from {anggperkeg} k  left join {jenis} j on mid(k.kodero,1,3)=j.kodej ' . $where;
	$fsql = sprintf($sql, db_escape_string($kodekeg));
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
				   where k.kodekeg=\'%s\' and mid(k.kodero,1,3)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($datajenis->kodej));
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
					$sql = 'select kodero,uraian,jumlah from {anggperkeg} k where kodekeg=\'%s\'  and mid(k.kodero,1,5)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($dataobyek->kodeo));
					
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
	
	return $output;
	
}

?>