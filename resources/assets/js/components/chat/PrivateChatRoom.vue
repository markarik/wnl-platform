<template>
	<div class="wnl-chat">
        Bye
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
		flex: 1 1 0
		flex-direction: column-reverse
		overflow-y: auto

	.wnl-chat-content
		position: relative

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
	import highlight from 'js/mixins/highlight'

	import { mapGetters } from 'vuex'

	export default {
		props: {
			messages:{
                type: Array,
                default: () => ([])
            },
            users: {
                type: Object,
                default: () => ({})
            }
		},
		data() {
			return {
				loaded: false,
				thereIsMore: true,
			}
		},
		computed: {
			...mapGetters(['isOverlayVisible']),
			...mapGetters('chatMessages', ['getProfileByUserId']),
			isAuthorUnique() {
				return this.messages.map((message, index) => {
					if (index === 0) return true

					let previous     = index - 1,
						halfHourInMs = 1000 * 60 * 30

					return message.user_id !== this.messages[previous].user_id ||
							message.time - this.messages[previous].time > halfHourInMs
				})
            },
            getMessageAuthor(message) {
                return this.getProfileByUserId(message.user_id)
            },
		},
		methods: {
		},
		mounted() {

		},
		beforeDestroy() {

		},
		components: {
			'wnl-message': Message,
			'wnl-message-form': MessageForm,
			'wnl-users-widget': UsersWidget,
		},
		watch: {
		}
	}

</script>
