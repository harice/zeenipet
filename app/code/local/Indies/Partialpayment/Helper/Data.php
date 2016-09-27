<?php

class Indies_Partialpayment_Helper_Data extends Mage_Core_Helper_Abstract
{
	private $temp;
	public function getDomain ()
    {
        $domain = $_SERVER['SERVER_NAME'];
        $temp = explode('.', $domain);
        $exceptions = array(
            'co.uk',
            'com.au',
			'com.hk',
			'co.nz',
			'co.in',
			'com.sg'
            );

            $count = count($temp);
            $last = $temp[($count-2)] . '.' . $temp[($count-1)];

            if(in_array($last, $exceptions)) {
                $new_domain = $temp[($count-3)] . '.' . $temp[($count-2)] . '.' . $temp[($count-1)];
            }
            else {
                $new_domain = $temp[($count-2)] . '.' . $temp[($count-1)];
            }
            return $new_domain;
    }


    public function checkEntry ($domain, $serial)
    {
        $key = sha1(base64_decode('UGFydGlhbFBheW1lbnRz'));
        if(sha1($key.$domain) == $serial)
		{
            return true;
        }
        return false;
    }


    public function canRun ()
    {
        if($_SERVER['SERVER_NAME'] == "localhost" || $_SERVER['SERVER_NAME'] == "127.0.0.1") {
			return true;
		}

        $temp = Mage::getStoreConfig('partialpayment/license_status_group/serial_key', Mage::app()->getStore());

		$url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
		$parsedUrl = parse_url($url);
		$host = explode('.', $parsedUrl['host']);
		$subdomains = array_slice($host, 0, count($host) - 2 );

		if(sizeof($subdomains) && ($subdomains[0] == 'test' || $subdomains[0] == 'demo' || $subdomains[0] == 'dev' || $subdomains[0] == 'shop')){
			return true;
		}

		if($parsedUrl['path'] == '/test/' || $parsedUrl['path'] == '/demo/' || $parsedUrl['path'] == '/dev/'){
			return true;
		}

		$original = $this->checkEntry($_SERVER['SERVER_NAME'], $temp);
        $wildcard = $this->checkEntry($this->getDomain(), $temp);

        if(!$original && !$wildcard) {
            return false;
        }
        return true;
    }


	public function getMessage ()
	{
		return $this->__(base64_decode('PGRpdiBzdHlsZT0iYm9yZGVyOjNweCBzb2xpZCAjRkYwMDAwOyBtYXJnaW46MTVweCAwOyBwYWRkaW5nOjVweDsiPkxpY2Vuc2Ugb2YgPGEgaHJlZj0iaHR0cDovL3d3dy5pbmRpZXN3ZWJzLmNvbS9tYWdlbnRvLWRlcG9zaXQtcGF5bWVudC5odG1sIiB0YXJnZXQ9Il9ibGFuayI+SW5kaWVzIERlcG9zaXQgUGF5bWVudDwvYT4gbW9kdWxlIGhhcyBiZWVuIHZpb2xhdGVkLiBUbyBnZXQgbGljZW5zZSBzZXJpYWwga2V5IHBsZWFzZSBjbGljayA8YSBocmVmPSJodHRwOi8vd3d3LmluZGllc3dlYnMuY29tL2Rvd25sb2FkYWJsZS9jdXN0b21lci9wcm9kdWN0cy8iIHRhcmdldD0iX2JsYW5rIj5oZXJlPC9hPi48L2Rpdj4='));
	}
}
