const urlParams = new URLSearchParams(window.location.search);


const edit_dialog_ids = [ "edit_param_parameter", "edit_param_role",
													"edit_param_group", "edit_param_repetition", "edit_param_value",
													"edit_param_desc"];

const edit_properties = [ "idParameter", "role", "group", "repetition",
													"value", "desc" ];


const endpoint =  `api/v2/projects/${urlParams.get("idProject")}`
				+ `/standards/${urlParams.get("idStandard")}`
				+ `/packets/${urlParams.get("idParent")}`
			+ `/derived_packets/${urlParams.get("idPacket")}`;

const packet_handler = new TableHandler({
		table_id: "table_params",
		template_id: "table_params_row",
		properties: [ "id", "parameter", "order", "role", "group", "repetition",
									"value", "desc", "type_name", "size", "multiplicity" ],
		modal_id: "params_modal",
		edit_dialog_ids: edit_dialog_ids,
		edit_properties: edit_properties,
		submit_button_id: "param_submit_button",
		create_button_id: "create_param_button_id",
		end_point: endpoint + `/parameters` ,
		create_item: {},
		empty_item: { order: 0, role: 0, group: 0, repetition: 0 },
		create_item_from_modal_fn: create_item
});


async function load_data()
{
		packet_handler.add_validations({
				validations: [],
				required_properties: [ "edit_param_parameter", "edit_param_role" ]
		});
		
		await packet_handler.load_items();
		draw_packet_size();
}


window.onload = load_data();


function create_item(edit_item)
{
		for (var i = 0; i < edit_dialog_ids.length; i++) {
				edit_item[edit_properties[i]] = document.getElementById(edit_dialog_ids[i]).value;
		}

		edit_item["parameter"] = document.getElementById(edit_dialog_ids[0]).selectedOptions[0].text;

		if (edit_item["order"] === 0 && packet_handler.items.length > 0)
				edit_item["order"] = packet_handler.items[packet_handler.items.length - 1].order + 1;

		return edit_item;
}

async function draw_packet_size()
{
		let parameter = [];

		const parent_size_response = await fetch(endpoint + `/parent_size`);
		const parent_size = await parent_size_response.json();

		parameter.push({
				name: parent_size.header.name,
				color: parent_size.header.color,
				size: parent_size.header.size * 8,
		});

		for (let i = 0; i < parent_size.parent.length; i++) {
				let parent_param = parent_size.parent[i];
				parameter.push({ name: parent_param.name, color: get_parent_color(parent_param), size: parent_param.size });
		}

		for (let i = 0; i < packet_handler.items.length; i++) {
				let item = packet_handler.items[i];
				parameter.push({ name: item.name, color: get_child_color(item), size: item.size });
		}

		draw_packet(document.getElementById("packet_container"),
								document.getElementById("packet_view"),
								{ draw_crc: true, parameter: parameter });
}

function get_parent_color(param)
{
		if (param.role == 3)
				return "#EEDD82";
		else if (param.role == 8)
				return "#FFF";
		else if (param.group != "")
				return "#F9D3B6";
		else
				return "#FAFAD2";
}


function get_child_color(param)
{
		if (param.role == 8)
				return "#F3FFF3";
		else
				return "#8FBC8F";
}


function click_up(event)
{
		move_parameter(event, -1);
}

function click_down(event)
{
		move_parameter(event, +1);
}

async function move_parameter(event, direction)
{
		const table_handler = packet_handler;
		const tbody = event.target.closest("tbody");
		const button_row = event.target.closest("tr");

		const items = packet_handler.items;
		const items_to_update = [];

		let row_index = -1;

		for (let i = 0; i < tbody.rows.length; i++) {
				if (button_row == tbody.rows[i]) {
						row_index = i;
						break;
				}
		}

		if (row_index == -1)
				return;

		// Get new row for item and flip the items
		const new_row = (row_index + direction + tbody.rows.length)  % tbody.rows.length;
		const item = items[row_index];
		items[row_index] = items[new_row];
		items[new_row] = item;

		for (let i = 0; i < items.length; i++) {
				if (items[i]["order"] != i + 1) {
						items[i]["order"] = i + 1;
						items_to_update.push(items[i]);
				}

				for(let j = 0; j < table_handler.props.properties.length; j++) {
						tbody.rows[i].cells[j].textContent = items[i][table_handler.props.properties[j]];
				}
		}

		draw_packet_size();

		// items_to_update has length of two most of the time
		// unless there is a wrap around when moving parameters
		const end_point =`api/v2/projects/${urlParams.get("idProject")}`
					+ `/standards/${urlParams.get("idStandard")}`
					+ `/packets/${urlParams.get("idParent")}`
					+ `/derived_packets/${urlParams.get("idPacket")}/parameters/`

		const update_error = []
		for(var i = 0; i < items_to_update.length; i++) {
				var response = await fetch(end_point + items_to_update[i].id,
																	 { method: 'put', headers: { 'Content-Type': 'application/json' },
																		 body: JSON.stringify(items_to_update[i]) });

				if (!response.ok) {
						update_error.push(i);
				}
		}

		if (update_error.length > 0) {
				var error_msg = "";
				for(var i = 0; i < update_error.length; i++) {
						error_msg += items_to_update[update_error[i]].name + ", ";
				}
				
				iziToast.error({ title: "Error", message: "Could not update parameter(s): " + error_msg.slice(0, -2), position: "bottomLeft" });
		} else {
				iziToast.success({ title: "Success", message: "Parameters successfully updated", position: "bottomLeft" });
		}
}
