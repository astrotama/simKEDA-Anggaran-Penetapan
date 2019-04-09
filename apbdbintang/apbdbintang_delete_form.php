<?php

function apbdbintang_delete_form() {
    drupal_add_css('files/css/kegiatancam.css');	
    drupal_add_js('files/js/common.js');
    drupal_add_js("$(document).ready(function(){ updateAnchorClass('.container-inline')});", 'inline');
    drupal_set_title('Data Rekening Bintang');
    $kodero = arg(2);
	
	drupal_set_message(arg(3));
	
    if (isset($kodero)) {
        $sql = 'select r.kodero, r.uraian, b.persen from {rekeningbintang} b inner join {rincianobyek} r on r.kodero=b.kodero where b.kodero=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array($kodero));
        if ($res) {
            $data = db_fetch_object($res);
            if ($data) {
                
                $form['formdata'] = array (
                    '#type' => 'fieldset',
                    '#title'=> 'Hapus Rekening Bintang',
                    '#collapsible' => TRUE,
                    '#collapsed' => FALSE,        
                );
                $form['formdata']['kodero'] = array('#type' => 'value', '#value' => $data->kodero);
                $form['formdata']['keterangan'] = array (
                            '#type' => 'markup',
                            '#value' => '<span>Rekening Bintang (' . $data->kodero . ' - ' . $data->uraian . ') </span>',
                            '#weight' => 1,
                            );
                
                return confirm_form($form,
                                    "Yakin menghapus data berikut ini ?",
                                    'apbdbintanglist',
                                    'Data yang dihapus tidak bisa dikembalikan lagi',
                                    'Hapus',
                                    'Batal');
            }
        }
    }
}
function apbdbintang_delete_form_validate($form, &$form_state) {
}
function apbdbintang_delete_form_submit($form, &$form_state) {
    if ($form_state['values']['confirm']) {
        $kodero = $form_state['values']['kodero'];
        $sql = 'DELETE FROM {rekeningbintang} WHERE kodero=\'%s\' ';
        $res = db_query(db_rewrite_sql($sql), array($kodero));

        $sql = 'DELETE FROM {anggperkegdetil} WHERE bintangjenis=1 and kodero=\'%s\'';
        $res = db_query(db_rewrite_sql($sql), array($kodero));

        if ($res) {
            drupal_set_message('Penghapusan berhasil dilakukan');
            drupal_goto('apbdbintanglist');
        }
        
    }
}
?>