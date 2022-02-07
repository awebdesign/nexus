<?php echo $header; ?>
<?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <h1>Nexus <?php echo $current_version; ?></h1>
        </div>
    </div>
    <div class="container-fluid">
        <?php _alerts('success', $succces); ?>
        <?php _alerts('warning', $warning); ?>
        <?php _alerts('errors'); ?>
    </div>
</div>
<?php echo $footer; ?>
