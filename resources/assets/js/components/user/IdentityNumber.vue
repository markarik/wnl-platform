<template>
    <div>
        <div class="level wnl-screen-title">
            <div class="level-left">
                <div class="level-item big strong">
                    Numer identyfikacyjny
                </div>
            </div>
        </div>
        <div class="identity-number">
            <div class="identity-number__has-personal-id" v-if="personalIdentityNumber">
                <div class="identity-number__has-personal-identity_number">
                    Podany przez Ciebie numer {{ identityNumberType }} to: {{ personalIdentityNumber }}
                    Jeśli chcesz dokonać zmiany, napisz na info@bethink.pl.
                </div>
            </div>
            <div class="identity-number__no-personal-id" v-else>
                <div class="identity-number__personal-identity-number-input">
                    <input
                        name="personal_identity_number"
                        class="input"
                        type="text"
                        placeholder="Numer identyfikacyjny"
                        v-model="personal_identity_number"
                    />
                    <div
                        class="identity-number__personal-identity-number-input__change"
                        @click="changeIdentityType"
                        v-if="!otherIdentity">
                        Nie chcę podawać numeru PESEL
                    </div>
                </div>
                <div
                    class="identity-number__other-identitification"
                    v-if="otherIdentity">
                    <div class="identity_number__radio control">
                        <label class="radio">
                            <input
                                class="radio"
                                type="radio"
                                name="identity_type"
                                value="personal_identity_number"
                                v-model="identity_type"
                            >
                            PESEL
                        </label>
                        <label class="radio">
                            <input
                                class="radio"
                                type="radio"
                                name="identity_type"
                                value="identity_card"
                                v-model="identity_type"
                            >
                            Dowód osobisty
                        </label>
                        <label class="radio">
                            <input
                                class="radio"
                                type="radio"
                                name="identity_type"
                                value="passport"
                                v-model="identity_type"
                            >
                            Paszport
                        </label>
                    </div>
                </div>
                <div class="level-item">
                    <a class="button is-primary is-wide"
                       @click="onSubmit"
                       :disabled="hasChanges"
                    >Zapisz
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<style lang="sass">

</style>

<script>
    import { mapGetters } from 'vuex'
    import { getApiUrl } from 'js/utils/env'

    export default {
        name: 'IdentityNumber',
        components: {
            'wnl-form-text': Text,
        },
        data() {
            return {
                personal_identity_number: '',
                identity_type: 'personal_identity_number',
                resourceUrl: 'users/current/identity',
                otherIdentity: false
            }
        },
        computed: {
			...mapGetters(['currentUserIdentity', 'currentUserId']),
			personalIdentityNumber() {
				return this.currentUserIdentity.personal_identity_number
			},
			identityNumberType() {
				return this.currentUserIdentity.identity_type
			},
            hasChanges() {
                return this.personal_identity_number === ''
            }
		},
        methods: {
            onSubmit() {
                axios.post(getApiUrl(`users/${this.currentUserId}/identity`), {
                    personal_identity_number: this.personal_identity_number,
                    identity_type: this.identity_type
                })
            },
            changeIdentityType() {
                return this.otherIdentity = true
            }
        }
    }
</script>
