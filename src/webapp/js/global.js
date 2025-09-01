/*
 * init contains both validations and required_properties
 *
 * validations: list of validation
 * validation: {
 *     id: <id of input field>,
 *     type: <min_length|max_length|is_number|min_value|max_value>,
 *     param: <parameter of the validator type>,
 *     msg: <message to be displayed in case of error>
 * }
 * 
 * required_properties: list of IDs of input that are required
 */

class Validator
{		
		constructor(init)
		{
				this.validations = init.validations;
				this.required_properties = init.required_properties;
		}

		validate_form()
		{
				const missing_field_error_msg = "This field is required!";
				const validator_types = [ "min_length", "max_length", "is_number", "min_value", "max_value" ];

				let validate_success = true;

				for (let i = 0; i < this.validations.length; i++) {
						const input = document.getElementById(this.validations[i].id);
						for (let j = 0; j < validator_types.length; j++) {
								let previous_error_msg = document.getElementById(this.validations[i].id + "_" + validator_types[j] + "_error_msg");
								if (previous_error_msg)
										previous_error_msg.remove();
						}

						switch(this.validations[i].type) {
						case 'min_length':
								if (input.value && input.value.length < this.validations[i].param) {
										this._create_error_msg(input, this.validations[i].id + "_min_length_error_msg", this.validations[i].msg);
										validate_success = false;
								}
								break;

						case 'max_length':
								if (input.value && input.value.length > this.validations[i].param) {
										this._create_error_msg(input, this.validations[i].id + "_max_length_error_msg", this.validations[i].msg);
										validate_success = false;
								}
								break;

						case 'is_number':
								if (!this._is_number(input.value)) {
										this._create_error_msg(input, this.validations[i].id + "_is_number_error_msg", this.validations[i].msg);
										validate_success = false;
								}
								break;

						case 'min_value':
								if (!this._is_number(input.value)) {
										this._create_error_msg(input, this.validations[i].id + "_min_value_error_msg", this.validations[i].msg);
										validate_success = false;
								} else {
										const int_value = parseInt(input.value);
										if (int_value < this.validations[i].param) {
												this._create_error_msg(input, this.validations[i].id + "_min_value_error_msg", this.validations[i].msg);
												validate_success = false;
										}
								}
								break;

						case 'max_value':
								if (!this._is_number(input.value)) {
										this._create_error_msg(input, this.validations[i].id + "_max_value_error_msg", this.validations[i].msg);
										validate_success = false;
								} else {
										const int_value = parseInt(input.value);
										if (int_value > this.validations[i].param) {
												this._create_error_msg(input, this.validations[i].id + "_max_value_error_msg", this.validations[i].msg);
												validate_success = false;
										}
								}
								break;
								break;
						}
				}

				for (let i = 0; i < this.required_properties.length; i++) {
						const input = document.getElementById(this.required_properties[i]);
						const previous_error_msg = document.getElementById(this.required_properties[i] + "_error_msg");
						if (previous_error_msg)
								previous_error_msg.remove()

						if (!input.value)  {
								this._create_error_msg(input, this.required_properties[i] + "_error_msg", missing_field_error_msg);
								validate_success = false;
						}
				}

				return validate_success;
		}

		hide_all_validations_msgs()
		{
				const validator_types = [ "min_length", "max_length", "is_number", "min_value", "max_value" ];
				
				for (let i = 0; i < this.validations.length; i++) {
						for (let j = 0; j < validator_types.length; j++) {
								let previous_error_msg = document.getElementById(this.validations[i].id + "_" + validator_types[j] + "_error_msg");
								if (previous_error_msg)
										previous_error_msg.remove();
						}
				}

				for (let i = 0; i < this.required_properties.length; i++) {
						const previous_error_msg = document.getElementById(this.required_properties[i] + "_error_msg");
						if (previous_error_msg)
								previous_error_msg.remove()
				}
				
		}

		_create_error_msg(input, id, msg)
		{
				const error_msg = document.createElement("div");
				error_msg.setAttribute("id", id);
				error_msg.setAttribute("class", "alert alert-error");
				error_msg.setAttribute("style", "max-width: inherit;");
				error_msg.innerText = msg;
				input.insertAdjacentElement("beforebegin", error_msg);
		}

