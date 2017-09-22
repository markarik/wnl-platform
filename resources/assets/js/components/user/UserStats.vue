<template>
	<div class="scrollable-main-container wnl-user-profile" :class="{mobile: isMobileProfile}">
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">Statystyki</div>
			</div>
		</div>

		<div class="strong">Ogólne</div>
		<div>Liczba spędzonych minut: {{timeSpent}}</div>
		<div class="strong margin top">Lekcje</div>
		<div>Procent przerobionych lekcji: {{lessonsCompleted}}</div>
		<div>Procent rozpoczętych lekcji: {{lessonsStarted}}</div>
		<div class="strong margin top">Baza Pytań</div>
		<div>Procent rozwiązanych pytań: {{questionsSolved}}</div>
		<div class="strong margin top">Społeczność</div>
		<div>Liczba wątków: {{totalSocial}}</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.myself-sidenav
		flex: 1

	.wnl-sidenav
		flex: 1
		padding: 7px 0

		&.mobile
			padding: 0

	.mobile-main
		overflow-y: auto
</style>

<script>
	import { mapActions, mapGetters } from 'vuex'
	import moment from 'moment'
	export default {
		name: 'UserStats',
		computed: {
			...mapGetters(['isMobileProfile', 'currentUserStats']),
			timeSpent() {
				return moment.duration({...this.currentUserStats.time}).asMinutes()
			},
			lessonsStarted() {
				const {started, total} = this.currentUserStats.lessons
				return Math.floor(started / total * 100)
			},
			lessonsCompleted() {
				const {total, completed} = this.currentUserStats.lessons
				return Math.floor(completed / total * 100)
			},
			questionsSolved() {
				const {total, solved} = this.currentUserStats.quiz_questions
				return Math.floor(solved / total * 100)
			},
			totalSocial() {
				return Object.values(this.currentUserStats.social).reduce((a,b) => a + b, 0)
			}
		},
		methods: {
			...mapActions(['fetchCurrentUserStats'])
		},
		mounted() {
			this.fetchCurrentUserStats();
		}
	}
</script>
