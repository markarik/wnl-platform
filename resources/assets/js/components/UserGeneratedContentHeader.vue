<template>
	<div class="user-generated-content-header">
		<wnl-avatar
			:class="{'author-forgotten': author.deleted_at, 'avatar': true}"
			:full-name="author.full_name"
			:url="author.avatar"
			size="medium"
			@click.native="showModal"
		/>
		<div class="user-generated-content-header__info">
			<span :class="{'author-forgotten': author.deleted_at}" @click="showModal">
				{{author.full_name}}
			</span>
			<div>
				<span class="user-generated-content-header__separator">·</span>
				<span>{{time}}</span>
				<span v-if="canDelete">
					<span class="user-generated-content-header__separator">·</span>
					<wnl-delete
						:target="deleteTarget"
						:request-route="deleteResourceRoute"
						@deleteSuccess="$emit('deleteSuccess')"
					/>
				</span>
				<wnl-resolve
					:resource="content"
					@resolveResource="$emit('resolveResource')"
					@unresolveResource="$emit('unresolveResource')"
				/>
			</div>
		</div>
		<wnl-modal v-if="modalVisible" @closeModal="closeModal">
			<wnl-user-profile-modal :author="author" />
		</wnl-modal>
	</div>
</template>

<style lang="sass" scoped>
@import 'resources/assets/sass/variables'
@import 'resources/assets/sass/mixins'

.user-generated-content-header
	flex-grow: 1
	display: flex
	align-items: flex-start
	flex-direction: row
	line-height: 1em

	@media #{$media-query-tablet}
		align-items: center

	/deep/ &__separator
		margin: 0 $margin-small-minus
		display: inline-block

	&__info
		display: flex
		flex-direction: column
		margin-left: $margin-small

		@media #{$media-query-tablet}
			flex-direction: row
			align-items: center

	.author-forgotten
		color: $color-gray
		pointer-events: none


</style>

<script>
import WnlDelete from 'js/components/global/Delete';
import WnlResolve from 'js/components/global/Resolve';
import WnlUserProfileModal from 'js/components/users/UserProfileModal';
import WnlModal from 'js/components/global/Modal';
import { timeFromS } from 'js/utils/time';


export default {
	components: { WnlModal, WnlDelete, WnlResolve, WnlUserProfileModal },
	props: {
		author: {
			type: Object,
			required: true
		},
		canDelete: {
			type: Boolean,
			default: false
		},
		deleteResourceRoute: {
			type: String,
			default: null
		},
		deleteTarget: {
			type: String,
			default: null
		},
		content: {
			type: Object,
			required: true
		}
	},
	data() {
		return {
			modalVisible: false
		};
	},
	computed: {
		time() {
			return timeFromS(this.content.created_at);
		},
	},
	methods: {
		showModal() {
			this.modalVisible = true;
		},
		closeModal() {
			this.modalVisible = false;
		},
	}
};
</script>
