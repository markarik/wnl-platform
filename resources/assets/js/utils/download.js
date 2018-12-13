export async function download(url, fileName) {
	try {
		const response = await axios.request({
			url: getApiUrl(url),
			responseType: 'blob',
		})

		this.downloadFile(response.data, fileName)
	} catch (err) {
		this.handleDownloadFailure()
	}
}
