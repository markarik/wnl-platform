<template>
	<div class="scrollable-main-container wnl-user-profile" :class="{mobile: isMobileProfile}" v-if="currentUserStats">
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					Twój Postęp
				</div>
			</div>
		</div>
		<div class="reset-progress">
			<p v-t="'progress.reset.info'"/>
			<button @click="resetProgress" class="button is-danger to-right">Wyczyść postęp w nauce</button>
		</div>

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
<style lang="sass" scoped>
.reset-progress
	color: #4a4a4a
	line-height: 1.8em
	max-width: 80%
	margin: 0 auto
	text-align: center

	p
		text-align: left
		margin-bottom: 1em
	button
		margin-bottom: 2em

</style>

<script>
	import { mapActions, mapGetters } from 'vuex'
	import moment from 'moment'
	import { swalConfig } from 'js/utils/swal'
	import emits_events from 'js/mixins/emits-events'
	import context from 'js/consts/events_map/context.json'
	import features from 'js/consts/events_map/features.json';

	export default {
		name: 'UserStats',
		mixins: [emits_events],
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
			...mapActions('progress', ['deleteProgress', 'setupCourse']),
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
					this.emitUserEvent({
						subcontext: context.account.subcontext.stats.value,
						features: features.progress.value,
						action: features.progress.actions.erase_progress.value
					})
					this.toggleOverlay({source: 'userStats', display: true})
					return this.deleteProgress()
				})
				.then(() => Promise.all([this.fetchCurrentUserStats(), this.setupCourse()]))
				.then(() => {
					this.toggleOverlay({source: 'userStats', display: false})
				})
				.catch($wnl.logger.error)
			}
		},
		mounted() {
			this.fetchCurrentUserStats();
		}
	}
</script>
