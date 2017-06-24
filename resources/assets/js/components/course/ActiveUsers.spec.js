import Vue from 'vue';
import {mount} from 'avoriaz';
import {expect} from 'chai';
import Vuex from 'vuex';
import ActiveUsers from './ActiveUsers.vue';
import Avatar from '../global/Avatar.vue';

Vue.use(Vuex);
Vue.component('wnl-avatar', Avatar)

describe('ActiveUsers.vue', () => {
	let store;
	let getters;

	context('when users active', () => {
		beforeEach(() => {
			getters = {
				activeUsers: () => [
					{fullName: 'foo bar', avatar: null, id: 7},
					{fullName: 'buzz fizz', avatar: null, id: 10}
				],
				currentUserId: () => 7,
			};
			store = new Vuex.Store({
				getters,
			});
		});

		it('Renders avatars base on activeUsers', () => {
			const wrapper = mount(ActiveUsers, {store});
			expect(wrapper.find(Avatar).length).to.equal(1);
		});

		it('Renders title correctly', () => {
			const wrapper = mount(ActiveUsers, {store});
			expect(wrapper.first('.active-users-title').text()).to.equal('Uczą się teraz z Tobą (1)');
		});

		it('Counts users correctly', () => {
			const wrapper = mount(ActiveUsers, {store});
			expect(wrapper.vm.activeUsersCount).to.equal(1);
		});
	})

	context('when no users', () => {
		beforeEach(() => {
			getters = {
				activeUsers: () => [],
				currentUserId: () => 7,
			};
			store = new Vuex.Store({
				getters,
			});
		});

		it('Renders message', () => {
			const wrapper = mount(ActiveUsers, {store});
			expect(wrapper.find(Avatar).length).to.equal(0);
			expect(wrapper.vm.activeUsersCount).to.equal(0);
			expect(wrapper.find('.active-users-container').length).to.equal(0);
		});
	})
});
