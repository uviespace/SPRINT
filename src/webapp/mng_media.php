<?php
session_start();
if(!isset($_SESSION['userid'])) {
    //die('Please <a href="login.php">login</a> first!');
    echo "Please <a href='login.php'>login</a> first!";
    echo "<br/><br/>";
    echo "<img src='img/loading.gif' />";
    header( "refresh:8;url=login.php" );
    die('');
}
require 'api/db_config.php';

//Abfrage der Nutzer ID vom Login
$userid = $_SESSION['userid'];
 
//echo "Hallo User: ".$userid;

// get user name from database
$sql = "SELECT * FROM `user` WHERE `id` = ".$userid;

$result = $mysqli->query($sql);

$row = $result->fetch_assoc();

$userName = $row["name"];

?>
<!DOCTYPE html>
<html>
<head>
	<title>SPRINT Editor - Media</title>
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
    <script type="text/javascript" src="int/config.js"></script>
    <style>
@media print
{    
    .no-print, .no-print *
    {
        display: none !important;
    }
}
.past-mission {
    border-left: 5px solid white;  
    border-color: #a0b0eb;
}
.current-mission {
    border-left: 5px solid white;  
    border-color: #93d490;
}
.future-mission {
    border-left: 5px solid white;  
    border-color: #ffe08a;
}
    </style>
	<script type="text/javascript">
        function auto_grow(element) {
            element.style.height = "5px";
            element.style.height = (element.scrollHeight)+"px";
        }
        function adjust_height(id){
            var el = document.getElementById(id) 
            el.style.height = (el.scrollHeight > el.clientHeight) ? (el.scrollHeight)+"px" : "60px";
        }
	</script>
	<script type="text/javascript" src="js/item-ajax_mng-media.js"></script>
