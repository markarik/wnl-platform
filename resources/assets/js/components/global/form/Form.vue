<template>
	<form :name="name" @keydown.enter="onSubmitForm">
		<wnl-alert v-for="(alert, timestamp) in alerts"
			:alert="alert"
			cssClass="fixed"
			:key="timestamp"
			:timestamp="timestamp"
			@delete="onDelete"
		></wnl-alert>

		<wnl-submit v-if="!hideSubmit && $slots['submit-before']" @submitForm="onSubmitForm">
			<slot name="submit-before"></slot>
		</wnl-submit>

		<slot></slot>

		<wnl-submit v-if="!hideSubmit && !$slots['submit-before']" @submitForm="onSubmitForm">
			<slot name="submit-after">Zapisz</slot>
		</wnl-submit>
	</form>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

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
		props: ['name', 'method', 'resourceRoute', 'populate', 'hideSubmit', 'attach'],
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
			onSubmitForm() {
				if (this.anyErrors || !this.hasChanges) {
					return false
				}

				this.action('submitForm', {
					method: this.method,
					attach: this.attach,
				})
					.then(
						data => {
							this.successFading(`
								<span class="icon is-small"><i class="fa fa-check-square-o"></i></span>
								<span>Zapisano!</span>
							`)
						},
						reason => {
							this.errorFading('Ups, coś nie wyszło... Spróbujesz jeszcze raz?')
						},
					)
					.catch((error) => {
						this.errorFading('Nie udało się.')
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

				if (_.has(options, 'computed.fillable')) {
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
		},
	}
</script>
