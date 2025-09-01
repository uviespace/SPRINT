const urlParams = new URLSearchParams(window.location.search);


const edit_dialog_ids = ["edit_tcheader_parameter",
												 "edit_tcheader_order",
												 "edit_tcheader_role",
												 "edit_tcheader_group",
												 "edit_tcheader_repetition",
												 "edit_tcheader_value",
												 "edit_tcheader_description"];

const edit_properties = ["idParameter",
												 "order",
												 "role",
												 "group",
												 "repetition",
												 "value",
												 "desc"];

const tcheader_handler = new TableHandler({ table_id: "table_tcheader",
																						template_id: "table_tcheader_row",
																						properties: ["id", "parameter", "order",
																												 "role", "group", "repetition",
																												 "value", "desc" ],
																						open_url: "",
																						open_url_param_name: "",
																						modal_id: "tcheader_modal",
																						edit_dialog_ids: edit_dialog_ids,
																						edit_properties: edit_properties,
																						submit_button_id: "tcheader_submit_button",
																						end_point: `api/v2/projects/${urlParams.get("idProject")}` +
																						`/standards/${urlParams.get("idStandard")}/tcheaders`,
																						create_button_id: "create_tcheader_button_id",
																						empty_item: {},
																						create_item_from_modal_fn: create_item
																					});


async function load_data()
{
		await tcheader_handler.load_items();
		draw_header();
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

function draw_header()
{
		let parameter = [];

		for (let i = 0; i < tcheader_handler.items.length; i++) {
				var item = tcheader_handler.items[i];
				parameter.push({ name: item.parameter, color: get_color(item), size: item.param_size });
		}

		draw_packet_alt(document.getElementById("header_canvas_parent"),
										document.getElementById("header_canvas"),
										{ draw_crc: false, parameter: parameter });
}

function get_color(param)
{
		if (param.role == 1)
				return "#96beea";
		else if (param.role == 2)
				return "#77a6d8";
		else if (param.role == 4)
				return "#77d7d8"
		else if (param.role == 5 )
				return "#7877d8"
		else
				return "#FAFAD2";
}
