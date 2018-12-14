<template>
	<div>
		<label class="label">Wybierz zestaw pytań</label>
		<span class="select">
			<wnl-select
					:options="quiz_sets"
					name="meta"
					v-model="selectedQuiz"
			></wnl-select>
		</span>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
</style>

<script>
import { forEach } from 'lodash';

import WnlSelect from 'js/admin/components/forms/Select';
import {getApiUrl} from 'js/utils/env';

export default {
	name: 'ScreensMetaEditorQuizes',
	props: ['value'],
	components: {
		WnlSelect
	},
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
	mounted() {
		this.fetchQuizSets();
	},
};
</script>
