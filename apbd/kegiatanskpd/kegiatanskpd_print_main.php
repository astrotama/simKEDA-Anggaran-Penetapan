<?php
function kegiatanskpd_print_main() {
$h ="<script>function PopupCenter(pageURL, title) {var left = 10;var top = 10;var targetWin = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+(screen.width-20)+', height='+(screen.height-20)+', top='+top+', left='+left);} </script>";
$h .= '<style>label{font-weight: bold; display: block; width: 150px; float: left;}</style>';
drupal_set_html_head($h);
drupal_add_css('files/css/kegiatancam.css');
	
	$topmargin = '20';
	$kodekeg = arg(3);
	$exportpdf = arg(6);
	$sampul = arg(7);
	$tipedok = arg(5);
	
	if(arg(8)!=null)
	{
		$ubahpdf="P";
		$ubahpdf2="perubahan ";
	}
	else
	{
		$ubahpdf="";
		$ubahpdf2="";
	}

	if (isset($topmargin)) $topmargin = arg(4);

	////drupal_set_message($kodekeg);
	if (isset($exportpdf) && ($exportpdf=='pdf'))  {
		if (isset($sampul))  {
			if ($sampul=='sampul') {
				$pdfFile = 'dpa-skpd-belanja-' . $kodekeg . '-sampul.pdf';
				$htmlContent = GenReportFormSampulBelanja($kodekeg, $ubahpdf2);
				apbd_ExportPDF_Sampul('L', 'F4', $htmlContent, $pdfFile);
			} else if ($sampul=='sampulp') {
				$pdfFile = 'dpa-skpd-pendapatan-sampul.pdf';
				$htmlContent = GenReportFormSampulPendapatan($kodekeg, $ubahpdf2);
				apbd_ExportPDF_Sampul('L', 'F4', $htmlContent, $pdfFile);
			} else {
				$pdfFile = 'dpa-skpd-sampul.pdf';
				$htmlContent = GenReportFormSampulDepan($kodekeg, $ubahpdf2, $ubahpdf);
				apbd_ExportPDF_Sampul('L', 'F4', $htmlContent, $pdfFile);
			}
			
		} else if($tipedok=='rka'){
			//require_once('test.php');
			//myt();
			
			$pdfFile = $tipedok . '-skpd-belanja-' . $kodekeg . '.pdf';

			//$htmlContent = GenReportForm(1);
			//apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);

			$htmlHeader = GenReportFormHeader($kodekeg, $tipedok);
			$htmlContent = GenReportFormContent($kodekeg);
			$htmlFooter = GenReportFormFooter($kodekeg, $tipedok);
			
			apbd_ExportPDF3tn($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, true, $pdfFile,'http://2017.sipkdjepara.net/sites/default/files/download/6241685_std_2.jpg');
			
			//apbd_ExportPDF3($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, true, $pdfFile,'http://2017.sipkdjepara.net/sites/default/files/download/6241685_std_2.jpg');
		} else {
			//require_once('test.php');
			//myt();
			
			$pdfFile = $tipedok . '-skpd-belanja-' . $kodekeg . '.pdf';

			//$htmlContent = GenReportForm(1);
			//apbd_ExportPDF('L', 'F4', $htmlContent, $pdfFile);

			$htmlHeader = GenReportFormHeader($kodekeg, $tipedok);
			$htmlContent = GenReportFormContent($kodekeg);
			$htmlFooter = GenReportFormFooter($kodekeg, $tipedok);
			
			
			$sql = 'SELECT u.ttd FROM {userskpd} us inner join {kegiatanskpd} k on us.kodeuk=k.kodeuk inner join {apbdop} u on us.username=u.username where k.kodekeg=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodekeg));
			$link=array();
			$ind=0; 
			//drupal_set_message( $fsql);
			$res = db_query($fsql);
			if ($res) {
				while ($data = db_fetch_object($res)) {
					
					//drupal_set_message($data->ttd);
					
					$link[$ind] = $data->ttd;
					$ind++;
					
				}
			}
			
			$_SESSION["link_ttd1"] = $link[0];
			$_SESSION["link_ttd2"] = $link[1];
			$_SESSION["link_ttd3"] = $link[2];
			$_SESSION["link_ttd4"] = $link[3];
			
			
			//NON TTD			
			//apbd_ExportPDF3($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, true, $pdfFile,'http://2017.sipkdjepara.net/sites/default/files/download/6241685_std_2.jpg');
			
			//TTD
			apbd_ExportPDF3t($topmargin,$topmargin, $htmlHeader, $htmlContent, $htmlFooter, true, $pdfFile,'http://2017.sipkdjepara.net/sites/default/files/download/6241685_std_2.jpg');
		}
		
	} else {
		
		$command = arg(4);
		if ($command == 'excel') {
			GenExcel($kodekeg);
			return null;
		} else if ($command == 'csv') {
			GenCSV($kodekeg);
			return null;
		} else {
			$output = drupal_get_form('kegiatanskpd_print_form');

			$htmlHeader = GenReportFormHeader($kodekeg, $tipedok);
			$htmlContent = GenReportFormContent($kodekeg);
			$htmlFooter = GenReportFormFooter($kodekeg, $tipedok);
			 
			//$output .= GenReportForm($kodekeg, $tipedok);
			$output .= $htmlHeader . $htmlContent . $htmlFooter;
			return $output;
		}
	}

}

