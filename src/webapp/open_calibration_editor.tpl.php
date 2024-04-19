<h4> Project <?=$project_name?> - Standard <?=$standard_name?></h4>

<h2>Editor for Calibration Curve</h2>


<div class="flex-container">

	<div class="site-form">
		<label for="name">Name:</label>
		<input class="form-input modal-input" type="text" name="name" data-bind="name" />

		<label for="short_desc">Short Description:</label>
		<input class="form-input modal-input" type="text" name="short_desc" data-bind="shortDesc" />

		<label for="type">Calibration Type:</label>
		<select id="calibration_type" name="type" class="form-input modal-input" data-bind="type">
			<option value="0">Numerical</option>
			<option value="1">Polynomial</option>
			<option value="2">Logarithmic</option>
		</select>



		<div id="numerical_options" style="display: block;">
			<fieldset>
				<legend>Numerical Calibration Curve Options</legend>
				
				<label for="engfmt">Format for the engineering values (ENGFMT):</label>
				<select name="engfmt" class="form-input modal-input" data-bind="numerical_settings.engfmt">
					<option value="I">Signed Integer (I)</option>
					<option value="U">Unsigned Integer (U)</option>
					<option value="R">Real (R)</option>
				</select>

				<label for="rawfmt">Format of raw values (RAWFMT):</label>
				<select name="rawfmt" class="form-input modal-input" data-bind="numerical_settings.rawfmt">
					<option value="I">Signed Integer (I)</option>
					<option value="U">Unsigned Integer (U)</option>
					<option value="R">Real (R)</option>
				</select>

				<label for="radix">Radix:</label>
				<select name="radix" class="form-input modal-input" data-bind="numerical_settings.radix">
					<option value="D">Decimal (D)</option>
					<option value="H">Hexadecimal (H)</option>
					<option value="O">Octal (O)</option>
				</select>

				<label for="unit">Unit:</label>
				<input name="unit" class="form-input modal-input" type="text" data-bind="numerical_settings.unit" />

				<label for="inter">Interpolation:</label>
				<select name="inter" class="form-input modal-input" data-bind="numerical_settings.inter">
					<option value="P">Interpolate/Extrapolate</option>
					<option value="F">Disabled</option>
				</select>

				<table class="table" data-bind-array="numerical_values:numerical_settings.values">
					<thead>
						<tr>
							<th>X</th>
							<th>Y</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</fieldset>
		</div>

		<div id="polynomial_options" style="display: none;">
			<fieldset>
				<legend>Polynomial Calibration Curve Options</legend>
				
				<label for="pol1">Polynomial coefficient A0</label>
				<input name="pol1" class="form-input modal-input" type="number" data-bind="polynomial_settings.pol1" />

				<label for="pol1">Polynomial coefficient of the first order A1</label>
				<input name="pol1" class="form-input modal-input" type="number" data-bind="polynomial_settings.pol2" />

				<label for="pol1">Polynomial coefficient of the second order A2</label>
				<input name="pol1" class="form-input modal-input" type="number" data-bind="polynomial_settings.pol3" />

				<label for="pol1">Polynomial coefficient of the third order A3</label>
				<input name="pol1" class="form-input modal-input" type="number" data-bind="polynomial_settings.pol4" />

				<label for="pol1">Polynomial coefficient of the fourth order A4</label>
				<input name="pol1" class="form-input modal-input" type="number" data-bind="polynomial_settings.pol5" />
			</fieldset>
		</div>

		<div id="logarithmic_options" style="display: none;">
			<fieldset>
				<legend>Logarithmic Calibration Curve Options</legend>

				<label for="pol1">Logarithmic coefficient A0</label>
				<input name="pol1" class="form-input modal-input" type="number" data-bind="logarithmic_settings.pol1" />

				<label for="pol1">Logarithmic coefficient of the first order A1</label>
				<input name="pol1" class="form-input modal-input" type="number" data-bind="logarithmic_settings.pol2" />

				<label for="pol1">Logarithmic coefficient of the second order A2</label>
				<input name="pol1" class="form-input modal-input" type="number" data-bind="logarithmic_settings.pol3" />

				<label for="pol1">Logarithmic coefficient of the third order A3</label>
				<input name="pol1" class="form-input modal-input" type="number" data-bind="logarithmic_settings.pol4" />

				<label for="pol1">Logarithmic coefficient of the fourth order A4</label>
				<input name="pol1" class="form-input modal-input" type="number" data-bind="logarithmic_settings.pol5" />
			</fieldset>
		</div>
		
	</div>


	<div style="width: 700px; margin-top: 24px;">
		<canvas id="chart"></canvas>
	</div>

</div>

<button class="btn" onclick="save_calibration()">
	<i class="nf nf-fa-save" style="margin-right: 4px; font-size: 16px;" ></i>Save Calibration
</button>

<template id="numerical_values">
	<tr>
		<td><input name="xval" class="form-input-table modal-input" type="number" /></td>
		<td><input name="yval" class="form-input-table modal-input" type="number" /></td>
		<td>
			<button class="btn-toolbar"><i class="nf nf-oct-x error" style="font-size: 14px;"></i></button>
		</td>
	</tr>
</template> 


<template id="add_numerical_value_button">
	<button class="btn">
		<i class="nf nf-oct-diff_added" style="margin-right: 4px; font-size: 16px;"></i>Add Value
	</button>
</template> 
