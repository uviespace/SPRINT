const urlParams = new URLSearchParams(window.location.search);

const end_point = `api/v2/projects/${urlParams.get("idProject")}` +
			`/standards/${urlParams.get("idStandard")}` +
			`/calibration`;



var calibration = {};
var chart = {};

const standard_numerical_settings = { engfmt: 'R', rawfmt:'U', radix:'H', unit:'V', ncurve:2, inter:'P', values: [] };
const standard_polynomial_settings = { pol1: 0, pol2: 0, pol3: 0, pol4: 0, pol5: 0 };
const standard_logarithmic_settings = { pol1: 0, pol2: 0, pol3: 0, pol4: 0, pol5: 0 };


document.addEventListener("DOMContentLoaded", function() {

		const calib_type_select = document.getElementById("calibration_type");
		calib_type_select.addEventListener('change', function() {
				const num_options = document.getElementById("numerical_options");
				const pol_options = document.getElementById("polynomial_options");
				const log_options = document.getElementById("logarithmic_options");
				
				switch(calib_type_select.value) {
				case "0":
						num_options.style.display = "block";
						pol_options.style.display = "none";
						log_options.style.display = "none";
						break;

				case "1":
						num_options.style.display = "none";
						pol_options.style.display = "block";
						log_options.style.display = "none";
						break;

				case "2":
						num_options.style.display = "none";
						pol_options.style.display = "none";
						log_options.style.display = "block";
						break;
				}				
		});


		load_data();
});





async function load_data()
{
		if (!urlParams.has("id") || urlParams.get("id") == 0) {
				calibration = {
						id: 0,
						name: "",
						shortDesc: "",
						type: 0,
						numerical_settings: JSON.parse(JSON.stringify(standard_numerical_settings)),
						polynomial_settings: JSON.parse(JSON.stringify(standard_polynomial_settings)),
						logarithmic_settings: JSON.parse(JSON.stringify(standard_logarithmic_settings))
				}
		} else {
				const response = await fetch(end_point + `/${urlParams.get("id")}`);
				const db_item = await response.json();
				parse_db_item(db_item);

		}

		const binder = new DomBinder(calibration, update_chart);
		binder.apply_bindings();
		
		create_chart();
}

function create_chart()
{
		const ctx = document.getElementById("chart");
		const chart_data = build_chart_data();

		ytext = calibration.name;

		chart = new Chart(ctx, {
				type: 'line',
				data: chart_data,
				options: {
						scales: {
								xAxisNumerical: {
										display: calibration.type == 0,
										type: "linear",
										title: {
												display: true,
												text: "ADU"
										}
								},
								yAxisNumerical: {
										display: calibration.type == 0,
										type: "linear",
										title: {
												display: true,
												text: ytext
										},
										beginAtZero: false
								},
								x: {
										display: calibration.type != 0,
										title: {
												display: true,
												text: "ADU"
										}	
								},
								y: {
										display: calibration.type != 0,
										title: {
												display: true,
												text: ytext
										},
								}
						},
						plugins: {
								title: {
										display: true,
										text: "Calibration Curve"
								},
								legend: {
										display: false
								}
						}
				}
		});
}

