<template>
	<div>
		<users-list :users="users" v-if="!isLoading">
			<div slot="search">
				<wnl-users-search @search="onSearch"/>
				<template v-if="searchPhrase">
					<span>Aktualne wyszukiwanie:</span>
					<span class="tag is-success">
						{{ searchPhrase }}
						<button class="delete is-small" @click="clearSearch"></button>
					</span>
				</template>
			</div>
			<wnl-pagination v-if="paginationMeta.last_page > 1"
				:currentPage="page"
				:lastPage="paginationMeta.last_page"
				@changePage="onPageChange"
				slot="pagination"
				class="annotations__pagination"
			/>
		</users-list>
		<div v-else>
			<wnl-text-loader v-if="isLoading"></wnl-text-loader>
		</div>
	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.annotations__pagination .pagination-list
		justify-content: center
		margin-top: $margin-medium
</style>

<script>
	import axios from 'axios';
	import {mapActions} from 'vuex'
	import {getApiUrl} from 'js/utils/env'
	import UsersList from "./UsersList";
	import WnlPagination from "js/components/global/Pagination";
	import WnlUsersSearch from "./Search";

	export default {
		components: {UsersList, WnlUsersSearch, WnlPagination},
		data() {
			return {
				users: [],
				isLoading: true,
				searchPhrase: '',
				searchFields: [],
				perPage: 50,
				page: 1,
				includes: 'roles',
				paginationMeta: {}
			}
		},
		methods: {
			...mapActions(['addAutoDismissableAlert']),
			async fetchUsers() {
				try {
					const {data: {data, ...paginationMeta}} = await axios.post(getApiUrl('users/.filter'), this.getRequestParams())

					this.paginationMeta = paginationMeta
					if (paginationMeta.total === 0) {
						this.users = []
					} else {
						const {included, ...users} = data
						this.users = Object.values(users).map(user => ({
							...user,
							roles: (user.roles || []).map(roleId => included.roles[roleId])
						}))
					}
				} catch (error) {
					this.addAutoDismissableAlert({
						text: "Ops, nie udało się pobrać użytkowników. Odśwież stronę i spróbuj jeszcze raz",
						type: 'error'
					})
					$wnl.logger.capture(error)
				} finally {
					this.isLoading = false
				}
			},
			async onSearch({phrase, fields}) {
				this.isLoading = true
				this.page = 1
				this.searchPhrase = phrase
				this.searchFields = fields

				await this.fetchUsers()
			},
			async clearSearch() {
				this.searchPhrase = ''
				this.searchFields = []
				this.page = 1
				await this.fetchUsers()
			},
			async onPageChange(page) {
				this.isLoading = true
				this.page = page
				await this.fetchUsers()
			},
			getRequestParams() {
				const params = {
					include: this.includes,
					limit: this.perPage,
					page: this.page,
					active: [],
					filters: []
				}

				if (this.searchPhrase) {
					params.active = [`search.${this.searchPhrase}`]
					params.filters = [{search: {phrase: this.searchPhrase, fields: this.searchFields}}]
				}

				return params
			},
		},
		async mounted() {
			this.fetchUsers()
		}
	}
</script>
