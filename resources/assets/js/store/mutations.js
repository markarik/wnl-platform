import * as types from './mutations-types'

export default {
	[types.SET_CURRENT_VIEW] (state, currentView) {
		state.currentView = currentView
	}
}
