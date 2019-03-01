<template>
	<div >
		<h3 class="title is-3">
			Produkty
			<router-link
					class="button is-primary"
					:to="{ name: 'products-edit', params: { id: 'new' } }"
			>
				+ Dodaj nowy produkt
			</router-link>
		</h3>
		<table class="table dashboard-news">
			<tr>
				<th>Id</th>
				<th>Tytuł</th>
				<th>Dostępność</th>
				<th>Start zapisów</th>
				<th>Utworzono</th>
			</tr>
			<tr
					class="dashboard-news__item"
					v-for="product in products"
					:key="product.id"
					@click="goToEdit(product.id)"
			>
				<td>{{product.id}}</td>
				<td>{{product.name}}</td>
				<td>{{product.quantity}}/{{product.initial}}</td>
				<td>{{formatDate(product.signups_start)}}</td>
				<td>{{formatDate(product.created_at)}}</td>

			</tr>
		</table>
	</div>
</template>

<style lang="sass" scoped>
	.dashboard-news__item
		cursor: pointer
</style>

<script>
import { mapActions } from 'vuex';
import moment from 'moment';
import {getApiUrl} from 'js/utils/env';
import {ALERT_TYPES} from 'js/consts/alert';

export default {
	name: 'DashboardNews',
	data() {
		return {
			products: []
		};
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		formatDate(date) {
			if (date) {
				return moment(date * 1000).format('DD/MM/YY H:mm');
			}
		},
		goToEdit(id) {
			this.$router.push({ name: 'products-edit', params: { id } });
		},
	},
	async mounted() {
		try {
			const {data} = await axios.get(getApiUrl('products/all'));
			this.products = Object.values(data).sort((a, b) => a.id < b.id ? 1 : -1);
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
