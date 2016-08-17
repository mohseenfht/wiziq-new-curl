<?php
class wiziq_authBase
{
	private $secretAcessKey="";
	private $access_key="";
	public function __construct($secretAcessKey,$access_key)
	{
		$this->secretAcessKey=$secretAcessKey;
		$this->access_key=$access_key;
	}
function wiziq_generateTimeStamp()
{
	return time();
}

function wiziq_generateSignature($methodName,&$requestParameters) {
$signatureBase="";
$secretAcessKey = urlencode($this->secretAcessKey);
$requestParameters["access_key"] = $this->access_key;
$requestParameters["timestamp"] =$this->wiziq_generateTimeStamp();
$requestParameters["method"] = $methodName;

foreach ($requestParameters as $key => $value)
{
	if(strlen($signatureBase)>0)
	$signatureBase.="&";
	$signatureBase.="$key=$value";
}
//echo "<br>signatureBase=".$signatureBase;
return base64_encode($this->wiziq_hmacsha1($secretAcessKey, $signatureBase));
}

function wiziq_hmacsha1($key,$data) { 
    $blocksize=64;
    $hashfunc='sha1';
    if (strlen($key)>$blocksize)
        $key=pack('H*', $hashfunc($key));
    $key=str_pad($key,$blocksize,chr(0x00));
    $ipad=str_repeat(chr(0x36),$blocksize);
    $opad=str_repeat(chr(0x5c),$blocksize);
    $hmac = pack(
                'H*',$hashfunc(
                    ($key^$opad).pack(
                        'H*',$hashfunc(
                            ($key^$ipad).$data
                        )
                    )
                )
            );
    return $hmac;
}

}//end class AuthBase

class wiziq_httpRequest
{
		function wiziq_do_post_request($url, $data, $optional_headers = null)
		  {  
			////////////   Curl Function if fopen not work also make sure the curl extenion is enable /////////////
			             try {
					            $ch = curl_init();
					            curl_setopt($ch, CURLOPT_URL, $url );
					            curl_setopt($ch, CURLOPT_POST, 1);
					            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
					            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					            curl_setopt($ch, CURLOPT_VERBOSE, 1 );
					            $response = curl_exec($ch);
					            return $response;
					            // close cURL resource, and free up system resources
					            curl_close($ch);
					        } catch (Exception $e) {
					            $errorexecption = $e->getMessage();
					            $errormsg = get_string('errorinservice', 'wiziq'). " " . $errorexecption;
					            echo "Problem reading data from".$url;
					        }
			   ////////////   Curl Function if fopen not work also make sure the curl extenion is enable /////////////
		  }
}//end class HttpRequest
