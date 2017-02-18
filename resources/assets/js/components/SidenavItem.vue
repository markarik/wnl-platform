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
	.wnl-sidenav-item
		padding: 5px 0
</style>

<script>
	export default {
		name: 'SidenavItem',
		props: ['type', 'id', 'ancestors'],
		computed: {
			to() {
				if (this.type === 'lessons') {
					return {
						name: this.type,
						params: {
							cid: this.ancestors.courses,
							lid: this.id
						}
					}
				} else if (this.type === 'courses') {
					return {
						name: this.type,
						params: {
							cid: this.id
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
