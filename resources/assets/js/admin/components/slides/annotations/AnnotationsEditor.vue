<template>
	<div class="annotations-editor">
		<span class="title is-5 annotations-editor__title">{{title}}</span>
		<form
			action=""
			method="POST"
			@submit.prevent="onSubmit"
			@keydown="form.errors.clear($event.target.name)"
		>
			<div class="field annotation-input-text is-horizontal">
				<div class="field-label">
					<label class="label">Tytuł</label>
				</div>
				<div class="field-body">
					<div class="field">
						<div class="control">
							<input
								v-model="annotation.title"
								class="input"
								type="text"
								@input="onFieldChange"
							>
						</div>
					</div>
				</div>
			</div>
			<div class="field is-horizontal annotation-input-text">
				<div class="field-label">
					<label class="label">Słowa kluczowe</label>
				</div>
				<div class="field-body">
					<div class="field">
						<div class="control">
							<input
								v-model="annotation.keywords"
								class="input"
								type="text"
								@input="onFieldChange"
							>
						</div>
						<p class="help">Lista słów kluczowych oddzielonych przecinkami</p>
					</div>
				</div>
			</div>
			<div class="annotation-input-description">
				<div>
					<quill
						v-model="annotation.description"
						:form="form"
						name="content"
						@input="onFieldChange"
					/>
				</div>
			</div>
			<fieldset class="annotation-tags">
				<legend class="annotation-tags__legend">Tagi</legend>
				<wnl-tags
					ref="tags"
					:default-tags="annotation.tags || []"
					@insertTag="onFieldChange"
				/>
			</fieldset>
			<wnl-content-item-classifier-editor
				v-if="annotation.id"
				class="margin bottom"
				:is-always-active="true"
				:content-item-id="annotation.id"
				:content-item-type="CONTENT_TYPES.ANNOTATION"
			/>
			<template v-if="annotation.id">
				<div class="title is-4">Dane do edytora</div>
				<div class="title is-5 annotations-editor__title">Typ przypisu</div>
				<div class="control annotation-type__form">
					<div class="field">
						<input
							id="typeNeutral"
							v-model="keywordType"
							class="is-checkradio"
							type="radio"
							name="keywordType"
							:value="ANNOTATIONS_TYPES.NEUTRAL"
						>
						<label for="typeNeutral">Neutralny</label>
						<input
							id="typeBasic"
							v-model="keywordType"
							class="is-checkradio"
							type="radio"
							name="keywordType"
							:value="ANNOTATIONS_TYPES.BASIC"
						>
						<label for="typeBasic">Wiedza podstawowa</label>
						<input
							id="typeExpert"
							v-model="keywordType"
							class="is-checkradio"
							type="radio"
							name="keywordType"
							:value="ANNOTATIONS_TYPES.EXPERT"
						>
						<label for="typeExpert">Wiedza specjalistyczna</label>
						<input
							id="typeImage"
							v-model="keywordType"
							class="is-checkradio"
							type="radio"
							name="keywordType"
							:value="ANNOTATIONS_TYPES.IMAGE"
						>
						<label for="typeImage">Zdjęcie</label>
					</div>
				</div>
				<span class="title is-5 annotations-editor__title">Dane do slides.com</span>
				<div class="field is-horizontal annotation-input-text">
					<div class="field-label">
						<label class="label">Tagi do slides.com</label>
					</div>
					<div class="field-body">
						<div
							v-for="tag in parserTags"
							:key="tag"
							class="field field--keyword"
						>
							<div class="control">
								<wnl-keyword-field :content="tag" :show="annotation.id" />
							</div>
						</div>
					</div>
				</div>
				<span class="title is-5 annotations-editor__title">Dane do edytora na platformie</span>
				<div class="field is-horizontal annotation-input-text annotation-input-text--full-width">
					<div class="field-label">
						<label class="label">Tagi do edytora</label>
					</div>
					<div class="field-body">
						<div
							v-for="tag in htmlTags"
							:key="tag"
							class="field field--keyword"
						>
							<div class="control">
								<wnl-keyword-field :content="tag" :show="annotation.id" />
							</div>
						</div>
					</div>
				</div>
			</template>
			<div class="level-item">
				<a
					class="button is-danger"
					:disabled="!annotation.id"
					@click="onDelete"
				>Usuń
				</a>
				<a class="button" @click="isVisible = true">Podgląd</a>
				<a
					class="button is-primary"
					:disabled="form.errors.any() || !annotation.description"
					@click="onSubmit"
				>Zapisz
				</a>
			</div>
		</form>
		<wnl-modal v-if="isVisible" @closeModal="isVisible = false">
			<wnl-preview-modal :content="annotation.description" />
		</wnl-modal>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.field-label
		flex-basis: 80px
		flex-grow: 0

	.annotation-input-description
		margin: $margin-big 0

	.copy-tag
		cursor: pointer

		&--success
			color: $color-green

	.annotation-tags
		border: $border-light-gray
		padding: 10px 15px
		margin: 15px 0

		&__legend
			font-size: 1.25rem

	.level-item
		justify-content: flex-start
		margin: $margin-big 0

		.button
				margin-right: $margin-base

	.annotation-input-text
		.field--keyword
			margin-bottom: $margin-base
			flex: 0 0 300px

		&--full-width .field--keyword
			flex: 0 0 100%

		.field-body
			flex-wrap: wrap

	.annotations-editor
		.quill-container
			height: 500px

		&__title
			margin-bottom: $margin-base
			display: inline-block

	.annotation-type__form
		margin-bottom: $margin-big
