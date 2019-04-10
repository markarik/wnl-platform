import {createLocalVue, shallowMount} from '@vue/test-utils';
import axios from 'axios';
import {expect} from 'chai';
import {describe, it, afterEach} from 'mocha';
import sinon from 'sinon';
import Vuex from 'vuex';

import SplashScreen from 'js/components/global/splashscreens/SplashScreen';
import TextLoader from 'js/components/global/TextLoader.vue';

const localVue = createLocalVue();
localVue.use(Vuex);
localVue.component('wnl-text-loader', TextLoader);

function fakeFetchOrders(data = null) {
	if (data === null) {
		data = [
			{
				product: {
					slug: 'wnl-album',
				},
				canceled: false,
			},
			{
				product: {
					slug: 'wnl-online',
				},
				canceled: false,
				paid: true,
			},
			{
				product: {
					slug: 'wnl-online',
				},
				canceled: true,
				paid: false,
			}
		];
	}

	const axiosGetFake = sinon.fake.returns({data: data});
	sinon.replace(axios, 'get', axiosGetFake);
}

async function waitForLoadingState() {
// We need to wait for `this.isLoading = true`, didn't find a better method yet
	await localVue.nextTick();
	await localVue.nextTick();
}

function getComponent(attrs) {
	return shallowMount(SplashScreen, {
		localVue,
		sync: false,
		mocks: {
			'$upcomingEditionParticipant': {
				isAllowed: () => false
			}
		},
		...attrs
	});
}

describe('SplashScreen.vue', () => {
	afterEach(() => {
		sinon.restore();
	});

	it('renders wnl-splash-screen-generic-error', async () => {
		const store = new Vuex.Store({
			getters: {
				currentUserLoadingError: () => true,
			},
		});

		fakeFetchOrders();

		const component = getComponent({store});
		await waitForLoadingState();

		expect(component.html()).to.include('<wnl-splash-screen-generic-error-stub');
	});

	it('renders wnl-splash-screen-account-suspended', async () => {
		const store = new Vuex.Store({
			getters: {
				currentUserLoadingError: () => false,
				currentUserAccountSuspended: () => true,
				currentUserSubscriptionStatus: () => 'inactive',
			},
		});

		fakeFetchOrders();

		const component = getComponent({store});
		await waitForLoadingState();

		expect(component.html()).to.include('<wnl-splash-screen-account-suspended-stub');
	});

	it('renders wnl-splash-screen-order-canceled', async () => {
		const store = new Vuex.Store({
			getters: {
				currentUserLoadingError: () => false,
				currentUserAccountSuspended: () => false,
				currentUserSubscriptionStatus: () => 'inactive',
			},
		});

		fakeFetchOrders([
			{
				product: {
					slug: 'wnl-album',
				},
				canceled: false,
			},
			{
				product: {
					slug: 'wnl-online',
				},
				canceled: true,
			},
			{
				product: {
					slug: 'wnl-online',
				},
				canceled: true,
			}
		]);

		const component = getComponent({store});
		await waitForLoadingState();

		expect(component.html()).to.include('<wnl-splash-screen-order-canceled-stub');
	});

	it('renders wnl-splash-screen-upcoming-edition', async () => {
		const store = new Vuex.Store({
			getters: {
				currentUserLoadingError: () => false,
				currentUserAccountSuspended: () => false,
				currentUserSubscriptionStatus: () => 'awaiting',
			},
		});

		fakeFetchOrders();

		const component = getComponent({
			store,
			mocks: {
				'$upcomingEditionParticipant': {
					isAllowed: () => true
				}
			},
		});
		await waitForLoadingState();

		expect(component.html()).to.include('<wnl-splash-screen-upcoming-edition-stub');
	});

	it('renders wnl-splash-screen-order-not-paid', async () => {
		const store = new Vuex.Store({
			getters: {
				currentUserLoadingError: () => false,
				currentUserAccountSuspended: () => false,
				currentUserSubscriptionStatus: () => 'inactive',
			},
		});

		fakeFetchOrders([
			{
				product: {
					slug: 'wnl-album',
				},
				canceled: false,
			},
			{
				product: {
					slug: 'wnl-online',
				},
				canceled: false,
				paid: false,
			},
			{
				product: {
					slug: 'wnl-online',
				},
				canceled: true,
				paid: false,
			}
		]);

		const component = getComponent({store});
		await waitForLoadingState();

		expect(component.html()).to.include('<wnl-splash-screen-order-not-paid-stub');
	});

	it('renders wnl-splash-screen-subscription-expired', async () => {
		const store = new Vuex.Store({
			getters: {
				currentUserLoadingError: () => false,
				currentUserAccountSuspended: () => false,
				currentUserSubscriptionStatus: () => 'expired',
			},
		});

		fakeFetchOrders();

		const component = getComponent({store});
		await waitForLoadingState();

		expect(component.html()).to.include('<wnl-splash-screen-subscription-expired');
	});

	it('renders wnl-splash-screen-default', async () => {
		const store = new Vuex.Store({
			getters: {
				currentUserLoadingError: () => false,
				currentUserAccountSuspended: () => false,
				currentUserSubscriptionStatus: () => 'inactive',
			},
		});

		fakeFetchOrders();

		const component = getComponent({store});
		await waitForLoadingState();

		expect(component.html()).to.include('<wnl-splash-screen-default');
	});
});
