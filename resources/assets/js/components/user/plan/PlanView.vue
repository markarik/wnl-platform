<template>
	<div class="scrollable-main-container">
		<div class="level wnl-screen-title tabs" v-if="!isMobile">
			<ul>
				<li
					v-for="(tab, index) in tabs"
					@click="activeTabIndex = index"
					:class="[index === activeTabIndex ? 'is-active' : '', 'big', 'strong']"
					:key="tab.title"
				>
					<a>{{tab.title}}</a>
				</li>
			</ul>
		</div>
		<div class="dropdown-container" v-else>
			<wnl-dropdown>
				<div slot="activator" class="dropdown-trigger is-active">
					{{activeTab.title}}
					<span class="icon"><i class="fa fa-angle-down"></i></span>
				</div>
				<div slot="content" class="dropdown-menu">
					<div class="dropdown-menu__content">
						<ul>
							<li
								v-for="tab in tabs"
								@click="onTabSelect(tab)"
								:key="tab.title"
								class="dropdown-menu__item"
							>
								<a class="dropdown-menu__item">{{tab.title}}</a>
							</li>
						</ul>
					</div>
				</div>
			</wnl-dropdown>
		</div>
		<component :is="activeView" @userEvent="onUserEvent"/>
	</div>
</template>

<style lang="sass" scoped>
	$dropdownWidth: 250px

	.dropdown-container
		position: relative
		width: $dropdownWidth
		margin-bottom: 20px

	.dropdown-trigger
		border: 1px solid black
		border-radius: 5px
		width: $dropdownWidth
		height: 50px
		padding: 0 10px
		display: flex
		justify-content: space-between
		align-items: center
	.dropdown-menu
		width: $dropdownWidth
		&__item
			color: black
			padding: 10px 20px
</style>

<script>
import {mapGetters, mapState} from 'vuex';

import LessonsPlanner from './LessonsPlanner';
import PlannerGuide from './PlannerGuide';
import DownloadPlan from './DownloadPlan';
import Dropdown from 'js/components/global/Dropdown';
import emits_events from 'js/mixins/emits-events';
import context from 'js/consts/events_map/context.json';

export default {
	name: 'PlanView',
	components: { 'wnl-dropdown': Dropdown },
	mixins: [emits_events],
	data() {
		return {
			activeTabIndex: 0,
		};
	},
	computed: {
		...mapState('course', ['isPlanBuilderEnabled']),
		...mapGetters(['isMobile']),
		tabs() {
			const tabs = [{
				title: 'Pobierz plan pracy',
				component: DownloadPlan,
			}];
			if (this.isPlanBuilderEnabled) {
				tabs.unshift({
					title: 'Twój plan pracy',
					component: LessonsPlanner,
				},
				{
					title: 'Jak zmienić plan?',
					component: PlannerGuide,
				});
			}

			return tabs;
		},
		activeTab() {
			return this.tabs[this.activeTabIndex] || {};
		},
		activeView() {
			return this.activeTab.component;
		}
	},
	methods: {
		onUserEvent(payload) {
			this.emitUserEvent({
				subcontext: context.account.subcontext.course_plan.value,
				...payload
			});
		}
	},
};
</script>