</head>
<body>

	<div class="container">
		<div class="row no-print">
		    <div class="col-lg-12 margin-tb">
		        <div class="pull-left">
		            <h2><img src="img/media_1_64x64.png" width="64" height="64">&nbsp;&nbsp;Media</h2>
		        </div>
		        <div class="pull-right">
				<button type="button" class="btn btn-success" data-toggle="modal" data-target="#create-item">
					  Create Item
				</button>
		        </div>
		    </div>
		</div>

        <div>
            <h3>
                <u style="text-decoration-color:#a0b0eb">Past</u> /
                <u style="text-decoration-color:#93d490">Current</u> /
                <u style="text-decoration-color:#ffe08a">Future</u> Missions</h3>
        </div>

        <div>
        <table class="table table-bordered">
        <thead>
        <tr>
        <th>Mission</th>
        <th>Logo / Patch</th>
        <th>Spacecraft</th>
        <th>Media Links</th>
        </tr>
        </thead>
        <tbody>
        <tr class="past-mission">
        <td>
            HSO (Herschel Space Observatory)
            <ul>
                <li>HIFI (Heterodyne Instrument for Far Infrared)</li>
                <li>PACS (Photodetector Array Camera and Spectrometer)</li>
                <li>SPIRE (Spectral and Photometric Image Receiver)</li>
            </ul>
        </td>
        <td>
            <img width="120px" src="img/logo/Herschel_mission_logo.jpg" /><br/>
        </td><td>
             <img width="120px" src="img/mission/herschel_img4-22_med.jpg" /></td>
        </td><td>
            <a href="https://www.esa.int/Science_Exploration/Space_Science/Herschel_overview" target="_blank">ESA - Herschel overview</a><br/>
            <a href="https://sci.esa.int/web/herschel" target="_blank">ESA Science & Technology - Herschel</a>
        </td>
        </tr>
        <tr class="current-mission">
        <td>
            CHEOPS (CHaracterising ExOPlanet Satellite)
        </td>
        <td>
            <img width="120px" src="img/logo/CHEOPS_Logo_1.png" /><br/>
            <img width="120px" src="img/logo/cheops-logo-with-additional-600x300.png" />
        </td><td>
             <img width="120px" src="img/mission/CHEOPS_spacecraft.png" /></td>
        </td><td>
            <a href="https://www.esa.int/Science_Exploration/Space_Science/Cheops" target="_blank">ESA - Cheops</a><br/>
            <a href="https://sci.esa.int/web/cheops" target="_blank">ESA Science & Technology - CHEOPS</a><br/>
            <a href="media/CHEOPS-MEDIAKIT_FA_2019-12-09.pdf">CHEOPS-MEDIAKIT_FA_2019-12-09</a>
        </td>
        </tr>
        <tr class="future-mission">
        <td>
            SMILE (Solar wind Magnetosphere Ionosphere Link Explorer)
            <ul>
                <li>SXI (Soft X-ray Imager)</li>
                <li>UVI (UV Imager)</li>
                <li>LIA (Light Ion Analyser)</li>
                <li>MAG (Magnetometer)</li>
            </ul>
        </td>
        <td>
            <img width="120px" src="img/logo/SMILELogo.png" />
        </td><td>
             <img width="120px" src="img/mission/SMILE_spacecraft.jpg" /></td>
        </td><td>
            <a href="https://sci.esa.int/web/smile" target="_blank">ESA Science & Technology - SMILE</a><br/>
            <a href="https://www.cosmos.esa.int/web/smile/mission" target="_blank">Mission - SMILE - Cosmos</a>
        </td>
        </tr>
        <tr class="future-mission">
        <td>
            ARIEL (Atmospheric Remote-sensing Infrared Exoplanet Large-survey)
            <ul>
                <li>TA (Telescope assembly)</li>
                <li>AIRS (Ariel infrared spectrometer)</li>
                <li>FGS (Fine Guidance System)</li>
            </ul>
        </td>
        <td>
            <img width="120px" src="img/logo/ARIEL_insignia.png" />
        </td><td>
             <img width="120px" src="img/mission/ariel4_med.png" /></td>
        </td><td>
            <a href="https://sci.esa.int/web/ariel" target="_blank">ESA Science & Technology - ARIEL</a><br/>
            <a href="https://www.cosmos.esa.int/web/ariel" target="_blank">Home - ARIEL - Cosmos</a><br/>
            <a href="https://arielmission.space/" target="_blank">Ariel Space Mission</a><br/>
            <a href="media/ARIELYellowBook_Final-170317-1730.pdf">ARIELYellowBook_Final-170317-1730</a>
        </td>
        </tr>
        <tr class="future-mission">
        <td>
            ATHENA (Advanced Telescope for High Energy Astrophysics)
            <ul>
                <li>WFI (Wide Field Imager)</li>
                <li>X-IFU (X-ray Integral Field Unit)</li>
            </ul>
        </td>
        <td>
            <img width="120px" src="img/logo/ATHENA_space_mission_logo_med.png" />
        </td><td>
             <img width="120px" src="img/mission/athena_xifu_good_med.png" /></td>
        </td><td>
            <a href="https://sci.esa.int/web/athena" target="_blank">ESA Science & Technology - ATHENA</a><br/>
            <a href="https://www.cosmos.esa.int/web/athena" target="_blank">Home - ATHENA - Cosmos</a><br/>
            <a href="https://www.the-athena-x-ray-observatory.eu/" target="_blank">Athena X-ray Observatory</a>
        </td>
        </tr>
        <tr class="future-mission">
        <td>
            PLATO (PLAnetary Transits and Oscillations of stars)
        </td>
        <td>
            <img width="120px" src="img/logo/Plato_Logo_med.png" />
        </td><td>
             <img width="120px" src="img/mission/PLATO_spacecraft.jfif" /></td>
        </td><td>
            <a href="https://sci.esa.int/web/plato" target="_blank">ESA Science & Technology - PLATO</a><br/>
            <a href="https://www.cosmos.esa.int/web/plato" target="_blank">Home - PLATO - Cosmos</a>
        </td>
        </tr>
        <tr class="future-mission">
        <td>
            eXTP (enhanced X-ray Timing and Polarimetry mission)
            <ul>
                <li>SFA (Spectroscopic Focusing Array)</li>
                <li>LAD (Large Area Detector)</li>
                <li>PFA (Polarimetry Focusing Array)</li>
                <li>WFM (Wide Field Monitor)</li>
            </ul>
        </td>
        <td>
            <img width="120px" src="img/logo/eXTP_logo.png" />
        </td><td>
             <img width="120px" src="img/mission/eXTP-satellite_med.png" /></td>
        </td><td>
            <a href="https://www.isdc.unige.ch/extp/" target="_blank">The eXTP mission</a>
        </td>
        </tr>

        </tbody>
        </table>
        </div>

        <div>
            <h3>Proposed Missions</h3>
        </div>

        <div>
        <table class="table table-bordered">
        <thead>
        <tr>
        <th>Mission</th>
        <th>Logo / Patch</th>
        <th>Spacecraft</th>
        <th>Media Links</th>
        </tr>
        </thead>
        <tbody>
        <tr>
        <td>
            SPICA (Space Infrared Telescope for Cosmology and Astrophysics)
            <ul>
                <li>SMI (SPICA Mid-infrared Instrument)</li>
                <li>SAFARI (SPICA Far-infrared Instrument)</li>
                <li>B-BOP (B-fields with BOlometers and Polarizers)</li>
            </ul>
        </td>
        <td>
            <img width="120px" src="img/logo/SPICA.logo.gif" />
        </td><td>
             <img width="120px" src="img/mission/SPICA-SAFARI_med.png" /></td>
        </td><td>
            <a href="https://sci.esa.int/web/cosmic-vision/-/53635-spica" target="_blank">ESA Science & Technology - SPICA</a><br/>
            <a href="https://www.sron.nl/missions-astrophysics/spica-safari" target="_blank">SPICA / SAFARI - SRON</a>
        </td>
        </tr>

        </tbody>
        </table>
        </div>

