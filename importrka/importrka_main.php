<?php
function importrka_main($arg=NULL, $nama=NULL) {
	drupal_add_css('files/css/kegiatancam.css');
 	drupal_add_js('files/js/kegiatanlt.js');
	
	$kodekeg = arg(2);

	$sql = 'select kegiatan,plafon from {kegiatanskpd} where kodekeg=\'%s\'';
	$res = db_query(db_rewrite_sql($sql), array ($kodekeg));
	if ($res) {
		$data = db_fetch_object($res);
		if ($data) {
			$kegiatan = $data->kegiatan;
			$plafon = $data->plafon;
		} 
	} 
	
	drupal_set_title('Pilih Kegiatan | ' . $kegiatan . ', Plafon : ' . apbd_fn($plafon) );
	if (isSuperuser())
		$kodeuk = '81';
	else
		$kodeuk = apbd_getuseruk();
	
	$header = array (
		array('data' => '', 'width' => '5px'),
		array('data' => 'No', 'width' => '10px'),
		array('data' => 'Kegiatan', 'field'=> 'kegiatan'),
		array('data' => 'Anggaran', 'field'=> 'totalp'),
		array('data' => 'Sumber Dana', 'field'=> 'sumberdana1'),
		array('data' => '', 'width' => '90px'),
	);
		
	$tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by kegiatan';
    }
	
	//Data
	$customwhere = " and kodeuk='" . $kodeuk . "' and kodekeg<>'" . $kodekeg . "'";
    $where = ' where total>0 and total<=' . $plafon . ' and isppkd=0 ' . $customwhere ;

    $sql = 'select kodekeg,kegiatan,total,sumberdana1 from {kegiatanskpd} ' . $where;
    $fsql = sprintf($sql, addslashes($nama));
	
	//drupal_set_message($sql);
	
    $limit = 30;
    $countsql = "select count(*) as cnt from {kegiatanskpd} " . $where;
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
			$editlink = l('Import', 'importrka/importrka/' . $kodekeg . '/' . $data->kodekeg, array('html' =>TRUE));
		
            $no++;
            $rows[] = array (
				array('data' => '', 'align' => 'right'),
                array('data' => $no, 'align' => 'right'),
                array('data' => $data->kegiatan, 'align' => 'left'),
                array('data' => apbd_fn($data->total), 'align' => 'right'),
                array('data' => $data->sumberdana1, 'align' => 'left'),
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

function importrka_filter_form() {

}

function importrka_filter_form_submit($form, &$form_state) {
}
?>