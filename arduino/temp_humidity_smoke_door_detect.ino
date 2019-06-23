/*
  Web client

 This sketch connects to a website (http://www.google.com)
 using an Arduino Wiznet Ethernet shield.

 Circuit:
 * Ethernet shield attached to pins 10, 11, 12, 13

 created 18 Dec 2009
 by David A. Mellis
 1st modified 9 Apr 2012
 2se modified 18 July 2018 by muxmonster@Banmihospital.
 `Arduino Mega 2560 + Ehternet shield W5100`
 */

#include <SPI.h>
#include <Ethernet.h>
#include <DHT.h>

// Enter a MAC address for your controller below.
// Newer Ethernet shields have a MAC address printed on a sticker on the shield
byte mac[] = { 0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED };
// if you don't want to use DNS (and reduce your sketch size)
// use the numeric IP instead of the name for the server:


//IPAddress server(203,157,134,42);
IPAddress server(192,168,2,29); // <-- Config IP Webserver
// Set the static IP address to use if the DHCP fails to assign
IPAddress ip(xxx, xxx, xxx, xxx); //<--- Config IPAddress in Lan

IPAddress subnet(255, 255, 252, 0); //<--- Config netmask in Lan
IPAddress gateway(192, 168, 1, 1); //<--- Config Gateway in Lan is Options
//IPAddress gateway(1, 0, 0, 1);
IPAddress dnServer(8, 8, 4, 4);

// Initialize the Ethernet client library
// with the IP address and port of the server
// that you want to connect to (port 80 is default for HTTP):
EthernetClient client;
int ledPin_A = 2;
DHT dht_a;  // <-- Temperature and Humidity A
DHT dht_b;  // <-- Temperature and Humidity B
// Smoke Detector in analog pin
int smoke_A = A0;  // <-- Smoke detect A
int smoke_B = A1;  // <-- Smoke detect B

// Config Ultrasonic PIN NUMBER ( Detect human open the door by distance the door )
const int trigPin = 12;  // <-- Receive sound from echo
const int echoPin = 13;  // <-- Send sound output

// DEFINES VARIABLE ULTRASONIC.
long duration;
int distance_a;

void setup() {
  pinMode(ledPin_A, OUTPUT);
  // Open serial communications and wait for port to open:
  Serial.begin(9600);
  dht_a.setup(7);
  dht_b.setup(8);
  // Define Smoke Station A && B
  pinMode(smoke_A, INPUT);  //Smoke detect in SERVER ROOM
  pinMode(smoke_B, INPUT);  //Smoke detect in RACK

  // Assign Ultrasonic INPUT/OUTPUT
  pinMode(trigPin, OUTPUT);
  pinMode(echoPin, INPUT);
/////////////////////////////////////
  
  
  while (!Serial) {
    ; // wait for serial port to connect. Needed for native USB port only
  }

  // start the Ethernet connection:
  if (Ethernet.begin(mac) == 0) {
    Serial.println("Failed to configure Ethernet using DHCP");
    // try to congifure using IP address instead of DHCP:
    Ethernet.begin(mac, ip, dnServer, gateway, subnet);
  }
  // give the Ethernet shield a second to initialize:
  Serial.println(Ethernet.localIP());
  delay(1000);
  Serial.println("connecting...");
}
  // if you get a connection, report back via serial:
void loop() {
//Read value of smoke
int sm_sensor_a = analogRead(smoke_A);
int sm_sensor_b = analogRead(smoke_B);

// Clear TrigPin
digitalWrite(trigPin, LOW);
delayMicroseconds(2);
////////////////////////////////

// Start Trig
digitalWrite(trigPin, HIGH);
delayMicroseconds(10);
digitalWrite(trigPin, LOW);

duration = pulseIn(echoPin, HIGH);

distance_a = duration * 0.034/2;

Serial.print("Distance => ");
Serial.println(distance_a);

Serial.print("Smoke detec in room server value : ");
Serial.print(sm_sensor_a);
Serial.println(" ppt");

Serial.print("Smoke detec in RACK value : ");
Serial.print(sm_sensor_b);
Serial.println(" ppt");

  if (client.connect(server, 80)) {
    Serial.println("connected.");
    Serial.print("Insert temperature to ");
    
    delay(dht_a.getMinimumSamplingPeriod());
    delay(dht_b.getMinimumSamplingPeriod());

    float temperature_a = dht_a.getTemperature();
    float hum_a = dht_a.getHumidity();
    //char temp_station[]={"A"};
    String temp_station_a = "A";

    float temperature_b = dht_b.getTemperature();
    float hum_b = dht_b.getHumidity();
    //char temp_station_b[]={"B"};
    String temp_station_b = "B";
    
    Serial.print("\t");
    Serial.print(temperature_a,1);
    Serial.print("\t\t");
    Serial.println(temp_station_a);

    Serial.print("Insert temperature to ");
    Serial.print("\t");
    Serial.print(temperature_b,1);
    Serial.print("\t\t");
    Serial.println(temp_station_b);

    // Make a HTTP request:
    //client.println("GET /search?q=arduino HTTP/1.1");
    client.print("GET /program/cc/arduinopj/get_temper.php?temp_a=");  //<----- path webserver GET VALUE
    client.print(temperature_a);
    client.print("&hum_a=");
    client.print(hum_a);  
    client.print("&temp_s=");
    client.print(temp_station_a);
    client.print("&sm_a=");
    client.print(sm_sensor_a);    
    client.print("&nodeid=0");  //node id = 0
    
    client.print("&temp_b=");
    client.print(temperature_b);
    client.print("&hum_b=");
    client.print(hum_b);     
    client.print("&temp_s_b=");
    client.print(temp_station_b);
    client.print("&sm_b=");
    client.print(sm_sensor_b);
    client.print("&nodeid=0"); //node id = 0

    client.print("&distance_a=");
    client.print(distance_a);
    client.println();
    
    client.println("HTTP/1.1");
    client.println("Host: xxx.xxx.xxx.xxx"); //<----- ip web server
    client.println("Connection: close");
    client.println();
    
/// LED ON OR OFF
    /*
    if (temp_station_a == "A" && temperature_a > 27.0)
    {
      digitalWrite(ledPin_A, HIGH);
      delay(1000);
      digitalWrite(ledPin_A, LOW);      
    }
    */
  } else {
    // if you didn't get a connection to the server:
    Serial.println("connection failed");
  }
      Serial.println("End Session > ");
    //Serial.println(C_SESSION++);
    client.stop();
    //delay(60000);
    delay(1000);    //<----- 1 sec is Send data from every sensor connect on board arduino mega 2560 to webserver
}
