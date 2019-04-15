<template>
	<div>
		<div class="screens-list-save">
			<a class="button is-small" :class="{'is-loading': loading}"
				:disabled="!changed"
				@click="saveOrder">
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
			:is-first="index === 0"
			:is-last="index === screens.length - 1"
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
			<wnl-alert v-for="(alert, timestamp) in alerts"
				:alert="alert"
				:timestamp="timestamp"
				:key="timestamp"
				@delete="onDelete">
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
import axios from 'axios';
import { forEach } from 'lodash';

import { getApiUrl } from 'js/utils/env';
import { alerts } from 'js/mixins/alerts';

import ScreensListItem from 'js/admin/components/lessons/edit/ScreensListItem.vue';

export default {
	name: 'ScreensList',
	props: ['lessonId'],
	components: {
		'wnl-screens-list-item': ScreensListItem
	},
	mixins: [ alerts ],
	data() {
		return {
			changed: false,
			loading: false,
			screens: [],
		};
	},
	methods: {
		fetchScreens() {
			return axios.get(getApiUrl(`lessons/${this.lessonId}/screens`))
				.then((response) => {
					this.screens = response.data;
				});
		},
		moveScreen(payload) {
			this.changed = true;
			this.screens.splice(payload.to, 0, this.screens.splice(payload.from, 1)[0]);
		},
		saveOrder() {
			if (!this.changed) {
				return false;
			}

			this.loading = true;

			let promises = [];
			forEach(this.screens, (screen, index) => {
				promises.push(
					axios.patch(getApiUrl(`screens/${screen.id}`), {
						order_number: index
					})
				);
			});

			Promise.all(promises)
				.then(() => {
					this.loading = false;
					this.changed = false;
					this.successFading('Kolejność zachowana!', 2000);
				})
				.catch((error) => {
					$wnl.logger.error(error);
					this.errorFading('Nie wyszło, sorry :())', 2000);
				});
		},
		addScreen() {
			let defaultData = {
				lesson_id: this.lessonId,
				name: 'Nowy ekran',
				content: '',
				order_number: 100,
				type: 'html',
				meta: '{}',
			};

			this.loading = true;

			axios.post(getApiUrl('screens'), defaultData)
				.then(() => {
					return this.fetchScreens();
				})
				.then(() => {
					this.loading = false;
					this.successFading('Ekran dodany!', 2000);
				})
				.catch((error) => {
					$wnl.logger.error(error);
					this.errorFading('Nie wyszło, sorry. :()', 2000);
					this.loading = false;
				});
		},
		deleteScreen(id) {
			this.loading = true;

			axios.delete(getApiUrl(`screens/${id}`))
				.then(() => {
					return this.fetchScreens();
				})
				.then(() => {
					this.loading = false;
					this.successFading('Ekran usunięty!', 2000);
				})
				.catch((error) => {
					this.loading = false;
					$wnl.logger.error(error);
					this.errorFading('Nie wyszło, sorry. :()', 2000);
				});
		},
	},
	mounted() {
		this.fetchScreens();
	}
};
</script>
