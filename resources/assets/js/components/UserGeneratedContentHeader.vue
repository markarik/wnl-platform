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
			<div class="user-generated-content-header__meta">
				<span class="user-generated-content-header__meta__item">
					<span class="user-generated-content-header__separator">·</span>
					<span>{{time}}</span>
				</span>
				<span v-if="canDelete" class="user-generated-content-header__meta__item">
					<span class="user-generated-content-header__separator">·</span>
					<wnl-delete
						:target="deleteTarget"
						:request-route="deleteResourceRoute"
						@deleteSuccess="$emit('deleteSuccess')"
					/>
				</span>
				<span v-if="canResolve" class="user-generated-content-header__meta__item">
					<span class="user-generated-content-header__separator">·</span>
					<wnl-resolve
						:resource="content"
						@resolveResource="$emit('resolveResource')"
						@unresolveResource="$emit('unresolveResource')"
					/>
				</span>
				<span v-if="canVerify" class="user-generated-content-header__meta__item">
					<span class="user-generated-content-header__separator" />
					<wnl-verify
						:resource="content"
						@verify="$emit('verify')"
						@unverify="$emit('unverify')"
					/>
				</span>
			</div>
		</div>
		<wnl-modal v-if="modalVisible" @closeModal="closeModal">
			<wnl-user-profile-modal :author="author" />
		</wnl-modal>
	</div>
</template>

<style lang="sass" scoped>
@import 'resources/assets/sass/variables'

.user-generated-content-header
	flex-grow: 1
	display: flex
	align-items: flex-start
	flex-direction: row
	line-height: 1em

	@media #{$media-query-tablet}
		align-items: center

	&__separator
		margin: 0 $margin-small-minus
		display: inline-block

	&__meta
		display: flex
		align-items: center

		&__item
			display: flex
			align-items: center

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
import { mapGetters } from 'vuex';

import WnlDelete from 'js/components/global/Delete';
import WnlResolve from 'js/components/global/Resolve';
import WnlVerify from 'js/components/global/Verify';
import WnlUserProfileModal from 'js/components/users/UserProfileModal';
import WnlModal from 'js/components/global/Modal';
import { timeFromS } from 'js/utils/time';
import moderatorFeatures from 'js/perimeters/moderator';

export default {
	components: { WnlModal, WnlDelete, WnlResolve, WnlUserProfileModal, WnlVerify },
	props: {
		author: {
			type: Object,
			required: true
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
		},
		resolvable: {
			type: Boolean,
			default: false
		},
	},
	perimeters: [ moderatorFeatures ],
	data() {
		return {
			modalVisible: false
		};
	},
	computed: {
		...mapGetters(['currentUserId']),
		canDelete() {
			return this.isCurrentUserAuthor || this.$moderatorFeatures.isAllowed('access');
		},
		canResolve() {
			return this.resolvable && this.$moderatorFeatures.isAllowed('access');
		},
		canVerify() {
			return this.$moderatorFeatures.isAllowed('access');
		},
		time() {
			return timeFromS(this.content.created_at);
		},
		isCurrentUserAuthor() {
			return this.currentUserId === this.author.user_id;
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
