import {set} from 'vue'

import * as types from '../mutations-types'
import {getApiUrl} from 'js/utils/env'

const state = {
	siteWideMessages: []
}

const getters = {
	siteWideMessages: state => state.siteWideMessages
}

const mutations = {
	[types.SITE_WIDE_MESSAGES_SET_MESSAGES] (state, payload) {
		set(state, 'siteWideMessages', payload)
	},
}

const actions = {
	async fetchUserSiteWideMessages({commit, rootGetters}) {
		try {
			const response = await axios.get(getApiUrl(`users/${rootGetters.currentUserId}/site_wide_messages`))
			if (!Array.isArray(response.data)) {
				$wnl.logger.error(`Incorrect shape of response for siteWideMessages, ${response.data.toString()}`)
			}

			commit(types.SITE_WIDE_MESSAGES_SET_MESSAGES, response.data.filter(message => {
				return message.message && message.target && message.id
			}))
		} catch (e) {
			$wnl.logger.error(e)
		}
	},
	async updateSiteWideMessage({commit}, messageId) {
		try {
			await axios.put(getApiUrl(`site_wide_messages/${messageId}`), {
				read_at: new Date()
			})
		} catch (e) {
			$wnl.logger.error(e)
		}
	}
}

export default {
	namespaced: true,
	state,
	getters,
	mutations,
	actions,
}
