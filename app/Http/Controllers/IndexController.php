<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Unirest;
use function Symfony\Component\Debug\header;
use function view;

class IndexController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {

        $status = $this->consulta('https://api.cartolafc.globo.com/mercado/status');
        $apostadores = DB::table('apostadores')
                        ->select('*')
                        ->where('status', '=', 1)->orderBy('id', 'asc')->get()->toArray();

        $boxPartidas = $this->boxPartidas();

        $tabelaGeral = array_values(array_sort($this->tabelaGeral($apostadores, $status), function ($value) {
                    return $value['pontuacaoGeral'];
                }));

        $tabelaMensal = array_values(array_sort($this->tabelaMensal($apostadores, $status), function ($value) {
                    return $value['pontuacaoMensal'];
                }));

        $tabelaVencedorMes = $this->tabelaVencedorMes($apostadores, $status);

        $tabelaDevido = $this->tabelaDevido($apostadores, $status);

        return view('index', ['boxPartidas' => $boxPartidas, 'tabelaGeral' => $tabelaGeral, 'tabelaMensal' => $tabelaMensal, 'status' => $status, 'tabelaMes' => $tabelaVencedorMes, 'tabelaDevido' => $tabelaDevido]);
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

    public function autenticar($url) {
        $body = array('payload' => array(
                'email' => $login,
                'password' => $password,
                'serviceId' => 4728
        ));
        return sendRequest('authentication', array(
            'base' => 'https://login.globo.com/api/',
            'body' => $body
        ));

        unset($arguments['p']);
        $path = $_GET['p'] . '?' . http_build_query($arguments);
        $results = sendRequest($path, array(
            'token' => isset($_GET['token']) ? $_GET['token'] : false,
            'body' => !empty($_POST) ? $_POST : false
        ));
        if (trim($results) == '404 page not found') {
            header('HTTP/1.0 404 Not Found');
        }
        echo $results;


        if (isset($_GET['p'])) {
            if ($_GET['p'] == 'login' && isset($_GET['login']) && isset($_GET['password'])) {
                echo login($_GET['login'], $_GET['password']);
            } else {
                echo api($_GET);
            }
        }
    }

    public function tabelaGeral($apostadores, $status) {

//        $status = $this->consulta('https://api.cartolafc.globo.com/mercado/status');
        $rodadaAtual = $status['rodada_atual'];
        $tabelaGeral = [];

        foreach ($apostadores as $apostador) {
            $rodada = 1;
            $scoutApostador = [];
            $totalPontos = 0;

            while ($rodada <= $rodadaAtual) {
                $scoutRodada = $this->consulta('https://api.cartolafc.globo.com/time/slug/' . $apostador->slug_time . '/' . $rodada);
                $scoutApostador[$rodadaAtual] = $scoutRodada;
                $totalPontos = $totalPontos + $scoutRodada['pontos'];
                $rodada++;
            }
            $scoutApostador['pontuacaoGeral'] = $totalPontos;
            $tabelaGeral[$apostador->id] = $scoutApostador;
        }
        return $tabelaGeral;
    }

    public function tabelaMensal($apostadores, $status) {

        $mesAtual = date('Y-m');

        $rodadasInfo = DB::table('rodadas')
                        ->select('rodada_id')
                        ->where('inicio', 'like', $mesAtual . '%')->orderBy('id', 'asc')->get()->toArray();

        $rodadaAtual = $status['rodada_atual'];
        $tabelaMensal = [];

        foreach ($apostadores as $apostador) {
            $scoutApostador = [];
            $totalPontos = 0;

            foreach ($rodadasInfo as $rodada) {
                if ($rodada->rodada_id < $rodadaAtual) {
                    $scoutRodada = $this->consulta('https://api.cartolafc.globo.com/time/slug/' . $apostador->slug_time . '/' . $rodada->rodada_id);
                    $scoutApostador[$rodadaAtual] = $scoutRodada;
                    $totalPontos = $totalPontos + $scoutRodada['pontos'];
                } else {
                    $scoutRodada = $this->consulta('https://api.cartolafc.globo.com/time/slug/' . $apostador->slug_time);
                    $scoutApostador[$rodadaAtual] = $scoutRodada;
                }
            }

            $scoutApostador['pontuacaoMensal'] = $totalPontos;
            $tabelaMensal[$apostador->id] = $scoutApostador;
        }

        return $tabelaMensal;
    }

    public function tabelaVencedorMes($apostadores, $status) {
        $rodadasInfo = array('Março' => array(1, 2, 3), 'Abril' => array(4, 5, 6, 7, 8), 'Junho' => array(9, 10, 11, 12), 'julho' => array(13, 14, 15, 16),
            'Agosto' => array(17, 18, 19, 20, 21), 'Setembro' => array(22, 23, 24, 25, 26, 27), 'Outubro' => array(28, 29, 30, 31), 'Novembro' => array(32, 33, 34, 35, 36, 37, 38));

        $tabelaDevido = [];

        foreach ($rodadasInfo as $key => $mes) {
            $devidoMes = [];

            foreach ($apostadores as $apostador) {
                $totalPontos = 0;
                $scoutApostador = [];
                foreach ($mes as $rodada) {

                    if ($status['rodada_atual'] > $rodada) {
                        $scoutRodada = $this->consulta('https://api.cartolafc.globo.com/time/slug/' . $apostador->slug_time . '/' . $rodada);

                        $scoutApostador[$apostador->id] = $scoutRodada;
                        $totalPontos = $totalPontos + $scoutRodada['pontos'];
                    }
                    $devidoMes[$key][$apostador->id] = $scoutRodada;
                    $devidoMes[$key][$apostador->id]['pontuacaoMes'] = $totalPontos;
                }

                $tabelaDevido[$key] = array_values(array_sort($devidoMes[$key], function ($value) {
                            return $value['pontuacaoMes'];
                        }));
            }
        }
        return $tabelaDevido;
    }

