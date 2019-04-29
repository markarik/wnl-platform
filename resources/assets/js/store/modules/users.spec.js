import { expect } from 'chai';
import { mutations, actions } from 'js/store/modules/users';
import * as types from 'js/store/mutations-types';
import { testAction } from 'js/tests/helpers';

const getInitialState = () => {
	return {};
};

describe('users vuex module', () => {
	describe('mutations', () => {
		test(types.ACTIVE_USERS_SET, () => {
			const state = getInitialState();

			mutations[types.ACTIVE_USERS_SET](state, { users: ['foo', 'bar'], channel: 'fizz' });

			expect(state).to.eql({
				fizz: ['foo', 'bar']
			});
		});
	});

	describe('actions', () => {
		test('user joined', done => {
			const payload = { user: 'John', channel: 'active' };
			// action, payload, state, expected mutations, done callback
			testAction(actions.userJoined, payload, { state: getInitialState() }, [
				{ type: types.ACTIVE_USERS_SET, payload: { users: [payload.user], channel: payload.channel } }
			], done);
		});
	});
});
