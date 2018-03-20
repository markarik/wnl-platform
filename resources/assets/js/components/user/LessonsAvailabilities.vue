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
			<ul class="items" v-if="structure">
				<li class="item" v-for="(item, index) in groupsAreOpen">
					<span class="item-name" @click="openItem(item)">
						<span class="icon is-small">
							<i class="toggle fa fa-angle-down"></i>
						</span>
						{{item.name}}
						<span class="subitems-count">
							({{item.lessons.length}})
						</span>
					</span>
					<ul class="subitems" v-if="openGroups.indexOf(item.id) > -1">
						<li class="subitem" v-for="(subitem, index) in item.lessons">
							<span class="subitem-name">{{subitem.name}}</span>
							<div class="datepicker">
								<wnl-datepicker v-model="startDate" :config="startDateConfig" @onChange="onStartDateChange"/>
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
			.items
				.item
					margin-bottom: $margin-base
					.item-name
						text-align: center
						width: 100%
						margin-bottom: $margin-small
						text-transform: uppercase
						.subitems-count
							color: $color-background-gray
							font-size: $font-size-minus-3

					.subitems
						display: flex
						flex-direction: column
						margin-bottom: $margin-small
						.subitem
							display: flex
							flex-direction: row
							justify-content: space-between
							.subitem-name
								width: 65%
								color: blue
							.datepicker

</style>

<script>
import { mapGetters } from 'vuex'
import { resource } from 'js/utils/config'
import Datepicker from 'js/components/global/Datepicker'
import { isEmpty, merge } from 'lodash'
import { pl } from 'flatpickr/dist/l10n/pl.js'

export default {
	name: 'LessonsAvailabilities',
	components: {
		'wnl-datepicker': Datepicker,
	},
	data() {
		return {
			startDate: new Date(),
			openGroups: []
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
					isOpen: false,
					lessons: group[resource('lessons')].map(lessonId => {
						return this.structure[resource('lessons')][lessonId]
					})
				})
			})
		},
		startDateConfig() {
			return {
				altInput: true,
				disableMobile: true,
				locale: pl,
				minDate: 'today',
			}
		},
	},
	methods: {
		onStartDateChange(payload) {
			if (isEmpty(payload)) this.startDate = null
		},
		openItem(item) {
			if (this.openGroups.indexOf(item.id)) {
				this.openGroups.push(item.id)
			} else {
				var index = this.openGroups.indexOf(item.id)
				if (index > -1) {
					this.openGroups.splice(index, 1)
				}
			}
		},
	}
}
</script>
