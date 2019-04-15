<template>
	<div>
		<input
			ref="annotationTag"
			class="input"
			type="text"
			:value="content"
			readonly
			tabindex="-1"
		>
		<span
			v-if="show && !copied"
			class="copy-tag"
			@click="copyTag"
		>Kopiuj tag</span>
		<span v-if="show && copied" class="copy-tag copy-tag--success">Skopiowano do schowka!</span>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.copy-tag
		cursor: pointer
		font-size: 0.8rem

		&--success
			color: $color-green;
</style>

<script>
export default {
	props: {
		show: {
			default: false
		},
		content: {
			type: [String, Number],
			default: ''
		}
	},
	data() {
		return {
			copied: false,
		};
	},
	methods: {
		copyTag() {
			this.$refs.annotationTag.select();
			document.execCommand('copy');
			this.copied = true;
			window.setTimeout(() => {
				this.copied = false;
			}, 5000);
		},
	}
};
</script>
