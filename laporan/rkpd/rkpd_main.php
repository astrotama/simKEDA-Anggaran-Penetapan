<?php
    function rkpd_main() {
    $h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
    $h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
    drupal_set_html_head($h);
    drupal_add_css('files/css/kegiatancam.css');
        
        $kodeuk = arg(3);
        $status = arg(4);
        $tahun = arg(5);
        $limit = arg(6);
        $exportpdf = arg(7);
        if (!isset($tahun)) 
            return drupal_get_form('rkpd_form');

        //if (isUserKecamatan()) {
        //    if ($kodeuk != apbd_getuseruk())
        //        return drupal_get_form('musrenbangcam_form');
        //}	

        if (isset($exportpdf) && ($exportpdf=='pdf'))  {
            //require_once('test.php');
            //myt();
            $htmlContent = GenReportForm(1);
            $pdfFile = 'rkpd.pdf';
            apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);
            
        } else {
            $url = 'apbd/laporan/rkpd/'. $kodeuk .'/'. $status . '/'. $tahun . "/0/pdf";
            $output .= drupal_get_form('rkpd_form');
            $output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
            $output .= GenReportForm();
            return $output;
        }

    }
    function GenReportForm($print=0) {
        
        $kodeuk = arg(3);
        $status = arg(4);
        $tahun = intval(arg(5));
        $limit = arg(6);
        $namauk = '';
        $pimpinannama='';
        $pimpinannip='';
        $pimpinanjabatan='';
        $pquery = sprintf("select kodeuk, namauk, pimpinannama, pimpinannip, pimpinanjabatan from {unitkerja} where kodeuk='%s'", db_escape_string($kodeuk)) ;
        $pres = db_query($pquery);
        if ($data = db_fetch_object($pres)) {
            $namauk = $data->namauk;
            $pimpinannama=$data->pimpinannama;
            $pimpinannip=$data->pimpinannip;
            $pimpinanjabatan=$data->pimpinanjabatan;
        }
        //---
        $col1 = '100px';
        $col2 = '275';	// '300px';
        $colstarget = '175px';
        $colplafon = '150px';
        $coltotal = '725px';	// '750px';
        
        if ($kodeuk=='00') {
            $col1 = '100px';
            $col2 = '175';		// '200px';
            $colstarget = '175px'; //2x
            $colplafon = '100px';
            $colpj = '150px';
            $coltotal = '625';		// '650px';
        }
        if ($kodeuk == '001') {
            $col1 = '100px';
            $col2 = '125px';	// '150px';
            $colstarget = '120px'; //2x
            $colplafon = '100px';
            
            $coltahun = '90px'; //2x
            $coltahung = '180px';
            
            $colpj = '130px';
            
            $coltotal = '465px';		// '490px';
            
        }

        
        $tablesort=' order by p.kodeu, p.np';
        $customwhere = ' and k.tahun=\'%s\' ';
        if (($kodeuk!='00') && ($kodeuk!='001')) {
            $customwhere .= ' and k.kodeuk=\'%s\' ';
        }
        switch($status) {
            case 0: //status = keseluruhan                
                break;
            case 1: //status = lolos
                $customwhere .= ' and k.lolos=1';
                break;
            case 2: //status = tidak lolos
                $customwhere .= ' and k.lolos=0';
                break;
        }
        $where = ' where true' . $customwhere . $qlike ;
    
        $sql = 'select d.namasingkat, d.kodedinas, p.kodeu, u.urusansingkat, p.program, p.np, k.kodekeg, k.nomorkeg, k.tahun, k.kodepro, k.kodeuk, k.kodeuktujuan, k.kegiatan, k.lokasi, k.sasaran, k.target, k.total, k.totalsebelum, k.totalsebelum2, k.totalpenetapan, k.apbdkab, k.pnpm, d.namasingkat from {kegiatanrkpd} k left join unitkerja d on(k.kodeuk=d.kodeuk) left join program p on (k.kodepro = p.kodepro) left join urusan u on (p.kodeu = u.kodeu)' . $where;
        $fsql = sprintf($sql, db_escape_string($tahun), db_escape_string($kodeuk));
        //$limit = 13;
        
        //drupal_set_message( $fsql);
        $countsql = "select count(*) as cnt from {kegiatanrkpd} k" . $where;
        $fcountsql = sprintf($countsql, db_escape_string($tahun), db_escape_string($kodeuk));
        if ($limit>0) {
            $result = pager_query($fsql . $tablesort, $limit, 0, $fcountsql);
        } else {
            $fsql .= ' ORDER BY p.kodeu, p.np, k.nomorkeg';
            $result = db_query($fsql);
        }
        
        $no=0;
        $page = $_GET['page'];
        if (isset($page)) {
            $no = $page * $limit;
        } else {
            $no = 0;
        }
        $kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
        $rows= array();
        $headers1[] = array (array ('data'=>'R.K.P.D ', 'width'=>'900px', 'colspan'=>'8', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
        $headers1[] = array (array ('data'=> $namauk . "&nbsp;" . $kabupaten , 'width'=>'900px', 'colspan'=>'8', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
        $headers1[] = array (array ('data'=> 'TAHUN ' . $tahun, 'width'=>'900px', 'colspan'=>'8', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
        $headers1[] = array (array ('data'=>'&nbsp;', 'colspan'=>'8', 'width'=>'900px', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
        $headers[] = array (
                                array('data' => 'KODE',  'width'=> $col1, 'style' => 'border: 1px solid black; text-align:center;'),
                                array('data' => 'USULAN KEGIATAN', 'width' => $col2, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
                                array('data' => 'SASARAN', 'width' => $colstarget, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
                                array('data' => 'TARGET', 'width' => $colstarget, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
                                array('data' => 'PLAFON ANGGARAN SEMENTARA (Rp)',  'width' => $colplafon, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;'),
                            );
        if ($kodeuk == '001') {
            $tdx = count($headers) - 1;
            $headers[$tdx][0]['rowspan'] = 2;
            $headers[$tdx][1]['rowspan'] = 2;
            $headers[$tdx][2]['rowspan'] = 2;
            $headers[$tdx][3]['rowspan'] = 2;
            $headers[$tdx][4]['rowspan'] = 2;
            $headers[$tdx][] = array('data' => 'ANGGARAN SEBELUMNYA',  'width' => $coltahung, 'colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;');
            $headers[$tdx][] = array('data' => 'SKPD PENANGGUNG JAWAB',  'width' => $colpj, 'rowspan'=> '2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;');
            //row berikutnya:
            $headers[] = array (
                array('data' => 'TAHUN ' . ($tahun-1) ,  'width' => $coltahun, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
                array('data' => 'TAHUN ' . ($tahun-2) ,  'width' => $coltahun, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
            );
        }

        if ($kodeuk=='00') {
            $tdx = count($headers) - 1;
            $headers[$tdx][] = array('data' => 'SKPD PENANGGUNG JAWAB',  'width' => $colpj, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:center;');
        }

        $headers[] = array (
                                array('data' => '1',  'width'=> $col1, 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
                                array('data' => '2', 'width' => $col2, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
                                array('data' => '3', 'width' => $colstarget, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
                                array('data' => '4', 'width' => $colstarget, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
                                array('data' => '5',  'width' => $colplafon, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
                            );

        if ($kodeuk=='00') {
            $tdx = count($headers) - 1;
            $headers[$tdx][] = array('data' => '6',  'width' => $colpj, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;');
        }
        if ($kodeuk=='001') {
            $tdx = count($headers) - 1;
            $headers[$tdx][] = array('data' => '6',  'width' => $coltahun, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;');
            $headers[$tdx][] = array('data' => '7',  'width' => $coltahun, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;');
            $headers[$tdx][] = array('data' => '8',  'width' => $colpj, 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;');
        }

        if ($result) {
            $u_array = array('URUSAN PADA SEMUA SKPD','URUSAN WAJIB','URUSAN PILIHAN');
            $pu=(double)0;
            $u ='';
            $u_nama='';
            $ju=(double)0;
            $ju_sebelum2=(double)0;
            $ju_sebelum=(double)0;
            
            $pu2=0;
            $u2='';
            $u2_nama='';
            $ju2=(double)0;
            $ju2_sebelum=(double)0;
            $ju2_sebelum2=(double)0;
            
            $pupro=0;
            $upro='';
            $upro_nama='';
            $jangthnusul=(double)0;
            $jangthnplus1=(double)0;
            $jangthnminus1=(double)0;
            
            $total = (double) 0;
            $totalsebelum = (double) 0;
            $totalsebelum2 = (double) 0;
            
            $first=true;
            
            $u_data = array();
            $u2_data = array();
            $u3_data = array();
            $temp_data = array();
            
            while ($data = db_fetch_object($result)) {                
                $no++;
                $r_u = substr($data->kodeu,0,1);
                $r_u2= $r_u . "." . substr($data->kodeu, 1,2);
                $r_upro= $r_u2 . "." . $data->np;
                //drupal_set_message($data->kegiatan);
                $total += (double) $data->total;
                $totalsebelum += (double) $data->totalsebelum;
                $totalsebelum2 += (double) $data->totalsebelum2;

                if ($first) {
                    $u = $r_u;
                    $u2 = $r_u2;
                    $upro = $r_upro;
                    $u_nama = $u_array[$u];                    
                    $u2_nama = $data->urusansingkat;
                    if ($u2=='0.00')
                        $u2_nama = 'URUSAN PADA SEMUA SKPD';
                    $upro_nama = $data->program;
                    
                    $ju = (double)$data->total;
                    $ju_sebelum = (double)$data->totalsebelum;
                    $ju_sebelum2 = (double)$data->totalsebelum2;

                    $ju2 = (double)$data->total;
                    $ju2_sebelum = (double)$data->totalsebelum;
                    $ju2_sebelum2 = (double)$data->totalsebelum2;

                    $jangthnusul = (double)$data->total;
                    $jangthnminus1 = (double)$data->totalsebelum;
                    $jangthnplus1 = (double)$data->totalsebelum2;
                    $first=false;
                } else {
                    if ($r_upro != $upro) {
                        //$tkode = $upro . "-" . $upro_nama;
                        $temp = array (
                            array('data' => $upro, 'width' => $col1, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:700;font-size: 1em;'),                            
                            array('data' => $upro_nama, 'width' => $col2, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => '' , 'width' => $colstarget, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => '', 'width' => $colstarget, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => apbd_fn($jangthnusul), 'width' => $colplafon, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                        );
                        if ($kodeuk=='00') {                            
                            $temp[] = array('data' => '', 'width' => $colpj, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;');
                        }
                        if ($kodeuk=='001') {
                            $temp[] = array('data' => apbd_fn($jangthnminus1), 'width' => $coltahun, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;');
                            $temp[] = array('data' => '', 'width' => $coltahun, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;');
                            $temp[] = array('data' => '', 'width' => $colpj, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;');
                        }
                        
                        array_unshift($temp_data, $temp);
                        $u3_data= array_merge($u3_data, $temp_data);
                        //
                        //array_unshift($temp_data, $temp);
                        //$u3_data[] = $temp_data;
                        $temp_data=array();
                        
                        $upro = $r_upro;
                        $upro_nama = $data->program;
                        $jangthnusul = (double) $data->total;
                        $jangthnplus1 = (double) $data->totalsebelum2;
                        $jangthnminus1 = (double) $data->totalsebelum;
                    } else {
                        $jangthnusul += (double) $data->total;
                        $jangthnplus1 += (double) $data->totalsebelum2;
                        $jangthnminus1 += (double) $data->totalsebelum;
                    }
                
                    
                    if ($u2 != $r_u2) {
                        $tkode = $u2 . '-' . $u2_nama;
                        $temp = array (
                            array('data' => $u2, 'width' => $col1, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => $u2_nama, 'width' => $col2, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => '' , 'width' => $colstarget, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => '', 'width' => $colstarget, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                            array('data' => apbd_fn($ju2), 'width' => $colplafon, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                        );
                        if ($kodeuk=='00') {                            
                            $temp[] = array('data' => '', 'width' => $colpj, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;');
                        }
                        if ($kodeuk=='001') {
                            $temp[] = array('data' => apbd_fn($ju2_sebelum2), 'width' => $coltahun, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;');
                            $temp[] = array('data' => '', 'width' => $coltahun, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;');
                            $temp[] = array('data' => '', 'width' => $colpj, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;');
                        }

                        array_unshift($u3_data, $temp);
                        $u2_data=array_merge($u2_data, $u3_data); 
                        $u3_data=array();
                        
                        $u2 = $r_u2;
                        $u2_nama = $data->urusansingkat;
                        if ($u2=='0.00')
                            $u2_nama = 'URUSAN PADA SEMUA SKPD';
                        
                        $ju2 = (double)$data->total;
                        $ju2_sebelum = (double) $data->totalsebelum;
                        $ju2_sebelum2 = (double) $data->totalsebelum2;
                    } else {
                        $ju2 += (double) $data->total;
                        $ju2_sebelum += (double) $data->totalsebelum;
                        $ju2_sebelum2 += (double) $data->totalsebelum2;
                    }

                    if ($u != $r_u) {
                        $tnama = $u_array[$u];
                        $temp = array (
                            array('data' => $u, 'width' => $col1, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-size: 1em;font-weight:900;'),
                            array('data' => $tnama, 'width' => $col2, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
                            array('data' => '', 'width' => $colstarget, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
                            array('data' => '', 'width' => $colstarget, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
                            array('data' => apbd_fn($ju), 'width' => $colplafon, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;font-weight:900;'),
                        );

                        if ($kodeuk=='00') {                            
                            $temp[] = array('data' => '', 'width' => $colpj, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;');
                        }
                        if ($kodeuk=='001') {
                            $temp[] = array('data' => apbd_fn($ju_sebelum), 'width' => $coltahun, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;');
                            $temp[] = array('data' => '', 'width' => $coltahun, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;');
                            $temp[] = array('data' => '', 'width' => $colpj, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;');
                        }
                        
                        array_unshift($u2_data, $temp);
                        $u_data= array_merge($u_data, $u2_data);
                        $u2_data=array();
                        
                        $u = $r_u;
                        $u_nama = $u_array[$u];
                        $ju = (double) $data->total;
                        $ju_sebelum = (double) $data->totalsebelum;
                        $ju_sebelum2 = (double) $data->totalsebelum2;
                    } else {
                        $ju += (double) $data->total;
                        $ju_sebelum += (double) $data->totalsebelum;
                        $ju_sebelum2 += (double) $data->totalsebelum2;
                    }

                }
                
                $tkode = $r_upro . "." .$data->kodedinas .'.' . $data->nomorkeg;
                
                $indikator = $data->sasaran . "/" . $data->target;

                $temp_data[] = array (
                    array('data' => $tkode, 'width' => $col1, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-size: 1em;'),                    
                    array('data' => $data->kegiatan , 'width' => $col2, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    array('data' => $data->sasaran, 'width' => $colstarget, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    array('data' => $data->target, 'width' => $colstarget, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                    array('data' => apbd_fn($data->total), 'width' => $colplafon, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size: 1em;'),
                );
                if ($kodeuk=='00') {
                    $tdx = count($temp_data) - 1;
                    $temp_data[$tdx][] = array('data' => $data->namasingkat, 'width' => $colpj, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;');
                }
                if ($kodeuk=='001') {
                    $tdx = count($temp_data) - 1;
                    $temp_data[$tdx][] = array('data' => apbd_fn($data->totalsebelum), 'width' => $coltahun, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;');
                    $temp_data[$tdx][] = array('data' => '', 'width' => $coltahun, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;');
                    $temp_data[$tdx][] = array('data' => $data->namasingkat, 'width' => $colpj, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;');
                }
                
            }
            
            if (count($temp_data)>0) {
                $tkode = $upro . "-" . $upro_nama;
                $temp = array (
                    array('data' => $upro, 'width' => $col1, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:700;font-size: 1em;'),                            
                    array('data' => $upro_nama, 'width' => $col2, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => '' , 'width' => $colstarget, 'align' => 'left', 'valign'=>'top', 'id' => 'aa', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => '', 'width' => $colstarget, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => apbd_fn($jangthnusul), 'width' => $colplafon, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                );

                if ($kodeuk=='00') {                            
                    $temp[] = array('data' => '', 'width' => $colpj, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;');
                }
                if ($kodeuk=='001') {
                    $temp[] = array('data' => apbd_fn($jangthnminus1), 'width' => $coltahun, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;');
                    $temp[] = array('data' => '', 'width' => $coltahun, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;');
                    $temp[] = array('data' => '', 'width' => $colpj, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;');
                }

                array_unshift($temp_data, $temp);
                $u3_data= array_merge($u3_data, $temp_data);
                //$u3_data[]= $temp_data;
                $tkode = $u2 . '-' . $u2_nama;

                $temp = array (
                    array('data' => $u2, 'width' => $col1, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-weight:700;font-size: 1em;'),                            
                    array('data' => $u2_nama, 'width' => $col2, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => '' , 'width' => $colstarget, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => '', 'width' => $colstarget, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                    array('data' => apbd_fn($ju2), 'width' => $colplafon, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;'),
                );

                if ($kodeuk=='00') {                            
                    $temp[] = array('data' => '', 'width' => $colpj, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;');
                }
                if ($kodeuk=='001') {
                    $temp[] = array('data' => apbd_fn($ju2_sebelum2), 'width' => $coltahun, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;');
                    $temp[] = array('data' => '', 'width' => $coltahun, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;');
                    $temp[] = array('data' => '', 'width' => $colpj, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;');
                }
                
                array_unshift($u3_data, $temp);
                $u2_data=array_merge($u2_data, $u3_data);
                
                $tnama = $u_array[$u];
                $temp = array (
                    array('data' => $u, 'width' => $col1, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-size: 1em;font-weight:900;'),                            
                    array('data' => $tnama , 'width' => $col2, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                    array('data' => '', 'width' => $colstarget, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                    array('data' => '', 'width' => $colstarget, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                    array('data' => apbd_fn($ju), 'width' => $colplafon, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                );

                if ($kodeuk=='00') {                            
                    $temp[] = array('data' => '', 'width' => $colpj, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;');
                }
                if ($kodeuk=='001') {
                    $temp[] = array('data' => apbd_fn($ju_sebelum), 'width' => $coltahun, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;');
                    $temp[] = array('data' => '', 'width' => $coltahun, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;');
                    $temp[] = array('data' => '', 'width' => $colpj, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;');
                }

                array_unshift($u2_data, $temp);
                $u_data= array_merge($u_data, $u2_data);
            }
            $rows = array_merge($rows, $u_data);
            
            if (count($rows) > 0) {
                //total
                $rows[] = array (
                    array('data' => 'Total', 'colspan'=>'4', 'width' => $coltotal, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-left: 1px solid black; font-size: 1em;font-weight:900;'),                            
                    array('data' => apbd_fn($total), 'width' => $colplafon, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:900;font-size: 1em;'),
                );
                if ($kodeuk=='00') {
                    $tdx = count($rows) - 1;
                    $rows[$tdx][] = array('data' => '', 'width' => $colpj, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;');
                }
                if ($kodeuk=='001') {
                    $tdx = count($rows) - 1;
                    $rows[$tdx][] = array('data' => apbd_fn($totalsebelum), 'width' => $coltahun, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;');
                    $rows[$tdx][] = array('data' => '', 'width' => $coltahun, 'align' => 'right', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;');
                    $rows[$tdx][] = array('data' => '', 'width' => $colpj, 'align' => 'left', 'valign'=>'top', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-weight:700;font-size: 1em;');
                }
                
            }
            
            
            
        } else {
            $rows[] = array (
                array('data' => 'data kosong, silahkan menambahkan', 'colspan'=>'9')
            );
        }
        
        $opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

		$rows1[] = array (array('data' => '', 'colspan'=>'2'));
		$output .= theme_box('', apbd_theme_table($headers1, $rows1, $opttbl));
		        
        if ($print) {
            //$opttbl['bgcolor'] = '#FF00FF';
            //for ($i=0; $i<count($headers); $i++) {
            //    for ($j=0;$j<count($headers[$i]); $j++) {
            //        if ($headers[$i][$j]['print'] )
            //            $headers[$i][$j]['style'] = $headers[$i][$j]['print'];
            //        else
            //            $headers[$i][$j]['style'] = "text-align: center;";
            //    }
            //}
            //
            //for ($i=0; $i<count($rows); $i++) {
            //    for ($j=0;$j<count($rows[$i]); $j++) {
            //        if ($rows[$i][$j]['print'])
            //            $rows[$i][$j]['style'] = $rows[$i][$j]['print'];
            //        else
            //            $rows[$i][$j]['style'] = '';
            //        
            //    }
            //}
        } 
        $toutput='';

        if (!isSuperuser()) {
                if ($print==0) {
                    $toutput = "        
                    <div style='clear:both'></div>
                    <div style='float:right; width:200px;border: 1px solid #eee'>
                        <div style='text-align:center;margin-bottom: 75px;'>" . $pimpinanjabatan . "</div>
                        <div style='text-align:center;text-decoration: underline;'>". $pimpinannama."</div>
                        <div style='text-align:center;'>NIP. ".$pimpinannip."</div>                        
                    </div>
                    <div style='clear:both'></div>
                    ";
                } else {
                    $rows[] = array (
                        array('data' => '', 'width' => '100px'),                    
                        array('data' => '', 'width' => '144px'),
                        array('data' => '', 'width' => '122px'),
                        array('data' => '', 'width' => '80px'),
                        array('data' => '', 'width' => '80px'),
                        array('data' => '', 'width' => '95px'),
                        array('data' => '', 'width' => '95px'),
                        array('data' => $pimpinanjabatan , 'width' => '182px', 'colspan'=>'2', 'height'=>'50px', 'style'=>'text-align:center'),
                    );
                    $rows[] = array (
                        array('data' => '', 'width' => '100px'),                    
                        array('data' => '', 'width' => '144px'),
                        array('data' => '', 'width' => '122px'),
                        array('data' => '', 'width' => '80px'),
                        array('data' => '', 'width' => '80px'),
                        array('data' => '', 'width' => '95px'),
                        array('data' => '', 'width' => '95px'),
                        array('data' => $pimpinannama , 'width' => '182px', 'colspan'=>'2', 'style'=>'text-decoration: underline;text-align:center'),
                    );
                    $rows[] = array (
                        array('data' => '', 'width' => '100px'),                    
                        array('data' => '', 'width' => '144px'),
                        array('data' => '', 'width' => '122px'),
                        array('data' => '', 'width' => '80px'),
                        array('data' => '', 'width' => '80px'),
                        array('data' => '', 'width' => '95px'),
                        array('data' => '', 'width' => '95px'),
                        array('data' => 'NIP.' . $pimpinannip , 'width' => '182px', 'colspan'=>'2', 'style'=>'text-align:center'),
                    );
                  
                }
        }
        
        $output .= theme_box('', apbd_theme_table($headers, $rows, $opttbl));
        $output .= $toutput;
        if ($limit >0)
            $output .= theme ('pager', NULL, $limit, 0);
        
        return $output;
        
    }
    
    function rkpd_form () {
        $form['formdata'] = array (
            '#type' => 'fieldset',
            '#title'=> 'Parameter Laporan',
            '#collapsible' => TRUE,
            '#collapsed' => FALSE,        
        );
        
        $kodeuk = arg(3);
        $status=arg(4);
        $tahun = arg(5);
        $limit = arg(6);

        if (isset($kodeuk)) {
            $form['formdata']['#collapsed'] = TRUE;
            //if (isUserKecamatan())
            //    if ($kodeuk != apbd_getuseruk())
            //        $form['formdata']['#collapsed'] = FALSE;
        }
        
      
        $pquery = "select kodeuk, namasingkat, namauk from {unitkerja} where aktif=1 order by namauk" ;
        $pres = db_query($pquery);
        $dinas = array();
        
        
        $dinas['00'] ='SEMUA SKPD/SELURUH KABUPATEN -- FORMAT 1';
        $dinas['001'] ='SEMUA SKPD/SELURUH KABUPATEN -- FORMAT 2';
        while ($data = db_fetch_object($pres)) {
            $dinas[$data->kodeuk] = $data->namauk;
        }
        $type='select';
        if (!isSuperuser()) {
            $type = 'hidden';
            $kodeuk = apbd_getuseruk();
            //drupal_set_message('user kec');
        }
        
        $form['formdata']['kodeuk']= array(
            '#type'         => $type, 
            '#title'        => 'SKPD',
            '#options'	=> $dinas,
            //'#description'  => 'kodeuktujuan', 
            //'#maxlength'    => 60, 
            '#width'         => 20, 
            //'#required'     => !$disabled, 
            //'#disabled'     => $disabled, 
            '#default_value'=> $kodeuk, 
        );
        $form['formdata']['status']= array(
            '#type'         => 'select', 
            '#title'        => 'Status',
            '#options'	=> array('Keseluruhan', 'Lolos', 'Tidak Lolos'),
            //'#description'  => 'kodeuktujuan', 
            //'#maxlength'    => 60, 
            '#width'         => 20, 
            //'#required'     => !$disabled, 
            //'#disabled'     => $disabled, 
            '#default_value'=> $status, 
        );
        //FILTER TAHUN-----
        $tahun = variable_get('apbdtahun', 0);
        $form['formdata']['tahun']= array(
            '#type'         => 'hidden', 
            '#title'        => 'Tahun',
            //'#description'  => 'kodeuktujuan', 
            //'#maxlength'    => 60, 
            '#width'         => 20, 
            //'#required'     => !$disabled, 
            //'#disabled'     => $disabled, 
            '#default_value'=> $tahun, 
        );
        $recordopt = array();
        $recordopt['0'] = 'Tampilkan semua';
        $recordopt['13'] = '13 Record/Halaman';
        $recordopt['26'] = '26 Record/Halaman';
        $form['formdata']['record']= array(
            '#type'         => 'select', 
            '#title'        => 'Record/Halaman',
            '#options'	=> $recordopt,
            //'#description'  => 'kodeuktujuan', 
            //'#maxlength'    => 60, 
            '#width'         => 20, 
            //'#required'     => !$disabled, 
            //'#disabled'     => $disabled, 
            '#default_value'=> $limit, 
        ); 
        $form['formdata']['submit'] = array (
            '#type' => 'submit',
            '#value' => 'Proses'
        );
        
        return $form;
    }
    function rkpd_form_submit($form, &$form_state) {
        //$kodeuk = $form_state['values']['kodeuk'];
        $tahun = $form_state['values']['tahun'];
        $record = $form_state['values']['record'];
        $kodeuk = $form_state['values']['kodeuk'];
        $status = $form_state['values']['status'];
        $uri = 'apbd/laporan/rkpd/' .$kodeuk .'/'. $status . '/' . $tahun . '/' . $record;
        drupal_goto($uri);
        
    }
?>