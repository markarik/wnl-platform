<template>
    <div>
        <div class="level wnl-screen-title">
            <div class="level-left">
                <div class="level-item big strong">
                    {{ $t('user.personalData.identityNumber.header') }}
                </div>
            </div>
        </div>
        <div class="message is-primary">
			<div class="message-header">
				{{ $t('user.personalData.identityNumber.header') }}
			</div>
			<div class="message-body">
				{{ $t('user.personalData.identityNumber.explanation') }}
			</div>
		</div>
        <div class="id-number" v-if="isLoaded">
            <div class="id-number__has-personal-id" v-if="idNumberAvailable">
                <div class="id-number__has-personal-identity_number">
                    Podany przez Ciebie numer {{ idType }} to: {{ idNumber }}
                    Jeśli chcesz dokonać zmiany, napisz na info@bethink.pl.
                </div>
            </div>
            <div class="id-number__no-personal-id" v-else>
                <div class="id-number__personal-identity-number-input">
                    <input
                        :name="this.identityTypes.personalIdNumber"
                        class="input"
                        type="text"
                        placeholder="Numer identyfikacyjny"
                        v-model="identity.personalIdentityNumber"
                    />
                </div>
                <div class="id-number__errors" v-if="errors.length">
                    <ul v-for="(error, index) in errors" :key="index">
                        <li>
                            {{ error }}
                        </li>
                    </ul>
                </div>
                <div
                    class="id-number__personal-identity-number-input__change"
                    @click="otherIdentity=true"
                    v-if="!otherIdentity">
                    Nie chcę podawać numeru PESEL
                </div>
                <div
                    class="id-number__other-identitification"
                    v-if="otherIdentity">
                    <div class="id_number__radio field">
                            <input
                                @click="selectRadio"
                                class="is-checkradio"
                                type="radio"
                                id="personal_identity_number"
                                :name="this.identityTypes.personalIdNumber"
                                value="personal_identity_number"
                                v-model="identity.identityType">
                            <label for="personal_identity_number">PESEL</label>
                            <input
                                @click="selectRadio"
                                class="is-checkradio"
                                type="radio"
                                id="identity_card"
                                :name="this.identityTypes.idCard"
                                value="identity_card"
                                v-model="identity.identityType">
                            <label :for="this.identityTypes.idCard">Dowód osobisty</label>
                            <input
                                @click="selectRadio"
                                class="is-checkradio"
                                type="radio"
                                id="passport"
                                :name="this.identityTypes.passport"
                                value="passport"
                                v-model="identity.identityType">
                            <label for="passport">Paszport</label>
                    </div>
                </div>
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

    .id-number__personal-identity-number-input
        margin-bottom: $margin-base

    .id-number__personal-identity-number-input__change
        margin-bottom: $margin-small
        cursor: pointer
        text-decoration: underline

    .id-number__other-identitification
        margin-bottom: $margin-small

    .id-number__errors
        color: $color-red
        margin-bottom: $margin-small

</style>

