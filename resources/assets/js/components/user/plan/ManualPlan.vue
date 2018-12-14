<template>
	<div>
		<wnl-text-overlay :isLoading="isLoading" :text="$t('lessonsAvailability.loader')"/>
		<div class="all-lessons-view">
			<div class="level-left all-lessons-annotation-header">
				<div class="level">
					{{ $t('lessonsAvailability.allLessonsAnnotation.header')}}
				</div>
			</div>
			<div class="level-left all-lessons-annotation-explanation">
				<div class="level">
					{{ $t('lessonsAvailability.allLessonsAnnotation.explanation')}}
				</div>
			</div>
			<div class="manual-start-dates" v-if="manualStartDates.length > 0">
				<div class="level-left">
					<div class="level-item">
						{{ $t('lessonsAvailability.lessonsToBeChangedList') }}
					</div>
				</div>
				<table class="table is-fullwidth">
					<tr>
						<th><abbr title="Lesson name">Lekcja</abbr></th>
						<th><abbr title="Old date">Stara data</abbr></th>
						<th><abbr title="New date">Nowa data</abbr></th>
					</tr>
					<tr v-for="(manualStartDate, index) in sortedManualStartDates" :key="index">
						<th title="Lesson name">{{ manualStartDate.lessonName }}</th>
						<th title="Old date">{{ manualStartDate.oldDate }}</th>
						<th title="New date">{{ manualStartDate.formatedStartDate }}</th>
					</tr>
				</table>
				<div class="accept-plan">
					<a
						@click="acceptPlan"
						class="button button is-primary is-outlined is-big"
					>{{ $t('lessonsAvailability.buttons.acceptPlan') }}
					</a>
				</div>
			</div>
			<div class="level-left">
				<div class="level-item big strong margin bottom">
					{{ $t('lessonsAvailability.viewsDropdownInfo') }}
				</div>
			</div>
			<div class="groups">
				<ul class="groups-list" v-if="structure">
					<li class="group" v-for="(item, index) in groupsWithLessons"
							:key="index">
						<span class="item-toggle" @click="toggleItem(item)">
							<span class="icon is-small">
								<i class="toggle fa fa-angle-down"
									 :class="{'fa-rotate-180': isOpen(item)}"></i>
							</span>
							<span class="item-name">{{item.name}}</span>
							<span class="subitems-count">
								({{item.lessons.length}})
							</span>
						</span>
						<ul class="subitems" v-if="isOpen(item)">
							<li class="subitem" v-for="(subitem, index) in item.lessons"
									:class="{'isEven': isEven(index)}"
									:key="index">
								<span class="subitem-name label"
											:class="{'is-grayed-out': !subitem.isAccessible}"
								>{{subitem.name}}</span>
								<div class="subitem-left-side">
									<div class="not-accesible" v-if="!subitem.isAccessible">
										{{ $t('lessonsAvailability.lessonNotAvilable') }}
									</div>
									<div class="datepicker" v-else>
										<wnl-datepicker
											:class="{'hasColorBackground': isEven(index)}"
											:value="getStartDate(subitem)"
											:subitemId="subitem.id"
											:config="startDateConfig"
											@onChange="(payload) => onStartDateChange(payload, subitem)"
										/>
									</div>
								</div>
							</li>
						</ul>
					</li>
				</ul>
			</div>
			<div class="manual-start-dates" v-show="manualStartDates.length > 0">
				<div class="level-left">
					<div class="level-item">
						{{ $t('lessonsAvailability.lessonsToBeChangedList') }}
					</div>
				</div>
				<table class="table is-fullwidth">
					<tr>
						<th>Lekcja</th>
						<th>Stara data</th>
						<th>Nowa data</th>
					</tr>
					<tr v-for="(manualStartDate, index) in sortedManualStartDates" :key="index">
						<th>{{ manualStartDate.lessonName }}</th>
						<th>{{ manualStartDate.oldDate }}</th>
						<th>{{ manualStartDate.formatedStartDate }}</th>
					</tr>
				</table>
			</div>
		</div>
		<div class="accept-plan">
			<a
				@click="acceptPlan"
				class="button button is-primary is-outlined is-big"
			>{{ $t('lessonsAvailability.buttons.acceptPlan') }}
			</a>
		</div>
	</div>
</template>

<style lang="sass" scoped>
	@import 'resources/assets/sass/variables'

	.manual-start-dates
		margin-bottom: $margin-small

	.accept-plan
		display: flex
		justify-content: space-around
		margin-bottom: $margin-small

	.all-lessons-view
		margin-bottom: $margin-base
		width: 100%
		.all-lessons-annotation-header
			width: 100%
			margin-bottom: $margin-base
			overflow-wrap: wrap
			.level-item
				width: 100%
		.all-lessons-annotation-explanation
			width: 100%
			margin-bottom: $margin-base
			overflow-wrap: wrap
			.level-item
				width: 100%

	.groups
		.groups-list
			.group
				margin-bottom: $margin-base
				.item-toggle
					color: $color-sky-blue
					cursor: pointer
					text-align: center
					text-transform: uppercase
					margin-bottom: $margin-small
					width: 100%
					.icon
						color: $color-gray
					.subitems-count
						color: $color-background-gray
						font-size: $font-size-minus-2

				.subitems
					display: flex
					flex-direction: column
					margin-bottom: $margin-small
					.subitem
						display: flex
						flex-direction: row-reverse
						justify-content: space-between
						padding-bottom: $margin-small
						padding-top: $margin-small
						min-height: 35px
						&.isEven
							background-color: $color-background-lightest-gray
						.subitem-name
							align-self: flex-end
							color: $color-gray
							width: 65%
							&.is-grayed-out
								color: $color-gray-dimmed
						.subitem-left-side
							align-items: center
							display: flex
							margin-right: $margin-small
							.not-accesible
								color: $color-gray-dimmed
								font-size: $font-size-plus-1
								text-align: center
								cursor: not-allowed
								min-width: 260px

	.accept-plan
		display: flex
		justify-content: space-around
		margin-bottom: $margin-small

