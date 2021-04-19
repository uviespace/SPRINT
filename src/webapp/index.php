<!DOCTYPE html>
<html>
<head>
	<title>CORDET FW Editor</title>
	<!-- https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css -->
	<link rel="stylesheet" type="text/css" href="ext/bootstrap/3.3.7/css/bootstrap.min.css">
	<!-- https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.js -->
	<script type="text/javascript" src="ext/ajax/libs/jquery/3.1.0/jquery.js"></script>
	<!-- https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha/js/bootstrap.min.js -->
	<script type="text/javascript" src="ext/ajax/libs/twitter-bootstrap/4.0.0-alpha/js/bootstrap.min.js"></script>
	<!-- https://cdnjs.cloudflare.com/ajax/libs/twbs-pagination/1.3.1/jquery.twbsPagination.min.js -->
	<script type="text/javascript" src="ext/ajax/libs/twbs-pagination/1.3.1/jquery.twbsPagination.min.js"></script>
	<!-- https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.5/validator.min.js -->
	<script src="ext/ajax/libs/1000hz-bootstrap-validator/0.11.5/validator.min.js"></script>
	<!-- //cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js -->
	<script type="text/javascript" src="ext/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
	<!-- //cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css -->
	<link href="ext/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="int/layout.css">
	<script type="text/javascript">
		var url = "http://localhost/dbeditor/";
	</script>
	<script type="text/javascript" src="js/item-ajax.js"></script>
<!--
<style type="text/css">
 .topcorner{
   position:absolute;
    top:10px;
    right: 10px;
  }
  
   .topcorner_left{
   position:absolute;
    top:10px;
    left: 10px;
  }

	.logo  {
  background-color: black;
  color: white;
  margin: 20px;
  padding: 20px;
	}
	
	.offset {
	background-color: blue;
    width: 100%;
    height: 500px;
}
	</style>
-->
</head>
<body>

	<div class="container">

		<div class="row">
		    <div class="col-lg-12 margin-tb">
		        <div class="pull-left">
		            <h2>CORDET FW Editor</h2>
		        </div>
				
				<!--
		        <div class="pull-right">
				<button type="button" class="btn btn-success" data-toggle="modal" data-target="#create-item">
					  Create Item
				</button>
		        </div>
				-->
		    </div>
		</div>

		<div>
			The CORDET FW Editor is a web-based tool to support the specification of a PUS-based system communication 
			standard and of the applications which use it. The PUS 
			(<a href="http://www.ecss.nl/wp-content/uploads/standards/ecss-e/ECSS-E-70-41A30Jan2003.pdf" target="_blank">Packet Utilization Standard</a>) 
			is an interface 
			standard promoted by the European Space Agency for on-board applications.
			<br/>
			The CORDET FW Editor allows a user to enter the specification information for a PUS-based system and to 
			generate from it the following items:
			<ul>
				<li>An Interface Control Document (ICD)</li>
				<li>A C-language component which implements the data pool for the applications in the PUS system</li>
				<li>A set of tables which specify the telecommands and telemetry reports in the PUS system and which 
				can be imported in a specification document</li>
				<li>The configuration files to instantiate the <a href="https://www.pnp-software.com/cordetfw/" target="_blank">CORDET Framework</a>
				for the applications in the PUS system</li>
			</ul>
			The <img<a href="https://www.pnp-software.com/cordetfw/editor-1.1/_lib/libraries/grp/doc/UserManual.html" target="_blank">help pages</a>
			explains how to use the CORDET FW Editor. The editor is publicly accessible for 
			registered users. Registration is free and only requires the user to enter a valid e-mail address. Local 
			installations of the editor are available on a commercial basis from 
			<a href="https://www.pnp-software.com/" target="_blank">P&P Software GmbH</a>.
		</div>

		<div>
			<hr>
			<a href="mng_project.php" target="_self"><button>Manage my projects...</button></a><br/>
			<br/>
			<div style="background-color:#EEEEEE;padding:2px;">
				<a href="sel_project.php" target="_self">Open project</a>
			</div>
			<br/>
			<br/>
			<br/>
			<br/>
			<br/>
			<hr>
			<div style="text-align:right;">(c) 2019, University of Vienna</div>
			<hr>
			<br/>
		</div>

		<div class="topcorner_left">
			<img src="img/grp__NM__menu_img__NM__logo.png" alt="Logo P&P Software" width="150" style="background-color: darkblue; padding: 5px;"><br/>
			<img src="img/uni_logo_220.jpg" alt="Logo University of Vienna" width="150" style="padding: 5px;"><br/>
			<img src="img/csm_uni_logo_schwarz_0ca81bfdea.jpg" alt="Logo Institute for Astrophysics" width="150" style="padding: 5px;">
		<div/>
		
	</div>

</body>

</html>