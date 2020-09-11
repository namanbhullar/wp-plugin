<?php
    $vehicle_type_list  =   get_terms(['taxonomy'=>'vehicle-category']);



    $post = get_post($_REQUEST['id']);
    $post_meta = get_post_meta($post->ID);

$args = array(
    'post_type' => 'vehicle',
    'tax_query' => array(
        array(
            'taxonomy' => 'vehicle-category',
            'field'    => 'term_id',
            'terms'    => $post_meta['vehicle_type'][0]
        )
    )
);

$query = get_posts($args);
    ?>

<style>
    .form-group{
        margin-bottom: 20px;
    }

    .label{
        width: 200px;
        display: inline-block;
    }
</style>
<div class="wrap">
    <h1 class="wp-heading-inline"> Edit Vehicle Bookings</h1>
    <form action="" method="post" style="margin-top: 50px;">
        <div class="form-group">
            <label class="label">First Name</label>
            <input type="text" name="first_name" value="<?php echo $post_meta['first_name'][0] ;?>" class="form-controll" required>
        </div>
        <div class="form-group">
            <label class="label">Last Name</label>
            <input type="text" name="last_name" value="<?php echo $post_meta['last_name'][0] ;?>" class="form-controll" required>
        </div>
        <div class="form-group">
            <label class="label">Email</label>
            <input type="email" name="email" value="<?php echo $post_meta['email'][0] ;?>" class="form-controll" required>
        </div>
        <div class="form-group">
            <label class="label">Phone</label>
            <input type="text" name="phone" value="<?php echo $post_meta['phone'][0] ;?>" class="form-controll" required>
        </div>
        <div class="form-group">
            <label class="label">Select vehicle type</label>
            <select name="vehicle_type" id="vehicle_type" style="width: 100%;padding: 5px;border: 1px solid #ccc;;font-size: 14px;" required>
                <option value="">Select vehicle type</option>
                <?php
                    if($vehicle_type_list) {
                        foreach ($vehicle_type_list as $list){
                            $selected = '';
                            if($post_meta['vehicle_type'][0]==$list->term_id)
                                $selected = 'selected';
                            ?>
                            <option <?php echo $selected; ?> value="<?php echo $list->term_id; ?>"><?php echo $list->name; ?></option>
                        <?php }
                    }
                ?>

            </select>
        </div>
        <div class="form-group">
            <label class="label">Select vehicle</label>
            <select name="vehicle" id="vehicle" style="width: 100%;padding: 5px;border: 1px solid #ccc;;font-size: 14px;" required>
                <option value="">Select vehicle</option>
                <?php
                if($query) {
                    foreach ($query as $list){
                        $price  =   get_post_meta($list->ID,'vehicle_price',true);
                        $selected = '';
                        if($post_meta['vehicle'][0]==$list->ID)
                            $selected = 'selected';
                        ?>
                        <option <?php echo $selected; ?> value="<?php echo $list->ID; ?>"><?php echo $list->post_title .' - '. $price; ?></option>
                    <?php }
                }
                ?>

            </select>
        </div>

        <div class="form-group">
            <label class="label">Message</label>
            <textarea name="message"><?php echo $post->post_content ;?></textarea>
        </div>
        <div class="form-group">
            <label class="label">Status</label>
            <select name="post_status" id="status" style="width: 100%;padding: 5px;border: 1px solid #ccc;;font-size: 14px;" required>
                <option value="pending">Pending</option>
                <option value="approved">Approve</option>
                <option value="rejected">Reject</option>

            </select>
        </div>

        <div class="form-group">
            <input type="submit" value="Update Booking">
        </div>
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce("vehicle_listing_nonce"); ?>">
        <input type="hidden" name="update_booking" value="<?php echo wp_create_nonce("vehicle_listing_nonce"); ?>">
        <input type="hidden" name="post_id" value="<?php echo $post->ID; ?>">
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