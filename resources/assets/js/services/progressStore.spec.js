import {assert} from 'chai';
import {spy, stub} from 'sinon';
import {noop} from 'lodash';
import progressStore from './progressStore';

describe('progressStore', () => {
	const fakeStore = {
		get: noop,
		set: noop,
		remove: noop
	};

	beforeEach(() => {
		progressStore.__Rewire__('store', fakeStore);
	});

	afterEach(() => {
		progressStore.__ResetDependency__('store');
	});

	it('calls store get method', function () {
		const getStub = stub(fakeStore, 'get').returns('bar');
		const returnVal = progressStore.get('foo');
		expect(getStub).to.have.been.called;
		expect(returnVal).to.equal('bar');
	});

	it('calls store set method', function () {
		const setStub = stub(fakeStore, 'set');
		progressStore.set('foo');
		expect(setStub).to.have.been.called;
	});

	it('calls store remove method', function () {
		const removeStub = stub(fakeStore, 'remove');
		progressStore.remove('foo');
		expect(removeStub).to.have.been.called;
	});
});
