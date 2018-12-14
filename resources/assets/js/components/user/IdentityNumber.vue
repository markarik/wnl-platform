<template>
	<div>
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					{{ $t('user.personalData.identityNumber.header') }}
				</div>
			</div>
		</div>
		<div class="id-number" v-if="isLoaded">
			<!-- HEADER -->
			<div class="id-number--has-personal-id" v-if="idNumberAvailable">
				{{ $t('user.personalData.identityNumber.yourNumber', {number: idNumber}) }}
				{{ $t('user.personalData.identityNumber.yourNumberChange') }}
			</div>

			<!-- INPUT -->
			<div class="id-number--no-personal-id" v-else>
				<div class="message is-primary">
					<div class="message-header">
						{{ $t('user.personalData.identityNumber.header') }}
					</div>
					<div class="message-body" v-html="$t('user.personalData.identityNumber.explanation')"></div>
				</div>
				<div class="id-number__personal-identity-number-input">
					<input
						:name="this.identityTypes.personalId"
						class="input"
						type="text"
						placeholder="Numer identyfikacyjny"
						v-model="identity.personalIdentityNumber"
						@keyup.enter="onSubmit"
					/>
				</div>

				<!-- ERROR MESSAGES -->
				<div class="id-number__errors" v-if="errors.length">
					<ul v-for="(error, index) in activeErrors" :key="index">
						<li>
							{{ $t(`user.personalData.errors.${error.errorCode}`) }}
						</li>
					</ul>
				</div>

				<!-- CHANGE IDENTITY NUMBER TYPE -->
				<div class="id-number-other-container">
					<div
						class="id-number__personal-identity-number-input__change"
						@click="otherIdentity=true"
						v-if="!otherIdentity">
						{{ $t('user.personalData.identityNumber.changeNumberType') }}
					</div>
					<div
						class="id-number--other-identitification"
						v-if="otherIdentity">
						<div class="id_number__radio field">
								<input
									@click="disableErrors"
									class="is-checkradio"
									type="radio"
									id="personal_identity_number"
									:name="this.identityTypes.personalId"
									value="personal_identity_number"
									v-model="identity.identityType">
								<label for="personal_identity_number">{{ $t('user.personalData.identityNumber.types.personal') }}</label>
								<input
									@click="disableErrors"
									class="is-checkradio"
									type="radio"
									id="passport"
									:name="this.identityTypes.passport"
									value="passport_number"
									v-model="identity.identityType">
								<label for="passport">{{ $t('user.personalData.identityNumber.types.passport') }}</label>
						</div>
					</div>
				</div>

				<!-- SUBMIT BUTTON -->
				<div class="level-item">
					<a
						class="button is-primary is-wide"
						@click="onSubmit"
						:disabled="hasNoChanges"
					>Zapisz</a>
				</div>
			</div>
		</div>
	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.id-number-other-container
		margin: $margin-big 0

	.id-number__personal-identity-number-input
		margin-bottom: $margin-base

	.id-number__personal-identity-number-input__change
		cursor: pointer
		text-align: center
		text-decoration: underline

	.id-number--other-identitification
		margin-bottom: $margin-small

	.id-number__errors
		color: $color-red
		margin-bottom: $margin-small

	.id_number__radio
		align-items: center
		display: flex
		justify-content: center

</style>

<script>
import { mapGetters, mapActions } from 'vuex';
import { getApiUrl } from 'js/utils/env';
import { get, isEmpty } from 'lodash';

