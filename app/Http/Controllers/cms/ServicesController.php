<?php

namespace App\Http\Controllers\cms;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\eros\ErosDB;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DateTime;

class ServicesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //echo session('userDepartmentCode'); die();
        $userRole = json_decode(session('userRole'));
        $matchedRoles = [];
            foreach ($userRole as $role) {
                $user = trim($role->ldap_role);
                if (strpos($user, '-BRANCH') !== false) {
                    $matchedRoles[] = $role->ldap_role;
                }
            }
            $transformedRoles = array_map(function($role) {
                if (preg_match('/\[(.*?)\-BRANCH\]/', $role, $matches)) {
                    return $matches[1];
                }
                return $role; 
            }, $matchedRoles);
            if (!empty($transformedRoles)) {
                $clinicCode = $transformedRoles;
            }
        $clinic = ErosDB::getClinicData(NULL,NULL,$clinicCode);
        $clinicName = ErosDB::getClinicData(session('userClinicCode'));
// dd($clinic);
        // $serverIp = getHostByName(getHostName());
        // $ipParts = explode('.', $serverIp);
        // $gatewayIp = $ipParts[0] . '.' . $ipParts[1] . '.' . $ipParts[2] . '.1';

		return view('cms.services',['Clinics' => $clinic, 'clinicName' => $clinicName, 'ClinicCode' => $clinicCode]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {


	}

    public function checkSql(Request $request)
    {
        $action = $request->query('action');
        $clinicName = strtolower(session('userClinicCode'));
        $user = session('userClinicCode');
        $output = '';

        try {
            if ($action === 'sqlStatus') {
                $cmd = escapeshellcmd("/usr/bin/sudo /home/cms/sqlStatus.sh sqlStatus");
                $output = shell_exec($cmd);
            } elseif ($action === 'sqlStart') {
                $cmd = escapeshellcmd("/usr/bin/sudo /home/cms/sqlStatus.sh sqlStart");
                $output = shell_exec($cmd);
            } elseif ($action === 'Skip') {
                $lastSqlErrno = $request->query('lastSqlErrno');
                                                              # ACTION  CLINIC    USER  LOGFILE  LOGPOS  LAST_ERRNO
                $cmd = escapeshellcmd("/usr/bin/sudo /home/cms/sqlStatus.sh Skip $clinicName '' '' '' $lastSqlErrno");
                $output = shell_exec($cmd);
            } elseif ($action === 'Reset') {
                $sourceLogFile = $request->query('sourceLogFile');
                $readSourceLogPos = $request->query('readSourceLogPos');
                $lastSqlErrno = $request->query('lastSqlErrno');
                $cmd = "/usr/bin/sudo /home/cms/sqlStatus.sh Reset $clinicName $user $sourceLogFile $readSourceLogPos $lastSqlErrno";
                $output = shell_exec($cmd);
            }

            if (!$output) {
                return response("SQL script failed or not permitted.", 500);
            }

            return response($output, 200)->header('Content-Type', 'text/plain');
        } catch (\Exception $e) {
            return response("Error: " . $e->getMessage(), 500);
        }
    }

    public function runSocket()
    {
        $cmd = escapeshellcmd("/usr/bin/sudo /home/cms/nodeJS.sh");

        try {
            $output = shell_exec($cmd);

            if (!$output) {
                return response("Socket script failed or not permitted.", 500);
            }

            return response($output, 200)->header('Content-Type', 'text/plain');
        } catch (\Exception $e) {
            return response("Error: " . $e->getMessage(), 500);
        }
    }

    public function runHl7()
    {
        $cmd = escapeshellcmd("/usr/bin/sudo /home/cms/HL7Mount.sh");

        try {
            $output = shell_exec($cmd);

            if (!$output) {
                return response("HL7 Mounting script failed or not permitted.", 500);
            }

            return response($output, 200)->header('Content-Type', 'text/plain');
        } catch (\Exception $e) {
            return response("Error: " . $e->getMessage(), 500);
        }
    }


    public function jasperAction(Request $request)
    {
        $action = $request->query('action');

        if (!in_array($action, ['start', 'stop', 'status'])) {
            return response("Invalid action", 400);
        }

        $cmd = escapeshellcmd("/usr/bin/sudo /opt/jasperreports-server-cp-7.1.0/ctlscript.sh $action");

        try {
            $output = shell_exec($cmd);

            if (!$output) {
                return response("Command failed or not permitted.", 500);
            }

            return response($output, 200)->header('Content-Type', 'text/plain');
        } catch (\Exception $e) {
            return response("Error: " . $e->getMessage(), 500);
        }
    }


    public function ping(Request $request)
    {
        $ip = $request->query('ip');
        $mode = $request->query('mode', 'ping'); // default = ping

        if (!$ip) {
            return response("No IP provided", 400);
        }

        $escapedIp = escapeshellarg($ip);

        try {
            if ($mode === "traceroute") {
                $command = "traceroute $escapedIp";
            } else {
                $command = "ping -c 4 $escapedIp";
            }

            $output = shell_exec($command);

            if (!$output) {
                return response("Command failed or not permitted.", 500);
            }

            return response($output, 200)
                ->header('Content-Type', 'text/plain');
        } catch (\Exception $e) {
            return response("Error: " . $e->getMessage(), 500);
        }
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
