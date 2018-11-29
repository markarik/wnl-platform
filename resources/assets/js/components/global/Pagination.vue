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
				let items = [],
					a = this.adjacentItems,
					c = this.currentPage,
					i = this.initialPage,
					l = this.lastPage,
					adjacentLeft = Math.min(c - i, a) + (a - Math.min(l - c, a)),
					adjacentRight = Math.min(l - c, a) + (a - Math.min(c - i, a)),
					displayInitial = c - adjacentLeft > i,
					displayLast = c + adjacentRight < l,
					hasLeftElipsis = c - adjacentLeft > i + 1,
					hasRightElipsis = c + adjacentRight < l - 1

				// Add extra item for not displayed elipsis and initial and last pages
				adjacentLeft = adjacentLeft + !hasRightElipsis + !displayLast
				adjacentRight = adjacentRight + !hasLeftElipsis + !displayInitial

				// And now, calculate it again...
				displayInitial = c - adjacentLeft > i
				displayLast = c + adjacentRight < l
				hasLeftElipsis = c - adjacentLeft > i + 1
				hasRightElipsis = c + adjacentRight < l - 1

				if (displayInitial) items.push(i)
				if (hasLeftElipsis) items.push(fill)

				if (adjacentLeft > 0) {
					for (let n = Math.max(c - adjacentLeft, i); n < c; n++) {
						items.push(n)
					}
				}

				items.push(c)

				if (adjacentRight > 0) {
					for (let n = c + 1; n <= Math.min(c + adjacentRight, l); n++) {
						items.push(n)
					}
				}

				if (hasRightElipsis) items.push(fill)
				if (displayLast) items.push(l)

				return items
			},
			routerPage() {
				return this.$route.query.page && parseInt(this.$route.query.page, 10) || 1;
			}
		},
		methods: {
			changePage(page) {
				this.$emit('changePage', page)
				this.$router.push({ query: { page }})
			},
			isPage(item) {
				return typeof item === 'number'
			},
		},
		mounted() {
			if (this.routerPage !== this.currentPage) {
				this.$emit('changePage', this.routerPage);
			}
		},
		watch: {
			currentPage(newVal) {
				if (this.routerPage !== newVal) {
					this.$router.push({ query: { page: newVal }})
				}
			}
		}
	}
</script>
