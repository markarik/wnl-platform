<template>
	<div>
		<article class="message is-info">
			<div class="message-header">
				<p>Pobierz swój plan pracy i połącz go z Kalendarzem Goolge</p>
			</div>
			<div class="message-body plan-details">
				<span></span>
				<span class="plan-details__header">Twój obecny plan pracy zakłada naukę od <strong>{{planStartDate}}</strong> do <strong>{{planEndDate}}</strong>.</span>
				<span class="plan-details__explainer">Po pobraniu planu - możesz zaimportować go do <a href="https://calendar.google.com" target="_blank">Kalendarza Google</a>:</span>
				<div class="paln-details__list">
					<ol>
						<li>Otwórz <a href="https://calendar.google.com" target="_blank">Kalendarz Google</a> na komputerze. Uwaga: import możesz wykonać tylko na komputerze, nie jest to możliwe na telefonie ani na tablecie.</li>
						<li>W prawym górnym rogu kliknij <span class="icon is-small"><i class="fa fa-cog"></i></span> a następnie Ustawienia.</li>
						<li>Kliknij <strong>Importuj/eksportuj</strong>.</li>
						<li>Kliknij <strong>Wybierz plik z komputera</strong> i wybierz wyeksportowany wcześniej plik. Nazwa pliku powinna mieć na końcu „csv”.</li>
						<li>Wybierz kalendarz, do którego chcesz dodać zaimportowane wydarzenia. Domyślnie wydarzenia są importowane do kalendarza głównego.</li>
						<li>Kliknij <strong>Importuj</strong>.</li>
					</ol>
				</div>
			</div>
		</article>
		<div class="download-plan">
			<a
				@click="downloadPlan"
				class="button button is-primary is-outlined is-big"
			>{{ $t('lessonsAvailability.buttons.downloadPlan') }}
			</a>
		</div>
	</div>

</template>

<style lang="sass" scoped>
@import 'resources/assets/sass/variables'

.plan-details
	display: flex
	flex-direction: column
	.plan-details__header
		margin-bottom: $margin-small
	.paln-details__list
		padding: $margin-base
		li
			margin: $margin-tiny

</style>

<script>
 	import { mapGetters } from 'vuex'
	import moment from 'moment'
	import { first,last } from 'lodash'
	import { getApiUrl } from 'js/utils/env'
	import { download } from 'js/utils/download'

	export default {
		name: 'DownloadPlan',
		computed: {
			...mapGetters('course', ['getRequiredLessons']),
			...mapGetters(['currentUserId']),
			sortedRequiredUserLessons() {
				return this.requiredLessons.sort((lessonA, lessonB) => {
					return lessonA.startDate - lessonB.startDate
				})
			},
			requiredLessons() {
				return Object.values(this.getRequiredLessons).filter(requiredLesson => {
					if (requiredLesson.is_required && requiredLesson.isAccessible) {
						return requiredLesson
					}
				})
			},
			planStartDate() {
				if (!first(this.sortedRequiredUserLessons)) return

				return moment(first(this.sortedRequiredUserLessons).startDate * 1000).format('LL')
			},
			planEndDate() {
				if (!last(this.sortedRequiredUserLessons)) return

				return moment(last(this.sortedRequiredUserLessons).startDate * 1000).format('LL')
			},
		},
		methods: {
			downloadPlan() {
				download(`user_lesson/${this.currentUserId}/exportPlan`, 'plan_pracy.csv')
			},
		}
	}
</script>