		_is_number(value)
		{
				if (typeof value != "string")
						return false;

				return !isNaN(value) && !isNaN(parseInt(value))
		}
}


/*
 * prop: Properties
 *
 * name: easy way to set all required IDs just by name
 * table_id: id of the table the data should be displayed
 * template_id: id of the template of a new table row
 * properties: array of properties in an object
 * open_url: url when opening one item
 * open_url_param_name: parameter_name for this item
 * modal_id: id of the modal used for editing
 * edit_dialog_ids: ids of the controls used for editing
 * edit_properties: properties that are edited
 * submit_button_id: id of the submit button
 * end_point: endpoint for crud functions of this item
 * create_button_id: id of the create item button
 * empty_item: item template for new creations (allows filling of standard properties
 * create_item_from_modal_fn: function to create item from entries in modal (optional)
 * modal_filled_fn: function that gets called when modal is filled
 */

class TableHandler
{
		constructor(props)
		{
				if (props.name != null && props.name != "") {
						props.table_id = props.name + "_table";
						props.template_id = props.name + "_table_row";
						props.modal_id = props.name + "_modal";
						props.submit_button_id = props.name + "_submit_button";
						props.create_button_id = props.name + "_create_button";
				}

				this.props = props;
				this.base_path = "";
				this.validator = new Validator({ validations: [], required_properties: [] });
		}

		async load_items()
		{
				const response = await fetch(this.props.end_point);
				this.items = await response.json();

				this.fill_table();
		}

		add_validations(validations)
		{
				this.validator.validations = validations.validations;
				this.validator.required_properties = validations.required_properties;
		}
		
		create_item()
		{
				const modal = document.getElementById(this.props.modal_id);

				for(var i = 0; i < this.props.edit_dialog_ids.length; i++) {
						document.getElementById(this.props.edit_dialog_ids[i]).value = "";
				}
				
				document.getElementById(this.props.submit_button_id).textContent = "Create";
				let instance = this;
				document.getElementById(this.props.submit_button_id).onclick = function() { instance.create_item_async.call(instance); };

				modal.style.display = "block";
		}

		async create_item_async()
		{
				if (!this.validator.validate_form())
						return;
				
				var created_item = { ...this.props.empty_item }; 

				if (this.props.create_item_from_modal_fn) {
						this.props.create_item_from_modal_fn(created_item, this);
				} else {
						for(var i = 0; i < this.props.edit_dialog_ids.length; i++) {
								created_item[this.props.edit_properties[i]] = document.getElementById(this.props.edit_dialog_ids[i]).value;
						}
				}

				var response = await fetch(this.props.end_point,
															 { method: "POST", headers: { 'Content-Type': 'application/json' },
																 body: JSON.stringify(created_item) });

				const response_item = await response.json();

				if (response.ok) {
						iziToast.success({ title: 'Success', message: 'Standard successfully created' });

						this.items.push(response_item);
						const actions = this.props.open_url
									? [this.action_open, this.action_edit, this.action_delete]
									: [this.action_edit, this.action_delete];
						
						const row = this.create_row(this.props.template_id, response_item,
																				this.props.properties, actions);
 
						const table = document.getElementById(this.props.table_id);
						const tbody = table.querySelector("tbody");
						tbody.appendChild(row);

						this.close_modal();
				} else {
						iziToast.error({title: 'Error', message: response_item.Error ? response_item.Error : 'Item could not be created'});
				}
		}

