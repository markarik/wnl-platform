<template>
	<div class="scrollable-main-container">
		<div class="level wnl-screen-title">
			<div class="level-left">
				<div class="level-item big strong">
					Dostępność lekcji
				</div>
			</div>
		</div>
		<div class="groups">
			<ul class="groups-list" v-if="structure">
				<li class="group" v-for="(item, index) in groupsAreOpen">
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
						<li class="subitem" v-for="(subitem, index) in item.lessons" :class="{'isEven': isEven(index)}">
							<span>{{subitem.id}}</span>
							<span class="subitem-name label">{{subitem.name}}</span>
							<div class="datepicker">
								<wnl-lesson-availability :class="{'hasColorBackground': isEven(index)}" :startDate="findStartDate(subitem.id)"/>
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
							&.isEven
								background-color: $color-background-lighter-gray
							.subitem-name
								align-self: flex-end
								width: 65%
								color: $color-gray
							.datepicker
								margin-right: $margin-small

</style>

<script>
import { mapGetters } from 'vuex'
import { resource } from 'js/utils/config'
import { getApiUrl } from 'js/utils/env'
import LessonAvailability from 'js/components/user/LessonAvailability'

export default {
	name: 'LessonsAvailabilities',
	components: {
		'wnl-lesson-availability': LessonAvailability,
	},
	data() {
		return {
			startDate: new Date(),
			openGroups: [],
			lessonAvailabilities: {},
		}
	},
	computed: {
		...mapGetters(['currentUserRoles']),
		...mapGetters('course', [
			'name',
			'groups',
			'structure',
		]),
		groupsAreOpen() {
			return this.groups.map(groupId => {
				const group = this.structure[resource('groups')][groupId]
				return Object.assign({}, {
					...group,
					lessons: group[resource('lessons')].map(lessonId => {
						return this.structure[resource('lessons')][lessonId]
					})
				})
			})
		},
	},
	methods: {
		findStartDate(id) {
			return new Date (this.lessonAvailabilities.find((lesson) => {
				return lesson.lesson_id === id
			}).start_date*1000)
		},
		isOpen(item) {
			return this.openGroups.indexOf(item.id) > -1 ? true : false
		},
		isEven(index) {
			return index % 2 === 0 ? true : false
		},

		toggleItem(item) {
			if (this.openGroups.indexOf(item.id) === -1) {
				this.openGroups.push(item.id)
			} else {
				var index = this.openGroups.indexOf(item.id)
				if (index > -1) {
					this.openGroups.splice(index, 1)
				}
			}
		},
	},
	mounted() {
		axios.get(getApiUrl('user_lesson_availabilities')).then((response) => {
			this.lessonAvailabilities = response.data
		})
	}
}
</script>
