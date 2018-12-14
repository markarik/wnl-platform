<template lang="html">
	<div>
		<div v-if="hasHits" class="wnl-slides-search" :class="{'is-mobile': isMobile}">
			<wnl-slide-thumbnail
				v-for="(hit, index) in hits"
				:key="index"
				:hit="hit"
				@resultClicked="emitResultClicked"
			/>
		</div>
		<div v-else-if="hasSearched" class="slides-zero-state">
			{{ $t('search.zeroState', {phrase}) }}
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-slides-search
		justify-content: flex-start
		display: flex
		flex-wrap: wrap

		&.is-mobile
			justify-content: center

	.slides-zero-state
		color: $color-gray-dimmed
		font-size: 3vh
		font-weight: bold
		line-height: $line-height-base
</style>

<script>
import {size} from 'lodash';
import {mapGetters} from 'vuex';

import SlideSearchResult from 'js/components/global/search/SlideSearchResult';
import {getApiUrl} from 'js/utils/env';

export default {
	name: 'SlidesSearch',
	components: {
		'wnl-slide-thumbnail': SlideSearchResult,
	},
	props: {
		phrase: {
			required: true,
			type: String,
		}
	},
	data() {
		return {
			hasSearched: false,
			hits: [],
		};
	},
	computed: {
		...mapGetters(['isMobile']),
		hasHits() {
			return size(this.hits) > 0;
		},
	},
	methods: {
		search() {
			if (!this.phrase) return;

			this.$emit('searchStarted');

			axios.get(getApiUrl(`slides/.search?q=${this.phrase}`))
				.then(response => {
					this.hits = response.data.hits.hits;
					this.hasSearched = true;
					this.$emit('searchComplete');
				});
		},
		emitResultClicked() {
			this.$emit('resultClicked');
		}
	},
	watch: {
		'phrase'() {
			this.search();
		}
	}
};
</script>
