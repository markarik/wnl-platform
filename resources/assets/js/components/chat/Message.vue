<template>
    <article class="media">
        <figure class="media-left">
            <p class="image is-64x64">
                <img src="http://bulma.io/images/placeholders/128x128.png">
            </p>
        </figure>
        <div class="media-content">
            <div class="content">
                <p>
                    <strong>{{ username }}</strong>
                    <small>{{ timeago }}</small>
                    <br>
                    <slot></slot>
                </p>
            </div>
        </div>
    </article>
</template>

<script>
    export default{
        props: ['username', 'timeago']
    }

</script>