<!--
		<table class="table table-bordered">
			<thead>
			    <tr>
				<th>ID</th>-->
				<!--<th>Short Name</th>
				<th>Number</th>-->
<!--				<th>Identifier</th>
				<th>Name</th>
				<th>Type</th>
				<th>Version</th>
				<th>Date</th>
				<th>Organisation</th>
				<th>Filename / Link</th>
				<th width="200px">Action</th>
			    </tr>
			</thead>
			<tbody>
			</tbody>
		</table>

		<ul id="pagination" class="pagination-sm"></ul>-->

		<!-- Create Item Modal -->
<!--		<div class="modal fade" id="create-item" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					<h4 class="modal-title" id="myModalLabel">Create Item</h4>
				</div>

				<div class="modal-body">
					<form data-toggle="validator" action-data="api/create_mng-reference.php" method="POST">

						<div class="form-group">
							<label class="control-label" for="title">Identifier:</label>
							<input type="text" name="identifier" class="form-control" data-error="Please enter identifier." required />
							<div class="help-block with-errors"></div>
						</div>
                        
						<div class="form-group">
							<label class="control-label" for="title">Name:</label>
							<input type="text" name="name" class="form-control" data-error="Please enter name." required />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Type:</label>
							<select id="sel_type_create" name="type" class="form-control" data-error="Please enter type." required>
								<option value="select"></option>
							</select>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Version:</label>
							<input type="text" name="version" class="form-control" data-error="Please enter short version." required />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Date:</label>
							<input type="text" name="date" class="form-control" data-error="Please enter short date." required />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Organisation:</label>
							<input type="text" name="organisation" class="form-control" data-error="Please enter organisation." required />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Filename / Link:</label>
							<input type="text" name="filename" class="form-control" data-error="Please enter short filename." />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Note:</label>
							<textarea name="note" class="form-control" style="overflow: hidden;" onInput="auto_grow(this)" data-error="Please enter note." ></textarea>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<button type="submit" class="btn crud-submit btn-success">Submit</button>
						</div>

		      		</form>

		      </div>
		    </div>

		  </div>
		</div>-->

		<!-- Edit Item Modal -->
<!--		<div class="modal fade" id="edit-item" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
		        <h4 class="modal-title" id="myModalLabel">Edit Item</h4>
		      </div>

		      <div class="modal-body">
					<form data-toggle="validator" action="api/update_mng-reference.php" method="put">

		      			<input type="hidden" name="id" class="edit-id">

						<div class="form-group">
							<label class="control-label" for="title">Identifier:</label>
							<input type="text" name="identifier" class="form-control" data-error="Please enter identifier." required />
							<div class="help-block with-errors"></div>
						</div>
                        
						<div class="form-group">
							<label class="control-label" for="title">Name:</label>
							<input type="text" name="name" class="form-control" data-error="Please enter name." required />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Type:</label>
							<select id="sel_type" name="type" class="form-control" data-error="Please enter type." required>
								<option value="select"></option>
							</select>
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Version:</label>
							<input type="text" name="version" class="form-control" data-error="Please enter short version." required />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Date:</label>
							<input type="text" name="date" class="form-control" data-error="Please enter short date." required />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Organisation:</label>
							<input type="text" name="organisation" class="form-control" data-error="Please enter organisation." required />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<label class="control-label" for="title">Filename / Link:</label>
							<input type="text" name="filename" class="form-control" data-error="Please enter short filename." />
							<div class="help-block with-errors"></div>
						</div>

						<div class="form-group">
							<button type="submit" class="btn btn-success crud-submit-edit">Submit</button>
						</div>

		      		</form>

		      </div>
		    </div>
		  </div>
		  
		</div>-->

				<div class="topcorner_left no-print">
					<img src="img/grp__NM__menu_img__NM__logo.png" alt="Logo P&P Software" width="150" style="background-color: darkblue; padding: 5px;"><br/>
					<img src="img/uni_logo_220.jpg" alt="Logo University of Vienna" width="150" style="padding: 5px;"><br/>
					<img src="img/csm_uni_logo_schwarz_0ca81bfdea.jpg" alt="Logo Institute for Astrophysics" width="150" style="padding: 5px;">
					<br/><br/>
					You are logged in as: <br/>
					<?php 
						echo "<b>".$userName."</b><br/>";
					?>
					<br/><br/>
					<a class="a_btn" href="index.php" target="_self">>> HOME <<</a>
				</div>

	</div>
</body>

</html>