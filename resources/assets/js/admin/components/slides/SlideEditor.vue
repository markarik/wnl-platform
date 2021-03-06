<template>
	<div class="slides-editor">
		<wnl-alert
			v-for="(alert, timestamp) in alerts"
			:key="timestamp"
			:alert="alert"
			css-class="fixed"
			:timestamp="timestamp"
			@delete="onDelete"
		/>
		<p class="title is-3">{{title}}</p>

		<slot name="above-content" />

		<div
			v-show="submissionFailed"
			class="notification is-danger has-text-centered"
		>
			Coś poszło nie tak...
		</div>

		<form
			class=""
			action=""
			method="POST"
			@submit.prevent="onSubmit"
			@keydown="form.errors.clear($event.target.name)"
		>

			<template v-if="form.quiz_questions && form.quiz_questions.length">
				<span class="subtitle is-5">Pytania powiązane ze slajdem #{{slideId}}:</span>
				<ul>
					<li v-for="qq in form.quiz_questions" :key="qq">
						<router-link :to="{name: 'quiz-editor', params: {quizQuestionId: qq}}">#{{qq}}</router-link>
					</li>
				</ul>
			</template>

			<div class="slide-content-editor">
				<wnl-code
					v-model="form.content"
					type="text"
					name="content"
					:form="form"
				/>
			</div>

			<slot name="below-content" />

			<div class="level">
				<div class="level-left">
					<div class="level-item">
						<p class="control">
							<wnl-checkbox
								v-model="form.is_functional"
								type="text"
								name="is_functional"
								:form="form"
							>
								Slajd funkcjonalny?
							</wnl-checkbox>
						</p>
					</div>
				</div>
				<div v-if="remove" class="level-center">
					<div v-if="confirmDetach" class="level-item confirm-detach">
						<div>Na pewno?</div>
						<a class="button" @click="confirmDetach=false">Nie</a>
						<a
							class="button is-danger"
							@click="detachSlide"
						>
							Tak
						</a>
					</div>
					<div v-else="" class="level-item">
						<a
							class="button is-danger"
							:class="{'is-loading': detachingSlide}"
							:disabled="!slideId && !screenId"
							@click="confirmDetach=true"
						>Usuń slajd z prezentacji</a>
					</div>
				</div>
				<div class="level-right">
					<div class="level-item">
						<a
							v-if="chartReady"
							class="button is-primary"
							:class="{'is-loading': updatingChart}"
							@click="updateChart"
						>Aktualizuj diagram</a>
					</div>
					<slot name="action" />
					<div class="level-item">
						<a
							class="button"
							:disabled="!slideId && !screenId"
							@click="preview"
						>Podgląd</a>
					</div>
					<div class="level-item">
						<a
							class="button is-primary"
							:class="{'is-loading': loading}"
							:disabled="form.errors.any() || !form.content"
							@click="onSubmit"
						>Zapisz slajd</a>
					</div>
				</div>
			</div>
		</form>
		<wnl-slide-preview
			v-if="showPreviewModal"
			:show-modal="showPreviewModal"
			:content="previewModalContent"
			@closeModal="showPreviewModal=false"
		/>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.slides-editor
		width: 100%

	.slide-content-editor
		border: $border-light-gray
		height: 500px
		margin: $margin-big 0

	.confirm-detach
		*
			margin-right: $margin-small
</style>

<script>
import axios from 'axios';
import { get } from 'lodash';
import { mapActions } from 'vuex';

import WnlCode from 'js/admin/components/forms/Code';
import WnlCheckbox from 'js/admin/components/forms/Checkbox';
import WnlSlidePreview from 'js/components/global/SlidePreview';

import { alerts } from 'js/mixins/alerts';
import Form from 'js/classes/forms/Form';
import { getApiUrl } from 'js/utils/env';

