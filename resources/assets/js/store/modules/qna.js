import _ from 'lodash';
import axios, { isCancel } from 'axios';
import * as types from 'js/store/mutations-types';
import { getApiUrl } from 'js/utils/env';
import { set, delete as destroy } from 'vue';
import { reactionsGetters, reactionsMutations, reactionsActions } from 'js/store/modules/reactions';
import { commentsGetters, commentsMutations, commentsActions, commentsState } from 'js/store/modules/comments';

const include = 'profiles,reactions,qna_answers.profiles,qna_answers.comments,qna_answers.comments.profiles';
const discussionsInclude = 'qna_questions,qna_questions.profiles,qna_questions.reactions,qna_questions.qna_answers.profiles,qna_questions.qna_answers.comments,qna_questions.qna_answers.comments.profiles';

function _updateQuestion(questionId, payload) {
	return axios.put(getApiUrl(`qna_questions/${questionId}`), payload);
}

function _getQuestionsByTagName(tagName, ids) {
	return axios.post(getApiUrl('qna_questions/byTags'), {
		tags_names: [tagName],
		ids,
		include
	});
}

function _getQuestionsForDiscussion({ discussionId, cancelToken }) {
	return axios.get(getApiUrl(`discussions/${discussionId}`), {
		params: {
			include: discussionsInclude
		},
		cancelToken
	});
}

function _handleGetQuestionsSuccess({ commit, dispatch }, { data }) {
	commit(types.QNA_DESTROY);

	if (!_.isUndefined(data.included)) {
		commit(types.UPDATE_INCLUDED, data.included);

		data.included.comments && dispatch('comments/setComments', data.included.comments, { root:true });

		destroy(data, 'included');
		commit(types.QNA_SET_QUESTIONS, data);
	}

	commit(types.IS_LOADING, false);
}

function _handleGetQuestionsError(commit, error) {
	if (!isCancel(error)) {
		$wnl.logger.error(error);
	}
	commit(types.IS_LOADING, false);
}

function _getAnswers(questionId) {
	return axios.get(getApiUrl(`qna_questions/${questionId}?include=profiles,qna_answers.profiles,qna_answers.comments,qna_answers.comments.profiles,reactions`));
}

function getInitialState() {
	return {
		...commentsState,
		loading: [],
		sorting: 'latest',
		questionsIds: [],
		qna_questions: {},
		qna_answers: {},
		comments: {},
		profiles: {},
		tags: {},
		config: {
			highlighted: {}
		},
	};
}

function sortByTime(questionsList) {
	return _.reverse(
		_.sortBy(
			_.values(questionsList),
			(question) => question.upvote.created_at
		)
	);
}

function sortByVotes(questionsList) {
	return _.reverse(
		_.sortBy(
			_.values(questionsList),
			(question) => question.upvote.count
		)
	);
}

function sortByNoAnswer(questionsList) {
	return _.reverse(
		_.sortBy(
			_.values(
				_.filter(questionsList, (question) => {
					return typeof question.qna_answers === 'undefined';
				})
			),
			(question) => question.upvote.created_at
		)
	);
}


function getUsersQuestions(questionsList, userProfileId) {
	return _.reverse(
		_.sortBy(
			_.values(
				_.filter(questionsList, (question) => {
					return question.profiles[0] === userProfileId;
				})
			),
			(question) => question.upvote.created_at
		)
	);
}

const namespaced = true;

// Initial state
const state = getInitialState();

// Getters
const getters = {
	...reactionsGetters,
	...commentsGetters,
	loading: state => state.loading.length > 0,
	currentSorting: state => state.sorting,
	questions: state => state.qna_questions,
	getSortedQuestions: (state, getters, rootState, rootGetters) => (sorting, list) => {
		switch (sorting) {
		case 'latest':
			return sortByTime(list);
		case 'no-answer':
			return sortByNoAnswer(list);
		case 'my':
			return getUsersQuestions(list, rootGetters.currentUserProfileId);
		default:
			return sortByVotes(list);
		}
	},
	// Resources
	getQuestion: state => (id) => state.qna_questions[id],
	answer:      state => (id) => state.qna_answers[id],
	profile:     state => (id) => state.profiles[id] || {},

	// Question
	questionAnswers: state => (id) => {
		let answersIds = state.qna_questions[id].qna_answers;
		if (_.isUndefined(answersIds)) {
			return [];
		}

		return answersIds.map((id) => state.qna_answers[id]);
	},
	questionTags: state => (id) => {
		let tags = state.qna_questions[id].tags;
		if (_.isUndefined(tags)) {
			return [];
		}

		return tags.map((id) => state.tags[id]);
	},
	questionAnswersFromHighestUpvoteCount: (state, getters) => (id) => {
		return getters.questionAnswers(id).sort((answerA, answerB) => answerB.upvote.count - answerA.upvote.count);
	},
};


