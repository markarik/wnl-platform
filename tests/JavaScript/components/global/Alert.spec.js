import {mount} from '@vue/test-utils';
import {describe} from 'mocha';
import expect from 'expect';
import Alert from 'js/components/global/Alert';

describe('Alert', () => {
	let component;

	beforeEach(() => {
		component = mount(Alert, {propsData: {alert: {message: 'Test'}}});
	});

	it('renders message', () => {
		expect(component.html()).toContain('Test');
	});
});