function build_chart_data()
{
		let chart_data = { };
		
		if (calibration.type == 0) {
				// Numerical calibration
				data = [];
				for(let i = 0; i< calibration.numerical_settings.values.length; i++) {
						data.push({
								x: calibration.numerical_settings.values[i].xval,
								y: calibration.numerical_settings.values[i].yval
						});
				}

				chart_data.datasets = [{
						label: calibration.name,
						fill: false,
						data: data,
						borderWidth: 1,
						xAxisId: 'xAxisNumercial',
						yAxisId: 'yAxisNumerical'
				}];

		} else if (calibration.type == 1) {
				// Polynomial calibration
				labels = [];
				data = [];

				let pol_curve = calibration.polynomial_settings;
				for (var x = 0; x < 10; x++) {
						labels.push(x);
						data.push(pol_curve.pol1 +
											pol_curve.pol2 * x +
											pol_curve.pol3 * Math.pow(x, 2) + 
											pol_curve.pol4 * Math.pow(x, 3) +
											pol_curve.pol5 * Math.pow(x, 4));
				}

				chart_data.labels = labels;
				chart_data.datasets = [ {
						label: calibration.name,
						fill: false,
						data: data,
						borderWidth: 1,
						xAxisId: "x",
						yAxisId: "y",
						tension: 0.1
				}];

		} else  if (calibration.type == 2) {
				// Logarithmic calibration
				labels = [];
				data = [];

				let log_curve = calibration.logarithmic_settings;
				for (var x = 0; x < 10; x++) {
						labels.push(x);
						data.push(log_curve.pol1 +
										  log_curve.pol2 * Math.log(x) +
										  log_curve.pol3 * Math.pow(Math.log(x), 2) +
											log_curve.pol4 * Math.pow(Math.log(x), 3) +
											log_curve.pol5 * Math.pow(Math.log(x), 4));
				}

				chart_data.labels = labels;
				chart_data.datasets = [ {
						label: calibration.name,
						fill: false,
						data: data,
						borderWidth: 1,
						xAxisId: "x",
						yAxisId: "y",
						tension: 0.1
				}];
		}

		return chart_data;
}

function update_chart()
{
		const chart_data = build_chart_data();

		chart.options.scales.x.display = calibration.type !== 0;
		chart.options.scales.y.display = calibration.type !== 0;
		chart.options.scales.xAxisNumerical.display = calibration.type === 0;
		chart.options.scales.yAxisNumerical.display = calibration.type === 0;

		if (calibration.type !== 0) {
				chart.options.scales.y.min = chart_data.datasets[0].data[0];
				chart.options.scales.y.max = chart_data.datasets[0].data[chart_data.datasets[0].data.length - 1];
		}

		chart.data = chart_data;
		chart.update();
}


function parse_db_item(db_item)
{
		calibration.id = db_item.id;
		calibration.name = db_item.name;
		calibration.shortDesc = db_item.shortDesc;
		calibration.type = db_item.type;

		switch(calibration.type) { 
		case 0:
				calibration.numerical_settings = JSON.parse(db_item.setting);
				calibration.polynomial_settings = JSON.parse(JSON.stringify(standard_polynomial_settings));
				calibration.logarithmic_settings = JSON.parse(JSON.stringify(standard_logarithmic_settings));
				break;
		case 1:
				calibration.numerical_settings = JSON.parse(JSON.stringify(standard_numerical_settings));
				calibration.polynomial_settings = JSON.parse(db_item.setting);
				calibration.logarithmic_settings = JSON.parse(JSON.stringify(standard_logarithmic_settings));
				break;
		case 2:
				calibration.numerical_settings = JSON.parse(JSON.stringify(standard_numerical_settings));
				calibration.polynomial_settings = JSON.parse(JSON.stringify(standard_polynomial_settings));
				calibration.logarithmic_settings = JSON.parse(db_item.setting);
				break
		default:
				iziToast.error({title: 'Error', message: 'Type ' + calibration.type + ' not recognized.'});
				break;
		}
}

function build_db_item()
{
		db_item = {
				id: calibration.id,
				name: calibration.name,
				shortDesc: calibration.shortDesc,
				type: calibration.type,
				setting: ""
		};

		switch(calibration.type) {
		case 0:
				calibration.numerical_settings.ncurve = calibration.numerical_settings.values.length;
				db_item.setting = JSON.stringify(calibration.numerical_settings);
				break;
		case 1:
				db_item.setting = JSON.stringify(calibration.polynomial_settings);
				break;
		case 2:
				db_item.setting = JSON.stringify(calibration.logarithmic_settings);
				break;
		}

		return db_item;
}

