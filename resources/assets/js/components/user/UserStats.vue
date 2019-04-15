<template>
	<div
		v-if="currentUserStats"
		class="scrollable-main-container wnl-user-profile"
		:class="{mobile: isMobileProfile}"
	>
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					Twój Postęp
				</div>
			</div>
		</div>
		<div class="reset-progress">
			<p v-t="'progress.reset.info'" />
			<button class="button is-danger to-right" @click="satisfactionGuaranteeModalVisible = true">Wyczyść postęp w nauce</button>
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
		<wnl-satisfaction-guarantee-modal
			:visible="satisfactionGuaranteeModalVisible"
			@closeModal="satisfactionGuaranteeModalVisible = false"
			@submit="resetProgress"
		>
			<template slot="title">{{$t('user.progressReset.progressModalHeader')}}</template>
		</wnl-satisfaction-guarantee-modal>
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
import { mapActions, mapGetters } from 'vuex';
import moment from 'moment';
import emits_events from 'js/mixins/emits-events';
import context from 'js/consts/events_map/context.json';
import features from 'js/consts/events_map/features.json';
import WnlSatisfactionGuaranteeModal from 'js/components/global/modals/SatisfactionGuaranteeModal';

export default {
	name: 'UserStats',
	components: { WnlSatisfactionGuaranteeModal },
	mixins: [emits_events],
	data() {
		return {
			satisfactionGuaranteeModalVisible: false
		};
	},
	computed: {
		...mapGetters(['isMobileProfile', 'currentUserStats']),
		timeSpent() {
			return moment.duration({ ...this.currentUserStats.time }).asMinutes();
		},
		lessonsStartedPerc() {
			const { started, total } = this.currentUserStats.lessons;
			return Math.floor(started / total * 100);
		},
		lessonsCompletedPerc() {
			const { total, completed } = this.currentUserStats.lessons;
			return Math.floor(completed / total * 100);
		},
		lessonsTotal(){
			return this.currentUserStats.lessons.total;
		},
		lessonsCompleted() {
			return this.currentUserStats.lessons.completed;
		},
		lessonsStarted() {
			return this.currentUserStats.lessons.started;
		},
		questionsSolvedPerc() {
			const { total, solved } = this.currentUserStats.quiz_questions;
			return Math.floor(solved / total * 100);
		},
		questionsTotal() {
			return this.currentUserStats.quiz_questions.total;
		},
		questionsSolved() {
			return this.currentUserStats.quiz_questions.solved;
		},
		totalSocial() {
			return Object.values(this.currentUserStats.social).reduce((a,b) => a + b, 0);
		}
	},
	methods: {
		...mapActions(['fetchCurrentUserStats', 'toggleOverlay']),
		...mapActions('progress', ['deleteProgress', 'setupCourse']),
		async resetProgress() {
			this.satisfactionGuaranteeModalVisible = false;
			try {
				this.emitUserEvent({
					subcontext: context.account.subcontext.stats.value,
					features: features.progress.value,
					action: features.progress.actions.erase_progress.value
				});
				this.toggleOverlay({ source: 'userStats', display: true });
				await this.deleteProgress();
				await Promise.all([this.fetchCurrentUserStats(), this.setupCourse()]);
			} catch (error) {
				$wnl.logger.error(error);
			} finally {
				this.toggleOverlay({ source: 'userStats', display: false });
			}
		}
	},
	mounted() {
		this.fetchCurrentUserStats();
	}
};
</script>
