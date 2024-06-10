function update_form()
{
		const value_curve_type = document.getElementById("cal_curve_type").value;
		const value_curve_action = document.getElementById("cal_curve_action").value;
		const value_standard = document.getElementById("cal_curve_standard").value;

		if (value_curve_type === "1") {
				document.getElementById("cal_curve_numerical_settings_additions").style = "display: block";
		} else {
				document.getElementById("cal_curve_numerical_settings_additions").style = "display: none";
		}

		if (value_curve_action === "1") {
				document.getElementById("cal_curve_settings_block").style = "display: block";
				document.getElementById("cal_curve_existing_row").style = "display: none";
		} else {
				document.getElementById("cal_curve_settings_block").style = "display: none";
				document.getElementById("cal_curve_existing_row").style = "display: block";

				fill_calibration_options();
		}
}

async function fill_calibration_options()
{
		const url_params = new URLSearchParams(window.location.search);
		const cal_curve_existing = document.getElementById("cal_curve_existing");
		const standard_id = document.getElementById("cal_curve_standard").value;

		const url = `api/v2/projects/${url_params.get("idProject")}/standards/${standard_id}/calibration`;

		const response = await fetch(url);
		const cal_curves = await response.json();

		cal_curve_existing.options.length = 0;

		for (let i = 0; i < cal_curves.length; i++) {
				cal_curve_existing.appendChild(new Option(cal_curves[i].name, cal_curves[i].id));
		}
}
