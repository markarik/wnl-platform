import { mount } from '@vue/test-utils';
import { expect } from 'chai';
import Alert from 'js/components/global/Alert';

describe('Alert.vue', () => {
	test('renders message', () => {
		const component = mount(Alert, { propsData: { alert: { message: 'Test' } }, sync: false });
		expect(component.html()).to.include('Test');
	});
});
