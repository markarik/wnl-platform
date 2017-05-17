<template>
	<transition name="fade">
		<div class="notification" :class="cssClass">
			<button class="delete" @click="onDelete"></button>
			<slot></slot>
		</div>
	</transition>
</template>

<script>
	export default {
		name: 'Alert',
		props: ['cssClass', 'timestamp'],
		methods: {
			onDelete() {
				this.$emit('delete', this.timestamp)
			}
		}
	}
</script>