export default {
	name: 'IdentityNumber',
	components: {
		'wnl-form-text': Text,
	},
	data() {
		return {
			identity: {
				personalIdentityNumber: '',
				identityType: 'personal_identity_number'
			},
			controlNumbers: {
				passport: 2
			},
			otherIdentity: false,
			errors: [
				{
					errorCode: 'incorrectIdStructure',
					active: false
				},
				{
					errorCode: 'incorrectIdNumber',
					active: false
				},
				{
					errorCode: 'incorrectNumberLength',
					active: false
				},
				{
					errorCode: 'incorrectNumberSeries',
					active: false
				},
				{
					errorCode: 'incorrectSerialNumber',
					active: false
				},
				{
					errorCode: 'incorrectNumber',
					active: false
				}
			],
			alertSuccess: {
				text: 'Udało się! :)',
				type: 'success',
			},
			alertError: {
				text: 'Ups, coś poszło nie tak :(',
				type: 'error',
			},
			isLoaded: false,
			identityTypes: {
				personalId: 'personal_identity_number',
				passport: 'passport_number'
			}
		};
	},
	computed: {
		...mapGetters(['currentUserIdentity', 'currentUserId']),
		idNumberAvailable() {
			return Boolean (
				this.currentUserIdentity.personalIdentityNumber ||
					this.currentUserIdentity.passportNumber
			);
		},
		idNumber() {
			return (
				this.currentUserIdentity.personalIdentityNumber ||
					this.currentUserIdentity.passportNumber
			);
		},
		hasNoChanges() {
			return this.identity.personalIdentityNumber === '';
		},
		activeErrors() {
			return this.errors.filter((error) => {
				return error.active;
			});
		}
	},
	methods: {
		...mapActions(['addAutoDismissableAlert', 'setUserIdentity', 'fetchUserPersonalData']),
		async onSubmit(event) {
			event.preventDefault();
			if (this.validateIdNumber()) {
				let query = {};
				query[this.identity.identityType] = this.identity.personalIdentityNumber;
				this.disableErrors();
				try {
					await axios.post(getApiUrl(`users/${this.currentUserId}/personal_data`), query);
					this.addAutoDismissableAlert(this.alertSuccess);
					this.setUserIdentity(this.identity);
				}
				catch (error) {
					this.errors = _.get(error, 'response.data.errors.personal_identity_number');
					if (isEmpty(this.errors)) {
						$wnl.logger.capture(error);
						this.addAutoDismissableAlert(this.alertError);
					}
				}
			}
		},
		validateIdNumber() {
			const idNumber = this.identity.personalIdentityNumber;
			const idType = this.identity.identityType;

			if (idType === this.identityTypes.personalId) {
				return this.validatePersonalIdNumber(idNumber);
			} else {
				if (
					idType === this.identityTypes.passport
				) {
					return this.validatePassportNumber(idNumber);
				}
			}
		},
		disableErrors() {
			this.errors.forEach((error) => {
				error.active = false;
			});
		},
		setErrorStatus(error) {
			this.errors.find((e) => {
				return e.errorCode === error;
			}).active = true;
		},
		validatePersonalIdNumber(idNumber) {
			const reg = /^[0-9]{11}$/;
			if (reg.test(idNumber) === false) {
				this.setErrorStatus('incorrectIdStructure');
				return false;
			} else {
				const weight = [1, 3, 7, 9, 1, 3, 7, 9, 1, 3, 1];
				let sum = 0;

				for (let i = 0; i < weight.length; i++) {
					sum += (Number(idNumber[i]) * weight[i]);
				}

				if (sum % 10 !== 0) {
					this.setErrorStatus('incorrectIdNumber');
					return false;
				}
			}
			return true;
		},
		validatePassportNumber(passportNumber) {
			let controlNumber = this.controlNumbers.passport;

			if (passportNumber.length !== 9) {
				this.setErrorStatus('incorrectNumberLength');
				return false;
			}

			passportNumber = passportNumber.toUpperCase();

			for (let i = 0; i < controlNumber; i++) {
				if (
					this.getLetterValue(passportNumber[i]) < 10
						|| passportNumber[i] === 'O'
						|| passportNumber === 'Q'
				) {
					this.setErrorStatus('incorrectNumberSeries');
					return false;
				}
			}

			for (let i = controlNumber; i < passportNumber.length; i++) {
				if (
					this.getLetterValue(passportNumber[i]) < 0
						|| this.getLetterValue(passportNumber[i]) > 9
				) {
					this.setErrorStatus('incorrectSerialNumber');
					return false;
				}
			}

			const weight = [7, 3, 1, 7, 3, 1, 7, 3, 1];
			weight.splice(controlNumber, 0, 0);
			weight.pop();

			let sum = 0;

			for (let i = 0; i < weight.length; i++) {
				sum += weight[i] * this.getLetterValue(passportNumber[i]);
			}

			sum %= 10;

			if (sum !== this.getLetterValue(passportNumber[controlNumber])) {
				this.setErrorStatus('incorrectNumber');
				return false;
			}
			return true;
		},
		getLetterValue(letter) {
			const letterValues = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			return letterValues.indexOf(letter);
		},
	},
	async mounted() {
		await this.fetchUserPersonalData();
		this.isLoaded = true;
	}
};
</script>
