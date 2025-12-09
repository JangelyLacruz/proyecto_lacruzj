<?php

namespace src\config\inc;
use src\config\connect\conexion;

class componentesModelo extends conexion
{
    public function listaDataTable($instrucciones)
    {
        $encabezado=$instrucciones['encabezado'];
        $tituloBtnReg=$instrucciones['tituloBtnReg'];

        $listaDataTable = '
            <div class="main-content" id="mainContent">
                <dvi class="container-fluid py-4">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h2 class="mb-0">'.$encabezado.'</h2>
                        </div>
                        <div class="col-md-6 text-end">
                            <button type="button" class="p-btn" data-bs-toggle="modal" data-bs-target=".modalRegistrar">
                                <i class="fas fa-plus-circle"></i> '.$tituloBtnReg.'
                            </button>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover tabla-ajax">
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        ';
        return $listaDataTable;
    }
}
