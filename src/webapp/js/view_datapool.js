const urlParams = new URLSearchParams(window.location.search);


// const edit_dialog_ids = [ "edit_datapool_domain",
// 													"edit_datapool_name",
// 													"edit_datapool_short_desc",
// 													"edit_datapool_kind",
// 													"edit_datapool_datatype",
// 													"edit_datapool_multiplicity",
// 													"edit_datapool_value",
// 													"edit_datapool_unit",
// 												  "edit_datapool_dp_id",
// 													"edit_datapool_monitored_value"];

// const edit_properties = [ "domain", "name", "shortDesc",
// 													"kind", "idType", "multiplicity",
// 													"value", "unit", "dp_id", "monitored_value"];


// var datapool_hdl = new TableHandler({ table_id: "table_datapool",
// 																			template_id: "table_datapool_row",
// 																			properties: [ "id", "domain", "name", "shortDesc",
// 																										"kind", "datatype", "multiplicity",
// 																										"value", "unit", "dp_id" ],
// 																			modal_id: "datapool_modal",
// 																			edit_dialog_ids: edit_dialog_ids,
// 																			edit_properties: edit_properties,
// 																			submit_button_id: "datapool_submit_button",
// 																			create_button_id: "create_datapool_button_id",
// 																			end_point: `api/v2/projects/${urlParams.get("idProject")}` +
// 																			`/standards/${urlParams.get("idStandard")}/datapool`,
// 																			empty_item: { "monitored_value": false },
// 																			create_item_from_modal_fn: create_item
// 																		});

// const observer = new IntersectionObserver(async (entries) => {
// 		entries.forEach(async entry => {
// 				const dp_entry_name_field = document.getElementById("edit_datapool_short_desc");
// 				if (entry.isIntersecting) {
// 						if (dp_entry_name_field.value == "") {
// 								const next_dp_id = await get_next_datapool_id();
// 								const datapool_id_input = document.getElementById("edit_datapool_dp_id");
// 								datapool_id_input.value = next_dp_id;
// 						} else {
// 								const is_monitored_value = await get_is_monitored_value();
// 								const datapool_monitored_checkbox = document.getElementById("edit_datapool_monitored_value");
// 								if (is_monitored_value)
// 										datapool_monitored_checkbox.disabled = true;

// 								const dp_name_entry_field = document.getElementById("edit_datapool_name");
// 								const name = dp_name_entry_field.value;

// 								[ "AlarmLowerLimit", "AlarmUpperLimit", "WarnLowerLimit", "WarnUpperLimit" ].forEach( text => {
// 										if (name.includes(text))
// 												datapool_monitored_checkbox.disabled = true;
// 								});
// 						}
// 				} else {
// 						const datapool_monitored_checkbox = document.getElementById("edit_datapool_monitored_value");
// 						datapool_monitored_checkbox.disabled = false;
// 				}
// 		});
// }, { threshold: 0.5});


function load_data()
{
		// datapool_hdl.add_validations({
		// 		validations: [
		// 				{ id: "edit_datapool_domain", type: "max_length", param: 256, msg: "Domain names cannot be longer than 256"},
		// 				{ id: "edit_datapool_name", type: "max_length", param: 256, msg: "Datapool names cannot be longer than 256"},
		// 		],
		// 		required_properties: [ "edit_datapool_name", "edit_datapool_kind", "edit_datapool_datatype" ]
		// });
		//datapool_hdl.load_items();


		let dp_data_table = new DataTable("test_table", {
				end_point: `api/v2/projects/${urlParams.get("idProject")}` +
						`/standards/${urlParams.get("idStandard")}/datapool`,
				columns: [ { data: "id" }, { data: "domain", sort: true }, { data: "name", sort: true }, { data: "shortDesc", sort: true },
								   { data: "kind", map: { "3": "DpPar (3)", "4": "DpVar (4)", "5": "DpPar Imp (5)", "6": "DpVar Imp (6)" } },
									 { data: "datatype" }, { data: "multiplicity" },
									 { data: "value", css: "max-width: 120px; overflow: hidden; text-overflow: ellipsis;" },
									 { data: "unit" }, { data: "dp_id", sort: true }, { data: "user_count" } ],
				filter: true,
				editor: {
						allow_add: true,
						modal_header: "Edit Datapool Item",
						fields: [
								{ data: "domain", label: "Domain",
									validator: { validations: [{ type: "max_length", param: 256, msg:"Domain names cannot be longer than 256" }] } },
								{ data: "name", label: "Name",
									validator: { required: true, validations: [ { type: "max_length", param: 256, msg: "Datapool names cannot be longer than 256" }]}},
								{ data: "shortDesc", label: "Short Description" },
								{ data: "kind", label: "Kind", type: "select", options: [ { value: 3, label: "DpPar (3)"},
																																					{ value: 4, label: "DpVar (4)" },
																																					{ value: 5, label: "DpPar Imported (5)" },
																																					{ value: 6, label: "DpVar Imported (6)" }],
								  validator: { required: true } },
								{ data: "idType", label: "Datatype", type: "data-select",  filter: true,
									source: `api/v2/projects/${urlParams.get("idProject")}/standards/${urlParams.get("idStandard")}/dropdown_datatypes`,
								  validator: { required: true }},
								{ data: "multiplicity", label: "Multiplicity", type: "number" },
								{ data: "value", label: "Value"},
								{ data: "unit", label: "Unit" },
								{ data: "dp_id", label: "Datapool Id" , type: "number", input: check_datapool_id, auto_fill: get_next_datapool_id },
								{ data: "monitored_value", label: "Monitored value (Creates additional DP variables for warn and alarm values)", type: "checkbox" }
						]
				},

		});

		dp_data_table.load_items();
}



