// Common
export const IS_LOADING      = 'IS_LOADING'
export const IS_FETCHING     = 'IS_FETCHING'
export const UPDATE_INCLUDED = 'UPDATE_INCLUDED'
export const RESET_MODULE    = 'RESET_MODULE'

// Navigation
export const ADD_BREADCRUMB    = 'ADD_BREADCRUMB'
export const REMOVE_BREADCRUMB = 'REMOVE_BREADCRUMB'
export const UPDATE_LESSON_NAV = 'UPDATE_LESSON_NAV'

// Users
export const USERS_SETUP_CURRENT  = 'USERS_SETUP_CURRENT'
export const USERS_UPDATE_CURRENT = 'USERS_UPDATE_CURRENT'
export const USERS_SETUP_SETTINGS = 'USERS_SETUP_SETTINGS'
export const USERS_CHANGE_SETTING = 'USERS_CHANGE_SETTING'
export const USERS_SET_STATS      = 'USERS_SET_STATS'

// Course
export const SET_STRUCTURE       = 'SET_STRUCTURE'
export const COURSE_READY        = 'COURSE_READY'
export const COURSE_REMOVE_GROUP = 'COURSE_REMOVE_GROUP'

// Sidenav
export const SET_NAVIGATION = 'SET_NAVIGATION'

// Comments
export const SET_COMMENTS   = 'SET_COMMENTS'
export const SET_COMMENTS_RAW   = 'SET_COMMENTS_RAW'
export const ADD_COMMENT    = 'ADD_COMMENT'
export const REMOVE_COMMENT = 'REMOVE_COMMENT'
export const RESOLVE_COMMENT = 'RESOLVE_COMMENT'
export const UNRESOLVE_COMMENT = 'UNRESOLVE_COMMENT'

// Reactions
export const SET_REACTION  = 'SET_REACTION'

// Forms
export const FORM_SETUP                = 'FORM_SETUP'
export const FORM_UPDATE_ORIGINAL_DATA = 'FORM_UPDATE_ORIGINAL_DATA'
export const FORM_UPDATE_URL           = 'FORM_UPDATE_URL'
export const FORM_POPULATE             = 'FORM_POPULATE'
export const FORM_HAS_CHANGES          = 'FORM_HAS_CHANGES'
export const FORM_IS_LOADING           = 'FORM_IS_LOADING'
export const FORM_IS_LOADED            = 'FORM_IS_LOADED'
export const FORM_INPUT                = 'FORM_INPUT'
export const FORM_RESET                = 'FORM_RESET'
export const ERRORS_RECORD             = 'ERRORS_RECORD'
export const ERRORS_CLEAR              = 'ERRORS_CLEAR'
export const ERRORS_CLEAR_SINGLE       = 'ERRORS_CLEAR_SINGLE'

// Chat
export const CHAT_ADD_NEW_MESSAGE = 'CHAT_ADD_NEW_MESSAGE'
export const CHAT_SET_ROOM        = 'CHAT_SET_ROOM'
export const CHAT_SET_MESSAGES    = 'CHAT_SET_MESSAGES'
export const CHAT_IS_LOADED       = 'CHAT_IS_LOADED'
export const CHAT_SET_USERS       = 'CHAT_IS_LOADED'

// Progress
export const PROGRESS_SETUP_COURSE     = 'PROGRESS_SETUP_COURSE'
export const PROGRESS_SETUP_LESSON     = 'PROGRESS_SETUP_LESSON'
export const PROGRESS_START_LESSON     = 'PROGRESS_START_LESSON'
export const PROGRESS_COMPLETE_LESSON  = 'PROGRESS_COMPLETE_LESSON'
export const PROGRESS_COMPLETE_SECTION = 'PROGRESS_COMPLETE_SECTION'
export const PROGRESS_COMPLETE_SCREEN  = 'PROGRESS_COMPLETE_SCREEN'
export const PROGRESS_COMPLETE_SUBSECTION  = 'PROGRESS_COMPLETE_SUBSECTION'

