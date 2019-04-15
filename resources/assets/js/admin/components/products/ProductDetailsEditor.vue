<template>
	<wnl-form
		name="ProductsEditor"
		:populate="isEdit"
		:method="formMethod"
		:resource-route="formResourceRoute"
		:suppress-enter="true"
		@submitSuccess="onSubmitSucess"
		class="product-form"
	>
		<div class="columns">
			<div class="column left">
				<wnl-text name="name">Nazwa</wnl-text>
				<wnl-text name="name">Nazwa</wnl-text>
				<wnl-text name="slug">Slug<br>
					<small>Unikatowy identyfikator kursu, na jego podstawie produkt wyświetla
					się na stronie sprzedażowej.</small>
				</wnl-text>
				<div class="field is-grouped">
					<wnl-text name="price">Cena brutto</wnl-text>
					<wnl-select name="vat_rate" :options="vatRates">Stawka VAT</wnl-select>
				</div>
				<wnl-text name="vat_note">Opis VAT<br>
					<small>Notatka dotycząca podstawy zwolnienia z podatku pojawiająca się
					na fakturze. Zbędna w przypadku stawki 23%.</small>
				</wnl-text>
				<wnl-textarea name="invoice_name">Opis na fakturze</wnl-textarea>

				<wnl-text name="quantity">Liczba (obecnie)</wnl-text>
				<wnl-text name="initial">Liczba (początkowa)</wnl-text>
			</div>

			<div class="column dates">
				<wnl-datepicker name="course_start" :config="datepickerConfig">Start kursu</wnl-datepicker>
				<wnl-datepicker name="course_end" :config="datepickerConfig">Koniec kursu</wnl-datepicker>

				<wnl-datepicker name="access_start" :config="datepickerConfig">Początek dostępu</wnl-datepicker>
				<wnl-datepicker name="access_end" :config="datepickerConfig">Koniec dostępu</wnl-datepicker>

				<wnl-datepicker name="signups_start" :config="datepickerConfig">Start zapisów</wnl-datepicker>
				<wnl-datepicker name="signups_end" :config="datepickerConfig">
					Koniec zapisów<br>
						<small>Po tej dacie wciąż można zapisać się na kurs, ale wyświetlane jest
						ostrzeżenie o tym, że kurs już się rozpoczął.</small>
				</wnl-datepicker>
				<wnl-datepicker name="signups_close" :config="datepickerConfig">
					Zamknięcie zapisów<br>
						<small>Po tej dacie nie można już zapisać się na kurs.</small>
				</wnl-datepicker>

				<wnl-datepicker name="delivery_date" :config="datepickerConfig">
					Data dostawy<br>
						<small>Data realizacji usługi. Zazwyczaj pokrywa się ze startem kursu.</small>
				</wnl-datepicker>
			</div>
		</div>
	</wnl-form>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.product-form
		.field.is-grouped
			.field
				margin-right: $margin-medium
		.column.left
			border-right: $border-light-gray

		.dates
			.field
				border-bottom: $border-light-gray
</style>

<script>
import axios from 'axios';
import {
	Form as WnlForm,
	Text as WnlText,
	Textarea as WnlTextarea,
	Datepicker as WnlDatepicker,
	Select as WnlSelect,
} from 'js/components/global/form';
import { mapActions } from 'vuex';
import { getApiUrl } from 'js/utils/env';
import { ALERT_TYPES } from 'js/consts/alert';

export default {
	data() {
		return {
			datepickerConfig: {
				altInput: true,
				enableTime: true,
				dateFormat: 'U',
				altFormat: 'Y-m-d H:i',
				time_24hr: true,
				defaultDate: null,
			},
			vatRates: [],
		};
	},
	props: {
		id: {
			required: true,
			type: [Number, String],
		},
		isEdit: {
			type: Boolean,
			default: true
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
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		onSubmitSucess(data) {
			if (!this.isEdit) {
				this.$router.push({ name: 'product-edit', params: { id: data.id } });
			}
		},
	},
	async mounted() {
		try {
			const { data } = await axios.get(getApiUrl('products/getVatRates'));
			this.vatRates = data.vat_rates.map(value => ({ value, text: value }));
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
