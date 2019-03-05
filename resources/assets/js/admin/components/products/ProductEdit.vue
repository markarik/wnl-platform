<template>
	<div>
		<wnl-form
				name="ProductsEditor"
				:populate="isEdit"
				:method="formMethod"
				:resource-route="formResourceRoute"
				:suppress-enter="true"
				@submitSuccess="onSubmitSucess"
				@change="onChange"
				class="product-form"
		>
			<h4>Produkt #{{ id }}</h4>

			<div class="columns">
				<div class="column left">
					<wnl-text name="name">Nazwa</wnl-text>
					<wnl-text name="slug">Slug</wnl-text>
					<div class="field is-grouped">
						<wnl-text name="price">Cena brutto</wnl-text>
						<wnl-select name="vat_rate" :options="vatRates">Stawka VAT</wnl-select>
					</div>
					<wnl-text name="vat_note">Opis VAT</wnl-text>
					<wnl-textarea name="invoice_name">Opis na fakturze</wnl-textarea>

					<wnl-text name="quantity">Liczba (obecnie)</wnl-text>
					<wnl-text name="initial">Liczba (początkowa)</wnl-text>
				</div>

				<div class="column">
					<wnl-datepicker name="course_start" :config="datepickerConfig">Start kursu</wnl-datepicker>
					<wnl-datepicker name="course_end" :config="datepickerConfig">Koniec kursu</wnl-datepicker>

					<hr>

					<wnl-datepicker name="access_start" :config="datepickerConfig">Początek dostępu</wnl-datepicker>
					<wnl-datepicker name="access_end" :config="datepickerConfig">Koniec dostępu</wnl-datepicker>

					<hr>

					<wnl-datepicker name="signups_start" :config="datepickerConfig">Start zapisów</wnl-datepicker>
					<wnl-datepicker name="signups_end" :config="datepickerConfig">Koniec zapisów</wnl-datepicker>
					<wnl-datepicker name="signups_close" :config="datepickerConfig">Zamknięcie zapisów</wnl-datepicker>

					<hr>

					<wnl-datepicker name="delivery_date" :config="datepickerConfig">Data dostawy</wnl-datepicker>
				</div>
			</div>



		</wnl-form>
	</div>

</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.product-form
		.field.is-grouped
			.field
				margin-right: $margin-medium
		.column.left
			border-right: $border-light-gray

</style>

<script>
import {
	Form as WnlForm,
	Text as WnlText,
	Textarea as WnlTextarea,
	Datepicker as WnlDatepicker,
	Select as WnlSelect,
} from 'js/components/global/form';
import {mapActions} from 'vuex';
import {getApiUrl} from 'js/utils/env';
import {ALERT_TYPES} from 'js/consts/alert';

export default {
	data() {
		return {
			datepickerConfig: {
				altInput: true,
				enableTime: true,
				dateFormat: 'U',
				altFormat: 'Y-m-d H:i',
				time_24hr: true,
			},
			formData: {},
			vatRates: [],
		};
	},
	props: {
		id: {
			required: true,
			type: [Number, String],
		}
	},
	components: {
		WnlForm,
		WnlText,
		WnlTextarea,
		WnlDatepicker,
		WnlSelect,
	},
	computed: {
		formResourceRoute() {
			return this.isEdit ? `products/${this.id}` : 'products';
		},
		formMethod() {
			return this.isEdit ? 'put' : 'post';
		},
		isEdit() {
			return this.id !== 'new';
		},
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		onSubmitSucess(data) {
			if (!this.isEdit) {
				this.$router.push({name: 'product-edit', params: {id: data.id}});
			}
		},
		onChange({formData}) {
			this.formData = formData;
		},
		escapeArgumentKey(key) {
			return `{{${key}}}`;
		},
	},
	async mounted() {
		try {
			const {data} = await axios.get(getApiUrl('products/getVatRates'));
			this.vatRates = data.vat_rates.map(value => {
				return {value, text: value};
			});
		} catch (error) {
			$wnl.logger.error(error);
			this.addAutoDismissableAlert({
				text: 'Coś poszło nie tak :(',
				type: ALERT_TYPES.ERROR,
			});
		}
	}
};
</script>
