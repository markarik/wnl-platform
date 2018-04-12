<template>
	<div class="scrollable-main-container">
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					Na co się uczę?
				</div>
			</div>
		</div>
		<div class="scopes-control">
			<a v-for="name, scope in scopes" class="panel-toggle" :class="{'is-active': isScopeActive(scope)}"  :key="scope" @click="toggleScope(scope)">
				{{name}}
				<span class="icon is-small">
					<i class="fa" :class="[isScopeActive(scope) ? 'fa-check-circle' : 'fa-circle-o']"></i>
				</span>
			</a>
		</div>
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					Ile mam czasu?
				</div>
			</div>
		</div>
		<div class="dates columns">
			<div class="column">
				<label class="date-label" for="startDate">
					{{$t('questions.plan.headings.startDate')}}
					<span class="icon is-small">
						<i class="fa fa-hourglass-1"></i>
					</span>
				</label>
				<wnl-datepicker :withBorder="true" v-model="startDate" :config="startDateConfig" @onChange="onStartDateChange"/>
				<p class="tip">
					{{$t('questions.plan.tips.startDate')}}
				</p>
			</div>
			<div class="column">
				<label class="date-label" for="endDate">
					{{$t('questions.plan.headings.endDate')}}
					<span class="icon is-small">
						<i class="fa fa-hourglass-3"></i>
					</span>
				</label>
				<wnl-datepicker :withBorder="true" v-model="endDate" :config="endDateConfig" @onChange="onEndDateChange"/>
				<p class="tip">
					{{$t('questions.plan.tips.endDate')}}
				</p>
			</div>
		</div>
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					Dni, w które moge pracować?
				</div>
			</div>
		</div>
		<div class="days">

		</div>
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					Dostępne plany nauki
				</div>
			</div>
		</div>
		<div class="presets">
			<div class="each-day-preset">
				<button @click="presetLessonAvailabilities(1)" class="button to-right">Jedna lekcja na dzień</button>
			</div>
			<div class="every-second-day-preset">
				<button @click="presetLessonAvailabilities(2)" class="button to-right">Jedna lekcja na dwa dni</button>
			</div>
			<div class="every-three-days-preset">
				<button @click="presetLessonAvailabilities(3)" class="button to-right">Jedna lekcja na trzy dni</button>
			</div>
		</div>
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					{{ $t('user.lessonsAvailabilities.header')}}
				</div>
			</div>
		</div>
		<div class="groups">
			<ul class="groups-list" v-if="structure">
				<li class="group" v-for="(item, index) in groupsWithLessons" :key="index">
					<span class="item-toggle" @click="toggleItem(item)">
						<span class="icon is-small">
							<i class="toggle fa fa-angle-down" :class="{'fa-rotate-180': isOpen(item)}"></i>
						</span>
						<span class="item-name">{{item.name}}</span>
						<span class="subitems-count">
							({{item.lessons.length}})
						</span>
					</span>
					<ul class="subitems" v-if="isOpen(item)">
						<li class="subitem" v-for="(subitem, index) in item.lessons" :class="{'isEven': isEven(index)}" :key="index">
							<span class="subitem-name label" :class="{'is-grayed-out': !subitem.isAccessible}">{{subitem.name}}</span>
							<div class="subitem-left-side">
								<div class="not-accesible" v-if="!subitem.isAccessible">
									Lekcja niedostępna
								</div>
								<div class="datepicker" v-else>
									<wnl-datepicker
										:class="{'hasColorBackground': isEven(index)}"
										:value="getStartDate(subitem)"
										:subitemId="subitem.id"
										:config="startDateConfig"
										@onChange="(payload) => onStartDateChange(payload, subitem.id)"
									/>
								</div>

							</div>
						</li>
					</ul>
				</li>
			</ul>
		</div>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass">
	@import 'resources/assets/sass/variables'

	.scrollable-main-container
		.presets
			margin-bottom: $margin-big

		.groups
			.groups-list
				.group
					margin-bottom: $margin-base
					.item-toggle
						cursor: pointer
						text-align: center
						width: 100%
						margin-bottom: $margin-small
						text-transform: uppercase
						color: $color-sky-blue
						.icon
							color: $color-gray
						.item-name
						.subitems-count
							color: $color-background-gray
							font-size: $font-size-minus-2

					.subitems
						display: flex
						flex-direction: column
						margin-bottom: $margin-small
						.subitem
							margin-bottom: $margin-small
							margin-top: $margin-small
							display: flex
							flex-direction: row-reverse
							justify-content: space-between
							min-height: 35px
							&.isEven
								background-color: $color-background-lightest-gray
							.subitem-name
								align-self: flex-end
								width: 65%
								color: $color-gray
								&.is-grayed-out
									color: $color-gray-dimmed
							.subitem-left-side
								margin-right: $margin-small
								display: flex
								align-items: center
								.not-accesible
									color: $color-gray-dimmed
									font-size: $font-size-plus-1
									text-align: center
									cursor: not-allowed
									min-width: 260px

