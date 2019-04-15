<template>
	<div>
		<input
			class="input"
			type="text"
			ref="annotationTag"
			:value="content"
			readonly
			tabindex="-1"
		>
		<span
			class="copy-tag"
			v-if="show && !copied"
			@click="copyTag"
		>Kopiuj tag</span>
		<span class="copy-tag copy-tag--success" v-if="show && copied">Skopiowano do schowka!</span>
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
