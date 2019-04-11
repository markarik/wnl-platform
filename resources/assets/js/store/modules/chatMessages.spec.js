import {describe, it} from 'mocha';
import chai from 'chai';
import chatMessagesModule from 'js/store/modules/chatMessages';
import * as types from 'js/store/mutations-types';
import {testAction} from 'js/tests/helpers';
import sinon from 'sinon';
import sinonChai from 'sinon-chai';

const expect = chai.expect;
const {actions} = chatMessagesModule;

chai.use(sinonChai);

describe('chatMessages module', () => {
	describe('actions', () => {
		describe('updateFromEventLog', () => {
			it('update from event log mark room as read', () => {
				const payload = [
					{
						name: 'markRoomAsRead',
						room: {
							id: 139,
						}
					}
				];
				const {actions} = chatMessagesModule;

				const dispatchSpy = sinon.spy();

				actions.updateFromEventLog({dispatch: dispatchSpy}, payload);
				expect(dispatchSpy).to.have.been.calledWith('markRoomAsRead', payload[0].room.id);
			});

			it('update from event log mark room as read', () => {
				const payload = [
					{
						name: 'sendMessage',
						message: {},
					}
				];
				const {actions} = chatMessagesModule;

				const dispatchSpy = sinon.spy();

				actions.updateFromEventLog({dispatch: dispatchSpy}, payload);
				expect(dispatchSpy).to.have.been.calledWith('onNewMessage', payload[0]);
			});

			it('update from event log when more than one event', () => {
				const payload = [
					{
						name: 'sendMessage'
					},
					{
						name: 'markRoomAsRead',
						room: {
							id: 7
						}
					}
				];
				const dispatchSpy = sinon.spy();

				actions.updateFromEventLog({dispatch: dispatchSpy}, payload);
				expect(dispatchSpy).to.have.been.callCount(payload.length);
			});
		});
		describe('onNewMessage', done => {
			it('new message in public room', done => {
				const payload = {
					room: {
						type: 'public',
						id: 7
					},
					message: {
						content: 'hello'
					},
				};

				const context = {
					getters: {
						getRoomById: sinon.stub().returns({id: 7})
					}
				};
				// action, payload, state, expected mutations, done callback
				testAction(actions.onNewMessage, payload, context, [
					{type: types.CHAT_MESSAGES_ADD_PROFILES, payload: []},
					{type: types.CHAT_MESSAGES_ADD_MESSAGE, payload: {roomId: payload.room.id, message: payload.message}}
				], done);
			});

			it('new message in private room', done => {
				const payload = {
					room: {
						type: 'private',
						id: 7
					},
					message: {
						content: 'hello',
						user_id: 1
					}
				};

				const context = {
					getters: {
						getRoomById: sinon.stub().returns({id: 7})
					},
					rootGetters: {
						currentUserId: 7
					}
				};
				// action, payload, state, expected mutations, done callback
				testAction(actions.onNewMessage, payload, context, [
					{type: types.CHAT_MESSAGES_ADD_PROFILES, payload: []},
					{type: types.CHAT_MESSAGES_ADD_MESSAGE, payload: {roomId: payload.room.id, message: payload.message}},
					{type: types.CHAT_MESSAGES_CHANGE_ROOM_SORTING, payload: {roomId: payload.room.id, newIndex: 0}}
				], done);
			});

			it('new message in private room from different user', done => {
				const payload = {
					room: {
						type: 'private',
						id: 7
					},
					message: {
						content: 'hello',
						user_id: 7
					}
				};

				const context = {
					getters: {
						getRoomById: sinon.stub().returns({id: 7}),
					},
					rootGetters: {
						currentUserId: 8
					}
				};
				testAction(actions.onNewMessage, payload, context, [
					{type: types.CHAT_MESSAGES_ADD_PROFILES, payload: []},
					{type: types.CHAT_MESSAGES_ADD_MESSAGE, payload: {roomId: payload.room.id, message: payload.message}},
					{type: types.CHAT_MESSAGES_CHANGE_ROOM_SORTING, payload: {roomId: payload.room.id, newIndex: 0}},
					{type: types.CHAT_MESSAGES_ROOM_INCREMENT_UNREAD, payload: payload.room.id}
				], done);
			});
		});
	});
});
