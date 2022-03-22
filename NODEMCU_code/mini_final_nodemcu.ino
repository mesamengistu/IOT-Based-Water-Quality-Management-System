#include<SoftwareSerial.h>
SoftwareSerial SUART(4,5);
#include <ESP8266WiFi.h>
#include <ESP8266HTTPClient.h>
#include <WiFiClient.h>
#include <Arduino_JSON.h>
const char* ssid     = "fool";
const char* password = "";
const char* serverName = "http://192.168.43.243/NODEMCU/php/insert.php";
const char* serverName2 = "http://192.168.43.243/NODEMCU/php/mes.json";
String apiKeyValue = "tPmAT5Ab3j7F9";
String sensorReadings;
String prev_interva = "0.5";
String prev_status ="OPEN";
String sensorReadingsArr[3];
const byte numChars = 32;
char receivedChars[numChars];
char tempChars[numChars];  
char gas_sensor[numChars] ={0}; //gas_sensor  water_level_sensor ph_sensor temp_sensor
float water_level_sensor =0.0;
float ph_sensor =0.0;
float temp_sensor =0.0;
boolean newData = false;
void  setup()
{
  SUART.begin(9600);
  Serial.begin(9600);
}
void loop()
{
 read_time_interval();
 recvWithStartEndMarkers();
    if (newData == true) {
        strcpy(tempChars, receivedChars);
            // this temporary copy is necessary to protect the original data
            //   because strtok() used in parseData() replaces the commas with \0
        parseData();
        showParsedData();
        newData = false;
    }
}
void read_time_interval(){
  if(WiFi.status()== WL_CONNECTED){      
      sensorReadings = httpGETRequest(serverName2);
      JSONVar myObject = JSON.parse(sensorReadings);
      if (JSON.typeof(myObject) == "undefined") {Serial.println("Parsing input failed!");return;}
       JSONVar t_interval = myObject["interval"];
       JSONVar v_status = myObject["status"];
      sensorReadingsArr[0] = (t_interval);
      sensorReadingsArr[1] = (v_status);
      if((sensorReadingsArr[0] != prev_interva)|| (sensorReadingsArr[1] != prev_status)){
        prev_interva = sensorReadingsArr[0];
        prev_status = sensorReadingsArr[1];
        String snt = "<" + prev_interva + "," + prev_status + ">";
        SUART.print(snt); 
         delay(400);
        SUART.flush();
        Serial.println(prev_interva);
        }
    }
    else{
      Serial.println("WiFi Disconnected");
    }
 }
 String httpGETRequest(const char* serverName2 ) {
  HTTPClient http; 
  http.begin(serverName2);
  int httpResponseCode = http.GET();
  String payload = "{}"; 
  if (httpResponseCode>0) {/*Serial.print("HTTP Response code: ");Serial.println(httpResponseCode);*/payload = http.getString();}
  else {Serial.print("Error code: ");Serial.println(httpResponseCode);}
  http.end();
  return payload;
}
void recvWithStartEndMarkers() {
    static boolean recvInProgress = false;
    static byte ndx = 0;
    char startMarker = '<';
    char endMarker = '>';
    char rc;
    while (SUART.available() > 0 && newData == false) {
        rc = SUART.read();

        if (recvInProgress == true) {
            if (rc != endMarker) {
                receivedChars[ndx] = rc;
                ndx++;
                if (ndx >= numChars) {
                    ndx = numChars - 1;
                }
            }
            else {
                receivedChars[ndx] = '\0'; // terminate the string
                recvInProgress = false;
                ndx = 0;
                newData = true;
            }
        }

        else if (rc == startMarker) {
            recvInProgress = true;
        }
    }
}
void parseData() {      // split the data into its parts
    //gas_sensor  water_level_sensor ph_sensor temp_sensor
    char * strtokIndx; // this is used by strtok() as an index
    strtokIndx = strtok(tempChars,",");      // get the first part - the string
    strcpy(gas_sensor, strtokIndx); // copy it to messageFromPC
    strtokIndx = strtok(NULL, ","); // this continues where the previous call left off
    water_level_sensor = atof(strtokIndx);     // convert this part to an integer
    strtokIndx = strtok(NULL, ",");
    ph_sensor = atof(strtokIndx);  
    strtokIndx = strtok(NULL, ",");
    temp_sensor = atof(strtokIndx);// convert this part to a float
}
void showParsedData() {
    Serial.print("Message ");
    Serial.flush();
    Serial.println(gas_sensor);
    Serial.flush();
    Serial.print("Integer ");
    Serial.flush();
    Serial.println(water_level_sensor);
    Serial.flush();
    Serial.print("Float ");
    Serial.flush();
    Serial.println(ph_sensor);
    Serial.flush();
    insert_DB();
}
void   insert_DB(){
  if(WiFi.status()== WL_CONNECTED){
    HTTPClient http;
    http.begin(serverName);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");
     //gas_sensor  water_level_sensor ph_sensor temp_sensor //gas_sensor  water_level_sensor ph_sensor temp_sensor gas_sensor water_level_sensor ph_sensor temp_sensor
     String httpRequestData =  "api_key=" + apiKeyValue + "&gas_sensor=" + gas_sensor + "&water_level_sensor=" + water_level_sensor +"&ph_sensor=" + ph_sensor+ "&temp_sensor=" + temp_sensor +"";
    Serial.print("httpRequestData: ");
    Serial.println(httpRequestData);
    int httpResponseCode = http.POST(httpRequestData);   
    if (httpResponseCode>0) {
      Serial.print("HTTP Response code: ");
      Serial.println(httpResponseCode);
    }
    else {
      Serial.print("Error code: ");
      Serial.println(httpResponseCode);
    }
    http.end();
  }
  else {
    Serial.println("WiFi Disconnected");
  }
  //delay(30000);  
  }
