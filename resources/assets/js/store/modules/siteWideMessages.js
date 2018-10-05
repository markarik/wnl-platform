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
	}
}

const actions = {
	async fetchUserSiteWideMessages({commit, rootGetters}) {
		try {
			const response = await axios.get(getApiUrl(`users/${rootGetters.currentUserId}/site_wide_messages`))
			commit(types.SITE_WIDE_MESSAGES_SET_MESSAGES, response.data)
		} catch (e) {
			$wnl.logger.error(e)
		}
	},
	async updateSiteWideMessage({commit}, messageId) {
		try {
			const response = await axios.put(getApiUrl(`site_wide_messages/${messageId}`), {
				read_at: new Date()
			})
			commit(types.SITE_WIDE_MESSAGES_UPDATE_MESSAGE, response.data)
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
