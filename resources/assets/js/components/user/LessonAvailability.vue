<template>
	<wnl-datepicker v-model="dupa" :config="startDateConfig" @onChange="onStartDateChange"/>
</template>

<style lang="sass">
</style>

<script>
	import Datepicker from 'js/components/global/Datepicker'
	import { pl } from 'flatpickr/dist/l10n/pl.js'
	import { isEmpty } from 'lodash'

export default {
	name: 'LessonAvailability',
	components: {
		'wnl-datepicker': Datepicker,
	},
	data() {
		return {
			date: '',
			dupa: new Date(),
		}
	},
	props: {
		startDate: {
			required: true,
			type: String|Date,
			default: new Date(),
		}
	},
	computed: {
		startDateConfig() {
			return {
				altInput: true,
				disableMobile: true,
				locale: pl,
				minDate: 'today',
			}
		},
	},
	methods: {
		onStartDateChange(payload) {
			if (isEmpty(payload)) this.date = null
		},
	},
	mounted() {
		this.date = this.startDate
	}
}
</script>
