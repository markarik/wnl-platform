<template>
	<div>
		<div class="quill-container">
			<div ref="quill">
				<slot></slot>
			</div>
		</div>
		<wnl-upload
			v-if="uploadEnabled"
			@success="onUploadSuccess"
			endpoint="upload"
			class="upload"
		>
			<a class="button is-small is-outlined is-primary margin top">
				Wstaff obraz
			</a>
		</wnl-upload>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import "node_modules/quill/dist/quill.snow"
	@import 'resources/assets/sass/variables'

	.quill-container
		height: 50vh
		margin-bottom: $margin-huge

	.ql-editor
		p
			margin: $margin-base 0
</style>

<script>
import Quill from 'quill';
import { fontColors } from 'js/utils/colors';

export default {
	name: 'Quill',
	props: {
		options: {
			type: Object,
			default() {
				return {
					theme: 'snow',
					modules: {
						toolbar: [
							[{ 'header': [false, 1, 2, 3] }],
							['bold', 'italic', 'underline', 'link'],
							[{ color: fontColors }],
							['clean'],
							[{ 'align': [] }],
							[{ list: 'ordered' }, { list: 'bullet' }, { 'indent': '-1' }, { 'indent': '+1' }],
							['blockquote', 'video'],
						]
					},
				};
			}
		},
		autofocus: Boolean,
		form: Object,
		name: String,
		value: String,
		uploadEnabled: {
			type: Boolean,
			default: true
		}
	},
	data () {
		return {
			focused: this.autofocus,
			quill: null,
			editor: null,
		};
	},
	methods: {
		onTextChange() {
			this.$emit('input', this.editor.innerHTML);
		},
		onUploadSuccess(data) {
			this.editor.innerHTML = this.editor.innerHTML + `<img src="${data}"/>`;
		}
	},
	mounted () {
		this.quill = new Quill(this.$refs.quill, this.options);
		this.editor = this.$refs.quill.firstElementChild;
		if (this.value) {
			this.editor.innerHTML = this.value;
		}
		this.$nextTick(() => {
			this.quill.on('text-change', this.onTextChange);
		});
	},
	watch: {
		focused (val) {
			this.editor[val ? 'focus' : 'blur']();
		},
		value (newValue) {
			if (newValue !== this.editor.innerHTML) {
				this.editor.innerHTML = newValue || '';
			}
		}
	}
};
</script>
