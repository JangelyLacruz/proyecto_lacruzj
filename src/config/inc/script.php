<script src="/proyecto-lacruz-j/src/assets/js/plugins/popper.min.js"></script>
<script src="/proyecto-lacruz-j/src/assets/js/plugins/bootstrap.min.js"></script>
<script src="/proyecto-lacruz-j/src/assets/js/plugins/jquery-3.7.1.min.js"></script>
<script src="/proyecto-lacruz-j/src/assets/js/plugins/jquery.mask.min.js"></script>
<script src="/proyecto-lacruz-j/src/assets/js/plugins/select2.min.js"></script>
<script src="/proyecto-lacruz-j/src/assets/js/plugins/sweetalert2.all.min.js"></script>
<script src="/proyecto-lacruz-j/src/assets/js/plugins/jquery.dataTables.min.js"></script>
<script src="/proyecto-lacruz-j/src/assets/js/plugins/dataTables.bootstrap5.min.js"></script>


<?php
    $directorioJs = '/proyecto-lacruz-j/src/assets/js/modulos/';
    if (
        $_SESSION['vistaActual'] == 'login' || $_SESSION['vistaActual']==''
    ){
        $archivoModulo= $directorioJs.'usuarios.js';
    }else{
        $archivoModulo = $directorioJs.$_SESSION['vistaActual'].'.js';
    }
?>
<script type="module" src="<?php echo $archivoModulo ?>"></script>
</body>
</html>