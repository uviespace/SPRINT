<div>
    The Space Project Resource Integration Network Tool integrates following standards, resources and processes:
    <ul>
        <li>ECSS (European Co-operation for Space Standardisation) Software Engineering standardization and processes:
            <a href="https://ecss.nl/" target="_blank">ECSS</a>,
            <a href="https://www.esa.int/esapub/bulletin/bullet111/chapter21_bul111.pdf" target="_blank">Introducing ECSS Software-Engineering Standards within ESA (ESA bulletin 111 — august 2002)</a>,
        </li>
        <li>SCOS2000 Mission Control System (e.g. run-time database): 
            <a href="https://www.esa.int/Enabling_Support/Operations/Ground_Systems_Engineering/SCOS-2000" target="_blank">ESA - SCOS-2000</a>, 
            <a href="https://issues.cosmos.esa.int/solarorbiterwiki/download/attachments/23757668/MOC-applicable%20MIB%20egos-mcs-s2k-icd-0001-version7.0-FINAL.pdf?version=1&modificationDate=1522940882000&api=v2" target="_blank">MOC-applicable MIB egos-mcs-s2k-icd-0001-version7.0-FINAL.pdf</a>
        </li>
        <li>EGS-CC (European Ground Systems Common Core) - common infrastructure to support space systems monitoring and control in pre- and post-launch phases form-group all mission types:
            <a href="http://www.egscc.esa.int/" target="_blank">ESA - EGS-CC</a>
			<li>CORDET Framework from PnP Software including code generation functionality (see below)</li>
    </ul>
</div>

<div>
    <h3><img src="img/grp__NM__menu_img__NM__logo.png" alt="Logo P&P Software" width="120" style="background-color: darkblue; padding: 5px;"> CORDET FW Editor</h3>
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

<div style="margin-top: 48px;"></div>


<?php if ($is_admin) : ?>
	<div class="management-block">
		<a class="menu-button" href="mng_user.php"><img src="img/users_64x64.png" class="button-icon" />Manage Users...</a>
	</div>
<?php endif; ?>

<div class="management-block">
	<a class="menu-button" href="mng_acronym.php"><img src="img/acronym_1_64x64.png" class="button-icon" />Manage Acronyms...</a>
	<a class="menu-button" href="mng_reference.php"><img src="img/reference_64x64.png" class="button-icon" />Manage References...</a>
	<a class="menu-button" href="mng_organisation.php"><img src="img/org_1_64x64.png" class="button-icon" />Manage Organisations...</a>
</div>

<div class="management-block">
	<a class="menu-button" href="mng_project.php"><img src="img/projects_64x64.png" class="button-icon" />Manage My Projects...</a>
	<a class="menu-button" href="sel_project.php"><img src="img/open_64x64.png" class="button-icon" />Open Project...</a>
</div>


