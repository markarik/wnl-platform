<template>
	<div>
		<label class="label">Wybierz zestaw pytań</label>
		<span class="select">
			<wnl-select
				v-model="selectedQuiz"
				:options="quiz_sets"
				name="meta"
			/>
		</span>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
</style>

<script>
import axios from 'axios';
import { forEach } from 'lodash';

import WnlSelect from 'js/admin/components/forms/Select';
import { getApiUrl } from 'js/utils/env';

export default {
	name: 'ScreensMetaEditorQuizes',
	components: {
		WnlSelect
	},
	props: ['value'],
	data: function() {
		return {
			quiz_sets: [],
		};
	},
	computed: {
		selectedQuiz: {
			get: function () {
				return this.value;
			},
			set: function (value) {
				this.$emit('input', value);
			}
		},
	},
	mounted() {
		this.fetchQuizSets();
	},
	methods: {
		formScreenMeta(resource, id) {
			return {
				resources: [
					{
						id: id,
						name: resource
					}
				]
			};
		},
		fetchQuizSets() {
			return axios.get(getApiUrl('quiz_sets/all'))
				.then((response) => {
					forEach(response.data, (quiz) => {
						this.quiz_sets.push({
							text: quiz.name,
							value: this.formScreenMeta('quiz_sets', quiz.id),
						});
					});
				});
		},
	},
};
</script>
