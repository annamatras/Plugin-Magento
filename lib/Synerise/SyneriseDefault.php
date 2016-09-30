<?php
namespace Synerise;

class SyneriseDefault extends SyneriseAbstractHttpClient
{
    public function test() 
    {
        try {
            $response = $this->request("GET", SyneriseAbstractHttpClient::BASE_API_URL . "/test");

            if ($response->getStatusCode() != '200') {
                throw new Exception\SyneriseException('API Synerise not responsed 200.', 500);
            }
            
            $responseArray = json_decode($response->getBody(), true);
            return isset($responseArray['data']) ? $responseArray['data'] : null;                        

        } catch (\Exception $e) {

            $this->_log($e->getMessage(), "DefaultERROR");
            throw $e;
        }
    }
}