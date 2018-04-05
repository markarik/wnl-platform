<template>
	<div class="scrollable-main-container">
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					Plany nauki
				</div>
			</div>
		</div>
		<div class="presets">
			<div class="each-day-preset">
				<button @click="presetLessonAvailabilities" class="button is-info to-right">Jedna lekcja na dzień</button>
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
import { mapGetters, mapActions } from 'vuex'
import { resource } from 'js/utils/config'
import { getApiUrl } from 'js/utils/env'
import Datepicker from 'js/components/global/Datepicker'
import { pl } from 'flatpickr/dist/l10n/pl.js'
import { isEmpty } from 'lodash'
import moment from 'moment'

export default {
	name: 'LessonsAvailabilities',
	components: {
		'wnl-datepicker': Datepicker,
	},
	data() {
		return {
			openGroups: [],
			startDateConfig: {
				altInput: true,
				disableMobile: true,
				locale: pl
			},
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
		}
	},
	computed: {
		...mapGetters(['currentUserId']),
		...mapGetters('course', [
			'name',
			'groups',
			'getLessons',
			'structure',
		]),
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
		}
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('course', ['setLessonAvailabilityStatus']),
		...mapActions(['toggleOverlay']),
		getStartDate(item) {
			return new Date (item.startDate*1000)
		},
		presetLessonAvailabilities() {
			axios.put(getApiUrl(`user_lesson/${this.currentUserId}`, {
				start: new Date(),
				end: new Date(moment("2018-09-20").format()),
				user: this.currentUserId,
				days: 1,
			}))
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
	},
}
</script>
