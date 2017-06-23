<template lang="html">
	<div class="wnl-newsfeed-notification">
		{{ notification.event }}
	</div>
</template>

<style lang="sass">
</style>

<script>
	export default {
		name: 'wnl-newsfeed-notification',
		props: ['notification'],
		components: {}
	}
</script>
