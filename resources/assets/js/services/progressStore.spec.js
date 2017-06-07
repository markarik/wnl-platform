import {assert} from 'chai';
import {stub} from 'sinon';
import {noop} from 'lodash';
import progressStore from './progressStore';

describe('progressStore', () => {
	const fakeStore = {
		get: noop,
		set: noop,
		remove: noop
	};

	const fakeAxios = {
		get: noop,
		patch: noop
	};

	const fakeGetApiUrl = (path) => `/fapi/v1/${path}`;

	beforeEach(() => {
		progressStore.__Rewire__('store', fakeStore);
		progressStore.__Rewire__('axios', fakeAxios);
		progressStore.__Rewire__('getApiUrl', fakeGetApiUrl);
	});

	afterEach(() => {
		progressStore.__ResetDependency__('store');
		progressStore.__ResetDependency__('axios');
		progressStore.__ResetDependency__('getApiUrl');
	});

	it('get calls store get method', function () {
		const getStub = stub(fakeStore, 'get').returns('bar');
		const returnVal = progressStore.get('foo');
		expect(getStub).to.have.been.called;
		expect(returnVal).to.equal('bar');
	});

	it('get calls API get method with expected params', function () {
		const patchStub = stub(fakeAxios, 'get');
		progressStore.get('foo', 1);
		expect(patchStub).to.have.been.calledWith(fakeGetApiUrl(`users/1/state`));
	});

	it('set calls store set method', function () {
		const setStub = stub(fakeStore, 'set');
		progressStore.set('foo');
		expect(setStub).to.have.been.called;
	});

	it('set calls API patch method with expected params', function () {
		const patchStub = stub(fakeAxios, 'patch');
		progressStore.set('foo', 'value', 1);
		expect(patchStub).to.have.been.calledWith(fakeGetApiUrl(`users/1/state`), {value: 'value'});
	});

	it('remove calls store remove method', function () {
		const removeStub = stub(fakeStore, 'remove');
		progressStore.remove('foo');
		expect(removeStub).to.have.been.called;
	});
});
