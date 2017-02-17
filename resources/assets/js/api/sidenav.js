import axios from 'axios'

export function getNavigation(url) {
	return axios.get(url)
}
