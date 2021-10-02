<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterTenantRequest;
use App\Models\Tenant;


use Aws\Route53\Route53Client;
use Aws\Exception\CredentialsException;
use Aws\Route53\Exception\Route53Exception;

class RegisteredTenantController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(RegisterTenantRequest $request)
    {
        $tenant = Tenant::create($request->validated());

        $tenant->createDomain(['domain' => $request->domain]);
        
        if(env('AWS_SECRET_ACCESS_KEY')!=null){
            $resp = $this->createNewSubDomainAWS($request->domain);
            if($resp!=null){
                return redirect(tenant_route($tenant->domains->first()->domain, 'tenant.login'));
            }

        }else{
            return redirect(tenant_route($tenant->domains->first()->domain, 'tenant.login'));
        }
        

        
    }


    private function createNewSubDomainAWS($subDomainName)
    {

        $client = Route53Client::factory(array(
            'region' => 'us-east-1', //region aws  
            'version' => 'latest',   // eg. latest or 2013-04-01   
            'credentials' => [
              'key' => env('AWS_ACCESS_KEY_ID'),
              'secret' => env('AWS_SECRET_ACCESS_KEY'),
            ]
        ));
        $dns = env('DNS');
        $ResourceRecordsValue = array('Value' => $dns);

        $resp= $client->changeResourceRecordSets([
            'ChangeBatch' => [
                'Changes' => [
                    [
                        'Action' => 'CREATE',
                        "ResourceRecordSet" => [
                            'Name' => $subDomainName,
                            'Type' => 'CNAME',
                            'TTL' => '300',
                            'ResourceRecords' => [
                                $ResourceRecordsValue,
                            ],
                        ],
                    ],
                ],
            ],
            'HostedZoneId' => env('AWS_HOSTED_ZONE_ID'),
        ]);

        return $resp;
    }

}