window.onload = load_data();

// document.addEventListener("DOMContentLoaded", (event) => {
// 		const modal = document.getElementById("datapool_modal");
// 		observer.observe(modal);
// });


// function create_item(edit_item)
// {
// 		for (var i = 0; i < edit_dialog_ids.length; i++) {
// 				edit_item[edit_properties[i]] = document.getElementById(edit_dialog_ids[i]).value;
// 		}

// 		edit_item["datatype"] = document.getElementById(edit_dialog_ids[4]).selectedOptions[0].text;
// 		edit_item["monitored_value"] = document.getElementById(edit_dialog_ids[9]).checked;

// 		return edit_item;
// }

async function check_datapool_id(value, item)
{
		const endpoint = `api/v2/projects/${urlParams.get("idProject")}` +
					`/standards/${urlParams.get("idStandard")}` +
					`/datapool/${item.id}` +
					`/check_datapool_id/${value}`

		const response = await fetch(endpoint);
		const result = await response.json();

		if (response.ok) {
				if (result.datapool_id_count > 0) {
						return { msg: "Datapool Id already in use by another datapool entry!" };
				} else {
						return {};
				}
		} else {
				iziToast.error({title: 'Error', message: response_item.Error ? response_item.Error : 'Could not fetch datapool conflicts'});
		}
}

// async function check_datapool_id_old()
// {
// 		if (!datapool_hdl.edit_item)
// 				return;

// 		const datapool_id_check_msg_id = "datapool_id_check_msg_id";
// 		const msg_div = document.getElementById(datapool_id_check_msg_id);

// 		const dp_id_control = document.getElementById(edit_dialog_ids[8]);
// 		const new_dp_id = dp_id_control.value;
		
// 		const endpoint = `api/v2/projects/${urlParams.get("idProject")}` +
// 					`/standards/${urlParams.get("idStandard")}` +
// 					`/datapool/${datapool_hdl.edit_item.id}` +
// 					`/check_datapool_id/${new_dp_id}`

// 		const response = await fetch(endpoint);
// 		const result = await response.json();

// 		if (response.ok) {
// 				if (result.datapool_id_count > 0)
// 						msg_div.style.display = "block";
// 				else
// 						msg_div.style.display = "none";
// 		} else {
// 				iziToast.error({title: 'Error', message: response_item.Error ? response_item.Error : 'Could not fetch datapool conflicts'});
// 		}
// }


async function get_next_datapool_id()
{
		const endpoint = `api/v2/projects/${urlParams.get("idProject")}/next_datapool_id`;

		const response = await fetch(endpoint);
		const result = await response.json();

		if (response.ok) {
				return result.next_datapool_id;
		} else {
				iziToast.error({title: 'Error', message: response_item.Error ? response_item.Error : 'Could not fetch datapool id'});
				return -1;
		}
}

// async function get_is_monitored_value()
// {
// 		const endpoint = `api/v2/projects/${urlParams.get("idProject")}` +
// 					`/standards/${urlParams.get("idStandard")}` +
// 					`/parameters/${datapool_hdl.edit_item.id}/is_monitored`;

// 		const response = await fetch(endpoint);
// 		const result = await response.json();

// 		if (response.ok) {
// 				return result.variable_monitored;
// 		} else {
// 				iziToast.error({title: 'Error', message: response_item.Error ? response_item.Error : 'Could not fetch parameter monitored status'});
// 				return -1;
// 		}
// }
