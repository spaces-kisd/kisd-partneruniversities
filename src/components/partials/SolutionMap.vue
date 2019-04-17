<template>
  <div id="map"></div>
</template>

<script>
import axios from "axios";
/* import mapboxgl from "mapbox-gl";
window.mapboxgl = mapboxgl; */
import MapConfig from "./SolutionMapHelper.js";
import { mapGetters, mapState, mapMutations } from "vuex";
import * as types from "../../store/mutation-types";

export default {
  data() {
    return {
      post: false,
      myMap: false
    };
  },
  computed: {
    ...mapGetters(["getSelected", "visible_single", "getSelectedFeature"])
  },
  mounted() {
    var sourceData = [];
    this.myMap = MapConfig.init();
    // disable map rotation using right click + drag
    this.myMap.dragRotate.disable();
    // disable map rotation using touch rotation gesture
    this.myMap.touchZoomRotate.disableRotation();
    this.myMap.addControl(
      new mapboxgl.NavigationControl({
        showCompass: false
        //todo: add mediaquery to hide on mobile.
      }),
      "bottom-left"
    );
    this.myMap.addControl(
      new mapboxgl.AttributionControl({
        compact: true
      }),
      "bottom-left"
    );
    let added = false;
    var comp = this;
    /* var types = types; */
    comp.myMap.on("sourcedata", sourceCallback);

    axios.get("/wp-json/map/v1/features/solution").then(response => {
      sourceData = response.data;
      sourceCallback();
    });

    function sourceCallback(e) {
      if (comp.myMap.isStyleLoaded() && !added && !_.isEmpty(sourceData)) {
        added = true;
        MapConfig.addData(comp.myMap, sourceData);
        MapConfig.addEvents(comp.myMap);
        comp.myMap.on("click", "unclustered-point", function(e) {
          //console.log("soluton_map commit", e.features[0].properties);
          comp.$store.commit(
            types.FEATURE_SELECTED,
            e.features[0].properties.feature_id
          );
          comp.$router.push(e.features[0].properties.link_relative);
        });
      }

      if (comp.myMap.getSource("solutions") && comp.myMap.isSourceLoaded) {
        //console.log('loaded source', MapConfig.getAllVisible(comp.myMap));
        //console.log(this.myMap.getSource("solutions"));
        //console.log(this.myMap.querySourceFeatures('solutions', {filter : ''}));
      }
    }

    this.myMap.on("moveend", function() {
      /*       if (comp.myMap) {
        MapConfig.getAllVisible(comp.myMap).then( function( features ) {
          comp.$store.commit(types.STORE_VISIBLE_FEATURES, features );
        });
      } */
    });
  },
  beforeMount() {
    //this.getPost();
  },
  watch: {
    getSelected(newValue, oldValue) {
      //console.log("watch feature", newValue);
      //console.log(this.myMap);
      /*       this.myMap.easeTo({
        center: newValue.geometry.coordinates
      }); */

      var rect = document.getElementById("map").getBoundingClientRect();
      var viewportX = [rect.bottom];
      var shiftScreen = viewportX;
      var offsetX = 0;
      //console.log('this.visible_single', this.visible_single)
      if (window.innerWidth > 650 && this.visible_single) {
        offsetX = 650 + (window.innerWidth - 650) / 2 - window.innerWidth / 2;
      }
      //console.log('offsetX', offsetX);

      if (!_.isUndefined(this.getSelectedFeature)) {
        this.myMap.flyTo({
          center: this.getSelectedFeature.geometry.coordinates,
          offset: [offsetX, 0],
          zoom: Math.min(this.myMap.getZoom() + 1, 8)
        });
        //console.log('geo', this.getSelectedFeature.geometry);
      }
      //console.log(newValue.geometry.coordinates);
    }
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
  width: 100vw; /* a little hack so the map width does not change when scrollbar is showing. */
  z-index: 0;
}
.mapboxgl-ctrl-bottom-right,
.mapboxgl-ctrl-top-right {
  right: 17px;
}
@media only screen and (max-width: 1280px) {
 .mapboxgl-ctrl-group, .mapboxgl-compact {
    display: none;
  }
}

.mapboxgl-ctrl-bottom-left .mapboxgl-ctrl {
  margin-left: calc(1% + 26px);
}
.mapboxgl-compact.mapboxgl-compact {
  bottom: 0px;
  position: absolute;
  margin-left: 200px;
}
/** zoom **/
.vertical .mapboxgl-ctrl-bottom-right .mapboxgl-ctrl-group {
  margin-right: 300px;
}
</style>





