<?php

namespace App\Http\Controllers\registration;
use App\Http\Controllers\Controller;

use Artisaninweb\SoapWrapper\SoapWrapper;

class SoapController extends Controller
{
    protected $soapWrapper;

    public function __construct(SoapWrapper $soapWrapper)
    {
        $this->soapWrapper = $soapWrapper;
    }

    public function callSoapService($lang)
    {
        $this->soapWrapper->add('ServRecvrDem2', function ($service) {
            $service
                ->wsdl('https://www4.parte.ramq.gouv.qc.ca/RAT/HL/HLB_RecvrDemAT/HLB2_ServRecvrDem_svc/ServRecvrDem2.svc?wsdl')
                ->trace(true);
        });
$username = "AIR0223AA";
$password = "Amar@2023a";
        // Call the SOAP service
        $response = $this->soapWrapper->call('ServRecvrDem2.RecevoirDemXML', [
            // Parameters for the SOAP method
            'UserName' => $username, 'Password' => $password
        ]);

        return view('registration.soapview', ['response' => $response]);
    }
}

