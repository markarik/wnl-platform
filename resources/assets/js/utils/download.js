export async function download(url, fileName) {
	try {
		const response = await axios.request({
			url: getApiUrl(url),
			responseType: 'blob',
		});

		downloadFile(response.data, fileName);
	} catch (err) {
		handleDownloadFailure();
	}
}

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

export function handleDownloadFailure() {
	if (err.response.status === 404) {
		return this.addAutoDismissableAlert({
			text: 'Nie udało się znaleźć pliku. Spróbuj ponownie, jeśli problem nie ustąpi daj Nam znać :)',
			type: 'error'
		});
	}

	if (err.response.status === 403) {
		return this.addAutoDismissableAlert({
			text: 'Nie masz uprawnień do pobrania pliku.',
			type: 'error'
		});
	}

	this.addAutoDismissableAlert({
		text: 'Ups, coś poszło nie tak, spróbuj ponownie.',
		type: 'error'
	});

	$wnl.logger.capture(err);
}
