<template>
	<div>
		<h3 class="title is-3">
			Newsy
			<router-link
				class="button is-primary"
				:to="{ name: 'dashboard-news-edit', params: { id: 'new' } }"
			>
				+ Dodaj nowego newsa
			</router-link>
		</h3>
		<table class="table dashboard-news">
			<tr>
				<th>Tytuł</th>
				<th>Wyświetlaj od</th>
				<th>Wyświetlaj do</th>
			</tr>
			<tr
				class="dashboard-news__item"
				:class="{'has-text-success': dashboardNewsItem.id === activeItemId}"
				v-for="dashboardNewsItem in dashboardNewsList"
				:key="dashboardNewsItem.id"
				@click="goToEdit(dashboardNewsItem.id)"
			>
				<td>{{dashboardNewsItem.slug}}</td>
				<td>{{formatDate(dashboardNewsItem.start_date)}}</td>
				<td>{{formatDate(dashboardNewsItem.end_date)}}</td>

			</tr>
		</table>
	</div>
</template>

<style lang="sass" scoped>
	.dashboard-news__item
		cursor: pointer
</style>

<script>
import axios from 'axios';
import { mapActions } from 'vuex';
import moment from 'moment';
import { getApiUrl } from 'js/utils/env';
import { ALERT_TYPES } from 'js/consts/alert';

export default {
	name: 'DashboardNews',
	data() {
		return {
			dashboardNewsList: []
		};
	},
	computed: {
		activeItemId() {
			const activeItem = this.dashboardNewsList
				.filter(item => (item.start_date === null || moment(item.start_date * 1000).isBefore())
						&& (item.end_date === null || moment(item.end_date * 1000).isAfter())
				)
				.sort((a, b) => a.created_at < b.created_at ? 1 : -1)[0];

			return activeItem && activeItem.id;
		}
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		formatDate(date) {
			if (date) {
				return moment(date * 1000).format('L LT');
			}
		},
		goToEdit(id) {
			this.$router.push({ name: 'dashboard-news-edit', params: { id } });
		},
	},
	async mounted() {
		try {
			const { data } = await axios.get(getApiUrl('site_wide_messages/dashboard_news'));
			this.dashboardNewsList = Object.values(data);
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
