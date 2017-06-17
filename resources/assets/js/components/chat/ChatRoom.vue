<template>
	<div class="wnl-chat">
		<div class="wnl-chat-messages" @scroll="onScroll">
			<div class="wnl-chat-content">
				<div class="wnl-chat-content-inside" v-if="loaded">
					<div class="notification aligncenter" v-if="!thereIsMore">
						To początek dyskusji na tym kanale!
					</div>
					<wnl-text-loader v-if="isPulling">
						Ładuję wiadomości...
					</wnl-text-loader>
					<div v-if="messages.length > 0">
						<wnl-message v-for="(message, index) in messages"
									 :key="index"
									 :showAuthor="isAuthorUnique[index]"
									 :full_name="message.full_name"
									 :avatar="message.avatar"
									 :time="message.time">
							{{ message.content }}
						</wnl-message>
					</div>
					<div class="metadata aligncenter margin vertical" v-else>
						Napisz pierwszą wiadomość i zacznij rozmowę!
						<p class="margin vertical">
							<span class="icon is-big text-dimmed">
								<i class="fa fa-comments-o"></i>
							</span>
						</p>
					</div>
				</div>
				<wnl-text-loader v-else>Ładuję wiadomości...</wnl-text-loader>
			</div>
		</div>
		<div class="wnl-chat-form">
			<wnl-message-form :loaded="loaded" :socket="socket" :room="room" :inputId="inputId"></wnl-message-form>
		</div>
	</div>
</template>
<style lang="sass" rel="stylesheet/sass">
	@import '../../../sass/variables'

	.wnl-chat
		display: flex
		flex: 1
		flex-direction: column
		justify-content: space-between

	.wnl-chat-messages
		display: flex
		flex: 1 1 auto
		flex-direction: column-reverse
		overflow-y: auto

	.wnl-chat-form
		border-top: $border-light-gray
		margin: $margin-base 0 0
		padding-top: $margin-base
</style>
<script>
	import Message from './Message.vue'
	import MessageForm from './MessageForm.vue'
	import UsersWidget from '../global/UsersWidget.vue'
	import {getApiUrl} from 'js/utils/env'
	import * as socket from '../../socket'
	import {nextTick} from 'vue'
	import _ from 'lodash'

	export default {
		props: ['room'],
		data() {
			return {
				loaded: false,
				messages: [],
				users: [],
				socket: {},
				isPulling: false,
				thereIsMore: true,
			}
		},
		computed: {
			isAuthorUnique() {
				return this.messages.map((message, index) => {
					if (index === 0) return true

					let previous     = index - 1,
						halfHourInMs = 1000 * 60 * 30
					return message.full_name !== this.messages[previous].full_name ||
							message.time - this.messages[previous].time > halfHourInMs
				})
			},
			container() {
				return this.$el.getElementsByClassName('wnl-chat-messages')[0]
			},
			content() {
				return this.$el.getElementsByClassName('wnl-chat-content')[0]
			},
			contentInside() {
				// In case you wonder - thank Firefox :/
				return this.$el.getElementsByClassName('wnl-chat-content-inside')[0]
			},
			inputId() {
				return `wnl-chat-form-${this.room}`
			},
			input() {
				return document.getElementById(this.inputId)
			}
		},
		methods: {
			joinRoom() {
				this.socket.emit('join-room', {
					room: this.room
				})
				this.socket.on('join-room-success', (data) => {
					if (!this.loaded) {
						this.messages = data.messages
						this.users    = data.users
						this.loaded   = true
						nextTick(() => {
							this.scrollToBottom()
						})
					}
				})
				return true
			},
			changeRoom(oldRoom) {
				this.loaded = false
				this.socket.emit('leave-room', {
					room: oldRoom
				})
				this.joinRoom()
			},
			setListeners(socket) {
				socket.on('user-sent-message', (data) => {
					this.messages.push(data.message)
				})

				socket.on('message-processed', (data) => {
					if (data.sent) {
						this.addMessage(data.message)
					}
				})

				socket.on('error', (data) => {
					$wnl.logger.error(`Socket error: ${data}`)
				})
			},
			addMessage(message) {
				this.messages.push(message)
				nextTick(() => {
					this.scrollToBottom()
					this.input.focus()
				})
			},
			scrollToBottom() {
				this.container.scrollTop = '1000000000'
			},
			pullDebouncer(event) {
				let target     = event.target,
					height     = target.scrollHeight,
					shouldPull =
							// We're always getting first 200 messages from hot storage,
							this.messages.length >= 200 &&
							// make sure we're not pulling from cold storage at the moment,
							!this.isPulling &&
							// we're reaching the top of the messages container,
							(target.scrollTop / height) < 0.1 &&
							// cold storage has some more messages we can pull.
							this.thereIsMore

				if (shouldPull) this.pull(target, height)
			},
			onScroll (target) {
				this.pullDebouncer.call(this, event)
			},
			pull (target, originalHeight){
				this.isPulling = true
				let data       = {
					query: {
						where: [
							['time', '<', this.messages[0].time],
						],
					},
					order: {
						time: 'desc',
					},
					include: 'profiles',
					limit: [100, 0]
				}

				axios.post(getApiUrl(`chat_rooms/${this.room}/chat_messages/.search`), data)
						.then(response => {
							let data = response.data

							if (typeof data[0] !== 'object') {
								this.isPulling   = false
								this.thereIsMore = false
								return
							}

							let profiles = data.included.profiles

							_.each(data, (element, key) => {
								if (key !== 'included') {
									let user    = element.profiles[0],
										message = _.assign(element, profiles[user])
									this.messages.unshift(message);
								}
							})
							setTimeout(()=> {
								this.container.scrollTop = this.container.scrollHeight - originalHeight
							}, 0)
							this.isPulling = false
						})
						.catch(error => {
							$wnl.logger.capture(error)
						})
			}
		},
		created() {
			socket.connect().then((socket) => {
				this.socket = socket
				this.joinRoom()
				this.setListeners(this.socket)
			}).catch(exception => $wnl.logger.capture(exception))

			this.pullDebouncer = _.debounce(this.pullDebouncer, 50)
		},
		beforeDestroy() {
			socket.disconnect().then(() => {
				return true
			}).catch(exception => $wnl.logger.capture(exception))
		},
		components: {
			'wnl-message': Message,
			'wnl-message-form': MessageForm,
			'wnl-users-widget': UsersWidget,
		},
		watch: {
			'room' (newRoom, oldRoom) {
				if (newRoom !== oldRoom) {
					this.changeRoom(oldRoom)
				}
			}
		}
	}

</script>
