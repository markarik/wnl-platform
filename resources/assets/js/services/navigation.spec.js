import Vue from 'vue';
import {expect} from 'chai';
import navigation from './navigation.js';

describe('navigation service', () => {
	let store;
	let getters;

	describe('composeItem', () => {
		it('default arguments as set as expected', () => {
			expect(navigation.composeItem({
				text: 'foo',
				itemClass: 'wnl-foo',
				routeName: 'foo-route',
				routeParams: {fizz: 'buzz'},
				iconClass: 'wnl-foo-icon',
				iconTitle: 'wnl-foo-title',
				meta: 'bar'
			})).to.eql({
				text: 'foo',
				itemClass: 'wnl-foo',
				to: {
					name: 'foo-route',
					params: {fizz: 'buzz'}
				},
				iconClass: 'wnl-foo-icon',
				iconTitle: 'wnl-foo-title',
				meta: 'bar',
				isDisabled: false,
				method: 'push',
				completed: false,
				active: false,
			});
		});
	});
});

