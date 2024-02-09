const urlParams = new URLSearchParams(window.location.search);


const edit_dialog_ids = [ "edit_datapool_domain",
													"edit_datapool_name",
													"edit_datapool_short_desc",
													"edit_datapool_kind",
													"edit_datapool_datatype",
													"edit_datapool_multiplicity",
													"edit_datapool_value",
													"edit_datapool_unit" ];

const edit_properties = [ "domain", "name", "shortDesc",
													"kind", "idType", "multiplicity",
													"value", "unit"];


function load_data()
{
		var datapool_hdl = new TableHandler({ table_id: "table_datapool",
																					template_id: "table_datapool_row",
																					properties: [ "id", "domain", "name", "shortDesc",
																												"kind", "datatype", "multiplicity",
																												"value", "unit" ],
																					modal_id: "datapool_modal",
																					edit_dialog_ids: edit_dialog_ids,
																					edit_properties: edit_properties,
																					submit_button_id: "datapool_submit_button",
																					create_button_id: "create_datapool_button_id",
																					end_point: `api/v2/projects/${urlParams.get("idProject")}` +
																					`/standards/${urlParams.get("idStandard")}/datapool`,
																					empty_item: {},
																					create_item_from_modal_fn: create_item
																				});

		datapool_hdl.load_items();
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
