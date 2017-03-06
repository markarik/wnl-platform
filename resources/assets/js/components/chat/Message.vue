<template>
	<article class="media wnl-chat-message" :class="{ 'is-full': showAuthor }">
		<figure class="media-left">
			<wnl-avatar :username="username" v-if="showAuthor"></wnl-avatar>
			<div class="media-left-placeholder" v-else></div>
		</figure>
		<div class="media-content">
			<div class="content">
				<p class="wnl-message-meta" v-if="showAuthor">
					<strong>{{ username }}</strong>
					<small>{{ formattedTime }}</small>
				</p>
				<p class="wnl-message-content">
					<slot></slot>
				</p>
			</div>
		</div>
	</article>
</template>
<style lang="sass">
	@import 'resources/assets/sass/variables'

	.media.wnl-chat-message
		border-top: 0
		padding-top: 0
		margin-top: 0.5rem

		&.is-full
			margin-top: 1.25rem

	.media-left-placeholder
		height: 1px
		width: map-get($rounded-square-sizes, 'medium')

	.media-content
		.content
			.wnl-message-meta
				line-height: 1em
				margin-bottom: 0.2em
</style>
<script>
	export default{
		props: ['username', 'time', 'showAuthor'],
		computed: {
			formattedTime () {
				let date = new Date(this.time);
				return date.toLocaleTimeString();
			}
		}
	}

</script>
