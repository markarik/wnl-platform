import {expect} from 'chai'
import {mutations} from './activeUsers'
import * as types from '../mutations-types'

describe('mutations', () => {
	it(types.ACTIVE_USERS_SET, () => {
		const state = { activeUsers: {} };

		mutations[types.ACTIVE_USERS_SET](state, {users: ['foo', 'bar'], channel: 'fizz'})

		expect(state.activeUsers).to.eql({
			fizz: ['foo', 'bar']
		})
	})
})
