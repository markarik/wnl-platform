import {set} from 'vue'
import {getApiUrl} from 'js/utils/env'
import * as mutationsTypes from "js/store/mutations-types";

const state = () => {
	return {
		sets: {}
	}
};

const getters = {
	sets: (state) => Object.values(state.sets),
	getSetById: (state) => id => state.sets[id]
};

const mutations = {
	[mutationsTypes.FLASHCARDS_SET_FLASHCARDS_SET](state, payload) {
		set(state.sets, payload.id, payload)
	},
	[mutationsTypes.FLASHCARDS_UPDATE_FLASHCARD](state, updatedFlashcard) {
		const {flashcards_sets: setId} = updatedFlashcard

		const updatedFlashcards = state.sets[setId].flashcards.map(flashcard => {
			if (flashcard.id === updatedFlashcard.id) {
				return updatedFlashcard
			}
			return flashcard;
		});

		set(state.sets[setId], 'flashcards', updatedFlashcards);
	}
}

const actions = {
	async setFlashcardsSet({commit}, {setId, ...requestParams}) {
		try {
			const {data} = await axios.get(getApiUrl(`flashcards_sets/${setId}`), {
				params: requestParams
			})
			const {included, ...flashcardSet} = data

			const {data: userResponseData} = await axios.post(getApiUrl('user_flashcards_results/current'), {
				...requestParams,
				flashcards_ids: flashcardSet.flashcards
			})

			flashcardSet.flashcards = flashcardSet.flashcards.map(flashcardId => {
				return {
					...included.flashcards[flashcardId],
					answer: _.get(userResponseData, `${flashcardId}.answer`, 'unsolved')
				}
			})

			commit(mutationsTypes.FLASHCARDS_SET_FLASHCARDS_SET, flashcardSet)
		} catch (e) {
			$wnl.logger.error(e)
		}
	},
	async postAnswer({commit}, {flashcard, answer, ...requestParams}) {
		try {
			await axios.post(
				getApiUrl(`user_flashcards_results/current/${flashcard.id}`),
				{
					...requestParams,
					answer
				}
			)
			commit(mutationsTypes.FLASHCARDS_UPDATE_FLASHCARD, {
				...flashcard, answer
			})
		} catch (e) {
			$wnl.logger.error(e)
		}
	},
	async postNote({commit}, {note, flashcard}) {
		try {
			await axios.post(
				getApiUrl(`user_flashcard_notes/${flashcard.id}`),
				{ note }
			)
			// TODO check if required
			commit(mutationsTypes.FLASHCARDS_UPDATE_FLASHCARD, {
				...flashcard, note
			})
		} catch (e) {
			$wnl.logger.error(e)
		}
	}
}
export default {
	namespaced: true,
	actions,
	state,
	mutations,
	getters,
}
