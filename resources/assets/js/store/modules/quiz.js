import axios from 'axios';
import _ from 'lodash';
import {set, delete as destroy} from 'vue';
import {getApiUrl} from 'js/utils/env';
import {commentsGetters, commentsMutations, commentsActions, commentsState} from 'js/store/modules/comments';
import {reactionsGetters, reactionsMutations, reactionsActions} from 'js/store/modules/reactions';
import * as types from 'js/store/mutations-types';
import quizStore from 'js/services/quizStore';
import {parseTaxonomyTermsFromIncludes} from 'js/utils/contentClassifier';

const _fetchQuestions = (requestParams) => {
	return axios.post(getApiUrl('quiz_questions/.filter'), requestParams);
};

const DEFAULT_INCLUDE = 'quiz_answers,comments.profiles,comments,reactions,slides,taxonomy_terms.tags,taxonomy_terms.taxonomies,taxonomy_terms.ancestors.tags';

function fetchQuizSet(id) {
	return new Promise((resolve, reject) => {
		_fetchQuestions({
			filters: [{'quiz-set': {
				'quiz_set_id': id,
			}}],
			include: DEFAULT_INCLUDE,
		}).then(({data: data}) => {
			resolve(data);
		}).catch((err) => {
			reject(err);
		});
	});
}

function fetchQuizSetStats(id) {
	return axios.get(
		getApiUrl(`quiz_sets/${id}/stats`)
	);
}

function _fetchQuestionsCollectionByTagName(tagName, ids, page = 1) {
	return axios.post(getApiUrl('quiz_questions/query'), {
		tag_name: tagName,
		ids,
		include: DEFAULT_INCLUDE,
		page,
	});
}

function _fetchSingleQuestion(id) {
	return axios.get(getApiUrl(`quiz_questions/${id}?include=${DEFAULT_INCLUDE}`));
}

function getInitialState() {
	return {
		...commentsState,
		attempts: [],
		comments: {},
		isComplete: false,
		loaded: false,
		questionsIds: [],
		quiz_answers: {},
		quiz_questions: {},
		slides: {},
		processing: false,
		profiles: {},
		setId: null,
		setName: '',
		quiz_stats: {},
		retry: false,
		pagination: {}
	};
}

// Should the module be namespaced?
const namespaced = true;

// Initial state
const state = getInitialState();

const getters = {
	...commentsGetters,
	...reactionsGetters,
	getCurrentScore: (state, getters) => {
		return _.round(getters.getResolved.length * 100 / getters.questionsLength, 0);
	},
	getAttempts: (state) => state.attempts,
	getQuestions: (state) => {
		return state.questionsIds.map((id) => {
			const quizQuestion = state.quiz_questions[id];
			if (!quizQuestion) return;
			return quizQuestion;
		}).filter(question => question);
	},
	getQuestion: state => id => state.quiz_questions[id] || {},
	getQuestionsWithAnswers: (state) => {
		return state.questionsIds.map((id) => {
			const quizQuestion = state.quiz_questions[id];
			if (!quizQuestion) return;
			return {
				...quizQuestion,
				answers: quizQuestion.quiz_answers.map(answerId => state.quiz_answers[answerId]),
				slides: (quizQuestion.slides || []).map(slideId => state.slides[slideId])
			};
		}).filter(question => question);
	},
	getQuestionsWithAnswersAndStats: (state) => {
		return state.questionsIds.map((id) => {
			const quizQuestion = state.quiz_questions[id];
			if (!quizQuestion) return;
			const questionStats = state.quiz_stats[id] || {};
			const allHits = Object.values(questionStats).reduce((count, current) => {
				return count + current;
			}, 0);

			return {
				...quizQuestion,
				answers: quizQuestion.quiz_answers.map((answerId) => {
					const answer = state.quiz_answers[answerId];
					return {
						...answer,
						stats: Math.round((questionStats[answerId] || 0) / allHits * 100)
					};
				}),
				slides: (quizQuestion.slides || []).map(slideId => state.slides[slideId])
			};
		}).filter(question => question);
	},
	getResolved: (state, getters) => _.filter(getters.getQuestions, {'isResolved': true}),
	getUnresolved: (state, getters) => getters.getQuestions.filter(question => !question.isResolved),
	isComplete: (state, getters) => state.isComplete || getters.getUnresolved.length === 0 && getters.hasQuestions,
	isLoaded: (state) => state.loaded,
	isProcessing: (state) => state.processing,
	hasQuestions: (state, getters) => getters.questionsLength !== 0,
	questionsLength: (state) => state.questionsIds.length,
	getAnswer: (state) => (id) => state.quiz_answers[id]
};

