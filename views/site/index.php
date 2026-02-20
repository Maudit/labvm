<?php

/** @var yii\web\View $this */

$this->title = 'LabVM - Home';
?>
<div class="site-index">
    <div class="jumbotron text-center bg-transparent mt-5 mb-5">
        <h1 class="display-4">Yuppieeeee!</h1>
        <p class="lead">Hai effettuato l'accesso a LabVM.</p>
        <p>Seleziona una delle funzioni elencate qua sotto per iniziare</p>
    </div>
    <div class="body-content">
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <div class="col">
                <div class="card h-100">
                    <img src="/img/inventory.jpg" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h2 class="card-title">Inventario</h2>

                    </div>
                    <div class="card-footer">
                        <a class="btn btn-secondary" href="/compound">Vai all'inventario</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100">
                    <img src="/img/experiment.jpg" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h2 class="card-title">Esperimenti</h2>
                    </div>
                    <div class="card-footer">
                        <a class="btn btn-secondary" href="/experiment">Vai agli Esperimenti</a>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card h-100">
                    <img src="/img/help.jpg" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h2 class="card-title">Guida d'uso</h2>
                    </div>
                    <div class="card-footer">
                    <a class="btn btn-secondary disabled" href="#">Leggi la guida</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>