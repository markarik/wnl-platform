<template>
	<div>
		<div class="screens-list-save">
			<a @click="saveOrder" class="button is-small" :class="{'is-loading': loading}">
				<span class="margin right">Zapisz kolejność</span>
				<span class="icon is-small">
					<i class="fa fa-save"></i>
				</span>
			</a>
		</div>
		<wnl-screens-list-item v-for="(screen, index) in screens"
			:key="screen.id"
			:index="index"
			:screen="screen"
			:isFirst="index === 0"
			:isLast="index === screens.length - 1"
			@moveScreen="moveScreen"
			@deleteScreen="deleteScreen">
		</wnl-screens-list-item>
		<div class="screens-list-add">
			<a class="button is-small" :class="{'is-loading': loading}" @click="addScreen">
				<span class="margin right">Dodaj ekran</span>
				<span class="icon is-small">
					<i class="fa fa-plus"></i>
				</span>
			</a>
		</div>
		<div class="margin top">
			<wnl-alert v-for="(alert, key) in alerts"
				:cssClass="alert.cssClass"
				:timestamp="key"
				:key="key"
				@delete="onDelete">
				{{alert.message}}
			</wnl-alert>
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
	import { alerts } from 'js/mixins/alerts'

	import ScreensListItem from 'js/admin/components/lessons/edit/ScreensListItem.vue'

	export default {
		name: 'ScreensList',
		components: {
			'wnl-screens-list-item': ScreensListItem
		},
		mixins: [ alerts ],
		data() {
			return {
				loading: false,
				screens: [],
			}
		},
		computed: {
			lessonId() {
				return this.$route.params.lessonId
			},
			screensListApiUrl() {
				return getApiUrl(`screens/search?q=lesson_id:${this.$route.params.lessonId}&order=order_number`)
			}
		},
		methods: {
			fetchScreens() {
				return axios.get(this.screensListApiUrl)
					.then((response) => {
						this.screens = response.data
					})
			},
			moveScreen(payload) {
				this.screens.splice(payload.to, 0, this.screens.splice(payload.from, 1)[0]);
			},
			saveOrder() {
				this.loading = true

				let promises = []
				_.forEach(this.screens, (screen, index) => {
					promises.push(
						axios.patch(getApiUrl(`screens/${screen.id}`), {
							order_number: index
						})
					)
				})

				Promise.all(promises)
					.then(() => {
						this.loading = false
						this.alertSuccessFading('Kolejność zachowana!', 2000)
					})
					.catch(() => {
						$wnl.logger.error(error)
						this.alertErrorFading('Nie wyszło, sorry :())', 2000)
					})
			},
			addScreen() {
				let defaultData = {
					lesson_id: this.lessonId,
					name: 'Nowy ekran',
					content: '',
					order_number: 100,
					type: 'html',
					meta: '{}',
				}

				this.loading = true

				axios.post(getApiUrl('screens'), defaultData)
					.then(() => {
						return this.fetchScreens()
					})
					.then(() => {
						this.loading = false
						this.alertSuccessFading('Ekran dodany!', 2000)
					})
					.catch((error) => {
						$wnl.logger.error(error)
						this.alertErrorFading('Nie wyszło, sorry. :()', 2000)
					})
			},
			deleteScreen(id) {
				this.loading = true

				axios.delete(getApiUrl(`screens/${id}`))
					.then(() => {
						return this.fetchScreens()
					})
					.then(() => {
						this.loading = false
						this.alertSuccessFading('Ekran usunięty!', 2000)
					})
					.catch((error) => {
						this.loading = false
						$wnl.logger.error(error)
						this.alertErrorFading('Nie wyszło, sorry. :()', 2000)
					})
			},
		},
		mounted() {
			this.fetchScreens()
		}
	}
</script>
