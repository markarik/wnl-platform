<template>
	<form :name="name" @keydown.enter="onEnter" @submit.prevent="onSubmitForm">
		<wnl-alert v-for="(alert, timestamp) in alerts"
			cssClass="fixed"
			:alert="alert"
			:key="timestamp"
			:timestamp="timestamp"
			@delete="onDelete"
		></wnl-alert>
		<slot></slot>
		<wnl-submit v-if="!hideDefaultSubmit"></wnl-submit>
	</form>
</template>

<script>
	import _ from 'lodash'
	import axios from 'axios'
	import { mapActions } from 'vuex'

	import Submit from 'js/components/global/form/Submit'
	import { alerts } from 'js/mixins/alerts'
	import { createForm } from 'js/store/modules/form'
	import { getApiUrl } from 'js/utils/env'
	import * as types from 'js/store/mutations-types'

	export default {
		name: 'Form',
		components: {
			'wnl-submit': Submit,
		},
		mixins: [ alerts ],
		// TODO: Introduce an options prop for better readability
		props: [
			'name',
			'method',
			'resourceRoute',
			'attach',
			'populate',
			'hideDefaultSubmit',
			'suppressEnter',
			'resetAfterSubmit',
		],
		computed: {
			anyErrors() {
				return this.getter('anyErrors')
			},
			hasChanges() {
				return this.getter('hasChanges')
			},
			submitEvent() {
				return `submitForm-${this.name}`
			},
		},
		methods: {
			action(action, payload = {}) {
				return this.$store.dispatch(`${this.name}/${action}`, payload)
			},
			getter(getter) {
				return this.$store.getters[`${this.name}/${getter}`]
			},
			getterFunction(getter, payload = {}) {
				return this.$store.getters[`${this.name}/${getter}`](payload)
			},
			mutation(mutation, payload = {}) {
				return this.$store.commit(`${this.name}/${mutation}`, payload)
			},
			onEnter() {
				if (!this.suppressEnter) {
					this.onSubmitForm()
				}
			},
			onSubmitForm() {
				if (this.anyErrors || !this.hasChanges) {
					return false
				}

				this.action('submitForm', {
					method: this.method,
					attach: this.attach,
				})
					.then(
						response => {
							this.successFading(`
								<span class="icon is-small"><i class="fa fa-check-square-o"></i></span>
								<span>Zapisano!</span>
							`)

							if (this.resetAfterSubmit) {
								this.mutation(types.FORM_RESET)
								this.mutation(types.FORM_UPDATE_ORIGINAL_DATA)
							}

							this.$emit('submitSuccess', response, this.getter('getData'))
						},
						reason => {
							$wnl.logger.error(reason)
							this.errorFading('Ups, coś nie wyszło... Spróbujesz jeszcze raz?')
							this.$emit('submitError')
						},
					)
					.catch((error) => {
						$wnl.logger.error(error, error.stack)
						this.errorFading('Nie udało się.')
						this.$emit('submitError')
					})
			}
		},
		created() {
			if (!this.$store.state.hasOwnProperty(this.name)) {
				this.$store.registerModule(this.name, createForm())
			}
		},
		mounted() {
			let dataModel = {}, defaults = {}

			_.each(this.$children, (child) => {
				let options = child.$options

				if (!_.isUndefined(options.computed.fillable)) {
					let name = options.propsData.name,
						defaultValue = options.computed.default() || ''

					dataModel[name] = defaultValue
					defaults[name] = defaultValue
				}
			})

			this.mutation(types.FORM_SETUP, {
				data: dataModel,
				defaults,
				resourceUrl: getApiUrl(this.resourceRoute),
			})

			if (this.populate) {
				this.action('populateForm').then(() => {
					this.mutation(types.FORM_IS_LOADED)
				})
			} else {
				this.mutation(types.FORM_IS_LOADED)
			}

			this.$on('submitForm', this.onSubmitForm)
		},
	}
</script>
