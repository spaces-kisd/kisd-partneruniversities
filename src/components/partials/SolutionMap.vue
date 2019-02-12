<template>
  <div id="map"></div>
</template>

<script>
import MapConfig from "./SolutionMapHelper.js";
export default {
  data() {
    return {
      post: false
    };
  },
  computed: {},
  mounted() {
    //this.$store.dispatch('getFeatures')
    var sourceData = [];
    var myMap = MapConfig.init();

    axios.get("/wp-json/map/v1/features/solution").then(response => {
      sourceData = response.data;
      myMap.on("sourcedata", sourceCallback);
      MapConfig.addData(myMap, sourceData);
      MapConfig.addEvents(myMap);

      console.log(sourceData)
    });

    //myMap.on("load", function() {});
    //myMap.once("style.load", ev => {});
    myMap.on("moveend", function() {
      console.log('mv', MapConfig.getAllVisible(myMap));
      //console.log("moveend");
      /*         if (map.isFullyLoaded()) {
            renderSidebar();
          } */
    });

    function sourceCallback(e) {
      //console.log(e);
      // assuming 'map' is defined globally, or you can use 'this'
      if (myMap.getSource("solutions") && e.isSourceLoaded) {
        console.log('loaded source', MapConfig.getAllVisible(myMap));
        //console.log(myMap.getSource("solutions"));
        //console.log(myMap.querySourceFeatures('solutions', {filter : ''}));
      }
    }
  },
  beforeMount() {
    //this.getPost();
  },
  methods: {},
  components: {}
};
</script>

<style>
body {
  margin: 0;
  padding: 0;
}
#map {
  position: fixed;
  top: 0;
  bottom: 0;
  width: 100%;
  z-index: 0;
}
</style>





