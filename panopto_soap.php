<?php
require_once 'config.php';
require_once 'lib.php';
require_once 'classes/RemoteRecorderManagement.php';
require_once 'classes/SessionManagement.php';
require_once 'vendor/autoload.php';



//generate an auth code
$AuthCode = generate_auth_code($UserKey, $ServerName, $ApplicationKey);

$requestPagination = Create_Pagination_Object(500, 0);
//Create a SOAP client for the desired Panopto API class, in this cas SessionManagement

//For RemoteRecorderManagement
$remoteRecorderManagementClient = new PanoptoRemoteRecorderManagementClient($ServerName,$UserKey,$AuthCode,$Password);
$remoteRecorderManagementClient->__setLocation("https://". $ServerName . "/Panopto/PublicAPI/4.6/RemoteRecorderManagement.svc");
$response = $remoteRecorderManagementClient->get_remote_recorders($requestPagination,'');
echo "<h2>:Remote Recorders :</h2>";

 //new \Ospinto\dBug($response);




//Create a list session request object. Sample values shown here.
$listSessionsRequest = Create_ListSessionsRequest_Object(
    "2017-02-27T12:12:22",
    null,
    $requestPagination,
    null,
    "Name",
    true,
    "2012-02-27T12:12:22",array('5'));

$listFolderRequest = Create_ListFolderRequest_Object(
    $requestPagination,
    null,
    true,
    "Name",
    true,
    false
);
$listSessionsRequest = Create_ListSessionsRequest_Object2(
    "2018-02-27T12:12:22",
    "00000000-0000-0000-0000-000000000000",
    "00000000-0000-0000-0000-000000000000",
    "Name",
    true,
    "2015-02-27T12:12:22");
 $sessionManagementClient = new PanoptoSessionManagementSoapClient($ServerName, $UserKey, $AuthCode, $Password);
// // //Set https endpoint in case wsdl specifies http
 $sessionManagementClient ->__setLocation("https://". $ServerName . "/Panopto/PublicAPI/4.6/SessionManagement.svc");
  //$response = $sessionManagementClient->get_folder_list($listFolderRequest,null);
$response2 = $sessionManagementClient->get_session_list($listSessionsRequest,"");
  echo "<h2>:Sessions:</h2>";
new \Ospinto\dBug($response2);
//var_dump($response);
//new \Ospinto\dBug($response);



/****** UPDATE FOLDER HERE **********/
//$updateresponse = $sessionManagementClient->update_folder_name('0008894a-2417-4451-bdcd-92cfdbc9e21b','TATS API2');

//new \Ospinto\dBug($updateresponse);


