<?php
function importpendapatan_main($arg=NULL, $nama=NULL) {
	drupal_add_css('files/css/kegiatancam.css');
 	drupal_add_js('files/js/kegiatanlt.js');
	
	$kodeuk = arg(2);
	if (!isSuperuser()) $kodeuk = apbd_getuseruk();
	$jenis = 0;

	
	
	drupal_set_title('Pilih Rekening');
	
	$header = array (
		array('data' => '', 'width' => '5px'),
		array('data' => 'No', 'width' => '10px'),
		array('data' => 'Kode', 'field'=> 'kodero'),
		array('data' => 'Uraian', 'field'=> 'uraian'),
		array('data' => 'Anggaran', 'field'=> 'jumlahp'),
		array('data' => 'Dasar Hukum'),
		array('data' => '', 'width' => '90px'),
	);
		
	$tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by a.kodero';
    }
	
	//Kegiatan yang sama persis
    $sql_keg = sprintf('select r.kodero,r.uraian,a.jumlahp,a.ketrekening from {anggperukperubahan} a inner join {rincianobyek} r on a.kodero=r.kodero where a.kodeuk=\'%s\' and a.kodero=\'%s\'', $kodeuk, $kodero);
	//drupal_set_message($sql_keg);
	$res_keg = db_query($sql_keg);
	if ($res_keg) {
		if ($data_keg = db_fetch_object($res_keg)) {
			$editlink = l('Import', 'importpendapatan/importrka/' . $kodero . '/' . $kodeuk, array('html' =>TRUE));
            $rows[] = array (
				array('data' => '', 'align' => 'right'),
                array('data' => '', 'align' => 'right'),
				array('data' => $data_keg->kodero, 'align' => 'left'),
                array('data' => '<strong style="font-size:20px; color:green">' . $data_keg->uraian . '</strong>', 'align' => 'left'),
                array('data' => apbd_fn($data_keg->jumlahp), 'align' => 'right'),
                array('data' => $data_keg->ketrekening, 'align' => 'left'),
                array('data' => $editlink, 'align' => 'right'),
            );
			
		}
	}

    $where = sprintf('where a.kodeuk=\'%s\'', $kodeuk);
	
    $sql = 'select r.kodero,r.uraian,a.jumlahp,a.ketrekening from {anggperukperubahan} a inner join {rincianobyek} r on a.kodero=r.kodero ' . $where;
	
	$fsql = sprintf($sql, addslashes($nama));
	
	//drupal_set_message($fsql);
	
    $limit = 30;
    $countsql = 'select count(*) as cnt from {anggperukperubahan} a ' . $where;
    $fcountsql = sprintf($countsql, addslashes($nama));
    $result = pager_query($fsql . $tablesort, $limit, 0, $fcountsql);
    
    $no=0;
    $page = $_GET['page'];
    if (isset($page)) {
        $no = $page * $limit;
    } else {
        $no = 0; 
    }
    if ($result) {
        while ($data = db_fetch_object($result)) {

			//$kegname = l($data->kegiatan, 'apbd/kegiatanrevisi/editperubahan/0/' . $kodeuk . '/' . $data->kodekeg, array('html' =>TRUE));
			$editlink = l('Import', 'importpendapatan/importrka/' . $data->kodero . '/' . $kodeuk, array('html' =>TRUE));
		
            $no++;
            $rows[] = array (
				array('data' => '', 'align' => 'right'),
                array('data' => $no, 'align' => 'right'),
				array('data' => $data->kodero, 'align' => 'left'),
                array('data' => $data->uraian, 'align' => 'left'),
                array('data' => apbd_fn($data->jumlahp), 'align' => 'right'),
                array('data' => $data->ketrekening, 'align' => 'left'),
                array('data' => $editlink, 'align' => 'right'),
            );
        }
    } else {
        $rows[] = array (
            array('data' => 'Data kosong, tidak bisa menambahkan perubahan', 'colspan'=>'3')
        );
    }
	
	$output = theme_box('', theme_table($header, $rows));

	
    $output .= theme ('pager', NULL, $limit, 0);
    return $output;
}

function importpendapatan_filter_form() {

}

function importpendapatan_filter_form_submit($form, &$form_state) {
}
?>