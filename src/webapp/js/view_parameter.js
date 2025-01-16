const urlParams = new URLSearchParams(window.location.search);

const edit_dialog_ids = [ "edit_parameter_domain",
													"edit_parameter_name",
													"edit_parameter_short_desc",
													"edit_parameter_kind",
													"edit_parameter_role",
													"edit_parameter_datatype",
													"edit_parameter_multiplicity",
													"edit_parameter_value",
													"edit_parameter_unit" ];

const edit_properties = [ "domain", "name", "shortDesc",
													"kind", "role", "idType", "multiplicity",
													"value", "unit"];

function load_data()
{
		var parameter_hdl = new TableHandler({ table_id: "table_parameter",
																					 template_id: "table_parameter_row",
																					 properties: [ "id", "domain", "name", "shortDesc",
																												 "kind", "datatype", "role", "multiplicity",
																												 "value", "unit", "ref_count"],
																					 modal_id: "parameter_modal",
																					 edit_dialog_ids: edit_dialog_ids,
																					 edit_properties: edit_properties,
																					 submit_button_id: "parameter_submit_button",
																					 create_button_id: "create_parameter_button_id",
																					 end_point: `api/v2/projects/${urlParams.get("idProject")}` +
																					 `/standards/${urlParams.get("idStandard")}/parameters`,
																					 empty_item: {},
																					 create_item_from_modal_fn: create_item
																				 });

		parameter_hdl.load_items();
}


window.onload = load_data();

function create_item(edit_item)
{
		for (var i = 0; i < edit_dialog_ids.length; i++) {
				edit_item[edit_properties[i]] = document.getElementById(edit_dialog_ids[i]).value;
		}

		edit_item["datatype"] = document.getElementById(edit_dialog_ids[4]).selectedOptions[0].text;

		return edit_item;
}
