<template lang="html">
	<div class="scrollable-main-container">
		<div class="message is-primary">
			<div class="message-header">
				Twoje wrażliwe dane
			</div>
			<div class="message-body">
				copy
			</div>
		</div>
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					Adres
				</div>
			</div>
		</div>
		<wnl-form
			class="margin vertical"
			method="put"
			name="MyAddress"
			resourceRoute="users/current/address"
			populate="true"
		>
			<wnl-form-text name="recipient">Osoba odbierająca przesyłkę</wnl-form-text>
			<wnl-form-text name="street">Ulica</wnl-form-text>
			<wnl-form-text name="zip">Kod pocztowy</wnl-form-text>
			<wnl-form-text name="city">Miasto</wnl-form-text>
			<wnl-form-text name="phone">Telefon</wnl-form-text>
		</wnl-form>
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					Numer PESEL
				</div>
			</div>
		</div>
		<div class="identity-number">
			<div class="identity-number__has-personal-id" v-if="hasIdentity">
				<div class="identity-number__has-personal-identity_number"
					v-if="personalIdentityNumber">
					Podany przez Ciebie numer PESEL to: {{personalIdentityNumber}}
				</div>
				<div class="identity-number__has-optional_identity" v-else>
					Podane przez Ciebie dane identyfikacyjne to: {{}}
				</div>
				Jeśli chcesz dokonać zmiany, napisz na info@bethink.pl.
			</div>
			<div class="identity-number__no-personal-id" v-else>
				<wnl-form
					class="margin vertical"
					method="post"
					name="MyIdentity"
					resourceRoute="users/current/identity"
				>
					<wnl-form-text name="personal_identity_number">Numer PESEL</wnl-form-text>
				</wnl-form>
			</div>
		</div>
	</div>
</template>

<style lang="sass">

</style>

<script>
	import { Form, Text, Submit } from 'js/components/global/form'
	import { mapGetters } from 'vuex'
	import { getApiUrl } from 'js/utils/env'

	export default {
		components: {
			'wnl-form': Form,
			'wnl-form-text': Text,
			'wnl-submit': Submit,
		},
		data() {
			return {
			}
		},
		computed: {
			...mapGetters(['currentUserIdentity', 'currentUserId']),
			hasIdentity() {
				return Boolean(this.currentUserIdentity.optional_identity || this.currentUserIdentity.personal_identity_number)
			},
			personalIdentityNumber() {
				return this.currentUserIdentity.personal_identity_number
			}
		},
		methods: {
		}
	}
</script>
