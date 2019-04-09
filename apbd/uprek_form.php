<?php

function uprek_form() {

	$form['item']= array(
		'#type' => 'markup',
		'#value' => '</br><h3>PETUNJUK UPDATE DATA REALISASI KAS</h3>
<ol>
<li>Copy table <strong>dokumen</strong> dari database Penatausahaan</li>
<li>Import tabel hasil langkah pertama ke database Anggaran Kas</li>
<li>Ubah nama tabel dokumen dengan menambahkan index bulan yang sesuai</li>
<li>Copy table <strong>dokumenrekening</strong> dari database Penatausahaan</li>
<li>Import tabel hasil langkah pertama ke database Anggaran Kas</li>
<li>Ubah nama tabel <strong>dokumenrekening</strong> dengan menambahkan index bulan yang sesuai</li>
<li>Jalankan proses Update dengan meng-klik tombol <strong>Update</strong> dibawah satu demi satu.</li>
</ol></br></br>',
		//'#disabled' => TRUE,		
	);
	
	$form['uprektable']= array(
		'#prefix' => '<table>',
		 '#suffix' => '</table>',
	);
	$form['uprektable']['update1']= array(
		'#type' => 'submit',
		'#value' => 'Update 1',
		'#prefix' => '<tr><td>',
		'#suffix' => '</td>',
	);
	$form['uprektable']['update2']= array(
		'#type' => 'submit',
		'#value' => 'Update 2',
		'#prefix' => '<td>',
		'#suffix' => '</td>',
	);
	$form['uprektable']['update3']= array(
		'#type' => 'submit',
		'#value' => 'Update 3',
		'#prefix' => '<td>',
		'#suffix' => '</td>',
	);
	$form['uprektable']['update4']= array(
		'#type' => 'submit',
		'#value' => 'Update 4',
		'#prefix' => '<td>',
		'#suffix' => '</td>',
	);
	$form['uprektable']['update5']= array(
		'#type' => 'submit',
		'#value' => 'Update 5',
		'#prefix' => '<td>',
		'#suffix' => '</td></tr>',
	);
	
	$form['uprektable']['rekap1']= array(
		'#type' => 'submit',
		'#value' => 'Rekap 1',
		'#prefix' => '<tr><td>',
		'#suffix' => '</td>',
	);
	$form['uprektable']['rekap2']= array(
		'#type' => 'submit',
		'#value' => 'Rekap 2',
		'#prefix' => '<td>',
		'#suffix' => '</td>',
	);
	$form['uprektable']['rekap3']= array(
		'#type' => 'submit',
		'#value' => 'Rekap 3',
		'#prefix' => '<td>',
		'#suffix' => '</td>',
	);
	$form['uprektable']['rekap4']= array(
		'#type' => 'submit',
		'#value' => 'Rekap 4',
		'#prefix' => '<td>',
		'#suffix' => '</td>',
	);
	$form['uprektable']['rekap5']= array(
		'#type' => 'submit',
		'#value' => 'Rekap 5',
		'#prefix' => '<td>',
		'#suffix' => '</td></tr>',
	);
	


	
	return $form;
}

function uprek_form_submit($form, &$form_state) {
	if($form_state['clicked_button']['#value'] == $form_state['values']['update1']) {
		return update_realisasi_kas(1);
	} else if($form_state['clicked_button']['#value'] == $form_state['values']['update2']) {
		return update_realisasi_kas(2);
	} else if($form_state['clicked_button']['#value'] == $form_state['values']['update3']) {
		return update_realisasi_kas(3);
	} else if($form_state['clicked_button']['#value'] == $form_state['values']['update4']) {
		return update_realisasi_kas(4);
	} else if($form_state['clicked_button']['#value'] == $form_state['values']['update5']) {
		return update_realisasi_kas(5);
	} else if($form_state['clicked_button']['#value'] == $form_state['values']['rekap1']) {
		return rekap_realisasi_kas(1);
	} else if($form_state['clicked_button']['#value'] == $form_state['values']['rekap2']) {
		return rekap_realisasi_kas(2);
	} else if($form_state['clicked_button']['#value'] == $form_state['values']['rekap3']) {
		return rekap_realisasi_kas(3);
	} else if($form_state['clicked_button']['#value'] == $form_state['values']['rekap4']) {
		return rekap_realisasi_kas(4);
	} else if($form_state['clicked_button']['#value'] == $form_state['values']['rekap5']) {
		return rekap_realisasi_kas(5);
	}
		
}


