<template>
	<div class="scrollable-main-container">
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					Ustawienia
				</div>
			</div>
		</div>

		<wnl-form
			class="margin vertical"
			name="Settings"
			method="put"
			resource-route="users/current/settings"
			populate="true"
			hide-default-submit="true"
			@submitSuccess="onSubmitSuccess"
		>
			<template slot-scope="slotParams">
				<!-- <wnl-form-check name="newsletter">Otrzymuj newsletter</wnl-form-check> -->
				<wnl-form-check
					:name="USER_SETTING_NAMES.CHAT_ON"
					@input="slotParams.onSubmit"
				>Chat włączony</wnl-form-check>
				<wnl-form-check
					:name="USER_SETTING_NAMES.SKIP_FUNCTIONAL_SLIDES"
					@input="slotParams.onSubmit"
				>Pomijaj slajdy funkcjonalne</wnl-form-check>
				<wnl-form-check
					:name="USER_SETTING_NAMES.NOTIFY_LIVE"
					@input="slotParams.onSubmit"
				>Powiadomienia w przeglądarce</wnl-form-check>
				<wnl-form-check
					:name="USER_SETTING_NAMES.THICK_SCROLLBAR"
					@input="slotParams.onSubmit"
				>Pogrubiony pasek przewijania</wnl-form-check>

				<wnl-form-select
					v-if="isAdmin"
					:name="USER_SETTING_NAMES.DEFAULT_TAXONOMY_ID"
					:options="taxonomiesSelectOptions"
					@input="slotParams.onSubmit"
				>Ulubiona taksonomia</wnl-form-select>
			</template>
		</wnl-form>


	</div>
</template>

<script>
import { mapGetters, mapActions, mapState } from 'vuex';

import { Form as WnlForm, Check as WnlFormCheck, Select as WnlFormSelect } from 'js/components/global/form';
import { USER_SETTING_NAMES } from 'js/consts/settings';

export default {
	components: {
		WnlForm,
		WnlFormCheck,
		WnlFormSelect,
	},
	data() {
		return {
			USER_SETTING_NAMES
		};
	},
	computed: {
		...mapGetters(['getAllSettings']),
		...mapGetters(['isAdmin']),
		...mapState('taxonomies', ['taxonomies']),
		taxonomiesSelectOptions() {
			return this.taxonomies.map(taxonomy => ({ value: taxonomy.id, text: taxonomy.name }));
		}
	},
	mounted() {
		if (this.isAdmin) {
			this.fetchTaxonomies();
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
					this.changeUserSetting({ setting, value });
				}
			});
		},
	},
};
</script>
