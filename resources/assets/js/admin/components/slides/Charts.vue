<template>
	<div>
		<wnl-alert v-for="(alert, timestamp) in alerts"
			:alert="alert"
			css-class="fixed"
			:key="timestamp"
			:timestamp="timestamp"
			@delete="onDelete"
		></wnl-alert>

		<a class="button is-primary" @click="updateCharts">Aktualizuj wszystkie diagramy</a>
	</div>
</template>

<style lang="sass">
</style>

<script>
import axios from 'axios';
import { alerts } from 'js/mixins/alerts';
import { getUrl } from 'js/utils/env';

export default {
	mixins: [ alerts ],
	methods: {
		updateCharts() {
			axios.get(getUrl('admin/update-charts'))
				.then(() => {
					this.successFading('OK, to może trochę potrwać. Kiedy skończę, dam znać na slacku.', 4000);
				})
				.catch(error => {
					this.errorFading('Ups... Coś poszło nie tak.', 4000);
					$wnl.logger.capture(error);
				});
		}
	}
};
</script>
