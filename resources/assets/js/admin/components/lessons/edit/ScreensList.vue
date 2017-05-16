<template>
	<div>
		<div class="screens-list-save">
			<a @onClick="" class="button is-small" :disabled="!hasChanged">
				<span class="margin right">Zapisz kolejność</span>
				<span class="icon is-small">
					<i class="fa fa-save"></i>
				</span>
			</a>
		</div>
		<wnl-screens-list-item v-for="screen in screens"
			:name="screen.name"
			:id="screen.id">
		</wnl-screens-list-item>
		<div class="screens-list-add">
			<a class="button is-small">
				<span class="margin right">Dodaj ekran</span>
				<span class="icon is-small">
					<i class="fa fa-plus"></i>
				</span>
			</a>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.screens-list-add
		margin-top: $margin-big
		text-align: center

	.screens-list-save
		margin-bottom: $margin-big
		text-align: center
</style>

<script>
	import _ from 'lodash'

	import { getApiUrl } from 'js/utils/env'

	import ScreensListItem from 'js/admin/components/lessons/edit/ScreensListItem.vue'

	export default {
		name: 'ScreensList',
		components: {
			'wnl-screens-list-item': ScreensListItem
		},
		data() {
			return {
				originalScreens: [],
				screens: [],
			}
		},
		computed: {
			hasChanged() {
				return !_.isEqual(this.originalScreens, this.screens)
			},
			screensListApiUrl() {
				return getApiUrl(`screens/search?q=lesson_id:${this.$route.params.lessonId}`)
			},
		},
		methods: {
			fetchScreens() {
				return axios.get(this.screensListApiUrl)
					.then((response) => {
						this.screens = response.data
						this.originalScreens = response.data
					})
			},
		},
		mounted() {
			this.fetchScreens()
		}
	}
</script>
