<template>
	<div class="questions-filters-container">
		<div class="questions-filters-content">
			<div class="wnl-active-filters-container">
				<wnl-active-filters
						:active-filters="activeFilters"
						:loading="fetchingData"
						:filters="filters"
						:items-names-source="itemsNamesSource"
						:matched-count="matchedQuestionsCount"
						:total-count="allQuestionsCount"
						@activeFiltersChanged="onActiveFiltersChanged"
						@autorefreshChange="onAutorefreshChange"
						@elementHeight="setActiveFiltersHeight"
						@fetchMatchingQuestions="$emit('fetchMatchingQuestions')"
						@refresh="onRefresh"
				/>
			</div>
			<div class="wnl-questions-filters"
				 :style="{paddingTop: activeFiltersHeight + 'px'}">
				 <div class="filters-heading">
				 	<span class="metadata margin vertical">
				 		<span class="icon is-tiny"><i class="fa fa-search"></i></span>
						{{$t('questions.filters.searchHeading')}}
				 	</span>
				 </div>
				 <wnl-questions-search class="search-input"
				 :loading="loading"
				 @emitValueToFilter="emitValueToList"/>
				<div class="filters-heading">
					<span class="metadata margin vertical">
						<span class="icon is-tiny"><i class="fa fa-sliders"></i></span>
						{{$t('questions.filters.heading')}}
					</span>
					<a v-if="!isChatMounted && isChatVisible"
					   class="hide-filters" @click="toggleChat">
						{{$t('questions.filters.hide')}}
						<span class="icon is-small"><i class="fa fa-close"></i></span>
					</a>
				</div>
				<wnl-accordion
					:data-source="listableFilters"
					:config="accordionConfig"
					:loading="fetchingData"
					@itemToggled="onItemToggled"
				/>
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'

	.questions-filters-container
		height: 100%
		position: relative
		width: 100%

	.questions-filters-content
		height: 100%
		overflow-y: auto
		padding-bottom: $margin-huge * 2
		width: 100%

	.wnl-questions-filters
		width: 100%

	.wnl-active-filters-container
		background-color: $color-background-light-gray
		left: 0
		position: absolute
		right: 0
		top: 0
		z-index: $z-index-active-filters

	.filters-heading
		align-items: center
		border-bottom: $border-light-gray
		display: flex
		justify-content: space-between
		padding: 0 $margin-base

	.hide-filters
		font-size: $font-size-minus-2
		font-weight: $font-weight-bold
		text-transform: uppercase
</style>

<script>
import {isEmpty, uniq} from 'lodash';
import {mapActions, mapGetters} from 'vuex';

import Accordion from 'js/components/global/accordion/Accordion';
import ActiveFilters from 'js/components/questions/ActiveFilters';
import QuestionsSearch from 'js/components/questions/QuestionsSearch';

const config = {
	flattened: ['resolution'],
	expanded: ['subjects'],
};

export default {
	name: 'QuestionsFilters',
	components: {
		'wnl-accordion': Accordion,
		'wnl-active-filters': ActiveFilters,
		'wnl-questions-search': QuestionsSearch,
	},
	props: {
		activeFilters: {
			type: Array,
			required: true,
		},
		fetchingData: {
			default: false,
			type: Boolean,
		},
		filters: {
			type: Object,
			required: true,
		},
		loading: {
			default: false,
			type: Boolean,
		}
	},
	data() {
		return {
			activeFiltersHeight: 0,
			autorefresh: true,
		};
	},
	computed: {
		...mapGetters(['isChatMounted', 'isChatVisible', 'isMobile']),
		...mapGetters('questions', [
			'allQuestionsCount',
			'matchedQuestionsCount',
		]),
		accordionConfig() {
			return {
				disableEmpty: true,
				expanded: this.expandedItems,
				flattened: ['quiz-planned', 'quiz-resolution', 'quiz-collection'],
				isMobile: this.isMobile,
				itemsNamesSource: this.itemsNamesSource,
				selectedElements: this.activeFilters,
			};
		},
		activeParents() {
			return this.activeFilters.map(this.getParents).reduce((a, b) => a.concat(b));
		},
		expandedItems() {
			const expanded = ['by_taxonomy-subjects'];
			return this.hasActive ? uniq(expanded.concat(this.activeParents)) : expanded;
		},
		hasActive() {
			return this.activeFilters.length > 0;
		},
		itemsNamesSource() {
			return 'questions.filters.items';
		},
		listableFilters() {
			return Object.entries(this.filters).reduce((acc, [key, val]) => {
				if (['list', 'tags'].includes(val.type)) {
					acc[key] = val;
				}
				return acc;
			}, {});
		}
	},
	methods: {
		...mapActions(['toggleChat']),
		getParents(filter) {
			return filter.split('.').map((item, index, splitted) => {
				return splitted.slice(0, index).join('.');
			});
		},
		emitValueToList(value) {
			this.$emit('search', value);
		},
		onActiveFiltersChanged(payload) {
			this.$emit('activeFiltersChanged', {
				refresh: this.autorefresh,
				...payload
			});
		},
		onAutorefreshChange(autorefresh) {
			this.autorefresh = autorefresh;
		},
		onItemToggled({path, selected}) {
			this.$emit('activeFiltersChanged', {
				active: selected,
				filter: path,
				refresh: this.autorefresh,
			});
		},
		onRefresh(payload) {
			this.$emit('activeFiltersChanged', {refresh: true, ...payload});
		},
		setActiveFiltersHeight(height) {
			this.activeFiltersHeight = height;
		},
	},
};
</script>
