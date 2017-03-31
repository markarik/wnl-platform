<template>
	<div class="wnl-screen-html content">
		<p class="title has-text-centered">To już wszystko!</p>
		<div class="margin vertical" v-if="content" v-html="content"></div>
		<p class="end-button has-text-centered">
			<router-link :to="{name: 'dashboard'}" class="button is-primary is-outlined">Zakończ lekcję</router-link>
		</p>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-screen-html
		margin: $margin-big 0

	.end-button
		margin-top: $margin-big
</style>

<script>
	export default {
		name: 'End',
		props: ['screenData'],
		computed: {
			content() {
				if (this.screenData.content.length > 0) {
					return this.screenData.content
				}

				return false
			}
		}
	}
</script>