//Example of creating object for use in a SOAP request.
//This will create a ListSessionsRequest object for use as a parameter in the
//ISessionManagement.GetSessionsList method.
//Refer to the API documentation on the requirements and datatypes of members.
//Members must be created within the containing object in the same order they appear in the documentation.
//All names are case sensitive.
function  Create_ListSessionsRequest_Object($endDate, $folderId, $pagination,$remoteRecorderId, $sortBy, $sortIncreasing, $startDate,$states)
{

    //Create empty object to store member data
    $listSessionsRequest = new stdClass();

    $listSessionsRequest->EndDate = new SoapVar($endDate, XSD_STRING, null, null, null, PanoptoSessionManagementSoapClient::OBJECT_MEMBER_NAMESPACE);
    $listSessionsRequest->FolderId = new SoapVar($folderId, XSD_STRING, null, null, null, PanoptoSessionManagementSoapClient::OBJECT_MEMBER_NAMESPACE);
    $listSessionsRequest->Pagination = new SoapVar($pagination,SOAP_ENC_OBJECT, null, null, null, PanoptoSessionManagementSoapClient::OBJECT_MEMBER_NAMESPACE);
    $listSessionsRequest->RemoteRecorderId = new SoapVar($remoteRecorderId, XSD_STRING, null, null, null, PanoptoSessionManagementSoapClient::OBJECT_MEMBER_NAMESPACE);
    $listSessionsRequest->SortBy = new SoapVar($sortBy, XSD_STRING, null, null, null, PanoptoSessionManagementSoapClient::OBJECT_MEMBER_NAMESPACE);
    $listSessionsRequest->SortIncreasing = new SoapVar($sortIncreasing, XSD_BOOLEAN, null, null, null, PanoptoSessionManagementSoapClient::OBJECT_MEMBER_NAMESPACE);
    $listSessionsRequest->StartDate = new SoapVar($startDate, XSD_STRING, null, null, null, PanoptoSessionManagementSoapClient::OBJECT_MEMBER_NAMESPACE);
    $listSessionsRequest->States = new SoapVar($states, SOAP_ENC_OBJECT, null, null, null, PanoptoSessionManagementSoapClient::OBJECT_MEMBER_NAMESPACE);
    return $listSessionsRequest;
}
function Create_ListSessionsRequest_Object2($endDate, $folderId, $remoteRecorderId, $sortBy, $sortIncreasing, $startDate)
{
    //Create empty object to store member data
    $listSessionsRequest = new stdClass();
    $listSessionsRequest->EndDate = new SoapVar($endDate, XSD_STRING, null, null, null, PanoptoSessionManagementSoapClient::OBJECT_MEMBER_NAMESPACE);
    $listSessionsRequest->FolderId = new SoapVar($folderId, XSD_STRING, null, null, null, PanoptoSessionManagementSoapClient::OBJECT_MEMBER_NAMESPACE);
    $listSessionsRequest->RemoteRecorderId = new SoapVar($remoteRecorderId, XSD_STRING, null, null, null, PanoptoSessionManagementSoapClient::OBJECT_MEMBER_NAMESPACE);
    $listSessionsRequest->SortBy = new SoapVar($sortBy, XSD_STRING, null, null, null, PanoptoSessionManagementSoapClient::OBJECT_MEMBER_NAMESPACE);
    $listSessionsRequest->SortIncreasing = new SoapVar($sortIncreasing, XSD_BOOLEAN, null, null, null, PanoptoSessionManagementSoapClient::OBJECT_MEMBER_NAMESPACE);
    $listSessionsRequest->StartDate = new SoapVar($startDate, XSD_STRING, null, null, null, PanoptoSessionManagementSoapClient::OBJECT_MEMBER_NAMESPACE);
    return $listSessionsRequest;
}
function Create_ListFolderRequest_Object($pagination,$parentFolderId,$publicOnly,$sortBy,$sortIncreasing,$WildcardSearchNameOnly){
    $folderListRequest = new stdClass();
    $folderListRequest->Pagination = new SoapVar($pagination,SOAP_ENC_OBJECT, null, null, null, PanoptoSessionManagementSoapClient::OBJECT_MEMBER_NAMESPACE);
    $folderListRequest->ParentFolderId = new SoapVar($parentFolderId,XSD_STRING);
    $folderListRequest->PublicOnly= new SoapVar($publicOnly,XSD_STRING);
    $folderListRequest->SortBy= new SoapVar($sortBy,XSD_STRING);
    $folderListRequest->SortIncreasing= new SoapVar($sortIncreasing,XSD_STRING);
    $folderListRequest->WildcardSearchNameOnly=new SoapVar($WildcardSearchNameOnly,XSD_STRING);
    return $folderListRequest;

}

function Create_RemoteRecorder_Object($devices,$id,$machineIp,$name,$settingsUrl,$state){
    $remoteRecorder = new stdClass();
    $remoteRecorder->Devices = new SoapVar($devices,XSD_ANYTYPE);
    $remoteRecorder->Id = new SoapVar($id,XSD_STRING);
    $remoteRecorder->MachineIP = new SoapVar($machineIp,XSD_STRING);
    $remoteRecorder->Name = new SoapVar($name,XSD_STRING);
    $remoteRecorder->SettingsUrl = new SoapVar($settingsUrl,XSD_STRING);
    $remoteRecorder->State = new SoapVar($state,XSD_ANYTYPE);
    return $remoteRecorder;

}

function Create_Pagination_Object($maxNumberResults, $pageNumber)
{

    //Create empty object to store member data
    $pagination = new stdClass();
    $pagination->MaxNumberResults = $maxNumberResults;
    $pagination->PageNumber = $pageNumber;
    return $pagination;
}
