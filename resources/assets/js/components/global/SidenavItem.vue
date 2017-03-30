<template>
	<li class="item" :class="[itemClass, { disabled: isDisabled }]">
		<span class="icon is-small" v-if="isTodo">
			<i title="W trakcie..." class="fa fa-dot-circle-o" v-if="isInProgress"></i>
			<i title="Zrobione!" class="fa fa-check-circle-o" v-else-if="isComplete"></i>
			<i title="Jeszcze przed Tobą" class="fa fa-circle-o" v-else></i>
		</span>
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
			isTodo() {
				return this.hasClass('todo')
			},
			isInProgress() {
				return this.hasClass('in-progress')
			},
			isComplete() {
				return this.hasClass('complete')
			},
			replace() {
				return this.method === 'replace'
			},
		},
		methods: {
			hasClass(className) {
				return this.itemClass.indexOf(className) > -1
			}
		},
	}
</script>
