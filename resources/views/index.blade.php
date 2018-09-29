@extends('home')
@section('content')
<link rel="stylesheet" type="text/css" href="{{ elixir('/css/index.css') }}">
<!-- Home Slider ==================================== -->
<section id="home" style="height: 500px!important;">
    <div id="home-carousel" class="carousel slide" data-interval="false">
        <ol class="carousel-indicators">
            <li data-target="#home-carousel" data-slide-to="0" class="active"></li>
            <li data-target="#home-carousel" data-slide-to="1"></li>
            <li data-target="#home-carousel" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner" style="height: 500px!important;">
            <div class="item active"  style="background-image: url('img/slider/bg2.jpg'); background-repeat: no-repeat; height: 500px!important;" >
                <div class="carousel-caption">
                    <div class="animated bounceInRight">
                        <h2>HELLO WORLD! <br>WE ARE KASPER,WE MAKE ART.</h2>
                        <p>Curabitur arcu erat, accumsan id imperdiet et, porttitor at sem. Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a. Curabitur aliquet quam. Accumsan id imperdiet et, porttitor at sem. Mauris blandit aliquet elit, eget tincidunt.</p>
                    </div>
                </div>
            </div>              

            <div class="item" style="background-image: url('img/slider/bg2.jpg'); height: 500px!important;">                
                <div class="carousel-caption">
                    <div class="animated bounceInDown">
                        <h2>HELLO WORLD! <br>WE ARE KASPER,WE MAKE ART.</h2>
                        <p>Curabitur arcu erat, accumsan id imperdiet et, porttitor at sem. Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a. Curabitur aliquet quam. Accumsan id imperdiet et, porttitor at sem. Mauris blandit aliquet elit, eget tincidunt.</p>
                    </div>
                </div>
            </div>

            <div class="item" style="background-image: url('img/slider/bg3.jpg'); height: 500px!important;">                 
                <div class="carousel-caption">
                    <div class="animated bounceInUp">
                        <h2>HELLO WORLD! <br>WE ARE KASPER,WE MAKE ART.</h2>
                        <p>Curabitur arcu erat, accumsan id imperdiet et, porttitor at sem. Mauris blandit aliquet elit, eget tincidunt nibh pulvinar a. Curabitur aliquet quam. Accumsan id imperdiet et, porttitor at sem. Mauris blandit aliquet elit, eget tincidunt.</p>
                    </div>
                </div>
            </div>
        </div>
        <!--/.carousel-inner-->
        <nav id="nav-arrows" class="nav-arrows hidden-xs hidden-sm visible-md visible-lg">
            <a class="sl-prev hidden-xs" href="#home-carousel" data-slide="prev" style="margin-top: -80px!important;">
                <i class="fa fa-angle-left fa-3x"></i>
            </a>
            <a class="sl-next" href="#home-carousel" data-slide="next" style="margin-top: -80px!important;">
                <i class="fa fa-angle-right fa-3x"></i>
            </a>
        </nav>
    </div>
</section>
<!-- End #home Slider ========================== -->
<div class="home-results">
    <?php $count = 1; ?>
    @foreach ($boxPartidas as $partidas)
    <?php $style = $count % 2 == 0 ? '#000000; color: #FFFFFF' : '#FFBE00; color: #000000'; ?>
    <div style="background-color: {{$style}};">
        <div class="form-group col-lg-12 text-center" style="min-height: 20px!important;">
            <div class="form-group col-lg-12 text-center" style="min-height: 20px!important;">
                <div class="col-lg-12">
                    <span class="text-center">{{$partidas['partida_data']}}</span>
                </div>
            </div>
            <div class="col-lg-4">
                <img src="{{$partidas['bandeira_mandante']}}">
                <span class="text-center">{{$partidas['abv_nome_mandante']}}</span>
            </div>
            <div class="col-lg-4 text-center">
                <i class="fa fa-close fa-3x danger"></i>
            </div>
            <div class="col-lg-4 text-center">
                <img src="{{$partidas['bandeira_visitante']}}">
                <span class="text-center">{{$partidas['abv_nome_visitante']}}</span>
            </div>
            <!--                            </div>
                                        <div class="form-group col-lg-12 text-center" style="min-height: 20px!important;">-->
            <div class="col-lg-12">
                <span class="text-center">{{$partidas['local']}}</span>
            </div>
        </div>
    </div>
    <?php $count++; ?>
    @endforeach