		fill_table()
		{
				const self = this;
				
				const table = document.getElementById(this.props.table_id);
				const tbody = table.querySelector("tbody");
				const template = document.getElementById(this.props.template_id);

				const actions = this.props.open_url
							? [this.action_open, this.action_edit, this.action_delete]
							: [this.action_edit, this.action_delete];
				

				for (let i = 0; i < this.items.length; i++) {		
						tbody.appendChild(this.create_row(this.props.template_id, this.items[i],
																							this.props.properties, actions));
				}


				// Wire up button events
				
				let instance = this;
				document.getElementById(this.props.create_button_id).onclick = function () { instance.create_item.call(instance);  }

				const modal = document.getElementById(this.props.modal_id);
				const close_button = modal.querySelector(".modal-close");
				close_button.onclick = function() {
						self.validator.hide_all_validations_msgs();
						instance.close_modal.call(instance);
				}

				// Create filter if neccesary and wire up events
				const controls = document.querySelectorAll('[data-filter="on"]');

				for(let i = 0; i < controls.length; i++) {
						if (controls[i].nodeName != "SELECT")
								continue;

						const input = document.createElement("input");
						input.setAttribute("type", "text");
						input.setAttribute("class", "form-input modal-input");
						input.setAttribute("placeholder", "Filter...");
						let current_control = controls[i];
						const options_list = self.create_options_list(current_control.options);
						
						input.addEventListener("input", function() { self.filter_event_listener(current_control, input, options_list ) });

						controls[i].insertAdjacentElement("beforebegin", input);
						controls[i].dataset.filter = "done"

						if  (controls[i].hasAttribute("data-focusout")) {
								input.addEventListener("focusout", function() { window[controls[i].dataset.focusout](); });
						}
						
				}
		}

		create_options_list(options)
		{
				let result = [];

				for(let i = 0; i < options.length; i++)
						result.push({ value: options[i].value, text: options[i].text });

				return result;
		}

		filter_event_listener(select, input, full_list)
		{
				// remove all options
				select.innerHTML = "";

				for(let i = 0; i < full_list.length; i++) {
						if (full_list[i].text.toUpperCase().includes(input.value.toUpperCase())) {
								const opt = document.createElement("option");
								opt.value = full_list[i].value;
								opt.text = full_list[i].text;
								select.appendChild(opt);
						}
				}
		}

		create_row(template_id, data_row, properties, actions)
		{
				const template = document.getElementById(template_id);
				const row = template.content.cloneNode(true);
				let td = row.querySelectorAll("td");

				const buttons = td[td.length  - 1].querySelectorAll("button")
		
				for (let j = 0; j < buttons.length; j++) {
						let instance = this;
						buttons[j].onclick = function() { actions[j].call(instance, data_row); }
				}
								
				for (let j = 0; j < properties.length; j++) {
						td[j].textContent = data_row[properties[j]];
				}

				return row;
		}


		action_open(item)
		{
				const url = this.props.open_url + "&" + this.props.open_url_param_name + "=" + item.id; 
				window.open(url, "_self");
		}

		action_edit(item)
		{
				const self = this;
				const modal = document.getElementById(this.props.modal_id);

				this.edit_item = item;

				for(var i = 0; i < this.props.edit_dialog_ids.length; i++) {
						document.getElementById(this.props.edit_dialog_ids[i]).value = item[this.props.edit_properties[i]];
				}

				document.getElementById(this.props.submit_button_id).textContent = "Update";
				let instance = this;
				document.getElementById(this.props.submit_button_id).onclick = function() {
						if (self.validator.validate_form())
								instance.update_item.call(instance)
				};

				modal.style.display = "block";

				if (this.props.modal_filled_fn)
						this.props.modal_filled_fn(item);
		}

		async action_delete(item)
		{
				var confirmation = confirm("Are you sure you want to delete this item?");

				if (confirmation) {
						const end_point = this.base_path + this.props.end_point + "/" + item.id;
						var response = await fetch(end_point, { method: "DELETE" });

						if (response.ok) {
								iziToast.success({ title: 'Success', message: 'Item successfully deleted' });
								const index = this.items.indexOf(item);
								document.getElementById(this.props.table_id).deleteRow(index + 1);
								this.items.splice(index, 1);

								this.close_modal();
						} else if (response.status == 403) {
								iziToast.error({ title: "Forbidden", message: "Not enough rights to delete item" });
						} else {
								const result = await response.json()
								iziToast.error({ title: "Error", message: result.Error ? result.Error : "Item could not be deleted"});
						}
				
				}
		}

