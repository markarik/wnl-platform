<template>
	<div class="stream-sorting">
		<div v-if="!isMobile" class="tabs">
			<ul>
				<li v-for="(option, index) in sortingOptions" :key="index" :class="{'is-active': isTabActive(option.slug)}">
					<a @click="changeSorting(option.slug)">
						<span class="icon is-small"><i class="fa" :class="option.icon"></i></span> {{option.text}}
					</a>
				</li>
			</ul>
		</div>
		<div v-else class="control">
			<span class="select">
				<select @input="changeSortingWithSelect">
					<option v-for="(option, index) in sortingOptions"
						:key="index"
						:value="option.slug"
						:selected="isTabActive(option.slug)"
					>
						<span class="icon is-small"><i class="fa" :class="option.icon"></i></span> {{option.text}}
					</option>
				</select>
			</span>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.stream-sorting
		margin-bottom: $margin-base

	.is-active
		font-weight: $font-weight-regular
</style>

<script>
	import {mapGetters} from 'vuex'

	const sortingOptions = [
		{
			slug: 'all',
			icon: 'fa-globe',
			text: 'Wszystkie',
		},
		{
			slug: 'slides',
			icon: 'fa-window-maximize',
			text: 'Slajdy',
		},
		{
			slug: 'quiz',
			icon: 'fa-check-circle-o',
			text: 'Pytania kontrolne',
		},
		{
			slug: 'qna',
			icon: 'fa-question-circle-o',
			text: 'Pytania i odpowiedzi',
		},
	]

	export default {
		name: 'StreamSorting',
		data() {
			return {
				activeTab: 'all',
			}
		},
		computed: {
			...mapGetters(['isMobile']),
			sortingOptions() {
				return sortingOptions
			}
		},
		methods: {
			changeSorting(tab) {
				this.activeTab = tab
				this.$emit('changeSorting', tab)
			},
			changeSortingWithSelect(event) {
				this.changeSorting(event.target.value)
			},
			isTabActive(tab) {
				return this.activeTab === tab
			},
		},
	}
</script>
