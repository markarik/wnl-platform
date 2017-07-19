<template lang="html">
	<div class="wnl-feed column is-full">
		<div v-if="isLoading">
			<wnl-text-loader></wnl-text-loader>
		</div>
		<div class="notification aligncenter" v-if="empty">
			Nie ma nowych powiadomień
		</div>
		<div v-else class="container">
			<component
					v-for="(event, index) in notifications"
					:is="eventComponent"
					:event="event"
					:key="index">
			</component>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	.wnl-feed
		display: flex
</style>

<script>
	import _ from 'lodash'
	import {mapGetters} from 'vuex'
	import Event from 'js/components/newsfeed/Event'
	import ModeratorEvent from 'js/components/moderators/FeedEvent'

	export default {
		name: 'wnl-feed',
		props: ['type', 'notifications'],
		computed: {
			...mapGetters('notifications', ['isLoading']),
			eventComponent() {
				return `wnl-newsfeed-event-${this.type}`
			},
			empty() {
				return !this.isLoading && _.size(this.notifications) === 0
			}
		},
		components: {
			'wnl-newsfeed-event-user': Event,
			'wnl-newsfeed-event-moderator': ModeratorEvent,
		},
		mounted() {

		}
	}
</script>
