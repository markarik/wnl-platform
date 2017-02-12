<template>
    <nav class="nav has-shadow">
        <div class="container">
            <div class="nav-left">
                <!--<a class="nav-item" href="{{ route('home') }}">-->
                    <!--<img src="{{ asset('/images/wnl-logo.svg') }}" alt="Logo Więcej niż LEK">-->
                <!--</a>-->
            </div>

            <span class="nav-toggle">
                <span></span>
                <span></span>
                <span></span>
            </span>

            <div class="nav-right nav-menu">
                <!--&lt;!&ndash; Authentication Links &ndash;&gt;-->
                <!--@if (Auth::guest())-->
                <!--<a href="{{ url('/login') }}" class="nav-item">-->
                    <!--@lang('nav.login-link-content')-->
                <!--</a>-->
                <!--@else-->
                <!--<a href="{{ url('/logout') }}" class="nav-item"-->
                   <!--onclick="event.preventDefault();-->
                                         <!--document.getElementById('logout-form').submit();">-->
                    <!--@lang('nav.logout-link-content')-->
                <!--</a>-->

                <!--<form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">-->
                    <!--{{ csrf_field() }}-->
                <!--</form>-->
                <!--@endif-->
                {{ currentUserName }}
            </div>
        </div>
    </nav>
</template>

<script>
    export default {
        name: 'Navbar',
        computed: {
            currentUserName () {
                const currentUser = this.$store.state.users.current
                return currentUser.first_name + ' ' + currentUser.last_name
            }
        }
    }
</script>
