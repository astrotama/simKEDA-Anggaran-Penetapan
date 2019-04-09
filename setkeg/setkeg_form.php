<?php

function setkeg_form() {
	$form['setkeg']= array(
		'#type' => 'submit',
		'#value' => 'Resume Anggaran',
	);
	return $form;
}

function setkeg_form_submit($form, &$form_state) {
	$query = db_query("update kegiatanskpd inner join q_kegiatananggaran on kegiatanskpd.kodekeg=q_kegiatananggaran.kodekeg set kegiatanskpd.total=q_kegiatananggaran.total
where kegiatanskpd.inaktif=0");
   
   if($query){
	   drupal_set_message("Berhasil");
   }
}

?>
