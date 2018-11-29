import axios from 'axios'
import Errors from './Errors'
import {cloneDeep} from "lodash";

export default class Form {
	/**
	 * Create a new Form instance.
	 *
	 * @param {object} data
	 */
	constructor(data) {
		this.originalData = cloneDeep(data);

		for (let field in data) {
			this[field] = data[field];
		}

		this.errors = new Errors();
	}


	/**
	 * Fetch all relevant data for the form.
	 */
	data() {
		let data = {};

		for (let property in this.originalData) {
			data[property] = this[property];
		}

		return data;
	}


	/**
	 * Reset the form fields.
	 */
	reset() {
		for (let field in this.originalData) {
			this[field] = '';
		}

		this.errors.clear();
	}


	/**
	 * Send a POST request to the given URL.
	 * .
	 * @param {string} url
	 */
	post(url) {
		return this.submit('post', url);
	}


	/**
	 * Send a PUT request to the given URL.
	 * .
	 * @param {string} url
	 */
	put(url) {
		return this.submit('put', url);
	}


	/**
	 * Send a PATCH request to the given URL.
	 * .
	 * @param {string} url
	 */
	patch(url) {
		return this.submit('patch', url);
	}


	/**
	 * Send a DELETE request to the given URL.
	 * .
	 * @param {string} url
	 */
	delete(url) {
		return this.submit('delete', url);
	}


	/**
	 * Submit the form.
	 *
	 * @param {string} requestType
	 * @param {string} url
	 * @param {object} payload
	 */
	submit(requestType, url, payload) {
		return new Promise((resolve, reject) => {
			axios[requestType](url, {...this.data(), ...payload})
				.then(response => {
					this.onSuccess(response.data);

					resolve(response.data);
				})
				.catch(error => {
					if (error.response.status === 422) {
						this.errors.record(_.get(error.response, 'data.errors', error.response.data));
					}
					reject(error);
				});
		});
	}

	/**
	 * Pre-fill the form with existing data.
	 *
	 * @param url
	 * @param exclude
	 */
	populate(url, exclude = []) {
		return axios.get(url)
			.then(response => {
				Object.keys(response.data).forEach((field) => {
					if (exclude.indexOf(field) > -1) {
						return false
					}
					this[field] = response.data[field]
					this.originalData[field] = cloneDeep(response.data[field])
				})
				return response.data
			})
	}

	/**
	 * Handle a successful form submission.
	 *
	 * @param {object} data
	 */
	onSuccess(data) {
		this.errors.clear();
	}

	onSubmit() {

	}
}
