import axios from 'axios'
import {getApiUrl} from 'js/utils/env'
import {debounce} from 'lodash'

const INCREMENT_BY = 1000 * 60 * 10
const INACTIVITY_TIME = 1000 * 60 * 30
const DEBOUNCE_TIME = 1000 * 60
const EVENTS = ['click', 'touchstart', 'keyup', 'mousemove', 'scroll'];
let trackingIntervalId = null;
let inactivityTimerId = null;

export const startTracking = (userId) => {
    EVENTS.forEach((event) => {
        document.addEventListener(event, debounce(() => track(userId), DEBOUNCE_TIME), true)
    })
}

const track = (userId) => {
    if (!trackingIntervalId) {
        trackingIntervalId = window.setInterval(() => {
            axios.put(getApiUrl(`users/${userId}/state/time`))
        }, INCREMENT_BY)
    }

    clearTimeout(inactivityTimerId)
    inactivityTimerId = setTimeout(stopTracking, INACTIVITY_TIME)

}

const stopTracking = () => {
    window.clearInterval(trackingIntervalId);
    trackingIntervalId = null
    inactivityTimerId = null
}
