<template>
	<span
		class="icon text-dimmed"
		:class="[sizeClass]"
		:title="computedTitle"
		@click="checkSanity"
	>
		<wnl-alert
			v-for="(alert, timestamp) in alerts"
			:key="timestamp"
			css-class="fixed"
			:alert="alert"
			:timestamp="timestamp"
			@delete="onDelete"
		></wnl-alert>
		<i class="fa fa-trash"></i>
	</span>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.icon
		cursor: pointer

		&:hover
			color: $color-red
</style>

<script>
import axios from 'axios';

import Alert from 'js/components/global/Alert';

import { alerts } from 'js/mixins/alerts';
import { swalConfig } from 'js/utils/swal';
import { getApiUrl } from 'js/utils/env';

export default {
	name: 'Delete',
	components: {
		'wnl-alert': Alert,
	},
	mixins: [ alerts ],
	props: ['target', 'requestRoute', 'title'],
	computed: {
		sizeClass() {
			return 'is-small';
		},
		computedTitle() {
			return this.title || 'Usuń';
		},
		targetText() {
			return `Chcesz usunąć ${this.target}?`;
		},
	},
	methods: {
		checkSanity() {
			this.$swal(swalConfig({
				showCancelButton: true,
				cancelButtonColor: '#696969',
				cancelButtonText: 'Nie, jednak nie',
				confirmButtonClass: 'button is-danger',
				confirmButtonColor: '#e53d2c',
				confirmButtonText: 'Tak, usuwam',
				text: this.targetText,
				title: 'Na pewno?',
				type: 'question',
			})).then(() => {
				this.sendRequest();
			}, () => false);
		},
		sendRequest() {
			axios.delete(getApiUrl(this.requestRoute))
				.then(() => {
					this.successFading('Usunięto pomyślnie!');
					this.$emit('deleteSuccess');
				})
				.catch((error) => {
					$wnl.logger.error(error);
					this.errorFading('Coś poszło nie tak...');
					this.$emit('deleteError');
				});
		},
	}
};
</script>
