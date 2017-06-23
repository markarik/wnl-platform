<template lang="html">
	<div class="wnl-newsfeed">
		<div v-if="isLoading">
			<wnl-text-loader></wnl-text-loader>
		</div>
		<div v-else class="container">
			<wnl-newsfeed-post
					v-for="(notification, index) in notifications"
					:notification="notification"
					:key="index">
			</wnl-newsfeed-post>
		</div>
	</div>
</template>

<style lang="sass">
</style>

<script>
	import {mapGetters} from 'vuex'
	import Post from 'js/components/newsfeed/Post'

	export default {
		name: 'wnl-newsfeed',
		computed: {
			...mapGetters('notifications', ['notifications', 'isLoading'])
		},
		components: {
			'wnl-newsfeed-post': Post,
		},
		mounted() {

		}
	}
</script>
