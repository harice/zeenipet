<?php
/*
 * Author Rudyuk Vitalij Anatolievich
 * Email rvansp@gmail.com
 * Blog www.cervic.info
 */
?>
<?php

class Infomodus_Upslabel_Model_Ups
{

    protected $AccessLicenseNumber;
    protected $UserId;
    protected $Password;
    protected $shipperNumber;
    protected $credentials;


    public $packages;
    public $weightUnits;
    public $packageWeight;
    public $weightUnitsDescription;
    public $largePackageIndicator;

    public $includeDimensions;
    public $unitOfMeasurement;
    public $unitOfMeasurementDescription;
    public $length;
    public $width;
    public $height;

    public $customerContext;
    public $shipperPhoneNumber;
    public $shipperAddressLine1;
    public $shipperCity;
    public $shipperStateProvinceCode;
    public $shipperPostalCode;
    public $shipperCountryCode;
    public $shipmentDescription;
    public $shipperAttentionName;

    public $shiptoCompanyName;
    public $shiptoAttentionName;
    public $shiptoPhoneNumber;
    public $shiptoAddressLine1;
    public $shiptoAddressLine2;
    public $shiptoCity;
    public $shiptoStateProvinceCode;
    public $shiptoPostalCode;
    public $shiptoCountryCode;
    public $residentialAddress;

    public $shipfromCompanyName;
    public $shipfromAttentionName;
    public $shipfromPhoneNumber;
    public $shipfromAddressLine1;
    public $shipfromCity;
    public $shipfromStateProvinceCode;
    public $shipfromPostalCode;
    public $shipfromCountryCode;

    public $serviceCode;
    public $serviceDescription;
    public $shipmentDigest;
    public $packagingReferenceNumberCode;
    public $packagingReferenceNumberValue;

    public $trackingNumber;
    public $shipmentIdentificationNumber;
    public $graphicImage;
    public $htmlImage;

    public $codYesNo;
    public $currencyCode;
    public $codMonetaryValue;
    public $codFundsCode;
    public $invoicelinetotal;
    public $carbon_neutral;
    public $testing;

    public function setCredentials($access, $user, $pass, $shipper)
    {
        $this->AccessLicenseNumber = $access;
        $this->UserID = $user;
        $this->Password = $pass;
        $this->shipperNumber = $shipper;
        $this->credentials = 1;
        return $this->credentials;
    }

