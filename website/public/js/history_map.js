function initMap() {
  var id = document.getElementById("id").getAttribute("data");
  
  axios.get('/api/data/' + id)
    .then(function (response) {

      var data = response.data;

      if (data.latitude == null || data.longitude == null) {
        const map = new google.maps.Map(document.getElementById("map"), {
          zoom: 6,
          center: { lat: 39.7443, lng: -8.80725 },
          mapTypeId: 'satellite',
          heading: 90,
          tilt: 45,
          scaleControl: true,
        });
        return;
      }

      var loc = { lat: Number(data.latitude), lng: Number(data.longitude) };

      const map = new google.maps.Map(document.getElementById("map"), {
        zoom: 18,
        center: loc,
        mapTypeId: 'satellite',
        heading: 90,
        tilt: 45
      });
      
      const infoWindow = new google.maps.InfoWindow({
        content: "Data: " + data.date + '<br/>' +
                  "Hora: " + data.time + '<br/>' +
                  "Temperatura: " + data.temperature + 'ºC<br/>' + 
                  "Humidade: " + data.humidity + '%<br/>' +
                  "Indice de calor: " + data.heat_index + 'ºC<br/>' +
                  "Luminosidade: " + data.luminosity + '%<br/>' +
                  "Latitude: " + data.latitude + '<br/>' +
                  "Longitude: " + data.longitude + '<br/>' +
                  "Altitude: " + data.altitude + ' m<br/>',
      });
        
      const marker = new google.maps.Marker({
        position: loc,
        map: map,
      });

      marker.addListener("click", () => {
        infoWindow.close();
        infoWindow.open({
          anchor: marker,
          map,
          shouldFocus: true,
        });
      });
  })
  .catch(error => {
    alert("Ocorreu um erro ao carregar o mapa\n" + error);
  })    
}