    public function tabelaDevido($apostadores, $status) {

        $rodada = 1;
        $tableVencedorRodada = [];
        $apostasRodaada = [];

        while ($rodada < $status['rodada_atual']) {
            $scoutRodada = [];
            foreach ($apostadores as $apostador) {
//                $scoutRodada['apostador'] = $apostador;
                $scoutRodada[$apostador->id] = $this->consulta('https://api.cartolafc.globo.com/time/slug/' . $apostador->slug_time . '/' . $rodada);
                $scoutRodada[$apostador->id]['time']['id_apostador'] = $apostador->id;

//        $rodadaAtual = $status['rodada_atual'];
//        $tabelaGeral = [];
//
//        foreach ($apostadores as $apostador) {
//            $rodada = 1;
//            $scoutApostador = [];
//            $totalPontos = 0;
//
//            while ($rodada <= $rodadaAtual) {
//                $scoutRodada = $this->consulta('https://api.cartolafc.globo.com/time/slug/' . $apostador->slug_time . '/' . $rodada);
//                $scoutApostador[$rodadaAtual] = $scoutRodada;
//                $totalPontos = $totalPontos + $scoutRodada['pontos'];
//                $rodada++;
//            }
//            $scoutApostador['pontuacaoGeral'] = $totalPontos;
//            $tabelaGeral[$apostador->id] = $scoutApostador;
//        }
            }
            $times = array_values(array_sort($scoutRodada, function ($value) {
                        return $value['pontos'];
                    }));


            foreach ($times as $id => $time) {
                if ($id != 0) {
                    $apostasRodaada[$time['time']['id_apostador']]['time'] = $time;
                    if (!isset($apostasRodaada[$time['time']['id_apostador']]['cota'])) {
//                        $apostasRodaada[$time['time']['id_apostador']]['cota'] = !isset($apostasRodaada[$time['time']['id_apostador']]['cota']) ? 0 : $apostasRodaada[$time['time']['id_apostador']]['cota'] + 1;
                        $apostasRodaada[$time['time']['id_apostador']]['cota'] = 0;
                    } else {
                        $apostasRodaada[$time['time']['id_apostador']]['cota'] ++;
                    }
                }
            }

            $rodada++;
        }

        $apostasOrdenadas = array_values(array_sort($apostasRodaada, function ($value) {
                    return $value['cota'];
                }));


        return $apostasOrdenadas;
    }

    public function boxPartidas() {
        $json = $this->consulta('https://api.cartolafc.globo.com/partidas');
        $listaPartidas = array_slice($json, 0, 1);
        $listaClubes = array_slice($json, 1, 2);
        $boxPartidas = [];

        foreach ($listaPartidas as $partidas) {
            foreach ($partidas as $key => $partida) {
                $boxPartidas[$key]['bandeira_mandante'] = $listaClubes["clubes"][$partida["clube_casa_id"]]['escudos']['30x30'];
                $boxPartidas[$key]['bandeira_visitante'] = $listaClubes["clubes"][$partida["clube_visitante_id"]]['escudos']['30x30'];
                $boxPartidas[$key]['nome_mandante'] = $listaClubes["clubes"][$partida["clube_casa_id"]]['nome'];
                $boxPartidas[$key]['nome_visitante'] = $listaClubes["clubes"][$partida["clube_visitante_id"]]['nome'];
                $boxPartidas[$key]['abv_nome_mandante'] = $listaClubes["clubes"][$partida["clube_casa_id"]]['abreviacao'];
                $boxPartidas[$key]['abv_nome_visitante'] = $listaClubes["clubes"][$partida["clube_visitante_id"]]['abreviacao'];
                $boxPartidas[$key]['clube_mandante_posicao'] = $partida["clube_casa_posicao"];
                $boxPartidas[$key]['aproveitamento_mandante'] = $partida["aproveitamento_mandante"];
                $boxPartidas[$key]['aproveitamento_visitante'] = $partida["aproveitamento_visitante"];
                $boxPartidas[$key]['clube_visitante_posicao'] = $partida["clube_visitante_posicao"];
                $dataPartida = explode('-', explode(' ', $partida["partida_data"])[0]);
                $boxPartidas[$key]['partida_data'] = $dataPartida[2] . '/' . $dataPartida[1] . '/' . $dataPartida[0] . ' ' . explode(' ', $partida["partida_data"])[1];
                $boxPartidas[$key]['local'] = $partida["local"];
                $boxPartidas[$key]['valida'] = $partida["valida"];
            }
        }
        return $boxPartidas;
    }

}
