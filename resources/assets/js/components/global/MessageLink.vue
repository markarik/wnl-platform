<template>
	<router-link
			:to="{ name: 'messages', query: {roomId: roomIdParam} }"
			v-if="roomIdParam"
	>
		<slot></slot>
	</router-link>
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
			...mapActions('chatMessages', ['createNewRoom'])
		},
		mounted() {
			if (this.roomId) {
				this.roomIdParam = this.roomId
			} else {
				const room = this.getRoomForPrivateChat(this.userId)
				if (room.id) {
					this.roomIdParam = room.id
				} else {
					const payload = {
						users: [this.currentUserId, this.userId]
					}
					this.createNewRoom(payload).then((data) => {
						this.roomIdParam = data.id
					})
				}
			}
		}
	}
</script>
