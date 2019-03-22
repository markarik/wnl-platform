<template>
	<div class="wnl-app-layout wnl-course-layout">
		<wnl-sidenav-slot
			:is-visible="isSidenavVisible"
			:is-detached="!isSidenavMounted"
		>
			<wnl-main-nav :is-horizontal="!isSidenavMounted"></wnl-main-nav>
		</wnl-sidenav-slot>
		<div class="wnl-course-content wnl-column">
			<component :is="stepComponent"/>
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
import { mapGetters, mapActions } from 'vuex';
import SidenavSlot from 'js/components/global/SidenavSlot';
import MainNav from 'js/components/MainNav';
import WnlOnboardingScreenWelcome from 'js/components/onboarding/OnboardingScreenWelcome';
import WnlOnboardingScreenLearningStyle from 'js/components/onboarding/OnboardingScreenLearningStyle';
import WnlOnboardingScreenUserPlan from 'js/components/onboarding/OnboardingScreenUserPlan';
import WnlOnboardingScreenTutorial from 'js/components/onboarding/OnboardingScreenTutorial';
import WnlOnboardingScreenSatisfactionGuarantee from 'js/components/onboarding/OnboardingScreenSatisfactionGuarantee';
import WnlOnboardingScreenFinal from 'js/components/onboarding/OnboardingScreenFinal';

export default {
	components: {
		'wnl-sidenav-slot': SidenavSlot,
		'wnl-main-nav': MainNav,
	},
	props: {
		step: {
			type: String,
			default: 'welcome'
		}
	},
	data() {
		return {
			stepComponents: {
				'welcome': WnlOnboardingScreenWelcome,
				'learning-style': WnlOnboardingScreenLearningStyle,
				'user-plan': WnlOnboardingScreenUserPlan,
				'tutorial': WnlOnboardingScreenTutorial,
				'satisfaction-guarantee': WnlOnboardingScreenSatisfactionGuarantee,
				'final': WnlOnboardingScreenFinal,
			}
		};
	},
	computed: {
		...mapGetters([
			'currentUser',
			'isSidenavVisible',
			'isSidenavMounted',
		]),
		stepComponent() {
			return this.stepComponents[this.step];
		}
	},
	methods: {
		...mapActions(['setupCurrentUser']),
	},
};
</script>
