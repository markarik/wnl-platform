<template>
	<div>
		<input class="input" type="text" ref="annotationTag" v-model="annotationTag" readonly tabindex="-1">
		<span class="copy-tag" v-if="show && !copied" @click="copyTag">Kopiuj tag</span>
		<span class="copy-tag--success" v-if="show && copied">Skopiowano do schowka!</span>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.copy-tag
		cursor: pointer

		&--success
			color: $color-green;
</style>

<script>
	export default {
		props: {
			show: {
				default: false
			},
			tagId: {
				type: String|Number,
				required: true
			},
			tagContent: {
				type: String|Number,
				required: true
			},
			tag: {
				type: String,
				default: 'a'
			}
		},
		data() {
			return {
				copied: false,
			}
		},
		computed: {
			annotationTag() {
				if (!this.show) return ''

				return `{${this.tag}:${this.tagId}}${this.tagContent}{${this.tag}}`
			},
		},
		methods: {
			copyTag() {
				this.$refs.annotationTag.select();
				document.execCommand("copy");
				this.copied = true;
				window.setTimeout(() => {
					this.copied = false;
				}, 5000)
			},
		}
	}
</script>
