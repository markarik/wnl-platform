<template>
	<div class="wnl-sidenav-group">
		<div class="wnl-sidenav-item-wrapper">
			<div class="wnl-sidenav-group-toggle" @click="toggleSubitems">
				<wnl-sidenav-item
					:item="item"
					:isGroupToggle="isGroupToggle"
					:toggleIcon="toggleIcon"
				>
					{{item.text}}
				</wnl-sidenav-item>
			</div>
			<ul class="wnl-sidenav-subitems" v-if="canRenderSubitems">
				<li v-for="(subitem, index) in item.subitems">
					<wnl-sidenav-item
						:item="subitem"
						:key="index"
					>
						{{subitem.text}}
					</wnl-sidenav-item>
				</li>
			</ul>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-sidenav-group-toggle
		cursor: pointer

</style>

<script>
	import SidenavItem from 'js/components/global/SidenavItem'
	import { mapGetters } from 'vuex'

	export default {
		components: {
			'wnl-sidenav-item': SidenavItem
		},
		name: 'SidenavGroup',
		props: ['item'],
		data() {
			return { isOpen: false }
		},
		computed: {
			...mapGetters('course', ['nextLesson']),
			canRenderSubitems() {
				return this.hasSubitems && this.isOpen
			},
			hasSubitems() {
				return this.item.subitems && this.item.subitems.length > -1
			},
			isGroupToggle() {
				return this.hasSubitems
			},
			toggleIcon() {
				return this.isOpen ? 'fa-chevron-down' : 'fa-chevron-right'
			}
		},
		watch: {
			nextLesson(val) {
				if (!this.item || !this.item.subitems) {
					return
				}

				const isCurrentlyInProgress = this.item.subitems.some((subitem) => {
					return subitem.to.params.lessonId === val.id
				})

				this.isOpen = isCurrentlyInProgress
			}
		},
		methods: {
			toggleSubitems() {
				this.isOpen = !this.isOpen
			}
		},
	}
</script>
