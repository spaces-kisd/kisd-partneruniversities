export default {

  /**
	 * 3d globe:
	 * @see https://mapsapidocs.digitalglobe.com/docs/maps-api-mapboxjs
	 *
	 * glowing button: https://www.mapbox.com/about/team/marena-brinkhurst/
	 */
  init () {
    if (typeof mapboxThemeSettings.accessToken === 'undefined') {
      console.log('No access mapbox token specified. Add one in Backend -> Settings -> General')
      return
    }
    mapboxgl.accessToken = mapboxThemeSettings.accessToken
    mapboxThemeSettings.attributionControl = false
    return new mapboxgl.Map(
      mapboxThemeSettings
    )
  },
  getRendered (map, layerName) {
    const layer = map.getLayer(layerName)
    if (typeof layer === 'undefined') {
      // console.log( 'SolutionMapHelper.js', 'not yet there', layer);
      return false
    }
    return map.queryRenderedFeatures({ layers: [layerName] })
  },
  getAllVisible (map, e) {
    /**
		 * @todo handle zoom ( when a cluster shows multiple times on the world map...)
		 */
    const allPoints = []
    const unclustered = this.getRendered(map, 'unclustered-point')

    const allIds = unclustered.map((f) => f.properties.post_id)

    allPoints.push.apply(allPoints, unclustered.map((feature) => {
      feature.cluster = false
      return feature
    }))

    const features = this.getRendered(map, 'clusters')
    const clusterSource = map.getSource('solutions')

    const promises = []

    // https://stackoverflow.com/questions/10004112/how-can-i-wait-for-set-of-asynchronous-callback-functions
    // https://javascript.info/promise-chaining
    features.forEach((feature) => {
      const fProp = feature.properties
      // Get all points under a cluster
      promises.push(
        clusterSource.getClusterLeaves(fProp.cluster_id, fProp.point_count, 0, (err, aFeatures) => {
          // allPoints.push(aFeatures[0]
          // console.log(aFeatures);
          // allPoints.push(aFeatures[0]);
          /* 				allIds.concat(
					allIds,
					aFeatures.map( f => f.properties.post_id )
				); */
          aFeatures.forEach((e) => {
            allIds.push(e.properties.post_id)
          })
          allPoints.push.apply(
            allPoints,
            aFeatures.map(
              (feature) => {
                feature.cluster = fProp.cluster_id
                return feature
              }
            )
          )
          // allPoints = allPoints.concat(aFeatures, allPoints);
          // allPoints = [...allPoints, aFeatures];//allPoints.concat(aFeatures);
          // console.log('getClusterLeaves', err, aFeatures);
        })
      )
    })
    return new Promise((resolve, reject) => {
      setTimeout(() => resolve(allIds), 2000) // (*)
    })

    Promise.all(promises).then(() => {
      console.log('all promises done!')
      // returned data is in arguments[0], arguments[1], ... arguments[n]
      // you can process it here
      return allIds
    }, (err) => {
      // error occurred
    })
    // return allIds;
    // return JSON.parse(JSON.stringify(allIds));
    /* 		return allPoints.filter(function (item) {
					return item !== undefined;
				}); */
  },
  centerOn (map, coordinates, zoom = 3) {
    // https://docs.mapbox.com/mapbox-gl-js/example/flyto-options/
    // https://stackoverflow.com/questions/47224472/define-center-of-map-in-mapbox
    const rect = document.getElementById('map').getBoundingClientRect()
    const viewportX = [rect.bottom]
    const shiftScreen = viewportX
    map.flyTo({
      center: coordinates,
      /* offset: [shiftScreen, shiftScreen], */
      zoom
    })
  },
  addEvents (map) {
    const MapConfig = this
    // inspect a cluster on click
    map.on('click', 'clusters', (e) => {
      MapConfig.getAllVisible(map, e)
      const features = map.queryRenderedFeatures(e.point, { layers: ['clusters'] })
      const clusterId = features[0].properties.cluster_id
      map.getSource('solutions').getClusterExpansionZoom(clusterId, (err, zoom) => {
        if (err) return

        MapConfig.centerOn(map, features[0].geometry.coordinates, zoom)
      })
    })

    map.on('mouseenter', 'clusters', () => {
      map.getCanvas().style.cursor = 'pointer'
    })
    map.on('mouseleave', 'clusters', () => {
      map.getCanvas().style.cursor = ''
    })

    /* 		map.on('click', 'unclustered-point', function (e) {
					//console.log(e.features);
					let description = "rata";
						new mapboxgl.Popup()
						.setLngLat(e.lngLat)
						.setHTML(e.features[0].properties.title)
						.addTo(map);
					MapConfig.getAllVisible(map, e);
				}); */

    map.on('mouseenter', 'unclustered-point', () => {
      map.getCanvas().style.cursor = 'pointer'
    })
    map.on('mouseleave', 'unclustered-point', () => {
      map.getCanvas().style.cursor = ''
    })
  },
  addData (map, data) {
    map.addSource('solutions', {
      type: 'geojson',
      // Point to GeoJSON data. This example visualizes all M1.0+ solutions
      // from 12/22/15 to 1/21/16 as logged by USGS' Earthquake hazards program.
      data,
      cluster: true,
      clusterMaxZoom: 9, // Max zoom to cluster points on
      clusterRadius: 15, // Radius of each cluster when clustering points (defaults to 50)
      tolerance: 3
    })

    map.addLayer({
      id: 'clusters',
      type: 'circle',
      source: 'solutions',
      filter: ['has', 'point_count'],
      paint: {
        // Use step expressions (https://www.mapbox.com/mapbox-gl-js/style-spec/#expressions-step)
        // with three steps to implement three types of circles:
        //   * Blue, 20px circles when point count is less than 100
        //   * Yellow, 30px circles when point count is between 100 and 750
        //   * Pink, 40px circles when point count is greater than or equal to 750
        'circle-color': [
          'step',
          ['get', 'point_count'],
          '#ED624B',
          100,
          '#ED624B',
          750,
          '#ED624B'
        ],
        'circle-radius': [
          'step',
          ['get', 'point_count'],
          15,
          5,
          20,
          10,
          25
        ]
      }
    })

    map.addLayer({
      id: 'cluster-count',
      type: 'symbol',
      source: 'solutions',
      filter: ['has', 'point_count'],
      layout: {
        'text-field': '{point_count_abbreviated}',
        'text-font': ['DIN Offc Pro Medium', 'Arial Unicode MS Bold'],
        'text-size': 12
      }
    })

    map.addLayer({
      id: 'unclustered-point',
      type: 'circle',
      source: 'solutions',
      filter: ['!', ['has', 'point_count']],
      paint: {
        'circle-color': '#ED624B',
        'circle-radius': 10,
        'circle-stroke-width': 1,
        'circle-stroke-color': '#fff'
      }
    })
  }
}
