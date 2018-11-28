<template>
    <div >
        <h3>Newsy <router-link class="button is-primary" :to="{ name: 'dashboard-news-edit', params: { id: 'new' } }">Stwórz nowego newsa</router-link></h3>
        <table class="table dashboard-news">
            <tr>
                <th>Slug</th>
                <th>Start date</th>
                <th>End date</th>
            </tr>
            <tr
                    class="dashboard-news__item"
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
	import {mapGetters, mapActions} from 'vuex'
	import moment from 'moment'

	export default {
		name: 'DashboardNews',
		components: {
		},
		computed: {
			...mapGetters('siteWideMessages', ['dashboardNewsList']),
		},
		methods: {
			...mapActions('siteWideMessages', ['fetchUserSiteWideMessages']),
            formatDate(date) {
				return moment(date * 1000).format('L LT')
			},
			goToEdit(id) {
				this.$router.push({ name: 'dashboard-news-edit', params: { id } })
			}
		},
		mounted() {
			this.fetchUserSiteWideMessages()
		}
	}
</script>
