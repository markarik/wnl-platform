<template>

    <div id="myModal" class="modal">

        <span id="close" v-on:click="closeLightbox">&times;</span>

        <img class="modal-content" :src="currentImageSource">

        <a class="prev" v-on:click="minusSlides">&#10094;</a>
        <a class="next" v-on:click="plusSlides">&#10095;</a>

    </div>

</template>

<style lang="sass">
    .modal
        background-color: rgba(0,0,0,0.9)
        display: none
        overflow: auto
        padding-top: 100px
        position: fixed
        width: 100%
        z-index: 60

    .modal-content
        display: block
        margin: auto
        height: 100vh
        // max-width: 90%
        width: 100vw

    #close
        color: #f1f1f1
        font-size: 80px
        font-weight: bold
        position: absolute
        right: 35px
        transition: 0.3
        top: 35px

    .prev,
    .next
        cursor: pointer
        position: absolute
        top: 50%
        width: auto
        padding: 16px
        margin-top: -50px
        color: white
        font-weight: bold
        font-size: 20px
        transition: 0.6s ease
        border-radius: 0 3px 3px 0
        user-select: none
        -webkit-user-select: none

    .next
        right: 0
        border-radius: 3px 0 0 3px
</style>

<script>
export default {
    name: 'Fullscreen',
    props: ['imagesSources', 'currentImageSource'],
    methods: {
        closeLightbox() {
            document.getElementById('myModal').style.display = "none";
            this.$emit('childCurrentImageSourceRemove')
        },
        setImageSource() {
            document.querySelector('.modal-content').setAttribute('src', this.currentImageSource);
            document.getElementById('myModal').style.display = "block";
        },
        plusSlides() {
            this.$emit('nextImage');
        },
        minusSlides() {
            this.$emit('prevImage');
        },
    },
    mounted() {
        this.setImageSource()
    },
}
</script>