		async update_item()
		{
				if (this.props.create_item_from_modal_fn) {
						this.props.create_item_from_modal_fn(this.edit_item, this);
				} else {
						for(var i = 0; i < this.props.edit_dialog_ids.length; i++) {
								this.edit_item[this.props.edit_properties[i]] = document.getElementById(this.props.edit_dialog_ids[i]).value;
						}
				}

				const end_point = this.base_path + this.props.end_point + "/" + this.edit_item.id;
				var response = await fetch(end_point,
																	 { method: 'put', headers: { 'Content-Type': 'application/json' },
																		 body: JSON.stringify(this.edit_item) });
				if (response.ok) {
						iziToast.success({ title: "Success", message: "Item successfully updated" });
				
						const table = document.getElementById(this.props.table_id);
						const row = table.rows[this.items.indexOf(this.edit_item) + 1];

						for (var i = 0; i < this.props.properties.length; i++) {
								row.cells[i].textContent = this.edit_item[this.props.properties[i]];
						}
						
						this.close_modal();
				
				} else {
						const result = await response.json()
						iziToast.error({ title: "Error", message: result.Error ? result.Error : "Item could not be updated" });
				}
		}

		close_modal()
		{
				document.getElementById(this.props.modal_id).style.display = "none";
		}
}


class DomBinder
{
		constructor(view_model, update_callback)
		{
				this.view_model = view_model;
				this.update_callback = update_callback;
		}


		apply_bindings()
		{
				const self = this;

				let bind_name_split = "";
				
				// fill all data-bind controls
				const controls = document.querySelectorAll('[data-bind]');

				for (let i = 0; i < controls.length; i++) {
						const bind_name = controls[i].getAttribute("data-bind");

						if (bind_name.includes(".")) {
								bind_name_split = bind_name.split(".");

								let value = this.view_model[bind_name_split[0]];
								for(let j = 1; j < bind_name_split.length; j++) {
										value = value[bind_name_split[j]];
								}

								this.set_control_value(controls[i], value);
								//controls[i].value = value;
						} else {
								this.set_control_value(controls[i], this.view_model[bind_name]);
								//controls[i].value = this.view_model[bind_name];
						}

						// Register event listener to write changes back to object
						controls[i].addEventListener("change", function() { self.event_listener(controls[i], bind_name); });
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
								self.add_row(template, value, { xval: 0, yval: 0 }, tbody);
						});

						let value = {};
						if (bind_name.includes(".")) {
								bind_name_split = bind_name.split(".");

								value = this.view_model[bind_name_split[0]];
								for(let j = 1; j < bind_name_split.length; j++) {
										value = value[bind_name_split[j]];
								}
						} else {
								value = this.view_model[bind_name];
						}

						
						for(let j = 0; j < value.length; j++) {
								this.add_row(template, value, value[j], tbody);
						}
				}
		}

		get_control_value(control)
		{
				if (control.nodeName == "INPUT" && control.getAttribute("type") == "checkbox")
						return control.checked;

				return isNaN(parseFloat(control.value)) ? control.value : parseFloat(control.value);
		}

		set_control_value(control, value)
		{
				if (control.nodeName == "INPUT" && control.getAttribute("type") == "checkbox")
						control.setAttribute("checked", "checked");
				else
						control.value = value;
		}

		event_listener(control, bind_name)
		{
				const control_value = this.get_control_value(control);
		
				if (bind_name.includes(".")) {
						let bind_name_split = bind_name.split(".");

						let value = this.view_model[bind_name_split[0]];
						for(let j = 1; j < bind_name_split.length; j++) {
								if (j == bind_name_split.length - 1)
										value[bind_name_split[j]] = control_value;
								else
										value = value[bind_name_split[j]];
						}
				} else {
						this.view_model[bind_name] = control_value;
				}

				if (this.update_callback)
						this.update_callback();
		}


		event_listener_array_element(control, array, index, property)
		{
				const control_value = isNaN(parseFloat(control.value)) ? control.value : parseFloat(control.value);
		
				array[index][property] = control_value;

				if (this.update_callback)
						this.update_callback();
		}

		event_listener_remove_array_element(array, object, control)
		{
				array.splice(array.indexOf(object), 1);
				control.parentElement.remove();

				if (this.update_callback)
						this.update_callback();

		}


		add_row(row_template, array, object, parent)
		{
				const self = this;
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

				for (var prop in object) {
						if (Object.prototype.hasOwnProperty.call(object, prop)) {
								let input = td[i++].firstChild;
								input.value = object[prop];

								let current_property = prop;
								input.addEventListener("change", function() {
										self.event_listener_array_element(input, array, index, current_property);
								});
						}
				}

				td[i].firstElementChild.addEventListener("click", function() {
						self.event_listener_remove_array_element(array, object, td[i]);
				});

				

				parent.appendChild(row);
		}
}


