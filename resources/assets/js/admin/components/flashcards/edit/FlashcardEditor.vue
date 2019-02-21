<template>
	<div class="flashcard-editor">
		<h3 class="title">
			Edycja pytania
			<span v-if="isEdit">Id: {{flashcardId}}</span>
		</h3>
		<form @submit.prevent="flashcardFormSubmit">
				<wnl-form-textarea
					name="content"
					:form="form"
					v-model="form.content"
				>
					Treść pytania
				</wnl-form-textarea>
				<fieldset class="tags-fieldset">
					<legend>Tagi</legend>
					<wnl-tags :default-tags="flashcardTags" @tagsChanged="onTagsChanged"></wnl-tags>
				</fieldset>
				<div class="control">
					<button class="button is-small is-success"
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
import { mapActions } from 'vuex';

import Form from 'js/classes/forms/Form';
import { getApiUrl } from 'js/utils/env';

import { Tags } from 'js/components/global/form';
import Textarea from 'js/admin/components/forms/Textarea';

export default {
	name: 'FlashcardEditor',
	components: {
		'wnl-form-textarea': Textarea,
		'wnl-tags': Tags
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
		...mapActions(['addAutoDismissableAlert']),
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
			const response = await this.form.populate(this.flashcardResourceUrl, ['include']);
			const tags = response.tags;
			if (!tags) return;

			this.flashcardTags = tags.map(tagId => response.included.tags[tagId]);
		}
	}
};
</script>
