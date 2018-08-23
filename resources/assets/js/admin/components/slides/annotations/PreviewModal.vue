<template>
	<div>
		<h4 class="title is-4">Podgląd Przypisu</h4>
		<div
      v-html="content"
      :class="['control annotation-preview__content', `control annotation-preview__content--${screenSize}`]"
    />
		<div>
			Rozdzielczość Ekranu:
			<div>
				<div class="field">
					<input
						class="is-checkradio"
						type="radio"
						name="keywordType"
						value="mobile"
						v-model="screenSize"
						id="typeMobile"
					>
					<label for="typeMobile">Telefon Komórkowy</label>
					<input
						class="is-checkradio"
						type="radio"
						name="screenSize"
						value="desktop"
						v-model="screenSize"
						id="typeDesktop"
					>
					<label for="typeDesktop">Monitor</label>
				</div>
			</div>
		</div>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.annotation-preview
		&__content
			border: $border-light-gray
			margin: 0 auto $margin-huge auto
			padding: $margin-base

			&--desktop
				width: 100%

			&--mobile
				max-width: 100%
				width: 480px
</style>

<script>
	export default {
		props: {
			content: {
				type: String,
				default: ''
			}
		},
		data() {
			return {
				screenSize: 'desktop'
			}
		}
	}
</script>
