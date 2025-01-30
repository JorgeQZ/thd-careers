<?php
/*
Template Name: Mi
*/

get_header();

?>

<div class="general">

    <div class="seccion">

        <p class="titulo">DATOS GENERALES</p>

        <div class="inputs">
            <div>
                <div>
                    <label for="">Nombre(s)</label>
                </div>
                <input type="text">
            </div>

            <div>
                <div>
                    <label for="">Apellido Paterno</label>
                </div>
                <input type="text">
            </div>

            <div>
                <div>
                    <label for="">Apellido Materno</label>
                </div>
                <input type="text">
            </div>

            <div>
                <div>
                    <label for="">Correo</label>
                </div>
                <input type="text">
            </div>

            <div>
                <div>
                    <label for="">Fecha de nacimiento</label>
                </div>
                <div class="div-icon">
                    <input type="date">
                    <span class="date-icon"></span>
                </div>
            </div>

            <div>
                <div>
                    <label for="">Nacionalidad</label>
                </div>
                <input type="text">
            </div>

            <div>
                <div>
                    <label for="">Elige tu grado de escolaridad:</label>
                </div>
                <select id="" name="">
                    <option value="">Escolaridad - Grado de estudios</option>
                    <option value="">Primaria</option>
                    <option value="">Secundaria</option>
                    <option value="">Preparatoria</option>
                    <option value="">Carrera técnica</option>
                    <option value="">Licenciatura</option>
                    <option value="">Posgrado</option>
                    <option value="">Maestría</option>
                    <option value="">Doctorado</option>
                </select>
            </div>

            <div>
                <div>
                    <label for="">Estado Civil</label>
                </div>
                <select id="" name="">
                    <option value="">Seleccione su estado civil</option>
                    <option value="">Soltero/a</option>
                    <option value="">Casado/a</option>
                    <option value="">Divorciado/a</option>
                    <option value="">Viudo/a</option>
                </select>
            </div>

            <div>
                <div>
                    <label for="">¿En qué Centro de Trabajo estás interesado(a)?</label>
                </div>
                <select id="" name="">
                    <option value="">Seleccione un centro de trabajo</option>
                    <option value="">Oficinas de apoyo a tiendas - Operaciones</option>
                    <option value="">Oficinas de apoyo a tiendas - Recursos Humanos</option>
                    <option value="">Oficinas de apoyo a tiendas - Finanzas</option>
                    <option value="">Tiendas</option>
                    <option value="">Centros de Distribución</option>
                </select>
            </div>
        </div>

    </div>

</div>

<?php  get_footer(); ?>