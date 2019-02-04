import {set} from 'vue';
import {getApiUrl} from 'js/utils/env';

// Initial state
const state = {
	currentRoom: '',
	loaded: false,
	messages: [],
	users: []
};

// Getters
const getters = {
	currentRoom: state => state.currentRoom,
	loaded: state => state.loaded,
	messages: state => state.messages
};

// Actions
const actions = {
	saveMentions({ commit }, data) {
		return axios.post(getApiUrl('events/mentions'), data);
	}
};

export default {
	state,
	getters,
	actions
};
