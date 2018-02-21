<template>
	<router-link
		:to="{ name: 'messages', query: {roomId: roomIdParam} }"
		v-if="roomIdParam"
		@click="$emit('navigate')"
	>
		<slot></slot>
	</router-link>
	<a @click="createNewRoomAndRedirect" v-else><slot></slot></a>
</template>

<script>
	import {mapActions, mapGetters} from 'vuex'
	import {getApiUrl} from 'js/utils/env'

	export default {
		name: 'MessageLink',
		props: {
			userId: {
				required: true,
				type: Number,
			},
			roomId: {
				required: false,
				default: 0
			}
		},
		computed: {
			...mapGetters('chatMessages', ['getRoomForPrivateChat']),
			...mapGetters(['currentUserId']),
			roomIdParam() {
				if (this.roomId) {
					return this.roomId
				} else {
					const room = this.getRoomForPrivateChat(this.userId)
					if (room.id) {
						return room.id
					}
				}
				return 0
			}
		},
		methods: {
			...mapActions('chatMessages', ['createNewRoom']),
			async createNewRoomAndRedirect() {
				const payload = {
					users: [this.currentUserId, this.userId]
				}
				const room = await this.createNewRoom(payload)
				this.$router.push({
					name: 'messages',
					query: {roomId: room.id}
				})
				this.$emit('navigate')
				return room
			},
			async navigate() {
				if (this.roomId) {
					this.$emit('navigate')
					return this.$router.push({name: 'messages', query: {roomId: this.roomIdParam}})
				} else {
					const room = this.getRoomForPrivateChat(this.userId)
					if (room.id) {
						this.$emit('navigate')
						return this.$router.push({name: 'messages', query: {roomId: room.id}})
					}
					await this.createNewRoomAndRedirect()
					this.$emit('navigate')
				}
			}
		}
	}
</script>
