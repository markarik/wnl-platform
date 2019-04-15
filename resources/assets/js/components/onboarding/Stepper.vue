<template>
	<div class="o-stepper">
		<div
			class="o-stepper__step"
			v-for="(step, index) in steps"
			:key="index"
			:class="{
				'-active': index === currentStep,
				'-done': index < currentStep,
			}"
		>
			<router-link :to="step.linkTo" v-if="index < currentStep">{{index + 1}}. {{step.text}}</router-link>
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
