function load_data()
{
		var user_handler = new TableHandler({
				table_id: "table_user",
				template_id: "table_user_row",
				properties: ["id", "name", "email" ],
				modal_id: "user_modal",
				edit_dialog_ids: [ "edit_user_name", "edit_user_email" ],
				edit_properties: [ "name", "email" ],
				submit_button_id: "user_submit_button",
				create_button_id: "create_user_button_id",
				end_point: "api/v2/users",
				create_item: {}
		});

		user_handler.load_items();
}



window.onload = load_data();
