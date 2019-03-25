<template>
	<div class="wnl-app-layout wnl-course-layout">
		<wnl-sidenav-slot
			:is-visible="isSidenavVisible"
			:is-detached="!isSidenavMounted"
			:is-narrow="true"
		>
			<wnl-main-nav :is-horizontal="!isSidenavMounted"></wnl-main-nav>
		</wnl-sidenav-slot>
		<div class="wnl-course-content wnl-column">
			<wnl-stepper
				:steps="stepsForStepper"
				:current-step="currentStepIndex"
				v-if="!currentStep.hideOnStepper"
			/>
			<component :is="currentStepComponent"/>
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
import axios from 'axios';
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

import {getApiUrl} from 'js/utils/env';
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
			default: 'welcome'
		}
	},
	data() {
		return {
			steps: [
				{
					name: ONBOARDING_STEPS.WELCOME,
					component: WnlOnboardingScreenWelcome,
					hideOnStepper: true,
					link_to: {name: 'onboarding', params: {step: ONBOARDING_STEPS.WELCOME}},
				},
				{
					name: ONBOARDING_STEPS.LEARNING_STYLE,
					component: WnlOnboardingScreenLearningStyle,
					text: '5 sposobów',
					link_to: {name: 'onboarding', params: {step: ONBOARDING_STEPS.LEARNING_STYLE}},
				},
				{
					name: ONBOARDING_STEPS.USER_PLAN,
					component: WnlOnboardingScreenUserPlan,
					text: 'Plan pracy',
					link_to: {name: 'onboarding', params: {step: ONBOARDING_STEPS.USER_PLAN}},
				},
				{
					name: ONBOARDING_STEPS.TUTORIAL,
					component: WnlOnboardingScreenTutorial,
					text: 'Wideo',
					link_to: {name: 'onboarding', params: {step: ONBOARDING_STEPS.TUTORIAL}},
				},
				{
					name: ONBOARDING_STEPS.SATISFACTION_GUARANTEE,
					component: WnlOnboardingScreenSatisfactionGuarantee,
					text: 'Gwarancja ',
					link_to: {name: 'onboarding', params: {step: ONBOARDING_STEPS.SATISFACTION_GUARANTEE}},
				},
				{
					name: ONBOARDING_STEPS.FINAL,
					component: WnlOnboardingScreenFinal,
					text: 'Powitanie',
					link_to: {name: 'onboarding', params: {step: ONBOARDING_STEPS.FINAL}},
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
		currentStepIndex() {
			return this.stepsForStepper.findIndex(({name}) => name === this.step);
		},
		stepsForStepper() {
			return this.steps.filter(step => !step.hideOnStepper);
		},
	},
	methods: {
		...mapActions(['setupCurrentUser']),
		updateUserProductState() {
			axios.put(getApiUrl(`users/${this.currentUserId}/user_product_state/latest`), {
				wizard_step: this.step
			});
		}
	},
	watch: {
		step() {
			this.updateUserProductState();
		}
	},
	mounted() {
		this.updateUserProductState();
	}
};
</script>
