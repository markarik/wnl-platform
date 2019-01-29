<template>
	<div class="item" :class="[itemClass, { disabled: item.isDisabled }]">
		<router-link
				class="item-wrapper"
				:class="{'router-link-exact-active': subsectionItem.active, 'is-disabled': subsectionItem.isDisabled, 'is-completed': subsectionItem.completed}"
				:to="to"
		>
			<span class="sidenav-item-content">
				{{subsectionItem.text}}
				<span class="sidenav-item-meta" v-if="hasMeta">{{subsectionItem.meta}}</span>
			</span>
		</router-link>
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

export default {
	name: 'SubsectionItem',
	props: {
		item: {
			type: Object,
			required: true
		},
	},
	computed: {
		...mapGetters(['lessonState']),
		...mapGetters('progress', {
			getSectionProgress: 'getSection'
		}),
		lessonId() {
			return this.item.lessons;
		},
		courseId() {
			return this.$route.params.courseId;
		},
		sectionId() {
			return this.item.sections;
		},
		screenId() {
			return this.item.screens;
		},
		itemClass() {
			return this.subsectionItem.itemClass;
		},
		to() {
			return this.subsectionItem.to;
		},
		hasMeta() {
			return typeof this.subsectionItem.meta !== 'undefined' && this.subsectionItem.meta.length > 0;
		},
		subsectionItem() {
			const subsection = this.item;
			const params = {
				courseId: this.courseId,
				lessonId: this.lessonId,
				screenId: this.screenId,
				slide: subsection.slide,
			};
			const isSubsectionActive = this.lessonState.activeSubsection === subsection.id;
			const sectionProgress = this.getSectionProgress(this.courseId, this.lessonId, this.screenId, this.sectionId) || {}
			return navigation.composeItem({
				text: subsection.name,
				itemClass: 'small subitem todo',
				routeName: 'screens',
				routeParams: params,
				method: 'replace',
				iconClass: 'fa-angle-right',
				iconTitle: subsection.name,
				completed: sectionProgress[subsection.id],
				active: isSubsectionActive,
				meta: `(${subsection.slidesCount})`
			});
		},
	},
	methods: {
		hasClass(className) {
			return !!this.itemClass && this.itemClass.indexOf(className) > -1;
		},
	},
};
</script>
