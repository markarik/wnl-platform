import axios from 'axios'

export function getCurrent() {
	return axios.get('/papi/v1/users/current');
}