    function getShip()
    {
        if ($this->credentials != 1) {
            return array('error' => array('cod' => 1, 'message' => 'Not correct registration data'), 'success' => 0);
        }
        /* if(is_dir($filename)){} */
        $path_upsdir = Mage::getBaseDir('media') . DS . 'upslabel' . DS;
        if (!is_dir($path_upsdir)) {
            mkdir($path_upsdir, 0777);
            mkdir($path_upsdir . DS . "label" . DS, 0777);
            mkdir($path_upsdir . DS . "test_xml" . DS, 0777);
        }
        $path = Mage::getBaseDir('media') . DS . 'upslabel' . DS . "label" . DS;
        $path_xml = Mage::getBaseDir('media') . DS . 'upslabel' . DS . "test_xml" . DS;
        $this->customerContext = str_replace('&', '&amp;', strtolower(Mage::app()->getStore()->getName()));
        $data = "<?xml version=\"1.0\" ?>
<AccessRequest xml:lang='en-US'>
<AccessLicenseNumber>" . $this->AccessLicenseNumber . "</AccessLicenseNumber>
<UserId>" . $this->UserID . "</UserId>
<Password>" . $this->Password . "</Password>
</AccessRequest>
<?xml version=\"1.0\"?>
<ShipmentConfirmRequest xml:lang=\"en-US\">
  <Request>
    <TransactionReference>
      <CustomerContext>" . $this->customerContext . "</CustomerContext>
      <XpciVersion/>
    </TransactionReference>
    <RequestAction>ShipConfirm</RequestAction>
    <RequestOption>validate</RequestOption>
  </Request>
  <LabelSpecification>
    <LabelPrintMethod>
      <Code>GIF</Code>
      <Description>gif file</Description>
    </LabelPrintMethod>
    <HTTPUserAgent>Mozilla/4.5</HTTPUserAgent>
    <LabelImageFormat>
      <Code>GIF</Code>
      <Description>gif</Description>
    </LabelImageFormat>
  </LabelSpecification>
  <Shipment>";
        if (Mage::getStoreConfig('upslabel/profile/negotiatedratesindicator') == 1) {
            $data .= "
   <RateInformation>
      <NegotiatedRatesIndicator/>
    </RateInformation>";
        }
        if (strlen($this->shipmentDescription) > 0) {
            $data .= "<Description>" . $this->shipmentDescription . "</Description>";
        }
        $data .= "<Shipper>
<Name>" . $this->shipperName . "</Name>";
        $data .= "<AttentionName>" . $this->shipperAttentionName . "</AttentionName>";

        $data .= "<PhoneNumber>" . $this->shipperPhoneNumber . "</PhoneNumber>
      <ShipperNumber>" . $this->shipperNumber . "</ShipperNumber>
	  <TaxIdentificationNumber></TaxIdentificationNumber>
      <Address>
    	<AddressLine1>" . $this->shipperAddressLine1 . "</AddressLine1>
    	<City>" . $this->shipperCity . "</City>
    	<StateProvinceCode>" . $this->shipperStateProvinceCode . "</StateProvinceCode>
    	<PostalCode>" . $this->shipperPostalCode . "</PostalCode>
    	<PostcodeExtendedLow></PostcodeExtendedLow>
    	<CountryCode>" . $this->shipperCountryCode . "</CountryCode>
     </Address>
    </Shipper>
	<ShipTo>
     <CompanyName>" . $this->shiptoCompanyName . "</CompanyName>
      <AttentionName>" . $this->shiptoAttentionName . "</AttentionName>
      <PhoneNumber>" . $this->shiptoPhoneNumber . "</PhoneNumber>
      <Address>
        <AddressLine1>" . $this->shiptoAddressLine1 . "</AddressLine1>";
        if (strlen($this->shiptoAddressLine2) > 0) {
            $data .= '<AddressLine2>' . $this->shiptoAddressLine2 . '</AddressLine2>';
        }
        $data .= "<City>" . $this->shiptoCity . "</City>
        <StateProvinceCode>" . $this->shiptoStateProvinceCode . "</StateProvinceCode>
        <PostalCode>" . $this->shiptoPostalCode . "</PostalCode>
        <CountryCode>" . $this->shiptoCountryCode . "</CountryCode>
        " . $this->residentialAddress . "
      </Address>
    </ShipTo>
    <ShipFrom>
      <CompanyName>" . $this->shipfromCompanyName . "</CompanyName>
      <AttentionName>" . $this->shipfromAttentionName . "</AttentionName>
      <PhoneNumber>" . $this->shipfromPhoneNumber . "</PhoneNumber>
	  <TaxIdentificationNumber></TaxIdentificationNumber>
      <Address>
        <AddressLine1>" . $this->shipfromAddressLine1 . "</AddressLine1>
        <City>" . $this->shipfromCity . "</City>
    	<StateProvinceCode>" . $this->shipfromStateProvinceCode . "</StateProvinceCode>
    	<PostalCode>" . $this->shipfromPostalCode . "</PostalCode>
    	<CountryCode>" . $this->shipfromCountryCode . "</CountryCode>
      </Address>
    </ShipFrom>
     <PaymentInformation>
      <Prepaid>
        <BillShipper>
          <AccountNumber>" . $this->shipperNumber . "</AccountNumber>
        </BillShipper>
      </Prepaid>
    </PaymentInformation>
    <Service>
      <Code>" . $this->serviceCode . "</Code>
      <Description>" . $this->serviceDescription . "</Description>
    </Service>";
        if ($this->shiptoCountryCode != $this->shipfromCountryCode) {
            $data .= "<ReferenceNumber>
    	  	<Code>" . $this->packagingReferenceNumberCode . "</Code>
    		<Value>" . $this->packagingReferenceNumberValue . "</Value>
    	  </ReferenceNumber>";
        }
        foreach ($this->packages AS $pv) {
            $data .= "<Package>
      <PackagingType>
        <Code>" . $pv["packagingtypecode"] . "</Code>
      </PackagingType>
      <Description>" . $pv["packagingdescription"] . "</Description>";
            if (($this->shiptoCountryCode == 'US' || $this->shiptoCountryCode == 'PR') && $this->shiptoCountryCode == $this->shipfromCountryCode) {
                $data .= "<ReferenceNumber>
	  	<Code>" . $pv['packagingreferencenumbercode'] . "</Code>
		<Value>" . $pv['packagingreferencenumbervalue'] . "</Value>
	  </ReferenceNumber>";
            }
            $data .= array_key_exists('additionalhandling', $pv) ? $pv['additionalhandling'] : '';
            if ($this->includeDimensions == 1) {
                $data .= "<Dimensions>
<UnitOfMeasurement>
<Code>" . $this->unitOfMeasurement . "</Code>";
                if (strlen($this->unitOfMeasurementDescription) > 0) {
                    $data .= "
<Description>" . $this->unitOfMeasurementDescription . "</<Description>";
                }
                $data .= "</UnitOfMeasurement>
<Length>" . $pv['length'] . "</Length>
<Width>" . $pv['width'] . "</Width>
<Height>" . $pv['height'] . "</Height>
            </Dimensions>";
            }
            $data .= "<PackageWeight>
        <UnitOfMeasurement>
            <Code>" . $this->weightUnits . "</Code>";
            if (strlen($this->weightUnitsDescription) > 0) {
                $data .= "
            <Description>" . $this->weightUnitsDescription . "</<Description>";
            }
            $packweight = array_key_exists('packweight', $pv) ? $pv['packweight'] : '';
            $weight = array_key_exists('weight', $pv) ? $pv['weight'] : '';
            $data .= "</UnitOfMeasurement>
        <Weight>" . round(($weight + (is_numeric(str_replace(',', '.', $packweight)) ? $packweight : 0)), 1) . "</Weight>" . (array_key_exists('large', $pv) ? $pv['large'] : '') . "
      </PackageWeight>
      <PackageServiceOptions>";
            if ($pv['insuredmonetaryvalue'] > 0) {
                $data .= "<InsuredValue>
                <CurrencyCode>" . $pv['currencycode'] . "</CurrencyCode>
                <MonetaryValue>" . $pv['insuredmonetaryvalue'] . "</MonetaryValue>
                </InsuredValue>
              ";
            }
            if ($pv['cod'] == 1 && ($this->shiptoCountryCode == 'US' || $this->shiptoCountryCode == 'PR' || $this->shiptoCountryCode == 'CA') && ($this->shipfromCountryCode == 'US' || $this->shipfromCountryCode == 'PR' || $this->shipfromCountryCode == 'CA')) {
                $data .= "
              <COD>
                  <CODCode>3</CODCode>
                  <CODFundsCode>" . $pv['codfundscode'] . "</CODFundsCode>
                  <CODAmount>
                      <CurrencyCod>" . $pv['currencycode'] . "</CurrencyCod>
                      <MonetaryValue>" . $pv['codmonetaryvalue'] . "</MonetaryValue>
                  </CODAmount>
              </COD>";
            }
            $data .= "</PackageServiceOptions>
              </Package>";
        }
        $data .= "<ShipmentServiceOptions>";
        if ($this->codYesNo == 1 && $this->shiptoCountryCode != 'US' && $this->shiptoCountryCode != 'PR' && $this->shiptoCountryCode != 'CA' && $this->shipfromCountryCode != 'US' && $this->shipfromCountryCode != 'PR' && $this->shipfromCountryCode != 'CA') {
            $data .= "<COD>
                  <CODCode>3</CODCode>
                  <CODFundsCode>" . $this->codFundsCode . "</CODFundsCode>
                  <CODAmount>
                      <CurrencyCode>" . $this->currencyCode . "</CurrencyCode>
                      <MonetaryValue>" . $this->codMonetaryValue . "</MonetaryValue>
                  </CODAmount>
              </COD>";

        }
        if ($this->carbon_neutral == 1) {
            $data .= "<UPScarbonneutralIndicator/>";
        }
        $data .= "</ShipmentServiceOptions>";
        if (strlen($this->invoicelinetotal) > 0 && ($this->shiptoCountryCode == 'US' || $this->shiptoCountryCode == 'PR' || $this->shiptoCountryCode == 'CA') && ($this->shipfromCountryCode == 'US' || $this->shipfromCountryCode == 'PR' || $this->shipfromCountryCode == 'CA') && $this->shiptoCountryCode != $this->shipfromCountryCode) {
            $data .= "<InvoiceLineTotal>
                          <CurrencyCode>" . $this->currencyCode . "</CurrencyCode>
                          <MonetaryValue>" . $this->invoicelinetotal . "</MonetaryValue>
              </InvoiceLineTotal>";
        }
        $data .= "</Shipment>
</ShipmentConfirmRequest>
";
        $file = file_put_contents($path_xml . "ShipConfirmRequest.xml", $data);
        $cie = 'wwwcie';
        if (0 == $this->testing) {
            $cie = 'onlinetools';
        }
        $ch = curl_init('https://' . $cie . '.ups.com/ups.app/xml/ShipConfirm');
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        $result = strstr($result, '<?xml');

        if ($result) {
            $file = file_put_contents($path_xml . "ShipConfirmResponse.xml", $result);
        }
        //return $result;
        $xml = simplexml_load_string($result);
        if ($xml->Response->ResponseStatusCode[0] == 1) {
            $this->shipmentDigest = $xml->ShipmentDigest[0];
            $data = "<?xml version=\"1.0\" ?>
<AccessRequest xml:lang='en-US'>
<AccessLicenseNumber>" . $this->AccessLicenseNumber . "</AccessLicenseNumber>
<UserId>" . $this->UserID . "</UserId>
<Password>" . $this->Password . "</Password>
</AccessRequest>
<?xml version=\"1.0\" ?>
<ShipmentAcceptRequest>
<Request>
<TransactionReference>
<CustomerContext>" . $this->customerContext . "</CustomerContext>
<XpciVersion>1.0001</XpciVersion>
</TransactionReference>
<RequestAction>ShipAccept</RequestAction>
</Request>
<ShipmentDigest>" . $this->shipmentDigest . "</ShipmentDigest>
</ShipmentAcceptRequest>";
            $file = file_put_contents($path_xml . "ShipAcceptRequest.xml", $data);

            $ch = curl_init('https://' . $cie . '.ups.com/ups.app/xml/ShipAccept');
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($ch);
            $result = strstr($result, '<?xml');
            if ($result) {
                $file = file_put_contents($path_xml . "ShipAcceptResponse.xml", $result);
            }
            $xml = simplexml_load_string($result);
            $this->shipmentIdentificationNumber = $xml->ShipmentResults[0]->ShipmentIdentificationNumber[0];
            $arrResponsXML = array();
            $i = 0;
            foreach ($xml->ShipmentResults[0]->PackageResults AS $resultXML) {
                $arrResponsXML[$i]['trackingnumber'] = $resultXML->TrackingNumber[0];
                $arrResponsXML[$i]['graphicImage'] = base64_decode($resultXML->LabelImage[0]->GraphicImage[0]);
                $file = fopen($path . 'label' . $arrResponsXML[$i]['trackingnumber'] . '.gif', 'w');
                fwrite($file, $arrResponsXML[$i]['graphicImage']);
                fclose($file);
                $arrResponsXML[$i]['htmlImage'] = base64_decode($resultXML->LabelImage[0]->HTMLImage[0]);
                $file = file_put_contents($path . $arrResponsXML[$i]['trackingnumber'] . ".html", $arrResponsXML[$i]['htmlImage']);
                $file = file_put_contents($path_xml . "HTML_image.html", $arrResponsXML[$i]['htmlImage']);
                $i += 1;
            }
            if ($this->codMonetaryValue > 999) {
                $htmlHVReport = '<html xmlns:o="urn:schemas-microsoft-com:office:office"
xmlns:w="urn:schemas-microsoft-com:office:word"
xmlns="http://www.w3.org/TR/REC-html40">

<head>
<meta http-equiv=Content-Type content="text/html; charset=windows-1252">
<meta name=ProgId content=Word.Document>
<meta name=Generator content="Microsoft Word 11">
<meta name=Originator content="Microsoft Word 11">
<link rel=File-List href="sample%20UPS%20CONTROL%20LOG_files/filelist.xml">
<title>UPS CONTROL LOG </title>
<!--[if gte mso 9]><xml>
 <o:DocumentProperties>
  <o:Author>xlm8zff</o:Author>
  <o:LastAuthor>xlm8zff</o:LastAuthor>
  <o:Revision>2</o:Revision>
  <o:TotalTime>2</o:TotalTime>
  <o:Created>2010-09-27T12:53:00Z</o:Created>
  <o:LastSaved>2010-09-27T12:53:00Z</o:LastSaved>
  <o:Pages>1</o:Pages>
  <o:Words>116</o:Words>
  <o:Characters>662</o:Characters>
  <o:Company>UPS</o:Company>
  <o:Lines>5</o:Lines>
  <o:Paragraphs>1</o:Paragraphs>
  <o:CharactersWithSpaces>777</o:CharactersWithSpaces>
  <o:Version>11.9999</o:Version>
 </o:DocumentProperties>
</xml><![endif]--><!--[if gte mso 9]><xml>
 <w:WordDocument>
  <w:SpellingState>Clean</w:SpellingState>
  <w:GrammarState>Clean</w:GrammarState>
  <w:PunctuationKerning/>
  <w:ValidateAgainstSchemas/>
  <w:SaveIfXMLInvalid>false</w:SaveIfXMLInvalid>
  <w:IgnoreMixedContent>false</w:IgnoreMixedContent>
  <w:AlwaysShowPlaceholderText>false</w:AlwaysShowPlaceholderText>
  <w:Compatibility>
   <w:BreakWrappedTables/>
   <w:SnapToGridInCell/>
   <w:WrapTextWithPunct/>
   <w:UseAsianBreakRules/>
   <w:DontGrowAutofit/>
  </w:Compatibility>
  <w:BrowserLevel>MicrosoftInternetExplorer4</w:BrowserLevel>
 </w:WordDocument>
</xml><![endif]--><!--[if gte mso 9]><xml>
 <w:LatentStyles DefLockedState="false" LatentStyleCount="156">
 </w:LatentStyles>
</xml><![endif]-->
<style>
<!--
 /* Style Definitions */
 p.MsoNormal, li.MsoNormal, div.MsoNormal
	{mso-style-parent:"";
	margin:0in;
	margin-bottom:.0001pt;
	mso-pagination:widow-orphan;
	font-size:10.0pt;
	mso-bidi-font-size:12.0pt;
	font-family:Arial;
	mso-fareast-font-family:"Times New Roman";}
span.GramE
	{mso-style-name:"";
	mso-gram-e:yes;}
@page Section1
	{size:8.5in 11.0in;
	margin:1.0in 1.25in 1.0in 1.25in;
	mso-header-margin:.5in;
	mso-footer-margin:.5in;
	mso-paper-source:0;}
div.Section1
	{page:Section1;}
-->
</style>
<!--[if gte mso 10]>
<style>
 /* Style Definitions */
 table.MsoNormalTable
	{mso-style-name:"Table Normal";
	mso-tstyle-rowband-size:0;
	mso-tstyle-colband-size:0;
	mso-style-noshow:yes;
	mso-style-parent:"";
	mso-padding-alt:0in 5.4pt 0in 5.4pt;
	mso-para-margin:0in;
	mso-para-margin-bottom:.0001pt;
	mso-pagination:widow-orphan;
	font-size:10.0pt;
	font-family:"Times New Roman";
	mso-ansi-language:#0400;
	mso-fareast-language:#0400;
	mso-bidi-language:#0400;}
</style>
<![endif]-->
</head>
<body lang=EN-US style=\'tab-interval:.5in\'>

<div class=Section1>

<p class=MsoNormal>UPS CONTROL <span class=GramE>LOG</span></p>

<p class=MsoNormal>DATE: ' . date('d') . ' ' . date('M') . ' ' . date('Y') . ' UPS SHIPPER NO. ' . $this->shipperNumber . ' </p>
<br />
<br />
<p class=MsoNormal>TRACKING # PACKAGE ID REFRENCE NUMBER DECLARED VALUE
CURRENCY </p>
<p class=MsoNormal>--------------------------------------------------------------------------------------------------------------------------
</p>
<br /><br />
<p class=MsoNormal>' . $this->trackingNumber . ' <span class=GramE>' . $this->packagingReferenceNumberValue . ' ' . round($this->codMonetaryValue, 2) . '</span> ' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol() . ' </p>
<br /><br />
<p class=MsoNormal>Total Number of Declared Value Packages = 1 </p>
<p class=MsoNormal>--------------------------------------------------------------------------------------------------------------------------
</p>
<br /><br />
<p class=MsoNormal>RECEIVED BY_________________________PICKUP
TIME__________________PKGS_______ </p>
</div>
</body>
</html>';
                file_put_contents($path . "HVR" . $this->shipmentIdentificationNumber . ".html", $htmlHVReport);
            }
            return array(
                'arrResponsXML' => $arrResponsXML,
                'digest' => '' . $this->shipmentDigest . '',
                'shipidnumber' => '' . $this->shipmentIdentificationNumber . '',
            );
        } else {
            $error = '<h1>Error</h1> <ul>';
            $errorss = $xml->Response->Error[0];
            $error .= '<li>Error Severity : ' . $errorss->ErrorSeverity . '</li>';
            $error .= '<li>Error Code : ' . $errorss->ErrorCode . '</li>';
            $error .= '<li>Error Description : ' . $errorss->ErrorDescription . '</li>';
            $error .= '</ul>';
            $error .= '<textarea>' . $result . '</textarea>';
            $error .= '<textarea>' . $data . '</textarea>';
            return array('error' => $error);
            //return print_r($xml->Response->Error);
        }
    }

    function getShipFrom()
    {
        if ($this->credentials != 1) {
            return array('error' => array('cod' => 1, 'message' => 'Not correct registration data'), 'success' => 0);
        }
        /* if(is_dir($filename)){} */
        $path_upsdir = Mage::getBaseDir('media') . DS . 'upslabel' . DS;
        if (!is_dir($path_upsdir)) {
            mkdir($path_upsdir, 0777);
            mkdir($path_upsdir . DS . "label" . DS, 0777);
            mkdir($path_upsdir . DS . "test_xml" . DS, 0777);
        }
        $path = Mage::getBaseDir('media') . DS . 'upslabel' . DS . "label" . DS;
        $path_xml = Mage::getBaseDir('media') . DS . 'upslabel' . DS . "test_xml" . DS;
        $this->customerContext = str_replace('&', '&amp;', strtolower(Mage::app()->getStore()->getName()));
        $data = "<?xml version=\"1.0\" ?>
        <AccessRequest xml:lang='en-US'>
        <AccessLicenseNumber>" . $this->AccessLicenseNumber . "</AccessLicenseNumber>
        <UserId>" . $this->UserID . "</UserId>
        <Password>" . $this->Password . "</Password>
        </AccessRequest>
        <?xml version=\"1.0\"?>
        <ShipmentConfirmRequest xml:lang=\"en-US\">
          <Request>
            <TransactionReference>
              <CustomerContext>" . $this->customerContext . "</CustomerContext>
              <XpciVersion/>
            </TransactionReference>
            <RequestAction>ShipConfirm</RequestAction>
            <RequestOption>validate</RequestOption>
          </Request>
          <LabelSpecification>
            <LabelPrintMethod>
              <Code>GIF</Code>
              <Description>gif file</Description>
            </LabelPrintMethod>
            <HTTPUserAgent>Mozilla/4.5</HTTPUserAgent>
            <LabelImageFormat>
              <Code>GIF</Code>
              <Description>gif</Description>
            </LabelImageFormat>
          </LabelSpecification>
          <Shipment>";
        if (Mage::getStoreConfig('upslabel/profile/negotiatedratesindicator') == 1) {
            $data .= "<RateInformation>
      <NegotiatedRatesIndicator/>
    </RateInformation>";
        }
        $data .= "<ShipmentServiceOptions>
                    <LabelDelivery>
                        <!--<EMailMessage>
                            <EMailAddress>" . $this->shiptoCustomerEmail . "</EMailAddress>
                        </EMailMessage>-->
                        <LabelLinksIndicator />
                    </LabelDelivery>";
        if ($this->carbon_neutral == 1) {
            $data .= "<UPScarbonneutralIndicator/>";
        }
        $data .= "</ShipmentServiceOptions>";
        $data .= "<ReturnService><Code>8</Code></ReturnService>";
        if (strlen($this->shipmentDescription) > 0) {
            $data .= "<Description>" . $this->shipmentDescription . "</Description>";
        }
        $data .= "<Shipper>
        <Name>" . $this->shipperName . "</Name>";
        $data .= "<AttentionName>" . $this->shipperAttentionName . "</AttentionName>";

        $data .= "<PhoneNumber>" . $this->shipperPhoneNumber . "</PhoneNumber>
              <ShipperNumber>" . $this->shipperNumber . "</ShipperNumber>
        	  <TaxIdentificationNumber></TaxIdentificationNumber>
              <Address>
            	<AddressLine1>" . $this->shipperAddressLine1 . "</AddressLine1>
            	<City>" . $this->shipperCity . "</City>
            	<StateProvinceCode>" . $this->shipperStateProvinceCode . "</StateProvinceCode>
            	<PostalCode>" . $this->shipperPostalCode . "</PostalCode>
            	<PostcodeExtendedLow></PostcodeExtendedLow>
            	<CountryCode>" . $this->shipperCountryCode . "</CountryCode>
             </Address>
            </Shipper>
        	<ShipFrom>
             <CompanyName>" . $this->shiptoCompanyName . "</CompanyName>
              <AttentionName>" . $this->shiptoAttentionName . "</AttentionName>
              <PhoneNumber>" . $this->shiptoPhoneNumber . "</PhoneNumber>
              <TaxIdentificationNumber></TaxIdentificationNumber>
              <Address>
                <AddressLine1>" . $this->shiptoAddressLine1 . "</AddressLine1>
                <City>" . $this->shiptoCity . "</City>
                <StateProvinceCode>" . $this->shiptoStateProvinceCode . "</StateProvinceCode>
                <PostalCode>" . $this->shiptoPostalCode . "</PostalCode>
                <CountryCode>" . $this->shiptoCountryCode . "</CountryCode>
              </Address>
            </ShipFrom>
            <ShipTo>
              <CompanyName>" . $this->shipfromCompanyName . "</CompanyName>
              <AttentionName>" . $this->shipfromAttentionName . "</AttentionName>
              <PhoneNumber>" . $this->shipfromPhoneNumber . "</PhoneNumber>
              <Address>
                <AddressLine1>" . $this->shipfromAddressLine1 . "</AddressLine1>";
        if (strlen($this->shiptoAddressLine2) > 0) {
            $data .= '<AddressLine2>' . $this->shiptoAddressLine2 . '</AddressLine2>';
        }
        $data .= "
                <City>" . $this->shipfromCity . "</City>
            	<StateProvinceCode>" . $this->shipfromStateProvinceCode . "</StateProvinceCode>
            	<PostalCode>" . $this->shipfromPostalCode . "</PostalCode>
            	<CountryCode>" . $this->shipfromCountryCode . "</CountryCode>
              </Address>
            </ShipTo>
             <PaymentInformation>
              <Prepaid>
                <BillShipper>
                  <AccountNumber>" . $this->shipperNumber . "</AccountNumber>
                </BillShipper>
              </Prepaid>
            </PaymentInformation>
            <Service>
              <Code>" . $this->serviceCode . "</Code>
              <Description>" . $this->serviceDescription . "</Description>
            </Service>";
        if ($this->shiptoCountryCode != $this->shipfromCountryCode) {
            $data .= "<ReferenceNumber>
                	  	<Code>" . $this->packagingReferenceNumberCode . "</Code>
                		<Value>" . $this->packagingReferenceNumberValue . "</Value>
                	  </ReferenceNumber>";
        }
        foreach ($this->packages AS $pv) {
            $data .= "<Package>
      <PackagingType>
        <Code>" . $pv["packagingtypecode"] . "</Code>
      </PackagingType>
      <Description>" . $pv["packagingdescription"] . "</Description>";
            if (($this->shiptoCountryCode == 'US' || $this->shiptoCountryCode == 'PR') && $this->shiptoCountryCode == $this->shipfromCountryCode) {
                $data .= "<ReferenceNumber>
	  	<Code>" . $pv['packagingreferencenumbercode'] . "</Code>
		<Value>" . $pv['packagingreferencenumbervalue'] . "</Value>
	  </ReferenceNumber>";
            }
            $data .= array_key_exists('additionalhandling', $pv) ? $pv['additionalhandling'] : '';
            if ($this->includeDimensions == 1) {
                $data .= "<Dimensions>
<UnitOfMeasurement>
<Code>" . $this->unitOfMeasurement . "</Code>";
                if (strlen($this->unitOfMeasurementDescription) > 0) {
                    $data .= "
<Description>" . $this->unitOfMeasurementDescription . "</<Description>";
                }
                $data .= "</UnitOfMeasurement>
<Length>" . $pv['length'] . "</Length>
<Width>" . $pv['width'] . "</Width>
<Height>" . $pv['height'] . "</Height>
            </Dimensions>";
            }
            $data .= "<PackageWeight>
        <UnitOfMeasurement>
            <Code>" . $this->weightUnits . "</Code>";
            if (strlen($this->weightUnitsDescription) > 0) {
                $data .= "
            <Description>" . $this->weightUnitsDescription . "</<Description>";
            }
            $packweight = array_key_exists('packweight', $pv) ? $pv['packweight'] : '';
            $weight = array_key_exists('weight', $pv) ? $pv['weight'] : '';
            $data .= "</UnitOfMeasurement>
        <Weight>" . round(($weight + (is_numeric(str_replace(',', '.', $packweight)) ? $packweight : 0)), 1) . "</Weight>" . (array_key_exists('large', $pv) ? $pv['large'] : '') . "
      </PackageWeight>
      <PackageServiceOptions>";
            if ($pv['insuredmonetaryvalue'] > 0) {
                $data .= "<InsuredValue>
                <CurrencyCode>" . $pv['currencycode'] . "</CurrencyCode>
                <MonetaryValue>" . $pv['insuredmonetaryvalue'] . "</MonetaryValue>
                </InsuredValue>
              ";
            }
            $data .= "</PackageServiceOptions>
              </Package>";
        }
        $data .= "
          </Shipment>
        </ShipmentConfirmRequest>
        ";
        $file = file_put_contents($path_xml . "ShipConfirmRequest.xml", $data);
        $cie = 'wwwcie';
        if (0 == $this->testing) {
            $cie = 'onlinetools';
        }
        $ch = curl_init('https://' . $cie . '.ups.com/ups.app/xml/ShipConfirm');
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        $result = strstr($result, '<?xml');

        if ($result) {
            $file = file_put_contents($path_xml . "ShipConfirmResponse.xml", $result);
        }
        //return $result;
        $xml = simplexml_load_string($result);
        if ($xml->Response->ResponseStatusCode[0] == 1) {
            $this->shipmentDigest = $xml->ShipmentDigest[0];
            $data = "<?xml version=\"1.0\" ?>
        <AccessRequest xml:lang='en-US'>
        <AccessLicenseNumber>" . $this->AccessLicenseNumber . "</AccessLicenseNumber>
        <UserId>" . $this->UserID . "</UserId>
        <Password>" . $this->Password . "</Password>
        </AccessRequest>
        <?xml version=\"1.0\" ?>
        <ShipmentAcceptRequest>
        <Request>
        <TransactionReference>
        <CustomerContext>" . $this->customerContext . "</CustomerContext>
        <XpciVersion>1.0001</XpciVersion>
        </TransactionReference>
        <RequestAction>ShipAccept</RequestAction>
        </Request>
        <ShipmentDigest>" . $this->shipmentDigest . "</ShipmentDigest>
        </ShipmentAcceptRequest>";
            $file = file_put_contents($path_xml . "ShipAcceptRequest.xml", $data);

            $ch = curl_init('https://' . $cie . '.ups.com/ups.app/xml/ShipAccept');
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            $result = curl_exec($ch);
            $result = strstr($result, '<?xml');
            if ($result) {
                $file = file_put_contents($path_xml . "ShipAcceptResponse.xml", $result);
            }
            if (0 == $this->testing) {
                $cie = 'www';
            }
            $xml = simplexml_load_string($result);
            curl_close($ch);
            $this->shipmentIdentificationNumber = $xml->ShipmentResults[0]->ShipmentIdentificationNumber[0];
            $i = 0;
            foreach ($xml->ShipmentResults[0]->PackageResults AS $resultXML) {
                $arrResponsXML[$i]['trackingnumber'] = $resultXML->TrackingNumber[0];
                $htmlUrlUPS = 'https://' . $cie . '.ups.com';
                $ch = curl_init($xml->ShipmentResults[$i]->LabelURL[0]);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $c = curl_exec($ch);
                curl_close($ch);
                $imgName = preg_replace('/.*?<img\s*?src="(.+?)".*/is', '$1', $c);
                $c = preg_replace('/<img\s*?src="/is', '<img src="' . $htmlUrlUPS, $c);
                $this->htmlImage = $c; /*base64_decode($xml->ShipmentResults[0]->PackageResults[0]->LabelImage[0]->HTMLImage[0]);*/
                $file = file_put_contents($path . $arrResponsXML[$i]['trackingnumber'] . ".html", $this->htmlImage);
                $file = file_put_contents($path_xml . "HTML_image.html", $this->htmlImage);

                $c = '';
                $ch = curl_init("https://" . $cie . ".ups.com" . $imgName);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $c = curl_exec($ch);
                curl_close($ch);
                /*$this->graphicImage = file_get_contents("https://".$cie.".ups.com/u.a/L.class?7IMAGE=".$this->trackingNumber."");*/
                //echo $this->graphicImage;
                $file = fopen($path . 'label' . $arrResponsXML[$i]['trackingnumber'] . '.gif', 'w');
                fwrite($file, $c);
                fclose($file);
                $i += 1;
            }

            if ($this->codMonetaryValue > 999) {
                $htmlHVReport = '<html xmlns:o="urn:schemas-microsoft-com:office:office"
        xmlns:w="urn:schemas-microsoft-com:office:word"
        xmlns="http://www.w3.org/TR/REC-html40">

        <head>
        <meta http-equiv=Content-Type content="text/html; charset=windows-1252">
        <meta name=ProgId content=Word.Document>
        <meta name=Generator content="Microsoft Word 11">
        <meta name=Originator content="Microsoft Word 11">
        <link rel=File-List href="sample%20UPS%20CONTROL%20LOG_files/filelist.xml">
        <title>UPS CONTROL LOG </title>
        <!--[if gte mso 9]><xml>
         <o:DocumentProperties>
          <o:Author>xlm8zff</o:Author>
          <o:LastAuthor>xlm8zff</o:LastAuthor>
          <o:Revision>2</o:Revision>
          <o:TotalTime>2</o:TotalTime>
          <o:Created>2010-09-27T12:53:00Z</o:Created>
          <o:LastSaved>2010-09-27T12:53:00Z</o:LastSaved>
          <o:Pages>1</o:Pages>
          <o:Words>116</o:Words>
          <o:Characters>662</o:Characters>
          <o:Company>UPS</o:Company>
          <o:Lines>5</o:Lines>
          <o:Paragraphs>1</o:Paragraphs>
          <o:CharactersWithSpaces>777</o:CharactersWithSpaces>
          <o:Version>11.9999</o:Version>
         </o:DocumentProperties>
        </xml><![endif]--><!--[if gte mso 9]><xml>
         <w:WordDocument>
          <w:SpellingState>Clean</w:SpellingState>
          <w:GrammarState>Clean</w:GrammarState>
          <w:PunctuationKerning/>
          <w:ValidateAgainstSchemas/>
          <w:SaveIfXMLInvalid>false</w:SaveIfXMLInvalid>
          <w:IgnoreMixedContent>false</w:IgnoreMixedContent>
          <w:AlwaysShowPlaceholderText>false</w:AlwaysShowPlaceholderText>
          <w:Compatibility>
           <w:BreakWrappedTables/>
           <w:SnapToGridInCell/>
           <w:WrapTextWithPunct/>
           <w:UseAsianBreakRules/>
           <w:DontGrowAutofit/>
          </w:Compatibility>
          <w:BrowserLevel>MicrosoftInternetExplorer4</w:BrowserLevel>
         </w:WordDocument>
        </xml><![endif]--><!--[if gte mso 9]><xml>
         <w:LatentStyles DefLockedState="false" LatentStyleCount="156">
         </w:LatentStyles>
        </xml><![endif]-->
        <style>
        <!--
         /* Style Definitions */
         p.MsoNormal, li.MsoNormal, div.MsoNormal
        	{mso-style-parent:"";
        	margin:0in;
        	margin-bottom:.0001pt;
        	mso-pagination:widow-orphan;
        	font-size:10.0pt;
        	mso-bidi-font-size:12.0pt;
        	font-family:Arial;
        	mso-fareast-font-family:"Times New Roman";}
        span.GramE
        	{mso-style-name:"";
        	mso-gram-e:yes;}
        @page Section1
        	{size:8.5in 11.0in;
        	margin:1.0in 1.25in 1.0in 1.25in;
        	mso-header-margin:.5in;
        	mso-footer-margin:.5in;
        	mso-paper-source:0;}
        div.Section1
        	{page:Section1;}
        -->
        </style>
        <!--[if gte mso 10]>
        <style>
         /* Style Definitions */
         table.MsoNormalTable
        	{mso-style-name:"Table Normal";
        	mso-tstyle-rowband-size:0;
        	mso-tstyle-colband-size:0;
        	mso-style-noshow:yes;
        	mso-style-parent:"";
        	mso-padding-alt:0in 5.4pt 0in 5.4pt;
        	mso-para-margin:0in;
        	mso-para-margin-bottom:.0001pt;
        	mso-pagination:widow-orphan;
        	font-size:10.0pt;
        	font-family:"Times New Roman";
        	mso-ansi-language:#0400;
        	mso-fareast-language:#0400;
        	mso-bidi-language:#0400;}
        </style>
        <![endif]-->
        </head>
        <body lang=EN-US style=\'tab-interval:.5in\'>

        <div class=Section1>

        <p class=MsoNormal>UPS CONTROL <span class=GramE>LOG</span></p>

        <p class=MsoNormal>DATE: ' . date('d') . ' ' . date('M') . ' ' . date('Y') . ' UPS SHIPPER NO. ' . $this->shipperNumber . ' </p>
        <br />
        <br />
        <p class=MsoNormal>TRACKING # PACKAGE ID REFRENCE NUMBER DECLARED VALUE
        CURRENCY </p>
        <p class=MsoNormal>--------------------------------------------------------------------------------------------------------------------------
        </p>
        <br /><br />
        <p class=MsoNormal>' . $this->trackingNumber . ' <span class=GramE>' . $this->packagingReferenceNumberValue . ' ' . round($this->codMonetaryValue, 2) . '</span> ' . Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol() . ' </p>
        <br /><br />
        <p class=MsoNormal>Total Number of Declared Value Packages = 1 </p>
        <p class=MsoNormal>--------------------------------------------------------------------------------------------------------------------------
        </p>
        <br /><br />
        <p class=MsoNormal>RECEIVED BY_________________________PICKUP
        TIME__________________PKGS_______ </p>
        </div>
        </body>
        </html>';
                file_put_contents($path . "HVR" . $this->shipmentIdentificationNumber . ".html", $htmlHVReport);
            }
            return array(
                'arrResponsXML' => $arrResponsXML,
                'digest' => '' . $this->shipmentDigest . '',
                'shipidnumber' => '' . $this->shipmentIdentificationNumber . '',
            );
        } else {
            $error = '<h1>Error</h1> <ul>';
            $errorss = $xml->Response->Error[0];
            $error .= '<li>Error Severity : ' . $errorss->ErrorSeverity . '</li>';
            $error .= '<li>Error Code : ' . $errorss->ErrorCode . '</li>';
            $error .= '<li>Error Description : ' . $errorss->ErrorDescription . '</li>';
            $error .= '</ul>';
            $error .= '<textarea>' . $result . '</textarea>';
            $error .= '<textarea>' . $data . '</textarea>';
            return array('error' => $error);
            //return print_r($xml->Response->Error);
        }
    }

    function getShipPrice()
    {
        if ($this->credentials != 1) {
            return array('error' => array('cod' => 1, 'message' => 'Not correct registration data'), 'success' => 0);
        }
        $this->customerContext = str_replace('&', '&amp;', strtolower(Mage::app()->getStore()->getName()));
        $data = "<?xml version=\"1.0\" ?>
<AccessRequest xml:lang='en-US'>
<AccessLicenseNumber>" . $this->AccessLicenseNumber . "</AccessLicenseNumber>
<UserId>" . $this->UserID . "</UserId>
<Password>" . $this->Password . "</Password>
</AccessRequest>
<?xml version=\"1.0\"?>
<RatingServiceSelectionRequest xml:lang=\"en-US\">
  <Request>
    <TransactionReference>
      <CustomerContext>" . $this->customerContext . "</CustomerContext>
      <XpciVersion>1.0</XpciVersion>
    </TransactionReference>
    <RequestAction>Rate</RequestAction>
    <RequestOption>Rate</RequestOption>
  </Request>
  <Shipment>";
        if (Mage::getStoreConfig('upslabel/profile/negotiatedratesindicator') == 1) {
            $data .= "
   <RateInformation>
      <NegotiatedRatesIndicator/>
    </RateInformation>";
        }
        if (strlen($this->shipmentDescription) > 0) {
            $data .= "<Description>" . $this->shipmentDescription . "</Description>";
        }
        $data .= "<Shipper>
<Name>" . $this->shipperName . "</Name>";
        $data .= "<AttentionName>" . $this->shipperAttentionName . "</AttentionName>";

        $data .= "<PhoneNumber>" . $this->shipperPhoneNumber . "</PhoneNumber>
      <ShipperNumber>" . $this->shipperNumber . "</ShipperNumber>
	  <TaxIdentificationNumber></TaxIdentificationNumber>
      <Address>
    	<AddressLine1>" . $this->shipperAddressLine1 . "</AddressLine1>
    	<City>" . $this->shipperCity . "</City>
    	<StateProvinceCode>" . $this->shipperStateProvinceCode . "</StateProvinceCode>
    	<PostalCode>" . $this->shipperPostalCode . "</PostalCode>
    	<PostcodeExtendedLow></PostcodeExtendedLow>
    	<CountryCode>" . $this->shipperCountryCode . "</CountryCode>
     </Address>
    </Shipper>
	<ShipTo>
     <CompanyName>" . $this->shiptoCompanyName . "</CompanyName>
      <AttentionName>" . $this->shiptoAttentionName . "</AttentionName>
      <PhoneNumber>" . $this->shiptoPhoneNumber . "</PhoneNumber>
      <Address>
        <AddressLine1>" . $this->shiptoAddressLine1 . "</AddressLine1>";
        if (strlen($this->shiptoAddressLine2) > 0) {
            $data .= '<AddressLine2>' . $this->shiptoAddressLine2 . '</AddressLine2>';
        }
        $data .= "<City>" . $this->shiptoCity . "</City>
        <StateProvinceCode>" . $this->shiptoStateProvinceCode . "</StateProvinceCode>
        <PostalCode>" . $this->shiptoPostalCode . "</PostalCode>
        <CountryCode>" . $this->shiptoCountryCode . "</CountryCode>
        " . $this->residentialAddress . "
      </Address>
    </ShipTo>
    <ShipFrom>
      <CompanyName>" . $this->shipfromCompanyName . "</CompanyName>
      <AttentionName>" . $this->shipfromAttentionName . "</AttentionName>
      <PhoneNumber>" . $this->shipfromPhoneNumber . "</PhoneNumber>
	  <TaxIdentificationNumber></TaxIdentificationNumber>
      <Address>
        <AddressLine1>" . $this->shipfromAddressLine1 . "</AddressLine1>
        <City>" . $this->shipfromCity . "</City>
    	<StateProvinceCode>" . $this->shipfromStateProvinceCode . "</StateProvinceCode>
    	<PostalCode>" . $this->shipfromPostalCode . "</PostalCode>
    	<CountryCode>" . $this->shipfromCountryCode . "</CountryCode>
      </Address>
    </ShipFrom>
     <PaymentInformation>
      <Prepaid>
        <BillShipper>
          <AccountNumber>" . $this->shipperNumber . "</AccountNumber>
        </BillShipper>
      </Prepaid>
    </PaymentInformation>
    <Service>
      <Code>" . $this->serviceCode . "</Code>
      <Description>" . $this->serviceDescription . "</Description>
    </Service>";
        foreach ($this->packages AS $pv) {
            $data .= "<Package>
      <PackagingType>
        <Code>" . $pv["packagingtypecode"] . "</Code>
      </PackagingType>
      <Description>" . $pv["packagingdescription"] . "</Description>";
            if (($this->shiptoCountryCode == 'US' || $this->shiptoCountryCode == 'PR') && $this->shiptoCountryCode == $this->shipfromCountryCode) {
                $data .= "<ReferenceNumber>
	  	<Code>" . $pv['packagingreferencenumbercode'] . "</Code>
		<Value>" . $pv['packagingreferencenumbervalue'] . "</Value>
	  </ReferenceNumber>";
            }
            $data .= array_key_exists('additionalhandling', $pv) ? $pv['additionalhandling'] : '';
            if ($this->includeDimensions == 1) {
                $data .= "<Dimensions>
<UnitOfMeasurement>
<Code>" . $this->unitOfMeasurement . "</Code>";
                if (strlen($this->unitOfMeasurementDescription) > 0) {
                    $data .= "
<Description>" . $this->unitOfMeasurementDescription . "</<Description>";
                }
                $data .= "</UnitOfMeasurement>
<Length>" . $pv['length'] . "</Length>
<Width>" . $pv['width'] . "</Width>
<Height>" . $pv['height'] . "</Height>
            </Dimensions>";
            }
            $data .= "<PackageWeight>
        <UnitOfMeasurement>
            <Code>" . $this->weightUnits . "</Code>";
            if (strlen($this->weightUnitsDescription) > 0) {
                $data .= "
            <Description>" . $this->weightUnitsDescription . "</<Description>";
            }
            $packweight = array_key_exists('packweight', $pv) ? $pv['packweight'] : '';
            $weight = array_key_exists('weight', $pv) ? $pv['weight'] : '';
            $data .= "</UnitOfMeasurement>
        <Weight>" . round(($weight + (is_numeric(str_replace(',', '.', $packweight)) ? $packweight : 0)), 1) . "</Weight>" . (array_key_exists('large', $pv) ? $pv['large'] : '') . "
      </PackageWeight>все
      <PackageServiceOptions>";
            if (array_key_exists('insuredmonetaryvalue', $pv) && $pv['insuredmonetaryvalue'] > 0) {
                $currencycode = array_key_exists('currencycode', $pv) ? $pv['currencycode'] : '';
                $insuredmonetaryvalue = array_key_exists('insuredmonetaryvalue', $pv) ? $pv['insuredmonetaryvalue'] : '';
                $data .= "<InsuredValue>
                <CurrencyCode>" . $currencycode . "</CurrencyCode>
                <MonetaryValue>" . $insuredmonetaryvalue . "</MonetaryValue>
                </InsuredValue>
              ";
            }
            $cod = array_key_exists('cod', $pv) ? $pv['cod'] : 0;
            if ($cod == 1 && ($this->shiptoCountryCode == 'US' || $this->shiptoCountryCode == 'PR' || $this->shiptoCountryCode == 'CA') && ($this->shipfromCountryCode == 'US' || $this->shipfromCountryCode == 'PR' || $this->shipfromCountryCode == 'CA')) {
                $codfundscode = array_key_exists('codfundscode', $pv) ? $pv['codfundscode'] : '';
                $codmonetaryvalue = array_key_exists('codmonetaryvalue', $pv) ? $pv['codmonetaryvalue'] : '';
                $data .= "
              <COD>
                  <CODCode>3</CODCode>
                  <CODFundsCode>" . $codfundscode . "</CODFundsCode>
                  <CODAmount>
                      <CurrencyCod>" . $currencycode . "</CurrencyCod>
                      <MonetaryValue>" . $codmonetaryvalue . "</MonetaryValue>
                  </CODAmount>
              </COD>";
            }
            $data .= "</PackageServiceOptions>
              </Package>";
        }
        $data .= "<ShipmentServiceOptions>";
        if ($this->codYesNo == 1 && $this->shiptoCountryCode != 'US' && $this->shiptoCountryCode != 'PR' && $this->shiptoCountryCode != 'CA' && $this->shipfromCountryCode != 'US' && $this->shipfromCountryCode != 'PR' && $this->shipfromCountryCode != 'CA') {
            $data .= "<COD>
                  <CODCode>3</CODCode>
                  <CODFundsCode>" . $this->codFundsCode . "</CODFundsCode>
                  <CODAmount>
                      <CurrencyCod>" . $this->currencyCode . "</CurrencyCod>
                      <MonetaryValue>" . $this->codMonetaryValue . "</MonetaryValue>
                  </CODAmount>
              </COD>";
        }
        if ($this->carbon_neutral == 1) {
            $data .= "<UPScarbonneutralIndicator/>";
        }
        $data .= "</ShipmentServiceOptions>";
        $data .= "</Shipment>
</RatingServiceSelectionRequest>
";
        $cie = 'wwwcie';
        if (0 == $this->testing) {
            $cie = 'onlinetools';
        }
        $ch = curl_init('https://' . $cie . '.ups.com/ups.app/xml/Rate');
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        $result = strstr($result, '<?xml');

        //return $result;
        $xml = simplexml_load_string($result);
        if ($xml->Response->ResponseStatusCode[0] == 1) {
            $defaultPrice = $xml->RatedShipment[0]->TotalCharges[0]->MonetaryValue[0];
            $defaultCurrencyCode = $xml->RatedShipment[0]->TotalCharges[0]->CurrencyCode[0];
            $priceNegotiatedRates = array();
            if ($xml->RatedShipment->NegotiatedRates) {
                $priceNegotiatedRates['MonetaryValue'] = $xml->RatedShipment[0]->NegotiatedRates[0]->NetSummaryCharges[0]->GrandTotal[0]->MonetaryValue[0];
                $priceNegotiatedRates['CurrencyCode'] = $xml->RatedShipment[0]->NegotiatedRates[0]->NetSummaryCharges[0]->GrandTotal[0]->CurrencyCode[0];
            }
            return json_encode(array(
                'price' => array(
                    'def' => array('MonetaryValue' => $defaultPrice, 'CurrencyCode' => $defaultCurrencyCode),
                    'negotiated' => $priceNegotiatedRates
                ),
            ));
        } else {
            $error = array('error' => $xml->Response[0]->Error[0]->ErrorDescription[0]);
            return json_encode($error);
        }
    }

    function getShipPriceFrom()
    {
        if ($this->credentials != 1) {
            return array('error' => array('cod' => 1, 'message' => 'Not correct registration data'), 'success' => 0);
        }
        $this->customerContext = str_replace('&', '&amp;', strtolower(Mage::app()->getStore()->getName()));
        $data = "<?xml version=\"1.0\" ?>
        <AccessRequest xml:lang='en-US'>
        <AccessLicenseNumber>" . $this->AccessLicenseNumber . "</AccessLicenseNumber>
        <UserId>" . $this->UserID . "</UserId>
        <Password>" . $this->Password . "</Password>
        </AccessRequest>
        <?xml version=\"1.0\"?>
        <RatingServiceSelectionRequest xml:lang=\"en-US\">
          <Request>
            <TransactionReference>
              <CustomerContext>" . $this->customerContext . "</CustomerContext>
              <XpciVersion/>
            </TransactionReference>
            <RequestAction>Rate</RequestAction>
            <RequestOption>Rate</RequestOption>
          </Request>
          <Shipment>";
        if (Mage::getStoreConfig('upslabel/profile/negotiatedratesindicator') == 1) {
            $data .= "<RateInformation>
      <NegotiatedRatesIndicator/>
    </RateInformation>";
        }
        if (strlen($this->shipmentDescription) > 0) {
            $data .= "<Description>" . $this->shipmentDescription . "</Description>";
        }
        $data .= "<Shipper>
        <Name>" . $this->shipperName . "</Name>";
        $data .= "<AttentionName>" . $this->shipperAttentionName . "</AttentionName>";

        $data .= "<PhoneNumber>" . $this->shipperPhoneNumber . "</PhoneNumber>
              <ShipperNumber>" . $this->shipperNumber . "</ShipperNumber>
        	  <TaxIdentificationNumber></TaxIdentificationNumber>
              <Address>
            	<AddressLine1>" . $this->shipperAddressLine1 . "</AddressLine1>
            	<City>" . $this->shipperCity . "</City>
            	<StateProvinceCode>" . $this->shipperStateProvinceCode . "</StateProvinceCode>
            	<PostalCode>" . $this->shipperPostalCode . "</PostalCode>
            	<PostcodeExtendedLow></PostcodeExtendedLow>
            	<CountryCode>" . $this->shipperCountryCode . "</CountryCode>
             </Address>
            </Shipper>
        	<ShipFrom>
             <CompanyName>" . $this->shiptoCompanyName . "</CompanyName>
              <AttentionName>" . $this->shiptoAttentionName . "</AttentionName>
              <PhoneNumber>" . $this->shiptoPhoneNumber . "</PhoneNumber>
              <TaxIdentificationNumber></TaxIdentificationNumber>
              <Address>
                <AddressLine1>" . $this->shiptoAddressLine1 . "</AddressLine1>
                <City>" . $this->shiptoCity . "</City>
                <StateProvinceCode>" . $this->shiptoStateProvinceCode . "</StateProvinceCode>
                <PostalCode>" . $this->shiptoPostalCode . "</PostalCode>
                <CountryCode>" . $this->shiptoCountryCode . "</CountryCode>
              </Address>
            </ShipFrom>
            <ShipTo>
              <CompanyName>" . $this->shipfromCompanyName . "</CompanyName>
              <AttentionName>" . $this->shipfromAttentionName . "</AttentionName>
              <PhoneNumber>" . $this->shipfromPhoneNumber . "</PhoneNumber>
              <Address>
                <AddressLine1>" . $this->shipfromAddressLine1 . "</AddressLine1>";
        if (strlen($this->shiptoAddressLine2) > 0) {
            $data .= '<AddressLine2>' . $this->shiptoAddressLine2 . '</AddressLine2>';
        }
        $data .= "<City>" . $this->shipfromCity . "</City>
            	<StateProvinceCode>" . $this->shipfromStateProvinceCode . "</StateProvinceCode>
            	<PostalCode>" . $this->shipfromPostalCode . "</PostalCode>
            	<CountryCode>" . $this->shipfromCountryCode . "</CountryCode>
              </Address>
            </ShipTo>
            <Service>
              <Code>" . $this->serviceCode . "</Code>
              <Description>" . $this->serviceDescription . "</Description>
            </Service>";
        foreach ($this->packages AS $pv) {
            $data .= "<Package>
      <PackagingType>
        <Code>" . $pv["packagingtypecode"] . "</Code>
      </PackagingType>
      <Description>" . $pv["packagingdescription"] . "</Description>";
            if (($this->shiptoCountryCode == 'US' || $this->shiptoCountryCode == 'PR') && $this->shiptoCountryCode == $this->shipfromCountryCode) {
                $data .= "<ReferenceNumber>
	  	<Code>" . $pv['packagingreferencenumbercode'] . "</Code>
		<Value>" . $pv['packagingreferencenumbervalue'] . "</Value>
	  </ReferenceNumber>";
            }
            $data .= array_key_exists('additionalhandling', $pv) ? $pv['additionalhandling'] : '';
            if ($this->includeDimensions == 1) {
                $data .= "<Dimensions>
<UnitOfMeasurement>
<Code>" . $this->unitOfMeasurement . "</Code>";
                if (strlen($this->unitOfMeasurementDescription) > 0) {
                    $data .= "
<Description>" . $this->unitOfMeasurementDescription . "</<Description>";
                }
                $data .= "</UnitOfMeasurement>
<Length>" . $pv['length'] . "</Length>
<Width>" . $pv['width'] . "</Width>
<Height>" . $pv['height'] . "</Height>
            </Dimensions>";
            }
            $data .= "<PackageWeight>
        <UnitOfMeasurement>
            <Code>" . $this->weightUnits . "</Code>";
            if (strlen($this->weightUnitsDescription) > 0) {
                $data .= "
            <Description>" . $this->weightUnitsDescription . "</<Description>";
            }
            $data .= "</UnitOfMeasurement>
        <Weight>" . round(($pv['weight'] + (is_numeric(str_replace(',', '.', $pv['packweight'])) ? $pv['packweight'] : 0)), 1) . "</Weight>" . $pv['large'] . "
      </PackageWeight>
      <PackageServiceOptions>";
            if ($pv['insuredmonetaryvalue'] > 0) {
                $data .= "<InsuredValue>
                <CurrencyCode>" . $pv['currencycode'] . "</CurrencyCode>
                <MonetaryValue>" . $pv['insuredmonetaryvalue'] . "</MonetaryValue>
                </InsuredValue>
              ";
            }
            if ($pv['cod'] == 1 && ($this->shiptoCountryCode == 'US' || $this->shiptoCountryCode == 'PR' || $this->shiptoCountryCode == 'CA') && ($this->shipfromCountryCode == 'US' || $this->shipfromCountryCode == 'PR' || $this->shipfromCountryCode == 'CA')) {
                $data .= "
              <COD>
                  <CODCode>3</CODCode>
                  <CODFundsCode>0</CODFundsCode>
                  <CODAmount>
                      <CurrencyCod>" . $pv['currencycode'] . "</CurrencyCod>
                      <MonetaryValue>" . $pv['codmonetaryvalue'] . "</MonetaryValue>
                  </CODAmount>
              </COD>";
            }
            $data .= "</PackageServiceOptions>
              </Package>";
        }
        $data .= "</Shipment>
        </RatingServiceSelectionRequest>
        ";
        $cie = 'wwwcie';
        if (0 == $this->testing) {
            $cie = 'onlinetools';
        }
        $ch = curl_init('https://' . $cie . '.ups.com/ups.app/xml/Rate');
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        $result = strstr($result, '<?xml');

        //return $result;
        $xml = simplexml_load_string($result);
        if ($xml->Response->ResponseStatusCode[0] == 1) {
            $defaultPrice = $xml->RatedShipment[0]->TotalCharges[0]->MonetaryValue[0];
            $defaultCurrencyCode = $xml->RatedShipment[0]->TotalCharges[0]->CurrencyCode[0];
            $priceNegotiatedRates = array();
            if ($xml->RatedShipment->NegotiatedRates) {
                $priceNegotiatedRates['MonetaryValue'] = $xml->RatedShipment[0]->NegotiatedRates[0]->NetSummaryCharges[0]->GrandTotal[0]->MonetaryValue[0];
                $priceNegotiatedRates['CurrencyCode'] = $xml->RatedShipment[0]->NegotiatedRates[0]->NetSummaryCharges[0]->GrandTotal[0]->CurrencyCode[0];
            }
            return json_encode(array(
                'price' => array(
                    'def' => array('MonetaryValue' => $defaultPrice, 'CurrencyCode' => $defaultCurrencyCode),
                    'negotiated' => $priceNegotiatedRates
                ),
            ));
        } else {
            $error = array('error' => $xml->Response[0]->Error[0]->ErrorDescription[0]);
            return json_encode($error);
        }
    }

    public function deleteLabel($trnum)
    {
        $path_xml = Mage::getBaseDir('media') . DS . 'upslabel' . DS . "test_xml" . DS;
        $cie = 'wwwcie';
        $testing = Mage::getStoreConfig('upslabel/profile/testing');
        $shipIndefNumbr = $trnum;
        if (0 == $testing) {
            $cie = 'onlinetools';
        } else {
            /*$trnum = '1Z2220060291994175';*/
            $shipIndefNumbr = '1ZISDE016691676846';
        }
        $data = "<?xml version=\"1.0\" ?>
<AccessRequest xml:lang='en-US'>
<AccessLicenseNumber>" . $this->AccessLicenseNumber . "</AccessLicenseNumber>
<UserId>" . $this->UserID . "</UserId>
<Password>" . $this->Password . "</Password>
</AccessRequest>
<?xml version=\"1.0\" ?>
<VoidShipmentRequest>
<Request>
<RequestAction>1</RequestAction>
</Request>
<ShipmentIdentificationNumber>" . $shipIndefNumbr . "</ShipmentIdentificationNumber>
    <ExpandedVoidShipment>
          <ShipmentIdentificationNumber>" . $shipIndefNumbr . "</ShipmentIdentificationNumber>
          </ExpandedVoidShipment>
</VoidShipmentRequest> ";
        /*<TrackingNumber>" . $trnum . "</TrackingNumber>*/
        /*  */
        $file = file_put_contents($path_xml . "VoidShipmentRequest.xml", $data);

        $ch = curl_init('https://' . $cie . '.ups.com/ups.app/xml/Void');
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        $result = strstr($result, '<?xml');
        if ($result) {
            $file = file_put_contents($path_xml . "VoidShipmentResponse.xml", $result);
        }
        $xml = simplexml_load_string($result);
        if ($xml->Response->Error[0] && (int)$xml->Response->Error[0]->ErrorCode != 190117) {
            $error = '<h1>Error</h1> <ul>';
            $errorss = $xml->Response->Error[0];
            $error .= '<li>Error Severity : ' . $errorss->ErrorSeverity . '</li>';
            $error .= '<li>Error Code : ' . $errorss->ErrorCode . '</li>';
            $error .= '<li>Error Description : ' . $errorss->ErrorDescription . '</li>';
            $error .= '</ul>';
            $error .= '<textarea>' . $result . '</textarea>';
            $error .= '<textarea>' . $data . '</textarea>';
            return array('error' => $error);
        } else {
            return true;
        }
    }

}

?>
