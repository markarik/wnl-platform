import axios from 'axios'
import * as types from 'js/store/mutations-types'

import { getApiUrl } from 'js/utils/env'

export const reactionsGetters = {

  getReaction: state => (reactableResource, id, reaction) => state[reactableResource][id][reaction],
}
