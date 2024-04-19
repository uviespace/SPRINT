

async function set_calibration_curve(id)
{
		const urlParams = new URLSearchParams(window.location.search);
		const select = document.getElementById(id);
		const curve_id = parseInt(select.value);

		const end_point = `api/v2/projects/${urlParams.get("idProject")}` +
					`/standards/${urlParams.get("idStandard")}` +
					`/parameters/${id}/calibration_curve/${curve_id}`

		

		var response = await fetch(end_point, { method: 'post', headers: { 'Content-Type': 'application/json'}, body: '' });
		
		if (response.ok)
				iziToast.success({ title: "Success", message: "Calibration successfully applied"});
		else
				iziToast.error({ title: "Error", message: "Calibration could not be saved" });													 
}
