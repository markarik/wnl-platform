<template>
	<li :class="itemClass">
		<router-link v-if="isLink" :to="to" :replace="replace">
			<slot></slot>
		</router-link>
		<span v-else :class="{ isDisabled: isDisabled }">
			<slot></slot>
		</span>
	</li>
</template>

<script>
	export default {
		name: 'SidenavItem',
		props: ['itemClass', 'to', 'isDisabled', 'method'],
		computed: {
			isLink() {
				return typeof this.to === 'object' && this.to.hasOwnProperty('name')
			},
			replace() {
				return this.method === 'replace'
			},
		}
	}
</script>
