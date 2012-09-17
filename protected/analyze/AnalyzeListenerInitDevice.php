<?php

/**
 * listener to init device
 * @author york
 */
class AnalyzeListenerInitDevice implements IAnalyzeListener{
	
	public function execute($analyze){
		$analyze->device = new Device();
		$analyze->device->udid = $analyze->json->device->udid;
		$analyze->device->channel = $analyze->json->device->channel;
		$analyze->device->channel_id = Channel::idFor($analyze->device->channel);
		$analyze->device->version = $analyze->json->device->app_version;
		$analyze->device->version_id = Version::idFor($analyze->device->version);
		$analyze->device->brand = $analyze->json->device->brand;
		$analyze->device->model = $analyze->json->device->model;
		$analyze->device->model_id = Model::idFor($analyze->device->brand, $analyze->device->model);
		$analyze->device->os_version = $analyze->json->device->os_version;
		$analyze->device->os_version_id = OsVersion::idFor($analyze->device->os_version);
		$analyze->device->resolution = $analyze->json->device->resolution;
		$analyze->device->resolution_id = Resolution::idFor($analyze->device->resolution);
		$analyze->device->carrier = $analyze->json->device->carrier;
		$analyze->device->carrier_id = Carrier::idFor($analyze->device->carrier);
		$analyze->device->network = $analyze->json->device->network;
		$analyze->device->network_id = Network::idFor($analyze->device->network);
		$analyze->device->save(date("Y-m-d H:i:s", $analyze->server_timestamp));
	}
}

