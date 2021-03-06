<?php
class PanoptoSessionManagementSoapClient extends SoapClient{

    //Namespace used for XML nodes for any root level variables or objects
    const ROOT_LEVEL_NAMESPACE = "http://tempuri.org/";

    //Namespace used for XML nodes for object members
    const OBJECT_MEMBER_NAMESPACE = "http://schemas.datacontract.org/2004/07/Panopto.Server.Services.PublicAPI.V40";

    //Username of calling user.
    public $ApiUserKey;
    //Auth code generated for calling user.
    public $ApiUserAuthCode;
    //Name of Panopto server being called.
    public $Servername;
    //Password needed if provider does not have a bounce page.
    public $Password;

    // Older PHP SOAP clients fail to pass the SOAPAction header properly.
    // Store the current action so we can insert it in __doRequest.
    public $currentURI = 'http://tempuri.org/ISessionManagement/';
    public $currentaction;
    public $uri;
    public $wsLocation = '/Panopto/PublicAPI/4.6/SessionManagement.svc?wsdl' ;
    public function __construct($servername,$apiuseruserkey, $apiuserauthcode, $password) {


        $this->ApiUserKey = $apiuseruserkey;

        $this->ApiUserAuthCode = $apiuserauthcode;

        $this->Servername = $servername;

        $this->Password = $password;
        $locationuri = "https://". $this->Servername . $this->wsLocation;

        // Instantiate SoapClient in WSDL mode.
        //Set call timeout to 5 minutes.
        parent::__construct
        (
            $locationuri,
            array('trace' => 1,'cache_wsdl' => WSDL_CACHE_NONE)
        );

    }

    /**
     *  Helper method for making a call to the Panopto API.
     *  $methodname is the case sensitive name of the API method to be called
     *  $namedparams is an associative array of the member parameters (other than authenticationinfo )
     *   required by the API method being called. Keys should be the case sensitive names of the method's
     *   parameters as specified in the API documentation.
     *  $auth should only be set to false if the method does not require authentication info.
     */
    public function call_web_method($methodname, $namedparams = array(), $auth = true) {
        $params = array();

        // Include API user and auth code params unless $auth is set to false.
        if ($auth)
        {
            //Create SoapVars for AuthenticationInfo object members
            $authinfo = new stdClass();


            $authinfo->AuthCode = new SoapVar(
                $this->ApiUserAuthCode, //Data
                XSD_STRING, //Encoding
                null, //type_name should be left null
                null, //type_namespace should be left null
                null, //node_name should be left null
                self::OBJECT_MEMBER_NAMESPACE); //Node namespace should be set to proper namespace.

            //Add the password parameter if a password is provided
            if(!empty($this->Password))
            {
                $authinfo->Password = new SoapVar($this->Password, XSD_STRING, null, null, null, self::OBJECT_MEMBER_NAMESPACE);
            }

            $authinfo->AuthCode = new SoapVar($this->ApiUserAuthCode, XSD_STRING, null, null, null, self::OBJECT_MEMBER_NAMESPACE);


            $authinfo->UserKey = new SoapVar($this->ApiUserKey, XSD_STRING, null, null, null,self::OBJECT_MEMBER_NAMESPACE);

            //Create a container for storing all of the soap vars required for the request.
            $obj = array();

            //Add auth info to $obj container
            $obj['auth'] = new SoapVar($authinfo, SOAP_ENC_OBJECT, null, null, null, self::ROOT_LEVEL_NAMESPACE);


            //Add the soapvars from namedparams to the container using their key as their member name.
            foreach($namedparams as $key => $value)
            {
                $obj[$key] = $value;
            }

            //Create a soap param using the obj container
            $param = new SoapParam(new SoapVar($obj, SOAP_ENC_OBJECT), 'data');

            //Add the created soap param to an array to be passed to __soapCall
            $params = array($param);
        }

        //Update current action with the method being called.
        $this->currentaction = $this->currentURI.$methodname;
        // Make the SOAP call via SoapClient::__soapCall.
        return parent::__soapCall($methodname, $params);
    }

    /**
     * Sample function for calling an API method. This method will call the sessionmanagement method GetSessionsList.
     * Because this method calls a method from the SessionManagement API, it should only be called by a soap client
     * that has been initialized to SessionManagement.
     * Auth parameter will be created within the soap clients calling logic.
     * $request is a soap encoded ListSessionsRequest object
     * $searchQuery is an optional string containing an custom sql query
     */
    public function get_session_list($request, $searchQuery)
    {
        $requestvar = new SoapVar($request, SOAP_ENC_OBJECT, null, null, null, self::ROOT_LEVEL_NAMESPACE);
        $searchQueryVar = new SoapVar($searchQuery, XSD_STRING, null, null, null, self::ROOT_LEVEL_NAMESPACE);

        return self::call_web_method("GetSessionsList", array("request" => $requestvar, "searchQuery" => $searchQueryVar));
    }
    public function get_folder_by_id($folderIds){
        $requestvar = new SoapVar($folderIds, SOAP_ENC_OBJECT, null, null, null, self::ROOT_LEVEL_NAMESPACE);
        return self::call_web_method("GetFoldersById", array("folderIds" => $requestvar));
    }
    public function get_folder_list($request,$searchQuery){
        $requestvar = new SoapVar($request, SOAP_ENC_OBJECT, null, null, null, self::ROOT_LEVEL_NAMESPACE);
        $searchQueryVar = new SoapVar($searchQuery, XSD_STRING, null, null, null, self::ROOT_LEVEL_NAMESPACE);
        return self::call_web_method("GetFoldersList", array("request" => $requestvar, "searchQuery" => $searchQueryVar));
    }
    public function update_folder_name($folderId,$newName){
        $folderIdVar = new SoapVar($folderId, XSD_STRING, null, null, null, self::ROOT_LEVEL_NAMESPACE);
        $newNameVar = new SoapVar($newName, XSD_STRING, null, null, null, self::ROOT_LEVEL_NAMESPACE);
        return self::call_web_method('UpdateFolderName',array('folderId'=> $folderIdVar,'name'=> $newNameVar));
    }

    /**
     * Override SOAP action to work around bug in older PHP SOAP versions.
     */
    public function __doRequest($request, $location, $action, $version, $oneway = null) {
        error_log(var_export($request,1));
        return parent::__doRequest($request, $location, $this->currentaction, $version);
    }

}