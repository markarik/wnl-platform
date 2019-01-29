<template>
	<div class="item" :class="[itemClass, { disabled: item.isDisabled }]">
		<router-link
			class="item-wrapper"
			:class="{'router-link-exact-active': screenItem.active, 'is-disabled': screenItem.isDisabled, 'is-completed': screenItem.completed}"
			:to="to"
		>
			<span class="sidenav-item-content">
				{{screenItem.text}}
				<span class="sidenav-item-meta" v-if="hasMeta">{{screenItem.meta}}</span>
			</span>
		</router-link>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

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
	name: 'ScreenItem',
	props: {
		item: {
			type: Object,
			required: true
		},
	},
	computed: {
		...mapGetters('progress', {
			getScreenProgress: 'getScreen',
		}),
		itemClass() {
			return this.screenItem.itemClass;
		},
		to() {
			return this.screenItem.to;
		},
		hasMeta() {
			return typeof this.screenItem.meta !== 'undefined' && this.screenItem.meta.length > 0;
		},
		screenItem() {
			const screen = this.item;
			const params = {
				courseId: 1,
				lessonId: this.$route.params.lessonId,
				screenId: this.item.id,
			};
			const completed = this.getScreenProgress(1, params.lessonId, screen.id);
			const itemProps = {
				text: screen.name,
				itemClass: 'todo',
				routeName: 'screens',
				routeParams: params,
				completed
			};

			if (screen.slides_count) {
				return navigation.composeItem({...itemProps, meta: `(${screen.slides_count})`});
			}
			return navigation.composeItem(itemProps);
		}
	},
	methods: {
		hasClass(className) {
			return !!this.itemClass && this.itemClass.indexOf(className) > -1;
		}
	},
};
</script>
