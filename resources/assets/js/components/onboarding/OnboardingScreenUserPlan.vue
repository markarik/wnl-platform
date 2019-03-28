<template>
	<div>
		<h2 class="title has-text-centered">Plan pracy 🗓</h2>
		<p class="title is-5 has-text-centered">
			Lekcje będą się otwierały zgodnie z ustalonymi przez Ciebie datami.<br>
			Do tego czasu lekcje pozostaną zamknięte.
		</p>

		<wnl-text-loader v-if="isLoading" />

		<template v-else>
			<div class="margin-top-huge" v-if="isReturningUser">
				<img :src="imageUrl" alt="" class="onboarding-plan-image">
				<div>
					<p class="margin bottom">Na najbliższą edycję szykujemy nową Chirurgię (na 10 czerwca) i Medycynę ratunkową (na 19 sierpnia). Możesz jednak spokojnie zaplanować naukę!</p>
					<p class="margin bottom">Zakres materiału oraz struktura lekcji pozostaną takie same, jak obecnie. Nawet jeżeli zrealizujesz te lekcje przed ich aktualizacją, możesz bez przeszkód kontynuować kurs i mieć pewność pokrycia całości materiału 🙂</p>
					<p class="text-dimmed">💡 Plan możesz zawsze zmienić w zakładce KONTO > Plan pracy.</p>
				</div>
			</div>

			<div class="margin-top-huge" v-else>
				<p class="text-dimmed margin bottom">💡 Plan zakłada optymalną kolejność przerabiania przedmiotów. Jeśli chcesz stworzyć indywidualny plan lub go edytować, będziesz mieć taką możliwość w zakładce KONTO > Plan pracy.</p>
				<div class="margin-top-huge">
					<img :src="imageUrl" alt="" class="onboarding-plan-image">
					<h3 class="title is-4 onboarding-plan-header">Domyślny plan</h3>
					<div>
						<p class="margin bottom">Proponowany przez nas plan pracy trwa od <strong>{{defaultPlanStartDate}}</strong>, zakłada pracę <strong>5&nbsp;dni w tygodniu przez 14 tygodni</strong> 🗓</p>
						<p>
							Możesz zmienić zakres dni, w których chcesz pracować, a my dostosujemy do nich Twój plan pracy 👉
							<a class="clickable" @click="openEditor">Edytuj plan</a>
						</p>
					</div>
				</div>
 				<wnl-automatic-plan
					class="margin-top-huge"
					v-if="isEditorVisible"
					:show-annotation="false"
				/>
			</div>
		</template>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.onboarding-plan-header
		text-align: center

		@media #{$media-query-tablet}
			text-align: left

	.onboarding-plan-image
		display: block
		margin: 0 auto
		max-width: 168px

		@media #{$media-query-tablet}
			float: left
			margin: 0 $margin-huge 0 0

</style>

<script>
import axios from 'axios';
import moment from 'moment';
import {mapActions} from 'vuex';

import WnlAutomaticPlan from 'js/components/user/plan/AutomaticPlan';

import {getApiUrl} from 'js/utils/env';
import {getImageUrl} from 'js/utils/env';
import {ALERT_TYPES} from 'js/consts/alert';

export default {
	components: {
		WnlAutomaticPlan,
	},
	data() {
		return {
			defaultPlanStartDate: null,
			isEditorVisible: false,
			isLoading: true,
			isReturningUser: false,
			imageUrl: getImageUrl('onboarding-screen-plan.png'),
		};
	},
	methods: {
		...mapActions([
			'addAutoDismissableAlert',
		]),
		openEditor() {
			this.isEditorVisible = true;
		}
	},
	async mounted() {
		try {
			const [{data: {course_start: courseStart}}, {data: {id, included}}] = await Promise.all([
				axios.get(getApiUrl('products/current/paidCourse')),
				axios.get(getApiUrl('users/current?include=has_prolonged_course')),
			]);
			this.isReturningUser = included.has_prolonged_courses[id].has_prolonged_course;
			this.defaultPlanStartDate = moment(courseStart * 1000).format('LL');
		} catch (error) {
			$wnl.logger.error(error);
			this.addAutoDismissableAlert({
				text: 'Coś poszło nie tak :(',
				type: ALERT_TYPES.ERROR,
			});
		} finally {
			this.isLoading = false;
		}
	},
};
</script>
