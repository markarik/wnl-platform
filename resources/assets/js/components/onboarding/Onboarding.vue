<template>
	<div class="wnl-app-layout wnl-course-layout">
		<wnl-sidenav-slot
			:is-visible="isSidenavVisible"
			:is-detached="!isSidenavMounted"
			:is-narrow="true"
		>
			<wnl-main-nav :is-horizontal="!isSidenavMounted"></wnl-main-nav>
		</wnl-sidenav-slot>
		<div class="scrollable-main-container">
			<wnl-stepper
				:steps="stepsForStepper"
				:current-step="currentStepIndexForStepper"
				v-if="!currentStep.hideOnStepper"
			/>
			<component
				:is="currentStepComponent"
			/>
			<div v-if="currentStep.getCustomButtons">
				<router-link
					v-for="(customButton, index) in currentStep.getCustomButtons()"
					:class="customButton.class || 'button is-primary'"
					:key="index"
					:to="customButton.linkTo || getNextStepLink()"
				>{{customButton.label}}</router-link>
			</div>
			<router-link
				v-else
				class="button is-primary"
				:to="getNextStepLink()"
			>Dalej</router-link>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.wnl-course-layout
		justify-content: space-between

	.wnl-course-content
		max-width: $course-content-max-width
		flex: $course-content-flex 0 0
		position: relative
		overflow-y: hidden
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
			default: ONBOARDING_STEPS.WELCOME,
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
					getCustomButtons: () => [
						{
							label: 'Rozumiem, dalej',
						},
					],
				},
				{
					name: ONBOARDING_STEPS.FINAL,
					component: WnlOnboardingScreenFinal,
					text: 'Powitanie',
					linkTo: {name: 'onboarding', params: {step: ONBOARDING_STEPS.FINAL}},
					getCustomButtons: () => [
						{
							class: 'button is-secondary',
							label: 'Pomijam Wstępny LEK',
							linkTo: {name: resource('courses'), params: {courseId: 1}}
						},
						{
							label: 'Otwórz Wstępny LEK',
							// FIXME ids should be dynamic and come from backend
							linkTo: {name: resource('lessons'), params: {courseId: 1, lessonId: 85}}
						}
					]
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
		currentStepComponent() {
			return this.currentStep.component;
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
		getNextStepLink() {
			const currentStepIndex = this.steps.findIndex(({name}) => name === this.step);
			const nextStep = this.steps[currentStepIndex + 1];

			return nextStep ? nextStep.linkTo : null;
		},
		validateAndSyncCurrentStep() {
			const currentStepIndex = this.steps.findIndex((step) => step.name === this.step);

			if (currentStepIndex === -1) {
				this.$router.replace(this.steps[0].linkTo);
				return;
			}

			// TODO Why do we need .productState? We don't need it for isOnboardingPassed. Unify if possible.
			const lastStep = this.currentUser.productState.onboarding_step;
			let maxStepIndex = null;

			if (!lastStep) {
				maxStepIndex = 0;
			} else {
				const lastStepIndex = this.steps.findIndex((step) => step.name === lastStep);
				maxStepIndex = lastStepIndex + 1;
			}

			if (currentStepIndex > maxStepIndex) {
				this.$router.replace(this.steps[maxStepIndex].linkTo);
			} else {
				this.updateUserProductState({
					onboarding_step: this.step
				});
			}
		}
	},
	watch: {
		step() {
			this.validateAndSyncCurrentStep();
		}
	},
	mounted() {
		this.validateAndSyncCurrentStep();
	}
};
</script>
