<template>
    <div>
        welcome home!
        <DialogCustom></DialogCustom>
        <swiper></swiper>
    </div>
</template>

<script>
import RecentPostsWidget from './widgets/RecentPosts.vue';
import PagesWidget from './widgets/Pages.vue';
import Swiper from './partials/Swiper.vue';
import DialogCustom from './partials/Dialog.vue';
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
