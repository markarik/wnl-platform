<template>
	<form :name="name" @keydown="keyEvent" @submit.prevent="onSubmitForm">
		<wnl-alert v-for="(alert, timestamp) in alerts"
			css-class="fixed"
			:alert="alert"
			:key="timestamp"
			:timestamp="timestamp"
			@delete="onDelete"
		></wnl-alert>
		<slot :on-submit="onSubmitForm"></slot>
		<wnl-submit v-if="!hideDefaultSubmit"></wnl-submit>
	</form>
</template>

<script>
import _ from 'lodash';

import Submit from 'js/components/global/form/Submit';
import { alerts } from 'js/mixins/alerts';
import { getApiUrl } from 'js/utils/env';
import { mapActions } from 'vuex';
import * as types from 'js/store/mutations-types';

export default {
	name: 'Form',
	components: {
		'wnl-submit': Submit,
	},
	mixins: [ alerts ],
	props: {
		name: {
			type: String,
			required: true,
		},
		method: {
			type: String,
			required: true,
		},
		resourceRoute: {
			type: String,
			required: true,
		},
		attach: {
			type: Object,
			default: () => ({}),
		},
		value: {
			type: [Object, String],
			default: () => ({}),
		},
		populate: {
			// TODO make type consistent
			default: false,
		},
		hideDefaultSubmit: {
			// TODO make type consistent
			default: false,
		},
		suppressEnter: {
			// TODO make type consistent
			default: false,
		},
		resetAfterSubmit: {
			// TODO make type consistent
			default: false,
		},
		submitError: {
			type: Boolean,
			default: false,
		},
		beforeSubmit: {
			type: Function,
			default: () => true,
		},
	},
	computed: {
		anyErrors() {
			return this.getter('anyErrors');
		},
		hasChanges() {
			return this.getter('hasChanges');
		},
		formData() {
			return this.getter('getData');
		}
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		action(action, payload = {}) {
			return this.$store.dispatch(`form/${action}`, {payload, formName: this.name});
		},
		getter(getter) {
			return this.$store.getters[`form/${getter}`](this.name);
		},
		getterFunction(getter, payload = {}) {
			return this.$store.getters[`form/${getter}`]({payload, formName: this.name});
		},
		mutation(mutation, payload = {}) {
			return this.$store.commit(`form/${mutation}`, {payload, formName: this.name});
		},
		keyEvent(event) {
			if (event.keyCode === 13 && !this.suppressEnter) {
				this.onSubmitForm();
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
		async onSubmitForm() {
			const hasAttachChanged = this.hasAttachChanged();

			if (!this.canSave(this.hasChanges, hasAttachChanged)) {
				return false;
			}

			if (await !this.beforeSubmit()) {
				$wnl.logger.info('Form submit was cancelled');
				return;
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
							`);

						if (this.resetAfterSubmit) {
							this.mutation(types.FORM_RESET);
							this.mutation(types.FORM_UPDATE_ORIGINAL_DATA);
						}

						this.$emit('submitSuccess', response, this.formData);

						hasAttachChanged && this.cacheAttach();
					},
					reason => {
						if (this.submitError) {
							this.$emit('submitError', reason.response);
						} else {
							this.handleError(reason);
						}
					},
				)
				.catch((error) => {
					$wnl.logger.error(error, error.stack);
					this.errorFading('Nie udało się.');
				});
		},
		handleError(reason) {
			if (reason.response.status === 404) {
				this.addAutoDismissableAlert({
					type: 'warning',
					text: this.$t('ui.error.notFound'),
				});
			}
			else if (reason.response.status === 422) {
				this.addAutoDismissableAlert({
					type: 'warning',
					text: this.$t('ui.error.validationFailed'),
				});
			}
			else {
				this.errorFading('Ups, coś nie wyszło... Spróbujesz jeszcze raz?');
			}
		},
		cacheAttach() {
			this.cachedAttach = _.cloneDeep(this.attach);
		},
		hasAttachChanged() {
			return !_.isEqual(this.attach, this.cachedAttach);
		},
		canSave(hasFieldChanges, hasAttachChanges) {
			return !this.anyErrors && (hasFieldChanges || hasAttachChanges);
		},
	},
	created() {
		this.mutation(types.FORM_INITIAL_SETUP);
	},
	async mounted() {
		let dataModel = {}, defaults = {};

		_.each(this.$children, (child) => {
			let options = child.$options;

			if (!_.isUndefined(_.get(options, 'computed.fillable'))) {
				let name = options.propsData.name,
					defaultValue = options.computed.default() || '';

				dataModel[name] = defaultValue;
				defaults[name] = defaultValue;
			}
		});

		this.mutation(types.FORM_SETUP, {
			data: dataModel,
			defaults,
			resourceUrl: getApiUrl(this.resourceRoute),
		});

		if (this.populate) {
			await this.action('populateFormFromApi');
		} else if (this.value) {
			this.action('populateFormFromValue', this.value);
		}

		this.mutation(types.FORM_IS_LOADED);
		this.$emit('formIsLoaded', dataModel);

		this.cacheAttach();

		this.$on('submitForm', this.onSubmitForm);
	},
	watch: {
		formData(newVal) {
			this.$emit('change', {formData: newVal});
		},
		resourceRoute(val) {
			this.mutation(
				types.FORM_UPDATE_URL,
				getApiUrl(this.resourceRoute)
			);
		}
	},
	beforeDestroy() {
		this.mutation(types.FORM_RESET);
	}
};
</script>
