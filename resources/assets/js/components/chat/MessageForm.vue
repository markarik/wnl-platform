<template>
	<article class="media">
		<figure class="media-left">
			<p class="image is-64x64">
				<img src="http://bulma.io/images/placeholders/128x128.png">
			</p>
		</figure>
		<div class="media-content">
			<p class="control">
				<textarea :text="message" class="textarea" @keyup.enter="sendMessage"></textarea>
			</p>
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
	export default{
		data(){
			return {
				message: ''
			}
		},

		methods: {
			sendMessage(){
				console.log('asdf')
//				this.$socket.emit('send-message', 'dupa')
				this.$store.dispatch('sendMessage', this.message)
//				this.message = ''
			}
		}
	}

</script>
