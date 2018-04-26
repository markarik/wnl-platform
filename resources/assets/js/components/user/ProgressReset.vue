<template lang="html">
	<div class="scrollable-main-container">
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					{{ $t('user.progressReset.header') }}
				</div>
			</div>
		</div>

		<div class="message is-danger reset-container">
			<div class="message-header">
    			<strong v-t="'user.progressReset.progressHeader'"></strong>
		  	</div>
		  	<div class="message-body" v-t="'progress.reset.info'"></div>
			<button
				@click="resetProgress"
				class="button is-danger to-right"
				v-t="'user.progressReset.progressButton'"/>
		</div>

		<div class="message is-danger reset-container">
			<div class="message-header">
    			<strong v-t="'user.progressReset.questionsHeader'"/>
		  	</div>
			<div class="message-body" v-t="'user.progressReset.questionsWarning'"/>
			<button
				@click="resetProgress"
				class="button is-danger to-right"
				v-t="'user.progressReset.questionsButton'"/>
		</div>

		<div class="message is-danger reset-container">
			<div class="message-header">
    			<strong v-t="'user.progressReset.questionsHeader'"></strong>
		  	</div>
			<div class="message-body" v-t="'user.progressReset.collectionsWarning'"/>
			<button
				@click="resetProgress"
				class="button is-danger to-right"
				v-t="'user.progressReset.questionsButton'"/>
		</div>
	</div>

</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	.reset-container
		text-align: center
		padding-bottom: $margin-base

		.message-body
			border: none


</style>

<script>
	export default {
		methods: {
			resetProgress() {

			}
		}
	}
</script>