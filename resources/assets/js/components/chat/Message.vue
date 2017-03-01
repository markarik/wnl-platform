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
					<small>{{ formattedTime }}</small>
					<br>
					<slot></slot>
				</p>
			</div>
		</div>
	</article>
</template>

<script>
	export default{
		props: ['username', 'time'],
		computed: {
			formattedTime () {
				let date = new Date(this.time);
				return date.toLocaleTimeString();
			}
		}
	}

</script>
