import {getApiUrl} from 'js/utils/env'

const actions = {
	async fetchFlashcardsSet(store, {setId, ...requestParams}) {
		try {
			const response = await axios.get(getApiUrl(`flashcards_sets/${setId}`), {
				params: requestParams
			})
			return response.data
		} catch (e) {
			$wnl.logger.error(e)
		}
	},
	async postAnswer({rootGetters}, {answer, flashcardId}) {
		try {
			const response = await axios.post(
				getApiUrl(`user_flashcards_results/${rootGetters.currentUserId}/${flashcardId}`),
				{
					answer
				}
			)
			console.log(response);
		} catch (e) {
			$wnl.logger.error(e)
		}
	}
}
export default {
	actions,
	namespaced: true
}
