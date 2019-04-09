<?php
function kegiatanppa_main($arg=NULL, $nama=NULL) {
    $h = '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    drupal_set_html_head($h);
	drupal_add_css('files/css/kegiatancam.css');
	drupal_add_js('files/js/kegiatancam.js');
	$limit = 150;

	if ($arg)
	{
		if ($arg=='1') {
           $revisi = 1;
        }
		else if ($arg=='2')
		{
			$revisi = 2;
		}
        else
		{
			drupal_access_denied();
		}
	}
        
		
	$revisi = variable_get('apbdrevisi', 1);
	$periode = $revisi+1;
	//$revisi='';
	if($revisi=='9')
	{
		$revisi='';
	}
	else
	{
		$revisi=$revisi;
	}
	drupal_set_title('Penomoran DPA Revisi #' . $revisi);
	//$output .= drupal_get_form('kegiatanppa_transfer_form');
	$output .= drupal_get_form('kegiatanppa_main_form');
	$header = array (
		array('data' => 'No', 'width' => '10px', 'valign'=>'top'),
		array('data' => 'SKPD',  'valign'=>'top'),
		array('data' => 'PAD No', 'valign'=>'top'),
		array('data' => 'PAD Tgl', 'valign'=>'top'),
		array('data' => '', 'valign'=>'top'),
		array('data' => 'BTL No', 'valign'=>'top'),
		array('data' => 'BTL Tgl', 'valign'=>'top'),
		array('data' => '', 'valign'=>'top'),
		array('data' => 'BL No', 'valign'=>'top'),
		array('data' => 'BL Tgl', 'valign'=>'top'),
		array('data' => '', 'valign'=>'top'),
	);
	
	$tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by uk.kodedinas';
    }

	$pquery = "select uk.kodedinas, uk.kodeuk, uk.namasingkat, d.penno, d.pentgl, d.penok, d.btlno, d.btltgl, d.btlok, d.blno, d.bltgl, d.blok from {unitkerja} uk inner join {dpanomor".$revisi."} d on uk.kodeuk=d.kodeuk where uk.aktif=1 and uk.kodeuk in (select kodeuk from {kegiatanrevisiperubahan} where status=1)" ;
    //$fsql = sprintf($sql, addslashes($nama));
	$fsql = $pquery;
    //echo $fsql;
    $countsql = "select count(*) as cnt from {unitkerja} uk " ;
    //$fcountsql = sprintf($countsql, addslashes($nama));
	$fcountsql = $countsql;
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
			$padlink = '';
			$padno = '';
			$padtgl = '';
			
			$sql = 'select kodeuk from anggperuk where {kodeuk}=\'%s\'';
			$resuk = db_query(db_rewrite_sql($sql), array ($data->kodeuk));
			if ($resuk) {
				//drupal_set_message('res ok');
				if ($datauk = db_fetch_object($resuk)) {
					$padlink =l ('Nomor PAD', 'apbd/kegiatanppa/edit/' . $data->kodeuk . '/pen' , array('html'=>TRUE)) . '&nbsp;';
					
					$padno =  $data->penno;
					$padtgl =  $data->pentgl;
				}
			}

			//$padlink =l ('Nomor PAD', 'apbd/kegiatanppa/edit/' . $data->kodeuk . '/pen' , array('html'=>TRUE)) . '&nbsp;';
			$btllink =l ('Nomor BTL', 'apbd/kegiatanppa/edit/' . $data->kodeuk . '/btl' , array('html'=>TRUE)) . '&nbsp;';
			$bllink =l ('Nomor BL', 'apbd/kegiatanppa/edit/' . $data->kodeuk . '/bl' , array('html'=>TRUE)) . '&nbsp;';
				
            $no++;
			
				$rows[] = array (
					array('data' => $no, 'align' => 'right', 'valign'=>'top'),
					
					array('data' => $data->namasingkat, 'align' => 'left', 'valign'=>'top'),
					array('data' => $padno, 'align' => 'left', 'valign'=>'top'),
					array('data' => $padtgl, 'align' => 'left', 'valign'=>'top'),
					array('data' => $padlink, 'align' => 'right', 'valign'=>'top'),
					array('data' => $data->btlno, 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->btltgl, 'align' => 'left', 'valign'=>'top'),
					array('data' => $btllink, 'align' => 'right', 'valign'=>'top'),
					array('data' => $data->blno, 'align' => 'left', 'valign'=>'top'),
					array('data' => $data->bltgl, 'align' => 'left', 'valign'=>'top'),
					array('data' => $bllink, 'align' => 'right', 'valign'=>'top'),
					
				);
		}
    } else {

    }
	
	
    $output .= $btn . theme_box('', theme_table($header, $rows)) . $btn;
//    $output .= theme_box('', theme_table($header, $rows));
//	if (user_access('kegiatanppa tambah'))
//		$output .= l("<img src='/files/button-add.png' title='Tambah data baru'>", 'apbd/kegiatanppa/edit/' , array('html'=>TRUE)) ;
//	if (user_access('kegiatanppa pencarian'))		
//        $output .= l("<img src='/files/button-search.png' title='Pencarian data'>", 'apbd/kegiatanppa/find/' , array('html'=>TRUE)) ;
    $output .= theme ('pager', NULL, $limit, 0);
    return $output;
}


function kegiatanppa_main_form() {
}
function kegiatanppa_main_form_submit($form, &$form_state) {
	
}



?>