// Mutations
const mutations = {
	...reactionsMutations,
	...commentsMutations,
	[types.IS_LOADING] (state, isLoading) {
		const loadingStatus = state.loading;
		if (isLoading) {
			set(state, 'loading', (new Array(loadingStatus.length + 1)).fill(true));
		} else {
			set(state, 'loading', (new Array(loadingStatus.length - 1)).fill(true));
		}
	},
	[types.QNA_CHANGE_SORTING] (state, sorting) {
		set(state, 'sorting', sorting);
	},
	[types.QNA_SET_QUESTIONS] (state, data) {
		Object.keys(data).forEach((key) => {
			let question = data[key];
			set(state.qna_questions, question.id, question);
		});
	},
	[types.QNA_UPDATE_QUESTION] (state, payload) {
		let id = payload.questionId,
			data = _.merge(state.qna_questions[id], payload.data);

		/**
		 * In case you wonder why I destroy it first - please visit.
		 * https://vuejs.org/v2/guide/list.html#Caveats
		 * In short, due to limitations of JS, Vue cannot recognize if an
		 * array updates. The best way to be sure everything is updated
		 * is to destroy the target first using Vue's reactive method
		 * destroy.
		 */
		destroy(state.qna_questions, id);
		set(state.qna_questions, id, data);
	},
	[types.QNA_REMOVE_QUESTION] (state, payload) {
		let id = payload.questionId,
			questionsIds = _.pull(state.questionsIds, id);

		destroy(state.qna_questions, id);
		set(state, 'questionsIds', questionsIds);
	},
	[types.QNA_UPDATE_ANSWER] (state, payload) {
		let id = payload.answerId,
			data = _.merge(state.qna_answers[id], payload.data);

		/**
		 * In case you wonder why I destroy it first - please visit.
		 * https://vuejs.org/v2/guide/list.html#Caveats
		 * In short, due to limitations of JS, Vue cannot recognize if an
		 * array updates. The best way to be sure everything is updated
		 * is to destroy the target first using Vue's reactive method
		 * destroy.
		 */
		destroy(state.qna_answers, id);
		set(state.qna_answers, id, _.merge(state.qna_answers[id], data));
	},
	[types.QNA_REMOVE_ANSWER] (state, payload) {
		let id = payload.answerId,
			questionId = payload.questionId,
			answers = _.pull(state.qna_questions[questionId].qna_answers, id);

		destroy(state.qna_answers, id);
		set(state.qna_questions[questionId], 'qna_answers', answers);
	},
	[types.UPDATE_INCLUDED] (state, included) {
		_.each(included, (items, resource) => {
			const resources = { ...state[resource], ...items };
			set(state, resource, resources);
		});
	},
	[types.QNA_DESTROY] (state) {
		let initialState = getInitialState();
		Object.keys(initialState)
			.filter((field) => field !== 'loading')
			.forEach((field) => set(state, field, initialState[field]));
	},
};

// Actions
const actions = {
	...reactionsActions,
	...commentsActions,
	changeSorting({ commit }, sorting) {
		commit(types.QNA_CHANGE_SORTING, sorting);
	},

	async fetchQuestionsForDiscussion({ commit, dispatch }, { discussionId, cancelToken }) {
		commit(types.IS_LOADING, true);

		try {
			commit(types.QNA_DESTROY);
			const { data } = await _getQuestionsForDiscussion({ discussionId, cancelToken });

			if (!_.isUndefined(data.included)) {
				const { qna_questions, ...included } = data.included;
				commit(types.UPDATE_INCLUDED, included);
				commit(types.QNA_SET_QUESTIONS, qna_questions);

				included.comments && dispatch('comments/setComments', included.comments, { root:true });
			}

			commit(types.IS_LOADING, false);
		} catch (e) {
			_handleGetQuestionsError(commit, e);
		}
	},

	fetchQuestionsByTagName({ commit, dispatch }, { tagName, ids }) {
		commit(types.IS_LOADING, true);

		return new Promise((resolve, reject) => {
			_getQuestionsByTagName(tagName, ids)
				.then((response) => {
					_handleGetQuestionsSuccess({ commit, dispatch }, response);
					resolve();
				})
				.catch((error) => {
					_handleGetQuestionsError(commit, error);
					reject();
				});
		});
	},

	fetchQuestion({ commit }, questionId) {
		return new Promise((resolve, reject) => {
			_getAnswers(questionId)
				.then((response) => {
					let data = response.data,
						included = data.included;

					commit(types.UPDATE_INCLUDED, included);
					delete(data.included);
					commit(types.QNA_UPDATE_QUESTION, { questionId, data });
					resolve();
				})
				.catch((error) => {
					$wnl.logger.error(error);
					reject();
				});
		});
	},
	removeQuestion({ commit }, questionId) {
		return new Promise((resolve) => {
			commit(types.QNA_REMOVE_QUESTION, { questionId });
			resolve();
		});
	},
	async resolveQuestion({commit}, questionId) {
		// TODO check why resolved is not marked
		await _updateQuestion(questionId, { resolved: true });
		commit(types.QNA_UPDATE_QUESTION, { questionId, data: { resolved: true } });
	},
	async unresolveQuestion({commit}, questionId) {
		await _updateQuestion(questionId, { resolved: false });
		commit(types.QNA_UPDATE_QUESTION, { questionId, data: { resolved: false } });
	},
	async verifyQuestion({ commit }, questionId) {
		const { data: question } = await _updateQuestion(questionId, { verified: true });
		commit(types.QNA_UPDATE_QUESTION, { questionId, data: { verified_at: question.verified_at } });
	},
	async unverifyQuestion({ commit }, questionId) {
		const { data: question } = await _updateQuestion(questionId, { verified: false });
		commit(types.QNA_UPDATE_QUESTION, { questionId, data: { verified_at: question.verified_at } });
	},
	removeAnswer({ commit }, payload) {
		return new Promise((resolve) => {
			commit(types.QNA_REMOVE_ANSWER, {
				questionId: payload.questionId,
				answerId: payload.answerId,
			});
			resolve();
		});
	},
	destroyQna({ commit }) {
		commit(types.QNA_DESTROY);
	},
	setUserQnaQuestions({ commit, dispatch }, { included, ...qnaQuestions }) {
		commit(types.QNA_SET_QUESTIONS, qnaQuestions);
		commit(types.UPDATE_INCLUDED, included);
		included && included.comments && dispatch('comments/setComments', included.comments, { root:true });
	},
};

export default {
	actions,
	getters,
	mutations,
	namespaced,
	state,
};