</style>

<script>
import axios from 'axios';
import { mapActions } from 'vuex';
import { getApiUrl } from 'js/utils/env';
import Form from 'js/classes/forms/Form';
import { Tags } from 'js/components/global/form/index';
import KeywordField from './KeywordField';
import PreviewModal from './PreviewModal';
import Quill from 'js/admin/components/forms/Quill.vue';
import Modal from 'js/components/global/Modal';
import WnlContentItemClassifierEditor from 'js/components/global/contentClassifier/ContentItemClassifierEditor';
import { CONTENT_TYPES } from 'js/consts/contentClassifier';

export default {
	name: 'AnnotationsEditor',
	components: {
		'wnl-tags': Tags,
		'wnl-keyword-field': KeywordField,
		'quill': Quill,
		'wnl-modal': Modal,
		'wnl-preview-modal': PreviewModal,
		WnlContentItemClassifierEditor
	},
	props: {
		annotation: {
			type: Object,
			default: () => ({})
		}
	},
	data() {
		const ANNOTATIONS_TYPES = {
			NEUTRAL: '1',
			BASIC: '2',
			EXPERT: '3',
			IMAGE: '4',
		};
		return {
			form: new Form({}),
			isDirty: false,
			keywordType: ANNOTATIONS_TYPES.NEUTRAL,
			ANNOTATIONS_TYPES,
			CONTENT_TYPES,
			isVisible: false
		};
	},
	computed: {
		title() {
			return this.annotation.id ?
				`Edycja Przypisu #${this.annotation.id}` : 'Nowy Przypis';
		},
		keywordsList() {
			if (!this.annotation.keywords) return [];

			return this.annotation.keywords.split(',');
		},
		hasKeywords() {
			return this.keywordsList.length;
		},
		parserTags() {
			if (!this.hasKeywords) return [`{a:${this.keywordType}:${this.annotation.id}}{a}`];

			return this.keywordsList.map(keyword => {
				return `{a:${this.keywordType}:${this.annotation.id}}${keyword}{a}`;
			});
		},
		htmlTags() {
			if (!this.hasKeywords) return [
				`<span data-annotation-id="${this.annotation.id}" class="annotation annotation-type-${this.keywordType}"/>`
			];

			return this.keywordsList.map(keyword => {
				return `<span data-annotation-id="${this.annotation.id}" class="annotation annotation-type-${this.keywordType}">${keyword}</span>`;
			});
		}
	},
	watch: {
		async 'annotation.id'() {
			await this.fetchTaxonomyTerms({ contentType: CONTENT_TYPES.ANNOTATION, contentIds: [this.annotation.id] });
		}
	},
	async mounted() {
		if (!this.annotation.id) return;

		await this.fetchTaxonomyTerms({ contentType: CONTENT_TYPES.ANNOTATION, contentIds: [this.annotation.id] });
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('contentClassifier', ['fetchTaxonomyTerms']),
		onFieldChange() {
			this.$emit('hasChanges', this.annotation.id);
		},
		async onSubmit() {
			const tags = this.$refs.tags.tags;
			let event = 'addSuccess';

			const annotation = {
				...this.annotation,
				keywords: this.annotation.keywords.split(',').map(keyword => keyword.trim()),
				tags,
			};

			try {
				if (this.annotation.id) {
					event = 'editSuccess';
					await axios.put(getApiUrl(`annotations/${this.annotation.id}`), annotation);
				} else {
					const { data } = await axios.post(getApiUrl('annotations'), annotation);
					annotation.id = data.id;
				}

				this.$emit(event, annotation);
				this.$emit('hasChanges', 0);
				this.addAutoDismissableAlert({
					text: 'Zapisano!',
					type: 'success'
				});
			} catch (e) {
				this.addAutoDismissableAlert({
					text: 'Nie udało się zapisać przypisu :( Spróbuj jeszcze raz.!',
					type: 'error'
				});
			}
		},
		async onDelete() {
			try {
				await axios.delete(getApiUrl(`annotations/${this.annotation.id}`));
				this.addAutoDismissableAlert({
					text: 'Usunięto!',
					type: 'success'
				});
				this.$emit('deleteSuccess', this.annotation);
				this.$emit('hasChanges', 0);
			} catch (e) {
				this.addAutoDismissableAlert({
					text: 'Nie udało się usunąć przypisu :( Spróbuj jeszcze raz.!',
					type: 'error'
				});
			}
		},
	},
};
</script>
