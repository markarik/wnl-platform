<template>
	<div class="qna-sorting">
		<div v-if="!isMobile" class="tabs">
			<ul>
				<li
					v-for="(option, index) in sortingOptions"
					:key="index"
					:class="{'is-active': isTabActive(option.slug)}"
				>
					<a @click="changeSorting(option.slug)">
						<span class="icon is-small"><i class="fa" :class="option.icon" /></span> {{$t(`qna.sorting.${option.slug}`)}}
					</a>
				</li>
			</ul>
		</div>
		<div v-else class="control">
			<span class="select">
				<select @input="changeSortingWithSelect">
					<option
						v-for="(option, index) in sortingOptions"
						:key="index"
						:value="option.slug"
						:selected="isTabActive(option.slug)"
					>
						<span class="icon is-small"><i class="fa" :class="option.icon" /></span> {{$t(`qna.sorting.${option.slug}`)}}
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

		.tabs
			font-size: $font-size-minus-1

	.is-active
		font-weight: $font-weight-regular

	.select
		font-size: $font-size-minus-2
</style>

<script>
import { mapGetters, mapActions } from 'vuex';

const sortingOptions = [
	{
		slug: 'latest',
		icon: 'fa-clock-o',
	},
	{
		slug: 'no-answer',
		icon: 'fa-question-circle-o',
	},
	{
		slug: 'hottest',
		icon: 'fa-thumbs-o-up',
	},
	{
		slug: 'my',
		icon: 'fa-user-o',
	},
];

export default {
	name: 'QnaSorting',
	computed: {
		...mapGetters(['isMobile']),
		...mapGetters('qna', ['currentSorting']),
		sortingOptions() {
			return sortingOptions;
		},
	},
	methods: {
		...mapActions('qna', ['changeSorting']),
		changeSortingWithSelect(event) {
			this.changeSorting(event.target.value);
		},
		isTabActive(slug) {
			return this.currentSorting === slug;
		},
	}
};
</script>
