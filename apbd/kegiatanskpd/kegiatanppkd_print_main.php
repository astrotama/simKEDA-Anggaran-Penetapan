<?php
function kegiatanppkd_print_main() {
$h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
$h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
drupal_set_html_head($h);
drupal_add_css('files/css/kegiatancam.css');
	$tipedok = arg(3);
	$topmargin = arg(4);
	$halaman = arg(5);
	$exportpdf = arg(6);

	if ($topmargin=='') $topmargin = arg(5);
	if ($tipedok=='') $tipedok = 'rka';

	drupal_set_title(strtoupper($tipedok) . '-PPKD Belanja');	
	////drupal_set_message('Hai');
	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		//require_once('test.php');
		//myt();

		//VERIFIKATOR
		$sql = 'SELECT u.ttd FROM {userskpd} us inner join {apbdop} u on us.username=u.username where us.kodeuk=\'%s\'';
		$fsql = sprintf($sql, db_escape_string('00'));
		$link=array();
		$ind=0;
		//////drupal_set_message( $fsql);
		$res = db_query($fsql);
		if ($res) {
			while ($data = db_fetch_object($res)) {
				////drupal_set_message($data->ttd);
				$link[$ind]=$data->ttd;
				$ind++;
				
			}
		}

		$_SESSION["link_ttd1"] = $link[0];
		$_SESSION["link_ttd2"] = $link[1];
		$_SESSION["link_ttd3"] = $link[2];
		$_SESSION["link_ttd4"] = $link[3];		
		

		//$htmlContent = GenReportForm(1);
		//apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);

		
		if($halaman=='0'){
			$htmlHeader = GenReportFormHeader($tipedok);
			$htmlContent = GenReportFormContent($tipedok);
			$htmlFooter = GenReportFormFooter($tipedok);
			
			$pdfFile = $tipedok . '-ppkd-kegiatan.pdf';
			
			apbd_ExportPDF3t($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, true, $pdfFile,'http://2017.sipkdjepara.net/sites/default/files/download/6241685_std_2.jpg');		
		}
		else if($halaman=='1'){
			$htmlHeader = GenReportFormHeader($tipedok);
			$htmlContent = GenReportFormContent1($tipedok);
			$htmlFooter = '';
			
			$pdfFile = $tipedok . '-ppkd-kegiatan_1.pdf';
			
			apbd_ExportPDF3t($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, true, $pdfFile,'http://2017.sipkdjepara.net/sites/default/files/download/6241685_std_2.jpg');		
		}
		else if($halaman=='2'){
			$htmlHeader = '';
			$htmlContent = GenReportFormContent2($tipedok);
			$htmlFooter = '';
			
			$pdfFile = $tipedok . '-ppkd-kegiatan_2.pdf';
			
			$_SESSION["start"] = 2;
			apbd_ExportPDF3t($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, false, $pdfFile,'http://2017.sipkdjepara.net/sites/default/files/download/6241685_std_2.jpg');		
		} 
		else if($halaman=='3'){
			$htmlHeader = '';
			$htmlContent = GenReportFormContent3($tipedok);
			$htmlFooter = '';
			
			$pdfFile = $tipedok . '-ppkd-kegiatan_3.pdf';
			
			$_SESSION["start"] = 15;
			apbd_ExportPDF3t($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, false, $pdfFile,'http://2017.sipkdjepara.net/sites/default/files/download/6241685_std_2.jpg');		
		}
		else if($halaman=='4'){
			$htmlHeader = '';
			$htmlContent = GenReportFormContent4($tipedok);
			$htmlFooter = GenReportFormFooter($tipedok);
			
			$pdfFile = $tipedok . '-ppkd-kegiatan_4.pdf';
			
			$_SESSION["start"] = 53;
			apbd_ExportPDF3t($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, false, $pdfFile,'http://2017.sipkdjepara.net/sites/default/files/download/6241685_std_2.jpg');		
		}
		
		/*
		$htmlContent = GenReportFormContent($tipedok);
		$htmlFooter = GenReportFormFooter($tipedok);
		*/
		
		//apbd_ExportPDF3($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, $pdfFile);
		
	} else {
		//$url = 'apbd/kegiatanppkd/print/'. $kodeuk . '/' . $topmargin . "/pdf";
		$output = drupal_get_form('kegiatanppkd_print_form');
		//$output .= l('Cetak (PDF)', $url , array('html'=>true, 'attributes' => array('target' => "_blank", 'class' => 'btn_blue', 'style' => 'color: #ffffff;'))) ;
		
		//$output .= GenReportFormContent1($tipedok) . GenReportFormContent2($tipedok) . GenReportFormContent3($tipedok).GenReportFormContent4($tipedok);
		
		//$output .= GenReportFormHeader(1);
		$output .= GenReportFormContent($tipedok);
		//$output .= GenReportFormFooter();
		return $output;
	}

}

