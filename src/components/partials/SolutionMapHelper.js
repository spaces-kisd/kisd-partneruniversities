export default {

	/**
	 * 3d globe:
	 * @see https://mapsapidocs.digitalglobe.com/docs/maps-api-mapboxjs
	 */
	init: function(){
		mapboxgl.accessToken = 'pk.eyJ1IjoibWFja2JpcmQiLCJhIjoiY2pwbjl2aGkxMDB0ODQzbWR6OGUxcjY3YyJ9.U8dt-qpZG0b_iH68TVWAtQ';
		console.log('init');
		return new mapboxgl.Map({
			container: 'map',
			style: 'mapbox://styles/mackbird/cjpnapc0x04vs2ro5ert88gpq',
			center: [-103.59179687498357, 40.66995747013945],
			zoom: 3
		});
	},
	getAllVisible: function(map, e){
		/**
		 * @todo handle zoom ( when a cluster shows multiple times on the world map...)
		 */
		var allPoints = [];
		let unclustered = map.queryRenderedFeatures( {layers: ['unclustered-point']});
		allPoints.push.apply(allPoints, unclustered);

		let features =  map.queryRenderedFeatures({ layers: ['clusters'] });
		let clusterSource = map.getSource('solutions');
		features.forEach(feature => {
			let fProp = feature.properties;
			// Get all points under a cluster
			clusterSource.getClusterLeaves(fProp.cluster_id, fProp.point_count, 0, function(err, aFeatures){
				//allPoints.push(aFeatures[0]
				//console.log(aFeatures);
				//allPoints.push(aFeatures[0]);
				allPoints.push.apply(allPoints, aFeatures);
				//allPoints = allPoints.concat(aFeatures, allPoints);
				//allPoints = [...allPoints, aFeatures];//allPoints.concat(aFeatures);
				//console.log('getClusterLeaves', err, aFeatures);
			});
		});

		return allPoints;
		//console.log([].concat.apply([], allPoints));
	},
	addEvents: function(map){
		var MapConfig = this;
		// inspect a cluster on click
		map.on('click', 'clusters', function (e) {
			MapConfig.getAllVisible( map, e );
			var features = map.queryRenderedFeatures(e.point, { layers: ['clusters'] });
			var clusterId = features[0].properties.cluster_id;
			map.getSource('solutions').getClusterExpansionZoom(clusterId, function (err, zoom) {
				if (err)
					return;

				map.easeTo({
					center: features[0].geometry.coordinates,
					zoom: zoom
				});
			});
		});

		map.on('mouseenter', 'clusters', function () {
			map.getCanvas().style.cursor = 'pointer';
		});
		map.on('mouseleave', 'clusters', function () {
			map.getCanvas().style.cursor = '';
		});


		map.on('click', 'unclustered-point', function (e) {
			//console.log(e.features);
			let description = "rata";
			new mapboxgl.Popup()
				.setLngLat(e.lngLat)
				.setHTML(e.features[0].properties.title)
				.addTo(map);
			
			MapConfig.getAllVisible( map, e );
		});

		map.on('mouseenter', 'unclustered-point', function () {
			map.getCanvas().style.cursor = 'pointer';
		});
		map.on('mouseleave', 'unclustered-point', function () {
			map.getCanvas().style.cursor = '';
		});
	},
	addData: function(map, data){
		map.addSource("solutions", {
			type: "geojson",
			// Point to GeoJSON data. This example visualizes all M1.0+ solutions
			// from 12/22/15 to 1/21/16 as logged by USGS' Earthquake hazards program.
			data: data,
			cluster: true,
			clusterMaxZoom: 14, // Max zoom to cluster points on
			clusterRadius: 50, // Radius of each cluster when clustering points (defaults to 50)
			tolerance: 3
		});

		map.addLayer({
			id: "clusters",
			type: "circle",
			source: "solutions",
			filter: ["has", "point_count"],
			paint: {
				// Use step expressions (https://www.mapbox.com/mapbox-gl-js/style-spec/#expressions-step)
				// with three steps to implement three types of circles:
				//   * Blue, 20px circles when point count is less than 100
				//   * Yellow, 30px circles when point count is between 100 and 750
				//   * Pink, 40px circles when point count is greater than or equal to 750
				"circle-color": [
					"step",
					["get", "point_count"],
					"#51bbd6",
					100,
					"#f1f075",
					750,
					"#f28cb1"
				],
				"circle-radius": [
					"step",
					["get", "point_count"],
					20,
					100,
					30,
					750,
					40
				]
			}
		});

		map.addLayer({
			id: "cluster-count",
			type: "symbol",
			source: "solutions",
			filter: ["has", "point_count"],
			layout: {
				"text-field": "{point_count_abbreviated}",
				"text-font": ["DIN Offc Pro Medium", "Arial Unicode MS Bold"],
				"text-size": 12
			}
		});

		map.addLayer({
			id: "unclustered-point",
			type: "circle",
			source: "solutions",
			filter: ["!", ["has", "point_count"]],
			paint: {
				"circle-color": "#11b4da",
				"circle-radius": 4,
				"circle-stroke-width": 1,
				"circle-stroke-color": "#fff"
			}
		});

	}
}