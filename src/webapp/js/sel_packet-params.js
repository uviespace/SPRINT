const urlParams = new URLSearchParams(window.location.search);

const packets = { };


const edit_dialog_ids = [ "edit_parameter_parameter", "edit_parameter_role", 
													"edit_parameter_group", "edit_parameter_repetition",
													"edit_parameter_value", "edit_parameter_description" ];

const edit_properties = [ "parameter_id", "role", "group", "repetition", "value", "desc" ];

async function toggle_param_visibility(packet_id)
{
		const packet_container = document.getElementById("packet-" + packet_id);
		const icon = document.getElementById("icon-" + packet_id);

		if (packet_container.style.display == "none") {
				packet_container.style.display = "block";
				icon.classList.remove("nf-cod-triangle_right");
				icon.classList.add("nf-cod-triangle_down");
		} else {
				packet_container.style.display = "none";
				icon.classList.remove("nf-cod-triangle_down");
				icon.classList.add("nf-cod-triangle_right");
		}

		if (!packets.hasOwnProperty(packet_id)) {
				const packet_end_point = `api/v2/projects/${urlParams.get("idProject")}` +
							`/standards/${urlParams.get("idStandard")}` +
							`/packets/${packet_id}`;

				const response = await fetch(packet_end_point + "/header_size");
				
				packets[packet_id] = {
						id: packet_id,
						table_handler: new TableHandler({
								table_id: "table_param_" + packet_id,
								template_id: "table_param_row",
								properties: ["id", "parameter", "order", "role", "group", "repetition",
														 "value", "type_name", "size", "desc" ],
								modal_id: "param_modal",
								edit_dialog_ids: edit_dialog_ids,
								edit_properties: edit_properties,
								submit_button_id: "param_submit_button",
								end_point: packet_end_point + "/parameters",
								create_button_id: "create_param_button_id_" + packet_id,
								empty_item: { order: 0 },
								create_item_from_modal_fn: create_item
						}),
						header_size: await response.json()
				};

				packets[packet_id].table_handler.add_validations({
						validations: [],
						required_properties: [ "edit_parameter_parameter", "edit_parameter_role" ]
				});
				await packets[packet_id].table_handler.load_items();
				draw_packet_size(packet_id);
		}
}


function create_item(edit_item, table_handler)
{
		for (var i = 0; i < edit_dialog_ids.length; i++) {
				edit_item[edit_properties[i]] = document.getElementById(edit_dialog_ids[i]).value;
		}

		edit_item["parameter"] = document.getElementById(edit_dialog_ids[0]).selectedOptions[0].text;

		if (edit_item["order"] === 0 && table_handler.items.length > 0)
				edit_item["order"] = table_handler.items[table_handler.items.length - 1].order + 1;

		return edit_item;
}


function draw_packet_size(packet_id)
{
		let parameter = [];

		parameter.push({
				name: packets[packet_id].header_size.name,
				color: packets[packet_id].header_size.color,
				size: packets[packet_id].header_size.size * 8
		});
		
		for (let i = 0; i < packets[packet_id].table_handler.items.length; i++) {
				var item = packets[packet_id].table_handler.items[i];
				parameter.push({ name: item.name, color: get_color(item), size: item.size});
		}

		draw_packet(document.getElementById("packet-" + packet_id),
								document.getElementById("packet_view_" + packet_id),
								{ draw_crc: true, parameter: parameter });
}

function get_color(param)
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
		const param_table = event.target.closest("table");
		const id = parseInt(param_table.id.substring(param_table.id.lastIndexOf("_") + 1));
		const tbody = param_table.querySelector("tbody");
		const button_row = event.target.closest("tr");

		const table_handler = packets[id].table_handler;
		const items = packets[id].table_handler.items;

		const items_to_update = [];

		var row_index = -1;

		for (var i = 0; i < tbody.rows.length; i++) {
				if (button_row == tbody.rows[i]) {
						row_index = i;
						break;
				}
		}
		
		if (row_index == -1)
				return;

		var new_row = (row_index + direction + tbody.rows.length)  % tbody.rows.length;

		const item = items[row_index];
		items[row_index] = items[new_row];
		items[new_row] = item;

		for (var i = 0; i < items.length; i++) {
				if (items[i]["order"] != i + 1) {
						items[i]["order"] = i + 1;
						items_to_update.push(items[i]);
				}

				for(var j = 0; j < table_handler.props.properties.length; j++) {
						tbody.rows[i].cells[j].textContent = items[i][table_handler.props.properties[j]];
				}
		}
		
		draw_packet_size(id);

		// items_to_update has length of two most of the time
		// unless there is a wrap around when moving parameters
		const end_point = `api/v2/projects/${urlParams.get("idProject")}` +
					`/standards/${urlParams.get("idStandard")}` +
					`/packets/${id}/parameters/`

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
				
				iziToast.error({ title: "Error", message: "Could not update parameter(s): " + error_msg.slice(0, -2) });
		} else {
				iziToast.success({ title: "Success", message: "Parameters successfully updated" });
		}
}
