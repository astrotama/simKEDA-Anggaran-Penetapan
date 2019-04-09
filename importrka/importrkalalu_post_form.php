<?php
    
function importrkalalu_post_form(){
    drupal_add_css('files/css/kegiatancam.css');		

    $kodekeg = arg(2);
	$kodekeglalu = arg(3);
	
	$total = 0;
	$sql = 'select kegiatan,tahun,total from {kegiatanskpd} where kodekeg=\'%s\'';
	$res = db_query(db_rewrite_sql($sql), array ($kodekeg));
	if ($res) {
		$data = db_fetch_object($res);
		if ($data) {
			$kegiatan = $data->kegiatan . ' (' . $data->tahun . ')';
			$total = $data->total;

		} 
	} 
	$sql = 'select kegiatan,tahun from {kegiatanperubahan} where kodekeg=\'%s\'';
	$res = db_query(db_rewrite_sql($sql), array ($kodekeglalu));
	if ($res) {
		$data = db_fetch_object($res);
		if ($data) {
			$kegiatanlalu = $data->kegiatan . ' (' . $data->tahun . ')';

		} 
	} 

	drupal_set_title('Import RKA-SKPD Lalu');


	$form['kodekeg']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $kodekeg, 
	); 
	$form['kodekeglalu']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $kodekeglalu, 
	); 

	$form['kegiatan']= array(
		'#type'         => 'markup', 
		'#value'		=> '<p>Import RKA-SKPD untuk Kegiatan <strong style="font-size:15px">' . $kegiatan . '</strong> dari Kegiatan <strong style="font-size:15px">' . $kegiatanlalu . '</strong>.</p>', 
	); 
	if ($total>0) {
		$form['peringatan']= array(
			'#type'         => 'markup', 
			'#value'		=> '<p style="color:red">Perhatian : Proses import akan menghapus <strong>SEMUA</strong> isian RKA yang sudah diisikan sebelumnya.</p>', 
		); 
	}
	
	$form['kegiatanrek']= array(
		'#type'         => 'markup', 
		'#value'		=> GenReportFormContent($kodekeglalu), 
	); 
	
    $form['submit'] = array (
        '#type' => 'submit',
		//'#weight'     => 99, 
		'#suffix' => "&nbsp;<a href='/apbd/kegiatanskpd' class='btn_blue' style='color: white'>Tutup</a>",
		'#value' => 'Import RKA'
    );
    
    return $form;
}

