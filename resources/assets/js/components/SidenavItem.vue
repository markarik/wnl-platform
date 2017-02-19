<template>
	<li :class="itemClass">
		<router-link :to="to" v-if="isLink">
			<slot></slot>
		</router-link>
		<span v-else>
			<slot></slot>
		</span>
	</li>
</template>

<style lang="sass">
	@import 'resources/assets/sass/variables'

	.wnl-sidenav-item
		padding: 5px 0

	.wnl-sidenav-item-groups

	.wnl-sidenav-item-lessons,
	.wnl-sidenav-item-screens
		font-size: $font-size-minus-1

	.wnl-sidenav-item-sections
		font-size: $font-size-minus-2
		padding-left: 10px
</style>

<script>
	export default {
		name: 'SidenavItem',
		props: ['type', 'id', 'ancestors'],
		computed: {
			to() {
				if (this.type === 'courses') {
					return {
						name: this.type,
						params: {
							courseId: this.id
						}
					}
				} else if (this.type === 'lessons') {
					return {
						name: this.type,
						params: {
							courseId: this.ancestors.courses,
							lessonId: this.id
						}
					}
				} else if (this.type === 'screens') {
					return {
						name: this.type,
						params: {
							courseId: this.ancestors.courses,
							lessonId: this.ancestors.lessons,
							screenId: this.id,
						}
					}
				} else if (this.type === 'sections') {
					return {
						name: 'screens',
						params: {
							courseId: this.ancestors.courses,
							lessonId: this.ancestors.lessons,
							screenId: this.ancestors.screens,
							slide:    this.slide
						}
					}
				}
			},
			itemClass() {
				return 'wnl-sidenav-item wnl-sidenav-item-' + this.type
			},
			isLink() {
				return this.type !== 'groups'
			}
		}
	}
</script>
