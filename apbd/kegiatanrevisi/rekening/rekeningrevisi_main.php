<?php 
function rekeningrevisi_main($arg=NULL, $nama=NULL) {
	drupal_add_css('files/css/kegiatancam.css');
	drupal_add_js('files/js/kegiatancam.js');	
   
   if ($arg) {

		//if (isSuperuser())
		//	drupal_set_message(arg(3));
   
		$kodekeg = arg(3);
		$id = arg(4);
		if ($id=='') $id='0';
		$qlike = sprintf(" and kodekeg='%s'", db_escape_string($kodekeg));    
		//$qlike = sprintf(" and id='%s'", db_escape_string($id));
		
   } else {
	if (isSuperuser())
		drupal_set_message($arg(3));

   }
	
	$revisi = variable_get('apbdrevisi', 0);
	
	if ($revisi==1) {
		$header = array (
			array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
			//array('data' => ucwords(strtolower('kodekeg')), 'field'=> 'kodekeg', 'valign'=>'top'),
			array('data' => 'Kode', 'field'=> 'kodero', 'valign'=>'top'),
			array('data' => 'Uraian', 'field'=> 'uraian', 'valign'=>'top'),
			array('data' => 'Penetapan', 'valign'=>'top', 'width'=>'90px'),
			array('data' => 'Usulan', 'field'=> 'jumlah', 'valign'=>'top', 'width'=>'90px'),
			array('data' => '', 'valign'=>'top'),
			array('data' => '', 'width' => '40px', 'valign'=>'top'),
		);
	} else {
		$strrevisi = 'Revisi #' . strval($revisi-1);
		$header = array (
			array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
			//array('data' => ucwords(strtolower('kodekeg')), 'field'=> 'kodekeg', 'valign'=>'top'),
			array('data' => 'Kode', 'field'=> 'kodero', 'valign'=>'top'),
			array('data' => 'Uraian', 'field'=> 'uraian', 'valign'=>'top'),
			array('data' => 'Penetapan', 'valign'=>'top', 'width'=>'90px'),
			array('data' => $strrevisi, 'valign'=>'top', 'width'=>'100px'),
			array('data' => 'Usulan', 'field'=> 'jumlah', 'valign'=>'top', 'width'=>'90px'),
			array('data' => '', 'valign'=>'top'),
			array('data' => '', 'width' => '40px', 'valign'=>'top'),
		);
	}
	$tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by kodero';
    }

	$batas = mktime(6, 0, 0, 11, 13, 2016) ;
	$sekarang = time () ;
	$selisih =($batas-$sekarang) ;
	$allowedit = (($selisih>0) || (isSuperuser()));

	//CEK DISPENSASI
	if ($allowedit==false) {
        $sqldisp = 'select dispensasirevisi from {unitkerja} where kodeuk=\'%s\'';
        $resdisp = db_query(db_rewrite_sql($sqldisp),  apbd_getuseruk());
		if ($resdisp) {
			if ($datadisp = db_fetch_object($resdisp)) {
				$allowedit = $datadisp->dispensasirevisi;
			}
		}
	}

	$detiluraian_r = 0;
	$rab_r = 0;
	$triwulan_r = 0;
	$lainnya_r = 0;
	$jenisrevisi_r = 0;
	
	if ($id=='0') {
		$detiluraian_r = 1;
		$rab_r = 1;
		$triwulan_r = 1;
		$lainnya_r = 1;
		$jenisrevisi_r = 3;
		
	} else {

		$sql = 'select jenisrevisi, kodekeg, detiluraian,rab, triwulan, lainnya
		from {kegiatanrevisiperubahan} where id=\'%s\'' ;

		//	$qlike = sprintf(" and id='%s'", db_escape_string($id)); 
		$sql = sprintf("select jenisrevisi, kodekeg, detiluraian,rab, triwulan, lainnya
		from {kegiatanrevisiperubahan} where id='%s'", db_escape_string($id));
		$res = db_query($sql);

		if ($res) {
			//if (isSuperuser())
			//	drupal_set_message('q'. $id);
			
			if ($data = db_fetch_object($res)) {
				$detiluraian_r = $data->detiluraian;
				$kodekeg = $data->kodekeg;
				$rab_r = $data->rab;
				$triwulan_r = $data->triwulan;
				$lainnya_r = $data->lainnya;
				$jenisrevisi_r = $data->jenisrevisi;

				//if (isSuperuser())
				//drupal_set_message('z'. $id);

			}
		} 
	}	
	
    //$customwhere = ' and appkey=\'%s\'';
	$qlike = sprintf(" and akg.kodekeg='%s'", db_escape_string($arg));
    $where = ' where true' . $qlike ;

    $sql = 'select akg.kodekeg, akg.kodero, akg.uraian, ro.uraian uraianrek, akg.jumlahsebelum, akg.jumlah, akg.jumlahsesudah from {anggperkegrevisi} akg inner join {rincianobyek} ro on akg.kodero=ro.kodero ' . $where . $tablesort;
    $fsql = sprintf($sql, addslashes($nama));
	
	//if (isSuperuser())
	//	drupal_set_message($fsql);
	
    $no=0;
    
	$result = db_query($fsql);
    if ($result) {
        while ($data = db_fetch_object($result)) {
			$editlink = '';
			$uraian = $data->uraian;
			if ($uraian=='') $uraian = '**MOHON AGAR DIEDIT**';
			if (user_access('kegiatanskpd edit')) {
				
				//$uraian = l($data->uraian, 'apbd/kegiatanskpd/rekening/edit/' . $data->kodekeg . "/" . $data->kodero , array('attributes' => array('target' => '_blank'), 'html' =>TRUE));
				
				$uraian = l($uraian, 'apbdkegrekeningrevisi/' . $data->kodekeg . '/' . $data->kodero . '/' . $id , array('html' =>TRUE));					
				
			} 
			
			
			$no++;
			if ($data->uraian==$data->uraianrek) {
				//$tag = '';
				$tagrekening = '';
				$kodero = $data->kodero;
				
			} else {
				//$tag = '*';
				$kodero = "<font color='red'>" . $data->kodero . "*</font>";

				$sqlrek = sprintf("select uraian from {rincianobyek}} where kodero='%s'", 
							   $data->kodero);
				$resrek = db_query($sqlrek);
				if ($resrek) {
					if ($datarek = db_fetch_object($resrek)) {
						$tagrekening = "<font color='red'>*) " . $data->kodero . ' menunjuk ke ' . $datarek->uraian . "</font>";
					}
				}
				
			}
			
			$penetapan = 0;
			if ($revisi>1) {
				
				//REVISI RESEBELUMNYA
				$revlalu = 0;
				$sqlpen = sprintf("select jumlah,jumlahp from {anggperkegperubahan} where kodekeg='%s' and kodero='%s'", 
							$data->kodekeg, $data->kodero);
				$respen = db_query($sqlpen);
				if ($respen) {
					if ($datapen = db_fetch_object($respen)) {
						$penetapan = $datapen->jumlah;
						$revlalu = $datapen->jumlahp;
					}
				}				
				if (user_access('kegiatanskpd penghapusan') and $allowedit and ($penetapan==0))
					$editlink .=l('Hapus', 'apbd/kegiatanrevisi/rekening/delete/' . $data->kodekeg . "/" . $data->kodero, array('html'=>TRUE));
				else
					$editlink .='Hapus';

				$rows[] = array (
					array('data' => $no, 'align' => 'right', 'valign'=>'top'),
					
					//array('data' => $data->id, 'align' => 'left', 'valign'=>'top'),
					//array('data' => $data->kodekeg, 'align' => 'left', 'valign'=>'top'),
					array('data' => $kodero, 'align' => 'left', 'valign'=>'top'),
					array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fn($penetapan), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($revlalu), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->jumlah), 'align' => 'right', 'valign'=>'top'),
					array('data' => $tagrekening, 'align' => 'left', 'valign'=>'top'),
					array('data' => $editlink, 'align' => 'left', 'valign'=>'top'),
				);

			} else { 

				//PENETAPAN
				$sqlpen = sprintf("select jumlah from {anggperkeg} where kodekeg='%s' and kodero='%s'", 
							$data->kodekeg, $data->kodero);
				$respen = db_query($sqlpen);
				if ($respen) {
					if ($datapen = db_fetch_object($respen)) {
						$penetapan = $datapen->jumlah;
					}
				}

				if (user_access('kegiatanskpd penghapusan') and $allowedit and ($penetapan==0))
					$editlink .=l('Hapus', 'apbd/kegiatanrevisi/rekening/delete/' . $data->kodekeg . "/" . $data->kodero, array('html'=>TRUE));
				else
					$editlink .='Hapus';
				
				$rows[] = array (
					array('data' => $no, 'align' => 'right', 'valign'=>'top'),
					
					//array('data' => $data->id, 'align' => 'left', 'valign'=>'top'),
					//array('data' => $data->kodekeg, 'align' => 'left', 'valign'=>'top'),
					array('data' => $kodero, 'align' => 'left', 'valign'=>'top'),
					array('data' => $uraian, 'align' => 'left', 'valign'=>'top'),
					array('data' => apbd_fn($penetapan), 'align' => 'right', 'valign'=>'top'),
					array('data' => apbd_fn($data->jumlah), 'align' => 'right', 'valign'=>'top'),
					array('data' => $tagrekening, 'align' => 'left', 'valign'=>'top'),
					array('data' => $editlink, 'align' => 'left', 'valign'=>'top'),
				);
			}
        }
    } else {
        $rows[] = array (
            array('data' => 'Akses/data error, hubungi administrator', 'colspan'=>'6')
        );
    }
	
	//Kosong
	if ($no==0) {
		//$linknew =  l('Rekening Baru', 'apbd/kegiatanrevisi/rekening/edit/' . db_escape_string($arg) , array ('html' => true));	
		$linknew = 'Rekening Baru';	
		$rows[] = array (
			array('data' => 'Rekening belum diisikan, klik ' . $linknew . ' untuk menambahkan.', 'colspan'=>'6')
		);
	}

	$pquery = sprintf("select total from {kegiatanskpd} where kodekeg='%s'", db_escape_string($arg));
	$pres = db_query($pquery);	
	if ($data = db_fetch_object($pres)) {
		$totpenetapan = $data->total;
	}
	
	$pquery = sprintf("select kegiatan, total, totalsebelum, concat_ws(' ', concat(p.kodeu,p.np), u.kodedinas, k.nomorkeg) as koderesmi  from {kegiatanrevisi} k left join {unitkerja} u on ( k.kodeuk=u.kodeuk) left join {program} p on (k.kodepro = p.kodepro)  where kodekeg='%s'", db_escape_string($arg));
	$pres = db_query($pquery);	
	if ($data = db_fetch_object($pres)) {
		$ptitle = $data->kegiatan;
	
		$ptitle =l($ptitle, 'apbd/kegiatanrevisi/edit/' . $arg, array('html'=>true));	
		$output .= theme_box('', theme_table($header, $rows));

		//Top Keterangan
		$rows1[] = array (
			array('data' => 'Penetapan: ' . apbd_fn($totpenetapan) . ', Revisi: ' . apbd_fn($data->total), 'colspan'=>'6', 'align' => 'right', 'valign'=>'top')
		);	
		$output1 = theme_box('', theme_table('', $rows1));	
		//$output1 = 'Plafon: ' . apbd_fn($data->plafon) . ', Anggaran: ' . apbd_fn($data->total);
		//$output1 .= theme ('pager', NULL, $limit, 0);
		
		drupal_set_title($ptitle);
		
		if (($jenisrevisi_r != '2') and $allowedit ) {
			if ($lama)
				$output2 = l('Rekening Baru', 'apbd/kegiatanrevisi/rekening/edit/' . db_escape_string($arg) , array ('html' => true, 'attributes'=> array ('class'=>'btn_green', 'style'=>'color:white;'))) ;
			else
				$output2 = l('Rekening Baru', 'apbdkegrekeningrevisi/' . db_escape_string($arg) , array ('html' => true, 'attributes'=> array ('class'=>'btn_green', 'style'=>'color:white;'))) ;

			//$output2 .= "&nbsp;" . l('Hapus Semua', 'apbd/kegiatanrevisi/rekening/delete/' . db_escape_string($arg) , array ('html' => true, 'attributes'=> array ('class'=>'btn_green', 'style'=>'color:white;'))) ;
			
			
		}
		
		$output2 .= "&nbsp;" .  l('Triwulan', 'apbd/kegiatanrevisi/triwulan/' . db_escape_string($arg) . '/1' , array ('html' => true, 'attributes'=> array ('class'=>'btn_green', 'style'=>'color:white;'))) ;
		
		if ($id=='0')
			$output2 .= "&nbsp;" .  l('Cetak', 'apbd/kegiatanskpd/printusulan/' . $kodekeg . '/10/rpka/pdf' , array ('html' => true, 'attributes'=> array ('class'=>'btn_green', 'style'=>'color:white;'))) ;
		else
			$output2 .= "&nbsp;" .  l('Cetak', 'apbd/kegiatanskpd/printusulan/' . $id . '/10/rka/' , array ('html' => true, 'attributes'=> array ('class'=>'btn_green', 'style'=>'color:white;'))) ;
			
		//Cetak
		//$output2 .="&nbsp;" .  l('Preview RKA', 'apbd/kegiatanrevisi/print/' . db_escape_string($arg) , array ('html' => true, 'attributes'=> array ('class'=>'btn_green', 'style'=>'color:white;'))) ;

		//Tombol ke Kegiatan
		$output2 .= "&nbsp;" . l('Buka Kegiatan', 'apbd/kegiatanrevisi/edit/' . $kodekeg . '/' . $id , array ('html' => true, 'attributes'=> array ('class'=>'btn_green', 'style'=>'color:white;')));	
		$output2 .= "&nbsp;" . l('Tutup', 'apbd/kegiatanskpdperubahan', array ('html' => true, 'attributes'=> array ('class'=>'btn_green', 'style'=>'color:white;')));	
		
		/*
		if ($allowedit) {
			if ($lama) 
				$output2 .= "&nbsp;" . l('Tampilan Baru', 'apbd/kegiatanrevisi/rekening/' . $kodekeg  , array ('html' => true, 'attributes'=> array ('class'=>'btn_green', 'style'=>'color:white;')));	
			else
				$output2 .= "&nbsp;" . l('Tampilan Lama', 'apbd/kegiatanrevisi/rekening/' . $kodekeg . '/1'  , array ('html' => true, 'attributes'=> array ('class'=>'btn_green', 'style'=>'color:white;')));	
		}
		*/
		
	//	if (user_access('kegiatanrevisi pencarian'))		
	//        $output .= l("<img src='/files/button-search.png' title='Pencarian data'>", 'apbd/kegiatanrevisi/subkegiatan/find/' , array('html'=>TRUE)) ;
		$output .= theme ('pager', NULL, $limit, 0);

	} else
		drupal_access_denied(); 

    return $output1 . $output2 . $output . $output2;
	//return $output2;
	
	//return 'Halo';
}

?>