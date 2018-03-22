<template>
	<div class="scrollable-main-container wnl-user-profile" :class="{mobile: isMobileProfile}" v-if="currentUserStats">
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					Twój Postęp
				</div>
			</div>
		</div>
		<button @click="resetProgress" class="button is-danger to-right">Wyczyść postęp w nauce</button>

		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">Statystyki</div>
			</div>
		</div>

		<div class="strong">Ogólne</div>
		<div>Liczba spędzonych minut: {{timeSpent}}</div>
		<div class="strong margin top">Lekcje</div>
		<div>Przerobionych lekcji: {{lessonsCompletedPerc}}% ({{lessonsCompleted}}/{{lessonsTotal}})</div>
		<div>Rozpoczętych lekcji: {{lessonsStartedPerc}}% ({{lessonsStarted}}/{{lessonsTotal}})</div>
		<div class="strong margin top">Baza Pytań</div>
		<div>Rozwiązanych pytań: {{questionsSolvedPerc}}% ({{questionsSolved}}/{{questionsTotal}})</div>
		<div class="strong margin top">Społeczność</div>
		<div>Liczba wątków: {{totalSocial}}</div>
	</div>
</template>

<script>
	import { mapActions, mapGetters } from 'vuex'
	import moment from 'moment'
	import { swalConfig } from 'js/utils/swal'

	export default {
		name: 'UserStats',
		computed: {
			...mapGetters(['isMobileProfile', 'currentUserStats']),
			timeSpent() {
				return moment.duration({...this.currentUserStats.time}).asMinutes()
			},
			lessonsStartedPerc() {
				const {started, total} = this.currentUserStats.lessons
				return Math.floor(started / total * 100)
			},
			lessonsCompletedPerc() {
				const {total, completed} = this.currentUserStats.lessons
				return Math.floor(completed / total * 100)
			},
			lessonsTotal(){
				return this.currentUserStats.lessons.total
			},
			lessonsCompleted() {
				return this.currentUserStats.lessons.completed
			},
			lessonsStarted() {
				return this.currentUserStats.lessons.started
			},
			questionsSolvedPerc() {
				const {total, solved} = this.currentUserStats.quiz_questions
				return Math.floor(solved / total * 100)
			},
			questionsTotal() {
				return this.currentUserStats.quiz_questions.total
			},
			questionsSolved() {
				return this.currentUserStats.quiz_questions.solved
			},
			totalSocial() {
				return Object.values(this.currentUserStats.social).reduce((a,b) => a + b, 0)
			}
		},
		methods: {
			...mapActions(['fetchCurrentUserStats', 'toggleOverlay']),
			...mapActions('progress', ['deleteProgress']),
			resetProgress() {
				this.$swal(swalConfig({
					title: this.$t('progress.reset.title'),
					text: this.$t('progress.reset.text'),
					showCancelButton: true,
					confirmButtonText: this.$t('ui.confirm.confirm'),
					cancelButtonText: this.$t('ui.confirm.cancel'),
					type: 'error',
					confirmButtonClass: 'button is-danger',
					reverseButtons: true
				}))
				.then(() => {
					this.toggleOverlay({source: 'userStats', display: true})
					return this.deleteProgress()
				})
				.then(this.fetchCurrentUserStats)
				.then(() => {
					this.toggleOverlay({source: 'userStats', display: false})
				})
				.catch($wnl.logger.errorF)
			}
		},
		mounted() {
			this.fetchCurrentUserStats();
		}
	}
</script>