function GenReportFormSampulBelanja($kodekeg, $ubahpdf2) {
	$where = ' where k.kodekeg=\'%s\'';
	$pquery = sprintf('select k.kodekeg,  right(k.kodekeg,3) nomorkeg, k.tahun, k.kodepro, k.kodeuk, k.kodesuk, 
				k.kegiatan, k.lokasi, k.jenis, k.total, 
				k.sumberdana1, k.sumberdana2, k.sumberdana1rp, k.sumberdana2rp, p.program,
				p.kodepro, p.kodeu, u.urusan, u.fungsi, u.kodef, uk.kodedinas, 
				uk.namauk, uk.pimpinannama, uk.pimpinanjabatan, uk.pimpinannip 
				from {kegiatanskpd} k left join {program} p on (k.kodepro = p.kodepro) 
				left join {urusan} u on p.kodeu=u.kodeu left join {unitkerja} uk on k.kodeuk=uk.kodeuk ' . $where, db_escape_string($kodekeg));
	////drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$kodeuk = $data->kodeuk;
		$fungsi = $data->kodef . ' - ' . $data->fungsi;
		$urusan = $data->kodeu . ' - ' . $data->urusan;
		$organisasi = $data->kodedinas . ' - ' . $data->namauk;
		$program = $data->kodepro . ' - ' . $data->program;
		$kegiatan = $data->kodedinas . '.' . $data->kodepro . '.' . $data->nomorkeg . ' - ' .  $data->kegiatan;
		
		$kodekeg = $data->kodedinas . '.' . $data->kodepro . '.' . $data->nomorkeg;
		
		$lokasi = str_replace('||',', ', $data->lokasi);
		$total = $data->total;
		$waktupelaksanaan = $data->waktupelaksanaan;
		$sumberdana1 = $data->sumberdana1;

		$pimpinannama=$data->pimpinannama;
		$pimpinannip=$data->pimpinannip;
		$pimpinanjabatan=$data->pimpinanjabatan;
		
		$jenis = $data->jenis;
		if ($data->jenis==1) $strjenis = '  -  T I D A K';
	}
	
	if ($jenis==1)
		$pquery = sprintf('select btlno dpano from {dpanomor} where kodeuk=\'%s\'' , db_escape_string($kodeuk));
	else
		$pquery = sprintf('select blno dpano from {dpanomor} where kodeuk=\'%s\'' , db_escape_string($kodeuk));
	////drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$dpano = $data->dpano;
	}
	
	if ($dpano !='') {
		$tahun = variable_get('apbdtahun', 0);
		
		if ($jenis==1)
			$pquery = sprintf('select dpabtlformat dpaformat from {setupapp} where tahun=\'%s\'', db_escape_string($tahun));
		else
			$pquery = sprintf('select dpablformat dpaformat from {setupapp} where tahun=\'%s\'', db_escape_string($tahun));
			
		////drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($pres) {
			if ($data = db_fetch_object($pres)) {
				$dpaformat = $data->dpaformat;
			}
		}
		
		$dpanolengkap = str_replace('NNN',$dpano,$dpaformat);
		$dpanolengkap = str_replace('NOKEG',$kodekeg,$dpanolengkap);
		
	} else 
		$dpanolengkap = '........................';
	
	$rows[] = array (
				array('data' => '',  'width'=> '50px', 'style' => 'border-none;'),
				array ('data'=>'', 'width'=>'825px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'));	
	$rows[] = array (
				array('data' => '',  'width'=> '50px', 'style' => 'border-none;'),
				array ('data'=>'KABUPATEN JEPARA', 'width'=>'825px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'));	
	$rows[] = array (
				array('data' => '',  'width'=> '50px', 'style' => 'border-none;'),
				array ('data'=>'DOKUMEN PELAKSANAAN '.strtoupper($ubahpdf2).'ANGGARAN', 'width'=>'825px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.75em; text-align:center;'));	
	$rows[] = array (
				array('data' => '',  'width'=> '50px', 'style' => 'border-none;'),
				array ('data'=>'SATUAN KERJA PERANGKAT DAERAH (DPA-SKPD)', 'width'=>'825px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.75em; text-align:center;'));	
	$rows[] = array (array('data' => '',  'width'=> '50px', 'style' => 'border-none;'),
	array ('data'=>'TAHUN ANGGARAN ' . apbd_tahun(), 'width'=>'825px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'));	
	$rows[] = array (array('data' => '',  'width'=> '50px', 'style' => 'border-none;'),
	array ('data'=>'B E L A N J A' . $strjenis . '  -  L A N G S U N G', 'width'=>'825px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.75em; text-align:center;'));	
	$rows[] = array (array('data' => '',  'width'=> '50px', 'style' => 'border-none;'),
	array ('data'=>'NO. DPA-SKPD : ' . $dpanolengkap, 'width'=>'825px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'));	

	$rows[]= array (
				array('data' => '',  'width'=> '175px', 'style' => 'border:none;font-weight:900; font-size:1em; text-align:left;'),
				array('data' => '', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => '', 'width' => '685px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => '',  'width'=> '25px', 'style' => 'border:none;font-weight:900; font-size:1em; text-align:left;'),
				array('data' => 'URUSAN PEMERINTAHAN',  'width'=> '150px', 'style' => 'border:none;font-weight:900; font-size:1em; text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => $urusan, 'width' => '685px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => '',  'width'=> '25px', 'style' => 'border:none;font-weight:900; font-size:1em; text-align:left;'),
				array('data' => 'ORGANISASI',  'width'=> '150px', 'style' => 'border:none;font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => $organisasi, 'width' => '685px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => '',  'width'=> '25px', 'style' => 'border:none;font-weight:900; font-size:1em; text-align:left;'),
				array('data' => 'PROGRAM',  'width'=> '150px', 'style' => 'border:none;font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => strtoupper($program), 'width' => '685px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => '',  'width'=> '25px', 'style' => 'border:none;font-weight:900; font-size:1em; text-align:left;'),
				array('data' => 'KEGIATAN',  'width'=> '150px', 'style' => 'border:none;font-weight:900; font-size:1em; text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => strtoupper($kegiatan), 'width' => '685px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	if ($jenis==2)
		$rows[]= array (
					array('data' => '',  'width'=> '25px', 'style' => 'border:none;font-weight:900; font-size:1em; text-align:left;'),
					array('data' => 'LOKASI',  'width'=> '150px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
					array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:right;'),
					array('data' => strtoupper($lokasi), 'width' => '685px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
				);
	$rows[]= array (
				array('data' => '',  'width'=> '25px', 'style' => 'border:none;font-weight:900; font-size:1em; text-align:left;'),
				array('data' => 'SUMBER DANA',  'width'=> '150px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => $sumberdana1, 'width' => '685px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
			);
	$rows[]= array (
				array('data' => '',  'width'=> '25px', 'style' => 'border:none;font-weight:900; font-size:1em; text-align:left;'),
				array('data' => 'JUMLAH ANGGARAN',  'width'=> '150px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => 'Rp ' . apbd_fn($total) . ',00', 'width' => '685px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => '',  'width'=> '25px', 'style' => 'border:none;font-weight:900; font-size:1em; text-align:left;'),
				array('data' => 'TERBILANG',  'width'=> '150px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => strtoupper(apbd_terbilang($total)), 'width' => '685px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);

	$rows[]= array (
				array('data' => '',  'width'=> '25px', 'style' => 'border:none;font-weight:900; font-size:1em; text-align:left;'),
				array('data' => 'PENGGUNA ANGGARAN',  'width'=> '150px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:right;'),
				array('data' => '', 'width' => '685px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => '',  'width'=> '25px', 'style' => 'border:none;font-weight:900; font-size:1em; text-align:left;'),
				array('data' => '- NAMA',  'width'=> '150px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => $pimpinannama, 'width' => '685px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => '',  'width'=> '25px', 'style' => 'border:none;font-weight:900; font-size:1em; text-align:left;'),
				array('data' => '- NIP',  'width'=> '150px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => $pimpinannip, 'width' => '685px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => '',  'width'=> '25px', 'style' => 'border:none;font-weight:900; font-size:1em; text-align:left;'),
				array('data' => '- JABATAN',  'width'=> '150px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => $pimpinanjabatan, 'width' => '685px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);

	$opttb0 = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0`');
	$headerkosong = array();

	$output = theme_box('', apbd_theme_table($headerkosong, $rows, $opttb0));
	
	return $output;
			
}

function GenReportFormSampulPendapatan($kodeuk, $ubahpdf2) {
	$where = ' where uk.kodeuk=\'%s\' and left(uk.kodero,2)=\'%s\'';
	$pquery = sprintf('select sum(uk.jumlah) total from {anggperuk} uk ' . $where, db_escape_string($kodeuk), db_escape_string('41'));  
	////drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$total = $data->total;
	}
	$where = ' where uk.kodeuk=\'%s\'';
	$pquery = sprintf('select u.kodeu, u.urusan, uk.kodedinas, uk.namauk, uk.pimpinannama, uk.pimpinanjabatan, uk.pimpinannip from {unitkerja} uk inner join {urusan} u on uk.kodeu=u.kodeu ' . $where, db_escape_string($kodeuk));
	////drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$kodedinas = $data->kodedinas;
		$urusan = $data->kodeu . ' - ' . $data->urusan;
		$organisasi = $data->kodedinas . ' - ' . $data->namauk;
		$pimpinannama = $data->pimpinannama;
		$pimpinannip = $data->pimpinannip;
		$pimpinanjabatan = $data->pimpinanjabatan;	
	}
	
	$pquery = sprintf('select penno dpano from {dpanomor} uk ' . $where, db_escape_string($kodeuk));
	////drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$dpano = $data->dpano;
	}
	
	if ($dpano !='') {
		$tahun = variable_get('apbdtahun', 0);
		
		$pquery = sprintf('select dpapenformat dpaformat from {setupapp} where tahun=\'%s\'', db_escape_string($tahun));
		////drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($pres) {
			if ($data = db_fetch_object($pres)) {
				$dpaformat = $data->dpaformat;
			}
		}
		
		$dpanolengkap = str_replace('NNN',$dpano,$dpaformat);
		$dpanolengkap = str_replace('NOKEG',$kodedinas . '.000.004',$dpanolengkap);
		
	} else 
		$dpanolengkap = '........................';
	
	$rows[] = array (
				array ('data'=>'', 'width'=>'50px', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'), 
				array ('data'=>'', 'width'=>'825px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'));	
	$rows[] = array (
				array ('data'=>'', 'width'=>'50px', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'), 
				array ('data'=>'KABUPATEN JEPARA', 'width'=>'825px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'));	
	$rows[] = array (
				array ('data'=>'', 'width'=>'50px', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'), 
				array ('data'=>'DOKUMEN PELAKSANAAN '.$ubahpdf2.'ANGGARAN', 'width'=>'825px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.75em; text-align:center;'));	
	$rows[] = array (
				array ('data'=>'', 'width'=>'50px', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'), 
				array ('data'=>'SATUAN KERJA PERANGKAT DAERAH (DPA-SKPD)', 'width'=>'825px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.75em; text-align:center;'));	
	$rows[] = array (
				array ('data'=>'', 'width'=>'50px', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'), 
				array ('data'=>'TAHUN ANGGARAN ' . apbd_tahun(), 'width'=>'825px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'));	
	$rows[] = array (
				array ('data'=>'', 'width'=>'50px', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'), 
				array ('data'=>'P E N D A P A T A N', 'width'=>'825px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.75em; text-align:center;'));	
	$rows[] = array (
				array ('data'=>'', 'width'=>'50px', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'), 
				array ('data'=>'NO. DPA-SKPD : ' . $dpanolengkap, 'width'=>'825px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'));	

	$rows[]= array (
				array ('data'=>'', 'width'=>'50px', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'), 
				array('data' => '',  'width'=> '175px', 'style' => 'border:none;font-weight:900; font-size:1em; text-align:left;'),
				array('data' => '', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => '', 'width' => '635px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array ('data'=>'', 'width'=>'50px', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'), 
				array('data' => 'URUSAN PEMERINTAHAN',  'width'=> '175px', 'style' => 'border:none;font-weight:900; font-size:1em; text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => $urusan, 'width' => '635px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array ('data'=>'', 'width'=>'50px', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'), 
				array('data' => 'ORGANISASI',  'width'=> '175px', 'style' => 'border:none;font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => $organisasi, 'width' => '635px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array ('data'=>'', 'width'=>'50px', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'), 
				array('data' => 'JUMLAH ANGGARAN',  'width'=> '175px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => 'Rp ' . apbd_fn($total) . ',00', 'width' => '635px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array ('data'=>'', 'width'=>'50px', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'), 
				array('data' => 'TERBILANG',  'width'=> '175px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => strtoupper(apbd_terbilang($total)), 'width' => '635px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);

	$rows[]= array (
				array ('data'=>'', 'width'=>'50px', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'), 
				array('data' => 'PENGGUNA ANGGARAN',  'width'=> '175px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:right;'),
				array('data' => '', 'width' => '635px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array ('data'=>'', 'width'=>'50px', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'), 
				array('data' => '- NAMA',  'width'=> '175px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => $pimpinannama, 'width' => '635px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array ('data'=>'', 'width'=>'50px', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'), 
				array('data' => '- NIP',  'width'=> '175px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => $pimpinannip, 'width' => '635px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array ('data'=>'', 'width'=>'50px', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'), 
				array('data' => '- JABATAN',  'width'=> '175px', 'style' => 'border:none; font-weight:900; font-size:1em;  text-align:left;'),
				array('data' => ':', 'width' => '15px', 'style' => 'border:none; font-weight:900; font-size:1em; text-align:right;'),
				array('data' => $pimpinanjabatan, 'width' => '635px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);

	$opttb0 = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0`');
	$headerkosong = array();

	$output = theme_box('', apbd_theme_table($headerkosong, $rows, $opttb0));
	
	return $output;
			
}

function GenReportFormSampulDepan($kodeuk, $ubahpdf2, $ubahpdf) {
	$where = ' where kodeuk=\'%s\'';
	$pquery = sprintf('select kodedinas, namauk from {unitkerja} ' . $where, db_escape_string($kodeuk));
	////drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$organisasi = $data->kodedinas . ' - ' . $data->namauk;
	}
	$rows[] = array (
				array('data' => '',  'width'=> '50px', 'style' => 'border-none;'),
				array ('data'=>'', 'width'=>'825px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'));	
	$rows[] = array (
				array('data' => '',  'width'=> '50px', 'style' => 'border-none;'),
				array ('data'=>'KABUPATEN JEPARA', 'width'=>'825px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'));	
	$rows[] = array (
				array('data' => '',  'width'=> '50px', 'style' => 'border-none;'),
				array ('data'=>'DOKUMEN PELAKSANAAN '.strtoupper($ubahpdf2).'ANGGARAN', 'width'=>'825px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.75em; text-align:center;'));	
	$rows[] = array (
				array('data' => '',  'width'=> '50px', 'style' => 'border-none;'),
				array ('data'=>'SATUAN KERJA PERANGKAT DAERAH (DPA-SKPD)', 'width'=>'825px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.75em; text-align:center;'));	
	$rows[] = array (
				array('data' => '',  'width'=> '50px', 'style' => 'border-none;'),
				array ('data'=> $organisasi, 'width'=>'825px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.75em; text-align:center;'));	
	$rows[] = array (
				array('data' => '',  'width'=> '50px', 'style' => 'border-none;'),
				array ('data'=>'TAHUN ANGGARAN ' . apbd_tahun(), 'width'=>'825px', 'colspan'=>'5', 'style' =>'border:none; font-weight:900; font-size:1.2em; text-align:center;'));	

	$rows[]= array (
				array('data' => '',  'width'=> '190px', 'style' => 'border:none;font-weight:900; font-size:1em; text-align:left;'),
				array('data' => '', 'width' => '650px', 'colspan'=>'5',  'style' => 'border:none; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => '',  'width'=> '50px', 'style' => 'border-none;'),
				array('data' => 'KODE',  'width'=> '140px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-bottom: 2px solid black; border-top: 2px solid black; font-weight:900; font-size:1em; text-align:center;'),
				array('data' => 'NAMA FORMULIR', 'width' => '650px', 'colspan'=>'6',  'style' => 'border-right: 1px solid black; border-bottom: 2px solid black; border-top: 2px solid black; ; font-weight:900; font-size:1em; text-align:center;'),
			);
	$rows[]= array (
				array('data' => '',  'width'=> '50px', 'style' => 'border-none;'),
				array('data' => 'DP'.$ubahpdf.'A - SKPD',  'width'=> '140px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black;  font-weight:900; font-size:1em; text-align:left;'),
				array('data' => 'Ringkasan Dokumen Pelaksanaan '.ucwords($ubahpdf2).'Anggaran Satuan Kerja Perangkat Daerah', 'width' => '650px', 'colspan'=>'6',  'style' => 'border-right: 1px solid black; border-bottom: 1px solid black;  ; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => '',  'width'=> '50px', 'style' => 'border-none;'),
				array('data' => 'DP'.$ubahpdf.'A - SKPD 1',  'width'=> '140px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black;  font-weight:900; font-size:1em; text-align:left;'),
				array('data' => 'Rincian Dokumen Pelaksanaan '.ucwords($ubahpdf2).'Anggaran Pendapatan Satuan Kerja Perangkat Daerah', 'width' => '650px', 'colspan'=>'6',  'style' => 'border-right: 1px solid black; border-bottom: 1px solid black;  ; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => '',  'width'=> '50px', 'style' => 'border-none;'),
				array('data' => 'DP'.$ubahpdf.'A - SKPD 2.1',  'width'=> '140px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black;  font-weight:900; font-size:1em; text-align:left;'),
				array('data' => 'Rincian Dokumen Pelaksanaan '.ucwords($ubahpdf2).'Anggaran Belanja Tidak Langsung Satuan Kerja Perangkat Daerah', 'width' => '650px', 'colspan'=>'6',  'style' => 'border-right: 1px solid black; border-bottom: 1px solid black;  ; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => '',  'width'=> '50px', 'style' => 'border-none;'),
				array('data' => 'DP'.$ubahpdf.'A - SKPD 2.2',  'width'=> '140px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-bottom: 1px solid black;  font-weight:900; font-size:1em; text-align:left;'),
				array('data' => 'Rekapitulasi Belanja Langsung menurut Program dan Kegiatan Satuan Kerja Perangkat Daerah', 'width' => '650px', 'colspan'=>'6',  'style' => 'border-right: 1px solid black; border-bottom: 1px solid black;  ; font-weight:900; font-size:1em; text-align:left;'),
			);
	$rows[]= array (
				array('data' => '',  'width'=> '50px', 'style' => 'border-none;'),
				array('data' => 'DP'.$ubahpdf.'A - SKPD 2.2.1',  'width'=> '140px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-bottom: 2px solid black;  font-weight:900; font-size:1em; text-align:left;'),
				array('data' => 'Rincian Dokumen Pelaksanaan '.ucwords($ubahpdf2).'Anggaran Belanja Langsung Program dan Per Kegiatan Satuan Kerja Perangkat Daerah', 'width' => '650px', 'colspan'=>'6',  'style' => 'border-right: 1px solid black; border-bottom: 2px solid black;  ; font-weight:900; font-size:1em; text-align:left;'),
			);


	$opttb0 = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0`');
	$headerkosong = array();

	$output = theme_box('', apbd_theme_table($headerkosong, $rows, $opttb0));
	
	return $output;
			
}
	
function GenReportForm($kodekeg, $tipedok) {
	
	
	/*
	$jumrincian = 0;
	$sql = 'select count(iddetil) jumrincian from {anggperkegdetil} where kodekeg=\'%s\'';
	$fsql = sprintf($sql, db_escape_string($kodekeg));
	$res = db_query($fsql);
	if ($res) {
		if ($data = db_fetch_object($res)) {
			$jumrincian = $data->jumrincian;
		}
	}
	
	$sql = 'select count(idsub) jumrincian from {anggperkegdetilsub} s inner join {anggperkegdetil} d
			on s.iddetil=d.iddetil where d.kodekeg=\'%s\'';
	$fsql = sprintf($sql, db_escape_string($kodekeg));
	$res = db_query($fsql);
	if ($res) {
		if ($data = db_fetch_object($res)) {
			$jumrincian += $data->jumrincian;
		}
	}
	
	echo $jumrincian;
	*/
	
	////drupal_set_message($dpa);

	
	$skpd = '';
	$pimpinannama='';
	$pimpinannip='';
	$pimpinanjabatan='';
	$pquery = sprintf("select kodedinas, namauk, pimpinannama, pimpinannip, pimpinanjabatan from {unitkerja} u left join {kegiatanskpd} k 
			  on u.kodeuk=k.kodeuk where k.kodekeg='%s'", db_escape_string($kodekeg)) ;
	////drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$kodedinas = $data->kodedinas;
		$skpd = $kodedinas . ' - ' . $data->namauk;
		$pimpinannama=$data->pimpinannama;
		$pimpinannip=$data->pimpinannip;
		$pimpinanjabatan=$data->pimpinanjabatan;
	}

	$pquery = sprintf("select dpatgl, budnama, budnip, budjabatan from {setupapp} where tahun='%s'", variable_get('apbdtahun', 0)) ;
	////drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$budnama = $data->budnama;
		$budnip = $data->budnip;
		$budjabatan = $data->budjabatan;
		$dpatgl = $data->dpatgl;
	}

	$where = ' where k.kodekeg=\'%s\'';
	$pquery = sprintf('select k.kodekeg, right(k.kodekeg,3) nomorkeg, k.tahun, k.kodepro, k.kodeuk, k.kodesuk, 
				k.kegiatan, k.jenis, k.lokasi, k.programsasaran, k.programtarget, k.masukansasaran, k.masukantarget,
				k.keluaransasaran, k.keluarantarget, k.hasilsasaran,  k.hasiltarget, k.total, k.plafon, 
				k.totalsebelum, k.totalsesudah, k.waktupelaksanaan, k.sumberdana1, k.sumberdana2, 
				k.sumberdana1rp, k.sumberdana2rp, k.latarbelakang, k.kelompoksasaran, p.program,
				p.kodepro, p.kodeu, u.urusan, u.kodef, u.fungsi, k.tw1, k.tw2, k.tw3, k.tw4 from {kegiatanskpd} k left join {program} p 
				on (k.kodepro = p.kodepro) left join {urusan} u on p.kodeu=u.kodeu' . $where, db_escape_string($kodekeg));
	////drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {

		$fungsi = $data->kodef . ' - ' . $data->fungsi;
		$urusan = $data->kodeu . ' - ' . $data->urusan;
		$program = $data->kodeu . '.' . $data->kodepro . ' - ' . $data->program;
		$kegiatan = $kodedinas . '.' . $data->kodeu . '.' . $data->kodepro . '.' . $data->nomorkeg . ' - ' .  $data->kegiatan;
		
		$jenis = $data->jenis;
		$tahun = $data->tahun;
		
		$lokasi = str_replace('||',', ', $data->lokasi);
		$programsasaran = $data->programsasaran;
		$programtarget = $data->programtarget;
		$masukansasaran = $data->masukansasaran;
		//$masukantarget = $data->masukantarget;
		$keluaransasaran = $data->keluaransasaran;
		$keluarantarget = $data->keluarantarget;
		$hasilsasaran = $data->hasilsasaran;
		$hasiltarget = $data->hasiltarget;
		$total = $data->total;
		$masukantarget = 'Rp ' . apbd_fn($data->total);
		$plafon = $data->plafon;
		$waktupelaksanaan = $data->waktupelaksanaan;
		$sumberdana1 = $data->sumberdana1;
		$sumberdana2 = $data->sumberdana2;
		$sumberdana1rp = $data->sumberdana1rp;
		$sumberdana2rp = $data->sumberdana2rp;
		$latarbelakang = $data->latarbelakang;
		$kelompoksasaran = $data->kelompoksasaran;
		$tw1 = $data->tw1;
		$tw2 = $data->tw2;
		$tw3 = $data->tw3;
		$tw4 = $data->tw4;
		
	}	
	$tahunsebelum = $tahun-1;
	$tahunsesudah = $tahun+1;
	 
	$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
	$rows= array();
	
	if ($jenis==1) $strbelanja = 'TIDAK ';
	
	if ($tipedok=='dpa')
		$rowsjudul[] = array (array ('data'=>'DPA-SKPD - BELANJA ' . $strbelanja . 'LANGSUNG', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));	
	else
		$rowsjudul[] = array (array ('data'=>'RKA-SKPD - BELANJA ' . $strbelanja . 'LANGSUNG', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border:none; font-weight:900; font-size:1.3em; text-align:center;'));	

	$rowskegiatan[]= array (
						 //array('data' => 'Fungsi',  'width'=> '150px', 'style' => ' border-bottom: 1px solid black;  text-align:left;'),
						 //array('data' => ':', 'width' => '25px', 'style' => 'border-bottom: 1px solid black;  text-align:left;'),
						 //array('data' => 'Fungsinnya', 'width' => '700', 'colspan'=>'5',  'style' => 'border-bottom: 1px solid black;  text-align:left;'),

						 array('data' => 'Fungsi',  'width'=> '150px', 'style' => 'border:none;; text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'border:none; text-align:right;'),
						 array('data' => $fungsi, 'width' => '710', 'colspan'=>'5',  'style' => 'border:none; text-align:left;'),

						 );
	$rowskegiatan[]= array (
						 array('data' => 'Urusan Pemerintahan',  'width'=> '150px', 'style' => 'border:none; text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'border:none; text-align:right;'),
						 array('data' => $urusan, 'width' => '710', 'colspan'=>'5',  'style' => 'border:none;text-align:left;'),
						 );
	$rowskegiatan[]= array (
						 array('data' => 'Organisasi',  'width'=> '150px', 'style' => ' text-align:left;'),
						 array('data' => ':',  'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $skpd,  'width' => '710', 'colspan'=>'5',  'style' => ' text-align:left;'),
						 );
	$rowskegiatan[]= array (
						 array('data' => 'Program',   'width'=> '150px', 'style' => ' text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $program,   'width' => '710', 'colspan'=>'5',  'style' => ' text-align:left;'),
						 );	
	$rowskegiatan[]= array (
						 array('data' => 'Kegiatan',  'width'=> '150px', 'style' => ' text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $kegiatan,  'width' => '710', 'colspan'=>'5',  'style' => ' text-align:left;'),
						 );	
	if ($jenis==2)
		$rowskegiatan[]= array (
							 array('data' => 'Lokasi',  'width'=> '150px', 'style' => ' text-align:left;'),
							 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
							 array('data' => $lokasi,  'width' => '710', 'colspan'=>'5',  'style' => ' text-align:left;'),
							 );	
	$rowskegiatan[]= array (
						 array('data' => 'Anggaran',  'width'=> '150px', 'style' => ' text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => apbd_fn($total),  'width' => '160', 'colspan'=>'2',  'style' => 'text-align:right;'),
						 array('data' => '',  'width' => '550', 'colspan'=>'3',  'style' => 'text-align:left;'),
						 );	
	$rowskegiatan[]= array (
						 array('data' => '',  'width'=> '150px', 'style' => ' text-align:left;'),
						 array('data' => '', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => apbd_terbilang($total),  'width' => '710', 'colspan'=>'5',  'style' => ' text-align:left;'),
						 );	

if ($jenis==2) {
		//TUK
		$rowskegiatan[]= array (
							 array('data' => 'Indikator',  'width'=> '175px', 'colspan'=>'2',  'style' => ' border-bottom: 1px solid black;  border-top: 1px solid black; text-align:left;'),
							 array('data' => 'Tolok Ukur Kinerja', 'width' => '350px', 'colspan'=>'3',  'style' => 'border-bottom: 1px solid black;  border-top: 1px solid black; text-align:left;'),
							 array('data' => 'Target Kinerja', 'width' => '350', 'colspan'=>'2',  'style' => 'border-bottom: 1px solid black;  border-top: 1px solid black; text-align:left;'),
							 );	
		$rowskegiatan[]= array (
							 array('data' => 'Capaian Program',  'width'=> '175px', 'colspan'=>'2',  'style' => '   text-align:left;'),
							 array('data' => $programsasaran, 'width' => '350px', 'colspan'=>'3',  'style' => ' text-align:left;'),
							 array('data' => $programtarget, 'width' => '350', 'colspan'=>'2',  'style' => ' text-align:left;'),
							 );	
		$rowskegiatan[]= array (
							 array('data' => 'Masukan',  'width'=> '175px', 'colspan'=>'2',  'style' => '  text-align:left;'),
							 array('data' => $masukansasaran, 'width' => '350px', 'colspan'=>'3',  'style' => ' text-align:left;'),
							 array('data' => $masukantarget, 'width' => '350', 'colspan'=>'2',  'style' => ' text-align:left;'),
							 );	
		$rowskegiatan[]= array (
							 array('data' => 'Keluaran',  'width'=> '175px', 'colspan'=>'2',  'style' => '   text-align:left;'),
							 array('data' => $keluaransasaran, 'width' => '350px', 'colspan'=>'3',  'style' => ' text-align:left;'),
							 array('data' => $keluarantarget, 'width' => '350', 'colspan'=>'2',  'style' => ' text-align:left;'),
							 );	
		$rowskegiatan[]= array (
							 array('data' => 'Hasil',  'width'=> '175px', 'colspan'=>'2',  'style' => '  text-align:left;'),
							 array('data' => $hasilsasaran, 'width' => '350px', 'colspan'=>'3',  'style' => ' text-align:left;'),
							 array('data' => $hasiltarget, 'width' => '350', 'colspan'=>'2',  'style' => ' text-align:left;'),
							 );	

		//Kelompok Sasaran Kegiatan
		$rowskegiatan[]= array (
							 array('data' => 'Kelompok Sasaran',   'width'=> '150px', 'style' => 'text-align:left;'),
							 array('data' => ':',  'width' => '15px', 'style' => 'text-align:right;'),
							 array('data' => $kelompoksasaran,   'width' => '710', 'colspan'=>'5',  'style' => 'text-align:left;'),
							 );				
	}
	 
	$headersrek[] = array (
						 //array('data' => 'Kode',  'width'=> '75px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 
						 array('data' => 'Kode',  'width'=> '75px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Uraian',  'width' => '400x', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Satuan', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Volume', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Harga Satuan',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'Total',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 

						 );

	//JENIS
	$total = 0;
	$where = ' where k.kodekeg=\'%s\'';
	$sql = 'select mid(k.kodero,1,3) kodej,j.uraian,sum(jumlah) jumlahx from {anggperkeg} k  left join {jenis} j on mid(k.kodero,1,3)=j.kodej ' . $where;
	$fsql = sprintf($sql, db_escape_string($kodekeg));
	$fsql .= ' group by mid(k.kodero,1,3),j.uraian order by mid(k.kodero,1,3)';
	
	////drupal_set_message( $fsql);
	$resultjenis = db_query($fsql);
	if ($resultjenis) {
		while ($datajenis = db_fetch_object($resultjenis)) {
			$total += $datajenis->jumlahx;
			$rowsrek[] = array (
								 array('data' => $datajenis->kodej,  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $datajenis->uraian,  'width' => '400x', 'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
								 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
								 array('data' => apbd_fn($datajenis->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black; text-align:right;font-weight:bold;'),
								 );
			    
			//OBYEK
			$sql = 'select mid(k.kodero,1,5) kodeo,o.uraian,sum(jumlah) jumlahx from {anggperkeg} k  left join {obyek} o on mid(k.kodero,1,5)=o.kodeo 
				   where kodekeg=\'%s\' and mid(k.kodero,1,3)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($datajenis->kodej));
			$fsql .= ' group by mid(k.kodero,1,5),o.uraian order by mid(k.kodero,1,5)';
			
			////drupal_set_message( $fsql);
			$resultobyek = db_query($fsql);
			if ($resultobyek) {
				while ($dataobyek = db_fetch_object($resultobyek)) {
					$rowsrek[] = array (
										 array('data' => apbd_format_rek_obyek($dataobyek->kodeo),  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => $dataobyek->uraian,  'width' => '400x', 'style' => ' border-right: 1px solid black; text-align:left;'),
										 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
										 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
										 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
										 array('data' => apbd_fn($dataobyek->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
										 );		

					//REKENING
					$sql = 'select kodero,uraian,jumlah from {anggperkeg} k where kodekeg=\'%s\' and mid(k.kodero,1,5)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($dataobyek->kodeo));
					
					////drupal_set_message( $fsql);
					$fsql .= ' order by k.kodero';
					$result = db_query($fsql);
					if ($result) {
						while ($data = db_fetch_object($result)) {
						$rowsrek[] = array (
											 array('data' => apbd_format_rek_rincianobyek($data->kodero),  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
											 array('data' => $data->uraian,  'width' => '400x', 'style' => ' border-right: 1px solid black; text-align:left;'),
											 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
											 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
											 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
											 array('data' => apbd_fn($data->jumlah),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;'),
											 );
							//DETIL
							$sql = 'select iddetil, uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan, harga,total,pengelompokan from {anggperkegdetil} where kodekeg=\'%s\' and kodero=\'%s\' order by nourut asc,iddetil';
							$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($data->kodero));
							////drupal_set_message($fsql);
							
							$resultdetil = db_query($fsql);
							if ($resultdetil) {
								while ($datadetil = db_fetch_object($resultdetil)) {
									if ($datadetil->pengelompokan) {
										$unitjumlah = '';
										$volumjumlah = '';
										$hargasatuan = '';
										
									} else {
										$unitjumlah = $datadetil->unitjumlah . ' ' . $datadetil->unitsatuan;
										$volumjumlah = $datadetil->volumjumlah . ' ' . $datadetil->volumsatuan;
										$hargasatuan = apbd_fn($datadetil->harga);
									}
									$rowsrek[] = array (
														 array('data' => '',  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
														 //array('data' => '-',  'width' => '25px', 'style' => ' border-right: 1px solid black; text-align:left;'),
														 array('data' => '- ' . $datadetil->uraian,  'width' => '400px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;font-style: italic;'),
														 array('data' => $unitjumlah, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
														 array('data' => $volumjumlah, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
														 array('data' => $hargasatuan,  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
														 array('data' => apbd_fn($datadetil->total),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
														 );
									if ($datadetil->pengelompokan) {
										//SUB DETIL
										$sql = 'select idsub,uraian,unitjumlah,unitsatuan,volumjumlah, volumsatuan,harga,total from {anggperkegdetilsub} where iddetil=\'%s\' order by nourut asc,idsub';
										$fsql = sprintf($sql, db_escape_string($datadetil->iddetil));
										////drupal_set_message($fsql);
										
										$resultsub = db_query($fsql);
										while ($datasub = db_fetch_object($resultsub)) {
											$rowsrek[] = array (
														 array('data' => '',  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
														 //array('data' => '-',  'width' => '25px', 'style' => ' border-right: 1px solid black; text-align:left;'),
														 array('data' => '. ' . $datasub->uraian,  'width' => '400px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;font-style: italic;'),
														 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
														 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
														 array('data' => apbd_fn($datasub->harga),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
														 array('data' => apbd_fn($datasub->total),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
														 );
										}
										
									}
									
								}
							}
							
							//CATATAN
							/*
							if (($tipedok!='dpa') and (variable_get("cetakverifikasirka", 0)==1)) {
								$sql_r = "select username,jawaban from {anggperkegverifikasi} where kodekeg='" . $kodekeg . "' and kodero='" . $data->kodero . "' order by username";
								$res_r = db_query($sql_r);
								
								while ($data_cat= db_fetch_object($res_r)) {	
									$catatan = '<img src="/files/icon/info_red_16.png">' . $data_cat->username . ': <font color="Red">' . $data_cat->jawaban . '</font>';
									$rowsrek[] = array (
												 array('data' => '',  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
												 //array('data' => '-',  'width' => '25px', 'style' => ' border-right: 1px solid black; text-align:left;'),
												 array('data' => $catatan,  'width' => '400px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;font-style: italic;'),
												 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
												 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
												 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
												 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
												 );								
								}
							}	//TIPE DOK		
							*/		
							
						}
					}
										 
				////////
				}
			}
		}
	}

	$rowsrek[] = array (
						 array('data' => 'JUMLAH BELANJA',  'width'=> '775px',  'colspan'=>'5',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($total),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 );


	if ($tipedok=='dpa') {						 
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '300px',  'colspan'=>'3',  'style' => 'text-align:center'),
							 array('data' => '',  'width'=> '275px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => '',  'width' => '300px', 'style' => 'text-align:center;'),
							 );

		$rowsfooter[] = array (
							 array('data' => 'RENCANA BELANJA TRI WULAN',  'width'=> '300px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black; border-top: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => '',  'width'=> '275px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => 'Jepara, ' . $dpatgl,  'width' => '300px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'Tri Wulan 1',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:center'),
							 array('data' => apbd_fn($tw1),  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '275px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; text-align:center'),
							 array('data' => $budjabatan,  'width' => '300px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'Tri Wulan 2',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:center'),
							 array('data' => apbd_fn($tw2),  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '275px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; text-align:center'),
							 array('data' => '',  'width' => '300px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'Tri Wulan 3',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:center'),
							 array('data' => apbd_fn($tw3),  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '275px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; text-align:center'),
							 array('data' => '',  'width' => '300px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'Tri Wulan 4',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:center'),
							 array('data' => apbd_fn($tw4),  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '275px',  'colspan'=>'2',  'style' => 'border-left: 1px solid black; text-align:center'),
							 array('data' => $budnama,  'width' => '300px', 'style' => 'text-align:center;text-decoration: underline'),
							 );	
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '100px', 'style' => 'border-top: 1px solid black; text-align:center'),
							 array('data' => '',  'width'=> '100px', 'style' => 'border-top: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '100px', 'style' => 'border-top: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '275px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => 'NIP. ' . $budnip,  'width' => '300px', 'style' => 'text-align:center;'),
							 );
	} else {
		
		$catatan = '';
		
		$pquery = sprintf("select count(kodero) jmlrek from {anggperkeg} where (jumlah mod 1000)>0 and  kodekeg='%s'", db_escape_string($kodekeg));
		////drupal_set_message($pquery); 
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			if ($data->jmlrek > 0)	$catatan = '!!!ADA SEJUMLAH REKENING YANG TIDAK BULAT PER 1000, HARAP DIPERBAIKI!!!';
		}
		
		//CATATAN
		if (($tipedok!='dpa') and (variable_get("cetakverifikasirka", 0)==1)) {
		
			$sql_r = "select username,persetujuan,jawaban from {kegiatanverifikasi} where kodekeg='" . $kodekeg . "' order by username";
			$res_r = db_query($sql_r);
			
			while ($data_r= db_fetch_object($res_r)) {	
				if ($data_r->jawaban!='') {
					if ($data_r->persetujuan==0) {
						$image = "<img src='/files/verify/fer_no.png'>";
						$color = 'red';
					} else if ($data_r->persetujuan==2) {
						$image = "<img src='/files/verify/fer_warning.png'>";
						$color = 'orange';
					} else {
						$image = "<img src='/files/verify/fer_ok.png'>";
						$color = 'green';
					}
					
					$catatan .= '<p>'. $image . ': <font color="' . $color . '">' . $data_r->jawaban . '</font></p>';
				}	
			}		
		}
		
		$rowsfooter[] = array (
							 array('data' => 'CATATAN',  'width'=> '675px',  'colspan'=>'5',  'style' => 'text-align:center'),
							 array('data' => 'KEPALA SKPD',  'width' => '200px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => $catatan,  'width'=> '675px',  'colspan'=>'5',  'style' => 'text-align:left'),
							 array('data' => '',  'width' => '200px', 'style' => 'text-align:right;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '675px',  'colspan'=>'5',  'style' => 'text-align:center'),
							 array('data' => $pimpinannama,  'width' => '200px', 'style' => 'text-align:center; text-decoration: underline;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '675px',  'colspan'=>'5',  'style' => 'text-align:center'),
							 array('data' => 'NIP. ' . $pimpinannip,  'width' => '200px', 'style' => 'text-align:center;'),
							 );		
	}

	$opttb0 = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0`');
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '1');
	$headerkosong = array();

	$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttb0));
	$output .= theme_box('', apbd_theme_table($headerkosong, $rowskegiatan, $opttb0));
	
	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	$output .= theme_box('', apbd_theme_table($headerkosong, $rowsfooter, $opttb0));
	
	$output .= $toutput;
	if ($limit >0)
		$output .= theme ('pager', NULL, $limit, 0);
	
	return $output;
	
}

function GenReportFormHeader($kodekeg, $tipedok) {
	
	$where = ' where k.kodekeg=\'%s\'';
	$pquery = sprintf('select k.kodekeg, right(k.kodekeg,3) nomorkeg, k.tahun, k.kodepro, k.kodeuk, k.kodesuk, k.kegiatan, k.lokasi, k.jenis, 
				k.programsasaran, k.programtarget, k.masukansasaran, k.masukantarget, k.keluaransasaran, k.keluarantarget, 
				k.hasilsasaran,  k.hasiltarget, k.total, k.anggaran, k.bintang, k.plafon, k.totalsebelum, k.totalsesudah, k.waktupelaksanaan, 
				k.sumberdana1, k.sumberdana2, k.sumberdana1rp, k.sumberdana2rp, k.latarbelakang, k.kelompoksasaran, p.program,
				p.kodepro, p.kodeu, u.urusan, u.fungsi, u.kodef, uk.kodedinas, uk.namauk from {kegiatanskpd} k left join {program} p on (k.kodepro = p.kodepro) 
				left join {urusan} u on p.kodeu=u.kodeu left join {unitkerja} uk on k.kodeuk=uk.kodeuk ' . $where, db_escape_string($kodekeg));
	////drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {

		$nomorkeg = $data->kodedinas . '.' . $data->kodepro . '.' . $data->nomorkeg;
	
		$fungsi = $data->kodef . ' - ' . $data->fungsi;
		$urusan = $data->kodeu . ' - ' . $data->urusan;
		$program = $data->kodepro . ' - ' . $data->program;
		$skpd = $data->kodedinas . ' - ' . $data->namauk;
		$kegiatan = $data->kodedinas . '.' . $data->kodepro . '.' . $data->nomorkeg . ' - ' .  $data->kegiatan;

		$tahun = $data->tahun;
		$jenis = $data->jenis;
		
		$tahunsebelum = $tahun-1;
		$tahunsesudah = $tahun+1;
		 
		$lokasi = str_replace('||',', ', $data->lokasi);
		$programsasaran = $data->programsasaran;
		$programtarget = $data->programtarget;
		$masukansasaran = $data->masukansasaran;
		//$masukantarget = $data->masukantarget;
		$masukantarget = 'Rp ' . apbd_fn($data->total);
		$keluaransasaran = $data->keluaransasaran;
		$keluarantarget = $data->keluarantarget;
		$hasilsasaran = $data->hasilsasaran;
		$hasiltarget = $data->hasiltarget;
		$total = $data->total;
		$plafon = $data->plafon;
		$waktupelaksanaan = $data->waktupelaksanaan;
		$sumberdana1 = $data->sumberdana1;
		$sumberdana2 = $data->sumberdana2;
		$sumberdana1rp = $data->sumberdana1rp;
		$sumberdana2rp = $data->sumberdana2rp;
		$latarbelakang = $data->latarbelakang;
		$kelompoksasaran = $data->kelompoksasaran;
		
		$anggaran = $data->anggaran;
		$statuskegiatan = "";
		//$isbintang = false;
		
		
		if ($data->bintang==1) {
			$statuskegiatan = '<font color="Red">*</font>';
			$isbintang = true;
		} else {
			$statuskegiatan = "";
			$isbintang = false;
		}
		
	}	

 
	$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
	$rows= array();
	//$rowsjudul[] = array (array ('data'=>'RENCANA KERJA DAN ANGGARAN SATUAN KERJA PERANGKAT DAERAH', 'width'=>'875px', 'colspan'=>'7', 'style' =>'border: 0px solid white;font-weight:900;font-size:1.3em;text-align:center;'));
  
	/*
	$rowskegiatan[]= array ( 
						 array('data' => 'PEMERINTAH KABUPATEN JEPARA',  'width'=> '250px', 'colspan'=>'3', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
						 array('data' => 'RENCANA KERJA DAN ANGGARAN SATUAN KERJA PERANGKAT DAERAH', 'width' => '500px', 'colspan'=>'3', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
						 array('data' => $tahun, 'width' => '125',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
						 );
	*/
	
	
	
	if ($jenis==1) $strjenis = 'T I D A K  -  ';
	if ($tipedok=='dpa') {
		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'DOKUMEN PELAKSANAAN ANGGARAN', 'width' => '310px', 'colspan'=>'3', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => '', 'width' => '300px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'FORMULIR', 'width' => '175',  'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1em; text-align:center;'),
							 );
		if ($jenis==2)
			$rowskegiatan[]= array ( 
								 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => 'SATUAN KERJA PERANGKAT DAERAH', 'width' => '310px', 'colspan'=>'3', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => 'B E L A N J A  -  ' . $strjenis . 'L A N G S U N G', 'width' => '300px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => 'DPA-SKPD 2.2.1', 'width' => '175',  'style' => 'border-right: 1px solid black; font-size:1em; text-align:center;'),
								 );
		else
			$rowskegiatan[]= array ( 
								 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => 'SATUAN KERJA PERANGKAT DAERAH', 'width' => '310px', 'colspan'=>'3', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => 'B E L A N J A  -  ' . $strjenis . 'L A N G S U N G', 'width' => '300px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => 'DPA-SKPD 2.1', 'width' => '175',  'style' => 'border-right: 1px solid black; font-size:1em; text-align:center;'),
								 );
		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '310px', 'colspan'=>'3', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => '', 'width' => '300px', 'colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
							 array('data' => 'TAHUN ' . $tahun, 'width' => '175',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size:1em; text-align:center;'),
							 );
	} else {
		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'RENCANA KERJA DAN ANGGARAN', 'width' => '310px', 'colspan'=>'3', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => '', 'width' => '300px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'FORMULIR', 'width' => '175',  'style' => 'border-right: 1px solid black; border-top: 2px solid black; font-size:1em; text-align:center;'),
							 );
		
		if ($jenis==2)
			$rowskegiatan[]= array ( 
								 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => 'SATUAN KERJA PERANGKAT DAERAH', 'width' => '310px', 'colspan'=>'3', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => 'B E L A N J A  -  ' . $strjenis . 'L A N G S U N G', 'width' => '300px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => 'RKA-SKPD 2.2.1', 'width' => '175',  'style' => 'border-right: 1px solid black; font-size:1em; text-align:center;'),
								 );
		else
			$rowskegiatan[]= array ( 
								 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => 'SATUAN KERJA PERANGKAT DAERAH', 'width' => '310px', 'colspan'=>'3', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => 'B E L A N J A  -  ' . $strjenis . 'L A N G S U N G', 'width' => '300px', 'colspan'=>'2', 'style' => 'border-right: 1px solid black; font-size:1.3em; text-align:center;'),
								 array('data' => 'RKA-SKPD 2.1', 'width' => '175',  'style' => 'border-right: 1px solid black; font-size:1em; text-align:center;'),
								);
		$rowskegiatan[]= array ( 
							 array('data' => '',  'width'=> '90px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'PEMERINTAH KABUPATEN JEPARA', 'width' => '310px', 'colspan'=>'3', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => '', 'width' => '300px', 'colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size:1.3em; text-align:center;'),
							 array('data' => 'TAHUN ' . $tahun, 'width' => '175',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; font-size:1em; text-align:center;'),
							 );		
	} 
	$rowskegiatan[]= array (
						 //array('data' => 'Fungsi',  'width'=> '150px', 'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:left;'),
						 //array('data' => ':', 'width' => '25px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:left;'),
						 //array('data' => 'Fungsinnya', 'width' => '700', 'colspan'=>'5',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:left;'),

						 array('data' => 'Fungsi',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $fungsi, 'width' => '710', 'colspan'=>'5',  'style' => 'border-right: 1px solid black; text-align:left;'),

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
	if ($jenis==2)					 
		$rowskegiatan[]= array ( 
							 array('data' => 'Program',   'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
							 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
							 array('data' => $program,   'width' => '710', 'colspan'=>'5',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 );	
	$rowskegiatan[]= array (
						 array('data' => 'Kegiatan',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $kegiatan . $statuskegiatan ,  'width' => '710', 'colspan'=>'5',  'style' => 'border-right: 1px solid black; text-align:left;'),
						 );	
	if ($jenis==2)
		$rowskegiatan[]= array (
							 array('data' => 'Lokasi',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
							 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
							 array('data' => $lokasi,  'width' => '710', 'colspan'=>'5',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 );	
	$rowskegiatan[]= array (
						 array('data' => 'Anggaran',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => 'Rp ' . apbd_fn($total) . ',00',  'width' => '160', 'colspan'=>'2',  'style' => 'border-bottom: 1px solid black;text-align:right;'),
						 array('data' => '',  'width' => '550', 'colspan'=>'3',  'style' => 'border-right: 1px solid black; text-align:left;'),
						 );	
	$rowskegiatan[]= array (
						 array('data' => '',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => '', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => apbd_terbilang($total),  'width' => '710', 'colspan'=>'5',  'style' => 'border-right: 1px solid black; text-align:left;'),
						 );	

	//BINTANG
	/*
	if ($isbintang) {
		$rowskegiatan[]= array (
							 array('data' => 'Tersedia' . $statuskegiatan,  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
							 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
							 array('data' => '<font color="red">Rp ' . apbd_fn($anggaran) . ',00</font>',  'width' => '160', 'colspan'=>'2',  'style' => 'border-bottom: 1px solid black;text-align:right;'),
							 array('data' => '',  'width' => '550', 'colspan'=>'3',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 );	
		$rowskegiatan[]= array (
							 array('data' => '',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
							 array('data' => '', 'width' => '15px', 'style' => 'text-align:right;'),
							 array('data' => '<font color="red">' . apbd_terbilang($anggaran) . '</font>',  'width' => '710', 'colspan'=>'5',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 );	
	}	
	*/
	$rowskegiatan[]= array (
						 array('data' => 'Sumber Dana',  'width'=> '150px', 'style' => 'border-left: 1px solid black; text-align:left;'),
						 array('data' => ':', 'width' => '15px', 'style' => 'text-align:right;'),
						 array('data' => $sumberdana1,  'width' => '710', 'colspan'=>'5',  'style' => 'border-right: 1px solid black; text-align:left;'),
						 );	


	//TUK
	if ($jenis==2) {
		$rowskegiatan[]= array (
							 array('data' => 'Indikator',  'width'=> '175px', 'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:left;'),
							 array('data' => 'Tolok Ukur Kinerja', 'width' => '350px', 'colspan'=>'3',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:left;'),
							 array('data' => 'Target Kinerja', 'width' => '350', 'colspan'=>'2',  'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:left;'),
							 );	
		$rowskegiatan[]= array (
							 array('data' => 'Capaian Program',  'width'=> '175px', 'colspan'=>'2',  'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => $programsasaran, 'width' => '350px', 'colspan'=>'3',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 array('data' => $programtarget, 'width' => '350', 'colspan'=>'2',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 );	
		$rowskegiatan[]= array (
							 array('data' => 'Masukan',  'width'=> '175px', 'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:left;'),
							 array('data' => $masukansasaran, 'width' => '350px', 'colspan'=>'3',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 array('data' => $masukantarget, 'width' => '350', 'colspan'=>'2',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 );	
		$rowskegiatan[]= array (
							 array('data' => 'Keluaran',  'width'=> '175px', 'colspan'=>'2',  'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
							 array('data' => $keluaransasaran, 'width' => '350px', 'colspan'=>'3',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 array('data' => $keluarantarget, 'width' => '350', 'colspan'=>'2',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 );	
		$rowskegiatan[]= array (
							 array('data' => 'Hasil',  'width'=> '175px', 'colspan'=>'2',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:left;'),
							 array('data' => $hasilsasaran, 'width' => '350px', 'colspan'=>'3',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 array('data' => $hasiltarget, 'width' => '350', 'colspan'=>'2',  'style' => 'border-right: 1px solid black; text-align:left;'),
							 );	

		//Kelompok Sasaran Kegiatan
		$rowskegiatan[]= array (
							 array('data' => 'Kelompok Sasaran',   'width'=> '150px', 'style' => 'border-left: 1px solid black;  border-top: 1px solid black; text-align:left;'),
							 array('data' => ':',  'width' => '15px', 'style' => ' border-top: 1px solid black; text-align:right;'),
							 array('data' => $kelompoksasaran,   'width' => '710', 'colspan'=>'5',  'style' => 'border-right: 1px solid black;  border-top: 1px solid black; text-align:left;'),
							 );							 
		 
	}
	if ($jenis==2)
		$rowskegiatan[]= array (
							 array('data' => 'Rincian Dokumen Pelaksanaan Anggaran Belanja Langsung per Program dan Kegiatan Satuan Kerja Perangkat Daerah',   'width' => '875', 'colspan'=>'7',  'style' => 'border-left: 1px solid black;  border-right: 1px solid black;  border-top: 1px solid black; text-align:center;'),
							 );							 
	else
	$rowskegiatan[]= array (
						 array('data' => 'Rincian Dokumen Pelaksanaan Anggaran Belanja Tidak Langsung Satuan Kerja Perangkat Daerah',   'width' => '875', 'colspan'=>'7',  'style' => 'border-left: 1px solid black;  border-right: 1px solid black;  border-top: 1px solid black; text-align:center;'),
						 );							 
	
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');
	$headerkosong = array();

	//$output = theme_box('', apbd_theme_table($headerkosong, $rowsjudul, $opttbl));
	$output = theme_box('', apbd_theme_table($headerkosong, $rowskegiatan, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormContent($kodekeg) {
	
	$tipedok = arg(5);
	if ($tipedok=='') $tipedok = 'rka';
	
	$keg_bintang = 0; $rek_bintang = 0; $det_bintang = 0; $sub_bintang = 0;
	$sql = sprintf('select bintang from {kegiatanskpd} where kodekeg=\'%s\'', db_escape_string($kodekeg));
	////drupal_set_message($pquery);
	$res = db_query($sql);
	if ($res) {
		if ($data = db_fetch_object($res)) {
			$keg_bintang = $data->bintang;
		}
	}	

	
	$jumrincian = 0;
	$sql = 'select count(iddetil) jumrincian from {anggperkegdetil} where kodekeg=\'%s\'';
	$fsql = sprintf($sql, db_escape_string($kodekeg));
	$res = db_query($fsql);
	if ($res) {
		if ($data = db_fetch_object($res)) {
			$jumrincian = $data->jumrincian;
		}
	}
	
	$sql = 'select count(idsub) jumrincian from {anggperkegdetilsub} s inner join {anggperkegdetil} d
			on s.iddetil=d.iddetil where d.kodekeg=\'%s\'';
	$fsql = sprintf($sql, db_escape_string($kodekeg));
	$res = db_query($fsql);
	if ($res) {
		if ($data = db_fetch_object($res)) {
			$jumrincian += $data->jumrincian;
		}
	}
	
	if ($jumrincian > 350) {
		set_time_limit(0);
		ini_set('memory_limit', '640M');
	}
	
	$total=0;

	$headersrek[] = array (
						 array('data' => 'KODE',  'width'=> '75px', 'rowspan'=>'2','style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 
						 array('data' => 'URAIAN',  'width' => '400x','rowspan'=>'2','colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'RINCIAN PERHITUNGAN', 'width' => '300px','colspan'=>'3','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'JUMLAH TOTAL',  'width' => '100px', 'rowspan'=>'2','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 );
	$headersrek[] = array (

						 array('data' => 'Satuan', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'Volume', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'Harga Satuan',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 );
						 
	 //JENIS
	$where = ' where k.kodekeg=\'%s\'';
	$sql = 'select mid(k.kodero,1,3) kodej,j.uraian,sum(jumlah) jumlahx from {anggperkeg} k  left join {jenis} j on mid(k.kodero,1,3)=j.kodej ' . $where;
	$fsql = sprintf($sql, db_escape_string($kodekeg));
	$fsql .= ' group by mid(k.kodero,1,3),j.uraian order by mid(k.kodero,1,3)';
	
	////drupal_set_message( $fsql);
	$resultjenis = db_query($fsql);
	if ($resultjenis) {
		while ($datajenis = db_fetch_object($resultjenis)) {
			$rowsrek[] = array (
								 array('data' => $datajenis->kodej,  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $datajenis->uraian,  'width' => '400x','colspan'=>'2',  'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
								 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
								 array('data' => apbd_fn($datajenis->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-weight:bold;'),
								 );
			$total += $datajenis->jumlahx;
			
			//OBYEK
			$sql = 'select mid(k.kodero,1,5) kodeo,o.uraian,sum(jumlah) jumlahx from {anggperkeg} k  left join {obyek} o on mid(k.kodero,1,5)=o.kodeo 
				   where kodekeg=\'%s\' and mid(k.kodero,1,3)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($datajenis->kodej));
			$fsql .= ' group by mid(k.kodero,1,5),o.uraian order by mid(k.kodero,1,5)';
			
			////drupal_set_message( $fsql);
			$resultobyek = db_query($fsql);
			if ($resultobyek) {
				while ($dataobyek = db_fetch_object($resultobyek)) {
					$rowsrek[] = array (
										 array('data' => apbd_format_rek_obyek($dataobyek->kodeo),  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => strtoupper($dataobyek->uraian),  'width' => '400x', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;'),
										 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
										 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
										 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
										 array('data' => apbd_fn($dataobyek->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;text-align:right;font-weight:bold;'),
										 );		

					//REKENING
					$sql = 'select kodero,uraian,jumlah,bintang from {anggperkeg} k where kodekeg=\'%s\' and mid(k.kodero,1,5)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($dataobyek->kodeo));
					
					
					////drupal_set_message( $fsql);
					$fsql .= ' order by k.kodero';
					$result = db_query($fsql);
					if ($result) {
						while ($data = db_fetch_object($result)) {
							
							$rek_bintang = ($keg_bintang==1? 1: $data->bintang);
							//$rek_bintang = $data->bintang;

							if ($rek_bintang ==1) {
								$status_rekekening = '<font color="Red">*</font>';
							} else {
								$status_rekekening = "";
							}							

							$rowsrek[] = array (
											 array('data' => apbd_format_rek_rincianobyek($data->kodero),  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
											 array('data' => $data->uraian . $status_rekekening,  'width' => '400x', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;'),
											 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
											 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
											 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
											 array('data' => apbd_fn($data->jumlah),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
										);

							//DETIL
							if ($tipedok=='rka') {
								$sql = 'select iddetil,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan,harga,total,bintang, pengelompokan from {anggperkegdetil} where total>0 and kodekeg=\'%s\' and kodero=\'%s\' order by nourut asc,iddetil';
								$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($data->kodero));
							} else {	
							
								if ($rek_bintang) {
									$sql = 'select iddetil,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan,harga,total,bintang, pengelompokan from {anggperkegdetil} where total>0 and kodekeg=\'%s\' and kodero=\'%s\' order by nourut asc,iddetil';
								} else {
									$sql = 'select iddetil,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan,harga,total,bintang, pengelompokan from {anggperkegdetil} where kodekeg=\'%s\' and kodero=\'%s\' order by nourut asc,iddetil';
								}
								$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($data->kodero));
							}
								////drupal_set_message($fsql);
							
							$resultdetil = db_query($fsql);
							if ($resultdetil) {
								while ($datadetil = db_fetch_object($resultdetil)) {
									
								
									if ($datadetil->pengelompokan) {
										$unitjumlah = '';
										$volumjumlah = '';
										$hargasatuan = '';
										$bullet = '#';
										
									} else {
										$unitjumlah = $datadetil->unitjumlah . ' ' . $datadetil->unitsatuan;
										$volumjumlah = $datadetil->volumjumlah . ' ' . $datadetil->volumsatuan;
										$hargasatuan = apbd_fn($datadetil->harga);
										$bullet = '•';
										
									}

									$det_bintang = ($rek_bintang==1? 1: $datadetil->bintang);

									if ($det_bintang ==1) {
										$status_detil = '<font color="Red">*</font>';
									} else {
										$status_detil = "";
									}	
							
									$rowsrek[] = array (
														 array('data' => '',  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
														 array('data' => $bullet,  'width' => '15px', 'style' => 'text-align:right;'),
														 array('data' => $datadetil->uraian . $status_detil ,  'width' => '385px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;'),
														 array('data' => $unitjumlah, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;'),
														 array('data' => $volumjumlah, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;'),
														 array('data' => $hargasatuan,  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;'),
														 array('data' => apbd_fn($datadetil->total),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;'),
														 );
									if ($datadetil->pengelompokan) {
										//SUB DETIL
										$sql = 'select idsub,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan, harga,total, bintang from {anggperkegdetilsub} where iddetil=\'%s\' order by nourut asc,idsub';
										$fsql = sprintf($sql, db_escape_string($datadetil->iddetil));
										////drupal_set_message($fsql);
										
										//$no = 0;
										$resultsub = db_query($fsql);
										if ($resultsub) {
											while ($datasub = db_fetch_object($resultsub)) {
												//$no += 1;
												


												$sub_bintang = ($det_bintang==1? 1: $datasub->bintang);

												if ($sub_bintang ==1) {
													$status_sub = '<font color="Red">*</font>';
												} else {
													$status_sub = "";
												}	
																						
												$rowsrek[] = array (
														 array('data' => '',  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
														 array('data' => '',  'width' => '15px', 'style' => 'text-align:right;'),
														 //--
														 array('data' =>  '- ' . $datasub->uraian . $status_sub,  'width' => '385px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;font-style: italic;'),
														 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
														 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
														 array('data' => apbd_fn($datasub->harga),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
														 array('data' => apbd_fn($datasub->total),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
														 );												
												//$$$
											}
										}
										
										//###
									}
								}
							}	//DETIL
							
							//CATATAN
							if (($tipedok!='dpa') and (variable_get("cetakverifikasirka", 0)==1)) {
								$sql_r = "select username,jawaban from {anggperkegverifikasi} where kodekeg='" . $kodekeg . "' and kodero='" . $data->kodero . "' order by username";
								$res_r = db_query($sql_r);
								
								while ($data_cat= db_fetch_object($res_r)) {	
									//$bullet = '<img src="/files/icon/info_red_16.png">';
									$bullet = '<font color="Red">*</font>';
									$catatan = $data_cat->username . ': <font color="Red">' . $data_cat->jawaban . '</font>';

									$rowsrek[] = array (
														 array('data' => '',  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
														 array('data' => $bullet,  'width' => '15px', 'style' => 'text-align:center;'),
														 array('data' => $catatan,  'width' => '385px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;'),
														 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;'),
														 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;'),
														 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;'),
														 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;'),
														 );							
								}
							}	//TIPE DOK	
							
						}
					}
										 
				////////
				}
			}
		}
	}
	
	$rowsrek[] = array (
						 array('data' => 'JUMLAH BELANJA',  'width'=> '775px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($total),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 );
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}


function GenReportFormContentBintang($kodekeg) {
	
	$bintangkegiatan = false;
	$sql = 'select anggaran from {kegiatanskpd} where kodekeg=\'%s\'';
	$fsql = sprintf($sql, db_escape_string($kodekeg));
	$res = db_query($fsql);
	if ($res) {
		if ($data = db_fetch_object($res)) {
			$bintangkegiatan = ($data->anggaran==0);
		}
	}

	$jumrincian = 0;
	$sql = 'select count(iddetil) jumrincian from {anggperkegdetil} where kodekeg=\'%s\'';
	$fsql = sprintf($sql, db_escape_string($kodekeg));
	$res = db_query($fsql);
	if ($res) {
		if ($data = db_fetch_object($res)) {
			$jumrincian = $data->jumrincian;
		}
	}
	
	$sql = 'select count(idsub) jumrincian from {anggperkegdetilsub} s inner join {anggperkegdetil} d
			on s.iddetil=d.iddetil where d.kodekeg=\'%s\'';
	$fsql = sprintf($sql, db_escape_string($kodekeg));
	$res = db_query($fsql);
	if ($res) {
		if ($data = db_fetch_object($res)) {
			$jumrincian += $data->jumrincian;
		}
	}
	
	if ($jumrincian > 350) {
		set_time_limit(0);
		ini_set('memory_limit', '640M');
	}
	
	$total=0;

	$headersrek[] = array (
						 array('data' => 'KODE',  'width'=> '75px', 'rowspan'=>'2','style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 
						 array('data' => 'URAIAN',  'width' => '400x','rowspan'=>'2','colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'RINCIAN PERHITUNGAN', 'width' => '300px','colspan'=>'3','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'JUMLAH TOTAL',  'width' => '100px', 'rowspan'=>'2','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 );
	$headersrek[] = array (

						 array('data' => 'Satuan', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'Volume', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'Harga Satuan',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 );
						 
	 //JENIS
	$where = ' where k.kodekeg=\'%s\'';
	$sql = 'select mid(k.kodero,1,3) kodej,j.uraian,sum(jumlah) jumlahx from {anggperkeg} k  left join {jenis} j on mid(k.kodero,1,3)=j.kodej ' . $where;
	$fsql = sprintf($sql, db_escape_string($kodekeg));
	$fsql .= ' group by mid(k.kodero,1,3),j.uraian order by mid(k.kodero,1,3)';
	
	////drupal_set_message( $fsql);
	$resultjenis = db_query($fsql);
	if ($resultjenis) {
		while ($datajenis = db_fetch_object($resultjenis)) {
			$rowsrek[] = array (
								 array('data' => $datajenis->kodej,  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $datajenis->uraian,  'width' => '400x','colspan'=>'2',  'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
								 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
								 array('data' => apbd_fn($datajenis->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-weight:bold;'),
								 );
			$total += $datajenis->jumlahx;
			
			//OBYEK
			$sql = 'select mid(k.kodero,1,5) kodeo,o.uraian,sum(jumlah) jumlahx from {anggperkeg} k  left join {obyek} o on mid(k.kodero,1,5)=o.kodeo 
				   where kodekeg=\'%s\' and mid(k.kodero,1,3)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($datajenis->kodej));
			$fsql .= ' group by mid(k.kodero,1,5),o.uraian order by mid(k.kodero,1,5)';
			
			////drupal_set_message( $fsql);
			$resultobyek = db_query($fsql);
			if ($resultobyek) {
				while ($dataobyek = db_fetch_object($resultobyek)) {
					$rowsrek[] = array (
										 array('data' => apbd_format_rek_obyek($dataobyek->kodeo),  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => strtoupper($dataobyek->uraian),  'width' => '400x', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;'),
										 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
										 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
										 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
										 array('data' => apbd_fn($dataobyek->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;text-align:right;font-weight:bold;'),
										 );		

					//REKENING
					$sql = 'select kodero,uraian,jumlah,anggaran from {anggperkeg} k where kodekeg=\'%s\' and mid(k.kodero,1,5)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($dataobyek->kodeo));
					
					////drupal_set_message( $fsql);
					$fsql .= ' order by k.kodero';
					$result = db_query($fsql);
					if ($result) {
						while ($data = db_fetch_object($result)) {
							
							$alldetilbintang = false;
							if ($bintangkegiatan) {
								$statusrek = '<font color="Red">*</font>';
								$alldetilbintang = true;
								
							} else {
								if (($data->anggaran==0) and ($data->jumlah>0)) {
									
									$statusrek = '<font color="Red">*</font>';
									$alldetilbintang = true;

								} else {
									
									$statusrek = '';
	
								}
							}
							
							$rowsrek[] = array (
											 array('data' => apbd_format_rek_rincianobyek($data->kodero),  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
											 array('data' => $data->uraian . $statusrek,  'width' => '400x', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;'),
											 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
											 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
											 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
											 array('data' => apbd_fn($data->jumlah),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
										);

							//DETIL
							$sql = 'select iddetil,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan,harga,total,anggaran, pengelompokan from {anggperkegdetil} where kodekeg=\'%s\' and kodero=\'%s\' order by nourut asc,iddetil';
							$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($data->kodero));
							////drupal_set_message($fsql);
							
							$resultdetil = db_query($fsql);
							if ($resultdetil) {
								while ($datadetil = db_fetch_object($resultdetil)) {
									
									$statusdetil = '';
									if ($alldetilbintang) {
										$statusdetil = '<font color="Red">*</font>';
										$allsubbintang = true;
										
									} else {
										if (($datadetil->anggaran==0) and ($datadetil->total>0)) {
											$statusdetil = '<font color="Red">*</font>';

											$allsubbintang = true;

										} else {
											$statusdetil = '';
											$allsubbintang = false;
											
										}
									}								
								
									if ($datadetil->pengelompokan) {
										$unitjumlah = '';
										$volumjumlah = '';
										$hargasatuan = '';
										$bullet = '#';
										
									} else {
										$unitjumlah = $datadetil->unitjumlah . ' ' . $datadetil->unitsatuan;
										$volumjumlah = $datadetil->volumjumlah . ' ' . $datadetil->volumsatuan;
										$hargasatuan = apbd_fn($datadetil->harga);
										$bullet = '•';
										
									}
									$rowsrek[] = array (
														 array('data' => '',  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
														 array('data' => $bullet,  'width' => '15px', 'style' => 'text-align:right;'),
														 array('data' => $datadetil->uraian . $statusdetil ,  'width' => '385px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;'),
														 array('data' => $unitjumlah, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;'),
														 array('data' => $volumjumlah, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;'),
														 array('data' => $hargasatuan,  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;'),
														 array('data' => apbd_fn($datadetil->total),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;'),
														 );
									if ($datadetil->pengelompokan) {
										//SUB DETIL
										$sql = 'select idsub,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan, harga,total, anggaran from {anggperkegdetilsub} where iddetil=\'%s\' order by nourut asc,idsub';
										$fsql = sprintf($sql, db_escape_string($datadetil->iddetil));
										////drupal_set_message($fsql);
										
										//$no = 0;
										$resultsub = db_query($fsql);
										if ($resultsub) {
											while ($datasub = db_fetch_object($resultsub)) {
												//$no += 1;
												
												$statussub = '';
												if ($allsubbintang) {
													$statussub = '<font color="Red">*</font>';
													
												} else {
													if ($datasub->total==$datasub->anggaran)
														$statussub = '';
													else {
														
														$statussub = '<font color="Red">*</font>';
														
													}
												}												
												
												$rowsrek[] = array (
														 array('data' => '',  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
														 array('data' => '',  'width' => '15px', 'style' => 'text-align:right;'),
														 array('data' =>  '- ' . $datasub->uraian . $statussub,  'width' => '385px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;font-style: italic;'),
														 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
														 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
														 array('data' => apbd_fn($datasub->harga),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
														 array('data' => apbd_fn($datasub->total),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
														 );												
												//$$$
											}
										}
										
										//###
									}
								}
							}	//DETIL
							
							//CATATAN
							if (($tipedok!='dpa') and (variable_get("cetakverifikasirka", 0)==1)) {
								$sql_r = "select username,jawaban from {anggperkegverifikasi} where kodekeg='" . $kodekeg . "' and kodero='" . $data->kodero . "' order by username";
								$res_r = db_query($sql_r);
								
								while ($data_cat= db_fetch_object($res_r)) {	
									//$bullet = '<img src="/files/icon/info_red_16.png">';
									$bullet = '<font color="Red">*</font>';
									$catatan = $data_cat->username . ': <font color="Red">' . $data_cat->jawaban . '</font>';

									$rowsrek[] = array (
														 array('data' => '',  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
														 array('data' => $bullet,  'width' => '15px', 'style' => 'text-align:center;'),
														 array('data' => $catatan,  'width' => '385px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;'),
														 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;'),
														 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;'),
														 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;'),
														 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;'),
														 );							
								}
							}	//TIPE DOK	
							
						}
					}
										 
				////////
				}
			}
		}
	}
	
	$rowsrek[] = array (
						 array('data' => 'JUMLAH BELANJA',  'width'=> '775px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($total),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 );
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}

function GenReportFormContentDraft($kodekeg) {


	$jumrincian = 0;
	$sql = 'select count(iddetil) jumrincian from {anggperkegdetildraft} where kodekeg=\'%s\'';
	$fsql = sprintf($sql, db_escape_string($kodekeg));
	$res = db_query($fsql);
	if ($res) {
		if ($data = db_fetch_object($res)) {
			$jumrincian = $data->jumrincian;
		}
	}
	
	$sql = 'select count(idsub) jumrincian from {anggperkegdetilsubdraft} s inner join {anggperkegdetildraft} d
			on s.iddetil=d.iddetil where d.kodekeg=\'%s\'';
	$fsql = sprintf($sql, db_escape_string($kodekeg));
	$res = db_query($fsql);
	if ($res) {
		if ($data = db_fetch_object($res)) {
			$jumrincian += $data->jumrincian;
		}
	}
	
	if ($jumrincian > 350) {
		set_time_limit(0);
		ini_set('memory_limit', '640M');
	}
	
	$total=0;

	$headersrek[] = array (
						 array('data' => 'KODE',  'width'=> '75px', 'rowspan'=>'2','style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 
						 array('data' => 'URAIAN',  'width' => '400x','rowspan'=>'2','colspan'=>'2', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'RINCIAN PERHITUNGAN', 'width' => '300px','colspan'=>'3','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 array('data' => 'JUMLAH TOTAL',  'width' => '100px', 'rowspan'=>'2','style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 2px solid black; text-align:center;'),
						 );
	$headersrek[] = array (

						 array('data' => 'Satuan', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'Volume', 'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 array('data' => 'Harga Satuan',  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
						 );
						 
	 //JENIS
	$where = ' where k.kodekeg=\'%s\'';
	$sql = 'select mid(k.kodero,1,3) kodej,j.uraian,sum(jumlah) jumlahx from {anggperkegdraft} k  left join {jenis} j on mid(k.kodero,1,3)=j.kodej ' . $where;
	$fsql = sprintf($sql, db_escape_string($kodekeg));
	$fsql .= ' group by mid(k.kodero,1,3),j.uraian order by mid(k.kodero,1,3)';
	
	////drupal_set_message( $fsql);
	$resultjenis = db_query($fsql);
	if ($resultjenis) {
		while ($datajenis = db_fetch_object($resultjenis)) {
			$rowsrek[] = array (
								 array('data' => $datajenis->kodej,  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
								 array('data' => $datajenis->uraian,  'width' => '400x','colspan'=>'2',  'style' => ' border-right: 1px solid black; text-align:left;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
								 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
								 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
								 array('data' => apbd_fn($datajenis->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1.5px solid black; text-align:right;font-weight:bold;'),
								 );
			$total += $datajenis->jumlahx;
			
			//OBYEK
			$sql = 'select mid(k.kodero,1,5) kodeo,o.uraian,sum(jumlah) jumlahx from {anggperkegdraft} k  left join {obyek} o on mid(k.kodero,1,5)=o.kodeo 
				   where kodekeg=\'%s\' and mid(k.kodero,1,3)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($datajenis->kodej));
			$fsql .= ' group by mid(k.kodero,1,5),o.uraian order by mid(k.kodero,1,5)';
			
			////drupal_set_message( $fsql);
			$resultobyek = db_query($fsql);
			if ($resultobyek) {
				while ($dataobyek = db_fetch_object($resultobyek)) {
					$rowsrek[] = array (
										 array('data' => apbd_format_rek_obyek($dataobyek->kodeo),  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
										 array('data' => strtoupper($dataobyek->uraian),  'width' => '400x', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;'),
										 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
										 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
										 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
										 array('data' => apbd_fn($dataobyek->jumlahx),  'width' => '100px', 'style' => ' border-right: 1px solid black; border-bottom: 1px solid black;text-align:right;font-weight:bold;'),
										 );		

					//REKENING
					$sql = 'select kodero,uraian,jumlah from {anggperkegdraft} k where kodekeg=\'%s\' and mid(k.kodero,1,5)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($dataobyek->kodeo));
					
					////drupal_set_message( $fsql);
					$fsql .= ' order by k.kodero';
					$result = db_query($fsql);
					if ($result) {
						while ($data = db_fetch_object($result)) {
						$rowsrek[] = array (
											 array('data' => apbd_format_rek_rincianobyek($data->kodero),  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
											 array('data' => $data->uraian,  'width' => '400x', 'colspan'=>'2', 'style' => ' border-right: 1px solid black; text-align:left;'),
											 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
											 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
											 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;'),
											 array('data' => apbd_fn($data->jumlah),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:bold;'),
											 );
							//DETIL
							$sql = 'select iddetil,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan,harga,total, pengelompokan from {anggperkegdetildraft} where kodekeg=\'%s\' and kodero=\'%s\' order by nourut asc,iddetil';
							$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($data->kodero));
							////drupal_set_message($fsql);
							
							$resultdetil = db_query($fsql);
							if ($resultdetil) {
								while ($datadetil = db_fetch_object($resultdetil)) {
									
									if ($datadetil->pengelompokan) {
										$unitjumlah = '';
										$volumjumlah = '';
										$hargasatuan = '';
										$bullet = '#';
										
									} else {
										$unitjumlah = $datadetil->unitjumlah . ' ' . $datadetil->unitsatuan;
										$volumjumlah = $datadetil->volumjumlah . ' ' . $datadetil->volumsatuan;
										$hargasatuan = apbd_fn($datadetil->harga);
										$bullet = '•';
										
									}
									$rowsrek[] = array (
														 array('data' => '',  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
														 array('data' => $bullet,  'width' => '15px', 'style' => 'text-align:right;'),
														 array('data' => $datadetil->uraian,  'width' => '385px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;'),
														 array('data' => $unitjumlah, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;'),
														 array('data' => $volumjumlah, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;'),
														 array('data' => $hargasatuan,  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;'),
														 array('data' => apbd_fn($datadetil->total),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;'),
														 );
									if ($datadetil->pengelompokan) {
										//SUB DETIL
										$sql = 'select idsub,uraian,unitjumlah,unitsatuan,volumjumlah,volumsatuan, harga,total from {anggperkegdetilsubdraft} where iddetil=\'%s\' order by nourut asc,idsub';
										$fsql = sprintf($sql, db_escape_string($datadetil->iddetil));
										////drupal_set_message($fsql);
										
										//$no = 0;
										$resultsub = db_query($fsql);
										if ($resultsub) {
											while ($datasub = db_fetch_object($resultsub)) {
												//$no += 1;
												$rowsrek[] = array (
														 array('data' => '',  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
														 array('data' => '',  'width' => '15px', 'style' => 'text-align:right;'),
														 array('data' =>  '- ' . $datasub->uraian,  'width' => '385px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;font-style: italic;'),
														 array('data' => $datasub->unitjumlah . ' ' . $datasub->unitsatuan, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
														 array('data' => $datasub->volumjumlah . ' ' . $datasub->volumsatuan, 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;font-style: italic;'),
														 array('data' => apbd_fn($datasub->harga),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
														 array('data' => apbd_fn($datasub->total),  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;font-style: italic;'),
														 );												
												//$$$
											}
										}
										
										//###
									}
								}
							}	//DETIL
							
							//CATATAN
							if ($tipedok!='dpa') {
								$sql_r = "select username,jawaban from {anggperkegverifikasi} where kodekeg='" . $kodekeg . "' and kodero='" . $data->kodero . "' order by username";
								$res_r = db_query($sql_r);
								
								while ($data_cat= db_fetch_object($res_r)) {	
									//$bullet = '<img src="/files/icon/info_red_16.png">';
									$bullet = '<font color="Red">*</font>';
									$catatan = $data_cat->username . ': <font color="Red">' . $data_cat->jawaban . '</font>';

									$rowsrek[] = array (
														 array('data' => '',  'width'=> '75px', 'style' => 'border-left: 1px solid black;  border-right: 1px solid black; text-align:left;'),
														 array('data' => $bullet,  'width' => '15px', 'style' => 'text-align:center;'),
														 array('data' => $catatan,  'width' => '385px', 'style' => ' border-right: 1px solid black; text-align:left;font-weight:lighter;'),
														 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;'),
														 array('data' => '', 'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:center;font-weight:lighter;'),
														 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;'),
														 array('data' => '',  'width' => '100px', 'style' => ' border-right: 1px solid black; text-align:right;font-weight:lighter;'),
														 );							
								}
							}	//TIPE DOK	
							
						}
					}
										 
				////////
				}
			}
		}
	}
	
	$rowsrek[] = array (
						 array('data' => 'JUMLAH BELANJA',  'width'=> '775px',  'colspan'=>'6',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 array('data' => apbd_fn($total),  'width' => '100px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; border-top: 1px solid black; text-align:right; font-weight:bold;'),
						 );
	$opttbl = array('cellspacing'=>'1', 'cellpadding'=>'1', 'border' => '0');

	$output .= theme_box('', apbd_theme_table($headersrek, $rowsrek, $opttbl));
	
	$output .= $toutput;
	
	return $output;
	
}


function GenReportFormFooter($kodekeg, $tipedok) {
	
	if ($tipedok=='dpa') {
		$pquery = sprintf("select tw1, tw2, tw3, tw4, total, anggaran from {kegiatanskpd} where kodekeg='%s'", db_escape_string($kodekeg)) ;
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			
			$tw1 = $data->tw1;
			$tw2 = $data->tw2;
			$tw3 = $data->tw3;
			$tw4 = $data->tw4;

			
		}

		$pquery = sprintf("select dpatgl, budnama, budnip, budjabatan from {setupapp} where tahun='%s'", variable_get('apbdtahun', 0)) ;
		////drupal_set_message($pquery);
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			$budnama = $data->budnama;
			$budnip = $data->budnip;
			$budjabatan = $data->budjabatan;
			$dpatgl = $data->dpatgl;
		}
		
		if (!isSuperuser())
			$str_tag = 'HANYA UNTUK REVIEW';
		else
			$str_tag = '';
		
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '300px',  'colspan'=>'3',  'style' => 'text-align:center'),
							 array('data' => '',  'width'=> '275px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => '',  'width' => '300px', 'style' => 'text-align:center;'),
							 );

		$rowsfooter[] = array (
							 array('data' => 'RENCANA BELANJA TRI WULAN',  'width'=> '300px',  'colspan'=>'3',  'style' => 'border-left: 1px solid black; border-top: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => '',  'width'=> '275px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => 'Jepara, ' . $dpatgl,  'width' => '300px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'Tri Wulan',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:center'),
							 array('data' => 'Anggaran (Rp)',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:center'),
							 array('data' => 'Keterangan',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black;  border-right: 1px solid black;text-align:center'),
							 array('data' => '',  'width'=> '275px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => 'Mengesahkan,',  'width' => '300px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'I',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:center'),
							 array('data' => apbd_fn($tw1),  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; border-right: 1px solid black; text-align:right'),
							 array('data' => $str_tag,  'width'=> '275px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => $budjabatan ,  'width' => '300px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'II',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:center'),
							 array('data' => apbd_fn($tw2),  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; border-right: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '275px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => $str_tag,  'width' => '300px', 'style' => 'text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => 'III',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:center'),
							 array('data' => apbd_fn($tw3),  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; border-right: 1px solid black;text-align:right'),
							 array('data' => '',  'width'=> '275px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => $str_tag,  'width' => '300px', 'style' => 'text-align:center;text-decoration: underline'),
							 );	
		$rowsfooter[] = array (
							 array('data' => 'IV',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black; text-align:center'),
							 array('data' => apbd_fn($tw4),  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black; text-align:right'),
							 array('data' => '',  'width'=> '100px', 'style' => 'border-left: 1px solid black; border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;  text-align:right'),
							 array('data' => '',  'width'=> '275px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => $budnama,  'width' => '300px', 'style' => 'text-align:center; text-decoration: underline;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '100px', 'style' => 'text-align:center'),
							 array('data' => '',  'width'=> '100px', 'style' => 'text-align:right'),
							 array('data' => '',  'width'=> '100px', 'style' => 'text-align:right'),
							 array('data' => '',  'width'=> '275px',  'colspan'=>'2',  'style' => 'text-align:center'),
							 array('data' => 'NIP. ' . $budnip,  'width' => '300px', 'style' => 'text-align:center;'),
							 );
	} else {
		$namauk = '';
		$pimpinannama='';
		$pimpinannip='';
		$pimpinanjabatan='';
		$pquery = sprintf("select u.kodedinas, u.namauk, u.pimpinannama, u.pimpinannip, u.pimpinanjabatan, k.plafon, k.total from {unitkerja} u left join {kegiatanskpd} k 
				  on u.kodeuk=k.kodeuk where k.kodekeg='%s'", db_escape_string($kodekeg)) ;
		$pres = db_query($pquery);
		if ($data = db_fetch_object($pres)) {
			
			$namauk = $data->namauk;
			$pimpinannama=$data->pimpinannama;
			$pimpinannip=$data->pimpinannip;
			$pimpinanjabatan=$data->pimpinanjabatan;
			$plafon = $data->plafon;
			$total = $data->total;
			
		}
		if ($total > $plafon)	
			$strplafon = '!!!ANGGARAN MELEBIHI PLAFON, HARAP DIPERBAIKI!!!';

		$catatan='';
		$pquery = sprintf("select count(kodero) jmlrek from {anggperkeg} where (jumlah mod 1000)>0 and  kodekeg='%s'", db_escape_string($kodekeg));
		$pres = db_query($pquery);
		////drupal_set_message($pquery); 
		if ($data = db_fetch_object($pres)) {
			if ($data->jmlrek > 0)	$catatan = '!!!ADA SEJUMLAH REKENING YANG TIDAK BULAT PER 1000, HARAP DIPERBAIKI!!!';
		}

		//CATATAN
		if (($tipedok!='dpa') and (variable_get("cetakverifikasirka", 0)==1)) {

			$sql_r = "select username,persetujuan,jawaban from {kegiatanverifikasi} where kodekeg='" . $kodekeg . "' order by username";
			$res_r = db_query($sql_r);
			
			while ($data_r= db_fetch_object($res_r)) {	
				if ($data_r->jawaban!='') {
					if ($data_r->persetujuan==0) {
						//$image = "<img src='/files/verify/fer_no.png'>";
						$color = 'red';
					} else if ($data_r->persetujuan==2) {
						//$image = "<img src='/files/verify/fer_warning.png'>";
						$color = 'orange';
					} else {
						//$image = "<img src='/files/verify/fer_ok.png'>";
						$color = 'green';
					}
					
					$catatan .= '('. $data_r->username . ') <font color="' . $color . '">' . $data_r->jawaban . '; </font>';
				}	
			}		
		}
		
		$rowsfooter[] = array (
							 array('data' => 'CATATAN',  'width'=> '675px',  'colspan'=>'5',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => 'KEPALA SKPD',  'width' => '200px', 'style' => 'border-bottom: 1px solid black; border-right: 1px solid black; text-align:center;'),
							 );
		$rowsfooter[] = array (
							 array('data' => $catatan,  'width'=> '675px',  'colspan'=>'5',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:left'),
							 array('data' => '',  'width' => '200px', 'style' => 'border-right: 1px solid black; text-align:right;'),
							 );
		$rowsfooter[] = array (
							 array('data' => $strplafon,  'width'=> '675px',  'colspan'=>'5',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => '',  'width' => '200px', 'style' => 'border-right: 1px solid black; text-align:right;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '675px',  'colspan'=>'5',  'style' => 'border-left: 1px solid black; border-right: 1px solid black; text-align:center'),
							 array('data' => $pimpinannama,  'width' => '200px', 'style' => 'border-right: 1px solid black; text-align:center; text-decoration: underline;'),
							 );
		$rowsfooter[] = array (
							 array('data' => '',  'width'=> '675px',  'colspan'=>'5',  'style' => 'border-left: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black; text-align:center'),
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

function GenExcel($kodekeg) {

	error_reporting(E_ALL);

	set_time_limit(0);
	ini_set('memory_limit', '640M');

	ini_set('display_errors', TRUE);
	ini_set('display_startup_errors', TRUE);
	//date_default_timezone_set('Europe/London');

	if (PHP_SAPI == 'cli')
		die('This example should only be run from a Web Browser');

	/** Include PHPExcel */
	require_once 'files/PHPExcel/Classes/PHPExcel.php';

	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();
	// Set document properties
	$objPHPExcel->getProperties()->setCreator("SIPKD Anggaran Online")
								 ->setLastModifiedBy("SIPKD Anggaran Online")
								 ->setTitle("Office 2007 XLSX Test Document")
								 ->setSubject("Office 2007 XLSX Test Document")
								 ->setDescription("Excel document generated from SIPKD Anggaran Online.")
								 ->setKeywords("office 2007 SIPKD Anggaran Online openxml php")
								 ->setCategory("SIPKD Anggaran Analisa");
	// Add Header
	$row = 1;
	
	//HEADER
	$skpd = '';
	$pimpinannama='';
	$pimpinannip='';
	$pimpinanjabatan='';
	$pquery = sprintf("select kodedinas, namauk, pimpinannama, pimpinannip, pimpinanjabatan from {unitkerja} u left join {kegiatanskpd} k 
			  on u.kodeuk=k.kodeuk where k.kodekeg='%s'", db_escape_string($kodekeg)) ;
	////drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		$kodedinas = $data->kodedinas;
		$skpd = $kodedinas . ' - ' . $data->namauk;
		$pimpinannama=$data->pimpinannama;
		$pimpinannip=$data->pimpinannip;
		$pimpinanjabatan=$data->pimpinanjabatan;
	}

	$where = ' where k.kodekeg=\'%s\'';
	$pquery = sprintf('select k.kodekeg, right(k.kodekeg,3) nomorkeg, k.tahun, k.kodepro, k.kodeuk, k.kodesuk, 
				k.kegiatan, k.jenis, k.lokasi, k.programsasaran, k.programtarget, k.masukansasaran, k.masukantarget,
				k.keluaransasaran, k.keluarantarget, k.hasilsasaran,  k.hasiltarget, k.total, k.plafon, 
				k.totalsebelum, k.totalsesudah, k.waktupelaksanaan, k.sumberdana1, k.sumberdana2, 
				k.sumberdana1rp, k.sumberdana2rp, k.latarbelakang, k.kelompoksasaran, p.program,
				p.kodepro, p.kodeu, u.urusan, u.kodef, u.fungsi, k.tw1, k.tw2, k.tw3, k.tw4 from {kegiatanskpd} k left join {program} p 
				on (k.kodepro = p.kodepro) left join {urusan} u on p.kodeu=u.kodeu ' . $where, db_escape_string($kodekeg));
	////drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {

		$fungsi = $data->kodef . ' - ' . $data->fungsi;
		$urusan = $data->kodeu . ' - ' . $data->urusan;
		$program = $data->kodeu . '.' . $data->kodepro . ' - ' . $data->program;
		$kegiatan = $kodedinas . '.' . $data->kodeu . '.' . $data->kodepro . '.' . $data->nomorkeg . ' - ' .  $data->kegiatan;
		
		$jenis = $data->jenis;
		$tahun = $data->tahun;
		
		$lokasi = str_replace('||',', ', $data->lokasi);
		$programsasaran = $data->programsasaran;
		$programtarget = $data->programtarget;
		$masukansasaran = $data->masukansasaran;
		//$masukantarget = $data->masukantarget;
		$keluaransasaran = $data->keluaransasaran;
		$keluarantarget = $data->keluarantarget;
		$hasilsasaran = $data->hasilsasaran;
		$hasiltarget = $data->hasiltarget;
		$total = $data->total;
		$masukantarget = 'Rp ' . apbd_fn($data->total);
		$plafon = $data->plafon;
		$waktupelaksanaan = $data->waktupelaksanaan;
		$sumberdana1 = $data->sumberdana1;
		$sumberdana2 = $data->sumberdana2;
		$sumberdana1rp = $data->sumberdana1rp;
		$sumberdana2rp = $data->sumberdana2rp;
		$latarbelakang = $data->latarbelakang;
		$kelompoksasaran = $data->kelompoksasaran;
		$tw1 = $data->tw1;
		$tw2 = $data->tw2;
		$tw3 = $data->tw3;
		$tw4 = $data->tw4;
		
	}	
	 
	//$kabupaten = variable_get('apbdwilayah', '');//'KABUPATEN JEPARA'; //setup
	$row = 1;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $row , 'KEGIATAN BELANJA LANGSUNG')
				->setCellValue('B' . $row , '')
				->setCellValue('C' . $row , '');
	
	$row = $row + 2;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $row , 'Fungsi')
				->setCellValue('B' . $row , $fungsi)
				->setCellValue('C' . $row , '');
	$row++;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $row , 'Urusan Pemerintahan')
				->setCellValue('B' . $row , $urusan)
				->setCellValue('C' . $row , '');
	$row++;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $row , 'Organisasi')
				->setCellValue('B' . $row , $skpd)
				->setCellValue('C' . $row , '');
	$row++;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $row , 'Program')
				->setCellValue('B' . $row , $program)
				->setCellValue('C' . $row , '');
	$row++;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $row , 'Kegiatan')
				->setCellValue('B' . $row , $kegiatan)
				->setCellValue('C' . $row , '');
	$row++;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $row , 'Lokasi')
				->setCellValue('B' . $row , $lokasi)
				->setCellValue('C' . $row , '');
	$row++;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $row , 'Anggaran')
				->setCellValue('B' . $row , $total)
				->setCellValue('C' . $row , '');
	
	$row = $row+2;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $row , 'INDIKATOR')
				->setCellValue('B' . $row , 'TOLOK UKUR KINERJA')
				->setCellValue('C' . $row , 'TARGET KINERJA');
	$row++;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $row , 'Capaian Program')
				->setCellValue('B' . $row , $programsasaran)
				->setCellValue('C' . $row , $programtarget);
	$row++;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $row , 'Masukan')
				->setCellValue('B' . $row , $masukansasaran)
				->setCellValue('C' . $row , $masukantarget);
	$row++;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $row , 'Keluaran')
				->setCellValue('B' . $row , $keluaransasaran)
				->setCellValue('C' . $row , $keluarantarget);
	$row++;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $row , 'Hasil')
				->setCellValue('B' . $row , $hasilsasaran)
				->setCellValue('C' . $row , $hasiltarget);

	$row = $row + 2;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $row , 'Kelompok Sasaran')
				->setCellValue('B' . $row , $kelompoksasaran)
				->setCellValue('C' . $row , '');

	$row = $row + 2;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $row , 'KODE')
				->setCellValue('B' . $row , 'URAIAN')
				->setCellValue('C' . $row , 'JUMLAH');


	//JENIS
	$total = 0;
	$where = ' where k.kodekeg=\'%s\'';
	$sql = 'select mid(k.kodero,1,3) kodej,j.uraian,sum(jumlah) jumlahx from {anggperkeg} k  left join {jenis} j on mid(k.kodero,1,3)=j.kodej ' . $where;
	$fsql = sprintf($sql, db_escape_string($kodekeg));
	$fsql .= ' group by mid(k.kodero,1,3),j.uraian order by mid(k.kodero,1,3)';
	
	////drupal_set_message( $fsql);
	$resultjenis = db_query($fsql);
	if ($resultjenis) {
		while ($datajenis = db_fetch_object($resultjenis)) {
			$total += $datajenis->jumlahx;

			$row++;
			$objPHPExcel->setActiveSheetIndex(0)
						->setCellValue('A' . $row , "'" . $datajenis->kodej)
						->setCellValue('B' . $row , $datajenis->uraian)
						->setCellValue('C' . $row , $datajenis->jumlahx);
				
			    
			//OBYEK
			$sql = 'select mid(k.kodero,1,5) kodeo,o.uraian,sum(jumlah) jumlahx from {anggperkeg} k  left join {obyek} o on mid(k.kodero,1,5)=o.kodeo 
				   where kodekeg=\'%s\' and mid(k.kodero,1,3)=\'%s\'';
			$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($datajenis->kodej));
			$fsql .= ' group by mid(k.kodero,1,5),o.uraian order by mid(k.kodero,1,5)';
			
			////drupal_set_message( $fsql);
			$resultobyek = db_query($fsql);
			if ($resultobyek) {
				while ($dataobyek = db_fetch_object($resultobyek)) {

					$row++;
					$objPHPExcel->setActiveSheetIndex(0)
								->setCellValue('A' . $row , "'" . $dataobyek->kodeo)
								->setCellValue('B' . $row , $dataobyek->uraian)
								->setCellValue('C' . $row , $dataobyek->jumlahx);
				
					//REKENING
					$sql = 'select kodero,uraian,jumlah from {anggperkeg} k where kodekeg=\'%s\' and mid(k.kodero,1,5)=\'%s\'';
					$fsql = sprintf($sql, db_escape_string($kodekeg), db_escape_string($dataobyek->kodeo));
					
					////drupal_set_message( $fsql);
					$fsql .= ' order by k.kodero';
					$result = db_query($fsql);
					if ($result) {
						while ($data = db_fetch_object($result)) {
							$row++;
							$objPHPExcel->setActiveSheetIndex(0)
										->setCellValue('A' . $row , "'" . $data->kodero)
										->setCellValue('B' . $row , $data->uraian)
										->setCellValue('C' . $row , $data->jumlah);
							
							
						}
					}
										 
				////////
				}
			}
		}
	}

	$row++;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $row , 'TOTAL')
				->setCellValue('B' . $row , '')
				->setCellValue('C' . $row , $total);	

	$row = $row + 2;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $row , '')
				->setCellValue('B' . $row , '')
				->setCellValue('C' . $row , 'KEPALA SKPD');	
	$row++;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $row , '')
				->setCellValue('B' . $row , '')
				->setCellValue('C' . $row , $pimpinannama);	
	$row = $row + 2;
	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A' . $row , '')
				->setCellValue('B' . $row , '')
				->setCellValue('C' . $row , 'NIP ' . $pimpinannip);	
				
		

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('RKA SKPD');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
$fname = 'RKA SKPD - ' . $kegiatan . '.xlsx';
header('Content-Disposition: attachment;filename=' . $fname);
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;	
}

function GenCSV($kodekeg) {

	$where = ' where k.kodekeg=\'%s\'';
	$pquery = sprintf('select k.kodekeg, right(k.kodekeg,3) nomorkeg, k.tahun, k.kodepro, k.kodeuk, k.kodesuk, 
				k.kegiatan, k.jenis, k.lokasi, k.programsasaran, k.programtarget, k.masukansasaran, k.masukantarget,
				k.keluaransasaran, k.keluarantarget, k.hasilsasaran,  k.hasiltarget, k.total, k.plafon, 
				k.totalsebelum, k.totalsesudah, k.waktupelaksanaan, k.sumberdana1, k.sumberdana2, 
				k.sumberdana1rp, k.sumberdana2rp, k.latarbelakang, k.kelompoksasaran, p.program,
				p.kodepro, p.kodeu, u.urusan, u.kodef, u.fungsi, k.tw1, k.tw2, k.tw3, k.tw4, uk.kodedinas, uk.namauk from {kegiatanskpd} k left join {program} p 
				on (k.kodepro = p.kodepro) left join {urusan} u on p.kodeu=u.kodeu inner join {unitkerja} uk on k.kodeuk=uk.kodeuk ' . $where, db_escape_string($kodekeg));
	////drupal_set_message($pquery);
	$pres = db_query($pquery);
	if ($data = db_fetch_object($pres)) {
		
		$kodedinas = $data->kodedinas;
		$namadinas = $data->namauk;
		$kode = $kodedinas . '.' . $data->kodeu . '.' . $data->kodepro . '.' . $data->nomorkeg;
		$kegiatan = $data->kegiatan;
		
		$lokasi = str_replace('||',', ', $data->lokasi);
		
		$keluaransasaran = $data->keluaransasaran;
		$keluarantarget = $data->keluarantarget;
		$total = $data->total;
		
		$sumberdana1 = $data->sumberdana1;
		
	}	

	$fname = 'RKA SKPD - ' . $kegiatan . '.csv';
	
	drupal_set_header('Content-Type: text/csv');
	drupal_set_header('Content-Disposition: attachment; filename=' . $fname);
	
	/*
	$count = mysql_num_fields($result);  
	for($i = 0; $i < $count; $i++){
		$header[] = mysql_field_name($result, $i);
	}
	*/

	$header = array ('Kode Dinas', 'Nama Dinas', 'Kode Kegiatan', 'Nama Kegiatan', 'Lokasi', 'Sumber Dana', 'Sasaran', 'Target', 'Anggaran');
  
	print implode(';', $header) ."\r\n";
	
	/*
	while($row = db_fetch_array($result)){
		foreach($row as $value){
			$values[] = '"' . str_replace('"', '""', decode_entities(strip_tags($value))) . '"'; 
		}
		print implode(',', $values) ."\r\n";
		unset($values);
	}
	*/
	
	$values = array ($kodedinas, $namadinas, $kode, $kegiatan, $lokasi, $sumberdana1, $keluaransasaran, $keluarantarget, $total);
	print implode(';', $values) ."\r\n";
	exit;
}

function kegiatanskpd_print_form () {
	$form['formdata'] = array (
		'#type' => 'fieldset',
		'#title'=> 'Setting Printer',
		'#collapsible' => TRUE,
		'#collapsed' => FALSE,        
	);
	
	$kodekeg = arg(3);
	$topmargin = arg(4);
	$tipedok = arg(5);
	if (!isset($topmargin)) $topmargin=10;

	$form['formdata']['kodekeg']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $kodekeg, 
	);
	$form['formdata']['tipedok']= array(
		'#type'         => 'hidden', 
		'#default_value'=> $tipedok, 
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
	
	if ($tipedok=='dpa') {
		$form['formdata']['submit'] = array (
			'#type' => 'submit',
			'#value' => 'Cetak DPA'
		);
		$form['formdata']['submitsampul'] = array (
			'#type' => 'submit',
			'#value' => 'Cetak Sampul'
		);
	} else {
		$form['formdata']['submit'] = array (
			'#type' => 'submit',
			'#value' => 'Cetak RKA'
		);		
	}
	
	return $form;
}
function kegiatanskpd_print_form_submit($form, &$form_state) {
	//$kodeuk = $form_state['values']['kodeuk'];
	$tipedok = $form_state['values']['tipedok'];
	$kodekeg = $form_state['values']['kodekeg'];
	$topmargin = $form_state['values']['topmargin'];
	$uri = 'apbd/kegiatanskpd/print/' . $kodekeg . '/'. $topmargin . '/' . $tipedok . '/pdf' ;
	
	if ($form_state['clicked_button']['#value'] == $form_state['values']['submitsampul']) $uri .= '/sampul';
	drupal_goto($uri);
	
}

function get_catatan_rekening($kodekeg, $kodero) {
$sql = "select username,jawaban from {anggperkegverifikasi} where kodekeg='" . $kodekeg . "' and kodero='" . $kodero . "' order by username";
$res = db_query($sql);

$i = 0;
$catatan = '';
while ($data = db_fetch_object($res)) {	
	//$catatan = '<img src="/files/icon/info_red_16.png">' . $data_cat->username . ': <font color="Red">' . $data_cat->jawaban . '</font>';
	$i++;
	$catatan = '(' . $i . ') ' . $data->jawaban . '; ';
}	
return $catatan;
}

function get_catatan_kegiatan($kodekeg) {
$sql = sprintf("select username,persetujuan,jawaban from {kegiatanverifikasi} where kodekeg='%s' order by username", db_escape_string($kodekeg));
$res = db_query($sql);

$catatan = '';
$i = 0;
while ($data = db_fetch_object($res)) {	
	//$catatan = '<img src="/files/icon/info_red_16.png">' . $data_cat->username . ': <font color="Red">' . $data_cat->jawaban . '</font>';
	$i++;
	$catatan = '(' . $i . ') ' . $data->jawaban . '; ';
}	
return $catatan;
}
?>