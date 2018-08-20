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
        <div class="id-number">
            <div class="id-number__has-personal-id" v-if="idNumberAvilable">
                <div class="id-number__has-personal-identity_number">
                    Podany przez Ciebie numer {{ idType }} to: {{ idNumber }}
                    Jeśli chcesz dokonać zmiany, napisz na info@bethink.pl.
                </div>
            </div>
            <div class="id-number__no-personal-id" v-else>
                <div class="id-number__personal-identity-number-input">
                    <input
                        name="personal_identity_number"
                        class="input"
                        type="text"
                        placeholder="Numer identyfikacyjny"
                        v-model="identity.personalIdentityNumber"
                    />
                </div>
                <div class="id-number__errors" v-if="errors.length">
                    <ul v-for="error in errors">
                        {{ error }}
                    </ul>
                </div>
                <div
                    class="id-number__personal-identity-number-input__change"
                    @click="changeIdentityType"
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
                                name="identity_type"
                                value="personal_identity_number"
                                v-model="identity.identityType">
                            <label for="personal_identity_number">PESEL</label>
                            <input
                                @click="selectRadio"
                                class="is-checkradio"
                                type="radio"
                                id="identity_card"
                                name="identity_type"
                                value="identity_card"
                                v-model="identity.identityType">
                            <label for="identity_card">Dowód osobisty</label>
                            <input
                                @click="selectRadio"
                                class="is-checkradio"
                                type="radio"
                                id="passport"
                                name="identity_type"
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
            }
        },
        computed: {
			...mapGetters(['currentUserIdentity', 'currentUserId']),
            idNumberAvilable() {
                return Boolean(this.currentUserIdentity.personalIdentityNumber)
            },
			idNumber() {
				return this.currentUserIdentity.personalIdentityNumber
			},
			idType() {
                let idType = this.currentUserIdentity.identityType
				if (idType === 'personal_identity_number') {
                    return 'PESEL'
                } else if (idType === 'identity_card') {
                    return 'dowodu osobistego'
                } else if (idType === 'passport') {
                    return 'paszportu'
                }
			},
            hasNoChanges() {
                return this.identity.personalIdentityNumber === ''
            },
            validateIdNumber() {
                return true
                let idNumber = this.identity.personalIdentityNumber
                if (this.identity.identityType === 'personal_identity_number') {
                    let reg = /^[0-9]{11}$/
                    if (reg.test(idNumber) == false) {
                        this.errors.push('PESEL powinien składać się tylko z 11 cyfr.')
                        return false
                    } else {
                        let weight = [1,3,7,9,1,3,7,9,1,3,1]
                        let sum = 0

                        for (var i = 0; i < weight.length; i++) {
                            sum += (parseInt(idNumber.substring(i, i+1), 10)*weight[i])
                        }

                        if (sum % 10 === 0) {
                            return true
                        } else {
                            this.errors.push('PESEL jest niepoprawny.')
                            return false
                        }
                    }
                } else {
                    if (idNumber.length != 9) {
                        this.errors.push('Numer powinien być złożony z dziewięciu znaków.')
                        return false
                    }

                    idNumber = idNumber.toUpperCase()
                    let letterValues = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'

                    function getLetterValue(letter){
                        return letterValues.indexOf(letter)
                    }

                    if (this.identity.identityType === 'identity_card') {
                        for (i = 0; i < 3; i++) {
                            if (getLetterValue(idNumber[i]) < 10 || idNumber[i] == 'O' || idNumber == 'Q') {
                                this.errors.push('Seria podanego numeru jest niepoprawna.')
                                return false
                            }
                        }

                        for (i = 3; i < 9; i++) {
                            if (getLetterValue(idNumber[i]) < 0 || getLetterValue(idNumber[i]) > 9) {
                                this.errors.push('Numer podanego identyfikatora jest niepoprawny.')
                                return false
                            }
                        }
                        let sum = 7 * getLetterValue(idNumber[0]) +
                            3 * getLetterValue(idNumber[1]) +
                            1 * getLetterValue(idNumber[2]) +
                            7 * getLetterValue(idNumber[4]) +
                            3 * getLetterValue(idNumber[5]) +
                            1 * getLetterValue(idNumber[6]) +
                            7 * getLetterValue(idNumber[7]) +
                            3 * getLetterValue(idNumber[8])

                        sum %= 10

                        if (sum != getLetterValue(idNumber[3])) {
                            this.errors.push('Numer jest nieprawidłowy')
                            return false;
                        }

                        return true

                    } else {
                        for (i = 0; i < 2; i++) {
                            if (getLetterValue(idNumber[i]) < 10 || idNumber[i] == 'O' || idNumber == 'Q') {
                                this.errors.push('Seria podanego numeru jest niepoprawna.')
                                return false
                            }
                        }

                        for (i = 2; i < 9; i++) {
                            if (getLetterValue(idNumber[i]) < 0 || getLetterValue(idNumber[i]) > 9) {
                                this.errors.push('Numer podanego identyfikatora jest niepoprawny.')
                                return false
                            }
                        }

                        let sum = 7 * getLetterValue(idNumber[0]) +
                            3 * getLetterValue(idNumber[1]) +
                            1 * getLetterValue(idNumber[3]) +
                            7 * getLetterValue(idNumber[4]) +
                            3 * getLetterValue(idNumber[5]) +
                            1 * getLetterValue(idNumber[6]) +
                            7 * getLetterValue(idNumber[7]) +
                            3 * getLetterValue(idNumber[8])

                        sum %= 10

                        if (sum != getLetterValue(idNumber[2])) {
                            this.errors.push('Numer jest nieprawidłowy')
                            return false;
                        }

                        return true
                    }
                }
            }
		},
        methods: {
            ...mapActions(['addAutoDismissableAlert', 'setUserIdentity']),
            async onSubmit() {
                if (this.validateIdNumber) {
                    this.errors = []
                    try {
                        await axios.post(getApiUrl(`users/${this.currentUserId}/identity`), {
                            personal_identity_number: this.identity.personalIdentityNumber,
                            identity_type: this.identity.identityType
                        })
                        this.addAutoDismissableAlert(this.alertSuccess)
                        await this.setUserIdentity(this.identity)
                    }
                    catch (error) {
                        $wnl.logger.capture(error)
                        this.addAutoDismissableAlert(this.alertError)
                    }
                }
            },
            changeIdentityType() {
                return this.otherIdentity = true
            },
            selectRadio() {
                this.errors = []
            }
        }
    }
</script>
