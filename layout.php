<link rel="stylesheet" href="<?= plugins_url() ?>/synclogic/css/bootstrap.min.css">
<script src="<?= plugins_url() ?>/synclogic/js/jquery-3.5.1.min.js"></script>
<script src="<?= plugins_url() ?>/synclogic/js/bootstrap.min.js"></script>

<div id="content" class="py-5">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <img style="float: right;" src="https://dev.shocklogic.com/v2/img/logo-v2.png">
                <h1>Shocklogic connection dashboard</h1>

                <form method="post" action="options.php">
                    <?php wp_nonce_field('update-options'); ?>

                    <div class="form-group">
                        <label for="synclogic_data">API Key:</label>
                        <input class="form-control" name="synclogic_data" type="text" id="synclogic_data" value="<?= get_option('synclogic_data'); ?>" />
                        <strong><em>* The API Key is provided by Shocklogic and It's unique for represent your event and respect the integrity of your information</em></strong>
                    </div>

                    <input type="hidden" name="action" value="update" />
                    <input type="hidden" name="page_options" value="synclogic_data" />

                    <input class="btn" style="background-color: #FF6E0D; color: #fff;" onclick="sync()" type="submit" value="<?php _e('Synchronize') ?>" />
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function spinner() {
        var expires = new Date();
        expires.setTime(expires.getTime() + ('1' * 24 * 60 * 60 * 1000));
        document.cookie = 'flag' + '=' + '1' + ';expires=' + expires.toUTCString();
        var spinner = "<div id='loading' class='spinner-border text-primary' role='status'></div>";
        $("#wpbody").css("opacity", "0.3");
        $("body").append(spinner);
        $("#loading").css("width", "60px");
        $("#loading").css("height", "60px");
        $("#loading").css("position", "absolute");
        $("#loading").css("top", "50%");
        $("#loading").css("left", "50%");
    }

    async function sync() {
        response = await fetch()
    }
</script>