function update_realisasi_kas($index) {
	
	$bulan = date('n');
	$bulan = $bulan-1;
	
	drupal_set_message($bulan);
	
	
	if ($index =='1') {
		$sql = "update {kegiatanperubahan} k set k.kodeuk='00' where k.kodeuk='81' and k.isppkd=1";	
		$result = db_query($sql);
		if ($result) drupal_set_message('Update OK');
	}

	if ($index=='1')	
		$where = sprintf(' and k.kodeuk<=\'%s\'', '25');
	elseif ($index=='2')	
		$where = sprintf(' and k.kodeuk>=\'%s\' and k.kodeuk<=\'%s\'', '26', '50');
	elseif ($index=='3')	
		$where = sprintf(' and k.kodeuk>=\'%s\' and k.kodeuk<=\'%s\'', '51', '75');
	elseif ($index=='4')
		$where = sprintf(' and k.kodeuk>=\'%s\' and k.kodeuk<=\'%s\'', '76', 'A0');
	else
		$where = sprintf(' and k.kodeuk>=\'%s\'', 'A1');
	
	$sql = sprintf('select distinct k.kodeuk, k.kodekeg,k.jenis from {dokumen' . $bulan .  '} d inner join {kegiatanperubahan} k on d.kodekeg=k.kodekeg where d.sp2dok=1 and month(sp2dtgl)=\'%s\'', $bulan) . $where ;
	//drupal_set_message($sql);
    $result = db_query($sql);
    
    if ($result) {
        while ($data = db_fetch_object($result)) {
			
			drupal_set_message($data->kodekeg);
			
			//RESET PROGRESS KAS
			$sql = "delete from {realisasi} where kode='" . $data->kodekeg . "' and bulan=" . $bulan;
			$res = db_query($sql);

			$sqlwhere = sprintf(' where d.sp2dok=1 and d.kodekeg=\'%s\' and month(d.sp2dtgl)=\'%s\'', $data->kodekeg, $bulan);


			$gaji = 0; $bunga = 0; $subsidi = 0; $hibah = 0; $bansos = 0; $bagihasil = 0; $bankeu = 0; $ttg = 0; 

			$hibahpusat = 0; $hibahmasyarakat = 0; $hibahbadan = 0; $hibahspd = 0; 
			$bankeudesa = 0; $bankeuparpol = 0;

			$pegawai = 0; $barangjasa = 0; $modal = 0; $tanah = 0; $mesin = 0; $gedung = 0; $jaringan = 0; $lainya = 0; 
						
			
			if ($data->jenis=='1') {		//BTL
				$sql = 'select left(kodero,3) kodej, sum(di.jumlah) realisasi from {dokumenrekening' . $bulan .  '} di inner join {dokumen' . $bulan .  '} d on di.dokid=d.dokid ' . $sqlwhere;
				//drupal_set_message($sql);
				$res = db_query($sql);
				if ($res) {
					while ($datarea = db_fetch_object($res)) {
						if ($datarea->kodej=='511')
							$gaji = $datarea->realisasi;
						elseif ($datarea->kodej=='512')
							$bunga = $datarea->realisasi;
						elseif ($datarea->kodej=='513')
							$subsidi = $datarea->realisasi;
						elseif ($datarea->kodej=='514')
							$hibah = $datarea->realisasi;
						elseif ($datarea->kodej=='515')
							$bansos = $datarea->realisasi;
						elseif ($datarea->kodej=='516')
							$bagihasil = $datarea->realisasi;
						elseif ($datarea->kodej=='517')
							$bankeu = $datarea->realisasi;
						elseif ($datarea->kodej=='518')
							$ttg = $datarea->realisasi;
					}
				}
				
				//HIBAH
				if ($hibah>0) {
					$sql = 'select left(kodero,5) kodeo, sum(di.jumlah) realisasi from {dokumenrekening' . $bulan .  '} di inner join {dokumen' . $bulan .  '} d on di.dokid=d.dokid ' . $sqlwhere;
					//drupal_set_message($sql);
					$res = db_query($sql);
					if ($res) {
						while ($datarea = db_fetch_object($res)) {
							
							if ($datarea->kodeo=='51401')
								$hibahpusat = $datarea->realisasi;
							elseif ($datarea->kodeo=='51405')
								$hibahmasyarakat = $datarea->realisasi;
							elseif ($datarea->kodeo=='51406')
								$hibahbadan = $datarea->realisasi;
							elseif ($datarea->kodeo=='51407')
								$hibahspd = $datarea->realisasi;
						}
					}					
				}

				//HIBAH
				if ($bankeu>0) {
					$sql = 'select left(kodero,5) kodeo, sum(di.jumlah) realisasi from {dokumenrekening' . $bulan .  '} di inner join {dokumen' . $bulan .  '} d on di.dokid=d.dokid ' . $sqlwhere;
					//drupal_set_message($sql);
					$res = db_query($sql);
					if ($res) {
						while ($datarea = db_fetch_object($res)) {
							if ($datarea->kodeo=='51703')
								$bankeudesa = $datarea->realisasi;
							elseif ($datarea->kodeo=='51704')
								$bankeuparpol = $datarea->realisasi;
						}
					}					
				}
				
			} else {		//BL
				$sql = 'select left(kodero,3) kodej, sum(di.jumlah) realisasi from {dokumenrekening' . $bulan .  '} di inner join {dokumen' . $bulan .  '} d on di.dokid=d.dokid ' . $sqlwhere;
				//drupal_set_message($sql);
				$res = db_query($sql);
				if ($res) {
					while ($datarea = db_fetch_object($res)) {
						if ($datarea->kodej=='521')
							$pegawai = $datarea->realisasi;
						elseif ($datarea->kodej=='522')
							$barangjasa = $datarea->realisasi;
						elseif ($datarea->kodej=='523')
							$modal = $datarea->realisasi;
					}
				}
				drupal_set_message($modal);
				
				//MODAL
				if ($modal>0) {
					$sql = 'select left(kodero,5) kodeo, sum(di.jumlah) realisasi from {dokumenrekening' . $bulan .  '} di inner join {dokumen' . $bulan .  '} d on di.dokid=d.dokid ' . $sqlwhere;
					//drupal_set_message($sql);
					$res = db_query($sql);
					if ($res) {
						while ($datarea = db_fetch_object($res)) {
							if ($datarea->kodeo=='52301')
								$tanah = $datarea->realisasi;
							elseif ($datarea->kodeo=='52302')
								$mesin = $datarea->realisasi;
							elseif ($datarea->kodeo=='52303')
								$gedung = $datarea->realisasi;
							elseif ($datarea->kodeo=='52304')
								$jaringan = $datarea->realisasi;
							elseif ($datarea->kodeo=='52305')
								$lainya = $datarea->realisasi;
						}
					}					
				}					

			}
			
		
			//INSERT
			$sql = 'insert into {realisasi} (kode, kodeuk, bulan, gaji, barangjasa, pegawai, tanah, mesin, gedung, jaringan, lainya, bunga, subsidi, hibah, bansos, bagihasil, bankeu, ttg, hibahpusat, hibahmasyarakat, hibahbadan, hibahspd, bankeudesa, bankeuparpol) values (\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\')';
			$res = db_query(db_rewrite_sql($sql), array($data->kodekeg, $data->kodeuk, $bulan, $gaji, $barangjasa, $pegawai, $tanah, $mesin, $gedung, $jaringan, $lainya, $bunga, $subsidi, $hibah, $bansos, $bagihasil, $bankeu, $ttg, $hibahpusat, $hibahmasyarakat, $hibahbadan, $hibahspd, $bankeudesa, $bankeuparpol));
			
			if ($res) {
				drupal_set_message('OK');
			}
        }
    }
}

