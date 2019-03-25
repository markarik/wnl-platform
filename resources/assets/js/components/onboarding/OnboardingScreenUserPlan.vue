<template>
	<div>
		<h2>Plan pracy</h2>
		<p>Lekcje będą się otwierały zgodnie z ustalonymi przez Ciebie datami. Przed datą otwarcia lekcje pozostaną zamknięte.</p>
		<h3>Domyślny plan</h3>
		<div>
			<p>Proponowany przez nas plan pracy trwa od {{defaultPlanStartDate}}, zakłada pracę 5 dni w tygodniu przez 14 tygodni.</p>
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
