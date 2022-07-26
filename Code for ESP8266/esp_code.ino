/**
  @file esp_code.ino
  @brief Code for ESP8266

  Code for ESP8266 to acquire data from sensors
  and send it to the server via Web API
  @date June 2022
  @authors Miguel Santos, Steven Lincango
*/

#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <DHT.h>
#include <SoftwareSerial.h>
#include <TinyGPSPlus.h>
#include <ArduinoJson.h>
#include <WiFiClientSecure.h>
#include <PubSubClient.h>


WiFiClientSecure wifiClient;
WiFiClient brokerClient;
HTTPClient httpClient;

// MQTT Broker
PubSubClient client(brokerClient);
const char *mqtt_broker = "x.x.x.x";
const char *topic = "configs";
const int mqtt_port = 1883;

const char *host = "https://meteodrones.tk/api/data";    //host to send data
const char *getHost = "https://meteodrones.tk/get-configs";    //host to get send configs

#define DHTPIN 2
#define DHTTYPE DHT11
DHT dht(DHTPIN, DHTTYPE);

SoftwareSerial SoftSerial(12, 13);
TinyGPSPlus gps;
char gpsBuffer[100];
char dateBuffer[100];
char timeBuffer[100];

StaticJsonDocument<1024> dataJson;
StaticJsonDocument<1024> configsJson;

unsigned long previousMillis = 0;
const long interval = 1000;    //delay to read data from DHT and LDR sensors
unsigned long previousMillis2 = 0;

float sendTime = 0.0;
float sendDistance = 0.0;
String type;

double initLat;
double initLng;
double distance = 0;
int flag = 0;
double altInterval = 0;
double initAlt = 0;

void setup() {
  Serial.begin(9600);
  SoftSerial.begin(9600);

  char ssid [] = "SSID";
  char pass [] = "PASSWORD";
  
  Serial.println("A ligar ao Wi-Fi...");
  WiFi.begin(ssid, pass);

  while (WiFi.status() != WL_CONNECTED) {
    Serial.print(".");
    delay(500);
  }
  Serial.println("\nLigado ao Wi-Fi com sucesso");

  Serial.print("Endereco IP: ");
  Serial.println(WiFi.localIP());
  Serial.print("Mascara de rede: ");
  Serial.println(WiFi.subnetMask());
  Serial.print("Endereco IP do Gateway: ");
  Serial.println(WiFi.gatewayIP());
  Serial.print("Potencia do sinal recebido: ");
  Serial.println(WiFi.RSSI());
  Serial.print("\n");

  //MQTT
  client.setServer(mqtt_broker, mqtt_port);
  client.setCallback(callback);
  while (!client.connected()) {
      String client_id = "esp8266-client-";
      client_id += String(WiFi.macAddress());
      Serial.printf("A conectar ao MQTT Broker\n");
      if (client.connect(client_id.c_str())) {
          Serial.println("Conectado ao MQTT Broker");
      } else {
          Serial.print("Erro ao conectar com o MQTT Broker. Erro: ");
          Serial.print(client.state());
          delay(2000);
      }
  }
  client.subscribe(topic);

  dht.begin();
  cleanJson();     //start json with null values
  setSendConfigs();    //configuration for send information
}

void loop() {
  while (SoftSerial.available() > 0) {
    if (gps.encode(SoftSerial.read())) {    //read data from GPS module
      printGPSData();
    }
  }

  unsigned long currentMillis = millis();
  if (currentMillis - previousMillis >= interval) {   //print DHT and LDR data
    previousMillis = currentMillis;
    printDHTData();
    printLDRData();
  }

  if (type == "T") {    //send data by time
    unsigned long currentMillis2 = millis();
    if (currentMillis2 - previousMillis2 >= (sendTime * 1000)) {
      previousMillis2 = currentMillis2;
      sendData();
    }
  }

  if (type == "D"){     //send data by distance
    if (distance >= sendDistance || altInterval >= sendDistance) {
      sendData();
      distance = 0;
      altInterval = 0;
      flag = 0;
    }
  }
  client.loop();    //for mqtt messages
}

