<template>
	<nav class="pagination" role="navigation">
		<ul class="pagination-list">
			<li v-for="n, index in items" :key="index">
				<a v-if="isPage(n)" class="pagination-link" :class="{'is-current': currentPage === n}" @click="changePage(n)">{{n}}</a>
				<span v-else class="pagination-ellipsis">&hellip;</span>
			</li>
		</ul>
	</nav>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.pagination-link
		transition: all $transition-length-base
</style>

<script>
	const fill = 'ellipsis'

	export default {
		name: 'Pagination',
		props: {
			adjacentItems: {
				default: 2,
				type: Number,
			},
			currentPage: {
				default: 1,
				type: Number,
			},
			initialPage: {
				default: 1,
				type: Number,
			},
			lastPage: {
				required: true,
				type: Number,
			},
		},
		computed: {
			items() {
				let items = [this.initialPage],
					leftRange = Math.max(this.initialPage + 1, this.currentPage - this.adjacentItems),
					rightRange = Math.min(this.lastPage - 1, this.currentPage + this.adjacentItems)

				if (this.currentPage - this.adjacentItems > this.initialPage + 1) {
					items.push(fill)
				}

				if (this.currentPage - 1 >= leftRange) {
					for (let i = leftRange; i < this.currentPage; i++) {
						items.push(i)
					}
				}

				if ([this.initialPage, this.lastPage].indexOf(this.currentPage) === -1) {
					items.push(this.currentPage)
				}

				if (this.currentPage + 1 <= rightRange) {
					for (let j = this.currentPage + 1; j <= rightRange; j++) {
						items.push(j)
					}
				}

				if (this.currentPage + this.adjacentItems < this.lastPage - 1) {
					items.push(fill)
				}

				if (this.lastPage !== this.initialPage) {
					items.push(this.lastPage)
				}

				return items
			},
		},
		methods: {
			changePage(page) {
				this.$emit('changePage', page)
			},
			isPage(item) {
				return typeof item === 'number'
			},
		},
	}
</script>
