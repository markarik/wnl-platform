import Vue from 'vue';
import {mount} from 'avoriaz';
import sinon from 'sinon';
import {expect} from 'chai';
import Vuex from 'vuex';
import ActiveUsers from './ActiveUsers.vue';
import Avatar from '../global/Avatar.vue';

Vue.use(Vuex);
Vue.component('wnl-avatar', Avatar)

describe('ActiveUsers.vue', () => {
	let state;
	let store;
	let getters;

	beforeEach(() => {
		getters = {
			activeUsers: () => [
				{fullName: 'foo bar', avatar: null, id: 7}, {fullName: 'buzz fizz', avatar: null, id: 10}
			],
			currentUserId: () => 7,
		};
		store = new Vuex.Store({
			getters,
		});
	});

	it('Renders avatars base on activeUsers', () => {
		const wrapper = mount(ActiveUsers, {store});
		expect(wrapper.find(Avatar).length).to.equal(2);
	});
});