void printGPSData()
{
  if (gps.location.isUpdated()) {
    Serial.println("\nGPS");

    double lat = gps.location.lat();
    double lng = gps.location.lng();
    double altitude = gps.altitude.meters();

    snprintf(gpsBuffer, sizeof(gpsBuffer),
             "Latitude: %.8f, Longitude: %.8f, Altitude: %.2f m",
             lat, lng, altitude);
    Serial.println(gpsBuffer);

    dataJson["latitude"] = lat;
    dataJson["longitude"] = lng;
    dataJson["altitude"] = altitude;

    if (flag == 0 && type == "D") {
      initLat = lat;
      initLng = lng;
      initAlt = altitude;
      flag = 1;
    }
    
    distance = TinyGPSPlus::distanceBetween(initLat, initLng, lat, lng);    //calculate distance traveled
    altInterval = altitude - initAlt;    //calculate distance traveled by altitude
    
    Serial.print("Distancia: ");
    Serial.print(distance);
    Serial.println(" m");
  }

  if (gps.date.isUpdated()) {
    int year = gps.date.year();
    int month = gps.date.month();
    int day = gps.date.day();

    snprintf(dateBuffer, sizeof(dateBuffer),
             "Data: %d-%02d-%02d",
             year, month, day);
    Serial.println(dateBuffer);

    dataJson["date"] = String(day) + '/' + String(month) + '/' + String(year);
  }

  if (gps.time.isUpdated()) {
    int hour = gps.time.hour();
    int minute = gps.time.minute();
    int second = gps.time.second();

    snprintf(timeBuffer, sizeof(timeBuffer),
             "Hora: %02d:%02d:%02d",
             hour, minute, second);
    Serial.println(timeBuffer);

    dataJson["time"] = String(hour) + ':' + String(minute) + ':' + String(second);
  }

  if (gps.speed.isUpdated()) {
    Serial.print("Velocidade: ");
    Serial.print(gps.speed.kmph());
    Serial.println(" km/h");
  }

  if (gps.satellites.isUpdated()) {
    Serial.print("Numero de satelites: ");
    Serial.println(gps.satellites.value());
  }
}

void printDHTData() {
  Serial.println("\nDHT");
  float humidity = dht.readHumidity();
  float temperature = dht.readTemperature();

  if (isnan(humidity) || isnan(temperature)) {
    Serial.println(F("Falha ao ler do sensor DHT!"));

    dataJson["temperature"] = 0;
    dataJson["humidity"] = 0;
    dataJson["heat_index"] = 0;
  }
  else {
    float heatIndex = dht.computeHeatIndex(temperature, humidity, false);    //compute heat index     //false to ºC
    Serial.print("Humidade = ");
    Serial.print(humidity);
    Serial.println("%");
    Serial.print("Temperatura = ");
    Serial.print(temperature);
    Serial.println("ºC");
    Serial.print("Indice de calor = ");
    Serial.print(heatIndex);
    Serial.println("ºC");

    dataJson["temperature"] = temperature;
    dataJson["humidity"] = humidity;
    dataJson["heat_index"] = heatIndex;
  }
}

void printLDRData() {
  Serial.println("\nLDR");
  int sensorValue = analogRead(A0);
  float luminosity = 100.0 - (sensorValue * (100.0 / 1024.0));
  Serial.print("Luminosidade = ");
  Serial.println(luminosity);
  dataJson["luminosity"] = luminosity;
}

void sendData() {
  Serial.println("\nSend Data");
  String requestBody;
  serializeJsonPretty(dataJson, requestBody);

  wifiClient.setInsecure();     //does not verify the certificate
  wifiClient.connect(host, 443);    //connect to host
  httpClient.begin(wifiClient, host);
  httpClient.addHeader("Content-Type", "application/json");
  int httpResponseCode = httpClient.POST(requestBody);    //send json

  if (httpResponseCode > 0) {    //get response
    String response = httpClient.getString();
    Serial.print("Status Code: ");
    Serial.println(httpResponseCode);
    Serial.println(response);
  }
  else {
    Serial.println("Ocorreu um erro ao enviar HTTP POST");
  }
  httpClient.end();

  cleanJson();    //clear json and start with null values
}

void cleanJson() {
  dataJson.clear();

  //start json with null values
  dataJson["latitude"] = nullptr;
  dataJson["longitude"] = nullptr;
  dataJson["altitude"] = nullptr;
  dataJson["date"] = "--/--/----";
  dataJson["time"] = "--:--:--";
  dataJson["luminosity"] = nullptr;
}

void setSendConfigs() {
  Serial.println("\nSet send configuration");

  wifiClient.setInsecure();    //does not verify the certificate
  wifiClient.connect(getHost, 443);    //connect to host
  httpClient.begin(wifiClient, getHost);
  int httpResponseCode = httpClient.GET();    //request send configuration

  if (httpResponseCode > 0) {    //get response
    String response = httpClient.getString();
    Serial.print("Status Code: ");
    Serial.println(httpResponseCode);
    Serial.println(response);

    deserializeJson(configsJson, response);
    sendTime = configsJson["time"];
    sendDistance = configsJson["meters"];
    type = String(configsJson["type"]);
  }
  else {
    Serial.println("Ocorreu um erro ao enviar HTTP GET");
  }
  httpClient.end();
}

//for mqtt messages
void callback(char *topic, byte *payload, unsigned int length) {
  String configurations = "";
  Serial.println("\nConfiguracoes: ");
  
  for (int i = 0; i < length; i++) {
      configurations += (char) payload[i];
  }
  Serial.println(configurations);
  Serial.println();
  deserializeJson(configsJson, configurations);
  sendTime = configsJson["time"];
  sendDistance = configsJson["meters"];
  type = String(configsJson["type"]);
}
