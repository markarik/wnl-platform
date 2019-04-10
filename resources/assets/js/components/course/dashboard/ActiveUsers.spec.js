import {shallowMount, createLocalVue} from '@vue/test-utils';
import {describe, it, beforeEach} from 'mocha';
import {expect} from 'chai';

import Vuex from 'vuex';
import ActiveUsers from 'js/components/course/dashboard/ActiveUsers';
import Avatar from 'js/components/global/Avatar.vue';

const localVue = createLocalVue();
localVue.use(Vuex);
localVue.component('wnl-avatar', Avatar);
localVue.directive('t', (args) => ({...args}));

describe('ActiveUsers.vue', () => {
	let store;
	let getters;
	let modules;

	describe('when users active', () => {
		beforeEach(() => {
			modules = {
				users: {
					namespaced: true,
					getters: {
						activeUsers: () => (channel) => [
							{id: 7, profile: {full_name: 'foo bar', avatar: null}},
							{id: 10, profile: {full_name: 'buzz fizz', avatar: null}},
						]
					}
				}
			};
			getters = {
				currentUserId: () => 7,
			};
			store = new Vuex.Store({
				getters,
				modules
			});
		});

		it('Counts users correctly', () => {
			const wrapper = shallowMount(ActiveUsers, {store, localVue, sync: false});
			expect(wrapper.vm.activeUsersCount).to.equal(1);
		});
	});

	describe('when no users', () => {
		beforeEach(() => {
			modules = {
				users: {
					namespaced: true,
					getters: {
						activeUsers: () => (channel) => []
					}
				}
			};
			getters = {
				currentUserId: () => 7,
			};
			store = new Vuex.Store({
				getters,
				modules
			});
		});

		it('Renders message', () => {
			const wrapper = shallowMount(ActiveUsers, {localVue, store, sync: false});
			expect(wrapper.findAll('.avatar').length).to.equal(0);
			expect(wrapper.vm.activeUsersCount).to.equal(0);
			expect(wrapper.findAll('.active-users-container').length).to.equal(0);
		});
	});
});
