<template>
	<div class="lesson-editor">
		<form @submit.prevent="lessonFormSubmit">
			<div class="field is-grouped">
				<wnl-input
					:form="form"
					name="name"
					v-model="form.name"
				></wnl-input>
				<wnl-form-checkbox
					class="checkbox button"
					:form="form"
					name="is_required"
					v-model="form.is_required"
				>Lekcja obowiązkowa</wnl-form-checkbox>
				<div class="control">
					<a
						class="button is-small is-success"
						:class="{'is-loading': loading}"
						:disabled="!hasChanged"
						@click="lessonFormSubmit"
					>
						<span class="margin right">Zapisz</span>
						<span class="icon is-small">
							<i class="fa fa-save"></i>
						</span>
					</a>
				</div>
			</div>
		</form>
		<wnl-screens-editor v-if="isEdit" :lesson-id="lessonId"></wnl-screens-editor>
		<p v-else>Zapisz lekcję, żeby dodać do niej ekran</p>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.checkbox
		margin-right: 10px
		display: flex
		justify-content: space-between

</style>

<script>
import _ from 'lodash';
import { mapActions } from 'vuex';

import Form from 'js/classes/forms/Form';
import { getApiUrl } from 'js/utils/env';

import ScreensEditor from 'js/admin/components/lessons/edit/ScreensEditor.vue';
import Input from 'js/admin/components/forms/Input.vue';
import Checkbox from 'js/admin/components/forms/Checkbox.vue';
import { ALERT_TYPES } from 'js/consts/alert';

export default {
	name: 'LessonEditor',
	props: ['lessonId'],
	components: {
		'wnl-screens-editor': ScreensEditor,
		'wnl-input': Input,
		'wnl-form-checkbox': Checkbox,
	},
	data() {
		return {
			form: new Form({
				name: null,
				is_required: false,
			}),
			loading: false,
		};
	},
	computed: {
		isEdit() {
			return this.lessonId !== 'new';
		},
		method() {
			return this.isEdit ? 'put' : 'post';
		},
		resourceUrl() {
			return this.isEdit ? getApiUrl(`lessons/${this.lessonId}`) : getApiUrl('lessons');
		},
		hasChanged() {
			return !_.isEqual(this.form.originalData, this.form.data());
		}
	},
	methods: {
		...mapActions([
			'addAutoDismissableAlert',
		]),
		lessonFormSubmit() {
			if (!this.hasChanged) {
				return false;
			}

			this.loading = true;
			this.form[this.method](this.resourceUrl)
				.then(response => {
					this.loading = false;
					this.addAutoDismissableAlert({
						text: 'Lekcja zapisana!',
						type: ALERT_TYPES.SUCCESS,
					});
					this.form.originalData = this.form.data();

					if (!this.isEdit) {
						this.$router.push({ name: 'lessons', params: { lessonId: response.id } });
					}
				})
				.catch(exception => {
					this.loading = false;
					this.addAutoDismissableAlert({
						text: 'Nie udało się :(',
						type: ALERT_TYPES.ERROR,
					});
					$wnl.logger.capture(exception);
				});
		}
	},
	mounted() {
		if (this.isEdit) {
			this.form.populate(this.resourceUrl);
		}
	}
};
</script>