const mutations = {
	...commentsMutations,
	...reactionsMutations,
	[types.QUIZ_ATTEMPT] (state, payload) {
		state.attempts.push(payload);
	},
	[types.QUIZ_COMPLETE] (state) {
		set(state, 'isComplete', true);
	},
	[types.QUIZ_IS_LOADED] (state, loaded) {
		set(state, 'loaded', loaded);
	},
	[types.QUIZ_RESET_ANSWER] (state, payload) {
		set(state.quiz_questions[payload.id], 'selectedAnswer', null);
		set(state.quiz_questions[payload.id], 'isResolved', false);
	},
	[types.QUIZ_RESOLVE_QUESTION] (state, payload) {
		const question = state.quiz_questions[payload.id];
		if (_.isNumber(question.selectedAnswer) && question.hasOwnProperty('original_answers')) {
			let selectedId = question.quiz_answers[question.selectedAnswer];
			question.quiz_answers = question.original_answers;
			question.selectedAnswer = question.quiz_answers.indexOf(selectedId);
		}
		set(question, 'isResolved', true);
	},
	[types.QUIZ_RESTORE_STATE] (state, payload) {
		set(state, 'setId', payload.setId);
		set(state, 'setName', payload.setName);
		set(state, 'attempts', payload.attempts);
		set(state, 'isComplete', payload.isComplete);
		set(state, 'questionsIds', payload.questionsIds);

		_.forEach(payload.quiz_questions, (value, id) => {
			if (!_.isUndefined(state.quiz_questions[id])) {
				if (!_.isUndefined(value.isResolved)) {
					set(state.quiz_questions[id], 'isResolved', value.isResolved);
				}
			}
		});
	},
	[types.QUIZ_SET_QUESTIONS] (state, payload) {
		set(state, 'setId', payload.setId);
		set(state, 'setName', payload.setName);
		set(state, 'questionsIds', payload.questionsIds);

		for (let i = 0; i < payload.len; i++) {
			let id = payload.questionsIds[i];

			set(state.quiz_questions[id], 'selectedAnswer', null);
			set(state.quiz_questions[id], 'isResolved', false);
		}
	},
	[types.QUIZ_SELECT_ANSWER] (state, payload) {
		set(state.quiz_questions[payload.id], 'selectedAnswer', payload.answer);
	},
	[types.QUIZ_SHUFFLE_ANSWERS] (state, payload) {
		const question = state.quiz_questions[payload.id];
		if (!question.hasOwnProperty('original_answers')) {
			set(question, 'original_answers', _.cloneDeep(question.quiz_answers));
		}
		set(question, 'quiz_answers', _.shuffle(question.quiz_answers));
	},
	[types.QUIZ_TOGGLE_PROCESSING] (state, isProcessing) {
		set(state, 'processing', isProcessing);
	},
	[types.UPDATE_INCLUDED] (state, included) {
		_.each(included, (items, resource) => {
			let resourceObject = state[resource];
			_.each(items, (item) => {
				set(resourceObject, item.id, item);
			});
		});
	},
	[types.QUIZ_DESTROY] (state) {
		let initialState = getInitialState();
		Object.keys(initialState).forEach((field) => {
			set(state, field, initialState[field]);
		});
	},
	[types.QUIZ_SET_STATS] (state, {stats}) {
		set(state, 'quiz_stats', stats);
	},
	[types.QUIZ_RESET_PROGRESS] (state) {
		Object.keys(state.quiz_questions).forEach((questionId) => {
			const updatedState = {
				...state.quiz_questions[questionId],
				isResolved: false,
				selectedAnswer: null,
			};

			set(state.quiz_questions, questionId, updatedState);
		});

		set(state, 'isComplete', false);
		set(state, 'attempts', []);
		set(state, 'retry', true);
	},
	[types.QUIZ_CHANGE_QUESTION] (state, targetIndex) {
		let deleteCount = targetIndex,
			spliced = state.questionsIds.splice(0, deleteCount);
		state.questionsIds.push(...spliced);
	},
	[types.QUIZ_SET_PAGINATION] (state, pagination) {
		set(state, 'pagination', pagination);
	}
};

