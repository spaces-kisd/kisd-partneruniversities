<template>
    <div>
        welcome home!
        <DialogCustom></DialogCustom>
        <swiper></swiper>
    </div>
</template>

<script>
import RecentPostsWidget from './widgets/RecentPosts'
import PagesWidget from './widgets/Pages'
import Swiper from './partials/Swiper'
import DialogCustom from './partials/Dialog'
//import SolutionMap from './partials/SolutionMap'

export default {
    components: {
        RecentPostsWidget,
        PagesWidget,
        Swiper,
        DialogCustom
    }
}
</script>

<style lang="scss">
    .jumbotron {
        margin-top: 5%;
    }
</style>