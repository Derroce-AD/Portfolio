@extends('layouts.app')

@section('title', 'Portfolio')

@section('content')
    <section class="introIndex">
        <article class="leftSideIntro">
            <h1 class="introTitle">Portfolio</h1>
            <h3 class="h3-intro">Ici je vous présente mes projets ainsi que mon parcours et mes informations personnelles !</h3>
        </article>

        <article class="rightSideIntro">
            <img class="portrait" src="{{ asset('assets/pictures/portrait.jpeg') }}" alt="portrait">
            <a class="contactButton" href="{{ url('/contact') }}">Contacter</a>
        </article>
    </section>

    <section class="paragraph">
        <h3 class="medium-title">Mon parcours :</h3>
        <p class="center-paragraph">J'ai commencé par un bac en graphisme en alternance. 
        Ensuite, j'ai suivi une courte formation de 2 mois pour m'initier
        au code. Et actuellement, je suis en train de suivre une formation de
        développement web de 16 mois.</p>
    </section>

    <hr>

    <section class="paragraph">
        <h3 class="medium-title">Mon CV :</h3>
        <p class="CV-paragraph">Voici mon CV ainsi que mes coordonnées au bas de la page.</p>
        <img class="cv" src="{{ asset('assets/pictures/CV.jpg') }}" alt="">

        <div class="download-cv">
            <a href="{{ asset('assets/pictures/cv.jpg') }}" download="CV.jpg" class="btn-download">Télécharger mon CV</a>
        </div>
    </section>
@endsection
