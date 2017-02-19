<template>
	<div>
		<div>Breadcrumbs</div>
		<div>
			<wnl-message v-for="message in messages" :username="message.username" :timeago="message.timeago">
				{{ message.content }}
			</wnl-message>
		</div>
		<div>
			<wnl-message-form>
			</wnl-message-form>
		</div>
	</div>
</template>
<style>


</style>
<script>
	import Message from './Message.vue'
	import MessageForm from './MessageForm.vue'
	export default{
		data(){
			return {
				msg: 'hello vue',
				messages: [
					{
						username: 'Stevie Ray Vaughan',
						timeago: '26 yrs',
						content: 'Hey guys, wanna buy some used fender strat?'
					},
					{
						username: 'Buddy Guy',
						timeago: '10 min',
						content: 'nope'
					},
					{
						username: 'Muddy Waters',
						timeago: 'a few seconds ago',
						content: 'fuck off stevie'
					},
					{
						username: 'Albert King',
						timeago: 'now',
						content: 'how much?'
					},
				]
			}
		},

		components: {
			'wnl-message': Message,
			'wnl-message-form': MessageForm
		},
		methods: {
			chatJoinRoom() {
//				this.$store.dispatch('chatJoinRoom', '1')
			}
		},

		created () {

//			this.chatJoinRoom()
		}
	}

</script>
