<template>
	<div class="quill-container">
		<div ref="quill" v-model="inputValue">
			<slot></slot>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import "node_modules/quill/dist/quill.bubble"

	.quill-container
		height: 200px
</style>

<script>
	import Quill from 'quill'
	import { set } from 'vue'

	import { formInput } from 'js/mixins/form-input'

	export default {
		name: 'Bubble',
		mixins: [formInput],
		props: {
			options: {
				type: Object,
				default: () => {
					theme: 'bubble'
				},
			},
			autofocus: {
				type: Boolean,
				default: true,
			},
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
				// this.mutation(types.FORM_INPUT, { name: this.name, value: this.editor.innerHTML })
				this.setValue(this.editor.innerHTML)
			}
		},
		mounted () {
			console.log(this.options)
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
