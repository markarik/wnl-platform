import * as types from './mutations-types'

export const setCurrentView = ({ commit }, currentView) => {
	commit(types.SET_CURRENT_VIEW, currentView)
}
