<template>
	<div>
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					Metody płatności
				</div>
			</div>
		</div>
		<wnl-text-loader v-if="loadingMethods">Ładuję...</wnl-text-loader>
		<div v-else>
			<div v-for="method in methods" :key="method.id">
				<input type="checkbox"
				       :checked="methodEnabled(method.id)"
				       :id="method.id"
				       @change="toggleMethod($event, method.id)"
				/>
				<label :for="method.id">{{ method.slug }}</label>
				<wnl-datepicker
						name="start_date"
						:config="datepickerConfig"
						:value="method.start_date"
						@closed="onDatePickerClosed($event, method.id, 'start_date')"
				/>
				<wnl-datepicker
						name="end_date"
						:config="datepickerConfig"
						:value="method.end_date"
						@closed="onDatePickerClosed($event, method.id, 'end_date')"
				/>
			</div>
		</div>

		<div>
			<div class="level wnl-screen-title" v-if="showInstalmentsSchedule">
				<div class="level-left">
					<div class="level-item big strong">
						Domyślny harmonogram rat dla produktu
					</div>
				</div>
			</div>
			<wnl-text-loader v-if="loadingInstalments">Ładuję...</wnl-text-loader>
			<table></table>
		</div>

		<a class="button is-primary is-wide" v-if="!loadingInstalments && !loadingMethods" @click="save">Zapisz</a>
	</div>
</template>

<style lang="sass" scoped>
	.wnl-table__cell--datepicker
		width: 240px
</style>

<script>
import {mapActions} from 'vuex';
import {isEmpty} from 'lodash';

import {getApiUrl} from 'js/utils/env';
import {swalConfig} from 'js/utils/swal';

import WnlDatepicker from 'js/components/global/Datepicker';

export default {
	components: {WnlDatepicker},
	props: {
		id: {
			type: [String, Number],
			required: true
		},
	},
	data() {
		return {
			loading: false,
			methods: {},
			loadingMethods: false,
			loadingInstalments: false,
			datepickerConfig: {
				altInput: true,
				enableTime: true,
				dateFormat: 'U',
				altFormat: 'Y-m-d H:i',
				time_24hr: true,
			},
		};
	},
	computed: {
		showInstalmentsSchedule() {
			return !isEmpty(this.methods)
				&& Object.values(this.methods).filter(method => method.slug === 'instalments')[0].enabled;
		},
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),

		methodEnabled(id) {
			return this.methods[id].enabled;
		},

		toggleMethod(event, id) {
			this.methods[id].enabled = event.target.checked;
		},

		onDatePickerClosed(value, id, date){
			this.methods[id][date] = value;
		},

		async save() {
			await axios.post(getApiUrl(`products/${this.id}/syncPaymentMethods`), this.methods);
		},

		async getPaymentMethods() {
			const {data} = await axios.get(getApiUrl('payment_methods/all'));
			return data;
		},

		async getProductInstalments() {

		},

		async getProduct() {
			const url = getApiUrl(`products/${this.id}?include=payment_methods`);
			const {data: {included, ...product}} = await axios.get(url);
			const productMethods = included.payment_methods;
			return {product, productMethods};
		}

	},
	async mounted() {
		this.loadingMethods = true;
		try {
			const methods = await this.getPaymentMethods();
			const {product, productMethods} = await this.getProduct();

			this.methods = Object.assign({}, ...methods.map(item => (
				{
					[item.id]: {
						...item,
						enabled: product.payment_methods.includes(item.id),
						start_date: productMethods[item.id].start_date * 1000 || null,
						end_date: productMethods[item.id].end_date * 1000 || null,
					}
				}
			)));

		} catch (e) {
			this.addAutoDismissableAlert({
				text: 'Nie udało się pobrać dostępnych metod płatności',
				type: 'error'
			});
			$wnl.logger.capture(e);
		} finally {
			this.loadingMethods = false;
		}
	}
};
</script>