// Q&A
export const QNA_DESTROY           = 'QNA_DESTROY'
export const QNA_SET_QUESTIONS_IDS = 'QNA_SET_QUESTIONS_IDS'
export const QNA_SET_QUESTIONS     = 'QNA_SET_QUESTIONS'
export const QNA_CHANGE_SORTING    = 'QNA_CHANGE_SORTING'
export const QNA_UPDATE_QUESTION   = 'QNA_UPDATE_QUESTION'
export const QNA_UPDATE_ANSWER     = 'QNA_UPDATE_ANSWER'
export const QNA_ADD_QUESTIONS     = 'QNA_ADD_QUESTIONS'

export const QNA_SET_ANSWERS   = 'QNA_SET_ANSWERS'
export const QNA_SET_COMMENTS  = 'QNA_SET_COMMENTS'

export const QNA_ADD_QUESTION    = 'QNA_ADD_QUESTION'
export const QNA_REMOVE_QUESTION = 'QNA_REMOVE_QUESTION'
export const QNA_RESOLVE_QUESTION = 'QNA_RESOLVE_QUESTION'
export const QNA_UNRESOLVE_QUESTION = 'QNA_UNRESOLVE_QUESTION'
export const QNA_ADD_ANSWER      = 'QNA_ADD_ANSWER'
export const QNA_REMOVE_ANSWER   = 'QNA_REMOVE_ANSWER'

// Quiz
export const QUIZ_ATTEMPT           = 'QUIZ_ATTEMPT'
export const QUIZ_COMPLETE          = 'QUIZ_COMPLETE'
export const QUIZ_DESTROY           = 'QUIZ_DESTROY'
export const QUIZ_IS_LOADED         = 'QUIZ_IS_LOADED'
export const QUIZ_CHANGE_QUESTION     = 'QUIZ_CHANGE_QUESTION'
export const QUIZ_QUESTIONS_ATTEMPT = 'QUIZ_QUESTIONS_ATTEMPT'
export const QUIZ_SET_QUESTIONS     = 'QUIZ_SET_QUESTIONS'
export const QUIZ_SET_SETUP         = 'QUIZ_SET_SETUP'
export const QUIZ_SELECT_ANSWER     = 'QUIZ_SELECT_ANSWER'
export const QUIZ_SHUFFLE_ANSWERS   = 'QUIZ_SHUFFLE_ANSWERS'
export const QUIZ_RESET_ANSWER      = 'QUIZ_RESET_ANSWER'
export const QUIZ_RESOLVE_QUESTION  = 'QUIZ_RESOLVE_QUESTION'
export const QUIZ_RESTORE_STATE     = 'QUIZ_RESTORE_STATE'
export const QUIZ_TOGGLE_PROCESSING = 'QUIZ_TOGGLE_PROCESSING'
export const QUIZ_SET_STATS         = 'QUIZ_SET_STATS'
export const QUIZ_RESET_PROGRESS    = 'QUIZ_RESET_PROGRESS'

// Slideshow
export const SLIDESHOW_SET_PRESENTABLES = 'SLIDESHOW_SET_PRESENTABLES'
export const SLIDESHOW_SET_SLIDES       = 'SLIDESHOW_SET_SLIDES'

// Collections
export const COLLECTIONS_SET_QNA    = 'COLLECTIONS_SET_QNA'
export const COLLECTIONS_SET_QUIZ   = 'COLLECTIONS_SET_QUIZ'
export const COLLECTIONS_SET_SLIDES = 'COLLECTIONS_SET_SLIDES'
export const COLLECTIONS_SET_REACTABLES = 'COLLECTIONS_SET_REACTABLES'
export const COLLECTIONS_SET_CATEGORIES = 'COLLECTIONS_SET_CATEGORIES'
export const COLLECTIONS_APPEND_SLIDE = 'COLLECTIONS_APPEND_SLIDE'
export const COLLECTIONS_REMOVE_SLIDE = 'COLLECTIONS_REMOVE_SLIDE'

