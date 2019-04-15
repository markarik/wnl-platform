<template>

	<div>
		<wnl-find-users
			@updateItems="onUpdateItems"
			@onKeyDown="onKeyDown"
		/>
		<wnl-text-loader v-if="loadingConversation">
			Rozpoczynam rozmowę...
		</wnl-text-loader>
		<div class="metadata aligncenter margin vertical" v-else-if="loadingConversationFailure">
			Nie udało się rozpocząć rozmowy.
			Spróbuj jeszcze raz.
		</div>
		<div v-else>
			<wnl-message-link
					v-for="(room, index) in roomsToShow"
					:key="index"
					:user-id="getInterlocutor(room).user_id"
					:room-id="room.id"
					@navigate="onNavigate"
					@beforeNavigate="onNavigateStart"
					ref="messageLink"
				>
				<wnl-conversation-snippet
					:key="index"
					:room="room"
					:both-names="true"
					:is-active="index === activeIndex"
					:profile="getInterlocutor(room)"
				/>
			</wnl-message-link>
		</div>
	</div>
</template>

<script>
import WnlAutocompleteKeyboardNavigation from 'js/mixins/autocomplete-keyboard-navigation';
import ConversationSnippet from 'js/components/messages/ConversationSnippet';
import FindUsers from 'js/components/messages/FindUsers';
import MessageLink from 'js/components/global/MessageLink';
import { mapGetters } from 'vuex';

export default {
	name: 'UsersAutocomplete',
	components: {
		'wnl-conversation-snippet': ConversationSnippet,
		'wnl-find-users': FindUsers,
		'wnl-message-link': MessageLink
	},
	mixins: [WnlAutocompleteKeyboardNavigation],
	data() {
		return {
			items: [],
			loadingConversation: false,
			loadingConversationTimer: null,
			loadingConversationFailure: false
		};
	},
	computed: {
		...mapGetters(['currentUserId', 'currentUserProfile', 'currentUserProfileId']),
		...mapGetters('chatMessages', ['getRoomForPrivateChat']),
		roomsToShow() {
			return this.items.map(profile => {
				const room = this.getRoomForPrivateChat(profile.user_id);

				return {
					messages: [],
					...room,
					profiles: this.currentUserProfileId === profile.id ? [profile] : [this.currentUserProfile, profile]
				};
			});
		}
	},
	methods: {
		onNavigate() {
			this.loadingConversation = false;
			clearTimeout(this.loadingConversationTimer);
			this.$emit('close');
		},
		onNavigateStart() {
			clearTimeout(this.loadingConversationTimer);
			this.loadingConversation = true;
			this.loadingConversationTimer = setTimeout(() => {
				this.onLoadingConversationFailure();
			}, 10000);
		},
		onLoadingConversationFailure() {
			this.loadingConversation = false;
			this.loadingConversationFailure = true;
		},
		onItemChosen(item, itemIndex) {
			this.onNavigateStart();
			this.$refs.messageLink[itemIndex].navigate()
				.then(this.onNavigate);
		},
		onUpdateItems(items) {
			clearTimeout(this.loadingConversationTimer);
			this.items = items;
		},
		getInterlocutor(room) {
			const profile = room.profiles.find(profile => profile.user_id !== this.currentUserId) || {};
			if (profile.id) return profile;
			return this.currentUserProfile;
		},
		// override method from mixin - don't emit close event until room is created
		onEnter(evt) {
			const activeIndex = this.activeIndex;

			if (activeIndex < 0) return;

			this.$set(this.items[activeIndex], 'active', false);
			this.onItemChosen(this.items[activeIndex], activeIndex);

			evt.preventDefault();
			evt.stopPropagation();
			return false;
		},
	}
};
</script>
