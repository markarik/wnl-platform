@if (!empty($user->profile->avatar_url))
	<img src="{{$user->profile->avatar_url}}" class="a-avatar -large"/>
@elseif (!empty($user->initials))
	<span class="a-avatar -automatic -large">
		{{$user->initials}}
	</span>
@else
	<i class="o-navigation__item a-icon a-avatar -large fa-user"></i>
@endif
