<template>
	<div class="annotations-editor">
		<span class="title is-5">{{title}}</span>
		<hr/>
		<form class="" action="" method="POST" @submit.prevent="onSubmit"
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
				<div class="screen-content-editor">
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
				<span class="title is-5">Dane do slides.com</span>
				<hr/>
				<div class="field is-horizontal annotation-input-text">
					<div class="field-label">
						<label class="label">ID</label>
					</div>
					<div class="field-body">
						<div class="field">
							<div class="control">
								<input class="input" type="text" v-model="annotation.id" readonly tabindex="-1">
							</div>
						</div>
					</div>
				</div>
				<div class="field is-horizontal annotation-input-text">
					<div class="field-label">
						<label class="label">Tagi do slides.com</label>
					</div>
					<div class="field-body">
						<div class="field field--keyword"  v-for="keyword in keywordsList" :key="keyword">
							<div class="control">
								<wnl-keyword-field :tag-id="annotation.id" :tag-content="keyword" :show="annotation.id"/>
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
		.field-body
			flex-wrap: wrap
</style>

<style lang="sass">
	.annotations-editor
		.quill-container
			height: 500px
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
				isDirty: false
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
				return (this.annotation.keywords || '').split(',')
			},
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

				if (this.annotation.id) {
					event = 'editSuccess'
					await axios.put(getApiUrl(`annotations/${this.annotation.id}`), annotation)
				} else {
					const {data} = await axios.post(getApiUrl('annotations'), annotation)
					annotation.id = data.id
				}

				this.$emit(event, annotation)
				this.addAutoDismissableAlert({
					text: "Zapisano!",
					type: 'success'
				})

				this.$emit('hasChanges', 0)
			},
			async onDelete() {
				await axios.delete(getApiUrl(`annotations/${this.annotation.id}`))
				this.addAutoDismissableAlert({
					text: "Usunięto!",
					type: 'success'
				})
				this.$emit('deleteSuccess', this.annotation)
				this.$emit('hasChanges', 0)
			}
		},
	}
</script>
