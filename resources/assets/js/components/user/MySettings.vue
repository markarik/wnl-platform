<template>
	<div class="scrollable-main-container">
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					Ustawienia
				</div>
			</div>
		</div>

		<wnl-form class="margin vertical"
			name="Settings"
			method="put"
			resourceRoute="users/current/settings"
			populate="true"
			hideDefaultSubmit="true"
			@submitSuccess="onSubmitSuccess">
			<template slot-scope="slotParams">
				<!-- <wnl-form-check name="newsletter">Otrzymuj newsletter</wnl-form-check> -->
				<wnl-form-check
					name="chat_on"
					@input="slotParams.onSubmit"
				>Chat włączony</wnl-form-check>
				<wnl-form-check
					name="skip_functional_slides"
					@input="slotParams.onSubmit"
				>Pomijaj slajdy funkcjonalne</wnl-form-check>
				<wnl-form-check
					name="notify_live"
					@input="slotParams.onSubmit"
				>Powiadomienia w przeglądarce</wnl-form-check>
				<wnl-form-check
					name="thick_scrollbar"
					@input="slotParams.onSubmit"
				>Pogrubiony pasek przewijania</wnl-form-check>

				<wnl-form-select
					v-if="isAdmin"
					name="default_taxonomy_id"
					:options="taxonomiesSelectOptions"
					@input="slotParams.onSubmit"
				>Ulubiona taksonomia</wnl-form-select>
			</template>
		</wnl-form>


	</div>
</template>

<script>
import { mapGetters, mapActions, mapState } from 'vuex';

import { Form, Check, Select as WnlFormSelect } from 'js/components/global/form';

export default {
	components: {
		'wnl-form': Form,
		'wnl-form-check': Check,
		WnlFormSelect,
	},
	computed: {
		...mapGetters(['getAllSettings']),
		...mapGetters(['isAdmin']),
		...mapState('taxonomies', ['taxonomies']),
		taxonomiesSelectOptions() {
			return this.taxonomies.map(taxonomy => ({value: taxonomy.id, text: taxonomy.name}));
		}
	},
	methods: {
		...mapActions(['changeUserSetting']),
		...mapActions('taxonomies', {
			fetchTaxonomies: 'fetchAll',
		}),
		onSubmitSuccess(response, newData) {
			Object.keys(newData).forEach(setting => {
				let value = newData[setting];
				if (newData[setting] !== this.getAllSettings[setting]) {
					this.changeUserSetting({setting, value});
				}
			});
		},
	},
	mounted() {
		this.fetchTaxonomies();
	},
};
</script>
