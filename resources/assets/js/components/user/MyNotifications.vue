<template lang="html">
	<div class="scrollable-main-container">
		<h1>Notyfikacje</h1>
		<wnl-private-feed />	
	</div>
</template>

<script>
	import PrivateFeed from 'js/components/notifications/feeds/stream/PrivateFeed'

	export default {
		components: {
			'wnl-private-feed': PrivateFeed
		},
	}
</script>
