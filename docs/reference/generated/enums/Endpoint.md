# `Byd\ApiClient\Enum\Endpoint`

_No PHPDoc description provided._

**Type:** `enum`  
**Source:** `src/Enum/Endpoint.php`

## Cases

| Case | Backed value |
| --- | --- |
| `LOGIN` | `/app/account/login` |
| `VEHICLES` | `/app/account/getAllListByUserId` |
| `REALTIME_REQUEST` | `/vehicleInfo/vehicle/vehicleRealTimeRequest` |
| `REALTIME_RESULT` | `/vehicleInfo/vehicle/vehicleRealTimeResult` |
| `GPS_REQUEST` | `/control/getGpsInfo` |
| `GPS_RESULT` | `/control/getGpsInfoResult` |
| `HVAC_STATUS` | `/control/getStatusNow` |
| `CHARGING_HOME` | `/control/smartCharge/homePage` |
| `ENERGY` | `/vehicleInfo/vehicle/getEnergyConsumption` |
| `PUSH_GET` | `/vehicle/vehicleswitch/getPushSwitchState` |
| `PUSH_SET` | `/vehicle/vehicleswitch/setPushSwitchState` |
| `VERIFY_PIN` | `/vehicle/vehicleswitch/verifyControlPassword` |
| `REMOTE_CONTROL` | `/control/remoteControl` |
| `REMOTE_CONTROL_RESULT` | `/control/remoteControlResult` |
| `CHARGING_TOGGLE` | `/control/smartCharge/changeChargeStatue` |
| `CHARGING_SAVE` | `/control/smartCharge/saveOrUpdate` |
| `CHARGING_RESULT` | `/control/smartCharge/changeResult` |
| `VEHICLE_RENAME` | `/control/vehicle/modifyAutoAlias` |
| `OTA_VERSION` | `/control/otaUpgrade/getOtaVersion` |
| `OTA_BOOKING` | `/control/otaUpgrade/bookingUpgrade` |
| `OTA_CANCEL_BOOKING` | `/control/otaUpgrade/cancelUpgrade` |
| `OTA_UPGRADE` | `/control/otaUpgrade/upgradeOta` |
| `OTA_SYNC_AUTO_UPGRADE` | `/control/otaUpgrade/syncAutoUpgrade` |

## Public properties

| Property | Type | Default |
| --- | --- | --- |
| `name` | `string` | `—` |
| `value` | `string` | `—` |