const actions = {
	...commentsActions,
	...reactionsActions,
	async setupQuestions({commit, rootGetters, getters, state, dispatch}, resource) {
		commit(types.QUIZ_IS_LOADED, false);

		await dispatch('setupCurrentUser', {}, { root: true });
		Promise.all([
			quizStore.getQuizProgress(resource.id, rootGetters.currentUserId),
			fetchQuizSet(resource.id),
			fetchQuizSetStats(resource.id)
		]).then(([storedState, response, quizStats]) => {
			const {included, ...quizQuestions} = response.data,
				quizQuestionsOldWay = {};

			Object.values(quizQuestions).forEach((quizQuestion) => {
				quizQuestionsOldWay[quizQuestion.id] = {
					...quizQuestion,
					// TODO constant
					type: 'quizQuestions',
					taxonomyTerms: parseTaxonomyTermsFromIncludes(quizQuestion.taxonomy_terms, included)
				};
			});

			delete included.tags;
			delete included.taxonomy_terms;
			delete included.taxonomies;
			delete included.ancestors;

			const quizQuestionsIds = Object.keys(quizQuestionsOldWay),
				len = quizQuestionsIds;

			included.comments && dispatch('comments/setComments', {...included.comments}, {root: true});
			commit(types.UPDATE_INCLUDED, {...included, quiz_questions: quizQuestionsOldWay});

			if (!_.isEmpty(storedState)) {
				commit(types.QUIZ_RESTORE_STATE, storedState);
			} else {
				commit(types.QUIZ_SET_QUESTIONS, {
					setId: resource.id,
					setName: '',
					len,
					questionsIds: quizQuestionsIds,
				});
			}
			commit(types.QUIZ_TOGGLE_PROCESSING, false);
			commit(types.QUIZ_IS_LOADED, true);
			commit(types.QUIZ_SET_STATS, quizStats.data);
		});
	},

	fetchQuestionsCollectionByTagName({commit, dispatch}, {tagName, ids, page}) {
		commit(types.QUIZ_IS_LOADED, false);

		return _fetchQuestionsCollectionByTagName(tagName, ids, page).then(({data: responseData = {}}) => {
			const {data, ...pagination} = responseData;

			if (data.included) {
				let included = _.clone(data.included);
				destroy(data, 'included');
				included['quiz_questions'] = _.mapValues(data, quizQuestion => ({
					...quizQuestion,
					type: 'quizQuestions',
					taxonomyTerms: parseTaxonomyTermsFromIncludes(quizQuestion.taxonomy_terms, included),
				}));

				delete included.tags;
				delete included.taxonomy_terms;
				delete included.taxonomies;
				delete included.ancestors;

				let questionsIds = _.map(data, (question) => question.id),
					len = questionsIds.length;

				included.comments && dispatch('comments/setComments', {...included.comments}, {root:true});
				commit(types.UPDATE_INCLUDED, included);
				commit(types.QUIZ_SET_QUESTIONS, {
					setId: 0,
					setName: `Kolekcja pytań kontrolnych dla ${tagName}`,
					len,
					questionsIds,
				});
				commit(types.QUIZ_SET_PAGINATION, pagination);
				commit(types.QUIZ_RESET_PROGRESS);
				commit(types.QUIZ_TOGGLE_PROCESSING, false);
			} else {
				commit(types.QUIZ_DESTROY);
			}

			commit(types.QUIZ_IS_LOADED, true);
		});
	},

	fetchSingleQuestion({commit, dispatch}, id) {
		commit(types.QUIZ_IS_LOADED, false);

		return _fetchSingleQuestion(id)
			.then(response => {
				if (response.data.included) {
					const included = _.clone(response.data.included);
					destroy(response.data, 'included');

					const id = response.data.id;
					included['quiz_questions'] = {};
					included['quiz_questions'][id] = response.data;

					included.comments && dispatch('comments/setComments', included.comments, {root:true});
					commit(types.UPDATE_INCLUDED, included);
					commit(types.QUIZ_SET_QUESTIONS, {
						setId: 0,
						setName: `Pytanie numer ${id}`,
						let: 1,
						questionsIds: [id],
					});
					commit(types.QUIZ_RESET_PROGRESS);
					commit(types.QUIZ_TOGGLE_PROCESSING, false);
				} else {
					commit(types.QUIZ_DESTROY);
				}

				commit(types.QUIZ_IS_LOADED, true);

				return response;
			})
			.catch(error => {
				$wnl.logger.error(error);
				commit(types.QUIZ_IS_LOADED, true);
				return error;
			});
	},

	checkQuiz({state, commit, getters, dispatch, rootGetters}, force = false) {
		return new Promise((resolve) => {
			commit(types.QUIZ_TOGGLE_PROCESSING, true);
			const data = [];
			const reactionsToSet = [];
			const attempts = getters.getAttempts.length;

			_.each(getters.getUnresolved, question => {
				let id = question.id,
					selectedId = question.quiz_answers[question.selectedAnswer],
					selected = state.quiz_answers[selectedId];

				if (_.isNumber(question.selectedAnswer)) {

					if (!attempts) {
						data.push({
							'quiz_question_id': id,
							'quiz_answer_id': selectedId,
							'user_id': rootGetters.currentUserId
						});
					}
				}

				if (_.isNumber(question.selectedAnswer) && selected.is_correct) {
					commit(types.QUIZ_RESOLVE_QUESTION, {id});
				} else {
					// react
					const reaction = getters.getReaction('quiz_questions', question.id, 'bookmark').hasReacted;

					attempts === 0 && !state.retry && !reaction && reactionsToSet.push({
						reactableResource: 'quiz_questions',
						reactableId: question.id,
						reaction: 'bookmark',
						hasReacted: question.hasReacted
					});

					 if (attempts < 2) {
						commit(types.QUIZ_RESET_ANSWER, {id});
						if (!question.preserve_order) {
							commit(types.QUIZ_SHUFFLE_ANSWERS, {id});
						}
					}
				}
			});

			commit(types.QUIZ_ATTEMPT, {score: getters.getCurrentScore});

			if (reactionsToSet.length) {
				dispatch('markManyAsReacted', reactionsToSet);
			}

			dispatch('saveQuiz', data);

			if (force || getters.getUnresolved.length === 0) {
				commit(types.QUIZ_COMPLETE);
			}

			commit(types.QUIZ_TOGGLE_PROCESSING, false);
			resolve();
		});
	},

	resolveQuestion({state, commit}, id) {
		commit(types.QUIZ_RESOLVE_QUESTION, {id});
	},

	/**
	 * Changes the current question (at index 0) to a selected question
	 * @param  {Object} state
	 * @param  {Function} commit
	 * @param  {Integer} targetIndex An index of a target question
	 */
	changeQuestion({state, commit}, targetIndex = 1) {
		let currentId = state.questionsIds[0];
		commit(types.QUIZ_CHANGE_QUESTION, targetIndex);
		commit(types.QUIZ_RESET_ANSWER, {id: currentId});
	},

	saveQuiz({state, rootGetters}, recordedAnswers){
		quizStore.saveQuizProgress(rootGetters.currentUserId, state, recordedAnswers);
	},

	autoResolve({state, commit}) {
		state.questionsIds.forEach(id => commit(types.QUIZ_RESOLVE_QUESTION, {id}));
		commit(types.QUIZ_COMPLETE);
	},

	resetState({state, commit}) {
		commit(types.QUIZ_RESET_PROGRESS);
		commit(types.QUIZ_IS_LOADED, true);
		commit(types.QUIZ_SET_PAGINATION, {page: 1, last_page: 1});
	},

	destroyQuiz({commit}){
		commit(types.QUIZ_IS_LOADED, false);
		commit(types.QUIZ_DESTROY);
		return Promise.resolve();
	},

	commitSelectAnswer({commit}, payload){
		commit(types.QUIZ_SELECT_ANSWER, payload);
	},

	shuffleAnswers({commit}, payload) {
		commit(types.QUIZ_SHUFFLE_ANSWERS, payload);
	}
};

export default {
	namespaced,
	state,
	getters,
	mutations,
	actions
};
