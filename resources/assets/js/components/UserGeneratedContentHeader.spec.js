import { shallowMount, createLocalVue } from '@vue/test-utils';
import { describe, it } from 'mocha';
import { expect } from 'chai';
import Vuex from 'vuex';

import UserGeneratedContentHeaderComponent from 'js/components/UserGeneratedContentHeader';

const localVue = createLocalVue();
localVue.use(Vuex);
localVue.component('wnl-avatar', '<div></div>');
localVue.directive('t', (args) => ({ ...args }));

describe('UserGeneratedContentHeader.vue', () => {
	let store;

	const avatarStub = '<div id="avatar"></div>';
	const resolveStub = '<div id="resolve"></div>';
	const verifyStub = '<div id="verify"></div>';
	const deleteStub = '<div id="delete"></div>';

	const createComponent = ({ propsData = {} } = {} ) => {
		return shallowMount(UserGeneratedContentHeaderComponent, {
			store,
			localVue,
			propsData: {
				author: { id : 1 },
				content: { created_at: '1' },
				...propsData
			},
			stubs: {
				WnlAvatar: avatarStub,
				WnlResolve: resolveStub,
				WnlVerify: verifyStub,
				WnlDelete: deleteStub
			},
			sync: false
		});
	};

	it('renders correctly for regular user', () => {
		const wrapper = createComponent();
		expect(wrapper.findAll('#avatar').length).to.equal(1);
		expect(wrapper.findAll('#resolve').length).to.equal(0);
		expect(wrapper.findAll('#delete').length).to.equal(0);
		expect(wrapper.findAll('#verify').length).to.equal(1);
	});

	it('renders correctly when user can delete', () => {
		const wrapper = createComponent({
			propsData: { canDelete: true }
		});
		expect(wrapper.findAll('#avatar').length).to.equal(1);
		expect(wrapper.findAll('#resolve').length).to.equal(0);
		expect(wrapper.findAll('#delete').length).to.equal(1);
		expect(wrapper.findAll('#verify').length).to.equal(1);
	});

	it('renders correctly when user can resolve', () => {
		const wrapper = createComponent({
			propsData: { resolvable: true }
		});
		expect(wrapper.findAll('#avatar').length).to.equal(1);
		expect(wrapper.findAll('#resolve').length).to.equal(1);
		expect(wrapper.findAll('#delete').length).to.equal(0);
		expect(wrapper.findAll('#verify').length).to.equal(1);
	});

	it('shows modal on avatar click', () => {
		const wrapper = createComponent();

		const avatar = wrapper.find('#avatar');
		expect(wrapper.vm.modalVisible).to.equal(false);
		avatar.trigger('click.native');
		expect(wrapper.vm.modalVisible).to.equal(true);
	});

	it('emits event on successful deletion', () => {
		const wrapper = createComponent({
			propsData: { canDelete: true }
		});

		const deleteComponent = wrapper.find('#delete');
		deleteComponent.vm.$emit('deleteSuccess');
		expect(Object.keys(wrapper.emitted()).includes('deleteSuccess')).to.equal(true);
	});

	it('emits resolve events correctly', () => {
		const wrapper = createComponent({
			propsData: { resolvable: true }
		});

		const resolveComponent = wrapper.find('#resolve');
		resolveComponent.vm.$emit('resolveResource');
		expect(Object.keys(wrapper.emitted()).includes('resolveResource')).to.equal(true);

		resolveComponent.vm.$emit('unresolveResource');
		expect(Object.keys(wrapper.emitted()).includes('unresolveResource')).to.equal(true);
	});

	it('emits verify events correctly', () => {
		const wrapper = createComponent();

		const verifyComponent = wrapper.find('#verify');
		verifyComponent.vm.$emit('verify');
		expect(Object.keys(wrapper.emitted()).includes('verify')).to.equal(true);

		verifyComponent.vm.$emit('unverify');
		expect(Object.keys(wrapper.emitted()).includes('unverify')).to.equal(true);
	});
});
