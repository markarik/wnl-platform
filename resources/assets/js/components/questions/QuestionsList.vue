<template>
	<div class="wnl-app-layout">
		<wnl-questions-navigation />
		<div class="wnl-middle wnl-app-layout-main">
			<div class="scrollable-main-container">
				<div class="questions-header">
					<div class="questions-breadcrumbs">
						<div class="breadcrumb">
							<span class="icon is-small"><i
								class="fa fa-check-square-o"
							/></span>
						</div>
						<div class="breadcrumb">
							<span class="icon is-small"><i
								class="fa fa-angle-right"
							/></span>
							<span>{{$t('questions.nav.solving')}}</span>
						</div>
					</div>
					<a
						v-if="isMobile"
						slot="heading"
						class="mobile-show-active-filters"
						@click="toggleChat"
					>
						<span>{{$t('questions.filters.show')}}</span>
						<span class="icon is-tiny">
							<i class="fa fa-sliders" />
						</span>
					</a>
				</div>
				<wnl-questions-solving
					v-if="computedQuestionsList.length > 0 || !fetchingQuestions"
					:active-filters="activeFiltersNames"
					:current-question="currentQuestion"
					:loading="fetchingQuestions || fetchingFilters"
					:get-reaction="computedGetReaction"
					:is-mobile="isMobile"
					:meta="meta"
					:questions-list-count="matchedQuestionsCount"
					:questions-current-page="questionsCurrentPage"
					:preset-options="presetOptionsToPass"
					:test-mode="testMode"
					:test-questions="testQuestions"
					:test-processing="testProcessing"
					:test-results="testResults"
					@buildTest="buildTest"
					@changeQuestion="onChangeQuestion"
					@changePage="onChangePage"
					@checkQuiz="verifyCheckQuestions"
					@endQuiz="verifyEndQuiz"
					@selectAnswer="onSelectAnswer"
					@setQuestion="setQuestion"
					@verify="onVerify"
					@userEvent="onUserEvent"
					@activeViewChange="onActiveViewChange"
					@updateTime="onUpdateTime"
				/>
				<div v-else class="text-loader">
					<wnl-text-loader />
				</div>
			</div>
		</div>
		<wnl-sidenav-slot
			:is-detached="!isChatMounted"
			:is-visible="isLargeDesktop || isChatVisible"
			:has-chat="true"
		>
			<wnl-questions-filters
				v-show="!testMode"
				:loading="fetchingQuestions || fetchingFilters"
				:active-filters="activeFilters"
				:fetching-data="fetchingQuestions || fetchingFilters"
				:filters="filters"
				@activeFiltersChanged="onActiveFiltersChanged"
				@search="onSearch"
			/>
		</wnl-sidenav-slot>
		<div
			v-if="!testMode && !isLargeDesktop && isChatToggleVisible"
			class="wnl-chat-toggle"
		>
			<span class="icon is-big" @click="toggleChat">
				<i class="fa fa-sliders" />
				<span>{{$t('questions.filters.show')}}</span>
			</span>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.wnl-app-layout-main
		width: 100%
		max-width: initial

	.mobile-show-active-filters
		font-size: $font-size-minus-2
		text-transform: uppercase

	.questions-header
		align-items: center
		display: flex
		justify-content: space-between
		margin-bottom: $margin-small

	.questions-breadcrumbs
		align-items: center
		color: $color-gray
		font-size: $font-size-minus-1
		display: flex
		margin-right: $margin-base

		.breadcrumb
			max-width: 200px
			overflow-x: hidden
			text-overflow: ellipsis
			white-space: nowrap

	.text-loader
		display: flex
		justify-content: center
</style>

<script>
import { isEmpty, get } from 'lodash';
import { mapGetters, mapActions, mapMutations, mapState } from 'vuex';
import { QUESTIONS_SET_TOKEN as setToken } from 'js/store/mutations-types';
import { VIEWS } from 'js/consts/questionsSolving';

import QuestionsFilters from 'js/components/questions/QuestionsFilters';
import QuestionsNavigation from 'js/components/questions/QuestionsNavigation';
import QuestionsSolving from 'js/components/questions/QuestionsSolving';
import SidenavSlot from 'js/components/global/SidenavSlot';

