<?php
function apbdbintanglist_main($arg=NULL, $nama=NULL) {	
	drupal_add_css('files/css/kegiatancam.css');
 	//drupal_add_js('files/js/kegiatanlt.js');

	if (arg(1)=='reset') {
		drupal_set_message('Rese');
		set_rek_bintang();
	}
	
	//dispensasirenja
	$header = array (
		array('data' => 'No', 'width' => '10px'),
		array('data' => 'Kode', 'width' => '50px'),
		array('data' => 'Uraian'),
		array('data' => 'Persen'),
		array('data' => 'Ditunda'),
		array('data' => ''),
	);
	
	$tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by r.kodero';
    }

    $fsql = 'select r.kodero, r.uraian, b.persen from {rekeningbintang} b inner join {rincianobyek} r on r.kodero=b.kodero ' . $where;
    //$fsql = sprintf($sql, addslashes($nama));
	
	//drupal_set_message($fsql);
	
    $limit = 50;
    $fcountsql = "select count(*) as cnt from {rekeningbintang} ";
    //$fcountsql = sprintf($countsql, addslashes($nama));
    $result = pager_query($fsql . $tablesort, $limit, 0, $fcountsql);
    
    $no=0;
    $page = $_GET['page'];
    if (isset($page)) {
        $no = $page * $limit;
    } else {
        $no = 0;
    }
	$total_tunda = 0;
    if ($result) {
        while ($data = db_fetch_object($result)) {

			//apbdbintang
			//	$uri = 'apbd/kegiatanskpdcari/filter/' . $kodeuk . '/' . $sumberdana . '/' . $statusisi . '/' . $kodesuk . '/'. $statustw . '/' . $statusinaktif . '/' . $jenis . '/' . $kegiatan . '/' . $rekening . '/' . $rincian ;
			$uraian = l($data->uraian, 'apbd/kegiatanskpdcari/filter/00////////' . $data->uraian, array('html'=>TRUE));
			
			$kodero = l($data->kodero, 'apbdbintang/' . $data->kodero, array('html'=>TRUE));
			$editlink = l('Hapus', 'apbdbintanglist/delete/' . $data->kodero, array('html'=>TRUE));

			$tunda = read_jumlah_tunda($data->kodero);
			$total_tunda += $tunda;
            $no++;
            $rows[] = array (
                array('data' => $no, 'align' => 'right'),
                array('data' => $kodero, 'align' => 'left'),
                array('data' => $uraian, 'align' => 'left'),
                array('data' => $data->persen, 'align' => 'right'),
				array('data' => apbd_fn($tunda), 'align' => 'right'),
                array('data' => $editlink, 'align' => 'right'),
            );
        }
    } 

	if ($no==0) {
        $rows[] = array (
            array('data' => 'data kosong, silahkan menambahkan', 'colspan'=>'5')
        );		
	}

	$rows[] = array (
		array('data' => '', 'align' => 'right'),
		array('data' => '', 'align' => 'left'),
		array('data' => 'TOTAL', 'align' => 'left'),
		array('data' => '', 'align' => 'right'),
		array('data' => apbd_fn($total_tunda), 'align' => 'right'),
		array('data' => '', 'align' => 'right'),
	);
	
	$btn = "";
	$btn .= l('Baru', 'apbdbintang', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) . "&nbsp;";
	$btn .= l('Reset', 'apbdbintanglist/reset', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;'))) . "&nbsp;";

	$output .= $btn . theme_box('', theme_table($header, $rows)) . $btn;

	
    $output .= theme ('pager', NULL, $limit, 0);
    return $output;
}

function read_jumlah_tunda($kodero) {
	$tunda = 0;
	$pquery = sprintf("select sum(total) totalx  from {anggperkegdetil} where total<0 and kodero='%s'", db_escape_string($kodero));
	$res = db_query($pquery);
	if ($data_b = db_fetch_object($res)) {
		$tunda = -$data_b->totalx;
	}	
	return $tunda;
}

?>