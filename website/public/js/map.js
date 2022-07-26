function initMap() {

  axios.get('/api/data')
    .then(function (response) {

        var data = response.data;
        var number = 9;
        var points = 9;
        var flag = 0;

        for (let index = data.length -1; index >= data.length - points; index--) {

          if (data[index].latitude == null || data[index].longitude == null) {
            points++;
            continue;
          }

          var loc = { lat: Number(data[index].latitude), lng: Number(data[index].longitude) };

          if (flag == 0) {
            var map = new google.maps.Map(document.getElementById("map"), {
              zoom: 19,
              center: loc,
              mapTypeId: 'satellite',
              heading: 90,
              tilt: 45,
              scaleControl: true,
            });

            flag = 1;
          }

          const infoWindow = new google.maps.InfoWindow({
            content: "Data: " + data[index].date + '<br/>' +
                      "Hora: " + data[index].time + '<br/>' +
                      "Temperatura: " + data[index].temperature + 'ºC<br/>' + 
                      "Humidade: " + data[index].humidity + '%<br/>' +
                      "Indice de calor: " + data[index].heat_index + 'ºC<br/>' +
                      "Luminosidade: " + data[index].luminosity + '%<br/>' +
                      "Latitude: " + data[index].latitude + '<br/>' +
                      "Longitude: " + data[index].longitude + '<br/>' +
                      "Altitude: " + data[index].altitude + ' m<br/>',
          });
          
          const marker = new google.maps.Marker({
            position: loc,
            label: String(number),
            map: map,
          });
          number--;

          marker.addListener("click", () => {
            infoWindow.close();
            infoWindow.open({
              anchor: marker,
              map,
              shouldFocus: true,
            });
          });
        }
  })
  .catch(error => {
    alert("Ocorreu um erro ao carregar o mapa\n" + error);
  })
}