function rekap_realisasi_kas($index) {

	if ($index=='1') {
		$sql = "update {realisasi} r, {kegiatanperubahan} k set r.kodeuk=k.kodeuk where r.kode=k.kodekeg and (r.kodeuk='81' or r.kodeuk='00')";
		$result = db_query($sql);
		if ($result) drupal_set_message('Update OK');
	}
	
	//Bulan
	$bulan = date('n');
	$initbulan = 1;

	if ($index=='1')	
		$where = sprintf(' where kodeuk<=\'%s\'', '25');
	elseif ($index=='2')	
		$where = sprintf(' where kodeuk>=\'%s\' and kodeuk<=\'%s\'', '26', '50');
	elseif ($index=='3')	
		$where = sprintf(' where kodeuk>=\'%s\' and kodeuk<=\'%s\'', '51', '75');
	elseif ($index=='4')
		$where = sprintf(' where kodeuk>=\'%s\' and kodeuk<=\'%s\'', '76', 'A0');
	else
		$where = sprintf(' where kodeuk>=\'%s\'', 'A1');
	
	$sql = 'select kodeuk from {unitkerja}' . $where;
    $result = db_query($sql);
    
    if ($result) {
        while ($data = db_fetch_object($result)) {
			
			drupal_set_message($data->kodeuk);
			
			//ANGGARAN APBD
			//BTL
			$apbdbtlagg = 0;
			$sql = "select sum(a.jumlahp) jumlah from {anggperkegperubahan} a inner join {kegiatanperubahan} k on a.kodekeg=k.kodekeg where k.kodeuk='" . $data->kodeuk . "' and a.kodero LIKE '51%'";
			$res = db_query($sql);
			if ($res) {
				if ($dataagg = db_fetch_object($res)) {
					$apbdbtlagg = $dataagg->jumlah;
				}
			}
			$apbdblagg = 0;
			$sql = "select sum(a.jumlahp) jumlah from {anggperkegperubahan} a inner join {kegiatanperubahan} k on a.kodekeg=k.kodekeg where k.kodeuk='" . $data->kodeuk . "' and a.kodero LIKE '52%'";
			$res = db_query($sql);
			if ($res) {
				if ($dataagg = db_fetch_object($res)) {
					$apbdblagg = $dataagg->jumlah;
				}
			}
			$apbdtotalagg = $apbdbtlagg + $apbdblagg;
			
			
			for ($i=$initbulan; $i<=$bulan; $i++) {

				//RESET PROGRESS KAS
				$sql = "delete from {progressrea} where kodeuk='" . $data->kodeuk . "' and bulan=" . $i;
				$res = db_query($sql);

				$sqlwhere = sprintf(' where k.kodeuk=\'%s\' and t.bulan<=\'%s\'', $data->kodeuk, $i);


				$btlagg = 0; $blagg = 0;
				$btlrea = 0; $blrea = 0;
				$btlpersen = 0; $blpersen = 0;
				
				$totalagg = 0; $totalrea = 0; $totalpersen = 0;
				
				//READ KAS
				$sql = 'select t.kodeuk, sum(t.gaji+t.bunga+t.subsidi+t.hibah+t.bansos+t.bagihasil+t.bankeu+t.ttg) btlagg, sum(t.pegawai+t.barangjasa+t.tanah+t.mesin+t.gedung+t.jaringan+t.lainya) blagg 
				from {triwulan} t inner join {kegiatanperubahan} k on t.kode=k.kodekeg ' . $sqlwhere;
				$res = db_query($sql);
				if ($res) {
					if ($datadetil = db_fetch_object($res)) {
						$btlagg = $datadetil->btlagg;
						$blagg = $datadetil->blagg;
						$totalagg = $btlagg + $blagg;
					}
				}
			
				//READ REA
				$sql = 'select t.kodeuk, sum(t.gaji+t.bunga+t.subsidi+t.hibah+t.bansos+t.bagihasil+t.bankeu+t.ttg) btlrea, sum(t.pegawai+t.barangjasa+t.tanah+t.mesin+t.gedung+t.jaringan+t.lainya) blrea 
				from {realisasi} t inner join {kegiatanperubahan} k on t.kode=k.kodekeg ' . $sqlwhere;
				$res = db_query($sql);
				if ($res) {
					if ($datadetil = db_fetch_object($res)) {
						$btlrea = $datadetil->btlrea;
						$blrea = $datadetil->blrea;
						$totalrea = $btlrea + $blrea;
					}
				}
				
				//KAS
				$btlpersen = apbd_hitungpersen_abs($btlagg, $btlrea);
				$blpersen = apbd_hitungpersen_abs($blagg, $blrea);
				$totalpersen = apbd_hitungpersen_abs($totalagg, $totalrea);
				
				//APBD
				$apbdbtlpersen = apbd_hitungpersen_abs($apbdbtlagg, $btlrea);
				$apbdblpersen = apbd_hitungpersen_abs($apbdblagg, $blrea);
				$apbdtotalpersen = apbd_hitungpersen_abs($apbdtotalagg, $totalrea);
				

				//INSERT
				$sql = 'insert into {progressrea} (kodeuk, bulan, btlagg, blagg, btlrea, blrea, btlpersen, blpersen, totalagg, totalrea, totalpersen, apbdbtlagg, apbdbtlpersen, apbdblagg, apbdblpersen, apbdtotalagg, apbdtotalpersen) values (\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\',\'%s\')';
				$res = db_query(db_rewrite_sql($sql), array($data->kodeuk, $i, $btlagg, $blagg, $btlrea, $blrea, $btlpersen, $blpersen, $totalagg, $totalrea, $totalpersen, $apbdbtlagg, $apbdbtlpersen, $apbdblagg, $apbdblpersen, $apbdtotalagg, $apbdtotalpersen));
				
				if ($res) {
					drupal_set_message('OK');
				}
			}
        }
    }
}


?>