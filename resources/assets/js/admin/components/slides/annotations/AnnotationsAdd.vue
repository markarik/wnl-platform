<template>
	<div>
		<p class="title is-3">Dodaj Adnotację</p>
		<form class="" action="" method="POST" @submit.prevent="onSubmit"
			@keydown="form.errors.clear($event.target.name)"
		>
			<div class="field is-horizontal annotation-input-text">
				<div class="field-label">
					<label class="label">Hasło</label>
				</div>
				<div class="field-body">
					<div class="field">
						<div class="control">
							<input class="input" type="text" v-model="keyword">
						</div>
					</div>
				</div>
			</div>
			<div class="field is-horizontal annotation-input-text">
				<div class="field-label">
					<label class="label">ID</label>
				</div>
				<div class="field-body">
					<div class="field">
						<div class="control">
							<input class="input" type="text" v-model="annotationId" readonly tabindex="-1">
						</div>
					</div>
				</div>
			</div>
			<div class="field is-horizontal annotation-input-text">
				<div class="field-label">
					<label class="label">Tag do slides.com</label>
				</div>
				<div class="field-body">
					<div class="field">
						<div class="control">
							<input class="input" type="text" ref="annotationTag" v-model="annotationTag" readonly tabindex="-1">
							<span class="copy-tag" v-if="annotationId && !copied" @click="copyTag">Kopiuj tag</span>
							<span class="copy-tag--success" v-if="copied">Skopiowano do schowka!</span>
						</div>
					</div>
				</div>
			</div>
			<div class="annotation-input-description">
				<wnl-form-code type="text" name="content" :form="form" v-model="form.content"/>
			</div>
			<fieldset class="question-form-fieldset">
				<legend class="question-form-legend">Tagi</legend>
				<wnl-tags :defaultTags="tags" ref="tags"></wnl-tags>
			</fieldset>
			<div class="level-item">
					<a class="button is-primary"
						 :disabled="form.errors.any() || !form.content"
						 @click="onSubmit">Dodaj Adnotacje
					</a>
			</div>
		</form>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.field-label
		flex-basis: 80px
		flex-grow: 0

	.annotation-input-text
		display: inline-flex
		width: 500px
		align-items: center

	.annotation-input-description
		border: $border-light-gray
		height: 500px
		margin: $margin-big 0

	.copy-tag
		cursor: pointer

		&--success
			color: $color-green;

	.question-form-fieldset
		border: $border-light-gray
		padding: 10px 15px
		margin: 15px 0

	.question-form-legend
		font-size: 1.25rem
</style>

<script>
	import {mapActions} from 'vuex';
	import {getApiUrl} from 'js/utils/env'
	import Code from 'js/admin/components/forms/Code'
	import Form from 'js/classes/forms/Form'
	import { Tags } from 'js/components/global/form/index'

	export default {
		name: 'AnnotationsAdd',
		components: {
			'wnl-form-code': Code,
			'wnl-tags': Tags
		},
		data() {
			return {
				form: new Form({
					content: null,
					is_functional: null,
				}),
				keyword: '',
				annotationId: 0,
				copied: false,
				tags: []
			}
		},
		computed: {
			requestPayload() {},
			annotationTag() {
				if (!this.annotationId) return ''

				return `{a:${this.annotationId}}${this.keyword}{a}`
			},
		},
		methods: {
			...mapActions(['addAutoDismissableAlert']),
			copyTag() {
				this.$refs.annotationTag.select();
				document.execCommand("copy");
				this.copied = true;
				window.setTimeout(() => {
					this.copied = false;
				}, 5000)
			},
			onSubmit() {
				const tags = this.$refs.tags.tags;

				axios.post(getApiUrl('annotations'), {
					keyword: this.keyword,
					description: this.form.content,
					tags
				}).then(({data}) => {
					this.annotationId = data.id
					this.addAutoDismissableAlert({
						text: "Dodano Adnotacje!",
						type: 'success'
					})
				})
			}
		}
	}
</script>
