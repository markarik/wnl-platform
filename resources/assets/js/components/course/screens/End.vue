<template>
	<div class="wnl-screen-html content">
		<div class="margin vertical" v-if="content" v-html="content"></div>
		<p class="end-button has-text-centered">
			<router-link :to="{name: 'dashboard'}" class="button is-primary is-outlined" v-if="isLast">
				Zakończ kurs!
			</router-link>
			<router-link :to="to" class="button is-primary is-outlined" v-else>
				Następna lekcja
			</router-link>
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
			},
			isLast() {
				// TODO: Apr 1, 2017 - Just for demo purposes, remove it mate!
				return this.screenData.lessons === 3
			},
			to() {
				if (!this.isLast) {
					return {
						name: 'lessons',
						params: {
							courseId: this.screenData.editions,
							lessonId: this.screenData.lessons + 1,
						}
					}
				}
			}
		}
	}
</script>