</div>
<div>
    <aside class="col-md-6" style="padding-left: 0px!important;">
        <!--Widget Ranking Start-->
        <div class="widget widget_ranking" style="border: 1px solid #c0c0c0;">
            <!--Heading 1 Start-->
            <h6 class="kf_hd1">
                <span>Ranking Mensal</span>
            </h6>
            <!--Heading 1 END-->
            <div class="kf_border" style="background-color: #FFFFFF;">
                <!--Table Wrap Start-->
                <div id="price">
                    <!--price tab-->
                    <div class="plan">
                        <div class="plan-inner">
                            <div class="entry-content">
                                <ul>
                                    <?php
                                    $posicaoMes = 1;
                                    $rodadaAtual = $status['rodada_atual'];
                                    ?>
                                    @foreach ($tabelaMensal as $key => $apostador)
                                    <li>
                                        <div class="row">
                                            <div class="col-lg-1">
                                                {{$posicaoMes}}º
                                            </div>
                                            <div class="col-lg-2">
                                                <img src="{{$apostador['escudo']}}" width="100%">
                                            </div>
                                            <div class="col-lg-4">
                                                <strong>{{$apostador['nome']}}</strong>
                                            </div>
                                            <div class="col-lg-2">
                                                <img src="{{$apostador['foto']}}" width="100%">
                                            </div>
                                            <div class="col-lg-2">
                                                {{number_format($apostador['pontuacaoMes'],2)}}
                                            </div>
                                        </div>
                                    </li>
                                    <?php $posicaoMes++ ?>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Table Wrap End-->
            </div>
        </div>
        <!--Widget Ranking End-->
    </aside>
    <aside class="col-md-6" style="padding-left: 0px!important;">
        <!--Widget Ranking Start-->
        <div class="widget widget_ranking" style="border: 1px solid #c0c0c0;">
            <!--Heading 1 Start-->
            <h6 class="kf_hd1">
                <span>Vencedores Mes</span>
            </h6>
            <!--Heading 1 END-->
            <div class="kf_border" style="background-color: #FFFFFF;">
                <!--Table Wrap Start-->
                <div id="price">
                    <!--price tab-->
                    <div class="plan">
                        <div class="plan-inner">
                            <div class="entry-content">
                                <ul>
                                    @foreach ($tabelaMes as $key => $vencedor)
                                    <li>
                                        <div class="row">
                                            <div class="col-lg-1">
                                                {{$key}}
                                            </div>
                                            <div class="col-lg-2">
                                                <img src="{{$vencedor['pontuacaoMes'] > 0 ? $vencedor['escudo']:'https://icon-icons.com/icons2/1239/PNG/512/shield_83973.png'}}" width="100%">
                                            </div>
                                            <div class="col-lg-4">
                                                <strong>{{$vencedor['pontuacaoMes'] > 0 ? $vencedor['nome']:'Aguardando'}}</strong>
                                            </div>
                                            <div class="col-lg-2">
                                                <img src="{{$vencedor['pontuacaoMes'] > 0 ? $vencedor['foto']:'https://img.freepik.com/free-icon/user_318-134392.jpg?size=338c&ext=jpg'}}" width="100%">
                                            </div>
                                            <div class="col-lg-2">
                                                {{number_format($vencedor['pontuacaoMes'],2)}}
                                            </div>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Table Wrap End-->
            </div>
        </div>
        <!--Widget Ranking End-->
    </aside>
    <aside class="col-lg-12" style="padding-left: 0px!important;">
        <!--Widget Ranking Start-->
        <div class="widget widget_ranking" style="border: 1px solid #c0c0c0;">
            <!--Heading 1 Start-->
            <h6 class="kf_hd1">
                <span>Saldo de Débitos</span>
            </h6>
            <!--Heading 1 END-->
            <div class="kf_border" style="background-color: #FFFFFF;">
                <!--Table Wrap Start-->
                <div id="price">
                    <!--price tab-->
                    <div class="plan">
                        <div class="plan-inner">
                            <div class="entry-content">
                                <ul>
                                    @foreach ($tabelaDevido as $key => $cotas)
                                    <li>
                                        <div class="row">
                                            <div class="col-lg-2">
                                                <img src="{{$cotas->escudo}}" width="70%">
                                            </div>
                                            <div class="col-lg-4">
                                                <strong>{{$cotas->nome}}</strong>
                                            </div>
                                            <div class="col-lg-2">
                                                <img src="{{$cotas->foto}}" width="70%">
                                            </div>
                                            <div class="col-lg-2">
                                                R$ {{number_format(($cotas->devido) * 3,2)}}
                                            </div>
                                        </div>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Table Wrap End-->
            </div>
        </div>
        <!--Widget Ranking End-->
    </aside>
</div>
@stop