const SECTION_OPEN_TAG_REGEX = /<section.*>$/;
const SECTION_CLOSE_TAG_REGEX = /<\/section>$/;
const COURSE_TAG_REGEX = /[#!]+\(.*\)/;
const FUNCTIONAL_SLIDE_TAG_REGEX = /[#!]+\(functional\)/;

export default {
	name: 'SlideEditor',
	components: {
		WnlCode,
		WnlCheckbox,
		WnlSlidePreview,
	},
	mixins: [alerts],
	props: {
		title: {
			type: String,
			default: 'Edytuj slajd'
		},
		resourceUrl: {
			type: String,
			default: ''
		},
		excluded: {
			type: Array,
			default: () => []
		},
		method: {
			type: String,
			default: 'put'
		},
		requestPayload: {
			type: Object,
			default: () => {
			}
		},
		slideId: {
			type: Number,
			default: 0
		},
		screenId: {
			type: Number,
			default: 0,
		},
		remove: {
			type: Boolean,
			default: false
		}
	},
	data() {
		return {
			form: new Form({
				content: null,
				is_functional: null,
			}),
			showPreviewModal: false,
			previewModalContent: '',
			submissionFailed: false,
			loading: false,
			updatingChart: false,
			detachingSlide: false,
			confirmDetach: false,
		};
	},
	computed: {
		chartReady() {
			let match = null;
			if (this.form.content) {
				match = this.form.content.match('class="chart"');
			}
			return !!this.slideId && !!match;
		},
		content() {
			return this.form.content;
		}
	},
	watch: {
		resourceUrl(newValue) {
			newValue !== '' && this.form.populate(`${this.resourceUrl}?include=quiz_questions`, this.excluded)
				.catch(error => {
					const statusCode = get(error, 'response.status');
					statusCode === 404 && this.addAutoDismissableAlert({
						type: 'error',
						text: 'Slajd o tym ID nie istnieje'
					});
				});
		},
		content() {
			this.removeCourseTags();
		}
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		reset() {
			this.submissionFailed = false;
		},
		onSubmit() {
			this.loading = true;
			this.reset();

			const isValid = this.validateContent(this.content);
			if (!isValid) {
				this.errorFading('Upewnij się że slajd posiada tag section na początku i na końcu treści');
				this.loading = false;
				return;
			}
			this.form.submit(this.method, this.resourceUrl, this.requestPayload)
				.then(data => {
					this.form.content = data.content;
					this.form.is_functional = data.is_functional;
					this.successFading('Zapisano', 2000);
					this.loading = false;
				})
				.catch((error) => {
					this.errorFading('Ups... Coś poszło nie tak.', 4000);
					$wnl.logger.capture(error);
					this.loading = false;
				});
		},
		validateContent(content) {
			const contentSplited = content
				.split('\n')
				.map(line => line.trim())
				.filter(line => line);

			return SECTION_OPEN_TAG_REGEX.test(contentSplited[0])
				&& SECTION_CLOSE_TAG_REGEX.test(contentSplited[contentSplited.length - 1]);
		},
		updateChart() {
			this.updatingChart = true;
			axios.get(getApiUrl(`slides/.updateCharts/${this.slideId}`))
				.then(response => {
					this.form.content = response.data.content;
					this.form.is_functional = response.data.is_functional;
					this.successFading('Diagram zaktualizowany!', 2000);
					this.updatingChart = false;
				})
				.catch(error => {
					this.errorFading('Ups... Coś poszło nie tak.', 4000);
					$wnl.logger.capture(error);
					this.updatingChart = false;
				});
		},
		preview(event) {
			event.preventDefault();
			event.stopPropagation();

			this.showPreviewModal = true;

			axios.post(getApiUrl('slideshow_builder/preview'), {
				content: this.form.content,
				slideId: this.slideId ? this.slideId : null,
				screenId: this.screenId ? this.screenId : null
			}).then(({ data }) => {
				this.previewModalContent = data;
			});
		},
		removeCourseTags() {
			if (!this.form.content) return;

			if (FUNCTIONAL_SLIDE_TAG_REGEX.test(this.form.content)) {
				this.form.is_functional = true;
			}
			this.form.content = this.form.content.replace(COURSE_TAG_REGEX, '');
		},
		detachSlide() {
			this.confirmDetach = false;
			if (!this.slideId) return;
			this.detachingSlide = true;

			axios.post(getApiUrl(`slides/${this.slideId}/.detach`), {
				slideId: this.slideId,
			}).then(() => {
				this.form.content = null;
				this.form.is_functional = false;
				this.form.quiz_questions = [];
				this.successFading('Slajd usunięty.', 2000);
				this.detachingSlide = false;
				this.$emit('resetSearchInputs');
			}).catch(error => {
				if (error.response.status === 400
					|| error.response.status === 404) {
					this.errorFading('Nie można znaleźć takiego slajdu.', 4000);
					this.detachingSlide = false;
				} else {
					this.errorFading('Ups... Coś poszło nie tak.', 4000);
					$wnl.logger.capture(error);
					this.detachingSlide = false;
				}
			});
		}
	},
};
</script>
