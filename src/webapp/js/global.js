/*
 * prop: Properties
 *
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
				this.props = props;
				this.base_path = "";
		}

		async load_items()
		{
				const response = await fetch(this.props.end_point);
				this.items = await response.json();

				this.fill_table();
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
				close_button.onclick = function() { instance.close_modal.call(instance);  }

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
				const modal = document.getElementById(this.props.modal_id);

				this.edit_item = item;

				for(var i = 0; i < this.props.edit_dialog_ids.length; i++) {
						document.getElementById(this.props.edit_dialog_ids[i]).value = item[this.props.edit_properties[i]];
				}

				document.getElementById(this.props.submit_button_id).textContent = "Update";
				let instance = this;
				document.getElementById(this.props.submit_button_id).onclick = function() { instance.update_item.call(instance) };

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

								controls[i].value = value;
						} else {
								controls[i].value = this.view_model[bind_name];
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

		event_listener(control, bind_name)
		{
				const control_value = isNaN(parseFloat(control.value)) ? control.value : parseFloat(control.value);
		
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
						update_chart();
		}

		event_listener_remove_array_element(array, object, control)
		{
				array.splice(array.indexOf(object), 1);
				control.parentElement.remove();

				if (this.update_callback)
						update_callback();

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
