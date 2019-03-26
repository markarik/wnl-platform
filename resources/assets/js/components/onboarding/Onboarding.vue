<template>
	<div class="wnl-app-layout wnl-course-layout">
		<wnl-sidenav-slot
			:is-visible="isSidenavVisible"
			:is-detached="!isSidenavMounted"
			:is-narrow="true"
		>
			<wnl-main-nav :is-horizontal="!isSidenavMounted"></wnl-main-nav>
		</wnl-sidenav-slot>
		<div class="onboarding-wrapper">
			<wnl-stepper
				class="onboarding-stepper"
				:steps="stepsForStepper"
				:current-step="currentStepIndexForStepper"
				v-if="!currentStep.hideOnStepper"
			/>
			<component
				class="scrollable-container"
				v-if="currentStep"
				:is="currentStep.component"
			/>
			<div class="has-text-centered buttons">
				<button
					v-if="isLastStep"
					class="button is-secondary"
					@click="onCancelClick"
				>Pomijam Wstępny LEK</button>
				<button
					class="button is-primary"
					@click="onNextClick"
				>{{currentStep.buttonText || 'Dalej'}}</button>
			</div>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-course-layout
		justify-content: space-between

	.wnl-course-content
		max-width: $course-content-max-width
		flex: $course-content-flex 0 0
		position: relative
		overflow-y: hidden

	.onboarding-wrapper
		display: flex
		flex-direction: column
		width: 100%

	.scrollable-container, .onboarding-stepper
		max-width: 730px
		margin-left: auto
		margin-right: auto

	.scrollable-container
		flex-grow: 1
		padding: $margin-base

		/deep/ .row
			display: flex

		/deep/ .row-item
			flex: 1 1 50%
			padding-right: $margin-base

	.buttons
		border-top: $border-light-gray
		padding: $margin-base

</style>

<script>
import {mapGetters, mapActions} from 'vuex';

import WnlSidenavSlot from 'js/components/global/SidenavSlot';
import WnlMainNav from 'js/components/MainNav';
import WnlOnboardingScreenWelcome from 'js/components/onboarding/OnboardingScreenWelcome';
import WnlOnboardingScreenLearningStyle from 'js/components/onboarding/OnboardingScreenLearningStyle';
import WnlOnboardingScreenUserPlan from 'js/components/onboarding/OnboardingScreenUserPlan';
import WnlOnboardingScreenTutorial from 'js/components/onboarding/OnboardingScreenTutorial';
import WnlOnboardingScreenSatisfactionGuarantee from 'js/components/onboarding/OnboardingScreenSatisfactionGuarantee';
import WnlOnboardingScreenFinal from 'js/components/onboarding/OnboardingScreenFinal';
import WnlStepper from 'js/components/onboarding/Stepper';

import {resource} from 'js/utils/config';
import {ONBOARDING_STEPS} from 'js/consts/user';

export default {
	components: {
		WnlSidenavSlot,
		WnlMainNav,
		WnlStepper,
	},
	props: {
		step: {
			type: String,
			required: false,
		}
	},
	data() {
		return {
			steps: [
				{
					name: ONBOARDING_STEPS.WELCOME,
					component: WnlOnboardingScreenWelcome,
					hideOnStepper: true,
					linkTo: {name: 'onboarding', params: {step: ONBOARDING_STEPS.WELCOME}},
				},
				{
					name: ONBOARDING_STEPS.LEARNING_STYLE,
					component: WnlOnboardingScreenLearningStyle,
					text: '5 sposobów',
					linkTo: {name: 'onboarding', params: {step: ONBOARDING_STEPS.LEARNING_STYLE}},
				},
				{
					name: ONBOARDING_STEPS.USER_PLAN,
					component: WnlOnboardingScreenUserPlan,
					text: 'Plan pracy',
					linkTo: {name: 'onboarding', params: {step: ONBOARDING_STEPS.USER_PLAN}},
				},
				{
					name: ONBOARDING_STEPS.TUTORIAL,
					component: WnlOnboardingScreenTutorial,
					text: 'Wideo',
					linkTo: {name: 'onboarding', params: {step: ONBOARDING_STEPS.TUTORIAL}},
				},
				{
					name: ONBOARDING_STEPS.SATISFACTION_GUARANTEE,
					component: WnlOnboardingScreenSatisfactionGuarantee,
					text: 'Gwarancja ',
					linkTo: {name: 'onboarding', params: {step: ONBOARDING_STEPS.SATISFACTION_GUARANTEE}},
					buttonText: 'Rozumiem, dalej',
				},
				{
					name: ONBOARDING_STEPS.FINAL,
					component: WnlOnboardingScreenFinal,
					text: 'Powitanie',
					linkTo: {name: 'onboarding', params: {step: ONBOARDING_STEPS.FINAL}},
					buttonText: 'Otwórz Wstępny LEK',
				},
			]
		};
	},
	computed: {
		...mapGetters([
			'currentUser',
			'currentUserId',
			'isSidenavVisible',
			'isSidenavMounted',
		]),
		currentStep() {
			return this.steps.find(({name}) => name === this.step);
		},
		nextStep() {
			return this.steps[this.currentStepIndex + 1] || null;
		},
		isLastStep() {
			return this.nextStep === null;
		},
		currentStepIndex() {
			return this.steps.findIndex(({name}) => name === this.step);
		},
		currentStepIndexForStepper() {
			return this.stepsForStepper.findIndex(({name}) => name === this.step);
		},
		stepsForStepper() {
			return this.steps.filter(step => !step.hideOnStepper);
		},
	},
	methods: {
		...mapActions([
			'setupCurrentUser',
			'updateUserProductState',
		]),
		validateCurrentStep() {
			const lastStep = this.currentUser.productState.onboarding_step;

			if (lastStep === ONBOARDING_STEPS.FINISHED) {
				return this.$router.replace({name: resource('courses'), params: {courseId: 1}});
			}

			if (this.currentStepIndex === -1) {
				let stepToRedirectTo;

				if (lastStep) {
					stepToRedirectTo = this.steps.find(({name}) => name === lastStep);
				} else {
					stepToRedirectTo = this.steps[0];
				}

				this.$router.replace(stepToRedirectTo.linkTo);
				return;
			}

			let maxStepIndex = null;

			if (!lastStep) {
				maxStepIndex = 0;
			} else {
				maxStepIndex = this.steps.findIndex(({name}) => name === lastStep);
			}

			if (this.currentStepIndex > maxStepIndex) {
				this.$router.replace(this.steps[maxStepIndex].linkTo);
			}
		},
		async onNextClick() {
			await this.updateUserProductState({
				onboarding_step: this.nextStep ? this.nextStep.name : ONBOARDING_STEPS.FINISHED,
			});
			this.$router.replace(this.nextStep ? this.nextStep.linkTo : {name: resource('lessons'), params: {courseId: 1, lessonId: 85}});
		},
		async onCancelClick() {
			await this.updateUserProductState({
				onboarding_step: ONBOARDING_STEPS.FINISHED,
			});
			this.$router.replace({name: resource('courses'), params: {courseId: 1}});
		},
	},
	watch: {
		step() {
			this.validateCurrentStep();
		}
	},
	mounted() {
		this.validateCurrentStep();
	}
};
</script>
