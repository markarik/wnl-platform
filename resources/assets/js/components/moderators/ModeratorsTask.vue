<template>
	<div class="card">
		<header class="card-header">
			<p class="card-header-title">{{title}}</p>
			<p class="events-counter">
				<span v-t="'tasks.task.fields.eventsCount'" class="tag is-light is-medium" />
				<span class="tag is-danger is-medium">{{eventsCount}}</span>
			</p>
		</header>
		<div class="card-content task-summary">
			<div class="tags field has-addons">
				<span v-t="'tasks.task.fields.status'" class="tag is-light is-medium" />
				<wnl-dropdown>
					<p
						slot="activator"
						class="tag is-medium"
						:class="statusTag.class"
					>
						{{statusTag.text}}&nbsp;
						<span class="icon is-small">
							<i class="fa fa-angle-down" />
						</span>
					</p>
					<div slot="content">
						<div
							v-for="(st, index) in status"
							:key="index"
							class="dropdown-item"
							@click="$emit('statusSelected', {status: st, id: task.id})"
						>
							{{$t(`tasks.task.status.${st}`)}}
						</div>
					</div>
				</wnl-dropdown>
			</div>
			<div class="tags field has-addons is-relative">
				<span v-t="'tasks.task.fields.createdAt'" class="tag is-light is-medium" />
				<span class="tag is-medium">{{formatedCreatedAt}}</span>
			</div>
			<div class="tags field has-addons is-relative">
				<span v-t="'tasks.task.fields.assignee'" class="tag is-light is-medium" />
				<wnl-moderators-autocomplete
					class="margin horizontal"
					:selected="task.assignee"
					:users-list="availableModerators"
					@change="assign"
				/>
			</div>
			<div class="tags field has-addons is-relative">
				<span v-t="'tasks.task.fields.updatedAt'" class="tag is-light is-medium" />
				<span class="tag is-medium">{{formatedUpdatedAt}}</span>
			</div>
		</div>
		<div class="card-content">
			<wnl-task-events :events="task.events" />
		</div>
		<footer class="card-footer">
			<router-link
				target="_blank"
				:to="taskContext"
				class="card-footer-item"
			>{{$t('tasks.task.action.go')}}</router-link>
			<div class="card-footer-item" @click="$emit('assign', {assignee_id: currentUserId, id: task.id})">Biore to!</div>
		</footer>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.tag
		border-radius: 0

	.task-summary
		display: flex
		flex-wrap: wrap
		padding-bottom: 0

		.tags.field
			flex: 1 0 50%

	.card-header
		align-items: center
		background-color: $color-background-light-gray

	.events-counter
		margin: 0 $margin-small

	.card-footer-item, .dropdown-item
		cursor: pointer

		&:hover
			background-color: $color-background-light-gray

	.dropdown-item
		padding: $margin-small
		border-bottom: $border-light-gray
</style>

<script>
import { get } from 'lodash';
import { mapGetters } from 'vuex';

import WnlDropdown from 'js/components/global/Dropdown';
import WnlTaskEvents from 'js/components/moderators/ModeratorsTaskEvents';
import WnlModeratorsAutocomplete from 'js/components/moderators/ModeratorsAutocomplete';

import { timeFromS } from 'js/utils/time';

export default {
	props: {
		task: {
			type: Object,
			required: true
		},
		availableModerators: {
			type: Array,
			required: true
		},
		closeDropdown: {
			type: Boolean,
			default: false
		}
	},
	components: {
		WnlDropdown,
		WnlModeratorsAutocomplete,
		WnlTaskEvents,
	},
	data() {
		return {
			status: {
				OPEN: 'open',
				IN_PROGRESS: 'inProgress',
				DONE: 'done',
				REOPEN: 'reopen'
			},
		};
	},
	computed: {
		...mapGetters(['currentUserId']),
		title() {
			return this.task.text || this.$t('tasks.task.defaultTitle');
		},
		statusTag() {
			switch (this.task.status) {
			case this.status.OPEN:
				return {
					class: 'is-warning',
					text: this.$t('tasks.task.status.open')
				};
			case this.status.IN_PROGRESS:
				return {
					class: 'is-info',
					text: this.$t('tasks.task.status.inProgress')
				};
			case this.status.DONE:
				return {
					class: 'is-success',
					text: this.$t('tasks.task.status.done')
				};
			case this.status.REOPEN:
				return {
					class: 'is-danger',
					text: this.$t('tasks.task.status.reopen')
				};
			default:
				return {
					class: 'is-ligth',
					text: this.$t('tasks.task.status.unknown')
				};
			}
		},
		eventsCount() {
			return this.task.events.length;
		},
		lastEvent() {
			return this.task.events[this.eventsCount - 1];
		},
		taskContext() {
			if (get(this.lastEvent, 'data.context.dynamic')) {
				const dynamic = get(this.lastEvent, 'data.context.dynamic');
				return {
					name: 'dynamicContextMiddleRoute',
					params: {
						resource: dynamic.resource,
						context: dynamic.value
					}
				};
			}

			if (this.lastEvent.data.context) {
				return {
					...this.lastEvent.data.context,
					query: {
						[this.lastEvent.data.objects.type]: this.lastEvent.data.objects.id
					}
				};
			}

			return get(this.lastEvent, 'data.context', this.lastEvent.data.referer);
		},
		formatedCreatedAt() {
			return timeFromS(this.task.created_at);
		},
		formatedUpdatedAt() {
			return timeFromS(this.task.updated_at);
		},
	},
	methods: {
		assign(user = {}) {
			this.$emit('assign', { assignee_id: user && user.user_id || null, id: this.task.id });
		},
	},
};
</script>
