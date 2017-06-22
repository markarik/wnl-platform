<template>
	<div class="qna-sorting">
		<div v-if="!isMobile" class="tabs">
			<ul>
				<li v-for="(option, index) in sortingOptions" :key="index" :class="{'is-active': isTabActive(option.slug)}">
					<a @click="changeSorting(option.slug)">
						<span class="icon is-small"><i class="fa" :class="option.icon"></i></span> {{option.text}}
					</a>
				</li>
			</ul>
		</div>
		<div class="control" v-else>
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

	.qna-sorting
		margin-bottom: $margin-base
</style>

<script>
	import {mapGetters, mapActions} from 'vuex'

	const sortingOptions = [
		{
			slug: 'hottest',
			icon: 'fa-thumbs-o-up',
			text: 'Popularne',
		},
		{
			slug: 'latest',
			icon: 'fa-clock-o',
			text: 'Najnowsze',
		},
		{
			slug: 'no-answer',
			icon: 'fa-question-circle-o',
			text: 'Bez odpowiedzi',
		},
		{
			slug: 'my',
			icon: 'fa-user-o',
			text: 'Moje',
		},
	]

	export default {
		name: 'QnaSorting',
		computed: {
			...mapGetters(['isMobile']),
			...mapGetters('qna', ['currentSorting']),
			sortingOptions() {
				return sortingOptions
			},
		},
		methods: {
			...mapActions('qna', ['changeSorting']),
			isTabActive(slug) {
				return this.currentSorting === slug
			},
			changeSortingWithSelect(event) {
				this.changeSorting(event.target.value)
			},
		}
	}
</script>
