import Errors from './Errors'
import axios from 'axios'

class Form {
	constructor(data) {
		this.originalData = data

		for (let field in data) {
			this[field] = data[field]
		}

		this.errors = new Errors()
	}

	data() {
		let data = Object.assign({}, this)

		delete(data.originalData)
		delete(data.errors)

		return data
	}

	reset() {
		for (let field in this.originalData) {
			this[field] = null
		}
	}

	submit(requestType, apiUrl) {
		axios[requestType](apiUrl, this.data())
			.then(this.onSuccess.bind(this))
			.catch(this.onFail.bind(this))
	}

	onSuccess(response) {
		console.log('Success!')
		console.log(response)
	}

	onFail(error) {
		this.errors.record(error.response.data)
	}
}

export { Form as default }
