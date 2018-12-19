export function downloadFile(responseData, fileName) {
	const data = window.URL.createObjectURL(responseData);
	const link = document.createElement('a');
	link.style.display = 'none';
	// For Firefox it is necessary to insert the link into body
	document.body.appendChild(link);
	link.href = data;
	link.setAttribute('download', fileName);
	link.click();

	setTimeout(function() {
		window.URL.revokeObjectURL(link.href);
		document.removeChild(link);
	}, 100);
}