function importrkalalu_post_form_submit($form, &$form_state) {

	
	$kodekeg = $form_state['values']['kodekeg'];
	$kodekeglalu = $form_state['values']['kodekeglalu'];
	
	//delete first
	$fsql = sprintf('delete from {anggperkegdetilsub} where iddetil in (select iddetil from {anggperkegdetil} where kodekeg=\'%s\')', db_escape_string($kodekeg));
	//drupal_set_message($fsql);
	$res = db_query($fsql);	
	$fsql = sprintf('delete from {anggperkegdetil} where kodekeg=\'%s\'', db_escape_string($kodekeg));
	//drupal_set_message($fsql);
	$res = db_query($fsql);
	$fsql = sprintf('delete from {anggperkeg} where kodekeg=\'%s\'', db_escape_string($kodekeg));
	//drupal_set_message($fsql);
	$res = db_query($fsql);
	
	
	
	//Anggperkeg
	$total = 0;
	$sql = 'select r.kodero,r.uraian,k.jumlahp jumlah from {anggperkegperubahan} k inner join {rincianobyek} r on k.kodero=r.kodero where k.kodekeg=\'%s\'';
	$fsql = sprintf($sql, db_escape_string($kodekeglalu));
	$result = db_query($fsql);
	if ($result) {
		while ($data = db_fetch_object($result)) {
			$total += $data->jumlah;
			
			$sql = 'insert into {anggperkeg} (kodekeg,kodero,uraian,jumlah,jumlahsebelum,jumlahsesudah,anggaran) 
				   values (\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\')';        
			$res = db_query(db_rewrite_sql($sql), array($kodekeg, $data->kodero,$data->uraian, $data->jumlah, $data->jumlah, $data->jumlahsesudah, $data->jumlah));
		
		}
	}		
	
	//anggperkegdetil
	$sql = 'select k.kodekeg, k.kodero, k.nourut, k.iddetil, k.uraian, k.unitjumlah, k.unitsatuan, k.volumjumlah, k.volumsatuan, k.harga, k.total, k.pengelompokan from {anggperkegdetilperubahan} k inner join {rincianobyek} r on k.kodero=r.kodero where k.total>0 and k.kodekeg=\'%s\' order by k.kodero';
	$fsql = sprintf($sql, db_escape_string($kodekeglalu));	
	//drupal_set_message($fsql);
	$result = db_query($fsql);
	if ($result) {
		while ($data = db_fetch_object($result)) {
			
			//drupal_set_message($data->kodero);
			
			$sql = 'insert into {anggperkegdetil} (kodekeg, kodero, nourut, uraian, unitjumlah, unitsatuan, volumjumlah, volumsatuan, harga, total, pengelompokan, anggaran) values(\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\')';        
			$res = db_query(db_rewrite_sql($sql), array($kodekeg, $data->kodero, $data->nourut, $data->uraian, $data->unitjumlah, $data->unitsatuan, $data->volumjumlah, $data->volumsatuan, $data->harga, $data->total, $data->pengelompokan, $data->total));	
			
			$iddetil_new = db_last_insert_id('anggperkegdetil', 'iddetil');
			
			//drupal_set_message($iddetil_new . '|' . $data->iddetil);
			
			//anggperkegdetilsub
			if ($data->pengelompokan) {
				//SUB DETIL
				$sql = 'select idsub,nourut,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan, harga,total, anggaran from {anggperkegdetilsubperubahan} where total>0 and iddetil=\'%s\' order by nourut asc,idsub';
				$fsql = sprintf($sql, db_escape_string($data->iddetil));
				$res_sub = db_query($fsql);
				if ($res_sub) {
					while ($data_sub = db_fetch_object($res_sub)) {

						$sql = 'insert into {anggperkegdetilsub} (iddetil, nourut, uraian, unitjumlah, unitsatuan, volumjumlah, volumsatuan, harga, total, anggaran) values(\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'%s\')';        
						$res = db_query(db_rewrite_sql($sql), array($iddetil_new, $data_sub->nourut, $data_sub->uraian, $data_sub->unitjumlah, $data_sub->unitsatuan, $data_sub->volumjumlah, $data_sub->volumsatuan, $data_sub->harga, $data_sub->total, $data_sub->total));												
					}
				}
				
			}
		
		}
	}		
	
	
	//update kegiatan
	$sql = sprintf("update {kegiatanskpd} set total='%s',anggaran='%s' where kodekeg='%s'", db_escape_string($total), db_escape_string($total), $kodekeg);		
	$res = db_query($sql);
	
    drupal_goto('apbd/kegiatanskpd/rekening/' . $kodekeg);    
}

function GenReportFormContent($kodekeg) {
	
	
	$total=0;

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
	$sql = 'select mid(k.kodero,1,3) kodej,j.uraian,sum(jumlahp) jumlahx from {anggperkegperubahan} k  left join {jenis} j on mid(k.kodero,1,3)=j.kodej ' . $where;
	$fsql = sprintf($sql, db_escape_string($kodekeg));
	$fsql .= ' group by mid(k.kodero,1,3),j.uraian order by mid(k.kodero,1,3)';
	
	//drupal_set_message( $fsql);
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
			$sql = 'select mid(k.kodero,1,5) kodeo,o.uraian,sum(jumlahp) jumlahx from {anggperkegperubahan} k  left join {obyek} o on mid(k.kodero,1,5)=o.kodeo 
				   where kodekeg=\'%s\' and mid(k.kodero,1,3)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($datajenis->kodej));
			$fsql .= ' group by mid(k.kodero,1,5),o.uraian order by mid(k.kodero,1,5)';
			
			////drupal_set_message( $fsql);
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
					$sql = 'select kodero,uraian,jumlahp jumlah from {anggperkegperubahan} k where jumlahp>0 and kodekeg=\'%s\' and mid(k.kodero,1,5)=\'%s\'';
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
							$sql = 'select iddetil,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan,harga,total,anggaran, pengelompokan from {anggperkegdetilperubahan} where total>0 and kodekeg=\'%s\' and kodero=\'%s\' order by nourut asc,iddetil';
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
														 array('data' => $datadetil->uraian ,  'width' => '385px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;'),
														 array('data' => $unitjumlah, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;'),
														 array('data' => $volumjumlah, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;'),
														 array('data' => $hargasatuan,  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;'),
														 array('data' => apbd_fn($datadetil->total),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;'),
														 );
									if ($datadetil->pengelompokan) {
										//SUB DETIL
										$sql = 'select idsub,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan, harga,total, anggaran from {anggperkegdetilsubperubahan} where total>0 and iddetil=\'%s\' order by nourut asc,idsub';
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
							}	//DETIL
							
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
	
	$output .= $toutput;
	
	return $output;
	
}
?>