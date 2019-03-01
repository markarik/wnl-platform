<template>
	<div class="flashcard-editor">
		<h3 class="title">
			Edycja pytania
			<span v-if="isEdit">Id: {{flashcardId}}</span>
		</h3>
		<form @submit.prevent="flashcardFormSubmit">
			<wnl-textarea
				name="content"
				:form="form"
				v-model="form.content"
			>
				Treść pytania
			</wnl-textarea>
			<fieldset class="tags-fieldset">
				<legend>Tagi</legend>
				<wnl-tags :default-tags="flashcardTags" @tagsChanged="onTagsChanged"></wnl-tags>
			</fieldset>
			<wnl-content-item-classifier-editor
				v-if="isEdit"
				class="margin bottom"
				:is-always-active="true"
				:content-item-id="flashcardId"
				:content-item-type="CONTENT_TYPES.FLASHCARD"
			/>
			<div v-else class="notification is-info">
				<span class="icon">
					<i class="fa fa-info-circle"></i>
				</span>
				Zapisz pytanie, aby przypisać do niego pojęcia
			</div>
			<div class="control">
				<button
					class="button is-small is-success"
					:class="{'is-loading': loading}"
					:disabled="!hasChanged"
					type="submit"
				>
					<span class="margin right">Zapisz</span>
					<span class="icon is-small">
						<i class="fa fa-save"></i>
					</span>
				</button>
			</div>
		</form>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.flashcard-editor
		max-width: 800px

		.tags-fieldset
			border: $border-light-gray
			padding: 10px 15px
			margin: 15px 0
</style>

<script>
import {isEqual} from 'lodash';
import {mapActions} from 'vuex';

import Form from 'js/classes/forms/Form';
import {getApiUrl} from 'js/utils/env';
import {CONTENT_TYPES} from 'js/consts/contentClassifier';

import {Tags as WnlTags} from 'js/components/global/form';
import WnlTextarea from 'js/admin/components/forms/Textarea';
import WnlContentItemClassifierEditor from 'js/components/global/contentClassifier/ContentItemClassifierEditor';

export default {
	name: 'FlashcardEditor',
	components: {
		WnlContentItemClassifierEditor,
		WnlTextarea,
		WnlTags
	},
	props: ['flashcardId'],
	data() {
		return {
			form: new Form({
				content: '',
				tags: ''
			}),
			loading: false,
			flashcardTags: [],
			CONTENT_TYPES,
		};
	},
	computed: {
		isEdit() {
			return this.flashcardId !== 'new';
		},
		flashcardResourceUrl() {
			return getApiUrl(this.isEdit ? `flashcards/${this.flashcardId}?include=tags` : 'flashcards');
		},
		hasChanged() {
			return !isEqual(this.form.originalData, this.form.data());
		},
	},
	methods: {
		...mapActions(['addAutoDismissableAlert', 'setupCurrentUser']),
		...mapActions('contentClassifier', ['fetchTaxonomyTerms']),
		...mapActions('flashcards', {
			invalidateFlashcardsCache: 'invalidateCache',
		}),
		flashcardFormSubmit() {
			if (!this.hasChanged) {
				return false;
			}

			this.loading = true;
			this.form[this.isEdit ? 'put' : 'post'](this.flashcardResourceUrl)
				.then(response => {
					this.loading = false;
					this.addAutoDismissableAlert({
						text: 'Pytanie zapisane!',
						type: 'success',
					});

					this.invalidateFlashcardsCache();
					this.form.originalData = this.form.data();

					this.$router.push({
						name: 'flashcards-edit',
						params: {
							flashcardId: response.id
						}
					});
				})
				.catch(exception => {
					this.loading = false;
					this.addAutoDismissableAlert({
						text: 'Nie udało się :(',
						type: 'error',
					});
					$wnl.logger.capture(exception);
				});
		},
		onTagsChanged(tags) {
			this.form.tags = tags.map(({id}) => id);
		}
	},
	async mounted() {
		if (this.isEdit) {
			await this.setupCurrentUser();
			await this.fetchTaxonomyTerms({contentType: CONTENT_TYPES.FLASHCARD, contentIds: [this.flashcardId]});

			const response = await this.form.populate(this.flashcardResourceUrl, ['include']);
			const tags = response.tags;
			if (!tags) return;

			this.flashcardTags = tags.map(tagId => response.included.tags[tagId]);
		}
	},
	watch: {
		async flashcardId() {
			// This is called only after user saves new flashcard and we put ID in the URL
			await this.fetchTaxonomyTerms({contentType: CONTENT_TYPES.FLASHCARD, contentIds: [this.flashcardId]});
		}
	}
};
</script>
