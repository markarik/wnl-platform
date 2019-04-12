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
		<table v-else class="table">
			<thead>
				<th>Dostępna</th>
				<th>Metoda płatności</th>
				<th>Dostępna od</th>
				<th>Dostępna do</th>
			</thead>
			<tbody>
				<tr v-for="method in methods" :key="method.id">
					<td>
						<input type="checkbox"
						       :checked="methodEnabled(method.id)"
						       :id="method.id"
						       @change="toggleMethod($event, method.id)"
						/>
					</td>
					<td>
						<label :for="method.id">{{ methodNames[method.slug] }}</label>
					</td>
					<td>
						<wnl-datepicker
								v-if="methodEnabled(method.id)"
								name="start_date"
								:config="datepickerConfig"
								:value="getDateForPaymentMethod(method.id, 'start_date')"
								@closed="onDatePickerClosed($event, method.id, 'start_date')"
						/>
					</td>
					<td>
						<wnl-datepicker
								v-if="methodEnabled(method.id)"
								name="end_date"
								:config="datepickerConfig"
								:value="getDateForPaymentMethod(method.id, 'end_date')"
								@closed="onDatePickerClosed($event, method.id, 'end_date')"
						/>
					</td>
				</tr>
			</tbody>
		</table>

		<div v-if="showInstalmentsSchedule">
			<div class="level wnl-screen-title">
				<div class="level-left">
					<div class="level-item big strong">
						Domyślny harmonogram rat dla produktu
					</div>
				</div>
			</div>
			<table class="table">
				<thead>
					<th>Numer raty</th>
					<th>Rodzaj</th>
					<th>Wysokość raty</th>
					<th>Termin (dni od zamówienia)</th>
					<th>Termin (data)</th>
				</thead>
				<tbody>
					<tr v-for="(instalment, index) in instalments" :key="index">
						<td>{{ instalment.order_number }}</td>
						<td>{{ instalmentTypes[instalment.value_type] }}</td>
						<td>{{ instalment.value }}{{ instalmentValueUnit(instalment.value_type) }}</td>
						<td>{{ instalment.due_days || '-'}}</td>
						<td>
							<wnl-datepicker
									v-if="!instalment.due_days"
									name="end_date"
									:config="datepickerConfig"
									:value="instalment.due_date * 1000"
									@closed="dueDateUpdated($event, instalment.order_number)"
							/>
							<span v-else>-</span>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

		<a class="button is-primary is-wide" v-if="!loadingMethods" @click="save">Zapisz</a>
	</div>
</template>

<script>
import axios from 'axios';
import {mapActions} from 'vuex';

import {getApiUrl} from 'js/utils/env';

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
			methods: [],
			selectedMethods: [],
			loadingMethods: false,
			datepickerConfig: {
				altInput: true,
				enableTime: true,
				dateFormat: 'U',
				altFormat: 'Y-m-d H:i',
				time_24hr: true,
			},
			instalments: [
				{
					order_number: 1,
					product_id: this.id,
					value_type: 'percentage',
					value: 50,
					due_days: 7,
					due_date: null,
				},
				{
					order_number: 2,
					product_id: this.id,
					value_type: 'percentage',
					value: 50,
					due_days: null,
					due_date: null,
				},
				{
					order_number: 3,
					product_id: this.id,
					value_type: 'percentage',
					value: 100,
					due_days: null,
					due_date: null,
				},
			],
			methodNames: {
				instalments: 'Raty',
				online: 'Płatność online (przelewy24)',
				transfer: 'Przelew bankowy',

			},
			instalmentTypes: {
				percentage: 'procent kwoty pozostałej do zapłaty',
				amount: 'kwota',
			}
		};
	},
	computed: {
		showInstalmentsSchedule() {
			return this.selectedMethods.some(item => item.slug === 'instalments');
		},
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),

		instalmentValueUnit(type){
			return type === 'percentage' ? '%' : 'zł';
		},

		getDateForPaymentMethod(id, date) {
			const method = this.selectedMethods.find(item => item.id === id);
			return method[date] * 1000 || null;
		},

		methodEnabled(id) {
			return this.selectedMethods.some(item => item.id === id);
		},

		toggleMethod(event, id) {
			if(event.target.checked) {
				const method = this.methods.find(item => item.id === id);
				this.selectedMethods.push({
					id: method.id,
					slug: method.slug,
					start_date: null,
					end_date: null,
				});
			} else {
				this.selectedMethods = this.selectedMethods.filter(item => item.id !== id);
			}
		},

		onDatePickerClosed(value, id, date){
			this.selectedMethods = this.selectedMethods.map(item => {
				if (item.id === id) item[date] = value;
				return item;
			});
		},

		dueDateUpdated(value, instalmentNumber) {
			this.instalments = this.instalments.map(item => {
				if (item.order_number === instalmentNumber) item.due_date = value;
				return item;
			});
		},

		async save() {
			try{
				await axios.put(getApiUrl(`products/${this.id}/syncPaymentMethods`), {
					payment_methods: this.selectedMethods
				});
				await axios.put(getApiUrl(`products/${this.id}/syncInstalments`), {
					instalments: this.instalments
				});
				this.addAutoDismissableAlert({
					text: 'OK 👍',
					type: 'success'
				});
			} catch (e) {
				this.addAutoDismissableAlert({
					text: 'Nie udało się pobrać dostępnych metod płatności',
					type: 'error'
				});
				$wnl.logger.capture(e);
			}
		},

		async getPaymentMethods() {
			const {data} = await axios.get(getApiUrl('payment_methods/all'));
			return data;
		},

		async getProduct() {
			const url = getApiUrl(`products/${this.id}?include=payment_methods,instalments`);
			const {data: {included}} = await axios.get(url);

			const productMethods = included && included.payment_methods ? Object.values(included.payment_methods) : [];
			const instalments = included && included.instalments ? Object.values(included.instalments) : [];
			return {productMethods, instalments};
		}

	},
	async mounted() {
		this.loadingMethods = true;
		try {
			const methods = await this.getPaymentMethods();
			const {productMethods, instalments} = await this.getProduct();

			this.instalments = this.instalments.map(item => {
				const instalment = instalments.find(instalment => instalment.order_number === item.order_number);
				if (instalment){
					item.due_date = instalment.due_date;
				}
				return item;
			});
			this.methods = methods;
			this.selectedMethods = productMethods.map(method => ({
				...method,
				start_date: method.start_date || null,
				end_date: method.end_date || null,
			}));
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
