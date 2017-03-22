<template>
	<div>
		<progress class="progress is-large" :class="modifyingClass" :value="value" :max="maxValue">{{ percentageValue }}</progress>
		<span v-if="hasNumbers" class="progress-numbers" :class="modifyingClass">
			{{ value }}/{{ max }}
		</span>
	</div>
</template>

<script>
	export default {
		name: 'Progress',
		props: ['value', 'max', 'hasNumbers'],
		computed: {
			maxValue() {
				return this.max || 100
			},
			percentageValue() {
				return `${Math.round(this.value/this.max)}%`
			},
			modifyingClass() {
				if (this.max - this.value > 5) {
					return 'is-warning'
				} else if (this.max - this.value > 10) {
					return 'is-danger'
				}

				return 'is-success'
			},
		},
	}
</script>