export const SLIDES_LOADING = 'SLIDES_LOADING'


//UI
export const UI_CHANGE_LAYOUT = 'UI_CHANGE_LAYOUT'
export const UI_RESET_LAYOUT = 'UI_RESET_LAYOUT'
export const UI_TOGGLE_SIDENAV = 'UI_TOGGLE_SIDENAV'
export const UI_TOGGLE_CHAT = 'UI_TOGGLE_CHAT'
export const UI_SET_CHAT_OPEN = 'UI_SET_CHAT_OPEN'
export const UI_CLOSE_SIDENAVS = 'UI_CLOSE_SIDENAVS'
export const UI_INIT_CHAT = 'UI_INIT_CHAT'
export const UI_KILL_CHAT = 'UI_KILL_CHAT'
export const UI_DISPLAY_OVERLAY = 'UI_DISPLAY_OVERLAY'
export const UI_TOGGLE_NAVIGATION_GROUP = 'UI_TOGGLE_NAVIGATION_GROUP'
export const UI_CHANGE_OVERVIEW_VIEW = 'UI_CHANGE_OVERVIEW_VIEW'
export const UI_SHOW_GLOBAL_NOTIFICATION = 'UI_SHOW_GLOBAL_NOTIFICATION'

//Users
export const ACTIVE_USERS_SET = 'ACTIVE_USERS_SET';
export const ALL_USERS_SET = 'ALL_USERS_SET';
export const USERS_BY_LOCATION = 'USERS_BY_LOCATION';

// Notifications
export const ADD_NOTIFICATION       = 'ADD_NOTIFICATION'
export const CHANNEL_HAS_MORE       = 'CHANNEL_HAS_MORE'
export const MODIFY_NOTIFICATION    = 'MODIFY_NOTIFICATION'
export const SET_NOTIFICATIONS      = 'SET_NOTIFICATIONS'
export const SET_NOTIFICATIONS_USER = 'SET_NOTIFICATIONS_USER'

// Questions
export const QUESTIONS_DYNAMIC_FILTERS_SET      = 'QUESTIONS_DYNAMIC_FILTERS_SET'
export const QUESTIONS_RESET_PAGES              = 'QUESTIONS_RESET_PAGES'
export const QUESTIONS_RESET_TEST               = 'QUESTIONS_RESET_TEST'
export const QUESTIONS_RESOLVE_QUESTION         = 'QUESTIONS_RESOLVE_QUESTION'
export const QUESTIONS_SELECT_ANSWER            = 'QUESTIONS_SELECT_ANSWER'
export const QUESTIONS_SET_CURRENT              = 'QUESTIONS_SET_CURRENT'
export const QUESTIONS_SET_META                 = 'QUESTIONS_SET_META'
export const QUESTIONS_SET_QUESTION_DATA        = 'QUESTIONS_SET_QUESTION_DATA'
export const QUESTIONS_SET_PAGE                 = 'QUESTIONS_SET_PAGE'
export const QUESTIONS_SET_TEST                 = 'QUESTIONS_SET_TEST'
export const QUESTIONS_SET_WITH_ANSWERS         = 'QUESTIONS_SET_WITH_ANSWERS'
export const QUESTIONS_UPDATE                   = 'QUESTIONS_UPDATE'

// Filters
export const ACTIVE_FILTERS_ADD = 'ACTIVE_FILTERS_ADD'
export const ACTIVE_FILTERS_RESET = 'ACTIVE_FILTERS_RESET'
export const ACTIVE_FILTERS_SET = 'ACTIVE_FILTERS_SET'
export const ACTIVE_FILTERS_REMOVE = 'ACTIVE_FILTERS_REMOVE'

// Autocomplete
export const GET_USERS_AUTOCOMPLETE = 'GET_USERS_AUTOCOMPLETE'
