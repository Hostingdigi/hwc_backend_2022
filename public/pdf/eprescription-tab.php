<?php
if($_REQUEST['submit'] != ""){
	//include("pdf/eprescriptionpdf.php");
	header("Location:pdf/eprescriptionpdf.php?appointmentid=1");
}
?>
<div align="right" class="bold xxs-mb">Patient Name: Demo</div>
<div class="modern-tabs-container t-center border-gray2 no-border">
	<ul class="nav nav-tabs modern-tabs font-11 bg-white dark uppercase bold" role="tablist">
		<li role="presentation"><a href="summary.php" aria-controls="tab1c" role="tab" data-toggle="tab" class="bg-info border-info white"><?php echo SUMMARYTAB; ?></a></li>
		<li role="presentation"><a href="current-reports.php" target="_blank" aria-controls="tab2c"  class="bg-info border-info white"><?php echo CURREPORTS; ?></a></li>
		<li role="presentation"><a href="#" aria-controls="tab3c" class="bg-info border-info white show active"><?php echo EPRESCFORM; ?></a></li>
		<li role="presentation"><a href="reports.php" aria-controls="tab2c"  class="bg-info border-info white"><?php echo PASTREPORTS; ?></a></li>
	</ul>
</div>
<div class="divider-1 font-12 uppercase bold xxs-py"> <span>Submit E-Prescription Form</span> </div>
<div class="tab-content t-left">
<div id="styled1" role="tabpanel" class="tab-pane fade show active">
  <form name="login_form" class="xxs-mt" method="post" action="" enctype="multipart/form-data">
	<label for="email">Patient Name:</label> <strong>Demo</strong><br />
	<label for="phone">Appointment Date</label> <strong>15 April, 2020</strong><br />
	<label for="email">Patient's Health Issue:</label>
	<textarea name="" class="classic_form radius">heath issue content here</textarea>
	<label for="name">Prescription:</label>
	<div class="row">
	<div class="col-md-3 col-xs-12 t-center-sm xxs-py xs-pl bold font-13">Name</div>
	<div class="col-md-2 col-xs-12 t-center-sm xxs-py xs-pl bold font-13">Dose</div>
	<div class="col-md-2 col-xs-12 t-center-sm xxs-py xs-pl bold font-13">Frequency</div>
	<div class="col-md-2 col-xs-12 t-center-sm xxs-py xs-pl bold font-13">B/A Food</div>
	<div class="col-md-2 col-xs-12 t-center-sm xxs-py xs-pl bold font-13">Days</div>
	<div class="col-md-1 col-xs-12 t-center-sm xxs-py xs-pl"></div>
	</div>
<div id="type_container">
  <div class="row">  
	<div class="col-md-3 col-xs-12 t-center-sm mini-py xs-pl font-13"><input type="text" name="name_1" id="name" class="classic_form radius" required pattern="[a-zA-Z\s.]{3,50}" title="only alphabets,. and space are allowed" value=""></div>
	<div class="col-md-2 col-xs-12 t-center-sm mini-py xs-pl font-13"><input type="text" name="dosage_1" id="name" class="classic_form radius" required value=""></div>
	<div class="col-md-2 col-xs-12 t-center-sm mini-py xs-pl font-13"><input type="text" name="name" id="name" class="classic_form radius" required value=""></div>
	<div class="col-md-2 col-xs-12 t-center-sm mini-py xs-pl font-13"><input type="text" name="name" id="name" class="classic_form radius" required value=""></div>
	<div class="col-md-2 col-xs-12 t-center-sm mini-py xs-pl font-13"><input type="text" name="name" id="name" class="classic_form radius" required value=""></div>
	<div class="col-md-1 col-xs-12 t-center-sm mini-py xs-pl font-13"><a class="add-type pull-right" href="javascript: void(0)" tiitle="Click to add more"><i class="fa fa-plus"></i></a> </div>	
	</div>
	</div>
	
<div id="type-container" class="hide"> 
  <div class="row type-row">  	
	<div class="col-md-3 col-xs-12 t-center-sm mini-py xs-pl font-13"><input type="text" name="name_1" id="name" class="classic_form radius" required pattern="[a-zA-Z\s.]{3,50}" title="only alphabets,. and space are allowed" value=""></div>
	<div class="col-md-2 col-xs-12 t-center-sm mini-py xs-pl font-13"><input type="text" name="dosage_1" id="name" class="classic_form radius" required value=""></div>
	<div class="col-md-2 col-xs-12 t-center-sm mini-py xs-pl font-13"><input type="text" name="name" id="name" class="classic_form radius" required value=""></div>
	<div class="col-md-2 col-xs-12 t-center-sm mini-py xs-pl font-13"><input type="text" name="name" id="name" class="classic_form radius" required value=""></div>
	<div class="col-md-2 col-xs-12 t-center-sm mini-py xs-pl font-13"><input type="text" name="name" id="name" class="classic_form radius" required value=""></div>
		<div class="col-md-1 col-xs-12 t-center-sm mini-py xs-pl font-13"><a class="remove-type pull-right" targetDiv="" data-id="0" href="javascript: void(0)"><i class="fa fa-trash"></i></a></div>	
	</div>
	</div>

	
	
	<label for="email">Review Note:</label>
	<textarea name="" class="classic_form radius"></textarea>
	<div class="clearfix xs-mt">
	<input type="hidden" name="submit" value="submit" />
	  <button type="submit" id="submit" data-bgcolor="#50487F" style="width:auto;" class="click-effect bg-colored white extrabold qdr-hover-6 classic_form radius">Save & Preview</button>
	  <button type="submit" id="submit" data-bgcolor="#50487F" style="width:auto;" class="click-effect bg-colored white extrabold qdr-hover-6 classic_form radius">Send to Patient</button>
	</div>
  </form>
</div>
</div>