<template lang="html">
	<div class="scrollable-main-container">
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					{{ $t('user.progressReset.header')}}
				</div>
			</div>
		</div>

		<div class="message is-danger">

			<p v-t="'progress.reset.info'"/>
			<button @click="resetProgress" class="button is-danger to-right">
				Wyczyść postęp w nauce
			</button>

			<div class="message-header">
    			<p>Danger</p>
   	 			<button class="delete" aria-label="delete"></button>
		  	</div>
		  	<div class="message-body"></div>
		</div>


		<div class="reset-questions reset-container">

			<p v-t="'user.progressReset.resetQuestionsWarning'"/>
			<button @click="resetQuestions" class="button is-danger to-right">
				Wyczyść wszystkie wyniki
			</button>
		</div>

		<div class="reset-collections reset-container">
			<p v-t="'user.progressReset.resetCollectionsWarning'"/>
			<button @click="resetCollections" class="button is-danger to-right">
				Wyczyść kolekcje
			</button>
		</div>


	</div>

</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	.reset-container
		text-align: center
		padding: $margin-big


</style>

<script>
	export default {
		
	}
</script>