<template>
	<nav class="pagination" role="navigation">
		<ul class="pagination-list">
			<li v-for="n in lastPage">
				<a class="pagination-link" :class="{'is-current': currentPage === n}" @click="changePage(n)">{{n}}</a>
			</li>
		</ul>
	</nav>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>

</style>

<script>
	export default {
		name: 'Pagination',
		props: {
			initialPage: {
				default: 1,
				type: Number,
			},
			lastPage: {
				required: true,
				type: Number,
			},
		},
		data() {
			return {
				currentPage: 1,
			}
		},
		methods: {
			changePage(page) {
				this.currentPage = page
				this.$emit('changePage', page)
			},
		},
		mounted() {
			this.currentPage = this.initialPage
		},
	}
</script>
