<template>
	<div class="quill-container">
		<div ref="quill">
			<slot></slot>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import "node_modules/quill/dist/quill.snow"

	.quill-container
		height: 50vh
</style>

<script>
	import Quill from 'quill'

	export default {
		name: 'Quill',
		props: {
			options: {
				type: Object,
				default: {
					theme: 'snow'
				}
			},
			autofocus: Boolean,
			form: Object,
			name: String,
			value: String,
		},
		data () {
			return {
				focused: this.autofocus,
				quill: null,
				editor: null,
			}
		},
		methods: {
			onTextChange() {
				this.$emit('input', this.editor.innerHTML)
			}
		},
		mounted () {
			this.quill = new Quill(this.$refs.quill, this.options)
			this.editor = this.$refs.quill.firstElementChild
			this.quill.on('text-change', this.onTextChange)
		},
		watch: {
			focused (val) {
				this.editor[val ? 'focus' : 'blur']()
			},
			value (newValue) {
				if (newValue !== this.editor.innerHTML) {
					this.editor.innerHTML = newValue
				}
			}
		}
	}
</script>
