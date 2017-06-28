<template lang="html">
	<div class="wnl-newsfeed column is-half-desktop">
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
	.wnl-newsfeed
		display: flex
</style>

<script>
	import _ from 'lodash'
	import {mapGetters} from 'vuex'
	import Event from 'js/components/newsfeed/Event'
	import AdminEvent from 'js/admin/components/newsfeed/Event'

	export default {
		name: 'wnl-newsfeed',
		props: ['type'],
		computed: {
			...mapGetters('notifications', ['notifications', 'isLoading']),
			eventComponent() {
				return `wnl-newsfeed-event-${this.type}`
			},
			empty() {
				return !this.isLoading && _.size(this.notifications) === 0
			}
		},
		components: {
			'wnl-newsfeed-event-user': Event,
			'wnl-newsfeed-event-admin': AdminEvent,
		},
		mounted() {

		}
	}
</script>
