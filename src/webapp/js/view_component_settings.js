const urlParams = new URLSearchParams(window.location.search);
const end_point = `api/v2/projects/${urlParams.get("idProject")}` +
			`/applications/${urlParams.get("idApplication")}` +
			`/component_settings/${urlParams.get("idComponent")}`;


document.addEventListener("DOMContentLoaded", function () {
		const nodes = draw_form(component_schema);

		for (var i = 0; i < nodes.length; i++)
				document.getElementById("settings_form").appendChild(nodes[i]);

		const binder = new DomBinder(component_settings);
		binder.apply_bindings();
});


async function save_settings()
{
		var response = await fetch(end_point,
															 { method: 'post' , headers: { 'Content-Type': 'application/json'},
															   body: JSON.stringify(component_settings) });
		if (response.ok)
				iziToast.success({ title: "Success", message: "Settings successfully updated" });
		else
				iziToast.error({ title: "Error", message: "Settings could not be saved" }); 
}

function draw_form(schema)
{
		const node_list = [];

		for (var i = 0; i < schema.length; i++)
				node_list.push.apply(node_list, draw_node(schema[i], ""));

		return node_list;
}

function draw_node(node, prefix)
{
		const node_list = [];

		const label = document.createElement('label');
		label.innerHTML = node.node_name + ": "

		node_list.push(label);

		if (node.type == "object") {
				label.setAttribute("class", "settings-label-top-level")
				for (var i = 0; i < node.children.length; i++)
						node_list.push.apply(node_list, draw_node(node.children[i], prefix + node.node_name + "."));
		} else if (node.type == "number" || node.type == "text") {
				label.setAttribute("class", "settings-label-standard");

				const input = document.createElement('input');
				input.setAttribute("name", node.node_name);
				input.setAttribute("class", "form-input modal-input");
				input.setAttribute("type", node.type);
				input.setAttribute("data-bind", prefix + node.node_name);
				node_list.push(input);
		} else if (node.type == "bool") {
				label.setAttribute("class", "settings-label-standard");
				
				const switch_label = document.createElement('label');
				switch_label.setAttribute("class", "switch");

				const input = document.createElement('input');
				input.setAttribute("type", "checkbox");
				input.setAttribute("data-bind", prefix + node.node_name);

				const span = document.createElement('span');
				span.setAttribute("class", "slider");

				switch_label.appendChild(input);
				switch_label.appendChild(span);

				node_list.push(switch_label);
		} else if (node.type == "array") {
				label.setAttribute("class", "settings-label-standard");
		}

		return node_list;
}
