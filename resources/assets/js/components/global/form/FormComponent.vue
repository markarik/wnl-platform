<template>
	<form :name="name">
		<wnl-alert v-for="(alert, timestamp) in alerts"
			:alert="alert"
			cssClass="fixed"
			:key="timestamp"
			:timestamp="timestamp"
		></wnl-alert>

		<slot></slot>

		<wnl-submit @submitForm="onSubmitForm">
			<slot name="submit"></slot>
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
	import { createForm } from 'js/store/modules/forms'
	import * as types from 'js/store/mutations-types'

	export default {
		name: 'FormComponent',
		components: {
			'wnl-submit': Submit,
		},
		mixins: [ alerts ],
		props: ['name', 'resourceUrl', 'populate', 'method'],
		computed: {
			isReady() {
				return this.getter('isReady')
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
			mutation(mutation, payload = {}) {
				return this.$store.commit(`${this.name}/${mutation}`, payload)
			},
			onSubmitForm() {
				this.action('submitForm', { method: this.method })
					.then(
						data => {
							this.successFading('Zapisano!')
						},
						reason => {
							this.errorFading('Nie udało się.')
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
				dataModel,
				defaults,
				resourceUrl: this.resourceUrl,
			})

			if (this.populate) {
				this.action('populateForm', {
					resourceUrl: this.resourceUrl
				})
			} else {
				this.mutation(types.FORM_IS_READY)
			}
		},
	}
</script>