async function save_calibration()
{
		if (calibration.id === 0) {
				var response = await fetch(end_point,
																	 { method: 'post', headers: { 'Content-Type': 'application/json'},
																	   body: JSON.stringify(build_db_item()) });

				var db_item = await response.json();
				parse_db_item(db_item);
		} else {
				var response = await fetch(end_point + `/${urlParams.get("id")}`,
																	 { method: 'put', headers: { 'Content-Type': 'application/json' },
																		 body: JSON.stringify(build_db_item()) });
		}
		
		if (response.ok)
				iziToast.success({ title: "Success", message: "Calibration successfully updated "});
		else
				iziToast.error({ title: "Error", message: "Calibration could not be saved" });
}

/*function fill_controls()
{

		// fill all data-bind controls
		const controls = document.querySelectorAll('[data-bind]');

		for (let i = 0; i < controls.length; i++) {
				const bind_name = controls[i].getAttribute("data-bind");

				if (bind_name.includes(".")) {
						bind_name_split = bind_name.split(".");

						let value = calibration[bind_name_split[0]];
						for(let j = 1; j < bind_name_split.length; j++) {
								value = value[bind_name_split[j]];
						}

						controls[i].value = value;
				} else {
						controls[i].value = calibration[bind_name];
				}

				// Register event listener to write changes back to object
				controls[i].addEventListener("change", function() { event_listener(controls[i], bind_name); });
		}


		// fill all data-bind-array controls
		const array_controls = document.querySelectorAll('[data-bind-array]');

		for (let i = 0; i < array_controls.length; i++) {
				let bind_value = array_controls[i].getAttribute("data-bind-array");
				let bind_value_split = bind_value.split(":");
				let template_id = bind_value_split[0];
				let bind_name = bind_value_split[1];
				let tbody = array_controls[i].querySelector("tbody");
				let template = document.getElementById(template_id);

				let button_template = document.getElementById("add_numerical_value_button");
				let btn = button_template.content.cloneNode(true);
				array_controls[i].parentNode.insertBefore(btn, array_controls[i]);
				array_controls[i].previousElementSibling.addEventListener("click", function() {
						add_row(template, value, { xval: 0, yval: 0 }, tbody);
				});

				let value = {};
				if (bind_name.includes(".")) {
						bind_name_split = bind_name.split(".");

						value = calibration[bind_name_split[0]];
						for(let j = 1; j < bind_name_split.length; j++) {
								value = value[bind_name_split[j]];
						}
				} else {
						value = calibration[bind_name];
				}

				
				for(let j = 0; j < value.length; j++) {
						add_row(template, value, value[j], tbody);
				}
		}
}


function event_listener(control, bind_name)
{
		const control_value = isNaN(parseFloat(control.value)) ? control.value : parseFloat(control.value);
		
		if (bind_name.includes(".")) {
				bind_name_split = bind_name.split(".");

				value = calibration[bind_name_split[0]];
				for(let j = 1; j < bind_name_split.length; j++) {
						if (j == bind_name_split.length - 1)
								value[bind_name_split[j]] = control_value;
						else
								value = value[bind_name_split[j]];
				}
		} else {
				calibration[bind_name] = control_value;
		}

		update_chart();
}

function event_listener_array_element(control, array, index, property)
{
		const control_value = isNaN(parseFloat(control.value)) ? control.value : parseFloat(control.value);
		
		array[index][property] = control_value;
		update_chart();
}


function event_listener_remove_array_element(array, object, control)
{
		array.splice(array.indexOf(object), 1);
		control.parentElement.remove();

		update_chart();
}


function add_row(row_template, array, object, parent)
{
		let row = row_template.content.cloneNode(true);
		let td = row.querySelectorAll("td");

		var index = -1;
		if (!array.includes(object)) {
				index = array.length;
				array.push(object);
		} else {
				index = array.indexOf(object);
		}

		let i = 0;
		
		for (prop in object) {
				if (Object.prototype.hasOwnProperty.call(object, prop)) {
						let input = td[i++].firstChild;
						input.value = object[prop];

						let current_property = prop;
						input.addEventListener("change", function() {
								event_listener_array_element(input, array, index, current_property);
						});
				}
		}

		td[i].firstElementChild.addEventListener("click", function() {
				event_listener_remove_array_element(array, object, td[i]);
		});

		

		parent.appendChild(row);
}*/

//window.onload = load_data();

