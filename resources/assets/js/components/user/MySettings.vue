<template lang="html">
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
			<!-- <wnl-form-check name="newsletter">Otrzymuj newsletter</wnl-form-check> -->
			<wnl-form-check name="chat_on">Chat włączony</wnl-form-check>
			<wnl-form-check name="skip_functional_slides">Pomijaj slajdy funkcjonalne</wnl-form-check>
			<wnl-form-check name="notify_live">Powiadomienia w przeglądarce</wnl-form-check>
			<wnl-form-check name="thick_scrollbar">Pogrubiony pasek przewijania</wnl-form-check>
		</wnl-form>

	</div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';

import { Form, Check, Text } from 'js/components/global/form';
import Toggler from 'js/components/global/Toggler';

export default {
	components: {
		'wnl-form': Form,
		'wnl-form-check': Check,
		'wnl-toggler': Toggler,
		'wnl-form-text': Text,
	},
	computed: {
		...mapGetters(['getAllSettings']),
	},
	methods: {
		...mapActions(['changeUserSetting']),
		onSubmitSuccess(response, newData) {
			Object.keys(newData).forEach(setting => {
				let value = newData[setting];
				if (newData[setting] !== this.getAllSettings[setting]) {
					this.changeUserSetting({setting, value});
				}
			});
		},
	}
};
</script>
