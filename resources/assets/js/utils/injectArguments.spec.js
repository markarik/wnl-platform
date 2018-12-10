import {expect} from 'chai';
import injectArguments from './injectArguments.js';

describe('injectArguments util', () => {
	it('parses existing arguments', () => {
		expect(injectArguments(
			'Witaj {{currentUserName}}!',
			{
				currentUserName: {value: 'Zosia'},
				ignore: 'Yes please'
			}
		)).to.eql('Witaj Zosia!');
	});
	it('doesn\'t fail on missing arguments', () => {
		expect(injectArguments(
			'Witaj {{currentUserName}}!',
			{}
		)).to.eql('Witaj !');
	});
});
