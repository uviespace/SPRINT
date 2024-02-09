const urlParams = new URLSearchParams(window.location.search);

const packet_end_point = `api/v2/projects/${urlParams.get("idProject")}` +
			`/standards/${urlParams.get("idStandard")/packets}`;


var packets = [];


function load_base_data()
{
		const response = await fetch(packet_end_point);
		packets = await response.json();
}





window.onload = load_base_data();
