<template>
	<div class="annotations-editor">
		<span class="title is-5 annotations-editor__title">{{title}}</span>
		<form action="" method="POST" @submit.prevent="onSubmit"
			@keydown="form.errors.clear($event.target.name)"
		>
			<div class="field annotation-input-text is-horizontal">
				<div class="field-label">
					<label class="label">Tytuł</label>
				</div>
				<div class="field-body">
					<div class="field">
						<div class="control">
							<input class="input" type="text" v-model="annotation.title" @input="onFieldChange">
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
							<input class="input" type="text" v-model="annotation.keywords" @input="onFieldChange">
						</div>
						<p class="help">Lista słów kluczowych oddzielonych przecinkami</p>
					</div>
				</div>
			</div>
			<div class="annotation-input-description">
				<div>
					<quill
						:form="form"
						name="content"
						v-model="annotation.description"
						@input="onFieldChange"
					>
					</quill>
				</div>
			</div>
			<fieldset class="annotation-tags">
				<legend class="annotation-tags__legend">Tagi</legend>
				<wnl-tags :defaultTags="annotation.tags || []" ref="tags" @insertTag="onFieldChange"></wnl-tags>
			</fieldset>
			<div class="level-item">
					<a class="button is-danger"
						 :disabled="!annotation.id"
						 @click="onDelete">Usuń
					</a>
					<a class="button is-primary"
						 :disabled="form.errors.any() || !annotation.description"
						 @click="onSubmit">Zapisz
					</a>
			</div>
			<template v-if="annotation.id">
				<div class="title is-4">Dane do edytora</div>
				<div class="title is-5 annotations-editor__title">Typ przypisu</div>
				<div class="control annotation-type__form">
					<label class="radio">
						<input type="radio" name="keywordType" value="1" v-model="keywordType" :disabled="!hasKeywords">
						Neutralny
					</label>
					<label class="radio">
						<input type="radio" name="keywordType" value="2" v-model="keywordType" :disabled="!hasKeywords">
						Wiedza Podstawowa
					</label>
					<label class="radio">
						<input type="radio" name="keywordType" value="3" v-model="keywordType">
						Bez Słowa Kluczowego
					</label>
				</div>
				<span class="title is-5 annotations-editor__title">Dane do slides.com</span>
				<div class="field is-horizontal annotation-input-text">
					<div class="field-label">
						<label class="label">Tagi do slides.com</label>
					</div>
					<div class="field-body">
						<div class="field field--keyword"  v-for="tag in parserTags" :key="tag">
							<div class="control">
								<wnl-keyword-field :content="tag" :show="annotation.id"/>
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
						<div class="field field--keyword"  v-for="tag in htmlTags" :key="tag">
							<div class="control">
								<wnl-keyword-field :content="tag" :show="annotation.id"/>
							</div>
						</div>
					</div>
				</div>
			</template>
		</form>
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
			color: $color-green;

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
	import {mapActions} from 'vuex';
	import {set} from 'vue';
	import {getApiUrl} from 'js/utils/env'
	import Code from 'js/admin/components/forms/Code'
	import Form from 'js/classes/forms/Form'
	import { Tags } from 'js/components/global/form/index'
	import KeywordField from "./KeywordField";
	import Quill from 'js/admin/components/forms/Quill.vue'

	export default {
		name: 'AnnotationsEditor',
		components: {
			'wnl-form-code': Code,
			'wnl-tags': Tags,
			'wnl-keyword-field': KeywordField,
			'quill': Quill,
		},
		data() {
			return {
				form: new Form({}),
				isDirty: false,
				keywordType: this.hasKeywords ? '1' : '3'
			}
		},
		props: {
			annotation: {
				type: Object,
				default: () => ({})
			}
		},
		computed: {
			title() {
				return this.annotation.id ?
					`Edycja Przypisu #${this.annotation.id}` : 'Nowy Przypis'
			},
			keywordsList() {
				if (!this.annotation.keywords) return []

				return this.annotation.keywords.split(',')
			},
			hasKeywords() {
				return this.keywordsList.length
			},
			parserTags() {
				if (this.keywordType === '3') {
					return [`{a:${this.keywordType}:${this.annotation.id}}{a}`]
				}

				if (!this.hasKeywords) return [];

				return this.keywordsList.map(keyword => {
					return `{a:${this.keywordType}:${this.annotation.id}}${keyword}{a}`
				})
			},
			htmlTags() {
				if (this.keywordType === '3') {
					return [
						`<span data-annotation-id="${this.annotation.id}" class="annotation annotation-type-${this.keywordType}"/>`
					]
				}

				if (!this.hasKeywords) return [];

				return this.keywordsList.map(keyword => {
					return `<span data-annotation-id="${this.annotation.id}" class="annotation annotation-type-${this.keywordType}">${keyword}</span>`
				})
			}
		},
		methods: {
			...mapActions(['addAutoDismissableAlert']),
			onFieldChange() {
				this.$emit('hasChanges', this.annotation.id)
			},
			async onSubmit() {
				const tags = this.$refs.tags.tags;
				let event = 'addSuccess'

				const annotation = {
					...this.annotation,
					keywords: this.annotation.keywords.split(',').map(keyword => keyword.trim()),
					tags,
				}

				try {
					if (this.annotation.id) {
						event = 'editSuccess'
						await axios.put(getApiUrl(`annotations/${this.annotation.id}`), annotation)
					} else {
						const {data} = await axios.post(getApiUrl('annotations'), annotation)
						annotation.id = data.id
					}

					this.$emit(event, annotation)
					this.$emit('hasChanges', 0)
					this.addAutoDismissableAlert({
						text: "Zapisano!",
						type: 'success'
					})
				} catch (e) {
					this.addAutoDismissableAlert({
						text: "Nie udało się zapisać przypisu :( Spróbuj jeszcze raz.!",
						type: 'error'
					})
				}
			},
			async onDelete() {
				try {
					await axios.delete(getApiUrl(`annotations/${this.annotation.id}`))
					this.addAutoDismissableAlert({
						text: "Usunięto!",
						type: 'success'
					})
					this.$emit('deleteSuccess', this.annotation)
					this.$emit('hasChanges', 0)
				} catch (e) {
					this.addAutoDismissableAlert({
						text: "Nie udało się usunąć przypisu :( Spróbuj jeszcze raz.!",
						type: 'error'
					})
				}
			}
		},
		watch: {
			hasKeywords() {
				if (!this.hasKeywords) this.keywordType = '3'
			}
		}
	}
</script>
