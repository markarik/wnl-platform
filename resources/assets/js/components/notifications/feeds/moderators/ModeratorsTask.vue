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
		</footer>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	@import 'resources/assets/sass/variables'

	.tag
		border-radius: 0

	.dropdown-item
		cursor: pointer
		padding: $margin-small
		border-bottom: $border-light-gray

		&:hover
			background-color: $color-background-light-gray
</style>

<script>
import Dropdown from 'js/components/global/Dropdown'

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
			return this.task.assignee || '----'
		},
		eventsCount() {
			return this.task.events.length
		},
		taskContent() {
			const lastEvent = this.task.events[this.eventsCount - 1]
			return lastEvent.data.subject.text
		}
	}
};
</script>
