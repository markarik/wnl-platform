<template>
	<div class="o-stepper">
		<div class="o-stepper__step"
			 v-for="(step, index) in steps"
			 :key="index"
			 :class="{
			 	'-active': index === currentStep,
			 	'-done': index < currentStep,
			 }"
		>
			<router-link :to="step.link_to">{{index + 1}}. {{step.text}}</router-link>
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
			required: false,
		},
	},
};
</script>
