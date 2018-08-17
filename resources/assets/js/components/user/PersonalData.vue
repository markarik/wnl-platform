<template lang="html">
	<div class="scrollable-main-container">
		<div class="message is-primary">
			<div class="message-header">
				Twoje wrażliwe dane
			</div>
			<div class="message-body">
				Informacje, które tu zostawisz będą wykorzystane przez nas do obsługi Twojego zamówienia. Dane nie będą widoczne publicznie. Więcej informacji na ten temat znajdziesz tutaj: link do polityki prywatności
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
		<wnl-identity-number></wnl-identity-number>
	</div>
</template>

<style lang="sass">

</style>

<script>
	import { Form, Text } from 'js/components/global/form'
	import IdentityNumber from 'js/components/user/IdentityNumber'

	export default {
		components: {
			'wnl-form': Form,
			'wnl-form-text': Text,
			'wnl-identity-number': IdentityNumber,
		}
	}
</script>