import { scrollToTop } from 'js/utils/animations';
import { swalConfig } from 'js/utils/swal';
import emits_events from 'js/mixins/emits-events';
import features from 'js/consts/events_map/features.json';
import context from 'js/consts/events_map/context.json';
import feature_components from 'js/consts/events_map/feature_components.json';
import { timeBaseOnQuestions } from 'js/services/testBuilder';
import examStateStore from 'js/services/examStateStore';

export default {
	name: 'QuestionsList',
	components: {
		'wnl-questions-navigation': QuestionsNavigation,
		'wnl-questions-filters': QuestionsFilters,
		'wnl-sidenav-slot': SidenavSlot,
		'wnl-questions-solving': QuestionsSolving,
	},
	mixins: [emits_events],
	props: {
		presetFilters: {
			default: () => [],
			type: Array,
		},
		presetOptions: {
			default: () => {},
			type: Object,
		}
	},
	data() {
		const currentContext = context.questions_bank;
		const currentFeature = features.quiz_questions;
		const featureComponent = currentFeature.feature_components.quiz_question;

		return {
			fetchingFilters: false,
			fetchingQuestions: true,
			orderedQuestionsList: [],
			showBuilder: false,
			testMode: false,
			testProcessing: false,
			testResults: {},
			reactionsFetched: false,
			presetOptionsToPass: isEmpty(this.presetOptions) ? {} : this.presetOptions,
			searchPhrase: '',
			context: currentContext,
			feature: currentFeature,
			featureComponent,
			activeView: ''
		};
	},
	computed: {
		...mapGetters([
			'isChatMounted',
			'isChatToggleVisible',
			'isChatVisible',
			'isMobile',
			'isLargeDesktop',
			'isSidenavMounted',
			'isSidenavVisible',
			'currentUserId',
			'currentUserHasFinishedEntryExam',
		]),
		...mapGetters('course', [
			'entryExamTagId'
		]),
		...mapGetters('questions', [
			'activeFilters',
			'activeFiltersObjects',
			'currentQuestion',
			'filters',
			'getQuestion',
			'getPage',
			'getReaction',
			'matchedQuestionsCount',
			'meta',
			'questionsCurrentPage',
			'questionsList',
			'testQuestions',
			'testQuestionsUnanswered',
			'getSafePage',
			'getAnswer',
		]),
		...mapState('questions', {
			'currentQuestionState': 'currentQuestion'
		}),
		activeFiltersNames() {
			return this.activeFiltersObjects.map(filter => {
				if (!filter) return;
				if (filter.type === 'search') {
					return filter.items[0] && filter.items[0].value && `Fraza: ${filter.items[0].value}`;
				}
				return filter.hasOwnProperty('name')
					? filter.name
					: filter.hasOwnProperty('message')
						? this.$t(`questions.filters.items.${filter.message}`)
						: this.$t(`questions.filters.items.${filter.value}`);
			});
		},
		computedGetReaction() {
			return this.reactionsFetched ? this.getReaction : () => {
			};
		},
		computedQuestionsList() {
			return this.orderedQuestionsList.length ? this.orderedQuestionsList : this.questionsList;
		},
		examMode() {
			return !!this.presetOptionsToPass.examMode && this.testMode;
		},
		examTagId() {
			return get(this.presetOptionsToPass, 'examTagId', 0);
		},
		initialFilters() {
			let filters = !isEmpty(this.presetFilters) ? this.presetFilters : this.activeFilters;

			if (get(this.presetOptions, 'examMode') && this.examTagId) {
				const filterName = 'by_taxonomy-exams';
				const filterIndex = this.filters[filterName].items.findIndex(item => {
					return item.value === this.examTagId;
				});
				filters = filterIndex > -1 ? [`${filterName}.items[${filterIndex}]`] : filters;
			}

			return filters;
		},
		examStateStoreKey() {
			return `wnl-exam-state-${this.currentUserId}`;
		}
	},
	watch: {
		testQuestionsCount() {
			this.estimatedTime = timeBaseOnQuestions(this.testQuestionsCount);
		},
		'$route.query.chatChannel'(newVal) {
			newVal && !this.isChatVisible && this.toggleChat();
		}
	},
	async mounted() {
		try {
			await this.setupQuestions();
			await this.restoreExamState();
		} catch (e) {
			$wnl.logger.error(e);
			this.fetchingFilters = false;
			this.switchOverlay(false);
		}
	},
	beforeRouteLeave(to, from, next) {
		if (this.testMode) {
			this.confirmQuizEnd()
				.then(() => next(false))
				.catch(() => {
					this.endQuiz();
					next();
				});
		} else {
			next();
		}
	},
	methods: {
		...mapActions(['toggleChat', 'toggleOverlay']),
		...mapActions('questions', [
			'addFilter',
			'activeFiltersSet',
			'activeFiltersToggle',
			'changeCurrentQuestion',
			'checkQuestions',
			'getPosition',
			'fetchQuestionData',
			'fetchQuestions',
			'fetchQuestionsByIds',
			'fetchPage',
			'fetchTestQuestions',
			'fetchDynamicFilters',
			'fetchQuestionsReactions',
			'resetCurrentQuestion',
			'resetPages',
			'resetTest',
			'resolveQuestion',
			'saveQuestionsResults',
			'savePosition',
			'selectAnswer',
			'setPage',
			'fetchActiveFilters',
		]),
		...mapMutations('questions', { setToken }),
		buildTest({ count }) {
			const text = this.presetOptionsToPass.hasOwnProperty('loadingText')
				? this.presetOptionsToPass.loadingText
				: 'testBuilding';

			this.switchOverlay(true, 'testBuilding', text);
			this.resetTest();
			this.testMode = true;
			this.fetchTestQuestions({
				activeFilters: this.activeFilters,
				count: count,
				randomize: !this.examMode,
			}).then(() => this.switchOverlay(false, 'testBuilding'));
		},
		changePage(page) {
			return new Promise((resolve) => {
				if (this.getPage(page)) {
					this.setPage(page);
					resolve();
					return;
				}

				this.switchOverlay(true);
				return this.fetchPage(page)
					.then(() => this.switchOverlay(false))
					.then(() => this.fetchQuestionsReactions(this.getPage(page)))
					.then(() => resolve());
			});
		},
		confirmQuizEnd(description = '') {
			const config = swalConfig({
				confirmButtonText: this.$t('questions.solving.confirm.yes'),
				cancelButtonText: this.$t('questions.solving.confirm.no'),
				reverseButtons: true,
				showCancelButton: true,
				showConfirmButton: true,
				title: this.$t('questions.solving.confirm.title'),
				type: 'question',
			});

			if (!isEmpty(description)) {
				config.html = description;
			}

			return new Promise((resolve, reject) => {
				this.$swal(config)
					.then(resolve)
					.catch(() => {
						examStateStore.remove(this.examStateStoreKey);
						return reject();
					});
			});
		},
		endQuiz() {
			this.testMode = this.testProcessing = false;
			examStateStore.remove(this.examStateStoreKey);
			this.testResults = {};
			this.trackFinshTest();
			this.resetTest();
		},
		fetchMatchingQuestions() {
			return this.fetchQuestions({ filters: this.activeFilters })
				.catch(error => $wnl.logger.error(error));
		},
		onActiveFiltersChanged(payload) {
			this.fetchingFilters = true;
			this.activeFiltersToggle(payload)
				.then(() => {
					if (!payload.refresh) return false;

					this.setToken();
					this.resetPages();
					this.resetCurrentQuestion();
					this.fetchDynamicFilters();
					this.setQuestion(this.currentQuestionState);
					return this.fetchMatchingQuestions();
				})
				.then(() => {
					this.fetchingFilters = false;

					if (!payload.refresh) return false;
					this.fetchQuestionsReactions(this.getPage(1));
				});
		},
		onChangeQuestion(step) {
			const currentIndex = this.currentQuestion.index;
			const currentPage = this.currentQuestion.page;
			const perPage = this.meta.perPage;
			const pageStep = Math.sign(step) * Math.ceil(Math.abs(step / perPage));

			let newIndex, newPage;

			if (step > 0 && currentIndex + step >= this.questionsCurrentPage.length) {
				newIndex = 0;
				newPage = currentPage === this.meta.lastPage ? 1 : currentPage + pageStep;
			} else if (step < 0 && currentIndex === 0) {
				newIndex = currentPage === 1 ? -1 : perPage - 1;
				newPage = currentPage === 1 ? this.meta.lastPage : currentPage + pageStep;
			} else {
				newPage = currentPage;
				newIndex = currentIndex + step;
			}

			this.setQuestion({ page: newPage, index: newIndex });
		},
		onChangePage(page) {
			scrollToTop();
			this.changePage(page);
		},
		onSelectAnswer(payload, useLocalStorage = true) {
			if (payload.answer === this.getQuestion(payload.id).selectedAnswer && !this.testMode) {
				this.onVerify(payload.id) || (payload.position && this.savePosition({ position: payload.position }));
			} else {
				this.selectAnswer(payload);
				useLocalStorage && this.persistStateInExamStateStorage();
			}
		},
		persistStateInExamStateStorage() {
			const persistedState = this.readPersistedState();
			this.testMode && examStateStore.set(this.examStateStoreKey, JSON.stringify({
				...persistedState,
				examTagId: this.examTagId,
				results: this.testQuestions.map(question => ({
					question_id: question.id,
					answer_id: question.answers[question.selectedAnswer] && question.answers[question.selectedAnswer].id
				}))
			}));
		},
		readPersistedState() {
			const persistedState = examStateStore.get(this.examStateStoreKey);
			let storedState = {};

			if (persistedState) {
				try {
					storedState = JSON.parse(persistedState);
				} catch (e) {
					$wnl.logger.warning(e);
				}
			}

			return storedState;
		},
		onVerify(questionId) {
			const question = this.getQuestion(questionId);
			const answer = question.answers[question.selectedAnswer];
			this.onUserEvent({
				action: feature_components.quiz_question.actions.check_answer.value,
				value: Number(answer.is_correct)
			});
			this.resolveQuestion(questionId);
			this.saveQuestionsResults({ questions: [questionId] });
		},
		onActiveViewChange(activeView) {
			this.activeView = activeView;
			this.$trackUserEvent({
				subcontext: this.activeView,
				feature: this.feature.value,
				action: this.feature.actions.open.value,
				context: this.context.value
			});
		},
		performCheckQuestions() {
			scrollToTop();
			this.testProcessing = true;
			this.checkQuestions({ examMode: this.examMode, examTagId: this.examTagId }).then(results => {
				this.testResults = results;
				this.testProcessing = false;
				this.testMode = false;
				this.presetOptionsToPass = {};
			});

			this.trackFinshTest();
		},
		trackFinshTest() {
			this.$trackUserEvent({
				subcontext: this.context.subcontext.test_yourself.value,
				context: this.context.value,
				action: this.context.subcontext.test_yourself.actions.finish_test.value,
			});
		},
		setQuestion({ page, index }) {
			this.switchOverlay(true, 'currentQuestion');
			this.changePage(page)
			// last page may change after fetching the page
			// when "nierozwiązane pytania" filter is active
				.then(() => this.changeCurrentQuestion({
					page: this.getSafePage(page),
					index
				}))
				.then(question => {
					this.switchOverlay(false, 'currentQuestion');
					this.fetchQuestionData(question.id);
					this.savePosition({
						position: {
							page: this.getSafePage(page),
							index
						}
					});

					this.$trackUserEvent({
						subcontext: this.activeView,
						feature: this.feature.value,
						feature_component: this.featureComponent.value,
						action: this.featureComponent.actions.open.value,
						target: question.id,
						context: this.context.value
					});
				});
		},
		setupFilters() {
			if (!isEmpty(this.filters)) return Promise.resolve(this.filters);

			return this.fetchDynamicFilters();
		},
		switchOverlay(display, source = 'filters', message = 'questions') {
			this.fetchingQuestions = display;
			this.toggleOverlay({
				source,
				display,
				text: this.$t(`ui.loading.${message}`)
			});
		},
		toggleBuilder() {
			this.showBuilder = !this.showBuilder;
		},
		verifyCheckQuestions({ unansweredCount }) {
			if (unansweredCount) {
				let description = this.$t('questions.solving.confirm.unanswered', {
					count: unansweredCount
				});

				if (!this.currentUserHasFinishedEntryExam && this.examTagId === this.entryExamTagId) {
					description = `<p class="margin bottom">${description}</p>
												 <p class="margin bottom">${this.$t('questions.solving.confirm.satisfactionGuaranteeWarning')}</p>
												 <p>${this.$t('questions.solving.confirm.continueLater')}</p>`;
				}

				this.confirmQuizEnd(description).then(() => false)
					.catch(() => this.performCheckQuestions());
			} else {
				examStateStore.remove(this.examStateStoreKey);
				this.performCheckQuestions();
			}
		},
		verifyEndQuiz() {
			if (this.testMode) {
				this.confirmQuizEnd()
					.then(() => false)
					.catch(() => this.endQuiz());
			} else {
				this.endQuiz();
			}
		},
		onSearch(phrase) {
			if (phrase.trim() !== '') {
				this.addFilter(phrase)
					.then(() => {
						return this.activeFiltersToggle({
							filter: `search.${phrase}`,
							active: true,
						});
					})
					.then(() => {
						this.fetchingFilters = true;

						this.resetCurrentQuestion();
						this.setToken();
						this.resetPages();
						return Promise.all([
							this.fetchDynamicFilters(),
							this.fetchMatchingQuestions()
						]);
					})
					.then(() => Promise.all([
						this.fetchQuestionsReactions(this.getPage(this.meta.currentPage)),
						this.fetchQuestionData(this.currentQuestion.id)
					]))
					.then(() => {
						this.fetchingFilters = false;
					});
			}
		},
		onUserEvent(payload) {
			this.$trackUserEvent({
				subcontext: this.activeView,
				feature: this.feature.value,
				feature_component: this.featureComponent.value,
				context: this.context.value,
				...payload,
			});
		},
		onUpdateTime(remainingTime) {
			const persistedState = this.readPersistedState();

			examStateStore.set(this.examStateStoreKey, JSON.stringify({
				...persistedState,
				time: Math.floor(remainingTime / 60)
			}));
		},
		setupQuestions() {
			const hasPresetFilters = !isEmpty(this.presetFilters);

			this.switchOverlay(true);
			this.fetchingFilters = true;
			return this.setupFilters().then(() => {
				return new Promise(resolve => {
					if (hasPresetFilters) {
						this.activeFiltersSet(this.presetFilters);
						resolve();
					} else {
						this.fetchActiveFilters().then(resolve);
					}
				})
					.then(() => {
						this.fetchingFilters = false;
						this.resetCurrentQuestion();
						this.resetPages();
						this.setToken();
					})
					.then(this.fetchDynamicFilters)
					.then(this.getPosition)
					.then(({ data = {} }) => {
						return new Promise((resolve) => {
							this.fetchQuestions({
								saveFilters: false,
								useSavedFilters: false,
								page: (data.position && data.position.page) || 1,
								filters: this.initialFilters,
							}).then(() => resolve(data));
						});
					})
					.then(({ position }) => {
						!isEmpty(position) && this.changeCurrentQuestion(position);
						this.switchOverlay(false);
						this.$trackUserEvent({
							subcontext: this.activeView,
							feature: this.feature.value,
							feature_component: this.featureComponent.value,
							action: this.featureComponent.actions.open.value,
							target: this.currentQuestion.id,
							context: this.context.value
						});
					})
					.then(() => this.fetchQuestionsReactions(this.getPage(1)))
					.then(() => this.reactionsFetched = true)
					.then(() => this.fetchQuestionData(this.currentQuestion.id));
			});
		},
		async restoreExamState() {
			const storedState = this.readPersistedState();
			if (!storedState.results) return;

			const results = storedState.results;
			const questionsIds = results.map(response => response.question_id);
			try {
				await this.$swal(swalConfig({
					title: 'Zapisaliśmy nieukończony test!',
					html: '<p>Wygląda na to, że jesteś w trakcie rozwiązywania testu.</p><p>Czy chcesz do niego wrócić?</p>',
					showCancelButton: true,
					confirmButtonText: 'TAK, WRACAM DO TESTU',
					cancelButtonText: 'NIE, PRZERYWAM TEST',
					type: 'info',
					confirmButtonClass: 'button is-primary',
					reverseButtons: true
				}));
				this.presetOptionsToPass = {
					activeView: VIEWS.TEST_YOURSELF,
					canChangeTime: false,
					loadingText: 'mockExam',
					sizesToChoose: [results.length],
					testQuestionsCount: results.length,
					time: storedState.time || timeBaseOnQuestions(results.length),
					examTagId: storedState.examTagId || 0,
					examMode: !!storedState.examTagId,
					examQuestions: questionsIds
				};
				this.switchOverlay(true, 'testBuilding', 'testBuilding');
				this.resetTest();
				this.testMode = true;
				await this.fetchQuestionsByIds(questionsIds);

				results
					.filter(response => response.answer_id)
					.forEach(response => {
						const question = this.getQuestion(response.question_id);
						const answerIndex = question.answers.findIndex(answer => answer.id === response.answer_id);
						if (answerIndex === -1) return;
						this.onSelectAnswer({ id: response.question_id, answer: answerIndex }, false);
					});
			} catch (e) {
				examStateStore.remove(this.examStateStoreKey);
			} finally {
				this.switchOverlay(false, 'testBuilding');
			}
		}
	},
};
</script>
