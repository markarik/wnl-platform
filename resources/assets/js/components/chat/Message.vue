<template>
	<article class="media">
		<figure class="media-left">
			<wnl-avatar :username="username"></wnl-avatar>
		</figure>
		<div class="media-content">
			<div class="content">
				<p class="wnl-message-meta">
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
	.media-content
		.content
			.wnl-message-meta
				line-height: 1em
				margin-bottom: 0.2em
</style>
<script>
	export default{
		props: ['username', 'time'],
		computed: {
			formattedTime () {
				let date = new Date(this.time);
				return date.toLocaleTimeString();
			}
		}
	}

</script>
