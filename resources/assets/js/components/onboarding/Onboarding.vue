<template>
	<div class="wnl-app-layout wnl-course-layout">
		<wnl-sidenav-slot
			:is-visible="isSidenavVisible"
			:is-detached="!isSidenavMounted"
			:is-narrow="true"
		>
			<wnl-main-nav :is-horizontal="!isSidenavMounted"></wnl-main-nav>
		</wnl-sidenav-slot>
		<div v-if="currentStep" class="scrollable-main-container">
			<wnl-stepper
				:steps="stepsForStepper"
				:current-step="currentStepIndexForStepper"
				v-if="!currentStep.hideOnStepper"
			/>
			<component
				:is="currentStepComponent"
			/>
			<div v-if="currentStep.customButtons">
				<button
					v-for="(customButton, index) in currentStep.customButtons"
					:class="customButton.class || 'button is-primary'"
					:key="index"
					@click="customButton.onClick"
				>{{customButton.label}}</button>
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
			required: false,
		}
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
		currentStepIndex() {
			return this.steps.findIndex(({name}) => name === this.step);
		},
		currentStepIndexForStepper() {
			return this.stepsForStepper.findIndex(({name}) => name === this.step);
		},
		stepsForStepper() {
			return this.steps.filter(step => !step.hideOnStepper);
		},
		steps() {
			return [
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
					customButtons: [
						{
							label: 'Rozumiem, dalej',
							onClick: () => {
								// eslint-disable-next-line vue/no-side-effects-in-computed-properties
								this.$router.push(this.getNextStepLink());
							},
						},
					],
				},
				{
					name: ONBOARDING_STEPS.FINAL,
					component: WnlOnboardingScreenFinal,
					text: 'Powitanie',
					linkTo: {name: 'onboarding', params: {step: ONBOARDING_STEPS.FINAL}},
					customButtons: [
						{
							class: 'button is-secondary',
							label: 'Pomijam Wstępny LEK',
							onClick: () => {
								// eslint-disable-next-line vue/no-async-in-computed-properties
								this.updateUserProductState({
									onboarding_step: ONBOARDING_STEPS.FINISHED
								}).then(() => {
									// eslint-disable-next-line vue/no-side-effects-in-computed-properties
									this.$router.push({name: resource('courses'), params: {courseId: 1}});
								});
							},
						},
						{
							label: 'Otwórz Wstępny LEK',
							onClick: () => {
								// eslint-disable-next-line vue/no-async-in-computed-properties
								this.updateUserProductState({
									onboarding_step: ONBOARDING_STEPS.FINISHED
								}).then(() => {
									// FIXME ids should be dynamic and come from backend
									// eslint-disable-next-line vue/no-side-effects-in-computed-properties
									this.$router.push({name: resource('lessons'), params: {courseId: 1, lessonId: 85}});
								});
							}
						}
					]
				},
			];
		}
	},
	methods: {
		...mapActions([
			'setupCurrentUser',
			'updateUserProductState',
		]),
		getNextStepLink() {
			const nextStep = this.steps[this.currentStepIndex + 1];

			return nextStep ? nextStep.linkTo : null;
		},
		validateAndSyncCurrentStep() {
			const lastStep = this.currentUser.productState.onboarding_step;

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
				const lastStepIndex = this.steps.findIndex(({name}) => name === lastStep);
				maxStepIndex = lastStepIndex + 1;
			}

			if (this.currentStepIndex > maxStepIndex) {
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
