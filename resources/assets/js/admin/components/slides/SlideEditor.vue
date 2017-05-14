<template>
	<div class="slides-editor">
		<p class="title is-3">Edycja slajdu</p>
		<div class="slides-search">
			<div class="level margin vertical">
				<div class="level-left">
					<div class="level-item">
						<div class="field is-grouped">
							<div class="control">
								<label class="label">Numer lekcji</label>
								<input type="text" class="input">
							</div>
							<div class="control">
								<label class="label">Numer slajdu</label>
								<input type="text" class="input">
							</div>
						</div>
					</div>
				</div>
				<div class="level-right">
					<div class="level-item">
						<a class="button is-outlined">Zaciung slajd</a>
					</div>
				</div>
			</div>
		</div>
		<div class="slide-content-editor">
			<brace
				style="min-height: 100%; min-width: 100%;"
				mode="html"
				theme="chrome"
				@code-change="onCodeChange"
			></brace>
		</div>
		<div class="level">
			<div class="level-left">
				<div class="level-item">
					<p class="control">
						<label class="checkbox">
							<input type="checkbox">
							Slajd funkcjonalny?
						</label>
					</p>
				</div>
			</div>
			<div class="level-right">
				<div class="level-item">
					<a class="button is-primary">
						Zapisz slajd
					</a>
				</div>
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.slides-editor
		max-width: 800px

	.slide-content-editor
		border: $border-light-gray
		height: 500px
		margin: $margin-big 0
</style>

<script>
	import Brace from 'vue-bulma-brace'

	export default {
		name: 'SlideEditor',
		components: {
			'brace': Brace,
		},
		data() {
			return {
				code: '',
			}
		},
		methods: {
			onCodeChange(code) {
				this.code = code
			}
		}
	}
</script>