function draw_packet(container, canvas, draw_packet)
{
		const padding_left = window.getComputedStyle(container, null).getPropertyValue("padding-left");
		const padding_right = window.getComputedStyle(container, null).getPropertyValue("padding-right");
		const dpr = Math.ceil(window.devicePixelRatio) || 1;
		const rect = container.getBoundingClientRect();
		const width = Math.floor(rect.width - parseInt(padding_left) - parseInt(padding_right)) - 1;
		const height = 50;

		canvas.style.width = `${width}px`;
		canvas.style.height = "52px";
		canvas.width = (width + 1) * dpr;
		canvas.height = (height + 2) * dpr;

		const ctx = canvas.getContext("2d");
		ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
		ctx.translate(0.5,0.5);

		const text_padding_top = 5 + 14;
		const text_padding_left = 5;

		const parameter = draw_packet.parameter;
		
		// let total_size = 0;
		// for (let i = 0; i < parameter.length; i++)
		// 		total_size += parameter[i].size;

		// if (draw_packet.draw_crc)
		// 		total_size += 16

		const total_size = packet_total_size(draw_packet);
		
		const bit_size = Math.floor(rect.width / total_size);

		ctx.strokeStyle = "#000";
		ctx.font = "normal 14px Sans-serif";

		let pos = 0;

		// draw parameter
		for (var i = 0; i < parameter.length; i++) {
				if (parameter[i].name == "TM Header" || parameter[i].name == "TC Header")
						continue;
				
				ctx.fillStyle = parameter[i].color;
				ctx.fillRect(pos, 0, parameter[i].size * bit_size, height);
				ctx.strokeRect(pos + 1, 1, parameter[i].size * bit_size, height);

				ctx.fillStyle = "#000";
				ctx.fillText(parameter[i].name, pos + text_padding_left, text_padding_top);
				ctx.fillText(`(${parameter[i].size}Bit)`, pos + text_padding_left, text_padding_top + 20);

				pos += parameter[i].size * bit_size;
		}

		// if (draw_packet.draw_crc) {
		// 		// add crc
		// 		ctx.fillStyle = "#FFA500";
		// 		ctx.fillRect(pos, 0, 16 * bit_size, canvas.height);
		// 		ctx.strokeRect(pos + 1, 1, 16 * bit_size, canvas.height - 1);

		// 		ctx.fillStyle = "#000";
		// 		ctx.fillText("CRC", pos + text_padding_left, text_padding_top);
		// 		ctx.fillText("(16Bit)", pos + text_padding_left, text_padding_top + 20);
		// }
}

