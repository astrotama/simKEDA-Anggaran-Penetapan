<?php
function downloadr2_main($arg=NULL, $nama=NULL) {
	drupal_add_css('files/css/kegiatancam.css');
    if ($arg)
        if ($arg=='show') {
           $qlike = " and lower(topik) like lower('%%%s%%')";    
        }
        else
            drupal_access_denied();
		
	drupal_add_js('foo.js');
    $header = array (
        array('data' => 'No','width' => '10px', 'valign'=>'top'),
        array('data' => 'SKPD', 'field'=> 'topik', 'width' => '200px', 'valign'=>'top'),
		array('data' => 'File', 'field'=> 'uraian', 'width' => '300px', 'valign'=>'top'),
		array('data' => 'Download', 'width' => '100px', 'align'=>'center','valign'=>'top'),
		array('data' => 'OK ', 'width' => '100px', 'align'=>'center','valign'=>'top'),
    );
    $tablesort = tablesort_sql($header);
    if ($tablesort=='') {
        $tablesort=' order by file desc';
    }

	drupal_set_title('Download Revisi #2');
	$customwhere = ' ';
    $where = '';//' where true' . $customwhere . $qlike ;

    $sql = 'select kodeuk, file, ok from {downloadr2} ' . $where;
    $fsql = sprintf($sql, addslashes($nama));
    $limit = 15;
    //echo $fsql;
    $countsql = "select count(*) as cnt from {download} " . $where;
    $fcountsql = sprintf($countsql, addslashes($nama));
    $result = pager_query($fsql . $tablesort, $limit, 0, $fcountsql);
    
    $no=0;
    $page = $_GET['page'];
    if (isset($page)) {
        $no = $page * $limit;
    } else {
        $no = 0;
    }
	$index=0;
    if ($result) {
        while ($data = db_fetch_object($result)) {
			$index++;
			$editlink =l('Download','files/dppar2/'.$data->file.'.jpg', array('html'=>TRUE)) . '&nbsp;';
			$editlink2 ='<input type="checkbox" name="'.$data->file.'" value="ok" checked></input>';
			
            $no++;
            $rows[] = array (
                array('data' => $index, 'align' => 'right', 'valign'=>'top'),
                
				array('data' => $data->kodeuk, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data->file, 'align' => 'left', 'valign'=>'top'),
                array('data' => $editlink, 'align' => 'center', 'valign'=>'top'),
				array('data' => $editlink2, 'align' => 'center', 'valign'=>'top'),
            );
        }
    } else {
        $rows[] = array (
            array('data' => 'data kosong, silahkan menambahkan', 'colspan'=>'3')
        );
    }
	$btn = "";
	
	//if (user_access('urusan pencarian'))	{
	//	$btn .= l('Cari', 'apbd/download/find/', array ('html' => true, 'attributes'=> array ('class'=>'btn_blue', 'style'=>'color:white;')));
	//}
    $output .= $btn . theme_box('', theme_table($header, $rows)) . $btn;

    $output .= theme ('pager', NULL, $limit, 0);
	$output .= drupal_get_form('downloadr2_form');
    return $output;
	
	
}


function downloadr2_form() {
	$form['formtransfer'] = array (
		'#type' => 'fieldset',
		
		
	);
	$form['formtransfer']['simpan']= array(
		'#type'         => 'submit', 
		'#value'		=> 'Simpan',
		//'#attributes'	=> array('style' => 'margin-left: 20px;'),
	); 
	
	
	return $form;
}


?>