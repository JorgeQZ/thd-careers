<?php
/*
Template Name: Notificaciones
*/

get_header();
$user_id = get_current_user_id();
?>

<!-- Banner con el titulo de la página -->
<div class="banner">
    <div class="container">
        <div class="title-cont">
           Notificaciones
        </div>
    </div>
</div><!-- Banner con el titulo de la página -->

<!-- Contenido de la página -->
<main>
    <div class="not-cont">
        <div class="container">

            <ul class="list">
                <?php
                $notif = get_user_notifications($user_id); // Obtener notificaciones
                foreach($notif as $item):
                    $vac_ID = $item->vacancy_id;
                    $link = get_the_permalink($vac_ID);
                    $message = $item->message;
                    $estado = $item->postulation_status;
                    $status = $item->status;
                    ?>
                    <li class="item <?php echo $item->status === 'seen' ? 'seen' : ''; ?>" id="notif-<?php echo $item->id; ?>">
                        <div class="img">
                            <img src="<?php echo get_template_directory_uri() . '/imgs/logo-thd.jpg' ?>" alt="">
                        </div>
                        <div class="desc">
                            <!-- Mostrar título real de la vacante -->
                            <a href="<?php echo $link;?>"><?php echo get_the_title($vac_ID); ?></a>
                            <div class="icon-cont">
                                <!-- Mostrar el estado real de la notificación -->
                                <div class="text">Estado: <span><?php echo $estado; ?></span></div>
                            </div>

                            <div class="text mensaje">
                                <br>
                                Mensaje:
                                <?php echo $message;?>
                            </div>
                        </div>
                        <div class="plus" data-notif-id="<?php echo $item->id; ?>">+</div>
                    </li>
                    <?php
                endforeach;
                ?>
            </ul>

        </div>
    </div><!-- Contenido de la página -->
</main>

<?php get_footer(); ?>

<!-- Agregar el script de JavaScript -->
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {
        const plusButtons = document.querySelectorAll('.plus');

        plusButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const notifId = this.getAttribute('data-notif-id');

                // Realizar solicitud AJAX para marcar la notificación como vista
                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'mark_notification_as_seen',
                        notif_id: notifId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Cambiar el estado visual del ícono (puedes personalizarlo)
                        document.getElementById('notif-' + notifId).classList.add('seen');
                    } else {
                        alert('Hubo un error al marcar la notificación como vista.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Hubo un problema con la solicitud.');
                });
            });
        });
    });
</script>
