<template>
	<form :name="name" @keydown="keyEvent" @submit.prevent="onSubmitForm">
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
			'loading',
			'submitError',
			'value'
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
			keyEvent(event) {
				if (event.keyCode === 13 && !this.suppressEnter) {
					this.onSubmitForm()
				}
				if (event.keyCode === 37) {
					event.stopImmediatePropagation();
					event.stopPropagation();
				}
				if (event.keyCode === 39) {
					event.stopImmediatePropagation();
					event.stopPropagation();
				}
				event.stopPropagation();
			},
			onSubmitForm() {
				const hasAttachChanged = this.hasAttachChanged()


				if (!this.canSave(this.hasChanges, hasAttachChanged)) {
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

							hasAttachChanged && this.cacheAttach()
						},
						reason => {
							if (this.submitError) {
								this.$emit('submitError', reason.response)
							} else {
								this.handleError(reason)
							}
						},
					)
					.catch((error) => {
						$wnl.logger.error(error, error.stack)
						this.errorFading('Nie udało się.')
					})
			},
			handleError(reason) {
				if (reason.response.status === 404) {
					this.errorFading(this.$t('ui.error.notFound'))
				} else {
					this.errorFading('Ups, coś nie wyszło... Spróbujesz jeszcze raz?')
				}
			},
			cacheAttach() {
				this.cachedAttach = _.cloneDeep(this.attach);
			},
			hasAttachChanged() {
				return !_.isEqual(this.attach, this.cachedAttach)
			},
			canSave(hasFieldChanges, hasAttachChanges) {
				return !this.anyErrors && (hasFieldChanges || hasAttachChanges)
			},
		},
		watch: {
			resourceRoute(val) {
				this.mutation(
					types.FORM_UPDATE_URL,
					getApiUrl(this.resourceRoute)
				)
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
			this.$emit('formIsLoaded')

			if (this.populate) {
				this.action('populateFormFromApi').then(() => {
					this.mutation(types.FORM_IS_LOADED)
				})
			} else if (this.value) {
				this.action('populateFormFromValue', this.value)
				this.mutation(types.FORM_IS_LOADED)
			} else {
				this.mutation(types.FORM_IS_LOADED)
			}

			this.cacheAttach()

			this.$on('submitForm', this.onSubmitForm)
		},
		beforeDestroy() {
			this.mutation(types.FORM_RESET)
		}
	}
</script>
