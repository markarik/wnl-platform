<template>
	<article class="media">
		<figure class="media-left">
			<p class="image is-64x64">
				<img src="http://bulma.io/images/placeholders/128x128.png">
			</p>
		</figure>
		<div class="media-content">
			<p class="control">
				<textarea :id="inputId" v-model="message" class="textarea"
					:disabled="disabled"
					@keydown.enter="suppressEnter"
					@keyup.enter="sendMessage">
				</textarea>
			</p>
			<div class="message is-warning" v-if="error.length > 0">
				<div class="message-body">{{ error }}</div>
			</div>
			<nav class="level">
				<div class="level-left">
					<div class="level-item">
						<a class="button is-info" @click="sendMessage">Post comment</a>
					</div>
				</div>
			</nav>
		</div>
	</article>
</template>
<script>
	import { mapGetters } from 'vuex'

	export default{
		props: ['loaded', 'socket', 'room', 'inputId'],
		data(){
			return {
				disabled: false,
				error: '',
				message: ''
			}
		},
		computed: {
			...mapGetters([
				'current'
			])
		},
		methods: {
			sendMessage(event) {
				this.disabled = true
				this.error = ''
				this.socket.emit('send-message', {
					room: this.room,
					message: {
						username: this.current.full_name,
						content: this.message,
						timeago: 'niedawno'
					}
				})
			},
			suppressEnter(event) {
				event.preventDefault()
			},
			setListeners() {
				this.socket.on('message-processed', (data) => {
					this.disabled = false
					if (data.sent) {
						this.message = ''
					} else {
						this.error = 'Nie udało się wysłać wiadomości... Proszę, spróbuj jeszcze raz. :)'
					}
				})
			}
		},
		watch: {
			'loaded' () {
				if (this.loaded) {
					this.setListeners()
				}
			}
		}
	}

</script>
