import { expect } from 'chai';
import injectArguments from 'js/utils/injectArguments.js';

describe('injectArguments util', () => {
	test('parses existing arguments', () => {
		expect(injectArguments(
			'Witaj {{currentUserName}}!',
			{
				currentUserName: { value: 'Zosia' },
				ignore: 'Yes please'
			}
		)).to.eql('Witaj Zosia!');
	});
	test('doesn\'t fail on missing arguments', () => {

		expect(injectArguments(
			'Witaj {{currentUserName}}!',
			{}
		)).to.eql('Witaj !');
	});
});
