<template lang="html">
	<div class="wnl-newsfeed column is-half-desktop">
		<div v-if="isLoading">
			<wnl-text-loader></wnl-text-loader>
		</div>
		<div v-else class="container">
			<a>Oznacz wszystkie jako przeczyane</a>
			<wnl-newsfeed-event
					v-for="(event, index) in notifications"
					:event="event"
					:key="index">
			</wnl-newsfeed-event>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	.wnl-newsfeed
		display: flex
</style>

<script>
	import {mapGetters} from 'vuex'
	import Event from 'js/components/newsfeed/Event'

	export default {
		name: 'wnl-newsfeed',
		computed: {
			...mapGetters('notifications', ['notifications', 'isLoading'])
		},
		components: {
			'wnl-newsfeed-event': Event,
		},
		mounted() {

		}
	}
</script>
