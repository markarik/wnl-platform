<template>
	<div>
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					Metody płatności
				</div>
			</div>
		</div>
	</div>
</template>

<style lang="sass" scoped>
	.wnl-table__cell--datepicker
		width: 240px
</style>

<script>
import {mapActions} from 'vuex';

import {getApiUrl} from 'js/utils/env';
import { swalConfig } from 'js/utils/swal';

export default {
	props: {
		id: {
			type: [String, Number],
			required: true
		},
	},
	data() {
		return {
			loading: false,
			methods: [],
		};
	},
	computed: {

	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),

	},
	async mounted() {

	}
};
</script>
