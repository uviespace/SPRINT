const urlParams = new URLSearchParams(window.location.search);

const edit_dialog_ids = [ "edit_parameter_domain",
													"edit_parameter_name",
													"edit_parameter_short_desc",
													"edit_parameter_kind",
													"edit_parameter_role",
													"edit_parameter_datatype",
													"edit_parameter_reference_param", 
													"edit_parameter_multiplicity",
													"edit_parameter_value",
													"edit_parameter_unit" ];

const edit_properties = [ "domain", "name", "shortDesc",
													"kind", "role", "idType", "ref_param_id", "multiplicity",
													"value", "unit"];

const ref_param_enabled_ids = [ "102" ];

window.onload = load_data();

document.addEventListener("DOMContentLoaded", function() {
		const datatype_select = document.getElementById("edit_parameter_datatype");
		datatype_select.addEventListener("change", set_ref_param_visibility);
		datatype_select.addEventListener("focusout", set_ref_param_visibility);
		
		set_ref_param_visibility();
});



function load_data()
{
		var parameter_hdl = new TableHandler({ table_id: "table_parameter",
																					 template_id: "table_parameter_row",
																					 properties: [ "id", "domain", "name", "shortDesc",
																												 "kind", "datatype", "role", "multiplicity",
																												 "value", "unit" ],
																					 modal_id: "parameter_modal",
																					 edit_dialog_ids: edit_dialog_ids,
																					 edit_properties: edit_properties,
																					 submit_button_id: "parameter_submit_button",
																					 create_button_id: "create_parameter_button_id",
																					 end_point: `api/v2/projects/${urlParams.get("idProject")}` +
																					 `/standards/${urlParams.get("idStandard")}/parameters`,
																					 empty_item: {},
																					 create_item_from_modal_fn: create_item,
																					 modal_filled_fn: set_ref_param_visibility,
																					 create_item_from_modal_fn: create_item_from_modal																				 });

		parameter_hdl.load_items();
}

function create_item(edit_item)
{
		for (var i = 0; i < edit_dialog_ids.length; i++) {
				edit_item[edit_properties[i]] = document.getElementById(edit_dialog_ids[i]).value;
		}

		edit_item["datatype"] = document.getElementById(edit_dialog_ids[4]).selectedOptions[0].text;

		return edit_item;
}

function set_ref_param_visibility()
{
		const datatype_select = document.getElementById("edit_parameter_datatype");
		const ref_param_area = document.getElementById("ref_param_area");

		if (ref_param_enabled_ids.includes(datatype_select.value))
				ref_param_area.style.display = "block";
		else
				ref_param_area.style.display = "none";
}

function create_item_from_modal(edit_item)
{
		for(var i = 0; i < edit_dialog_ids.length; i++) {
				edit_item[edit_properties[i]] = document.getElementById(edit_dialog_ids[i]).value;
		}

		if (!ref_param_enabled_ids.includes(edit_item["idType"]))
				edit_item["ref_param_id"] = null;
}
