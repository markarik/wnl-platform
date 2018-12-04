<template>
	<div>
        <router-link class="button is-primary margin bottom" :to="{'name': 'users-add'}">Dodaj Użytkownika</router-link>
		<users-list :users="users" v-show="!isLoading">
			<wnl-search-input @search="onSearch" :availableFields="searchAvailableFields" slot="search" />
			<wnl-pagination v-if="paginationMeta.last_page > 1"
				:currentPage="page"
				:lastPage="paginationMeta.last_page"
				@changePage="onPageChange"
				slot="pagination"
				class="users__pagination"
			/>
		</users-list>
		<wnl-text-loader v-if="isLoading"></wnl-text-loader>
	</div>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.users__pagination .pagination-list
		justify-content: center
		margin-top: $margin-medium
</style>

<script>
	import axios from 'axios';
	import {mapActions} from 'vuex'
	import {getApiUrl} from 'js/utils/env'
	import UsersList from "./UsersList";
	import WnlPagination from "js/components/global/Pagination";
	import WnlSearchInput from 'js/components/global/SearchInput';

	export default {
		components: {UsersList, WnlSearchInput, WnlPagination},
		data() {
			return {
				users: [],
				isLoading: true,
				searchPhrase: '',
				searchFields: [],
				perPage: 50,
				page: 1,
				includes: 'roles',
				paginationMeta: {},
				searchAvailableFields: [
					{value: 'id', title: 'ID'},
					{value: 'email', title: 'Email'},
					{value: 'full_name', title: 'Imię i nazwisko'},
				]
			}
		},
		methods: {
			...mapActions(['addAutoDismissableAlert']),
			async fetchUsers() {
				try {
					const response = await axios.post(getApiUrl('users/.filter'), this.getRequestParams())
					const {data: {data, ...paginationMeta}} = response;

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
