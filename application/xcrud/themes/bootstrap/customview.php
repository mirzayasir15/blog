<?php echo $this->render_table_name($mode); ?>
<div class="xcrud-top-actions">
    <?php echo $this->render_button('','list','','xcrud-button xcrud-orange btn btn-warning text-white','fa fa-reply') ?>
    <?php //echo $this->render_button('return','list','','xcrud-button xcrud-orange btn btn-warning') ?>
</div>
<div class="xcrud-view row">
    
<?php
    foreach ($this->fields_output as $data){
        if($data['value']){
?>
    <br>
        <div class="col-md-6">
            <label><?php echo $data['label'] ?></label>
            <?php if($data['label'] == "Is Active*" || $data["field"] == NULL) { ?>
                <p><?php echo "<i class='fa fa-close'></i>" ?></p>
            <?php } else { ?>
            
            <p><?php echo $data['field'] ?></p>
            <?php } ?>
        </div>
<?php
        }
    }
?>
    
</div>