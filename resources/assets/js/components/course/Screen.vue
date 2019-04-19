<template>
	<div>
		<h4>{{name}}</h4>
		<component
			:is="component"
			:key="screenData.id"
			:screen-data="screenData"
			:context="model"
			@userEvent="proxyUserEvent"
		/>
		<wnl-qna
			v-if="showQna"
			:sorting-enabled="true"
			:context-tags="tags"
			class="wnl-screen-qna"
			:discussion-id="screenData.discussion_id"
		/>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-screen-qna
		margin: $margin-huge 0
</style>

<script>
import { mapActions, mapGetters } from 'vuex';

import End from 'js/components/course/screens/End';
import Html from 'js/components/course/screens/Html';
import MockExam from 'js/components/course/screens/MockExam';
import Flashcards from 'js/components/course/screens/flashcards/Flashcards';
import Qna from 'js/components/qna/Qna';
import Quiz from 'js/components/quiz/Quiz';
import Slideshow from 'js/components/course/screens/slideshow/Slideshow';
import Widget from 'js/components/course/screens/Widget';
import emits_events from 'js/mixins/emits-events';
import features from 'js/consts/events_map/features.json';

const TYPES_MAP = {
	end: {
		component: 'wnl-end',
		feature_component: features.screen.feature_components.bonuses.value
	},
	html: {
		component: 'wnl-html',
		feature_component: features.screen.feature_components.html.value
	},
	slideshow: {
		component: 'wnl-slideshow',
		feature_component: features.screen.feature_components.slideshow.value
	},
	quiz: {
		component: 'wnl-quiz',
		feature_component: features.screen.feature_components.quiz_set.value
	},
	widget: {
		component: 'wnl-widget',
		feature_component: features.screen.feature_components.widget.value
	},
	mockexam: {
		component: 'wnl-mock-exam',
		feature_component: features.screen.feature_components.mockexam.value
	},
	flashcards: {
		component: 'wnl-flashcards',
		feature_component: features.screen.feature_components.flashcards.value
	},
};

export default {
	name: 'Screen',
	components: {
		'wnl-end': End,
		'wnl-html': Html,
		'wnl-mock-exam': MockExam,
		'wnl-qna': Qna,
		'wnl-quiz': Quiz,
		'wnl-slideshow': Slideshow,
		'wnl-widget': Widget,
		'wnl-flashcards': Flashcards
	},
	mixins: [emits_events],
	props: {
		screenId: {
			type: Number,
			required: true,
		},
	},
	data() {
		return {
			model: 'App\\Models\\Screen'
		};
	},
	computed: {
		...mapGetters('course', [
			'getScreen',
		]),
		screenData() {
			return this.getScreen(this.screenId);
		},
		name() {
			return this.screenData.name;
		},
		type() {
			return this.screenData.type;
		},
		tags() {
			return this.screenData.tags;
		},
		component() {
			if (!this.type) return;

			return TYPES_MAP[this.type].component;
		},
		showQna() {
			return this.screenData.is_discussable;
		},
		eventFeatureComponent() {
			if (!this.type) return;

			return TYPES_MAP[this.type].feature_component;
		}
	},
	watch: {
		screenId() {
			this.showQna && this.fetchQuestionsForDiscussion({ discussionId: this.screenData.discussion_id });
			this.trackScreenOpen();
		}
	},
	mounted() {
		this.showQna && this.fetchQuestionsForDiscussion(this.screenData.discussion_id);
		this.trackScreenOpen();
	},
	methods: {
		...mapActions('qna', ['fetchQuestionsForDiscussion']),
		trackScreenOpen() {
			this.emitUserEvent({
				feature: features.screen.value,
				feature_component: this.eventFeatureComponent,
				action: features.screen.actions.open.value,
				target: this.screenId
			});
		}
	},
};
</script>