function GenReportFormHeader($tipedok ) {
	
	$skpd = '';
	$pimpinannama='';
	$pimpinannip='';
	$pimpinanjabatan='';
	
	$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
	$tahun = variable_get('apbdtahun', 0);
	$rows= array();
	
	if ($tipedok=='dpa') {
		$kodedinas = '12000';
		$urusan = '120 -  OTONOMI DAERAH, PEMERINTAHAN UMUM, ADMINISTRASI KEUANGAN DAERAH, PERANGKAT DAERAH, KEPEGAWAIAN DAN PERSANDIAN';
		$skpd = $kodedinas . ' - PEJABAT PENGELOLA KEUANGAN DAERAH';

		$pquery = sprintf("select dpatgl, setdanama, setdanip, setdajabatan from {setupapp} where tahun='%s'", variable_get('apbdtahun', 0)) ;
		//////drupal_set_message($pquery);
		$pres = db_query($pquery);
		
		
		if ($data = db_fetch_object($pres)) {
			$pimpinannama = $data->setdanama;
			$pimpinannip = $data->setdanip;
			$pimpinanjabatan = $data->setdajabatan;
			$dpatgl = '.........................';
		}	

		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'DOKUMEN PELAKSANAAN ANGGARAN', 'width' => '340px', 'colspan'=>'4', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'RINCIAN ANGGARAN PPKD', 'width' => '270px',  'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'FORMULIR', 'width' => '175',  'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1em; text-align:center;'),
							 );
		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'PEJABAT PENGELOLA KEUANGAN DAERAH', 'width' => '340px', 'colspan'=>'4', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'BELANJA TIDAK LANGSUNG', 'width' => '270px',  'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'DPA-PPKD 2.1', 'width' => '175',  'style' => 'border-right: 1px solid black; font-size:1em; text-align:center;'),
							 );
		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '340px', 'colspan'=>'4', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => '', 'width' => '270px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'TAHUN ' . $tahun, 'width' => '175',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size:1em; text-align:center;'),
							 );	
		$rowskegiatan[]= array (
							 array('data' => 'Urusan Pemerintahan',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
							 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
							 array('data' => $urusan, 'width' => '710', 'colspan'=>'5',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 );
		$rowskegiatan[]= array (
							 array('data' => 'Organisasi',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
							 array('data' => ':',  'width' => '15px', 'style' => 'text-align:right;'),
							 array('data' => $skpd,  'width' => '710', 'colspan'=>'5',  'style' => 'border-right: 1px solid black; text-align:left;'),					 
							);		
	} else {
		$pquery = sprintf("select '40400' kodedinas, 'PEJABAT PENGELOLA KEUANGAN DAERAH' namauk, uk.pimpinannama, uk.pimpinannip, 'BENDAHARA UMUM DAERAH' pimpinanjabatan, '404' kodeu, 'PEMERINTAHAN' urusan 
					from {unitkerja} uk inner join {urusan} u on uk.kodeu=u.kodeu 
					where uk.kodeuk='%s'", db_escape_string('81')) ;
		////drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$kodedinas = $data->kodedinas;
			$urusan = $data->kodeu . ' - ' . $data->urusan;
			$skpd = $kodedinas . ' - ' . $data->namauk;
			$pimpinannama=$data->pimpinannama;
			$pimpinannip=$data->pimpinannip;
			$pimpinanjabatan=$data->pimpinanjabatan;
		}


	  
		/*
		$rowskegiatan[]= array ( 
							 array('data' => 'PEMERINTAH KABUPATEN JEPARA',  'width'=> '250px', 'colspan'=>'3', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'RENCANA KERJA DAN ANGGARAN SATUAN KERJA PERANGKAT DAERAH', 'width' => '500px', 'colspan'=>'3', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => $tahun, 'width' => '125',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 );
		*/
		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'RENCANA KERJA DAN ANGGARAN', 'width' => '340px', 'colspan'=>'3', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'RINCIAN ANGGARAN PPKD', 'width' => '270px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'FORMULIR', 'width' => '175',  'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1em; text-align:center;'),
							 );
		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'PEJABAT PENGELOLA KEUANGAN DAERAH', 'width' => '340px', 'colspan'=>'3', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'BELANJA TIDAK LANGSUNG', 'width' => '270px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'RKA-PPKD 2.1', 'width' => '175',  'style' => 'border-right: 1px solid black; font-size:1em; text-align:center;'),
							 );
		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '340px', 'colspan'=>'3', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => '', 'width' => '270px', 'colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'TAHUN ' . $tahun, 'width' => '175',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size:1em; text-align:center;'),
							 );	
		$rowskegiatan[]= array (
							 array('data' => 'Urusan Pemerintahan',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
							 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
							 array('data' => $urusan, 'width' => '710', 'colspan'=>'5',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 );
		$rowskegiatan[]= array (
							 array('data' => 'Organisasi',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
							 array('data' => ':',  'width' => '15px', 'style' => 'text-align:right;'),
							 array('data' => $skpd,  'width' => '710', 'colspan'=>'5',  'style' => 'border-right: 1px solid black; text-align:left;'),					 
							);
	}
	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	$output = theme_box('', apbd_theme_table($headerkosong, $rowskegiatan, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormContent($tipedok) {

	
	set_time_limit(0);
	ini_set('memory_limit', '640M');

	$headersrek[] = array (
						 array('data' => 'Kode',  'width'=> '75px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 
						 array('data' => 'Uraian',  'width' => '400x','colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Satuan', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Volume', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Harga Satuan',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Total',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );

	
	$total = 0;
	$totalp = 0;
	
	////drupal_set_message('HaiX');
	
	//KELOMPOK
	$where = sprintf(' where keg.inaktif=0 and left(k.kodero,2)=\'%s\' and left(k.kodero,3)>\'%s\' ', db_escape_string('51'), db_escape_string('511'));
	$sql = 'select l.kodek,l.uraian,sum(k.jumlah) jumlahx from {anggperkeg} k inner join {kegiatanskpd} keg on k.kodekeg=keg.kodekeg  inner join {kelompok} l on mid(k.kodero,1,2)=l.kodek ' . $where;
	$sql .= ' group by l.kodek,l.uraian order by l.kodek';
	
	//echo $sql;
	
	$resultkel = db_query($sql);
	if ($resultkel) {
		while ($datakel = db_fetch_object($resultkel)) {
			$total += $datakel->jumlahx;
			$totalp += $datakel->jumlahxp;
			$rowsrek[] = array (
								 array('data' => $datakel->kodek,  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $datakel->uraian,  'width' => '400px','colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black;text-align:center;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black;text-align:center;'),
								 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black;text-align:center;'),
								 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-weight:bold;'),

								 );
			
			//JENIS	
			$where = sprintf(' where keg.inaktif=0 and left(k.kodero,2)=\'%s\' and left(k.kodero,2)=\'%s\' and left(k.kodero,3)>\'%s\' ',  
			db_escape_string($datakel->kodek), db_escape_string('51'), db_escape_string('511'));
			$sql = 'select mid(k.kodero,1,3) kodej,j.uraian,sum(jumlah) jumlahx from {anggperkeg} k  inner join {kegiatanskpd} keg on k.kodekeg=keg.kodekeg left join {jenis} j on mid(k.kodero,1,3)=j.kodej ' . $where;
			$sql .= ' group by mid(k.kodero,1,3),j.uraian order by mid(k.kodero,1,3)';
			
			////drupal_set_message( $sql);
			$resultjenis = db_query($sql);
			if ($resultjenis) {
				while ($datajenis = db_fetch_object($resultjenis)) {
					
					$rowsrek[] = array (
										 array('data' => ($datajenis->kodej),  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datajenis->uraian,  'width' => '400px', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;'),
										 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;'),
										 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;'),
										 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;'),
										 array('data' => apbd_fn($datajenis->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:right;font-weight:bold;'),


										 );
						
					//OBYEK
					$sql = 'select mid(k.kodero,1,5) kodeo,o.uraian,sum(k.jumlah) jumlahx from {anggperkeg} k  inner join {kegiatanskpd} keg on k.kodekeg=keg.kodekeg left join {obyek} o on mid(k.kodero,1,5)=o.kodeo where keg.inaktif=0 and k.jumlah>0 and mid(k.kodero,1,3)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($datajenis->kodej));
					$fsql .= ' group by mid(k.kodero,1,5),o.uraian order by mid(k.kodero,1,5)';
					
					////drupal_set_message( $fsql);
					$resultobyek = db_query($fsql);
					if ($resultobyek) {
						while ($dataobyek = db_fetch_object($resultobyek)) {
							$rowsrek[] = array (
												 array('data' => apbd_format_rek_obyek($dataobyek->kodeo),  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
												 array('data' => $dataobyek->uraian,  'width' => '400px', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;'),
												 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;'),
												 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;'),
												 array('data' => apbd_fn($dataobyek->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:right;'),


												 );		

							//REKENING
							$sql = 'select k.kodekeg,k.kodero,k.uraian,sum(k.jumlah) jumlahx  from {anggperkeg} k  inner join {kegiatanskpd} keg on k.kodekeg=keg.kodekeg where keg.inaktif=0 and k.jumlah>0 and mid(k.kodero,1,5)=\'%s\'';
							$fsql = sprintf($sql, db_escape_string($dataobyek->kodeo));
							$fsql .= ' group by kodero,uraian order by kodero';
							
							////drupal_set_message( $fsql);
							$result = db_query($fsql);
							if ($result) {
								while ($data = db_fetch_object($result)) {
								$rowsrek[] = array (
													 array('data' => apbd_format_rek_rincianobyek($data->kodero),  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
													 array('data' => $data->uraian,  'width' => '400px', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;'),
													 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
													 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
													 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
													 array('data' => apbd_fn($data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),


													 );
//SLASH													 
												 
									if ($datajenis->kodej<'516') {
										
										$penrekening = $data->jumlahx;
										
										//DETIL
										$sql = 'select iddetil,uraian,unitjumlah,unitsatuan,volumjumlah, volumsatuan,harga,total from {anggperkegdetil} where total>0 and kodero=\'%s\' order by total';
										$fsql = sprintf($sql, db_escape_string($data->kodero));
										////drupal_set_message($fsql);
										
										$resultsub = db_query($fsql);
										while ($datasub = db_fetch_object($resultsub)) {
											
											$rowsrek[] = array (
														 array('data' => '',  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
														 array('data' => '' ,  'width' => '20px',  'style' => 'text-align:center;font-weight:lighter;font-style: italic;'),
														 array('data' => '- ' . $datasub->uraian,  'width' => '380px',  'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;font-style: italic;'),
														 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
														 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
														 array('data' => apbd_fn($datasub->harga),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
														 array('data' => apbd_fn($datasub->total),  'width' => '100px', 'style' => ' border-right: 1px solid black;  text-align:right;font-weight:lighter;font-style: italic;'),

														 );
											
										}
									
									} else {
									
										//KEGIATAN
										$sql = 'select k.kodekeg,k.kegiatan,a.jumlah from {kegiatanskpd} k inner join {anggperkeg} a on k.kodekeg=a.kodekeg where k.inaktif=0 and a.jumlah>0 and a.kodero=\'%s\' order by a.jumlah';
										$fsql = sprintf($sql, db_escape_string($data->kodero));
										
										////drupal_set_message($fsql);
										
										$resultdetil = db_query($fsql);
										if ($resultdetil) {
											while ($datadetil = db_fetch_object($resultdetil)) {
												$rowsrek[] = array (
																	 array('data' => '',  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																	 array('data' => '-' ,  'width' => '20px', 'style' => 'text-align:center;font-weight:lighter;font-style: italic;'),
																	 array('data' => $datadetil->kegiatan,  'width' => '380px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;font-style: italic;'),
																	 array('data' => '1 keg', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
																	 array('data' => '1 kali', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
																	 array('data' => apbd_fn($datadetil->jumlah),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
																	 array('data' => apbd_fn($datadetil->jumlah),  'width' => '100px', 'style' => ' border-right: 1px solid black;  text-align:right;font-weight:lighter;font-style: italic;'),

																	 );
												
																	 
											}
										}
									}
//END SLASH									
								}
							}
												 
						////////
						}
					}
				}
			}	
		}	//KELOMPOK LOOPING
	}	//KELOMPOK

	$rowsrek[] = array (
						 array('data' => 'JUMLAH BELANJA',  'width'=> '475px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;border-top: 1px solid black;text-align:center;'),
						 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;'),
						 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;'),
						 array('data' => apbd_fn($total),  'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:right;font-weight:bold;'),

						 );
						 
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormContent1($tipedok) {

	
	set_time_limit(0);
	ini_set('memory_limit', '640M');

	$headersrek[] = array (
						 array('data' => 'Kode',  'width'=> '75px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 
						 array('data' => 'Uraian',  'width' => '400x','colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Satuan', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Volume', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Harga Satuan',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Total',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );

	
	$total = 0;
	$totalp = 0;
	
	////drupal_set_message('HaiX');
	
	//KELOMPOK
	$where = sprintf(' where keg.inaktif=0 and left(k.kodero,2)=\'%s\' and left(k.kodero,3)>\'%s\' ', db_escape_string('51'), db_escape_string('511'));
	$sql = 'select l.kodek,l.uraian,sum(k.jumlah) jumlahx from {anggperkeg} k inner join {kegiatanskpd} keg on k.kodekeg=keg.kodekeg  inner join {kelompok} l on mid(k.kodero,1,2)=l.kodek ' . $where;
	$sql .= ' group by l.kodek,l.uraian order by l.kodek';
	
	////drupal_set_message($sql);
	
	$resultkel = db_query($sql);
	if ($resultkel) {
		while ($datakel = db_fetch_object($resultkel)) {
			$total += $datakel->jumlahx;
			$totalp += $datakel->jumlahxp;
			$rowsrek[] = array (
								 array('data' => $datakel->kodek,  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $datakel->uraian,  'width' => '400px','colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:bold;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black;text-align:center;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black;text-align:center;'),
								 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black;text-align:center;'),
								 array('data' => apbd_fn($datakel->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-weight:bold;'),

								 );
			
			//JENIS	
			$where = sprintf(' where left(k.kodero,2)=\'%s\' and left(k.kodero,2)=\'%s\' and left(k.kodero,3)>\'%s\' ',  
			db_escape_string($datakel->kodek), db_escape_string('51'), db_escape_string('511'));
			$sql = 'select mid(k.kodero,1,3) kodej,j.uraian,sum(jumlah) jumlahx from {anggperkeg} k  left join {jenis} j on mid(k.kodero,1,3)=j.kodej ' . $where;
			$sql .= " and left(k.kodero,3)<'516' group by mid(k.kodero,1,3),j.uraian order by mid(k.kodero,1,3)";
			
			////drupal_set_message( $sql);
			$resultjenis = db_query($sql);
			if ($resultjenis) {
				while ($datajenis = db_fetch_object($resultjenis)) {
					
					$rowsrek[] = array (
										 array('data' => ($datajenis->kodej),  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $datajenis->uraian,  'width' => '400px', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;'),
										 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;'),
										 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;'),
										 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;'),
										 array('data' => apbd_fn($datajenis->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:right;font-weight:bold;'),


										 );
						
					//OBYEK
					$sql = 'select mid(k.kodero,1,5) kodeo,o.uraian,sum(k.jumlah) jumlahx from {anggperkeg} k  left join {obyek} o on mid(k.kodero,1,5)=o.kodeo where k.jumlah>0 and mid(k.kodero,1,3)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($datajenis->kodej));
					$fsql .= " and left(k.kodero,3)<'516' group by mid(k.kodero,1,5),o.uraian order by mid(k.kodero,1,5)";
					
					////drupal_set_message( $fsql);
					$resultobyek = db_query($fsql);
					if ($resultobyek) {
						while ($dataobyek = db_fetch_object($resultobyek)) {
							$rowsrek[] = array (
												 array('data' => apbd_format_rek_obyek($dataobyek->kodeo),  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
												 array('data' => $dataobyek->uraian,  'width' => '400px', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;'),
												 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;'),
												 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;'),
												 array('data' => apbd_fn($dataobyek->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:right;'),


												 );		

							//REKENING
							$sql = 'select kodero,uraian,sum(jumlah) jumlahx  from {anggperkeg} k where jumlah>0 and mid(k.kodero,1,5)=\'%s\'';
							$fsql = sprintf($sql, db_escape_string($dataobyek->kodeo));
							$fsql .= " and left(k.kodero,3)<'516' group by kodero,uraian order by kodero";
							
							////drupal_set_message( $fsql);
							$result = db_query($fsql);
							if ($result) {
								while ($data = db_fetch_object($result)) {
								$rowsrek[] = array (
													 array('data' => apbd_format_rek_rincianobyek($data->kodero),  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
													 array('data' => $data->uraian,  'width' => '400px', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;'),
													 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
													 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
													 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
													 array('data' => apbd_fn($data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),


													 );
//SLASH													 
												 
									if ($datajenis->kodej<'516') {
										
										$penrekening = $data->jumlahx;
										
										//DETIL
										$sql = 'select iddetil,uraian,unitjumlah,unitsatuan,volumjumlah, volumsatuan,harga,total from {anggperkegdetil} where total>0 and kodero=\'%s\' order by kodekeg,iddetil';
										$fsql = sprintf($sql, db_escape_string($data->kodero));
										////drupal_set_message($fsql);
										
										$resultsub = db_query($fsql);
										while ($datasub = db_fetch_object($resultsub)) {
											
											$rowsrek[] = array (
														 array('data' => '',  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
														 array('data' => '' ,  'width' => '20px',  'style' => 'text-align:center;font-weight:lighter;font-style: italic;'),
														 array('data' => '- ' . $datasub->uraian,  'width' => '380px',  'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;font-style: italic;'),
														 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
														 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
														 array('data' => apbd_fn($datasub->harga),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
														 array('data' => apbd_fn($datasub->total),  'width' => '100px', 'style' => ' border-right: 1px solid black;  text-align:right;font-weight:lighter;font-style: italic;'),

														 );
											
										}
									
									} else {
									
										//KEGIATAN
										$sql = 'select k.kodekeg,k.kegiatan,a.jumlah from {kegiatanskpd} k inner join {anggperkeg} a on k.kodekeg=a.kodekeg where k.inaktif=0 and a.jumlah>0 and  a.kodero=\'%s\' order by kegiatan';
										$fsql = sprintf($sql, db_escape_string($data->kodero));
										
										////drupal_set_message($fsql);
										
										$resultdetil = db_query($fsql);
										if ($resultdetil) {
											while ($datadetil = db_fetch_object($resultdetil)) {
												$rowsrek[] = array (
																	 array('data' => '',  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
																	 array('data' => '-' ,  'width' => '20px', 'style' => 'text-align:center;font-weight:lighter;font-style: italic;'),
																	 array('data' => $datadetil->kegiatan,  'width' => '380px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;font-style: italic;'),
																	 array('data' => '1 keg', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
																	 array('data' => '1 kali', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
																	 array('data' => apbd_fn($datadetil->jumlah),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
																	 array('data' => apbd_fn($datadetil->jumlah),  'width' => '100px', 'style' => ' border-right: 1px solid black;  text-align:right;font-weight:lighter;font-style: italic;'),

																	 );
												
																	 
											}
										}
									}
//END SLASH									
								}
							}
												 
						////////
						}
					}
				}
			}	
		}	//KELOMPOK LOOPING
	}	//KELOMPOK
	
	/*
	$rowsrek[] = array (
						 array('data' => 'JUMLAH BELANJA',  'width'=> '475px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;border-top: 1px solid black;text-align:center;'),
						 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;'),
						 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;'),
						 array('data' => apbd_fn($total),  'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:right;font-weight:bold;'),

						 );
	*/
	$rowsrek[] = array (
					array('data' => '',  'width'=> '881px',    'style' => 'border-top: 1px solid black;'),
				);
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormContent2($tipedok) {

	
	set_time_limit(0);
	ini_set('memory_limit', '640M');

	$headersrek[] = array (
						 array('data' => 'Kode',  'width'=> '75px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 
						 array('data' => 'Uraian',  'width' => '400x','colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Satuan', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Volume', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Harga Satuan',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Total',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );

	

	//JENIS	
	$where = sprintf(' where left(k.kodero,2)=\'%s\' and left(k.kodero,3)=\'%s\' ',  
	db_escape_string('51'), db_escape_string('516'));
	$sql = 'select mid(k.kodero,1,3) kodej,j.uraian,sum(jumlah) jumlahx from {anggperkeg} k  left join {jenis} j on mid(k.kodero,1,3)=j.kodej ' . $where;
	$sql .= " group by mid(k.kodero,1,3),j.uraian order by mid(k.kodero,1,3)";
	
	////drupal_set_message( $sql);
	$resultjenis = db_query($sql);
	if ($resultjenis) {
		while ($datajenis = db_fetch_object($resultjenis)) {
			
			$rowsrek[] = array (
								 array('data' => ($datajenis->kodej),  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $datajenis->uraian,  'width' => '400px', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;'),
								 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;'),
								 array('data' => apbd_fn($datajenis->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:right;font-weight:bold;'),


								 );
				
			//OBYEK
			$sql = 'select mid(k.kodero,1,5) kodeo,o.uraian,sum(k.jumlah) jumlahx from {anggperkeg} k  left join {obyek} o on mid(k.kodero,1,5)=o.kodeo where k.jumlah>0 and mid(k.kodero,1,3)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($datajenis->kodej));
			$fsql .= " and left(k.kodero,3)='516' group by mid(k.kodero,1,5),o.uraian order by mid(k.kodero,1,5)";
			
			////drupal_set_message( $fsql);
			$resultobyek = db_query($fsql);
			if ($resultobyek) {
				while ($dataobyek = db_fetch_object($resultobyek)) {
					$rowsrek[] = array (
										 array('data' => apbd_format_rek_obyek($dataobyek->kodeo),  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $dataobyek->uraian,  'width' => '400px', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;'),
										 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;'),
										 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;'),
										 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;'),
										 array('data' => apbd_fn($dataobyek->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:right;'),


										 );		

					//REKENING
					$sql = 'select kodero,uraian,sum(jumlah) jumlahx  from {anggperkeg} k where jumlah>0 and  mid(k.kodero,1,5)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($dataobyek->kodeo));
					$fsql .= " and left(k.kodero,3)='516' group by kodero,uraian order by kodero";
					
					////drupal_set_message( $fsql);
					$result = db_query($fsql);
					if ($result) {
						while ($data = db_fetch_object($result)) {
						$rowsrek[] = array (
											 array('data' => apbd_format_rek_rincianobyek($data->kodero),  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
											 array('data' => $data->uraian,  'width' => '400px', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;'),
											 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
											 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
											 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
											 array('data' => apbd_fn($data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),


											 );
										 
							
								//KEGIATAN
								$sql = 'select k.kodekeg,k.kegiatan,a.jumlah from {kegiatanskpd} k inner join {anggperkeg} a on k.kodekeg=a.kodekeg where k.inaktif=0 and a.jumlah>0 and a.kodero=\'%s\' order by kegiatan';
								$fsql = sprintf($sql, db_escape_string($data->kodero));
								
								////drupal_set_message($fsql);
								
								$resultdetil = db_query($fsql);
								if ($resultdetil) {
									while ($datadetil = db_fetch_object($resultdetil)) {
										$rowsrek[] = array (
															 array('data' => '',  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
															 array('data' => '-' ,  'width' => '20px', 'style' => 'text-align:center;font-weight:lighter;font-style: italic;'),
															 array('data' => $datadetil->kegiatan,  'width' => '380px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;font-style: italic;'),
															 array('data' => '1 keg', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
															 array('data' => '1 kali', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
															 array('data' => apbd_fn($datadetil->jumlah),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
															 array('data' => apbd_fn($datadetil->jumlah),  'width' => '100px', 'style' => ' border-right: 1px solid black;  text-align:right;font-weight:lighter;font-style: italic;'),

															 );
										
															 
									}
								}
						}
					}
										 
				////////
				}
			}
		}
	}	
	$rowsrek[] = array (
					array('data' => '',  'width'=> '881px',    'style' => 'border-top: 1px solid black;'),
				);
						 
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormContent3($tipedok) {

	
	set_time_limit(0);
	ini_set('memory_limit', '1024M');

	$headersrek[] = array (
						 array('data' => 'Kode',  'width'=> '75px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 
						 array('data' => 'Uraian',  'width' => '400x','colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Satuan', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Volume', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Harga Satuan',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Total',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );

	

	//JENIS	
	$where = sprintf(' where left(k.kodero,2)=\'%s\' and left(k.kodero,3)=\'%s\'',  
	db_escape_string('51'), db_escape_string('517'));
	$sql = 'select mid(k.kodero,1,3) kodej,j.uraian,sum(jumlah) jumlahx from {anggperkeg} k  left join {jenis} j on mid(k.kodero,1,3)=j.kodej ' . $where;
	$sql .= " group by mid(k.kodero,1,3),j.uraian order by mid(k.kodero,1,3)";
	
	//drupal_set_message( $sql);
	$resultjenis = db_query($sql);
	if ($resultjenis) {
		while ($datajenis = db_fetch_object($resultjenis)) {
			
			$rowsrek[] = array (
								 array('data' => ($datajenis->kodej),  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $datajenis->uraian,  'width' => '400px', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;'),
								 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;'),
								 array('data' => apbd_fn($datajenis->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:right;font-weight:bold;'),


								 );
				
			//OBYEK
			$sql = 'select mid(k.kodero,1,5) kodeo,o.uraian,sum(jumlah) jumlahx from {anggperkeg} k  left join {obyek} o on mid(k.kodero,1,5)=o.kodeo where mid(k.kodero,1,3)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($datajenis->kodej));
			$fsql .= " and left(k.kodero,3)='517' group by mid(k.kodero,1,5),o.uraian order by mid(k.kodero,1,5)";
			
			////drupal_set_message( $fsql);
			$resultobyek = db_query($fsql);
			if ($resultobyek) {
				while ($dataobyek = db_fetch_object($resultobyek)) {
					$rowsrek[] = array (
										 array('data' => apbd_format_rek_obyek($dataobyek->kodeo),  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $dataobyek->uraian,  'width' => '400px', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;'),
										 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;'),
										 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;'),
										 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;'),
										 array('data' => apbd_fn($dataobyek->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:right;'),


										 );		

					//REKENING
					$sql = 'select kodero,uraian,sum(jumlah) jumlahx  from {anggperkeg} k where mid(k.kodero,1,5)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($dataobyek->kodeo));
					$fsql .= " and left(k.kodero,3)='517' group by kodero,uraian order by kodero";
					
					////drupal_set_message( $fsql);
					$result = db_query($fsql);
					if ($result) {
						while ($data = db_fetch_object($result)) {
						$rowsrek[] = array (
											 array('data' => apbd_format_rek_rincianobyek($data->kodero),  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
											 array('data' => $data->uraian,  'width' => '400px', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;'),
											 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
											 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
											 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
											 array('data' => apbd_fn($data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),


											 );
//SLASH													 										 
							
								//KEGIATAN
								$sql = 'select k.kodekeg,k.kegiatan,a.jumlah from {kegiatanskpd} k inner join {anggperkeg} a on k.kodekeg=a.kodekeg where k.inaktif=0 and a.jumlah>0 and a.kodero=\'%s\' order by kegiatan';
								$fsql = sprintf($sql, db_escape_string($data->kodero));
								
								////drupal_set_message($fsql);
								
								$resultdetil = db_query($fsql);
								if ($resultdetil) {
									while ($datadetil = db_fetch_object($resultdetil)) {
										$rowsrek[] = array (
															 array('data' => '',  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
															 array('data' => '-' ,  'width' => '20px', 'style' => 'text-align:center;font-weight:lighter;font-style: italic;'),
															 array('data' => $datadetil->kegiatan,  'width' => '380px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;font-style: italic;'),
															 array('data' => '1 keg', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
															 array('data' => '1 kali', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
															 array('data' => apbd_fn($datadetil->jumlah),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
															 array('data' => apbd_fn($datadetil->jumlah),  'width' => '100px', 'style' => ' border-right: 1px solid black;  text-align:right;font-weight:lighter;font-style: italic;'),

															 );
										
															 
									}
								}
							
//END SLASH									
						}
					}
										 
				////////
				}
			}
		}
	}	

	$rowsrek[] = array (
					array('data' => '',  'width'=> '881px',    'style' => 'border-top: 1px solid black;'),
				);					 
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormContent4($tipedok) {

	
	set_time_limit(0);
	ini_set('memory_limit', '640M');

	$headersrek[] = array (
						 array('data' => 'Kode',  'width'=> '75px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 
						 array('data' => 'Uraian',  'width' => '400x','colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Satuan', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Volume', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Harga Satuan',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Total',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );

	

	//JENIS	
	$where = sprintf(' where left(k.kodero,2)=\'%s\' and left(k.kodero,3)>\'%s\' ',  
	db_escape_string('51'), db_escape_string('517'));
	$sql = 'select mid(k.kodero,1,3) kodej,j.uraian,sum(jumlah) jumlahx from {anggperkeg} k  left join {jenis} j on mid(k.kodero,1,3)=j.kodej ' . $where;
	$sql .= " group by mid(k.kodero,1,3),j.uraian order by mid(k.kodero,1,3)";
	
	//drupal_set_message( $sql);
	$resultjenis = db_query($sql);
	if ($resultjenis) {
		while ($datajenis = db_fetch_object($resultjenis)) {
			
			$rowsrek[] = array (
								 array('data' => ($datajenis->kodej),  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $datajenis->uraian,  'width' => '400px', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;'),
								 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;'),
								 array('data' => apbd_fn($datajenis->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:right;font-weight:bold;'),


								 );
				
			//OBYEK
			$sql = 'select mid(k.kodero,1,5) kodeo,o.uraian,sum(jumlah) jumlahx from {anggperkeg} k  left join {obyek} o on mid(k.kodero,1,5)=o.kodeo where mid(k.kodero,1,3)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($datajenis->kodej));
			$fsql .= " and left(k.kodero,3)>'517' group by mid(k.kodero,1,5),o.uraian order by mid(k.kodero,1,5)";
			
			////drupal_set_message( $fsql);
			$resultobyek = db_query($fsql);
			if ($resultobyek) {
				while ($dataobyek = db_fetch_object($resultobyek)) {
					$rowsrek[] = array (
										 array('data' => apbd_format_rek_obyek($dataobyek->kodeo),  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $dataobyek->uraian,  'width' => '400px', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;'),
										 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;'),
										 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;'),
										 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:center;'),
										 array('data' => apbd_fn($dataobyek->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black; text-align:right;'),


										 );		

					//REKENING
					$sql = 'select kodero,uraian,sum(jumlah) jumlahx  from {anggperkeg} k where mid(k.kodero,1,5)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($dataobyek->kodeo));
					$fsql .= " and left(k.kodero,3)>'517' group by kodero,uraian order by kodero";
					
					////drupal_set_message( $fsql);
					$result = db_query($fsql);
					if ($result) {
						while ($data = db_fetch_object($result)) {
						$rowsrek[] = array (
											 array('data' => apbd_format_rek_rincianobyek($data->kodero),  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
											 array('data' => $data->uraian,  'width' => '400px', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;'),
											 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
											 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
											 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
											 array('data' => apbd_fn($data->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),


											 );
//SLASH													 
										 
							if ($datajenis->kodej<'516') {
								
								$penrekening = $data->jumlahx;
								
								//DETIL
								$sql = 'select iddetil,uraian,unitjumlah,unitsatuan,volumjumlah, volumsatuan,harga,total from {anggperkegdetil} where kodero=\'%s\' order by kodekeg,iddetil';
								$fsql = sprintf($sql, db_escape_string($data->kodero));
								////drupal_set_message($fsql);
								
								$resultsub = db_query($fsql);
								while ($datasub = db_fetch_object($resultsub)) {
									
									$rowsrek[] = array (
												 array('data' => '',  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
												 array('data' => '' ,  'width' => '20px',  'style' => 'text-align:center;font-weight:lighter;font-style: italic;'),
												 array('data' => '- ' . $datasub->uraian,  'width' => '380px',  'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;font-style: italic;'),
												 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
												 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
												 array('data' => apbd_fn($datasub->harga),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
												 array('data' => apbd_fn($datasub->total),  'width' => '100px', 'style' => ' border-right: 1px solid black;  text-align:right;font-weight:lighter;font-style: italic;'),

												 );
									
								}
							
							} else {
							
								//KEGIATAN
								$sql = 'select k.kodekeg,k.kegiatan,a.jumlah from {kegiatanskpd} k inner join {anggperkeg} a on k.kodekeg=a.kodekeg where  k.inaktif=0 and a.jumlah>0 and a.kodero=\'%s\' order by kegiatan';
								$fsql = sprintf($sql, db_escape_string($data->kodero));
								
								////drupal_set_message($fsql);
								
								$resultdetil = db_query($fsql);
								if ($resultdetil) {
									while ($datadetil = db_fetch_object($resultdetil)) {
										$rowsrek[] = array (
															 array('data' => '',  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
															 array('data' => '-' ,  'width' => '20px', 'style' => 'text-align:center;font-weight:lighter;font-style: italic;'),
															 array('data' => $datadetil->kegiatan,  'width' => '380px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;font-style: italic;'),
															 array('data' => '1 keg', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
															 array('data' => '1 kali', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
															 array('data' => apbd_fn($datadetil->jumlah),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
															 array('data' => apbd_fn($datadetil->jumlah),  'width' => '100px', 'style' => ' border-right: 1px solid black;  text-align:right;font-weight:lighter;font-style: italic;'),

															 );
										
															 
									}
								}
							}
//END SLASH									
						}
					}
										 
				////////
				}
			}
		}
	}	

	//TOTAL
	$total = 0;
	$where = sprintf(' where keg.inaktif=0 and left(k.kodero,2)=\'%s\' and left(k.kodero,3)>\'%s\' ', db_escape_string('51'), db_escape_string('511'));
	$sql = 'select sum(k.jumlah) jumlahx from {anggperkeg} k inner join {kegiatanskpd} keg on k.kodekeg=keg.kodekeg' . $where;
	$result = db_query($sql);
	if ($result) {
		while ($data = db_fetch_object($result)) {	
			$total = $data->jumlahx;
		}
	}
	
	$rowsrek[] = array (
						 array('data' => 'JUMLAH BELANJA',  'width'=> '475px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;border-top: 1px solid black;text-align:center;'),
						 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;'),
						 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:center;'),
						 array('data' => apbd_fn($total),  'width' => '100px', 'style' => ' border-right: 1px solid black;border-bottom: 1px solid black;border-top: 1px solid black; text-align:right;font-weight:bold;'),

						 );
						 
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormFooter($tipedok) {
	
	$namauk = '';
	$pimpinannama='';
	$pimpinannip='';
	$pimpinanjabatan='';
	
	if ($tipedok=='dpa') {
		//$pquery = 'select sum(tw1p) tw1t,sum(tw2p) tw2t,sum(tw3p) tw3t,sum(tw4p) tw4t from {kegiatanperubahan} where isppkd=1';
		
	$pquery = "select sum(tw1) tw1t,sum(tw2) tw2t,sum(tw3) tw3t,sum(tw4) tw4t from {kegiatanskpd}
where total>0 and inaktif=0 and kodekeg in (select kodekeg from anggperkeg where left(kodero,3) between '512' and '518')";
		
		////drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$tw1 = $data->tw1t;
			$tw2 = $data->tw2t;
			$tw3 = $data->tw3t;
			$tw4 = $data->tw4t;
		}

		$pquery = sprintf("select dpatgl, budnama, budnip, budjabatan from {setupapp} where tahun='%s'", variable_get('apbdtahun', 0)) ;
		//////drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$pimpinannama = $data->budnama;
			$pimpinannip = $data->budnip;
			$pimpinanjabatan = $data->budjabatan;
			$dpatgl = $data->dpatgl;
		}
		
		$rowsfooter[] = array (
							 array('data' => 'RENCANA TRIWULAN',  'width'=> '475px',  'colspan'=>'5',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black;border-right: 1px solid black; text-align:center'),
							 array('data' => '',  'width'=> '100px',   'style' => 'text-align:center'),
							 array('data' => 'Jepara, ' . $dpatgl,  'width' => '300px', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'TRIWULAN',  'width'=> '275px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black;border-bottom: 1px solid black; text-align:center'),
							 array('data' => 'JUMLAH',  'width'=> '100px',   'style' => 'border-left: 1px solid black;border-bottom: 1px solid black; text-align:center'),
							 array('data' => 'KETERANGAN',  'width'=> '100px',   'style' => 'border-left: 1px solid black;border-bottom: 1px solid black; border-right: 1px solid black; text-align:center'),

							 array('data' => '',  'width'=> '100px',   'style' => 'text-align:center'),
							 array('data' => 'Mengesahkan,',  'width' => '300px', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'TRIWULAN I',  'width'=> '275px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black;  text-align:left'),
							 array('data' => apbd_fn($tw1),  'width'=> '100px',   'style' => 'border-left: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '100px',   'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),

							 array('data' => '',  'width'=> '100px',   'style' => 'text-align:center'),
							 array('data' => $pimpinanjabatan,  'width' => '300px', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'TRIWULAN II',  'width'=> '275px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black;  text-align:left'),
							 array('data' => apbd_fn($tw2),  'width'=> '100px',   'style' => 'border-left: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '100px',   'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),

							 array('data' => '',  'width'=> '100px',   'style' => 'text-align:center'),
							 array('data' => '',  'width' => '300px', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'TRIWULAN III',  'width'=> '275px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black;  text-align:left'),
							 array('data' => apbd_fn($tw3),  'width'=> '100px',   'style' => 'border-left: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '100px',   'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),

							 array('data' => '',  'width'=> '100px',   'style' => 'text-align:center'),
							 array('data' => '',  'width' => '300px', 'style' => 'border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'TRIWULAN IV',  'width'=> '275px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black;  text-align:left'),
							 array('data' => apbd_fn($tw4),  'width'=> '100px',   'style' => 'border-left: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '100px',   'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),

							 array('data' => '',  'width'=> '100px',   'style' => 'text-align:center'),
							 array('data' => $pimpinannama,  'width' => '300px', 'style' => 'border-right: 1px solid black; text-align:center;text-decoration: underline;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '275px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; text-align:left'),
							 array('data' => '',  'width'=> '100px',   'style' => 'border-left: 1px solid black; border-bottom: 1px solid black;text-align:right'),
							 array('data' => '',  'width'=> '100px',   'style' => 'border-left: 1px solid black; border-bottom: 1px solid black;border-right: 1px solid black; text-align:center'),

							 array('data' => '',  'width'=> '100px',   'style' => 'border-bottom: 1px solid black;text-align:center'),
							 array('data' => 'NIP. ' . $pimpinannip,  'width' => '300px', 'style' => 'border-right: 1px solid black; border-bottom: 1px solid black;text-align:center;'),
							 );

	 } else {
		$pquery = sprintf("select kodedinas, namauk, pimpinannama, pimpinannip, pimpinanjabatan 
					from {unitkerja} where kodeuk='%s'", db_escape_string('81')) ;
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			
			$namauk = $data->namauk;
			$pimpinannama=$data->pimpinannama;
			$pimpinannip=$data->pimpinannip;
			$pimpinanjabatan=$data->pimpinanjabatan;
		}

		$rowsfooter[] = array (
							 array('data' => 'CATATAN',  'width'=> '675px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => 'BENDAHARA UMUM DAERAH',  'width' => '200px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '675px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => '',  'width' => '200px', 'style' => 'border-right: 1px solid black; text-align:right;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '675px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => '',  'width' => '200px', 'style' => 'border-right: 1px solid black; text-align:right;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '675px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => $pimpinannama,  'width' => '200px', 'style' => 'border-right: 1px solid black; text-align:center; text-decoration: underline;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '675px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => 'NIP. ' . $pimpinannip,  'width' => '200px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
							 );
	}
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	$output = theme_box('', apbd_theme_table($headerkosong, $rowsfooter, $opttbl));
	
	$output .= $toutput;
	return $output;
	
}

function kegiatanppkd_print_form () {
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Setting Printer',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);
	
	$tipedok = arg(3);
	$topmargin = arg(4);
	if (!isset($topmargin)) $topmargin=10;

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
	$options=array(1,2,3,4);
	$form['formdata']['halaman']= array(
		'#type'         => 'select', 
		'#title'        => 'Halaman', 
		'#options'		=>$options,
		'#default_value'=> 0, 
	);
	$form['formdata']['tipedok'] = array (
		'#type' => 'value',
		'#value' => $tipedok
	);
	$form['formdata']['submit'] = array (
		'#type' => 'submit',
		'#value' => 'Cetak'
	);
	
	return $form;
}
function kegiatanppkd_print_form_submit($form, &$form_state) {
	$topmargin = $form_state['values']['topmargin'];
	$halaman = $form_state['values']['halaman']+1;
	$tipedok = $form_state['values']['tipedok'];
	$uri = 'apbd/kegiatanppkd/print/'.$tipedok.'/' . $topmargin .'/'.$halaman. '/pdf' ;
	drupal_goto($uri);
	
}
?>