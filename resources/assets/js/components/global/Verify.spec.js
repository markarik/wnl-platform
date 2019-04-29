import { shallowMount, createLocalVue } from '@vue/test-utils';
import { expect } from 'chai';


import VerifyComponent from 'js/components/global/Verify';

const localVue = createLocalVue();


describe('Verify.vue', () => {
	const createComponent = ({ propsData = {} } = {} ) => {
		return shallowMount(VerifyComponent, {
			localVue,
			propsData: {
				resource: { verified_at: null },
				...propsData
			},
			stubs: {
				FileVerifiedSvg: '<div></div>',
			},
			sync: false
		});
	};

	test('renders correctly when content not verified', () => {
		const wrapper = createComponent();

		expect(wrapper.vm.message).to.equal('Zweryfikuj');
	});
});
