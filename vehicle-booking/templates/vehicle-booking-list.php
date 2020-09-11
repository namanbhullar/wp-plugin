<?php
$args = array(
    'post_type' => 'vehicle_bookings',
    'post_status' =>    'any'
);

$query = get_posts($args);


?>

<div class="wrap">
    <h1 class="wp-heading-inline"> Vehicle Bookings</h1>
    <table class="wp-list-table widefat fixed striped table-view-list posts">
        <thead>
            <th>ID</th>
            <th>Name</th>
            <th>Vehicle</th>
            <th>Category</th>
            <th>Date</th>
            <th>Status</th>
        </thead>
        <tbody>
            <?php if($query){ ?>
                <?php foreach ($query as $booking){ ?>
                    <tr>
                        <td><a href="<?php echo admin_url('edit.php?post_type=vehicle&page=vehicle-booking&type=edit&id='.$booking->ID); ?>"><?php echo $booking->ID; ?></a></td>
                        <td><a href="<?php echo admin_url('edit.php?post_type=vehicle&page=vehicle-booking&type=edit&id='.$booking->ID); ?>"><?php echo $booking->post_title; ?></a></td>
                        <td><?php echo $booking->post_title; ?></td>
                        <td><?php echo $booking->post_title; ?></td>
                        <td><?php echo $booking->post_date; ?></td>
                        <td><?php echo ucfirst($booking->post_status); ?></td>
                    </tr>
                 <?php } ?>
            <?php } ?>
        </tbody>
    </table>
</div>
