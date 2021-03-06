<template>
	<wnl-slide-link
		class="slide-router-link unselectable"
		:context="context"
		blank-page="_blank"
	>
		<div class="slide-context">
			<div class="group-and-lesson">
				<span
					v-for="(groupName, index) in groupNames"
					:key="index"
					class="group-name"
					:title="groupName"
				>
					{{truncate(groupName, 15)}}
					<span class="icon is-small"><i class="fa fa-angle-right" /></span>
				</span>

				<span class="lesson-name" :title="lessonName">{{truncate(lessonName, 30)}}</span>
			</div>
			<div class="section-name" :title="sectionName">
				{{truncate(sectionName, 40)}}
			</div>
		</div>
		<div class="slide-thumb" @click="onThumbnailClick">
			<div class="thumb-meta">
				<span class="slide-number">{{slideNumber}}</span>
				<span v-if="media" class="icon is-tiny"><i class="fa" :class="media.icon" /></span>
			</div>
			<p class="thumb-heading metadata" v-html="header" />
			<div class="slide-snippet" v-html="snippet" />
			<div v-if="media" class="slide-snippet has-media">
				<span class="icon is-tiny">
					<i class="fa" :class="media.icon" />
				</span>
				{{media.text}}
			</div>
			<div v-if="page" class="page">{{page}}</div>
		</div>
	</wnl-slide-link>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'
	@import 'resources/assets/sass/mixins'
	$thumb-height: 210px
	$thumb-width: 290px

	.slide-router-link
		color: $color-darkest-gray
		margin: $margin-small $margin-small $margin-base

	.slide-context
		margin-bottom: $margin-small

		.group-and-lesson
			align-items: center
			display: flex
			font-size: $font-size-minus-2
			justify-content: center

			.group-name
				letter-spacing: 1px
				text-transform: uppercase

		.section-name
			font-size: $font-size-minus-2
			font-weight: $font-weight-bold
			line-height: $line-height-minus
			text-align: center

	.slide-thumb
		border: $border-light-gray
		cursor: pointer
		flex: 1 0 $thumb-width
		height: $thumb-height
		max-width: $thumb-width
		overflow-y: auto
		padding: $margin-small
		text-align: center
		transition: color $transition-length-base
		width: $thumb-width

		&:hover
			color: $color-ocean-blue
			transition: color $transition-length-base

		.thumb-meta
			align-items: center
			display: flex
			justify-content: space-between

			.slide-number
				font-size: $font-size-minus-3
				line-height: $line-height-minus
				margin-bottom: $margin-tiny

		.thumb-heading
			line-height: $line-height-minus
			margin-bottom: $margin-small
			padding: 0 $margin-base

		.thumb-subheading
			font-size: $font-size-minus-2
			line-height: $line-height-minus
			margin-bottom: $margin-small
			padding: 0 $margin-base
			text-transform: uppercase

		.slide-snippet
			font-size: $font-size-minus-1
			line-height: $line-height-minus

			&.has-media
				margin-top: $margin-small

		.page
			font-size: $font-size-minus-3
			line-height: $line-height-minus
			margin-top: $margin-small
</style>

<script>
import { get, truncate } from 'lodash';
import { mapGetters } from 'vuex';

import SlideLink from 'js/components/global/SlideLink';

const mediaMap = {
	chart: {
		icon: 'fa-sitemap',
		text: 'Diagram',
	},
	movie: {
		icon: 'fa-film',
		text: 'Film',
	},
	audio: {
		icon: 'fa-music',
		text: 'Nagranie audio',
	},
};

export default {
	name: 'SlideSearchResult',
	components: {
		'wnl-slide-link': SlideLink,
	},
	props: {
		hit: {
			required: true,
			type: Object,
		},
	},
	computed: {
		...mapGetters('course', ['getLesson', 'getGroupsByLessonId']),
		context() {
			return this.hit._source.context;
		},
		content() {
			return this.hit._source.content;
		},
		groupNames() {
			return this.getGroupsByLessonId(this.lessonId).map(group => group.name);
		},
		header() {
			return this.getHighlight(this.hit, 'snippet.header') || this.hit._source.snippet.header;
		},
		id() {
			return this.hit._source.id;
		},
		lessonId() {
			return this.context.lesson.id;
		},
		lessonName() {
			return this.getLesson(this.lessonId).name;
		},
		media() {
			return this.hit._source.snippet && this.hit._source.snippet.media !== null ? mediaMap[this.hit._source.snippet.media] : null;
		},
		page() {
			return this.hit._source.snippet.page;
		},
		sectionName() {
			return this.context.section.name;
		},
		slideNumber() {
			return this.context.orderNumber + 1;
		},
		snippet() {
			return this.getHighlight(this.hit, 'snippet.content') || this.hit._source.snippet.content;
		},
	},
	methods: {
		getHighlight(hit, key) {
			const highlight = get(hit, `highlight["${key}"]`);

			if (Array.isArray(highlight)) {
				return highlight.join('...');
			}

			return highlight;
		},
		onThumbnailClick(event) {
			if (!event.metaKey && !event.ctrlKey) {
				this.$emit('resultClicked');
			}
		},
		truncate(text, length) {
			return truncate(text, { length });
		},
	}
};
</script>
