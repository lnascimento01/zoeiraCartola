<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Unirest;
use function array_sort;
use function Symfony\Component\Debug\header;
use function view;

class IndexController extends Controller {

    private $req;
    private $tableMes;
    private $status;
    private $apostadores;
    private $mesAtual;

    public function index(Request $request) {
        $this->req = $request->api;

        $status = $this->consulta('https://api.cartolafc.globo.com/mercado/status');
        $apostadores = DB::table('apostadores')->leftJoin('cotas_pg', 'apostadores.id', '=', 'cotas_pg.id_apostador')
                        ->select('apostadores.*', DB::raw('SUM(cotas_pg.qtd_cotas) as cotas_pagas'))
                        ->where('apostadores.status', '=', 1)
                        ->orderBy('apostadores.id', 'asc')->groupBy('apostadores.id')->get()->toArray();

        $this->status = $status;
        $this->apostadores = $apostadores;

        switch (date("m")) {
            case "01": $this->mesAtual = "Janeiro";
                break;
            case "02": $this->mesAtual = "Fevereiro";
                break;
            case "03": $this->mesAtual = "Março";
                break;
            case "04": $this->mesAtual = "Abril";
                break;
            case "05": $this->mesAtual = "Maio";
                break;
            case "06": $this->mesAtual = "Jun/Jul";
                break;
            case "07": $this->mesAtual = "Jun/Jul";
                break;
            case "08": $this->mesAtual = "Agosto";
                break;
            case "09": $this->mesAtual = "Setembro";
                break;
            case "10": $this->mesAtual = "Outubro";
                break;
            case "11": $this->mesAtual = "Novembro";
                break;
            case "12": $this->mesAtual = "Dezembro";
                break;
        }

        if ($request->api == '0') {
            $login = $this->autenticar($request->login, $request->pass);
            return $login;
        } else if ($request->api == '1') {

            $tabelaDevido = $this->tabelaDevido($apostadores, $status);

            return json_encode($tabelaDevido);
        } else if ($request->api == '2') {
            $time = $request->slug;
            $plantel = $this->parcial($status, $time);

            return json_encode($plantel);
        } else if ($request->api == '3') {
//            $body = json_encode(array(
//                "esquema" => 3,
//                "atleta" => [
//                    84709,
//                    80692,
//                    72294,
//                    83433,
//                    51772,
//                    92180,
//                    78435,
//                    69138,
//                    50856,
//                    72491,
//                    91508,
//                    87863
//                ],
//                "capitao_id" => 84709))
//            ;
            $_SESSION['X-GLB-Token'] = $request->token;
            $logado = $this->consultaAuthApiCartola('https://api.cartolafc.globo.com/auth/time', $request->token);

            return json_encode($logado);
        } else if ($request->api == '4') {
            $_SESSION['X-GLB-Token'] = $request->token;
            $logado = $this->consultaAuthApiCartola('https://api.cartolafc.globo.com/auth/ligas', $request->token);

            $logadoOrdenado = array_values(array_sort($logado->body->ligas, function ($value) {
                        return $value->tipo;
                    }));

            return json_encode($logadoOrdenado);
        } else if ($request->api == '5') {
            $return = array(
                "RB" => array(
                    "acao" => "Roubada de bola",
                    "pontos" => 1.7
                ),
                "FC" => array(
                    "acao" => "Falta cometida",
                    "pontos" => -0.5
                ),
                "GC" => array(
                    "acao" => "Gol contra",
                    "pontos" => -6.0
                ),
                "CA" => array(
                    "acao" => "Cartão amarelo",
                    "pontos" => -2.0
                ),
                "CV" => array(
                    "acao" => "Cartão vermelho",
                    "pontos" => -5.0
                ),
                "FS" => array(
                    "acao" => "Falta sofrida",
                    "pontos" => 0.5
                ),
                "PE" => array(
                    "acao" => "Passe errado",
                    "pontos" => -0.3
                ),
                "FT" => array(
                    "acao" => "Finalização na trave",
                    "pontos" => 3.5
                ),
                "FD" => array(
                    "acao" => "Finalização defendida",
                    "pontos" => 1.0
                ),
                "FF" => array(
                    "acao" => "Finalização para fora",
                    "pontos" => 0.7
                ),
                "G" => array(
                    "acao" => "Gols",
                    "pontos" => 8.0
                ),
                "I" => array(
                    "acao" => "Impedimento",
                    "pontos" => -0.5
                ),
                "PP" => array(
                    "acao" => "Penalti perdido",
                    "pontos" => -3.5
                ),
                "A" => array(
                    "acao" => "Assistência",
                    "pontos" => 5.0
                ),
                "SG" => array(
                    "acao" => "Jogo sem sofrer gol",
                    "pontos" => 5.0
                ),
                "DD" => array(
                    "acao" => "Defesa difícil",
                    "pontos" => 3.0
                ),
                "DP" => array(
                    "acao" => "Defesa de penalti",
                    "pontos" => 7.0
                ),
                "GS" => array(
                    "acao" => "Gol sofrido",
                    "pontos" => -2.0
                )
            );

            return json_encode($return);
        } else {
//            $tabelaDevido = $this->tabelaDevido($apostadores, $status);
            $tabelaDevido = $apostadores;

            $boxPartidas = $this->boxPartidas();

//            $consultaGeral = $this->tabelaGeral($apostadores, $status);

            $tabelaVencedorMes = $this->tabelaVencedorMes($apostadores, $status);

//            $tabelaGeral = array_values(array_sort($consultaGeral['tabela_geral'], function ($value) {
//                        return $value['pontuacaoGeral'];
//                    }));

            $tabelaMensal = array_values(array_sort($this->tableMes, function ($value) {
                        return $value['pontuacaoMes'];
                    }));

            return view('index', ['boxPartidas' => $boxPartidas, 'tabelaMensal' => $tabelaMensal, 'status' => $status, 'tabelaMes' => $tabelaVencedorMes,
                'tabelaDevido' => $tabelaDevido]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function consulta($url) {
        ini_set('max_execution_time', 380);
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

    public function consultaAuthApiCartola($url, $token) {
        $response = Unirest\Request::get($url, array(
                    "X-GLB-Token" => $token,
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
                'email' => $login,
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

    public function tabelaGeral($apostadores, $status) {
        $rodadaAtual = $status['rodada_atual'];
        $tabelaGeral = [];
        $vencedoresRodadas = [];

        foreach ($apostadores as $apostador) {

            $rodada = 1;
            $scoutApostador = [];
            $totalPontos = 0;
            $pontosRodada = [];

            while ($rodada < $rodadaAtual) {
                $scoutRodada = $this->consulta('https://api.cartolafc.globo.com/time/slug/' . $apostador->slug_time . '/' . $rodada);
                $scoutApostador[$rodadaAtual] = $scoutRodada;
                $totalPontos = $totalPontos + $scoutRodada['pontos'];
//                $vencedoresRodadas[$apostador->id] = array('time' => $scoutRodada['time'], 'pontos_rodada' => array());

                array_push($pontosRodada, array($rodada => $scoutRodada['pontos']));
                $rodada++;
            }
            $vencedoresRodadas[$apostador->id]['pontos_rodada'] = $pontosRodada;
            $scoutApostador['pontuacaoGeral'] = $totalPontos;
            $tabelaGeral[$apostador->id] = $scoutApostador;
        }

        $mes = [];

        $rodada = 1;
        while ($rodada < $rodadaAtual) {
            $mes[$rodada] = [];
            foreach ($vencedoresRodadas as $key => $vencedor) {
                $chave = $key - 1;

                array_push($mes[$rodada], array('id_apostador' => $key, 'apostador' => $apostadores[$chave]->nome, 'pontos' => $vencedor['pontos_rodada'][$rodada - 1][$rodada]));
            }
            $rodada++;
        }

        $mesOrdenado = [];
        foreach ($mes as $idMes => $rodada) {
            $mesOrdenado[$idMes] = array_values(array_sort($rodada, function ($value) {
                        return $value['pontos'];
                    }));
        }

        $return = array('tabela_geral' => $tabelaGeral, 'vencedor_mes_mes' => $mesOrdenado);

        return $return;
    }

    public function tabelaMensal($apostadores, $status) {
        $mesAtual = 1;

        if ($status['rodada_atual'] >= 9) {
            $mesAtual = date('Y-m', strtotime('-1 months', strtotime(date('Y-m-d'))));
        } else {
            $mesAtual = date('Y-m');
        }

        $rodadasInfo = DB::table('rodadas')
                        ->select('rodada_id')
                        ->where('inicio', 'like', $mesAtual . '%')
                        ->orWhere('inicio', 'like', date('Y-m') . '%')->orderBy('id', 'asc')->get()->toArray();

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

            $parcialPontos = $this->parcial($status, $apostador->slug_time);

            $scoutApostador['pontuacaoMensal'] = $totalPontos + $parcialPontos['total'];
            $tabelaMensal[$apostador->id] = $scoutApostador;
        }

        return $tabelaMensal;
    }

    public function tabelaVencedorMes() {
//        $vMes = DB::table('vencedores_mes')
//                ->select('*')
//                ->get()
//                ->toArray();
        $qry = 'select mes_vigente, id_apostador, SUM(pontos) as pontos from pontos_rodada pr
                INNER JOIN rodadas r
                ON r.rodada_id = pr.id_rodada
                group by mes_vigente, id_apostador
                order by id_rodada, pontos DESC';
        $vMes = DB::select($qry);

        $vencedoresMes = [];
        $vencedoresMesAtual = [];


        foreach ($vMes as $mes) {
            if (isset($vencedoresMes[$mes->mes_vigente])) {
                if ($mes->pontos > $vencedoresMes[$mes->mes_vigente]['pontuacaoMes']) {
                    $vencedoresMes[$mes->mes_vigente] = array('nome' => $this->apostadores[$mes->id_apostador - 1]->nome, 'escudo' => $this->apostadores[$mes->id_apostador - 1]->escudo,
                        'foto' => $this->apostadores[$mes->id_apostador - 1]->foto, 'pontuacaoMes' => $mes->pontos);
                }
            } else {
                $vencedoresMes[$mes->mes_vigente] = array('nome' => $this->apostadores[$mes->id_apostador - 1]->nome, 'escudo' => $this->apostadores[$mes->id_apostador - 1]->escudo,
                    'foto' => $this->apostadores[$mes->id_apostador - 1]->foto, 'pontuacaoMes' => $mes->pontos);
            }
            if ($mes->mes_vigente == $this->mesAtual) {
                $vencedoresMesAtual[$mes->id_apostador] = array('nome' => $this->apostadores[$mes->id_apostador - 1]->nome, 'escudo' => $this->apostadores[$mes->id_apostador - 1]->escudo,
                    'foto' => $this->apostadores[$mes->id_apostador - 1]->foto, 'pontuacaoMes' => $mes->pontos);
            }
        }

        $this->tableMes = $vencedoresMesAtual;

        return $vencedoresMes;
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

    public function parcial($status, $timeApostador) {
        $rodadaAtual = $status['status_mercado'] == 1 ? $status['rodada_atual'] - 1 : $status['rodada_atual'];
        $rodadasInfo = DB::table('rodadas')
                        ->select('*')
                        ->where('rodada_id', '=', $rodadaAtual)->orderBy('id', 'asc')->get()->toArray();

        $time = $this->consulta('https://api.cartolafc.globo.com/time/slug/' . $timeApostador . '/' . $rodadaAtual);

        $pontuacoes = $this->consulta('https://api.cartolafc.globo.com/atletas/pontuados');

        $dadosScout = [];
        $dadosScout['GS'] = 'Gols sofridos';
        $dadosScout['CA'] = 'Cartões Amarelos';
        $dadosScout['CV'] = 'Cartões Vermelhos';
        $dadosScout['FC'] = 'Faltas Cometidas';
        $dadosScout['FS'] = 'Faltas Sofridas';
        $dadosScout['GC'] = 'Gols Contra';
        $dadosScout['RB'] = 'Bolas Roubadas';
        $dadosScout['DD'] = 'Defesas Difíceis';
        $dadosScout['DP'] = 'Defesas Penâlti';
        $dadosScout['SG'] = 'Jogos S/ Gol';
        $dadosScout['PE'] = 'Passes Errados';
        $dadosScout['PP'] = 'Penâlti Perdido';
        $dadosScout['I'] = 'Impedimento';
        $dadosScout['FF'] = 'Finalização Fora';
        $dadosScout['FT'] = 'Finalização Trave';
        $dadosScout['FD'] = 'Finalização Defendida';
        $dadosScout['A'] = 'Assistência';
        $dadosScout['G'] = 'Gols';

        $scout = [];
        $scoutTotal = 0;
        foreach ($time['atletas'] as $atleta) {
            $idAtleta = $atleta['atleta_id'];
            $idClube = $atleta['clube_id'];
            $idPosicao = $atleta['posicao_id'];
            $pontosAtual = ($status['status_mercado'] != 1 ? !array_key_exists($idAtleta, $pontuacoes['atletas']) ? 0 : $pontuacoes['atletas'][$idAtleta]['pontuacao'] : $pontuacoes['atletas'][$idAtleta]['pontuacao']);
            if ($status['status_mercado'] != 1) {
                if ($status['status_mercado'] != 1) {
                    if (!array_key_exists($idAtleta, $pontuacoes['atletas'])) {
//                $scoutAtual = ($status['status_mercado'] != 1 ? !array_key_exists($idAtleta, $pontuacoes['atletas']) ? 0 : $pontuacoes['atletas'][$idAtleta]['scout'] : $pontuacoes['atletas'][$idAtleta]['scout']);
                        $scoutAtual = [];
                        $scoutAtual[] = 'Sem scout';
                    } else {
                        $scoutAtual = [];
                        foreach ($pontuacoes['atletas'][$idAtleta]['scout'] as $abrv => $fundamento) {
                            $scoutAtual[$dadosScout[$abrv]] = $fundamento;
                        }
                    }
                } else {
                    $scoutAtual = [];
                    foreach ($pontuacoes['atletas'][$idAtleta]['scout'] as $abrv => $fundamento) {
                        $scoutAtual[$dadosScout[$abrv]] = $fundamento;
                    }
                }
            } else {
                $scoutAtual = [];
                foreach ($atleta['scout'] as $abrv => $fundamento) {
                    $scoutAtual[$dadosScout[$abrv]] = $fundamento;
                }
            }
            $pontos = $status['status_mercado'] == 1 || in_array($idClube, explode(',', $rodadasInfo[0]->ausencias)) ? $atleta['pontos_num'] : $pontosAtual;

            $scout[] = array('atleta_id' => $idAtleta, 'apelido' => $atleta['apelido'],
                'pontos' => $idAtleta == $time['capitao_id'] ? $pontos * 2 : $pontos, 'scout' => $scoutAtual, 'capitao' => $idAtleta == $time['capitao_id'] ? 'closed-caption' : '',
                'foto' => str_replace('FORMATO', '140x140', $atleta['foto']), 'posicao_id' => $atleta['posicao_id'],
                'posicao_atleta' => $time['posicoes'][$idPosicao]['nome'], 'nome_clube' => $idClube == 1 ? 'Tra' : $time['clubes'][$idClube]['nome'],
                'posicao_clube' => $idClube == 1 ? 'Tra' : $time['clubes'][$idClube]['posicao'], 'escudo_clube' => $idClube == 1 ? '' : $time['clubes'][$idClube]['escudos']['60x60']);
            $scoutTotal += $idAtleta == $time['capitao_id'] ? $pontos * 2 : $pontos;
        }

        $scoutOrdenado = array_values(array_sort_asc($scout, function ($value) {
                    return $value['posicao_id'];
                }));

        $scoutJson = [];
        $contador = 0;
        foreach ($scoutOrdenado as $scout) {
            $scoutJson[] = array('name' => $scout['apelido'] . ' - ' . $scout['nome_clube'] . ' ' . $scout['posicao_clube'] . 'º', 'foto' => $scout['foto'], 'header' => ($contador % 2) == 0 ? true : false, 'index' => $contador);
            $contador++;
            $scoutJson[] = array('name' => ' Posição: ' . $scout['posicao_atleta'] . ' - Pontos: ' . $scout['pontos'], 'header' => ($contador % 2) == 0 ? true : false, 'scout' => $scout['scout']);
//            $scoutJson[] = array('name' => $scout['apelido'] . ' - Clube:' . $scout['nome_clube'] . ' ' . $scout['posicao_clube'],
//                'content' => ' Posição:' . $scout['posicao_atleta'] . ' - Pontos:' . $scout['pontos']);
            $contador++;
        }
//          const dataArray = [{title: nome.apelido + " - Clube: " + nome.nome_clube +" "+  nome.posicao_clube+"º", 
//	  content: "Posição: " + nome.posicao_atleta + 
//      " - Pontos: "+ nome.pontos }];

        $return = array('scout' => $scoutOrdenado, 'total' => number_format((float) $scoutTotal, 2, '.', ''));

        return $return;
    }

    public function parcialAccord($status) {
        $rodadaAtual = $status['status_mercado'] == 1 ? $status['rodada_atual'] - 1 : $status['rodada_atual'];
        $rodadasInfo = DB::table('rodadas')
                        ->select('*')
                        ->where('rodada_id', '=', $rodadaAtual)->orderBy('id', 'asc')->get()->toArray();

        $time = $this->consulta('https://api.cartolafc.globo.com/time/slug/azzurras-f-c/' . $rodadaAtual);

        $pontuacoes = $this->consulta('https://api.cartolafc.globo.com/atletas/pontuados');

        $scout = [];
        $scoutTotal = 0;
        foreach ($time['atletas'] as $atleta) {
            $idAtleta = $atleta['atleta_id'];
            $idClube = $atleta['clube_id'];
            $idPosicao = $atleta['posicao_id'];
            $pontosAtual = !array_key_exists($idAtleta, $pontuacoes['atletas']) ? 0 : $pontuacoes['atletas'][$idAtleta]['pontuacao'];
            $scoutAtual = !array_key_exists($idAtleta, $pontuacoes['atletas']) ? 0 : $pontuacoes['atletas'][$idAtleta]['scout'];

            $pontos = $status['status_mercado'] == 1 || in_array($idClube, explode(',', $rodadasInfo[0]->ausencias)) ? $atleta['pontos_num'] : $pontosAtual;

            $scout[] = array('atleta_id' => $idAtleta, 'apelido' => $atleta['apelido'],
                'pontos' => $idAtleta == $time['capitao_id'] ? $pontos * 2 : $pontos, 'scout' => $status['status_mercado'] == 1 ? $atleta['scout'] : $scoutAtual,
                'foto' => str_replace('FORMATO', '140x140', $atleta['foto']), 'posicao_id' => $atleta['posicao_id'],
                'posicao_atleta' => $time['posicoes'][$idPosicao]['nome'], 'nome_clube' => $idClube == 1 ? 'Tra' : $time['clubes'][$idClube]['nome'],
                'posicao_clube' => $idClube == 1 ? 'Tra' : $time['clubes'][$idClube]['posicao'], 'escudo_clube' => $idClube == 1 ? '' : $time['clubes'][$idClube]['escudos']['60x60']);
            $scoutTotal += $idAtleta == $time['capitao_id'] ? $pontos * 2 : $pontos;
        }

        $scoutOrdenado = array_values(array_sort_asc($scout, function ($value) {
                    return $value['posicao_id'];
                }));

        $layoutAccordion = [];

        foreach ($scoutOrdenado as $scout) {
            $layoutAccordion[$scout['atleta_id']] = array('title' => '"<Thumbnail square large source=' . $scout['foto'] . ' />' . $scout['foto'] . ' - Clube: ' . $scout['nome_clube'] . $scout['posicao_clube'] . '"',
                'content' => '"Posição: ' . $scout['posicao_atleta'] . ' - Pontos: ' . $scout['pontos']);
        }

        $return = array('scout' => $layoutAccordion, 'total' => $scoutTotal);

        return $return;
    }

    public function updateApostas() {
        $frases = [];
        $status = $this->consulta('https://api.cartolafc.globo.com/mercado/status');
        $apostadores = DB::table('apostadores')->leftJoin('cotas_pg', 'apostadores.id', '=', 'cotas_pg.id_apostador')
                        ->select('apostadores.*', DB::raw('SUM(cotas_pg.qtd_cotas) as cotas_pagas'))
                        ->where('apostadores.status', '=', 1)
                        ->orderBy('apostadores.id', 'asc')->groupBy('apostadores.id')->get()->toArray();

        $this->status = $status;
        $this->apostadores = $apostadores;
        $this->updateRodadas();
        
        $frases[] = array(1 => 'Tecnologia não ganha rodada!', 2 => 'Escala mas não chega a lugar algum!', 3 => 'O perseguido!', 4 => 'O perdedor silencioso!',
            5 => 'Late demais, não ganha nada!', 6 => 'O Lenny do cartola!', 7 => 'Falsa promessa!');

        $rodada = 1;
        $apostasRodaada = [];

        while ($rodada < $status['rodada_atual']) {
            $scoutRodada = [];
            foreach ($apostadores as $apostador) {

                $retorno = $this->consulta('https://api.cartolafc.globo.com/time/slug/' . $apostador->slug_time . '/' . $rodada);
                $scoutRodada[$apostador->id] = $retorno['time'];
                $scoutRodada[$apostador->id]['pontos'] = $retorno['pontos'];
                $scoutRodada[$apostador->id]['id_apostador'] = $apostador->id;
            }
            $times = array_values(array_sort($scoutRodada, function ($value) {
                        return $value['pontos'];
                    }));

            foreach ($times as $id => $time) {
                if ($id != 0) {
                    $apostasRodaada[$time['id_apostador']]['time'] = $time;
                    if (!isset($apostasRodaada[$time['id_apostador']]['cota'])) {
//                        $apostasRodaada[$time['time']['id_apostador']]['cota'] = !isset($apostasRodaada[$time['time']['id_apostador']]['cota']) ? 0 : $apostasRodaada[$time['time']['id_apostador']]['cota'] + 1;
                        $apostasRodaada[$time['id_apostador']]['cota'] = 1;
                    } else {
                        $apostasRodaada[$time['id_apostador']]['cota'] ++;
                    }
                }
            }
            $rodada++;
        }

        foreach ($apostadores as $apostador) {
            $apostasRodaada[$apostador->id]['cota'] -= $apostador->cotas_pagas;
            $apostasRodaada[$apostador->id]['frase'] = $frases[0][$apostador->id];

            DB::table('apostadores')
                    ->where('id', $apostador->id)
                    ->update(['nome' => $apostasRodaada[$apostador->id]['time']['nome_cartola'], 'foto' => $apostasRodaada[$apostador->id]['time']['foto_perfil'],
                        'escudo' => $apostasRodaada[$apostador->id]['time']['url_escudo_svg'], 'frase' => $frases[0][$apostador->id], 'devido' => $apostasRodaada[$apostador->id]['cota']]);
        }
//
//        $apostasOrdenadas = array_values(array_sort($apostasRodaada, function ($value) {
//                    return $value['cota'];
//                }));
//        $apostasOrdenadas = array_values(array_sort($apostadores, function ($value) {
//                    return $value->cotas_pagas;
//                }));
//        foreach ($apostasOrdenadas as $aposta) {
//            $idApostador = $aposta['time']['id_apostador'];
//
//
//            $return[] = array('id_apostador' => $idApostador, 'nome' => $aposta['time']['nome'], 'cartola' => $aposta['time']['nome_cartola'],
//                'cotas' => $aposta['cota'], 'escudo' => $aposta['time']['url_escudo_png'], 'url_escudo_svg' => $aposta['time']['url_escudo_svg'],
//                'frase' => $aposta['frase']);
//        }
        return 'Apostas atualizadas';
    }

    public function updateRodadas() {

        $status = $this->status;
        $apostadores = $this->apostadores;
        $rodadas = $this->consulta("https://api.cartolafc.globo.com/rodadas");

        foreach ($rodadas as $rodada) {
            DB::table('rodadas')
                    ->where('rodada_id', $rodada['rodada_id'])
                    ->update(['inicio' => explode(' ', $rodada['inicio'])[0], 'fim' => explode(' ', $rodada['fim'])[0]]);



            $rodadasInfo = array('Abril' => array(1, 2, 3), 'Maio' => array(4, 5, 6, 7, 8), 'Jun/Jul' => array(9, 10, 11, 12, 13, 14, 15, 16),
                'Agosto' => array(17, 18, 19, 20, 21), 'Setembro' => array(22, 23, 24, 25, 26, 27), 'Outubro' => array(28, 29, 30, 31), 'Novembro' => array(32, 33, 34, 35, 36, 37, 38));

            $tabelaDevido = [];

            foreach ($rodadasInfo as $key => $meses) {
                $devidoMes = [];

                foreach ($apostadores as $apostador) {
                    $totalPontos = 0;
                    $scoutApostador = [];
                    foreach ($meses as $rodada) {

                        if ($status['rodada_atual'] > $rodada) {
                            $scoutRodada = $this->consulta('https://api.cartolafc.globo.com/time/slug/' . $apostador->slug_time . '/' . $rodada);

                            $scoutApostador[$apostador->id] = $scoutRodada;
                            $totalPontos = $totalPontos + $scoutRodada['pontos'];

                            DB::table('pontos_rodada')
                                    ->where('id_apostador', $apostador->id)
                                    ->where('id_rodada', $rodada)
                                    ->update(['id_rodada' => $rodada, 'id_apostador' => $apostador->id, 'pontos' => $scoutRodada['pontos'], 'ativo' => 1]);
                        }
                        $devidoMes[$key][$apostador->id] = $scoutRodada;
                        $devidoMes[$key][$apostador->id]['pontuacaoMes'] = $totalPontos;
                    }

                    $tabelaDevido[$key] = array_values(array_sort($devidoMes[$key], function ($value) {
                                return $value['pontuacaoMes'];
                            }));
                }
            }
        }
    }

}