</style>

<script>
import TextOverlay from 'js/components/global/TextOverlay.vue';
import Datepicker from 'js/components/global/Datepicker';
import { mapGetters, mapActions } from 'vuex';
import { resource } from 'js/utils/config';
import moment from 'moment';
import { getApiUrl } from 'js/utils/env';
import momentTimezone from 'moment-timezone';
import { isEmpty } from 'lodash';
import emits_events from 'js/mixins/emits-events';
import features from 'js/consts/events_map/features.json';

export default {
	name: 'ManualPlan',
	components: {
		'wnl-text-overlay': TextOverlay,
		'wnl-datepicker': Datepicker,
	},
	mixins: [emits_events],
	data() {
		return {
			openGroups: [1],
			manualStartDates: [],
			isLoading: false,
			alertSuccess: {
				text: this.$i18n.t('lessonsAvailability.alertSuccess'),
				type: 'success',
			},
			alertError: {
				text: this.$i18n.t('lessonsAvailability.alertError'),
				type: 'error',
			},
			defaultDateConfig: {
				altInput: true,
				disableMobile: true,
			},
		};
	},
	computed: {
		...mapGetters('course', [
			'groups',
			'structure',
			'userLessons',
		]),
		...mapGetters(['currentUserId']),
		startDateConfig() {
			return {
				...this.defaultDateConfig
			};
		},
		groupsWithLessons() {
			return this.groups.map(groupId => {
				const group = this.structure[resource('groups')][groupId];
				return {
					...group,
					lessons: group[resource('lessons')].map(lessonId => {
						return this.structure[resource('lessons')][lessonId];
					})
				};
			});
		},
		sortedManualStartDates() {
			return this.manualStartDates.sort((a, b) => {
				const dateA = new Date(a.startDate);
				const dateB = new Date(b.startDate);
				return dateA - dateB;
			});
		},
	},
	methods: {
		...mapActions(['addAutoDismissableAlert']),
		...mapActions('course', ['setStructure']),
		isEven(index) {
			return index % 2 === 0;
		},
		getStartDate(item) {
			return item.startDate ? new Date (item.startDate*1000) : new Date();
		},
		toggleItem(item) {
			if (this.openGroups.indexOf(item.id) === -1) {
				this.openGroups.push(item.id);
			} else {
				const index = this.openGroups.indexOf(item.id);
				if (index > -1) {
					this.openGroups.splice(index, 1);
				}
			}
		},
		isOpen(item) {
			return this.openGroups.indexOf(item.id) > -1;
		},
		onStartDateChange(newStartDate, subitem) {
			if (!newStartDate[0]) return;

			const lessonWithStartDate = {
				lessonId: subitem.id,
				lessonName: subitem.name,
				oldDate: moment(subitem.startDate * 1000).format('LL'),
				startDate: newStartDate[0],
				formatedStartDate: moment(newStartDate[0]).format('LL'),
			};

			const index = this.manualStartDates.findIndex((el) => {
				return el.lessonId === lessonWithStartDate.lessonId;
			});

			if (index === -1) {
				this.manualStartDates.push(lessonWithStartDate);
			} else {
				this.manualStartDates.splice(index, 1, lessonWithStartDate);
			}
		},
		async acceptPlan() {
			if (!this.validate()) {
				return false;
			}

			this.isLoading = true;
			try {
				await axios.put(getApiUrl(`user_lesson/${this.currentUserId}/batch`), {
					manual_start_dates: this.manualStartDates,
					timezone: momentTimezone.tz.guess(),
				});
				await this.setStructure();
				this.isLoading = false;
				this.manualStartDates = [];
				this.addAutoDismissableAlert(this.alertSuccess);
				this.emitUserEvent({
					action: features.manual_settings.actions.save_plan.value,
					feature: features.manual_settings.value,
				});
			}
			catch(error) {
				this.isLoading = false;
				this.manualStartDates = [];
				$wnl.logger.capture(error);
				this.addAutoDismissableAlert(this.alertError);
			}
		},
		validate() {
			if (isEmpty(this.manualStartDates)) {
				this.addAutoDismissableAlert({
					text: 'Aby ustawić plan, należy zmienić chociaż jedną datę! :)',
					type: 'error',
					timeout: 3000,
				});
				return false;
			}
			return true;
		}
	}
};
</script>
