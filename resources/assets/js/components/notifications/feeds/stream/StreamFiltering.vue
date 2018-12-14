<template>
	<div class="stream-filtering">
		<div v-if="!isMobile" class="tabs">
			<ul>
				<li v-for="(option, index) in filteringOptions" :key="index" :class="{'is-active': isTabActive(option.slug)}">
					<a @click="changeFiltering(option.slug)">
						<span class="icon is-small"><i class="fa" :class="option.icon"></i></span> {{option.text}}
					</a>
				</li>
			</ul>
		</div>
		<div v-else class="control">
			<span class="select">
				<select @input="changeFilteringWithSelect">
					<option v-for="(option, index) in filteringOptions"
						:key="index"
						:value="option.slug"
						:selected="isTabActive(option.slug)"
					>
						<span class="icon is-small"><i class="fa" :class="option.icon"></i></span> {{option.text}}
					</option>
				</select>
			</span>
		</div>
		<a class="button is-small toggle-archived" @click="$emit('toggleShowRead')">
			<span class="text">{{buttonMessage}}</span>
			<span class="icon is-small">
				<i class="fa" :class="showRead ? 'fa-eye' : 'fa-eye-slash'"></i>
			</span>
		</a>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.stream-filtering
		align-items: flex-end
		display: flex
		margin-bottom: $margin-base
		justify-content: space-between

		.tabs
			flex: 1 auto
			font-size: $font-size-minus-1
			margin-bottom: 0

	.toggle-archived
		margin-left: $margin-base

		.text
			padding-right: $margin-tiny

		.icon
			color: $color-gray-dimmed

	.is-active
		font-weight: $font-weight-regular

	.select
		font-size: $font-size-minus-2
</style>

<script>
import {mapGetters} from 'vuex';

const filteringOptions = [
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
];

export default {
	name: 'StreamFiltering',
	props: {
		showRead: {
			type: Boolean,
		}
	},
	data() {
		return {
			activeTab: 'all',
		};
	},
	computed: {
		...mapGetters(['isMobile']),
		filteringOptions() {
			return filteringOptions;
		},
		buttonMessage() {
			return this.showRead ? this.$t('notifications.stream.showUnread') : this.$t('notifications.stream.showRead');
		},
	},
	methods: {
		changeFiltering(tab) {
			this.activeTab = tab;
			this.$emit('changeFiltering', tab);
		},
		changeFilteringWithSelect(event) {
			this.changeFiltering(event.target.value);
		},
		isTabActive(tab) {
			return this.activeTab === tab;
		},
	},
};
</script>
