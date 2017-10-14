<template>
	<wnl-stream-feed :channel="channel" />	
</template>

<script>
	import _ from 'lodash'
	import { mapActions, mapGetters } from 'vuex'

	import StreamFeed from 'js/components/notifications/feeds/stream/StreamFeed'


	export default {
		name: 'PublicFeed',
		components: {
			'wnl-stream-feed': StreamFeed
		},
		computed: {
			...mapGetters('notifications', {
				channel: 'streamChannel',
			}),
		},
	}
</script>
