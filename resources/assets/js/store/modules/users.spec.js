import {expect} from 'chai'
import {mutations, actions} from './users'
import * as types from '../mutations-types'
import {testAction} from '../../tests/helpers'

const getInitialState = () => {
	return {}
}

describe('activeUsers module', () => {
	describe('mutations', () => {
		it(types.ACTIVE_USERS_SET, () => {
			const state = getInitialState();

			mutations[types.ACTIVE_USERS_SET](state, {users: ['foo', 'bar'], channel: 'fizz'})

			expect(state).to.eql({
				fizz: ['foo', 'bar']
			})
		})
	})

	describe('actions', () => {
		it('user joined', done => {
			const payload = {user: 'John', channel: 'active'}
			// action, payload, state, expected mutations, done callback
			testAction(actions.userJoined, payload, getInitialState(), [
				{type: types.ACTIVE_USERS_SET, payload: {users: [payload.user], channel: payload.channel}}
			], done);
		})
	})
})
