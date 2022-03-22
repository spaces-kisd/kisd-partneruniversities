<template>
  <div id="map"></div>
</template>

<script>
import axios from 'axios'
/* import mapboxgl from "mapbox-gl";
window.mapboxgl = mapboxgl; */
import { mapGetters, mapState, mapMutations } from 'vuex'
import MapConfig from './SolutionMapHelper.js'
import * as types from '../../store/mutation-types'
import * as _ from 'lodash'

export default {
  data () {
    return {
      post: false,
      myMap: false
    }
  },
  computed: {
    ...mapGetters(['getSelected', 'visible_single', 'getSelectedFeature'])
  },
  mounted () {
    let sourceData = []
    this.myMap = MapConfig.init()
    // disable map rotation using right click + drag
    this.myMap.dragRotate.disable()
    // disable map rotation using touch rotation gesture
    this.myMap.touchZoomRotate.disableRotation()
    this.myMap.addControl(
      new mapboxgl.NavigationControl({
        showCompass: false
        // todo: add mediaquery to hide on mobile.
      }),
      'bottom-left'
    )
    this.myMap.addControl(
      new mapboxgl.AttributionControl({
        compact: true
      }),
      'bottom-left'
    )
    let added = false
    const comp = this
    /* var types = types; */
    comp.myMap.on('sourcedata', sourceCallback)

    axios.get('/wp-json/map/v1/features/solution').then((response) => {
      sourceData = response.data
      sourceCallback()
    })

    function sourceCallback (e) {
      if (comp.myMap.isStyleLoaded() && !added && !_.isEmpty(sourceData)) {
        added = true
        MapConfig.addData(comp.myMap, sourceData)
        MapConfig.addEvents(comp.myMap)
        comp.myMap.on('click', 'unclustered-point', (e) => {
          // console.log("soluton_map commit", e.features[0].properties);
          comp.$store.commit(
            types.FEATURE_SELECTED,
            e.features[0].properties.feature_id
          )
          comp.$router.push(e.features[0].properties.link_relative)
        })
      }

      if (comp.myMap.getSource('solutions') && comp.myMap.isSourceLoaded) {
        // console.log('loaded source', MapConfig.getAllVisible(comp.myMap));
        // console.log(this.myMap.getSource("solutions"));
        // console.log(this.myMap.querySourceFeatures('solutions', {filter : ''}));
      }
    }

    this.myMap.on('moveend', () => {
      /*       if (comp.myMap) {
        MapConfig.getAllVisible(comp.myMap).then( function( features ) {
          comp.$store.commit(types.STORE_VISIBLE_FEATURES, features );
        });
      } */
    })
  },
  beforeMount () {
    // this.getPost();
  },
  watch: {
    getSelected (newValue, oldValue) {

      const rect = document.getElementById('map').getBoundingClientRect()
      const viewportX = [rect.bottom]
      const shiftScreen = viewportX
      let offsetX = 0
      // console.log('this.visible_single', this.visible_single)
      if (window.innerWidth > 650 && this.visible_single) {
        offsetX = 650 + (window.innerWidth - 650) / 2 - window.innerWidth / 2
      }

      if (!_.isUndefined(this.getSelectedFeature)) {
        this.myMap.flyTo({
          center: this.getSelectedFeature.geometry.coordinates,
          offset: [offsetX, 0],
          zoom: Math.min(this.myMap.getZoom() + 1, 8)
        })
      }
    }
  },
  methods: {},
  components: {}
}
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
  .mapboxgl-ctrl-group,
  .mapboxgl-compact {
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
