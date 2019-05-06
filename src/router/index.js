import _ from "lodash";
import Vue from "vue";
import Router from "vue-router";

// Components
import Home from "../components/Home.vue";
import Category from "../components/Types/Category.vue";
import Solution from "../components/Types/Solution.vue";
import PostPage from "../components/Types/PostPage.vue";

Vue.use(Router);

console.log(vueWp);
const rootPath = vueWp.path;
/**
 * some examples: https://github.com/vuejs/vue-router/blob/dev/examples/route-matching/app.js
 */
const router = new Router({
  routes: [
    {
      path: "/",
      name: "App"
    },
    {
      path: "/home",
      name: "Home",
      component: Home, //menu open
      props: {}
    },
    {
      // Assuming you're using the default permalink structure for posts
      path: '/solution/:postSlug',
      name: 'Solution',
      component: Solution
    },
    {
      // nested cats have grandparent/parent/current-cat. add parent as var?
      path: '/category*/:categorySlug',
      name: 'Post Category',
      component: Category
    },
    {
      // nested cats have grandparent/parent/current-cat. add parent as var?
      path: '/solutions*/:categorySlug',
      name: 'Solution Category',
      component: Category
    },
    {
      path: "/:slug",
      name: "Page",
      component: PostPage,
      props: { postType: 'pages' }
    },
    {
      // Assuming you're using the default permalink structure for posts
      path: '/:year/:month/:day/:slug',
      name: 'Post',
      component: PostPage,
      props: { postType: 'posts' }
    },
  ],
  mode: "history",
  base: rootPath,

  // Prevents window from scrolling back to top
  // when navigating between components/views
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition;
    } else {
      return { x: 0, y: 0 };
    }
  }
});

router.afterEach((to, from) => {
  // Add a body class specific to the route we're viewing
  //todo: debug. for categories & custom post types.
/*   let body = document.querySelector("body");
  let bodyClasses = body.className.split(" ");

  if (bodyClasses.length > 0) {
    const newBodyClasses = bodyClasses.filter(theClass =>
      theClass.startsWith("vue--page--")
    );
    newBodyClasses.forEach(c => body.classList.remove(c));
  }

  const slug = _.isEmpty(to.params.postSlug)
    ? to.params.pageSlug
    : to.params.postSlug;

  body.classList.add("vue--page--" + slug); */
});

export default router;
