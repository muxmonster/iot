# Sensor detect environment.
## Step 00
```
mkdir ~/sensor
cd ~/sensor
git clone https://github.com/muxmonster/iot .
```
## Step 01 (Config and connect hardware)
- [x] burn temp_humidity_smoke_door_detect.ino from folder iot in to Arduino **But arduino should connected Ethernet Shield W5000**
- [x] connect wire between arduino and dht/ultrasonic/smoke detect
## Step 02 (Config webserver)
- [x] copy file get_temperature.php from folder php into Webserver
### This file `iot/temp_humidity_smoke_door_detect.ino` is detect
> Temperature, Humidity, Smoke and Distance