<script>
    import { mapGetters, mapActions } from 'vuex'
    import { getApiUrl } from 'js/utils/env'

    const ID_CARD_CONTROL_NUMBER = 3
    const PASSPORT_CONTROL_NUMBER = 2

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
                otherIdentity: false,
                errors: [],
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
                    personalIdNumber: 'personal_identity_number',
                    idCard: 'identity_card',
                    passport: 'passport'
                }
            }
        },
        computed: {
            ...mapGetters(['currentUserIdentity', 'currentUserId']),
            idNumberAvailable() {
                return Boolean(this.currentUserIdentity.personalIdentityNumber)
            },
            idNumber() {
                return this.currentUserIdentity.personalIdentityNumber
            },
			idType() {
                const idType = this.currentUserIdentity.identityType
				if (idType === this.identityTypes.personalIdNumber) {
                    return 'PESEL'
                } else if (idType === this.identityTypes.idCard) {
                    return 'dowodu osobistego'
                } else if (idType === this.identityTypes.passport) {
                    return 'paszportu'
                }
			},
            hasNoChanges() {
                return this.identity.personalIdentityNumber === ''
            },
            validateIdNumber() {
                const idNumber = this.identity.personalIdentityNumber
                const idType = this.identity.identityType

                if (idType === this.identityTypes.personalIdNumber) {
                    return this.validatePersonalIdNumber(idNumber)
                } else {
                    if (idNumber.length !== 9) {
                        this.errors.push('Numer powinien być złożony z dziewięciu znaków.')
                        return false
                    }

                    if (
                        idType === this.identityTypes.idCard ||
                        idType === this.identityTypes.passport
                    ) {
                        return this.validateIdCardAndPassportNumbers(idNumber)
                    }
                }
            }
		},
        methods: {
            ...mapActions(['addAutoDismissableAlert', 'setUserIdentity', 'fetchUserPersonalData']),
            async onSubmit() {
                if (this.validateIdNumber) {
                    this.errors = []
                    try {
                        await axios.post(getApiUrl(`users/${this.currentUserId}/personal_data`), {
                            personal_identity_number: this.identity.personalIdentityNumber,
                            identity_type: this.identity.identityType
                        })
                        this.addAutoDismissableAlert(this.alertSuccess)
                        this.setUserIdentity(this.identity)
                    }
                    catch (error) {
                        this.errors.push(Object.values(error.response.data.errors).toString())
                        $wnl.logger.capture(error)
                        this.addAutoDismissableAlert(this.alertError)
                    }
                }
            },
            selectRadio() {
                this.errors = []
            },
            validatePersonalIdNumber(idNumber) {
                const reg = /^[0-9]{11}$/
                if (reg.test(idNumber) === false) {
                    this.errors.push('PESEL powinien składać się tylko z 11 cyfr.')
                    return false
                } else {
                    const weight = [1, 3, 7, 9, 1, 3, 7, 9, 1, 3, 1]
                    let sum = 0

                    for (let i = 0; i < weight.length; i++) {
                        sum += (Number(idNumber[i]) * weight[i])
                    }

                    if (sum % 10 === 0) {
                        return true
                    } else {
                        this.errors.push('PESEL jest niepoprawny.')
                        return false
                    }
                }
            },
            validateIdCardAndPassportNumbers(idNumber) {
                const idType = this.identity.identityType
                let controlNumber = 0

                if (idType === this.identityTypes.idCard) {
                    controlNumber = ID_CARD_CONTROL_NUMBER
                } else if (idType === this.identityTypes.passport) {
                    controlNumber = PASSPORT_CONTROL_NUMBER
                }

                idNumber = idNumber.toUpperCase()

                for (let i = 0; i < controlNumber; i++) {
                    if (
                        this.getLetterValue(idNumber[i]) < 10
                        || idNumber[i] === 'O'
                        || idNumber === 'Q'
                    ) {
                        this.errors.push('Seria podanego numeru jest niepoprawna.')
                        return false
                    }
                }

                for (let i = controlNumber; i < idNumber.length; i++) {
                    if (
                        this.getLetterValue(idNumber[i]) < 0
                        || this.getLetterValue(idNumber[i]) > 9
                    ) {
                        this.errors.push('Numer podanego identyfikatora jest niepoprawny.')
                        return false
                    }
                }

                const weight = [7, 3, 1, 7, 3, 1, 7, 3, 1]
                weight[controlNumber] = 0

                let sum = 0

                for (let i = 0; i < weight.length; i++) {
                    sum += weight[i] * this.getLetterValue(idNumber[i])
                }

                sum %= 10

                if (sum !== this.getLetterValue(idNumber[controlNumber])) {
                    this.errors.push('Numer jest nieprawidłowy')
                    return false
                }
            },
            getLetterValue(letter) {
                const letterValues = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'
                return letterValues.indexOf(letter)
            },
        },
        mounted() {
            this.fetchUserPersonalData()
            .then(() => {
                this.isLoaded = true
            }).catch((e) => {
                this.isLoaded = true
            })
        }
    }
</script>