</style>

<script>
import { pull } from 'lodash'
import { mapGetters, mapActions } from 'vuex'
import { resource } from 'js/utils/config'
import { getApiUrl } from 'js/utils/env'
import Datepicker from 'js/components/global/Datepicker'
import { pl } from 'flatpickr/dist/l10n/pl.js'
import { isEmpty, merge } from 'lodash'
import moment from 'moment'

export default {
	name: 'LessonsAvailabilities',
	components: {
		'wnl-datepicker': Datepicker,
	},
	data() {
		return {
			openGroups: [],
			alertSuccess: {
				text: this.$t('user.lessonsAvailabilities.alertSuccess'),
				type: 'success',
				timeout: 2000,
			},
			alertError: {
				text: this.$t('user.lessonsAvailabilities.alertError'),
				type: 'error',
				timeout: 2000,
			},
			activeScopes: ['thisExam', 'nextExam'],
			startDate: new Date(),
			endDate: null,
		}
	},
	computed: {
		...mapGetters(['currentUserId']),
		...mapGetters('course', [
			'name',
			'groups',
			'getRequiredLessons',
			'structure',
		]),
		...mapGetters('progress', [
			'getCompleteLessons',
		]),
		inProgressLessonsLength() {
			// tutaj wykorzystac filter + includes

			return Object.keys(this.getRequiredLessons).filter(requiredQuestion => {
				console.log(requiredQuestion);
				console.log(this.completedLessons);
				return !this.completedLessons.includes(Number(requiredQuestion))
			}).length
			// let completedLessonsIds = this.completedLessons.forEach(completedLesson => {
			// 	console.log(completeLesson);
			// })

		},
		minimumEndDate() {
			return moment(new Date()).add(this.inProgressLessonsLength, 'days').toDate()
		},
		completedLessons() {
			return this.getCompleteLessons(1).map(lesson => lesson.id)
			// return this.getCompleteLessons(1)
		},
		startDateConfig() {
			return merge(this.defaultDateConfig(), {
				minDate: 'today',
			})
		},
		endDateConfig() {
			return merge(this.defaultDateConfig(), {
				minDate: this.minimumEndDate,
			})
		},
		groupsWithLessons() {
			return this.groups.map(groupId => {
				const group = this.structure[resource('groups')][groupId]
				return {
					...group,
					lessons: group[resource('lessons')].map(lessonId => {
						return this.structure[resource('lessons')][lessonId]
					})
				}
			})
		},
		scopes() {
			return {
				thisExam: 'Na ten LEK',
				nextExam: 'Na następny LEK',
			}
		}
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('course', ['setLessonAvailabilityStatus']),
		...mapActions(['toggleOverlay']),
		isScopeActive(scope) {
			// console.log(this.activeScopes.includes(scope), scope);
			return this.activeScopes[1] === scope
		},
		defaultDateConfig() {
			return {
				altInput: true,
				disableMobile: true,
				locale: pl,
			}
		},
		onEndDateChange(payload) {
			if (isEmpty(payload)) this.endDate = null
		},
		onStartDateChange(payload) {
			if (isEmpty(payload)) this.startDate = null
		},
		toggleScope(scope) {
			let index = this.activeScopes.indexOf(scope)

			if (index > -1 && this.activeScopes.length > 1) {
				this.activeScopes.splice(index, 1)
			} else if (index === -1) {
				this.activeScopes.push(scope)
			} else {
				let other = pull(Object.keys(this.scopes), scope)
				if (other.length > 0) {
					this.activeScopes = [other[0]]
				}
			}
		},
		getStartDate(item) {
			return new Date (item.startDate*1000)
		},
		presetLessonAvailabilities(workdays) {
			axios.put(getApiUrl(`user_lesson/${this.currentUserId}`), {
				user_id: this.currentUserId,
				workdays: workdays,
				start_date: this.startDate,
				end_date: this.endDate,
			})
		},
		isOpen(item) {
			return this.openGroups.indexOf(item.id) > -1
		},
		isEven(index) {
			return index % 2 === 0
		},
		toggleItem(item) {
			if (this.openGroups.indexOf(item.id) === -1) {
				this.openGroups.push(item.id)
			} else {
				const index = this.openGroups.indexOf(item.id)
				if (index > -1) {
					this.openGroups.splice(index, 1)
				}
			}
		},
		onStartDateChange(payload, lessonId) {
			const date = payload[0]
			const diff = moment().startOf('day').diff(date, 'days')

			this.setLessonAvailabilityStatus({lessonId, status: diff >= 0})

			axios.put(getApiUrl(`user_lesson/${this.currentUserId}/${lessonId}`), {
				date: payload[0]
			}).then(() => {
				this.addAutoDismissableAlert(this.alertSuccess)
			}).catch((error) => {
				$wnl.logger.error(error)
				this.addAutoDismissableAlert(this.alertError)
			})
		}
	}
}
</script>
