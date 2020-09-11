<?php
    $vehicle_type_list  =   get_terms(['taxonomy'=>'vehicle-category']);

?>

<style>
    .form-group{
        margin-bottom: 20px;
    }
</style>
<div class="container">

    <form action="" method="post">
        <div class="form-group">
            <label class="label">First Name</label>
            <input type="text" name="first_name" value="" class="form-controll" required>
        </div>
        <div class="form-group">
            <label class="label">Last Name</label>
            <input type="text" name="last_name" value="" class="form-controll" required>
        </div>
        <div class="form-group">
            <label class="label">Email</label>
            <input type="email" name="email" value="" class="form-controll" required>
        </div>
        <div class="form-group">
            <label class="label">Phone</label>
            <input type="text" name="phone" value="" class="form-controll" required>
        </div>
        <div class="form-group">
            <label class="label">Select vehicle type</label>
            <select name="vehicle_type" id="vehicle_type" style="width: 100%;padding: 15px;border: 1px solid #ccc;;font-size: 14px;" required>
                <option value="">Select vehicle type</option>
                <?php
                    if($vehicle_type_list) {
                        foreach ($vehicle_type_list as $list){ ?>
                            <option value="<?php echo $list->term_id; ?>"><?php echo $list->name; ?></option>
                        <?php }
                    }
                ?>

            </select>
        </div>
        <div class="form-group">
            <label class="label">Select vehicle</label>
            <select name="vehicle" id="vehicle" style="width: 100%;padding: 15px;border: 1px solid #ccc;;font-size: 14px;" required>
                <option value="">Select vehicle</option>

            </select>
        </div>

        <div class="form-group">
            <label class="label">Message</label>
            <textarea name="message"></textarea>
        </div>

        <div class="form-group">
            <input type="submit" value="Submit Booking">
        </div>
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce("vehicle_listing_nonce"); ?>">
        <input type="hidden" name="submit_booking" value="<?php echo wp_create_nonce("vehicle_listing_nonce"); ?>">
        <input type="hidden" name="redirect_url" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
    </form>
</div>
<script
    src="https://code.jquery.com/jquery-3.5.1.js"
    integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
    crossorigin="anonymous"></script>


<script type="text/javascript">
    $(document).ready(function () {
        $("#vehicle_type").change(function () {
            var term_id     =   $(this).val();
            var nonce       =   '<?php echo wp_create_nonce("vehicle_listing_nonce"); ?>'

            jQuery.ajax({
                type : "post",
                dataType : "json",
                url : '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                data : {action: "get_vehicle_list", term_id : term_id, nonce: nonce},
                success: function(response) {
                    if(response.type=='success'){
                        $("#vehicle").html(response.html);
                    }else{
                        alert('Please select vehicle type again');
                    }

                }
            })
        })
    })
</script>