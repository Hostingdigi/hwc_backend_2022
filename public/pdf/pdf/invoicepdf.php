<?php
include '../../includes/connection.php'; 
include '../../includes/constants.php'; 
include '../../includes/functions.php'; 

//$appointmentid = $_REQUEST['appointmentid'];
$appointmentid = $_REQUEST['id'];$businesssettings = [];$logo = $pdffooter = $pdflogo = $invoiceterms = '';$settingdata = selectFields(APPOINTMENT_SETTINGS, '', '', 'applogo, pdf_footer, invoice_terms', $conn);if(!empty($settingdata)) {	$settingdata = json_decode($settingdata);	$businesssettings = $settingdata->tabledata;	if(!empty($businesssettings) && count($businesssettings) > 0) {		foreach($businesssettings as $businesssetting) {			$logo = $businesssetting->applogo;			$pdffooter = $businesssetting->pdf_footer;			$invoiceterms = $businesssetting->invoice_terms;		}	}}if($logo != '') {	$pdflogo = "<img src=\"".SITEPATH."images/".$logo."\">";}
require_once('tcpdf_include.php');

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
//$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}
$pdf->setFontSubsetting(true);
$pdf->SetFont('helvetica', '', 11, '', true);
$pdf->AddPage();
$pdf->SetLineStyle( array( 'width' => 0, 'color' => array(0,0,0)));
//$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));
$htmlcon  = "";

$brdr = "border-bottom:#000000 solid 1px;border-right:#000000 solid 1px; vertical-align:middle;margin-top:5px;margin-bottom:5px;";

$condition = 'id = '.$appointmentid.'';
$data1 = selectFields(APPOINTMENTS, $condition, '', 'patient_id,department_id,doctor_id,booking_date,price,booking_id', $conn); 
if(!empty($data1)) { 
	$data1 = json_decode($data1);	
	$appoint = $data1->tabledata;			
}
if(!empty($appoint) && count($appoint) > 0) {	
foreach($appoint as $appointval) {
	$patient_id = $appointval->patient_id;
	$department_id = $appointval->department_id;
	$doctor_id = $appointval->doctor_id;
	$booking_date = formatdate($appointval->booking_date);
	$priceamt = $appointval->price;
	$booking_id = $appointval->booking_id;
}
}

$condition = 'id = '.$doctor_id.'';
$data = selectFields(DOCTORS, $condition, '', 'name,regno', $conn); 
if(!empty($data)) { 
	$data = json_decode($data);	
	$doctors = $data->tabledata;			
}
if(!empty($doctors) && count($doctors) > 0) {	
foreach($doctors as $doctorsval) {
	$doctorname = $doctorsval->name;
	$regno = $doctorsval->regno;
}
}

$condition = 'id = '.$department_id.'';
$data = selectFields(DEPARTMENTS, $condition, '', 'name', $conn); 
if(!empty($data)) { 
	$data = json_decode($data);	
	$dept = $data->tabledata;			
}
if(!empty($dept) && count($dept) > 0) {	
foreach($dept as $deptval) {
	$deptname = $deptval->name;
}
}
$parentid = 0;
$condition = 'id = '.$patient_id.'';
$data = selectFields(PATIENT_FAMILY_MEMBERS, $condition, '', 'patient_name,age,parent_id', $conn); 
if(!empty($data)) { 
	$data = json_decode($data);	
	$patients = $data->tabledata;			
}
if(!empty($patients) && count($patients) > 0) {	
foreach($patients as $patientsval) {
	$patientname = $patientsval->patient_name;
	$age = $patientsval->age;		$parentid = $patientsval->parent_id;
}
}

$condition = 'id = '.$parentid.'';
$data = selectFields(PATIENTS, $condition, '', 'email,phone', $conn); 
if(!empty($data)) { 
	$data = json_decode($data);	
	$patients1 = $data->tabledata;			
}
if(!empty($patients1) && count($patients1) > 0) {	
foreach($patients1 as $patientsvalue) {
	$email = $patientsvalue->email;
	$phone = $patientsvalue->phone;
}
}

if($email != ""){
$mailcont = '<strong>Email:</strong> '.$email.'';
 } 
if($phone != ""){
$phcont = '<tr>
    <td height="25">&nbsp;</td>
    <td height="25"><strong>Phone:</strong> '.$phone.'</td>
  </tr>';
 } 
 
 $linkbr = ' <tr>
		<td height="5" style="border-right:#000000 solid 1px;"></td><td></td>
	  </tr>';
 
 $htmlcon .= '<tr>
		<td height="25" align="left" style="'.$brdr.'">&nbsp;&nbsp;Online Consultation</td>
		<td height="25" align="right" style="border-bottom:#000000 solid 1px;">Rs.'.$priceamt.'&nbsp;&nbsp;</td>
	  </tr>';
  $htmlcon .= ''.$linkbr.'<tr>
	<td height="25" align="right" style="'.$brdr.'">Total Amount&nbsp;&nbsp;</td>
	<td height="25" align="right" style="border-bottom:#000000 solid 1px;">Rs.'.$priceamt.'&nbsp;&nbsp;</td>
  </tr>';
$html = <<<EOD
<table width="100%" cellspacing="3" cellpadding="3">
<tr>
<td>
<table width="100%" cellspacing="0" cellpadding="0" >
  <tr>
    <td colspan="2" align="center">$pdflogo</td>
  </tr>
   <tr>
    <td colspan="2" height="20">&nbsp;</td>
  </tr>  
  <tr>
    <td width="65%" height="25"><strong>Date</strong>: $booking_date </td>
    <td width="35%" ><strong>Invoice No.</strong>: $booking_id  </td>
  </tr>  
  <tr>
    <td width="65%" height="25"><strong>Doctor Name</strong>: $doctorname </td>
    <td width="35%" ><strong>Patient Name</strong>: $patientname  </td>
  </tr>
  <tr>
    <td height="25"><strong>Doctor Reg. Number</strong>: $regno</td>
    <td height="25"><strong>Age:</strong> $age</td>
  </tr>
  <tr>
    <td height="25"><strong>Department:</strong> $deptname </td>
    <td height="25">$mailcont</td>
  </tr>
 
  $phcont
  <tr>
    <td height="10" colspan="2"></td>
  </tr>
  
   <tr>
    <td width="100%" height="25" colspan="2"><table width="100%" cellspacing="0" cellpadding="0" style="border:#000000 solid 1px;">
	 $linkbr
      <tr>
        <td width="50%" height="25" align="left" style="font-size:15px;$brdr">&nbsp;&nbsp;<strong>Particulars</strong></td>
		<td width="50%" height="25" align="right" style="font-size:15px;border-bottom:#000000 solid 1px;"><strong>Amount</strong>&nbsp;&nbsp;</td>
      </tr>
	 $linkbr
    	$htmlcon	
    </table></td>
  </tr>
  <tr>
    <td height="40" colspan="2"></td>
  </tr>
  <tr>
    <td height="25" colspan="2" align="justify"><span style="font-size:13px;">Terms: $invoiceterms</span></td>
  </tr>
  
   <tr>
    <td height="350" colspan="2">&nbsp;</td>
  </tr>
   <tr>
     <td colspan="2" align="center" style="font-size:14px;">$pdffooter</td>
   </tr>
</table>
</td>
  </tr>
</table>
EOD;
/*echo $html;
exit();*/

$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
ob_end_clean();
//for open
//$pdf->Output('invoice.pdf', 'I');

//for auto save
$pdf_string = $pdf->Output('invoice.pdf', 'S');
file_put_contents('../../invoices/invoice-'.$appointmentid.'.pdf', $pdf_string);
?>