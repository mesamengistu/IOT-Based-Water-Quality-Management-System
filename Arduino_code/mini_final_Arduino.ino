#include<SoftwareSerial.h>
SoftwareSerial SUART(2,3);
const byte numChars = 32;
String gas_new ="GAS NOT DETECTED" ;
float water_new =92.1;
float ph_new  =7.2;
float temp_new =27.98 ;
char receivedChars[numChars];
char tempChars[numChars]; 
char v_status[numChars] ={0}; 
boolean newData = false;
float new_interval = 0.5;
String valve_status = "OPEN";
int valve_pin = 4;
long int lastTime =millis();
void setup()
{ 
 pinMode(valve_pin,OUTPUT);
 SUART.begin(9600);
 Serial.begin(9600);
}
void loop()
{
  recvWithStartEndMarkers();
  if (newData == true)
  {
        strcpy(tempChars, receivedChars);// this temporary copy is necessary to protect the original data    
        parseData();//   because strtok() used in parseData() replaces the commas with \0
        showParsedData();
        newData = false;
  }
  if(valve_status =="OPEN")
  {
    digitalWrite(valve_pin,LOW);
  }
  if( valve_status == "CLOSE")
  {
    digitalWrite(valve_pin,HIGH);
  }
  //Serial.print("time Interval");
  //Serial.println(new_interval);
  if((millis()-lastTime) > (new_interval*60000))
  {
    String All_data = "<" + gas_new + "," + String(water_new) + "," + String(ph_new)+ "," + String(temp_new) + ">";
     Serial.print(All_data);
     SUART.print(All_data);
    // delay(4000);
     SUART.flush();
    lastTime =millis();
  }
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
void parseData() {      
    char * strtokIndx; // this is used by strtok() as an index
    strtokIndx = strtok(tempChars,",");      // get the first part - the string
    new_interval = atoi(strtokIndx); 
    strtokIndx = strtok(NULL, ",");
    strcpy(v_status, strtokIndx); // copy it to messageFromPC
}
void showParsedData() {
    Serial.print("Interval ");
    Serial.flush();
    Serial.println(new_interval);
    Serial.print("Status ");
    Serial.flush();
    valve_status = v_status;
    Serial.println(v_status);
}
