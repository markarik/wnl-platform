<template>
	<div class="item" :class="[itemClass, { disabled: item.isDisabled }]">
		<router-link
				class="item-wrapper"
				:class="{'router-link-exact-active': sectionItem.active, 'is-disabled': sectionItem.isDisabled, 'is-completed': sectionItem.completed}"
				:to="to"
		>
			<span class="sidenav-item-content">
				{{sectionItem.text}}
				<span class="sidenav-item-meta" v-if="hasMeta">{{sectionItem.meta}}</span>
			</span>
		</router-link>
		<wnl-subsection-item v-for="subsection in sectionSubsections" :key="subsection.id" :item="subsection"></wnl-subsection-item>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.item
		margin-left: $margin-base
	.item-wrapper
		height: 100%
		width: 100%
		user-select: none
		display: flex
		line-height: 1.5em
		padding: 7px 15px
		word-break: break-word
		word-wrap: break-word

	.sidenav-item-meta
		color: $color-background-gray
		font-size: $font-size-minus-3
		line-height: $line-height-plus

	.sidenav-item-content
		margin-left: 5px

	a
		transition: background-color $transition-length-base

		&.router-link-exact-active
			background: $color-background-lighter-gray
			font-weight: $font-weight-regular
			transition: background-color $transition-length-base

		&.is-active
			font-weight: $font-weight-regular

	.todo
		a:before
			color: $color-background-gray
			content: '○'
			margin-left: $margin-tiny

		a.is-completed:before
			content: '✓'
</style>

<script>
import {mapGetters} from 'vuex';
import navigation from 'js/services/navigation';
import {STATUS_COMPLETE, STATUS_IN_PROGRESS} from 'js/services/progressStore';
import WnlSubsectionItem from 'js/components/course/navigation/SubsectionItem';

export default {
	name: 'SectionItem',
	components: {
		WnlSubsectionItem
	},
	props: {
		item: {
			type: Object,
			required: true
		},
	},
	computed: {
		...mapGetters(['lessonState']),
		...mapGetters('course', ['getSubsectionsForSection']),
		...mapGetters('progress', {
			getSectionProgress: 'getSection'
		}),
		lessonId() {
			return this.$route.params.lessonId;
		},
		courseId() {
			return this.$route.params.courseId;
		},
		screenId() {
			return this.item.screens;
		},
		itemClass() {
			return this.sectionItem.itemClass;
		},
		to() {
			return this.sectionItem.to;
		},
		hasMeta() {
			return typeof this.sectionItem.meta !== 'undefined' && this.sectionItem.meta.length > 0;
		},
		sectionItem() {
			const section = this.item;
			const params = {
				courseId: this.courseId,
				lessonId: this.lessonId,
				screenId: this.screenId,
				slide: section.slide,
			};
			const isSectionActive = this.lessonState.activeSection === section.id;
			return navigation.composeItem({
				text: section.name,
				itemClass: 'small subitem todo',
				routeName: 'screens',
				routeParams: params,
				method: 'replace',
				iconClass: 'fa-angle-right',
				iconTitle: section.name,
				completed: this.getSectionProgress(this.courseId, this.lessonId, this.screenId, section.id),
				active: isSectionActive,
				meta: `(${section.slidesCount})`
			});
		},
		sectionSubsections() {
			return this.getSubsectionsForSection(this.item.id);
		}
	},
	methods: {
		hasClass(className) {
			return !!this.itemClass && this.itemClass.indexOf(className) > -1;
		},
	},
};
</script>