function draw_packet_alt(container, canvas, draw_packet)
{
		const padding_left = window.getComputedStyle(container, null).getPropertyValue("padding-left");
		const padding_right = window.getComputedStyle(container, null).getPropertyValue("padding-right");
		const dpr = Math.ceil(window.devicePixelRatio) || 1;
		const rect = container.getBoundingClientRect();
		const width = Math.floor(rect.width - parseInt(padding_left) - parseInt(padding_right));
		const line_height = 50;
		const line_header_width = 100;
		const header_height = 28;
		const header_background = "#EFEFEF";
		const text_fill_style = "#000";
		const text_padding_top = 5 + 14;
		const text_padding_left = 5;
		const parameter = draw_packet.parameter;
		const ctx = canvas.getContext("2d");

		canvas.style.width = `${width}px`;
		canvas.width = width * dpr;

		const total_size = packet_total_size(draw_packet);
		/* 32 bits per line */
		const lines = Math.ceil(total_size / 32);
		let draw_width = width - 1;

		/* Ensure every bit can be aligned to pixel grid */
		while (draw_width % 32 != 0)
				draw_width--;

		canvas.style.height = (lines * line_height) + header_height + 1;
		canvas.height = ((lines * line_height) + header_height + 1) * dpr;

		ctx.setTransform(dpr, 0, 0, dpr, 0, 0);		
		ctx.translate(0.5,0.5);
		ctx.strokeStyle = "#000";
		ctx.font = "normal 14px Sans-serif";

		const byte_width = (draw_width - line_header_width) / 4;
		const bit_width = byte_width / 8;
		let text_len;

		/* draw header */
		ctx.fillStyle = header_background;
		ctx.fillRect(0, 0, line_header_width, header_height);
		ctx.strokeRect(0, 0, line_header_width, header_height);

		text_len = ctx.measureText("Bit offset").width;
		ctx.fillStyle = text_fill_style;
		ctx.fillText("Bit offset", (line_header_width - text_len) / 2.0, text_padding_top);

		hdr_text = [ "0-7", "8-15", "16-23" , "24-31" ]
		
		for (let i = 0; i < 4; i++) {
				ctx.fillStyle = header_background;
				ctx.fillRect(line_header_width + i * byte_width, 0, byte_width, header_height);
				ctx.strokeRect(line_header_width + i * byte_width, 0, byte_width, header_height);

				text_len = ctx.measureText(hdr_text[i]).width;
				ctx.fillStyle = text_fill_style;
				ctx.fillText(hdr_text[i], line_header_width + i * byte_width + (byte_width - text_len) / 2.0, text_padding_top);
		}

		let line_bit_pos = 0;
		let draw_line_pos = header_height;
		let bit_offset = 0;
		let pkt_bit_offset = 0;

		/* draw line header */
		ctx.fillStyle = header_background;
		ctx.fillRect(0, draw_line_pos, line_header_width, line_height);
		ctx.strokeRect(0, draw_line_pos, line_header_width, line_height);
		text_len = ctx.measureText(bit_offset.toString()).width;
		ctx.fillStyle = text_fill_style;
		ctx.fillText(bit_offset.toString(), (line_header_width - text_len) / 2.0, draw_line_pos + text_padding_top);
		
		
		/* draw parameter */
		for (let i = 0; i < parameter.length; i++) {
				let param_size = parameter[i].size;
				do {
						if (line_bit_pos >= 32) {
								line_bit_pos = 0;
								draw_line_pos += line_height;

								/* draw line header */
								bit_offset += 32;
								ctx.fillStyle = header_background;
								ctx.fillRect(0, draw_line_pos, line_header_width, line_height);
								ctx.strokeRect(0, draw_line_pos, line_header_width, line_height);
								text_len = ctx.measureText(bit_offset.toString()).width;
								ctx.fillStyle = text_fill_style;
								ctx.fillText(bit_offset.toString(), (line_header_width - text_len) / 2.0, draw_line_pos + text_padding_top);
						}
						
						const draw_size = Math.min(param_size, 32 - line_bit_pos);
						const col_start = line_bit_pos * bit_width + line_header_width;

						ctx.fillStyle = parameter[i].color;
						ctx.fillRect(col_start, draw_line_pos, draw_size * bit_width, line_height);
						ctx.strokeRect(col_start, draw_line_pos, draw_size * bit_width, line_height);

						if (param_size == parameter[i].size) {
								ctx.fillStyle = "#000";
								ctx.fillText(transform_param_name(parameter[i].name), col_start + text_padding_left, draw_line_pos + text_padding_top);
								ctx.fillText(param_size + " Bit", col_start + text_padding_left, draw_line_pos + text_padding_top + 20);
						}

						line_bit_pos += param_size;
						param_size -= draw_size;
				} while(param_size > 0)
				pkt_bit_offset += parameter[i].size;
		}
		
}

function transform_param_name(param_name)
{
		return param_name.substring(param_name.indexOf("/") + 1);
}

function packet_total_size(draw_packet)
{
		const parameter = draw_packet.parameter;

		let total_size = 0;
		for (let i = 0; i < parameter.length; i++) {
				if (parameter[i].name != "TM Header" && parameter[i].name != "TC Header")
						total_size += parameter[i].size;
		}

		// if (draw_packet.draw_crc)
		// 		total_size += 16

		return total_size;
}
