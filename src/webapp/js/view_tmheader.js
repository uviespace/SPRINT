const urlParams = new URLSearchParams(window.location.search);


const edit_dialog_ids = ["edit_tmheader_parameter",
												 "edit_tmheader_order",
												 "edit_tmheader_role",
												 "edit_tmheader_group",
												 "edit_tmheader_repetition",
												 "edit_tmheader_value",
												 "edit_tmheader_description"];

const edit_properties = ["idParameter",
												 "order",
												 "role",
												 "group",
												 "repetition",
												 "value",
												 "desc"];


function load_data()
{
		var tmheader_handler = new TableHandler({
				table_id: "table_tmheader",
				template_id: "table_tmheader_row",
				properties: ["id", "parameter", "order",
										 "role", "group", "repetition",
										 "value", "desc" ],
				open_url: "",
				open_url_param_name: "",
				modal_id: "tmheader_modal",
				edit_dialog_ids: edit_dialog_ids,
				edit_properties: edit_properties,
				submit_button_id: "tmheader_submit_button",
				end_point: `api/v2/projects/${urlParams.get("idProject")}` +
						`/standards/${urlParams.get("idStandard")}/tmheaders`,
				create_button_id: "create_tmheader_button_id",
				empty_item: {},
				create_item_from_modal_fn: create_item
		});

		tmheader_handler.load_items();
		
}

window.onload = load_data();

function create_item(edit_item)
{
		for (var i = 0; i < edit_dialog_ids.length; i++) {
				edit_item[edit_properties[i]] = document.getElementById(edit_dialog_ids[i]).value;
		}

		edit_item["parameter"] = document.getElementById(edit_dialog_ids[0]).selectedOptions[0].text;

		return edit_item;
}
