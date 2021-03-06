<?php
function detilpendapatan_main($arg=NULL, $nama=NULL) {
	drupal_add_css('files/css/kegiatancam.css');
	drupal_add_js('files/js/kegiatancam.js');	
    if ($arg) {
		//$qlike = sprintf(" and id='%s'", db_escape_string($id));    
		$kodeuk=arg(3);
		$kodero = arg(4);
		$tahun = variable_get('apbdtahun', 0);
		
		//drupal_set_message(arg(3));
		
		$qlike = sprintf(" and tahun='%s' and kodeuk='%s' and kodero='%s'", db_escape_string($tahun), db_escape_string($kodeuk), db_escape_string($kodero));
		
		//drupal_set_message($kodero);
		//drupal_set_message($qlike);
		
	} else
		drupal_access_denied();
    
	$header = array (
        array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
        //array('data' => ucwords(strtolower('kodekeg')), 'field'=> 'kodekeg', 'valign'=>'top'),
		array('data' => 'Uraian', 'valign'=>'top'),
		array('data' => 'Unit', 'valign'=>'top', 'width'=>'90px'),
		array('data' => 'Volume', 'valign'=>'top', 'width'=>'90px'),
		array('data' => '@Harga', 'valign'=>'top', 'width'=>'90px'),
		array('data' => 'Jml. Harga', 'valign'=>'top', 'width'=>'90px'),
		array('data' => '', 'width' => '110px', 'valign'=>'top'),
    );
    $tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by iddetil';
    }

    //$customwhere = ' and appkey=\'%s\'';
	$customwhere = ' ';
    $where = ' where true'  . $qlike ;

    $sql = 'select iddetil,kodeuk,kodero,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan,harga,total,pengelompokan from {anggperukdetil}' . $where;
    $fsql = sprintf($sql, addslashes($nama));
    $limit = 15;
    //echo $fsql;
    $countsql = "select count(*) as cnt from {anggperukdetil}" . $where;
    $fcountsql = sprintf($countsql, addslashes($nama));
    $result = pager_query($fsql . $tablesort, $limit, 0, $fcountsql);

	$batas = mktime(6, 0, 0, 11, 13, 2015) ;
	$sekarang = time () ;
	$selisih =($batas-$sekarang) ;
	$allowedit = (($selisih>0) || (isSuperuser()));	

	if ($allowedit==false) {
		//dispensasippas
		//$sqluk = sprintf(' and k.kodeuk=\'%s\' ', apbd_getuseruk());	
        $sql = sprintf('select dispensasippas from {unitkerja} where kodeuk=\'%s\'', apbd_getuseruk());
		$res = db_query($sql);
		if ($res) {
			$data = db_fetch_object($res);
			if ($data) {  		
				$allowedit = $data->dispensasippas;
			}
		}
	}
    
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
			
			$kodero = $data->kodero;
			if (user_access('kegiatanskpd edit')) {
				$editlink .= l('Edit', 'apbd/pendapatan/detil/edit/' . $kodeuk . '/' . $kodero . '/' . $data->iddetil, array('html'=>TRUE)) . '&nbsp;';
			//if (user_access('kegiatanskpd penghapusan'))
				if ($allowedit)
					$editlink .=l('Hapus', 'apbd/pendapatan/detil/delete/' . $kodeuk . '/' . $kodero . '/' . $data->iddetil, array('html'=>TRUE)) . '&nbsp;';
				else
					$editlink .= 'Hapus'  . '&nbsp;';

				if ($data->pengelompokan) {
					$editlink .= l('Subdetil', 'apbd/pendapatan/detil/subdetil/' . $kodeuk . '/' . $kodero . '/' . $data->iddetil, array('html'=>TRUE));

					$unitjumlah = '';
					$volumjumlah = '';
					$hargasatuan = '';
					$hargatotal = apbd_fn($data->total);
					
					//if (isSuperuser()) {
					//	$editlink .= l('Subdetil', 'apbd/kegiatanskpd/rekening/detil/subdetil/' . $kodekeg . '/' . $kodero . '/' . $data->iddetil, array('html'=>TRUE));
					
					//}
					
				} else {
					$editlink .= 'Subdetil';

					$unitjumlah = $data->unitjumlah . ' ' . $data->unitsatuan;
					$volumjumlah = $data->volumjumlah . ' ' . $data->volumsatuan;
					$hargasatuan = apbd_fn($data->harga);
					$hargatotal = apbd_fn($data->total);
				}
			
			}
            $no++;
            $rows[] = array (
                array('data' => $no, 'align' => 'right', 'valign'=>'top'),
                
				array('data' => $data->uraian, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data->unitjumlah . ' ' . $data->unitsatuan, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data->volumjumlah . ' ' . $data->volumsatuan, 'align' => 'left', 'valign'=>'top'),
				array('data' => apbd_fn($data->harga), 'align' => 'right', 'valign'=>'top'),
				array('data' => apbd_fn($data->total), 'align' => 'right', 'valign'=>'top'),
                array('data' => $editlink, 'align' => 'right', 'valign'=>'top'),
            );
        }
    } else {
		//drupal_set_message('Goes here');
        $rows[] = array (
            array('data' => 'data kosong, silahkan menambahkan', 'colspan'=>'5')
        );
    }
	$pquery = sprintf("select uraian,jumlah from {anggperuk} where tahun='%s' and kodeuk='%s' and kodero='%s'", db_escape_string($tahun), db_escape_string($kodeuk), db_escape_string($kodero));
	$pres = db_query($pquery);	
	if ($data = db_fetch_object($pres))
		$ptitle = $data->uraian . ', Anggaran: ' . apbd_fn($data->jumlah);


	
	//$ptitle =l($ptitle, 'apbd/kegiatanskpd/rekening/' . $arg, array('html'=>true));
    $output .= theme_box($ptitle, theme_table($header, $rows));
	if (user_access('kegiatanskpd tambah'))
		$output .= l('Detil Rekening Baru', 'apbd/pendapatan/detil/edit/' . $kodeuk . '/' . $kodero , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;

	$output .= "&nbsp;" . l('Daftar Rekening', 'apbd/pendapatan' , array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) ;

	//	if (user_access('kegiatanskpd pencarian'))		
//        $output .= l("<img src='/files/button-search.png' title='Pencarian data'>", 'apbd/kegiatanskpd/subkegiatan/find/' , array('html'=>TRUE)) ;
    $output .= theme ('pager', NULL, $limit, 0);
    return $output;
}

?>