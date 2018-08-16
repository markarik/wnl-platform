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
				copy
			</div>
		</div>
        <div class="id-number">
            <div class="id-number__has-personal-id" v-if="personalIdentityNumber">
                <div class="id-number__has-personal-identity_number">
                    Podany przez Ciebie numer {{ identityType }} to: {{ personalIdentityNumber }}
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
                        v-model="personal_identity_number"
                    />
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
                                v-model="identity_type">
                            <label for="personal_identity_number">PESEL</label>
                            <input
                                class="is-checkradio"
                                type="radio"
                                id="identity_card"
                                name="identity_type"
                                value="identity_card"
                                v-model="identity_type">
                            <label for="identity_card">Dowód osobisty</label>
                            <input
                                class="is-checkradio"
                                type="radio"
                                id="passport"
                                name="identity_type"
                                value="passport"
                                v-model="identity_type">
                            <label for="passport">Paszport</label>
                    </div>
                </div>
                <div class="level-item">
                    <a class="button is-primary is-wide"
                       @click="onSubmit"
                       :disabled="hasChanges"
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
			identityType() {
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
