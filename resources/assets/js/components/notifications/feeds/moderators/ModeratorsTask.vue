<template>
	<div class="card">
		<header class="card-header">
			<p class="card-header-title" v-t="title"/>
			<span class="tag is-light is-medium" v-t="'tasks.task.fields.eventsCount'"/>
			<span class="tag is-danger is-medium">{{eventsCount}}</span>
		</header>
		<div class="card-content">
			<div class="tags field has-addons">
				<span class="tag is-light is-medium" v-t="'tasks.task.fields.status'"/>
				<wnl-dropdown>
					<p slot="activator" class="tag is-medium" :class="statusTag.class">
						{{statusTag.text}}&nbsp
						<span class="icon is-small">
							<i class="fa fa-angle-down"></i>
						</span>
					</p>
					<div slot="content">
						<div @click="$emit('statusSelected', {status: st, id: task.id})" class="dropdown-item" v-for="(st, index) in status" :key="index">
							{{$t(`tasks.task.status.${st}`)}}
						</div>
					</div>
				</wnl-dropdown>
			</div>
			<div class="tags field has-addons">
				<span class="tag is-light is-medium" v-t="'tasks.task.fields.assignee'"/>
				<span class="tag is-medium is-ligth">{{assigneeText}}</span>
			</div>
			<p>{{taskContent}}</p>
		</div>
		<footer class="card-footer">
			<router-link :to="taskContext" class="card-footer-item">Idziem tam!</router-link>
			<div class="card-footer-item" @click="$emit('assign', {assignee_id: currentUserId, id: task.id})">Biore to!</div>
		</footer>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.tag
		border-radius: 0

	.card-header-title
		align-items: center

	.card-footer-item, .dropdown-item
		cursor: pointer

		&:hover
			background-color: $color-background-light-gray

	.dropdown-item
		padding: $margin-small
		border-bottom: $border-light-gray
</style>

<script>
import Dropdown from 'js/components/global/Dropdown'
import {mapGetters} from 'vuex'

export default {
	props: {
		task: {
			type: Object,
			required: true
		}
	},
	components: {
		'wnl-dropdown': Dropdown
	},
	data() {
		return {
			status: {
				OPEN: 'open',
				IN_PROGRESS: 'inProgress',
				DONE: 'done'
			}
		}
	},
	computed: {
		...mapGetters(['currentUserId']),
		title() {
			return this.task.text || this.$t('tasks.task.defaultTitle')
		},
		statusTag() {
			switch (this.task.status) {
				case this.status.OPEN:
					return {
						class: 'is-warning',
						text: this.$t('tasks.task.status.open')
					}
				case this.status.IN_PROGRESS:
					return {
						class: 'is-info',
						text: this.$t('tasks.task.status.inProgress')
					}
				case this.status.DONE:
					return {
						class: 'is-success',
						text: this.$t('tasks.task.status.done')
					}
				defaut:
					return {
						class: 'is-ligth',
						text: this.$t('tasks.task.status.unknown')
					}
			}
		},
		assigneeText() {
			return this.task.assignee.full_name || '----'
		},
		eventsCount() {
			return this.task.events.length
		},
		lastEvent() {
			return this.task.events[this.eventsCount - 1]
		},
		taskContent() {
			return this.lastEvent.data.subject.text
		},
		taskContext() {
			return _.get(this.lastEvent, 'data.context', this.lastEvent.data.referer)
		}
	}
};
</script>
