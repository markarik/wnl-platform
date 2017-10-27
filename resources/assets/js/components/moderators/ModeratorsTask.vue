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
			<div class="tags field has-addons is-relative">
				<span class="tag is-light is-medium" v-t="'tasks.task.fields.assignee'"/>
				<input @focus="onFocus" :value="assigneeTextComputed" @input="onInput" @keydown="onKeyDown"/>
					<wnl-autocomplete
						v-show="showAutocomplete"
						:items="availableModeratorsFilter"
						:onItemChosen="assign"
						:itemComponent="'wnl-user-autocomplete-item'"
						@close="onClose"
						class="wnl-autocomplete-dropdown"
						ref="autocomplete"
					/>
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

	.wnl-autocomplete-dropdown
		top: 100%
		// TODO it should auto expand
		// next 2 lines should be gone
		height: 250px
		overflow: hidden
</style>

<script>
import Dropdown from 'js/components/global/Dropdown'
import Autocomplete from 'js/components/global/Autocomplete'

import {mapGetters} from 'vuex'

const keys = {
		enter: 13,
		esc: 27,
		arrowUp: 38,
		arrowDown: 40,
	}

export default {
	props: {
		task: {
			type: Object,
			required: true
		},
		availableModerators: {
			type: Array,
			default: () => []
		}
	},
	components: {
		'wnl-dropdown': Dropdown,
		'wnl-autocomplete': Autocomplete
	},
	data() {
		return {
			status: {
				OPEN: 'open',
				IN_PROGRESS: 'inProgress',
				DONE: 'done'
			},
			showAutocomplete: false,
			assigneeTextInput: '',
			focused: false
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
		},
		availableModeratorsFilter() {
			return this.availableModerators.filter(moderator => (
				moderator.full_name.toLowerCase().indexOf(this.assigneeTextInput.toLowerCase()) > -1)
			).slice(0, 5)
		},
		assigneeTextComputed() {
			return this.focused ? this.assigneTextInput : this.task.assignee.full_name
		},
	},
	methods: {
		assign(user) {
			this.showAutocomplete = false
		},
		onFocus() {
			this.focused = true
			this.showAutocomplete = true
		},
		onKeyDown(evt) {
			const { enter, arrowUp, arrowDown, esc } = keys

			if (this.availableModerators.length === 0) {
				this.showAutocomplete = false
				return
			}

			if (evt.keyCode === esc) {
				this.showAutocomplete = false
				this.focused = false
				return
			}
			if ([enter, arrowUp, arrowDown].indexOf(evt.keyCode) === -1) {
				this.focused = true
				this.showAutocomplete = true
				return
			}

			this.$refs.autocomplete.onKeyDown(evt)
			this.killEvent(evt)

			//for some of the old browsers, returning false is the true way to kill propagation
			return false
		},
		killEvent(evt) {
			evt.preventDefault()
			evt.stopPropagation()
		},
		onClose() {
			this.showAutocomplete = false
		},
		onInput(event) {
			console.log('...on input', event)
			this.assigneeTextInput = event.target.value
		}
	},
};
</script>
