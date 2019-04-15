<template>
	<router-link
		:to="{ name: 'messages', query: {roomId: roomIdParam} }"
		v-if="roomIdParam"
		@click="$emit('navigate')"
	>
		<slot></slot>
	</router-link>
	<a @click="createPrivateRoomAndRedirect" v-else><slot></slot></a>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';

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
				return this.roomId;
			} else {
				const room = this.getRoomForPrivateChat(this.userId);
				if (room.id) {
					return room.id;
				}
			}
			return 0;
		}
	},
	methods: {
		...mapActions('chatMessages', ['createPrivateRoom']),
		async createPrivateRoomAndRedirect() {
			this.$emit('beforeNavigate');
			const payload = {
				users: [this.currentUserId, this.userId]
			};
			const room = await this.createPrivateRoom(payload);
			this.$router.push({
				name: 'messages',
				query: { roomId: room.id }
			});
			this.$emit('navigate');
			return room;
		},
		// this is used by ConversationsSearch - sorry :(
		async navigate() {
			this.$emit('beforeNavigate');
			if (this.roomId) {
				this.$emit('navigate');
				return this.$router.push({ name: 'messages', query: { roomId: this.roomIdParam } });
			} else {
				const room = this.getRoomForPrivateChat(this.userId);
				if (room.id) {
					this.$emit('navigate');
					return this.$router.push({ name: 'messages', query: { roomId: room.id } });
				}
				await this.createPrivateRoomAndRedirect();
				this.$emit('navigate');
			}
		}
	}
};
</script>
