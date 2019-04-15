<template>
	<div>
		<article class="message is-info">
			<div class="message-header">
				<p>Pobierz swój plan pracy i połącz go z Kalendarzem Google</p>
			</div>
			<div class="message-body plan-details">
				<span>Wiemy, że dobra organizacja pracy jest dla Ciebie priorytetem. Dlatego stworzyliśmy możliwość połączenia indywidualnego planu pracy z Twoim kalendarzem Google. Aby to zrobić postępuj zgodnie z podanymi instrukcjami. 🙂</span>
				<span>Twój obecny plan pracy zakłada naukę od <strong>{{planStartDate}}</strong> do <strong>{{planEndDate}}</strong>.</span>
				<div class="paln-details__list">
					<ol>
						<li>Kliknij na "POBIERZ PLAN PRACY", aby wyeksportowanć plik z nazwami lekcji i przypisanymi do nich datami. Plik powinien nazywać się "plan_pracy.csv".</li>
						<li>Otwórz <a href="https://calendar.google.com" target="_blank">Kalendarz Google</a> na komputerze. Uwaga: import możesz wykonać tylko na komputerze, nie jest to możliwe na telefonie ani na tablecie.</li>
						<li>W prawym górnym rogu kliknij <span class="icon is-small"><i class="fa fa-cog"></i></span> a następnie Ustawienia.</li>
						<li>Kliknij <strong>Importuj/eksportuj</strong>.</li>
						<li>Kliknij <strong>Wybierz plik z komputera</strong> i wybierz wcześniej pobrany wcześniej plik.</li>
						<li>Wybierz kalendarz, do którego chcesz dodać zaimportowane wydarzenia. Domyślnie wydarzenia są importowane do kalendarza głównego.</li>
						<li>Kliknij <strong>Importuj</strong>.</li>
					</ol>
				</div>
				<span>I gotowe! Twój plan pracy został zaimportowany do Twojego kalendarza 😎</span>
				<span>Pamiętaj, jeśli zmienisz daty lekcji - nie pojawią się one automatycznie w kalendarzu - należy ponownie go zaimportować.</span>
			</div>
		</article>
		<div class="download-plan">
			<a
				@click="downloadPlan"
				class="button button is-primary is-outlined is-big"
			>{{$t('lessonsAvailability.buttons.downloadPlan')}}
			</a>
		</div>
	</div>
</template>

<style lang="sass" scoped>
@import 'resources/assets/sass/variables'

.plan-details
	display: flex
	flex-direction: column
	.paln-details__list
		margin: $margin-base

</style>

<script>
import axios from 'axios';
import { mapActions, mapGetters } from 'vuex';
import moment from 'moment';
import { first,last } from 'lodash';
import { getApiUrl } from 'js/utils/env';
import { downloadFile } from 'js/utils/download';

export default {
	name: 'DownloadPlan',
	computed: {
		...mapGetters('course', ['getRequiredLessons']),
		...mapGetters(['currentUserId']),
		sortedRequiredUserLessons() {
			return this.requiredLessons
				.slice()
				.sort((lessonA, lessonB) => {
					return lessonA.startDate - lessonB.startDate;
				});
		},
		requiredLessons() {
			return Object.values(this.getRequiredLessons).filter(requiredLesson => {
				if (requiredLesson.is_required && requiredLesson.isAccessible) {
					return requiredLesson;
				}
			});
		},
		planStartDate() {
			if (!first(this.sortedRequiredUserLessons)) return;

			return moment(first(this.sortedRequiredUserLessons).startDate * 1000).format('LL');
		},
		planEndDate() {
			if (!last(this.sortedRequiredUserLessons)) return;

			return moment(last(this.sortedRequiredUserLessons).startDate * 1000).format('LL');
		},
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		async downloadPlan() {
			try {
				const response = await axios.request({
					url: getApiUrl(`user_lesson/${this.currentUserId}/exportPlan`),
					responseType: 'blob',
				});

				downloadFile(response.data, 'plan_pracy.csv');
			} catch (err) {
				this.handleDownloadFailure(err);
			}
		},
		handleDownloadFailure(err) {
			this.addAutoDismissableAlert({
				text: 'Ups, coś poszło nie tak, spróbuj ponownie.',
				type: 'error'
			});

			$wnl.logger.capture(err);
		},
	}
};
</script>
