import Vue from 'vue';
import {mount} from 'avoriaz';
import {expect} from 'chai';
import Vuex from 'vuex';
import ActiveUsers from './ActiveUsers.vue';
import Avatar from 'js/components/global/Avatar.vue';
import VueI18n from 'vue-i18n';
import { pl } from 'js/i18n';

Vue.use(Vuex);
Vue.use(VueI18n);
Vue.config.lang = 'pl';

const messages = {pl};
const i18n = new VueI18n({fallbackLocal: 'pl', locale: 'pl', messages});

Vue.component('wnl-avatar', Avatar);

describe('ActiveUsers.vue', () => {
	let store;
	let getters;
	let modules;

	context('when users active', () => {
		beforeEach(() => {
			modules = {
				users: {
					namespaced: true,
					getters: {
						activeUsers: () => (channel) => [
							{fullName: 'foo bar', avatar: null, id: 7},
							{fullName: 'buzz fizz', avatar: null, id: 10}
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
			const wrapper = mount(ActiveUsers, {store, i18n});
			expect(wrapper.vm.activeUsersCount).to.equal(1);
		});
	});

	context('when no users', () => {
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
			const wrapper = mount(ActiveUsers, {store, i18n});
			expect(wrapper.find(Avatar).length).to.equal(0);
			expect(wrapper.vm.activeUsersCount).to.equal(0);
			expect(wrapper.find('.active-users-container').length).to.equal(0);
		});
	});
});
