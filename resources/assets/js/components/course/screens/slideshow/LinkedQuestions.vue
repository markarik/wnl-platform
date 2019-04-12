<template>
	<div>
		{{linkedQuestions}}
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

<script>
import axios from 'axios';
import {getApiUrl} from 'js/utils/env';
import { debounce, isEmpty } from 'lodash';

const defaultMessage = 'Szukam powiązanych pytań...';

export default {
	name: 'LinkedQuestions',
	props: {
		slideId: {
			type: Number,
		},
	},
	data() {
		return {
			linkedQuestions: defaultMessage,
		};
	},
	computed: {
		slideApiUrl() {
			return 'slides/' + this.slideId + '?include=quiz_questions';
		},
	},
	methods: {
		debouncedGetLinkedQuestions: debounce(function () {
			this.setLinkedQuestions();
		}, 300, {leading: false, trailing: true}),
		getLinkedQuestions() {
			this.linkedQuestions = defaultMessage;
			return axios.get(getApiUrl(this.slideApiUrl))
				.then((response) => {
					let included = response.data.included,
						questions;

					if (isEmpty(included) || isEmpty(included.quiz_questions)) {
						return 'Brak powiązanych pytań';
					}
					questions = Object.keys(included.quiz_questions).join(', ');

					return 'Powiązane pytania: ' + questions;
				});
		},
		setLinkedQuestions() {
			if (this.slideId == 0) return;

			this.getLinkedQuestions()
				.then((response) => {
					this.linkedQuestions = response;
				})
				.catch(() => {
					this.linkedQuestions = 'Nie powiodło się...';
				});
		},
	},
	mounted() {
		this.setLinkedQuestions();
	},
	watch: {
		'slideId' () {
			this.debouncedGetLinkedQuestions();
		}
	}
};
</script>
