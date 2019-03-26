<template>
	<div>
		<h2 class="title">Plan pracy 🗓</h2>
		<p class="title is-5">Lekcje będą się otwierały zgodnie z ustalonymi przez Ciebie datami. Przed datą otwarcia lekcje pozostaną zamknięte.</p>

		<p></p>
		<h3 class="title">Domyślny plan</h3>

		<template v-if="isReturningUser">
			<div>
				<div>
					<span>ℹ️</span>
					<p>Na najbliższą edycję szykujemy nową Chirurgię (na 10 czerwca) i Medycynę ratunkową (na 19 sierpnia). Możesz jednak spokojnie zaplanować naukę!</p>
					<p>Zakres materiału oraz struktura lekcji pozostaną takie same, jak obecnie. Nawet jeżeli zrealizujesz te lekcje przed ich aktualizacją, możesz bez przeszkód kontynuować kurs i mieć pewność pokrycia całości materiału.</p>
				</div>
				<div>
					<p>Wskazówka:</p>
					<p>Plan możesz zawsze zmienić w zakłade KONTO > Plan pracy.</p>
				</div>
			</div>
		</template>
		<template v-else>
			<div>
				<p>Proponowany przez nas plan pracy trwa od <strong>{{defaultPlanStartDate}}</strong>, zakłada pracę <strong>5 dni w tygodniu przez 14 tygodni</strong>.</p>
				<p>
					Możesz zmienić zakres dni, w których chcesz pracować, a my dostosujemy do nich Twój plan pracy –
					<a @click="openEditor">Edytuj plan</a>
				</p>
			</div>
			<div>
				<p>Wskazówka:</p>
				<p>Plan zakłada optymalną kolejność przerabiania przedmiotów. Jeśli chcesz stworzyć indywidualny plan lub go edytować, możesz to zrobić w zakładce KONTO > PLAN PRACY.</p>
			</div>
			<wnl-automatic-plan
				v-if="isEditorVisible"
				:show-annotation="false"
			/>
		</template>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

</style>

<script>
import axios from 'axios';
import moment from 'moment';

import WnlAutomaticPlan from 'js/components/user/plan/AutomaticPlan';

import {getApiUrl} from 'js/utils/env';

export default {
	components: {
		WnlAutomaticPlan,
	},
	data() {
		return {
			defaultPlanStartDate: null,
			isEditorVisible: false,
			isReturningUser: false,
		};
	},
	methods: {
		openEditor() {
			this.isEditorVisible = true;
		}
	},
	async mounted() {
		const {data: {course_start}} = await axios.get(getApiUrl('products/current/paidCourse'));

		this.defaultPlanStartDate = moment(course_start * 1000).format('LL');
	},
};
</script>
