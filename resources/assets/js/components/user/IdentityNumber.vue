<template>
    <div>
        <div class="level wnl-screen-title">
            <div class="level-left">
                <div class="level-item big strong">
                    Numer identyfikacyjny
                </div>
            </div>
        </div>
        <div class="message is-primary">
			<div class="message-header">
				Numer identyfikacyjny
			</div>
			<div class="message-body">
				W związku z podjęciem współpracy z CEM, prosimy Cię o uzupełnienie numeru PESEL na Twoim profilu! Te dane nie będą widoczne publicznie, a posłużą nam do celów statystycznych. Twoja pomoc pozwoli nam rozwijać i doskonalić kurs!
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
                        v-model="identity.personal_identity_number"
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
                                class="is-checkradio"
                                type="radio"
                                id="personal_identity_number"
                                name="identity_type"
                                value="personal_identity_number"
                                v-model="identity.identity_type">
                            <label for="personal_identity_number">PESEL</label>
                            <input
                                class="is-checkradio"
                                type="radio"
                                id="identity_card"
                                name="identity_type"
                                value="identity_card"
                                v-model="identity.identity_type">
                            <label for="identity_card">Dowód osobisty</label>
                            <input
                                class="is-checkradio"
                                type="radio"
                                id="passport"
                                name="identity_type"
                                value="passport"
                                v-model="identity.identity_type">
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
                    personal_identity_number: '',
                    identity_type: 'personal_identity_number'
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
                wrongIdNumber: {
                    text: 'PESEL jest niepoprawny :(',
                    type: 'error',
                }
            }
        },
        computed: {
			...mapGetters(['currentUserIdentity', 'currentUserId']),
            idNumberAvilable() {
                return Boolean(this.currentUserIdentity.personal_identity_number)
            },
			idNumber() {
				return this.currentUserIdentity.personal_identity_number
			},
			idType() {
				return this.currentUserIdentity.identity_type
			},
            hasNoChanges() {
                return this.identity.personal_identity_number === ''
            },
            validateIdNumber() {
                if (this.identity.identity_type === 'personal_identity_number') {
                    let reg = /^[0-9]{11}$/
                    if (reg.test(this.identity.personal_identity_number) == false) {
                        this.errors.push('PESEL powinien składać się tylko z 11 cyfr')
                        return false
                    } else {
                        let weight = [1,3,7,9,1,3,7,9,1,3,1]
                        let sum = 0

                        for (var i = 0; i < weight.length; i++) {
                            sum += (parseInt(this.identity.personal_identity_number.substring(i, i+1), 10)*weight[i])
                        }
                        if (sum % 10 === 0) {
                            return true
                        } else {
                            this.errors.push('PESEL jest niepoprawny')
                            return false
                        }
                    }
                }
            }
		},
        methods: {
            ...mapActions(['addAutoDismissableAlert', 'setUserIdentity']),
            onSubmit() {
                if (this.validateIdNumber) {
                    this.errors = []
                    try {
                        // axios.post(getApiUrl(`users/${this.currentUserId}/identity`), {
                        //     personal_identity_number: this.identity.personal_identity_number,
                        //     identity_type: this.identity.identity_type
                        // })
                        this.addAutoDismissableAlert(this.alertSuccess)
                        this.setUserIdentity(this.identity)
                    }
                    catch (error) {
                        $wnl.logger.capture(error)
                        this.addAutoDismissableAlert(this.alertError)
                    }
                } else if (this.identity.identity_type !== 'personal_identity_number') {
                    this.addAutoDismissableAlert(this.alertSuccess)
                    this.setUserIdentity(this.identity)
                } else {
                    this.addAutoDismissableAlert(this.wrongIdNumber)
                }
            },
            changeIdentityType() {
                return this.otherIdentity = true
            }
        }
    }
</script>
