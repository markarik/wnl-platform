import axios from 'axios'

import { getApiUrl } from 'js/utils/env'

export const reactionsGetters = {
  /**
   * [getComments description]
   * @param  {Object} reactable { reactable_type: String, reactable_id: Int, reaction_id: Int }
   */
  reactionsResource: (state) => (payload) => {
    if(!state.[payload.resource][payload.id].hasOwnProperty('reactions')) {
      return []
    }

    return state[payload.resource][payload.id].reactions.map((reactableId) => state.reactions[reactableId])
  },
  reactionProfile: (state) => (id) => state.profiles[id]
}
