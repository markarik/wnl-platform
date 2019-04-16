<template>
	<div class="screens-editor">
		<div class="screens-list">
			<p class="title is-5">Ekrany</p>
			<wnl-screens-list
				ref="ScreensList"
				:lesson-id="lessonId"
				:screens="screens"
			></wnl-screens-list>
		</div>
		<div v-if="loaded" class="screen-editor">
			<form>
				<!-- Screen meta -->
				<div class="field is-grouped">
					<div class="control">
						<wnl-form-input
							v-model="screenForm.name"
							:form="screenForm"
							name="name"
						></wnl-form-input>
					</div>
					<div class="control">
						<a
							class="button is-success is-small"
							:class="{'is-loading': loading}"
							:disabled="!hasChanged"
							@click="onSubmit"
						>
							<span class="margin right">Zapisz</span>
							<span class="icon is-small">
								<i class="fa fa-save"></i>
							</span>
						</a>
					</div>
				</div>

				<!-- Screen type -->
				<div class="field is-grouped">
					<div class="control">
						<label class="label">Typ ekranu</label>
						<span class="select">
							<wnl-select
								v-model="screenForm.type"
								:form="screenForm"
								:options="typesOptions"
								name="type"
							>
							</wnl-select>
						</span>
					</div>
					<div v-if="currentType && currentType.metaEditorComponent" class="control">
						<component :is="currentType.metaEditorComponent" v-model="screenMeta" />
					</div>
				</div>

				<!-- Screen content -->
				<div class="screen-content-editor">
					<quill
						v-model="screenForm.content"
						:form="screenForm"
						name="content"
					>
					</quill>
				</div>
			</form>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.field.is-grouped
		align-items: center

	.screens-editor
		border-top: $border-light-gray
		display: flex
		margin-top: $margin-big
		padding-top: $margin-big

		.title.is-5
			border-bottom: $border-light-gray
			padding-bottom: $margin-base

	.screens-list
		border-right: $border-light-gray
		flex: 0 0 auto
		padding: $margin-base
		min-width: 250px

	.screen-editor
		flex: 8 auto
		padding: 0 $margin-base $margin-base

	.screen-content-editor
		margin-top: $margin-big

		.ql-container
			max-height: 50vh
</style>

<script>
import axios from 'axios';
import { isEqual, isObject } from 'lodash';
import { mapActions } from 'vuex';

import ScreensList from 'js/admin/components/lessons/edit/ScreensList.vue';
import Form from 'js/classes/forms/Form';
import Input from 'js/admin/components/forms/Input.vue';
import Quill from 'js/admin/components/forms/Quill.vue';
import Select from 'js/admin/components/forms/Select.vue';

import { getApiUrl } from 'js/utils/env';
import WnlScreensMetaEditorFlashcards from 'js/admin/components/lessons/edit/ScreensMetaEditorFlashcards';
import WnlScreensMetaEditorQuizes from 'js/admin/components/lessons/edit/ScreensMetaEditorQuizes';

let types = {
	html: {
		text: 'Tekst',
		value: 'html',
		hasMeta: false,
	},
	quiz: {
		text: 'Zestaw pytań',
		value: 'quiz',
		hasMeta: true,
		metaEditorComponent: WnlScreensMetaEditorQuizes,
	},
	end: {
		text: 'Zakończenie',
		value: 'end',
		hasMeta: false,
	},
	mockexam: {
		text: 'Próbny egzamin',
		value: 'mockexam',
		hasMeta: true,
	},
	flashcards: {
		text: 'Powtórki',
		value: 'flashcards',
		hasMeta: true,
		metaEditorComponent: WnlScreensMetaEditorFlashcards,
	},
};

export default {
	name: 'ScreensEditor',
	props: ['lessonId'],
	components: {
		WnlScreensMetaEditorFlashcards,
		WnlScreensMetaEditorQuizes,
		'quill': Quill,
		'wnl-form-input': Input,
		'wnl-screens-list': ScreensList,
		'wnl-select': Select,
	},
	data() {
		return {
			ready: false,
			loading: false,
			screenForm: new Form({
				content: null,
				meta: null,
				name: null,
				type: null,
			}),
			screens: [],
			quiz_sets: [],
		};
	},
	computed: {
		screenId() {
			return this.$route.params.screenId;
		},
		screenMeta: {
			get() {
				return JSON.parse(this.screenForm.meta);
			},
			set(screenMeta) {
				this.screenForm.meta = JSON.stringify(screenMeta);
			}
		},
		loaded() {
			return !!this.$route.params.screenId;
		},
		screenFormResourceUrl() {
			return getApiUrl(`screens/${this.$route.params.screenId}`);
		},
		typesOptions() {
			return Object.keys(types).map((key) => types[key]);
		},
		currentType() {
			const type = this.screenForm.type;

			if (type !== null && types.hasOwnProperty(type)) {
				return types[type];
			}

			return null;
		},
		hasChanged() {
			return !isEqual(this.screenForm.data(), this.screenForm.originalData);
		},
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		populateScreenForm() {
			axios.get(this.screenFormResourceUrl)
				.then(response => {
					Object.keys(response.data).forEach((field) => {
						let value = response.data[field];
						if (isObject(value)) {
							value = JSON.stringify(value);
						}
						this.screenForm[field] = value;
						this.screenForm.originalData[field] = value;
					});
				});
		},
		onSubmit() {
			if (!this.hasChanged) {
				return false;
			}

			this.loading = true;

			if (this.currentType.hasMeta) {
				this.screenForm.meta = unescape(this.screenForm.meta);
			} else {
				this.screenForm.meta = '{}';
			}

			this.screenForm.put(this.screenFormResourceUrl)
				.then(() => {
					this.loading = false;
					this.screenForm.originalData = this.screenForm.data();
					this.addAutoDismissableAlert({
						text: 'Zapisano!',
						type: 'success',
					});
					return this.$refs.ScreensList.fetchScreens();
				})
				.catch(exception => {
					this.loading = false;
					this.addAutoDismissableAlert({
						text: 'Nie wyszło :(',
						type: 'error',
					});
					$wnl.logger.capture(exception);
				});
		}
	},
	mounted() {
		if (this.screenId) {
			this.populateScreenForm();
		}
	},
	watch: {
		'$route': 'populateScreenForm'
	}
};
</script>
