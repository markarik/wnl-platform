<template>
	<div class="card">
		<header class="card-header">
			<p class="card-header-title">{{title}}</p>
			<span class="tag is-light is-medium" v-t="'tasks.task.fields.eventsCount'"/>
			<span class="tag is-danger is-medium">{{eventsCount}}</span>
		</header>
		<div class="card-content">
			<div class="tags field has-addons">
				<span class="tag is-light is-medium" v-t="'tasks.task.fields.status'"/>
				<span class="tag is-medium" :class="statusTag.class">{{statusTag.text}}</span>
			</div>
			<div class="tags field has-addons">
				<span class="tag is-light is-medium" v-t="'tasks.task.fields.assignee'"/>
				<span class="tag is-medium is-ligth" v-t="asigneeText"/>
			</div>
			<p>{{taskContent}}</p>
		</div>
		<footer class="card-footer">
		</footer>
	</div>
</template>

<style lang="sass" rel="stylesheet/sass" scoped>
	.tag
		border-radius: 0
</style>

<script>
export default {
	props: {
		task: {
			type: Object,
			required: true
		}
	},
	computed: {
		title() {
			return this.task.text || this.$t('tasks.task.defaultTitle')
		},
		statusTag() {
			switch (this.task.status) {
				case 'open':
					return {
						class: 'is-warning',
						text: this.$t('tasks.task.status.open')
					}
				case 'inProgress':
					return {
						class: 'is-info',
						text: this.$t('tasks.task.status.inProgress')
					}
				case 'done':
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
		asigneeText() {
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
