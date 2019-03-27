<template>
	<div class="wnl-app-layout wnl-course-layout">
		<wnl-sidenav-slot
			:is-visible="isSidenavVisible"
			:is-detached="!isSidenavMounted"
			:is-narrow="true"
		>
			<wnl-main-nav :is-horizontal="!isSidenavMounted"></wnl-main-nav>
		</wnl-sidenav-slot>
		<div v-if="$currentEditionParticipant.isAllowed('access')" class="onboarding-wrapper">
			<wnl-stepper
				class="onboarding-stepper"
				:steps="stepsForStepper"
				:current-step="currentStepIndexForStepper"
				v-if="currentStep && !currentStep.hideOnStepper"
			/>
			<component
				class="scrollable-container"
				v-if="currentStep"
				:is="currentStep.component"
			/>
			<div class="has-text-centered buttons" v-if="currentStep">
				<button
					v-if="isLastStep"
					class="button is-secondary margin"
					:class="{
						'is-loading': isLoading
					}"
					@click="onCancelClick"
				>Pomijam Wstępny LEK</button>
				<button
					class="button is-primary margin"
					:class="{
						'is-loading': isLoading
					}"
					@click="onNextClick"
				>{{currentStep.buttonText || 'Dalej'}}</button>
			</div>
		</div>
		<wnl-splash-screen v-else/>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-course-layout
		justify-content: space-between
		overflow-x: hidden

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
		margin-left: auto
		margin-right: auto
		max-width: 760px
		width: 100%

	.onboarding-stepper
		flex-shrink: 0
		overflow-x: auto

		&::-webkit-scrollbar
			display: none

	.scrollable-container
		flex-grow: 1
		padding: $margin-big

		/deep/
			.row
				@media #{$media-query-tablet}
					display: flex

			.row-item
				display: flex
				flex: 1 1 50%
				padding-right: $margin-base
				margin-bottom: $margin-base

			.row-item-icon
				font-size: $font-size-plus-3
				margin-right: $margin-base

			.ordered-item
				margin: $margin-huge 0

				@media #{$media-query-tablet}
					display: flex

				.-purple-secondary
					border-color: $color-purple-second
					color: $color-purple-second

				.-sky-blue
					border-color: $color-sky-blue
					color: $color-sky-blue

				.-ocean-blue
					border-color: $color-ocean-blue
					color: $color-ocean-blue

				.-green
					border-color: $color-green
					color: $color-green

				.title
					text-align: center

					@media #{$media-query-tablet}
						text-align: left

			.ordered-item-number
				align-items: center
				border: 1px solid $color-purple
				border-radius: 100%
				color: $color-purple
				display: flex
				flex-shrink: 0
				font-size: $font-size-plus-4
				height: 64px
				justify-content: center
				margin: 0 auto $margin-base
				width: 64px

				@media #{$media-query-tablet}
					margin: 0 $margin-big 0 0

			.margin-bottom-medium
				margin-bottom: $margin-medium

			.margin-top-huge
				margin-top: $margin-huge


	.buttons
		border-top: $border-light-gray

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
import WnlSplashScreen from 'js/components/global/SplashScreen.vue';

import {resource} from 'js/utils/config';
import {ONBOARDING_STEPS} from 'js/consts/user';
import currentEditionParticipant from 'js/perimeters/currentEditionParticipant';
import {ALERT_TYPES} from 'js/consts/alert';

export default {
	perimeters: [currentEditionParticipant],
	components: {
		WnlMainNav,
		WnlSidenavSlot,
		WnlSplashScreen,
		WnlStepper,
	},
	props: {
		step: {
			type: String,
			default: null,
		}
	},
	data() {
		return {
			isLoading: false,
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
			'currentUserLastestProductState',
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
			'updateLatestProductState',
			'addAutoDismissableAlert',
		]),
		validateCurrentStep() {
			const lastStep = this.currentUserLastestProductState && this.currentUserLastestProductState.onboarding_step;

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
			await this.updateOnboardingStep(this.nextStep ? this.nextStep.name : ONBOARDING_STEPS.FINISHED);
			// TODO get lessonId for ldek and stop using hardcoded values
			this.$router.replace(this.nextStep ? this.nextStep.linkTo : {name: resource('lessons'), params: {courseId: 1, lessonId: 85}});
		},
		async onCancelClick() {
			await this.updateOnboardingStep(ONBOARDING_STEPS.FINISHED);
			this.$router.replace({name: resource('courses'), params: {courseId: 1}});
		},
		async updateOnboardingStep(step) {
			this.isLoading = true;
			try {
				await this.updateLatestProductState({
					onboarding_step: step,
				});

			} catch (error) {
				$wnl.logger.error(error);
				this.addAutoDismissableAlert({
					text: 'Coś poszło nie tak :(',
					type: ALERT_TYPES.ERROR,
				});
			} finally {
				this.isLoading = false;
			}
		}
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
