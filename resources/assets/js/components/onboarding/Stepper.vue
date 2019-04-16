<template>
	<div class="o-stepper">
		<div
			v-for="(step, index) in steps"
			:key="index"
			class="o-stepper__step"
			:class="{
				'-active': index === currentStep,
				'-done': index < currentStep,
			}"
		>
			<router-link v-if="index < currentStep" :to="step.linkTo">{{index + 1}}. {{step.text}}</router-link>
			<a v-else>{{index + 1}}. {{step.text}}</a>
		</div>
	</div>
</template>

<script>
export default {
	props: {
		steps: {
			type: Array,
			required: true,
		},
		currentStep: {
			type: Number,
			required: true,
		},
	},
};
</script>
