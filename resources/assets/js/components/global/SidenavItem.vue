<template>
	<li :class="[itemClass, { disabled: isDisabled }]">
		<router-link v-if="isLink" :to="to" :replace="replace">
			<slot></slot>
		</router-link>
		<span v-else>
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
