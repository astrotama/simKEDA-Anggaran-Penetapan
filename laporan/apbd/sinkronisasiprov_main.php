<?php
function sinkronisasiprov_main() { 
$h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
$h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
drupal_set_html_head($h);
drupal_add_css('files/css/kegiatancam.css');
	
	$prov = arg(4);
	$topmargin = arg(5);
	$hal1 = arg(6);
	$exportpdf = arg(7);

	if ($topmargin=='') $topmargin = 10;
	if ($hal1=='') $hal1 = 1;
	if ($prov=='1') $prov = 'prov';

	drupal_set_title('Cetak Sinkronisasi Prioritas Daerah');
	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		//require_once('test.php');
		//myt();
		
		$pdfFile = 'apbd-sinkronisasiprov.pdf';

		//$htmlContent = GenReportForm(1);
		//apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);

		$htmlHeader = GenReportFormHeader($prov);
		$htmlContent = GenReportFormContent($prov);
		$htmlFooter = GenReportFormFooter();
		
		apbd_ExportPDF3_CF_Long($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, false, $pdfFile, $hal1);
		
	} else {
		$output = drupal_get_form('sinkronisasiprov_form');
		//$output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
		$output .= GenReportForm($prov);
		return $output;
	}

}
function GenReportForm($prov) {

	if ($prov=='prov') {
		$strbidang = 'Provinsi';
	} else {
		$strbidang = 'Kabupaten';
	}	

	if ($prov=='prov') {
		$strbidang = 'Provinsi';
		$rowsjudul[] = array (array ('data'=>'Sinkronisasi Kebijakan Pemerintah Kabupaten Jepara dengan Prioritas Pembangunan Provinsi', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	} else {
		$strbidang = 'Kabupaten';
		$rowsjudul[] = array (array ('data'=>'Alokasi Anggaran Pemerintah Kabupaten Jepara berdasarkan Prioritas Daerah Tahun ' . apbd_tahun(), 'width'=>'875px', 'colspan'=>'7', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	}
	$rowsjudul[] = array (array ('data'=>'', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));		

	$headersrek[] = array (
						 
						 array('data' => 'No',  'width'=> '35px','rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Prioritas ' . $strbidang,  'width' => '465px','colspan'=>'2','rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Alokasi Anggaran Belanja dalam Rancangan APBD',  'width' => '200px', 'colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Jumlah',  'width' => '125px', 'rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),

						 );	
	$headersrek[] = array (

						 array('data' => 'Belanja Langsung',  'width' => '125px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'Belanja Tidak Langsung',  'width' => '125px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 );		

				
	//PRIORITASS	
	$sql = 'select distinct p.* from prioritas' . $prov . ' p inner join prioritasprogram' . $prov . ' pp on p.prioritasno=pp.prioritasno ' . $where . ' order by p.prioritasno';
	//drupal_set_message($sql);
	$resultu = db_query($sql);
	
	$nomor = 0;
	$tbl =0;
	$tbtl = 0;
	if ($resultu) {
		while ($datau = db_fetch_object($resultu)) {
			
			$unumbl = 0;
			$unumbtl = 0;
			
			$nomor++;
			
			$where = sprintf(' where k.inaktif=0 and p.prioritasno=\'%s\'', db_escape_string($datau->prioritasno));
			
			$sql = 'select k.jenis,sum(k.total) totalu from prioritas' . $prov . ' p inner join prioritasprogram' . $prov . ' pp on p.prioritasno=pp.prioritasno inner join kegiatanskpd k on pp.kodepro=k.kodepro ' . $where . ' group by k.jenis';
			$resunom = db_query($sql);	
			
			if ($resunom) 	{
				while ($dataunom = db_fetch_object($resunom)) {
					if ($dataunom->jenis==1)
						$unumbtl = $dataunom->totalu;
					else
						$unumbl = $dataunom->totalu;
				}
			}	

			$tbl += $unumbl;
			$tbtl += $unumbtl;
			
			$rowsrek[] = array (
								 array('data' => $nomor . '.',  'width'=> '35px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:center;'),
								 array('data' => $datau->uraian,  'width' => '465px','colspan'=>'2', 'style ' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => apbd_fn($unumbl),  'width' => '125px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($unumbtl),  'width' => '125px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($unumbl+$unumbtl),  'width' => '125px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
							);		
			
			$where = sprintf(' and k.inaktif=0 and pp.prioritasno=\'%s\'', db_escape_string($datau->prioritasno));

			//BTL
			$sql = 'select j.kodej, j.uraian, sum(agg.jumlah) totalbtl from anggperkeg agg inner join kegiatanskpd k on agg.kodekeg=k.kodekeg
			inner join prioritasprogram' . $prov . ' pp on k.kodepro=pp.kodepro
			inner join jenis j on left(agg.kodero,3)=j.kodej
			where k.jenis=1 ' . $where . ' group by j.kodej, j.uraian';
			$resbtl = db_query($sql);
			if ($resbtl) {
				while ($databtl = db_fetch_object($resbtl)) {
					$rowsrek[] = array (
										 array('data' => '',  'width'=> '35px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => '',  'width' => '20px', 'style' => ' text-align:center;'),
										 array('data' => ucfirst(strtolower($databtl->uraian)),  'width' => '445px', 'style' => ' border-right: 1px solid black; text-align:left;font-style: italic;'),
										 array('data' => '0',  'width' => '125px', 'style' => ' border-right: 1px solid black; text-align:right;'),
										 array('data' => apbd_fn($databtl->totalbtl),  'width' => '125px', 'style' => ' border-right: 1px solid black;  text-align:right;'),
										 array('data' => apbd_fn($databtl->totalbtl),  'width' => '125px', 'style' => ' border-right: 1px solid black;  text-align:right;'),
										 );						
				}
			}
			
			//BL
			$sql = 'select pro.kodepro, pro.program, sum(k.total) totalbl 
			from prioritas' . $prov . ' p inner join prioritasprogram' . $prov . ' pp on p.prioritasno=pp.prioritasno 
			inner join program pro on pp.kodepro=pro.kodepro 
			inner join kegiatanskpd k on pro.kodepro=k.kodepro  
			where k.jenis=2 ' . $where . ' group by pro.kodepro, pro.program';
			$resbl = db_query($sql);
			if ($resbl) {
				while ($databl = db_fetch_object($resbl)) {
					
					$rowsrek[] = array (
										 array('data' => '',  'width'=> '35px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => '-',  'width' => '20px', 'style' => ' text-align:center;'),
										 array('data' => $databl->program,  'width' => '445px', 'style' => ' border-right: 1px solid black; text-align:left;'),
										 array('data' => apbd_fn($databl->totalbl),  'width' => '125px', 'style' => ' border-right: 1px solid black; text-align:right;'),
										 array('data' => '0',  'width' => '125px', 'style' => ' border-right: 1px solid black;  text-align:right;'),
										 array('data' => apbd_fn($databl->totalbl),  'width' => '125px', 'style' => ' border-right: 1px solid black;  text-align:right;'),
										 );								
				}
			}
		
		}
	}

	$rowsrek[] = array (
						 array('data' => 'TOTAL',  'width'=> '500px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($tbl),  'width' =>  '125px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($tbtl),  'width' => '125px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($tbl+$tbtl),  'width' => '125px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 );
	
	$opttb0 = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0`');
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '1');
	$headerkosong = array();

	$output = theme_box('', apbd_theme_table($headerkosong, $rowslampiran, $opttb0));
	$output .= theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttb0));
	$output .= theme_box('', apbd_theme_table($headerkosong, $rowskegiatan, $opttb0));
	
	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	$output .= theme_box('', apbd_theme_table($headerkosong, $rowsfooter, $opttb0));
	
	$output .= $toutput;

	
	return $output;
	
}

function GenReportFormHeader($prov) {
	
	/*
	$rowsjudul[] = array (array ('data'=>'Sinkronisasi Kebijakan Pemerintah Kabupten Jepara dalam', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=>'Rancangan Peraturan Daerah tentang APBD Tahun Anggaran 2017 dan', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=>'Rancangan Peraturan Kepala Daerah tentang Penjabaran APBD Tahun Anggaran 2017', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	if ($prov=='prov') {
		$strbidang = 'Provinsi';
		$rowsjudul[] = array (array ('data'=>'dengan Prioritas Provinsi', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	} else {
		$strbidang = 'Nasional';
		$rowsjudul[] = array (array ('data'=>'dengan Prioritas Pembangunan Nasional', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	}
	*/
	if ($prov=='prov')
		$rowsjudul[] = array (array ('data'=>'Sinkronisasi Kebijakan Pemerintah Kabupaten Jepara dengan Prioritas Pembangunan Provinsi', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	else
		$rowsjudul[] = array (array ('data'=>'Alokasi Anggaran Pemerintah Kabupaten Jepara berdasarkan Prioritas Daerah Tahun ' . apbd_tahun(), 'width'=>'875px', 'colspan'=>'7', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));
	$rowsjudul[] = array (array ('data'=>'', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));	

	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	
	return $output;
	
}

function GenReportFormContent($prov) {

	if ($prov=='prov') {
		$strbidang = 'Provinsi';
	} else {
		$strbidang = 'Kabupaten';
	}
	
	$headersrek[] = array (
						 
						 array('data' => 'No',  'width'=> '35px','rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Prioritas ' . $strbidang,  'width' => '465px','colspan'=>'2','rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Alokasi Anggaran Belanja dalam Rancangan APBD',  'width' => '250px', 'colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Jumlah',  'width' => '125px', 'rowspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),

						 );	
	$headersrek[] = array (

						 array('data' => 'Belanja Langsung',  'width' => '125px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'Belanja Tidak Langsung',  'width' => '125px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 );		

				
	//PRIORITASS	
	$sql = 'select distinct p.* from prioritas' . $prov . ' p inner join prioritasprogram' . $prov . ' pp on p.prioritasno=pp.prioritasno ' . $where . ' order by p.prioritasno';
	//drupal_set_message($sql);
	$resultu = db_query($sql);
	
	$nomor = 0;
	$tbl =0;
	$tbtl = 0;
	if ($resultu) {
		while ($datau = db_fetch_object($resultu)) {
			
			$unumbl = 0;
			$unumbtl = 0;
			
			$nomor++;
			
			$where = sprintf(' where k.inaktif=0 and p.prioritasno=\'%s\'', db_escape_string($datau->prioritasno));
			
			$sql = 'select k.jenis,sum(k.total) totalu from prioritas' . $prov . ' p inner join prioritasprogram' . $prov . ' pp on p.prioritasno=pp.prioritasno inner join kegiatanskpd k on pp.kodepro=k.kodepro ' . $where . ' group by k.jenis';
			$resunom = db_query($sql);	
			
			if ($resunom) 	{
				while ($dataunom = db_fetch_object($resunom)) {
					if ($dataunom->jenis==1)
						$unumbtl = $dataunom->totalu;
					else
						$unumbl = $dataunom->totalu;
				}
			}	

			$tbl += $unumbl;
			$tbtl += $unumbtl;
			
			$rowsrek[] = array (
								 array('data' => $nomor . '.',  'width'=> '35px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:center;'),
								 array('data' => $datau->uraian,  'width' => '465px','colspan'=>'2', 'style ' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => apbd_fn($unumbl),  'width' => '125px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($unumbtl),  'width' => '125px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
								 array('data' => apbd_fn($unumbl+$unumbtl),  'width' => '125px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
							);		
			
			$where = sprintf(' and k.inaktif=0 and pp.prioritasno=\'%s\'', db_escape_string($datau->prioritasno));

			//BTL
			$sql = 'select j.kodej, j.uraian, sum(agg.jumlah) totalbtl from anggperkeg agg inner join kegiatanskpd k on agg.kodekeg=k.kodekeg
			inner join prioritasprogram' . $prov . ' pp on k.kodepro=pp.kodepro
			inner join jenis j on left(agg.kodero,3)=j.kodej
			where k.jenis=1 ' . $where . ' group by j.kodej, j.uraian';
			$resbtl = db_query($sql);
			if ($resbtl) {
				while ($databtl = db_fetch_object($resbtl)) {
					$rowsrek[] = array (
										 array('data' => '',  'width'=> '35px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => '',  'width' => '20px', 'style' => ' text-align:center;'),
										 array('data' => ucfirst(strtolower($databtl->uraian)),  'width' => '445px', 'style' => ' border-right: 1px solid black; text-align:left;font-style: italic;'),
										 array('data' => '0',  'width' => '125px', 'style' => ' border-right: 1px solid black; text-align:right;'),
										 array('data' => apbd_fn($databtl->totalbtl),  'width' => '125px', 'style' => ' border-right: 1px solid black;  text-align:right;'),
										 array('data' => apbd_fn($databtl->totalbtl),  'width' => '125px', 'style' => ' border-right: 1px solid black;  text-align:right;'),
										 );						
				}
			}
			
			//BL
			$sql = 'select pro.kodepro, pro.program, sum(k.total) totalbl 
			from prioritas' . $prov . ' p inner join prioritasprogram' . $prov . ' pp on p.prioritasno=pp.prioritasno 
			inner join program pro on pp.kodepro=pro.kodepro 
			inner join kegiatanskpd k on pro.kodepro=k.kodepro  
			where k.jenis=2 ' . $where . ' group by pro.kodepro, pro.program';
			$resbl = db_query($sql);
			if ($resbl) {
				while ($databl = db_fetch_object($resbl)) {
					
					$rowsrek[] = array (
										 array('data' => '',  'width'=> '35px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => '-',  'width' => '20px', 'style' => ' text-align:center;'),
										 array('data' => $databl->program,  'width' => '445px', 'style' => ' border-right: 1px solid black; text-align:left;'),
										 array('data' => apbd_fn($databl->totalbl),  'width' => '125px', 'style' => ' border-right: 1px solid black; text-align:right;'),
										 array('data' => '0',  'width' => '125px', 'style' => ' border-right: 1px solid black;  text-align:right;'),
										 array('data' => apbd_fn($databl->totalbl),  'width' => '125px', 'style' => ' border-right: 1px solid black;  text-align:right;'),
										 );								
				}
			}
		
		}
	}

	$rowsrek[] = array (
						 array('data' => 'TOTAL',  'width'=> '500px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black; border-bottom: 2px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($tbl),  'width' =>  '125px', 'style' => 'border-bottom: 2px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($tbtl),  'width' => '125px', 'style' => 'border-bottom: 2px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($tbl+$tbtl),  'width' => '125px', 'style' => 'border-bottom: 2px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:right; font-weight:bold;'),
						 );
	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output = theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));

	
	
	return $output;
	
}

function GenReportFormFooter() {
	
	
	$pimpinannama= 'Ir. SHOLIH, MM';
	$pimpinanjabatan= 'SEKRETARIS DAERAH';


	$rowsfooter[] = array (
						 array('data' => '',  'width'=> '635px',  'colspan'=>'2',  'style' => 'text-align:center'),
						 array('data' => '',  'width' => '200px', 'style' => 'text-align:center;'),
						 );
	$rowsfooter[] = array (
						 array('data' => '',  'width'=> '635px',  'colspan'=>'2',  'style' => 'text-align:center'),
						 array('data' => $pimpinanjabatan,  'width' => '200px', 'style' => 'text-align:center;'),
						 );
	$rowsfooter[] = array (
						 array('data' => '',  'width'=> '635px',  'colspan'=>'2',  'style' => 'text-align:center'),
						 array('data' => '',  'width' => '200px', 'style' => 'text-align:center;'),
						 );
	$rowsfooter[] = array (
						 array('data' => '',  'width'=> '635px',  'colspan'=>'2',  'style' => 'text-align:center'),
						 array('data' => '',  'width' => '200px', 'style' => 'text-align:center;'),
						 );
	$rowsfooter[] = array (
						 array('data' => '',  'width'=> '635px',  'colspan'=>'2',  'style' => 'text-align:center'),
						 array('data' => '',  'width' => '200px', 'style' => 'text-align:center;'),
						 );
	$rowsfooter[] = array (
						 array('data' => '',  'width'=> '635px',  'colspan'=>'2',  'style' => 'text-align:center'),
						 array('data' => $pimpinannama,  'width' => '200px', 'style' => 'text-align:center;'),
						 );

	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	$output = theme_box('', apbd_theme_table($headerkosong, $rowsfooter, $opttbl));	
	
	return $output;
}

function sinkronisasiprov_form () {
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Paramater Laporan dan Printer',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);
	
	$prov = arg(4);
	$topmargin = arg(5);
	$hal1 = arg(6);
	$exportpdf = arg(5);

	if ($topmargin=='') $topmargin = 10;
	if ($hal1=='') $hal1 = 1;
 
	$form['formdata']['prov']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $prov, 
	);
	$form['formdata']['topmargin']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Margin Atas', 
		'#attributes'	=> array('style' => 'text-align: right'),
		'#maxlength'    => 10, 
		'#size'         => 20, 
		//'#required'     => !$disabled, 
		'#disabled'     => false, 
		'#default_value'=> $topmargin, 
	);
	$form['formdata']['hal1']= array(
		'#type'         => 'textfield', 
		'#title'        => 'Halaman #1', 
		'#attributes'	=> array('style' => 'text-align: right'),
		'#description'  => 'Halaman #1 dari laporan, isikan 9999 bila menghendaki agar nomor halaman tidak muncul', 		
		'#maxlength'    => 10, 
		'#size'         => 20, 
		//'#required'     => !$disabled, 
		'#disabled'     => false, 
		'#default_value'=> $hal1, 
	);
	$form['formdata']['tampilkan'] = array (
		'#type' => 'submit',
		'#value' => 'Tampilkan',
	);	
	$form['formdata']['submit'] = array (
		'#type' => 'submit',
		'#value' => 'Cetak'
	);
	 
	return $form;
}

function sinkronisasiprov_form_submit($form, &$form_state) {
	$prov = $form_state['values']['prov'];
	$kodeuk = $form_state['values']['kodeuk'];
	$topmargin = $form_state['values']['topmargin'];
	$hal1 = $form_state['values']['hal1'];
	
	if($form_state['clicked_button']['#value'] == $form_state['values']['tampilkan']) 
		$uri = 'apbd/laporan/apbd/sinkronisasiprov/' . $prov . '/' . $topmargin . '/' . $hal1 ;
	else
		$uri = 'apbd/laporan/apbd/sinkronisasiprov/' . $prov . '/' . $topmargin . '/' . $hal1 . '/pdf' ;
	drupal_goto($uri);
	
}
?>