<template>
	<div class="scrollable-main-container" :style="{height: `${elementHeight}px`}">
		<template v-if="!shouldDisplaySatisfactionGuaranteeModal">
			<div v-if="shouldShowLesson" class="wnl-lesson">
				<div class="wnl-lesson-view">
					<div class="level wnl-screen-title">
						<div class="level-left">
							<div class="level-item metadata">
								{{lessonName}}
							</div>
						</div>
						<div class="level-right">
							<div class="level-item small">
								Lekcja {{lessonNumber}}
							</div>
						</div>
					</div>
					<router-view v-if="!isLessonLoading" @userEvent="onUserEvent" />
				</div>
				<div class="wnl-lesson-previous-next-nav">
					<wnl-previous-next />
				</div>
			</div>
			<div v-else-if="isPlanBuilderEnabled">
				<p class="has-text-centered margin vertical">
					<img src="https://media.giphy.com/media/BCfw7hyQeq9TNsC7st/giphy.gif">
				</p>
				<h5 class="title is-5 has-text-centered">Zgodnie z Twoim planem, ta lekcja otworzy się <strong>{{lessonStartDate}}</strong></h5>
				<p class="has-text-centered margin vertical">Jeżeli chcesz zrealizować tę lekcję dziś, <router-link :to="{name: 'lessons-availabilites'}">zmień swój plan pracy</router-link>.</p>
			</div>
			<div v-else>
				<h2 class="title is-2 has-text-centered margin vertical">{{lesson.name}}️</h2>
				<p class="has-text-centered margin vertical">
					<img src="https://media.giphy.com/media/MQEBfbPco0fao/giphy.gif">
				</p>
				<h3 class="title is-3 has-text-centered"><strong>Lekcja nieaktywna</strong>🛡️</h3>
				<h5 class="title is-5 has-text-centered">Lekcja będzie dostępna od <strong>{{lessonStartDate}}</strong></h5>
				<p class="has-text-centered">
					Zachęcamy Cię do powrotu do ostatniej niezakończonej lekcji. 🙂
				</p>
				<div class="has-text-centered margin vertical">
					<router-link :to="{name: 'courses', params: {courseId}}" class="button is-primary is-outlined">Wróć na dashboard</router-link>
				</div>
			</div>
		</template>

		<wnl-satisfaction-guarantee-modal
			v-if="isSatisfactionGuaranteeModalVisible"
			:visible="isSatisfactionGuaranteeModalVisible"
			:display-headline="false"
			@closeModal="() => satisfactionGuaranteeModalReject(satisfactionGuaranteeModalCanceled)"
			@submit="satisfactionGuaranteeModalResolve"
		>
			<template slot="title">⚠️ Rozpoczęcie nauki przed rozwiązaniem Wstępnego LEK-u wiąże się z utratą Gwarancji Satysfakcji!</template>
			<template slot="body">Odzyskanie Gwarancji Satysfakcji jest możliwe przed oficjalnym startem kursu. Warunkiem jest usunięcie postępu, ułożenie nowego planu pracy i rozwiązanie Wstępnego LEK-u przed rozpoczęciem pierwszej obowiązkowej lekcji.</template>
			<template slot="footer">
				<span v-html="$t('ui.satisfactionGuarantee.noteInLesson', {url: $router.resolve({name: 'satisfaction-guarantee'}).href})" />
			</template>
			<template slot="close">Wróć na dashboard</template>
			<template slot="submit">Rozumiem, akceptuję</template>
		</wnl-satisfaction-guarantee-modal>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	$previous-next-height: 45px

	.wnl-lesson
		width: 100%

	.wnl-lesson-view
		padding-bottom: calc(2 * #{$previous-next-height})

	.wnl-lesson-previous-next-nav
		background: $color-white
		bottom: 0
		height: $previous-next-height
		left: 0
		right: 0
		position: absolute

	.wnl-screen-title
		padding-bottom: $margin-base
</style>

<script>
import { get, isEmpty, head, noop } from 'lodash';
import moment from 'moment';
import { mapGetters, mapActions, mapState } from 'vuex';

import WnlPreviousNext from 'js/components/course/PreviousNext';
import WnlSatisfactionGuaranteeModal from 'js/components/global/modals/SatisfactionGuaranteeModal';

import { resource } from 'js/utils/config';
import { breadcrumb } from 'js/mixins/breadcrumb';
import context from 'js/consts/events_map/context.json';
import { STATUS_COMPLETE, STATUS_IN_PROGRESS } from 'js/services/progressStore';
import { USER_SETTING_NAMES } from 'js/consts/settings';
import { ALERT_TYPES } from 'js/consts/alert';

export default {
	name: 'Lesson',
	components: {
		WnlPreviousNext,
		WnlSatisfactionGuaranteeModal,
	},
	mixins: [breadcrumb],
	props: {
		courseId: {
			type: Number,
			required: true,
		},
		lessonId: {
			type: Number,
			required: true,
		},
		screenId: {
			type: Number,
			default: null,
		},
		slide: {
			type: Number,
			default: null,
		},
		presenceChannel: {
			type: String,
			required: true,
		},
	},
	data() {
		return {
			/**
				 * elementHeight is used to prevent Safari from expanding
				 * a container with an overflow-y: auto and height: 100%.
				 * Using a specific height, the height of the parent element
				 * (which btw is defined as 100% of its parent element),
				 * all browsers are able to beautifully scroll the content.
				 */
			elementHeight: get(this.$parent, '$el.offsetHeight') || '100%',
			isSatisfactionGuaranteeModalVisible: false,
			satisfactionGuaranteeModalCanceled: 'satisfactionGuaranteeModalCanceled',
			satisfactionGuaranteeModalResolve: noop,
			satisfactionGuaranteeModalReject: noop,
		};
	},
	computed: {
		...mapState('course', ['isPlanBuilderEnabled', 'isLessonLoading']),
		...mapGetters('course', [
			'entryExamLessonId',
			'getScreensForLesson',
			'getLesson',
			'getSectionsForScreen',
			'getSubsectionsForSection',
			'getScreen',
			'getScreenSectionsCheckpoints',
			'getSectionSubsectionsCheckpoints',
			'getLessons',
			'isLessonAvailable',
		]),
		...mapGetters('progress', {
			getSavedLesson: 'getSavedLesson',
			screenProgress: 'getScreen',
			lessonProgress: 'getLesson'
		}),
		...mapGetters([
			'currentUserProfileId',
			'currentUserHasFinishedEntryExam',
			'getSetting',
		]),
		breadcrumb() {
			return {
				level: 1,
				text: this.lessonName,
				to: {
					name: 'lessons',
					params: {
						courseId: this.courseId,
						lessonId: this.lessonId,
					}
				}
			};
		},
		lesson() {
			return this.getLesson(this.lessonId);
		},
		lessonName() {
			return this.lesson && this.lesson.name;
		},
		lessonNumber() {
			return this.getLessons.findIndex(({ id }) => id === this.lesson.id) + 1;
		},
		lessonStartDate() {
			return moment.unix(this.lesson.startDate).format('LL');
		},
		screens() {
			return this.getScreensForLesson(this.lessonId);
		},
		currentScreen() {
			return this.getScreen(this.screenId);
		},
		currentSection() {
			if (!this.sectionsReversed) return;

			return this.sectionsReversed.find((section) => this.slide >= section.slide);
		},
		currentSubsection() {
			if (!this.subsectionsReversed) return;

			return this.subsectionsReversed.find((subsection) => this.slide >= subsection.slide);
		},
		sectionsReversed() {
			if (!(this.currentScreen && this.currentScreen.id)) return;

			const sections = this.getSectionsForScreen(this.currentScreen.id);

			// map needed because reverse modifies intial array
			return sections.map(el => el).reverse();
		},
		subsectionsReversed() {
			if (!this.currentSection) return;

			const subsections = this.getSubsectionsForSection(this.currentSection.id);

			// map needed because reverse modifies intial array
			return subsections.map(el => el).reverse();
		},
		hasSubsections() {
			return this.subsectionsReversed.length > 0;
		},
		firstScreenId() {
			if (isEmpty(this.screens)) {
				return null;
			}

			return head(this.screens).id;
		},
		lessonProgressContext() {
			return {
				courseId: this.courseId,
				lessonId: this.lessonId,
				screenId: this.screenId,
				route: {
					name: this.$route.name,
					params: this.$route.params,
					query: this.$route.query,
					meta: this.$route.meta,
				},
			};
		},
		shouldDisplaySatisfactionGuaranteeModal() {
			return this.lesson.is_required &&
				this.isLessonAvailable(this.lesson.id) &&
				this.lesson.id !== this.entryExamLessonId &&
				!this.getSetting(USER_SETTING_NAMES.SKIP_SATISFACTION_GUARANTEE_MODAL) &&
				!this.currentUserHasFinishedEntryExam;
		},
		shouldShowLesson() {
			return this.isLessonAvailable(this.lessonId);
		},
	},
	watch: {
		lessonId() {
			this.setup();
		},
		'$route'() {
			if (!this.shouldDisplaySatisfactionGuaranteeModal) {
				this.updateLessonProgress();
			}
		},
	},
	mounted () {
		this.setup();
	},
	beforeDestroy () {
		window.Echo.leave(this.presenceChannel);
		window.removeEventListener('resize', this.updateElementHeight);
	},
	methods: {
		...mapActions('progress', [
			'startLesson',
			'completeLesson',
			'completeScreen',
			'completeSection',
			'completeSubsection',
			'saveLessonProgress',
		]),
		...mapActions([
			'addAutoDismissableAlert',
			'changeUserSettingAndSync',
			'setupCurrentUser',
			'toggleOverlay',
			'updateLessonNav',
		]),
		...mapActions('users', ['setActiveUsers', 'userJoined', 'userLeft']),
		...mapActions('course', ['setupLesson']),
		onUserEvent(payload) {
			this.$trackUserEvent({
				...payload,
				context: context.lesson.value
			});
		},
		launchLesson() {
			this.startLesson(this.lessonProgressContext).then(() => {
				this.goToDefaultScreenIfNone();
			});

			window.Echo.join(this.presenceChannel)
				.here(users => this.setActiveUsers({ users, channel: this.presenceChannel }))
				.joining(user => this.userJoined({ user, channel: this.presenceChannel }))
				.leaving(user => this.userLeft({ user, channel: this.presenceChannel }));
		},
		goToDefaultScreenIfNone() {
			const query = this.$route.query || {};

			if (!this.screenId) {
				this.setupCurrentUser().then(() => {
					this.getSavedLesson(this.courseId, this.lessonId, this.currentUserProfileId)
						.then(({ route, status }) => {
							if (this.firstScreenId && (!route || status === STATUS_COMPLETE || route && route.name !== resource('screens'))) {
								const params = {
									courseId: this.courseId,
									lessonId: this.lessonId,
									screenId: this.firstScreenId,
								};
								if (this.getScreen(this.firstScreenId) && this.getScreen(this.firstScreenId).type === 'slideshow' && !get(route, 'params.slide')) {
									params.slide = 1;
								}
								this.$router.replace({ name: resource('screens'), params, query });
							} else if (route && route.hasOwnProperty('name')) {
								this.$router.replace({ ...route, query });
							}
						});
				});
			} else if (this.screenId && !this.slide) {
				const params = {
					courseId: this.courseId,
					lessonId: this.lessonId,
					screenId: this.screenId,
				};

				if (this.currentScreen.type === 'slideshow') {
					params.slide = 1;
				}
				this.$router.replace({ name: resource('screens'), params, query });
			} else {
				this.updateLessonProgress();
			}

			this.updateLessonNav({
				activeSection: (this.currentSection && this.currentSection.id) || 0,
				activeSubsection: (this.currentSubsection && this.currentSection.id) || 0,
				activeScreen: parseInt(this.screenId)
			});
		},
		async displaySatisfactionGuaranteeModalIfNeeded() {
			if (this.shouldDisplaySatisfactionGuaranteeModal) {
				this.isSatisfactionGuaranteeModalVisible = true;

				await new Promise((resolve, reject) => {
					this.satisfactionGuaranteeModalResolve = resolve;
					this.satisfactionGuaranteeModalReject = reject;
				});

				this.changeUserSettingAndSync({
					setting: USER_SETTING_NAMES.SKIP_SATISFACTION_GUARANTEE_MODAL,
					value: true,
				});
			}
		},
		shouldCompleteScreen() {
			if (!this.currentScreen.sections) {
				return true;
			}

			const allSections = this.currentScreen.sections;
			const completedSections = get(this.screenProgress(this.courseId, this.lessonId, this.currentScreen.id), 'sections', {});

			return !allSections.find(id => !completedSections[id]);
		},
		shouldCompleteLesson() {
			const startedScreens = get(this.lessonProgress(this.courseId, this.lessonId), 'screens', {});

			if (this.screens && !startedScreens) {
				return false;
			}

			return !this.screens.find(({ id }) => {
				return !startedScreens[id] || startedScreens[id].status === STATUS_IN_PROGRESS;
			});
		},
		async updateLessonProgress() {
			if (typeof this.screenId !== 'undefined') {
				if (this.currentSection) {
					if (this.getScreenSectionsCheckpoints(this.screenId).includes(this.slide)) {
						await this.completeSection({ ...this.lessonProgressContext, sectionId: this.currentSection.id });
					}
				}

				if (this.currentSubsection) {
					if (this.getSectionSubsectionsCheckpoints(this.currentSection.id).includes(this.slide)) {
						await this.completeSubsection({
							...this.lessonProgressContext,
							sectionId: this.currentSection.id,
							subsectionId: this.currentSubsection.id
						});
					}
				}

				if (this.shouldCompleteScreen()) {
					await this.completeScreen(this.lessonProgressContext);

					if (this.shouldCompleteLesson()) {
						await this.completeLesson(this.lessonProgressContext);
					}
				}

				this.updateLessonNav({
					activeSection: (this.currentSection && this.currentSection.id) || 0,
					activeSubsection: parseInt(this.currentSubsection && this.currentSubsection.id,) || 0,
					activeScreen: this.screenId || 0,
				});
			}
		},
		updateElementHeight() {
			this.elementHeight = this.$parent.$el.offsetHeight;
		},
		async setup() {
			try {
				await this.displaySatisfactionGuaranteeModalIfNeeded();
				this.isSatisfactionGuaranteeModalVisible = false;
			} catch (e) {
				if (e !== this.satisfactionGuaranteeModalCanceled) {
					$wnl.logger.error(e);
					this.addAutoDismissableAlert({
						text: 'Ups, coś poszło nie tak. Spróbuj ponownie, a jeżeli to nie pomoże to daj nam znać o błędzie.',
						type: ALERT_TYPES.ERROR,
					});
				}

				// User wants to keep the satisfaction guarantee
				this.$router.push('/');
				return;
			}

			this.toggleOverlay({ source: 'lesson', display: true });

			try {
				await this.setupLesson(this.lessonId);
				if (this.isLessonAvailable(this.lessonId)) {
					this.launchLesson();
				}
			} catch (e) {
				$wnl.logger.error(e);

				this.addAutoDismissableAlert({
					text: 'Ups, coś poszło nie tak. Spróbuj ponownie, a jeżeli to nie pomoże to daj nam znać o błędzie.',
					type: ALERT_TYPES.ERROR,
				});
			}

			this.toggleOverlay({ source: 'lesson', display: false });
			window.addEventListener('resize', this.updateElementHeight);
		}
	},
};
</script>
