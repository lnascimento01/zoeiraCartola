<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Unirest;
use function Symfony\Component\Debug\header;
use function view;

class AuthenticarController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request) {

//        $_SESSION['X-GLB-Token'] = '102058e952e669adbe7c5ba6d8b30dca56e61543435704877784158424f4b485641474b55474c335178786d422d4b4a6d475a5448647065564f555a724a456f694d6f34683949594f524c504a78676f7539526168734777767057666d356649334c6457634d673d3d3a303a6c65616e64726f5f66726f65732e32303135';
//        $logado = $this->consulta('https://api.cartolafc.globo.com/auth/ligas');
        
        $logado = $this->autenticar($request->login, $request->pass);
        
        return $logado;
//        $apostadores = DB::table('apostadores')
//                        ->select('*')
//                        ->where('status', '=', 1)->orderBy('id', 'asc')->get()->toArray();
//        $status = $this->consulta($apostadores);
//        $users = $this->consulta($apostadores);

        return view('index', [$apostadores]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function consulta($url) {
        $response = Unirest\Request::get($url, array(
                    "X-GLB-Token" => "10e2e2b0eba61a7b6698f7621cdb0ebb74872762d507443756a71476e5555575153726155703731784a62426d6570645935683546414355753879364d5851386445316e396531517a314f3663756256542d5f70665357575633646a6b754e4a657767756c78773d3d3a303a6c65616e64726f5f66726f65732e32303135",
                    "Accept" => "application/json"
                        )
        );
        return $response;
    }

    public function consultaApi($url) {
        $response = Unirest\Request::get($url, array(
                    "X-Mashape-Key" => "IuOeHxDFpNmshodMtqadRp6mH0xDp1AWcE9jsn3YxNFEO45CZr",
                    "Accept" => "application/json"
                        )
        );
        return $response;
    }

    public function parciais($url) {
        $options = array(
            'http' =>
            array(
                'method' => 'GET',
                'user_agent' => 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)',
                'timeout' => 1
            )
        );

        $context = stream_context_create($options);
        $file = file_get_contents($url, false, $context);

        return json_decode($file, true);
    }

    public function autenticar($login, $pass) {

        $body = array('payload' => array(
                'email' =>$login,
                'password' => $pass,
                'serviceId' => 4728
        ));

        return $this->sendRequest('authentication', array(
                    'base' => 'https://login.globo.com/api/',
                    'body' => $body
        ));
    }

    function sendRequest($path, $options = array()) {
        $options = array_merge(array(
            'base' => 'https://api.cartolafc.globo.com/',
            'body' => false,
            'token' => false
                ), $options);
        $c = curl_init();
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_URL, $options['base'] . $path);
        if ($options['body']) {
            curl_setopt($c, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($c, CURLOPT_POST, true);
            curl_setopt($c, CURLOPT_POSTFIELDS, json_encode($options['body']));
        } else {
            curl_setopt($c, CURLOPT_FRESH_CONNECT, true);
        }
        if ($options['token']) {
            curl_setopt($c, CURLOPT_HTTPHEADER, array('X-GLB-Token: ' . $options['token']));
            curl_setopt($c, CURLOPT_VERBOSE, false);
        }
        
        $result = curl_exec($c);
        curl_close($c);
        return $result;
    }
}
