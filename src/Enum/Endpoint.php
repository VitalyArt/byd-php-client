<?php

declare(strict_types=1);

namespace Byd\ApiClient\Enum;

enum Endpoint: string
{
    case LOGIN = '/app/account/login';
    case VEHICLES = '/app/account/getAllListByUserId';
    case REALTIME_REQUEST = '/vehicleInfo/vehicle/vehicleRealTimeRequest';
    case REALTIME_RESULT = '/vehicleInfo/vehicle/vehicleRealTimeResult';
    case GPS_REQUEST = '/control/getGpsInfo';
    case GPS_RESULT = '/control/getGpsInfoResult';
    case HVAC_STATUS = '/control/getStatusNow';
    case CHARGING_HOME = '/control/smartCharge/homePage';
    case ENERGY = '/vehicleInfo/vehicle/getEnergyConsumption';
    case PUSH_GET = '/vehicle/vehicleswitch/getPushSwitchState';
    case PUSH_SET = '/vehicle/vehicleswitch/setPushSwitchState';
    case VERIFY_PIN = '/vehicle/vehicleswitch/verifyControlPassword';
    case REMOTE_CONTROL = '/control/remoteControl';
    case REMOTE_CONTROL_RESULT = '/control/remoteControlResult';
    case CHARGING_TOGGLE = '/control/smartCharge/changeChargeStatue';
    case CHARGING_SAVE = '/control/smartCharge/saveOrUpdate';
    case CHARGING_RESULT = '/control/smartCharge/changeResult';
    case VEHICLE_RENAME = '/control/vehicle/modifyAutoAlias';
    case OTA_VERSION = '/control/otaUpgrade/getOtaVersion';
    case OTA_BOOKING = '/control/otaUpgrade/bookingUpgrade';
    case OTA_CANCEL_BOOKING = '/control/otaUpgrade/cancelUpgrade';
    case OTA_UPGRADE = '/control/otaUpgrade/upgradeOta';
    case OTA_SYNC_AUTO_UPGRADE = '/control/otaUpgrade/syncAutoUpgrade';
}
