<template>
	<div>
		<article class="message is-info">
			<div class="message-header">
				<p>Pobierz swój plan pracy</p>
			</div>
			<div class="message-body plan-details">
				<span>Twój obecny plan pracy zakłada naukę od <strong>{{planStartDate}}</strong> do <strong>{{planEndDate}}</strong>.</span>
				<span></span>
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

<style lang="sass">
</style>

<script>
import { mapGetters } from 'vuex';
import moment from 'moment';
import { first,last } from 'lodash';
import { getApiUrl } from 'js/utils/env';

export default {
	name: 'DownloadPlan',
	computed: {
		...mapGetters('course', ['getRequiredLessons']),
		...mapGetters(['currentUserId']),
		sortedRequiredUserLessons() {
			return this.requiredLessons.sort((lessonA, lessonB) => {
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
		async downloadPlan() {
			try {
				const response = await axios.request({
					url: getApiUrl(`user_lesson/${this.currentUserId}/exportPlan`),
					responseType: 'blob',
				});

				this.downloadFile(response.data, 'plan_pracy.csv');
			} catch (err) {
				this.handleDownloadFailure();
			}
		},
		downloadFile(responseData, fileName) {
			const data = window.URL.createObjectURL(responseData);
			const link = document.createElement('a');
			link.style.display = 'none';
			// For Firefox it is necessary to insert the link into body
			document.body.appendChild(link);
			link.href = data;
			link.setAttribute('download', fileName);
			link.click();

			setTimeout(function() {
				window.URL.revokeObjectURL(link.href);
				document.removeChild(link);
			}, 100);
		},
		handleDownloadFailure() {
			if (err.response.status === 404) {
				return this.addAutoDismissableAlert({
					text: 'Nie udało się znaleźć Twojego planu pracy. Spróbuj ponownie, jeśli problem nie ustąpi daj Nam znać :)',
					type: 'error'
				});
			}

			if (err.response.status === 403) {
				return this.addAutoDismissableAlert({
					text: 'Nie masz uprawnień do pobrania planu.',
					type: 'error'
				});
			}

			this.addAutoDismissableAlert({
				text: 'Ups, coś poszło nie tak, spróbuj ponownie.',
				type: 'error'
			});

			$wnl.logger.capture(err);
		},
	}
};
</script>
