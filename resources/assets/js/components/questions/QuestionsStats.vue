<template>
	<div class="wnl-app-layout">
		<wnl-questions-navigation/>
		<div class="wnl-middle wnl-app-layout-main">
			<div class="scrollable-main-container">
				<p class="title is-3">Stats</p>

				<p>Cała baza: {{ allQuestionsCount }} {{ totalUnresolved }}</p>

				<p>% przerobionia bazy:</p>

				<p>% przerobienia przedmiotów:</p>

				<p>% poprawnych odpowiedzi:</p>

				<p>% poprawnych odpowiedzi - przedmioty:</p>

			</div>
		</div>
		<wnl-sidenav-slot
				:isDetached="!isChatMounted"
				:isVisible="isLargeDesktop"
				:hasChat="true"
		>
		</wnl-sidenav-slot>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

<script>
	import {mapGetters, mapActions} from 'vuex'
	import QuestionsNavigation from 'js/components/questions/QuestionsNavigation'
	import SidenavSlot from 'js/components/global/SidenavSlot'

	export default {
		name: 'QuestionsStats',
		components: {
			'wnl-questions-navigation': QuestionsNavigation,
			'wnl-sidenav-slot': SidenavSlot,
		},
		computed: {
			...mapGetters(['isChatMounted', 'isLargeDesktop']),
			...mapGetters('questions', [
				'allQuestionsCount',
				'filters'
			]),
			totalUnresolved() {
				let dupa = this.filters
				console.log(dupa)
			},
		},
		methods: {
			...mapActions('questions', [

			]),
		},
		mounted() {

		}
	}
</script>
