<template>
	<router-link
		:to="{ name: 'messages', query: {roomId: roomIdParam} }"
		v-if="roomIdParam"
		@click.native="$emit('close')"
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
		data() {
			return {
				roomIdParam: 0
			}
		},
		computed: {
			...mapGetters('chatMessages', ['getRoomForPrivateChat']),
			...mapGetters(['currentUserId']),
		},
		methods: {
			...mapActions('chatMessages', ['createNewRoom']),
			async createNewRoomAndRedirect() {
				this.$emit('close')
				const payload = {
					users: [this.currentUserId, this.userId]
				}
				const room = await this.createNewRoom(payload)
				this.$router.push({
					name: 'messages',
					query: {roomId: room.id}
				})
			}
		},
		created() {
			if (this.roomId) {
				this.roomIdParam = this.roomId
			} else {
				const room = this.getRoomForPrivateChat(this.userId)
				if (room.id) {
					this.roomIdParam = room.id
				}
			}
		}
	}
</script>
