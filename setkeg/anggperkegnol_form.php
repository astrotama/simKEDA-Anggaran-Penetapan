<?php

function anggperkegnol_main() {
	
	$header = array (
			array('data' => 'Kode','width' => '5px', 'valign'=>'top'),
			array('data' => 'Uraian', 'field'=> 'uraian', 'width' => '10px', 'valign'=>'top'),
			array('data' => 'Jumlah', 'field'=> 'jumlah','width' => '90px', 'valign'=>'top'), 

		);	
	
	$where = ' where jumlah=0';
	$pquery = sprintf(' select kodekeg, uraian, jumlah from anggperkeg' . $where);
	$pquery .= ' order by kodekeg';
	////drupal_set_message($pquery);
	$i = 0;
	$pres = db_query($pquery);
	while ($data = db_fetch_object($pres)) {
	$rows[] = array(
				array('data' => $data->kodekeg, 'width' => '10px', 'align' => 'left', 'valign'=>'top'),
				array('data' => $data->uraian, 'align' => 'left', 'valign'=>'top'),
				array('data' => $data->jumlah, 'align' => 'center', 'valign'=>'top'),
			);	
	}
	
	
	$output  = drupal_get_form('anggperkegnol_form');
	$output .= theme_box('', theme_table($header, $rows));	
    $output .= theme ('pager', NULL, $limit, 0);
	
	
	return  $output;
}

function anggperkegnol_form() {
	$form['tampilkan']= array(
		'#type' => 'submit',
		'#value' => 'Tampilkan',
	);
	$form['hapus']= array(
		'#type' => 'submit',
		'#value' => 'Hapus',
	);
	return $form;
}

function anggperkegnol_form_submit($form, &$form_state) {
	
	if($form_state['clicked_button']['#value'] == $form_state['values']['tampilkan']) {
		
		drupal_goto('anggperkegnol');
	
	}else {
	
		$query = db_query("delete from anggperkeg where jumlah=0");
		   
		   if($query){
			   drupal_set_message("Berhasil");
		   }
	   
	}	
		
}

?>
