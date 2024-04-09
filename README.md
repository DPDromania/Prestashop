## Changelog
Mantainer: Bogdan Ivanov <contact@jarbot.tech>

### 2022-03-15 - v2.19.1
- fix for notice warning when serviceId is not known yet
- make Reusable Return appear and disappear dynamically based on the selected shipping method before shipment creation

### 2022-03-14 - v2.19.0
- for DPD Standard add a new option to create shipment - Reusable Return - and change the serviceId in swap to 2007
- ability to set in dpd module configuration a default weight to be used in shipment and price calculation when the product has no weight set in admin

### 2022-02-07 - v2.18.0
- fixed bug where continue button was deactivated when coming back to a non DPD delivery option
- fixed bug where pickup delivery method took priority if dpd_office was set, even if it was a home delivery in price calculation and label creation
- update price calculation in frontend when changing from delivery office to home delivery and vice versa