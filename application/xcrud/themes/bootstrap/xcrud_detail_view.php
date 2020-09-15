<?php echo $this->render_table_name($mode); ?>
<div class="xcrud-top-actions btn-group">
   <?php
   echo $this->render_button('save_return','save','list','btn btn-info text-white','','create,edit','','font-size:12px; padding:10px 5px;');
   //echo $this->render_button('save_new','save','create','btn btn-default','','create,edit','','font-size:12px;padding:10px 5px;');
   echo $this->render_button('save_edit','save','edit','btn btn-default','','create,edit','','font-size:12px;padding:10px 5px;');
   echo $this->render_button('','list','','btn btn-warning text-white','fa fa-reply','','','padding:10px 5px;'); ?>
</div>
<!--<div class="xcrud-top-actions btn-group">
    <?php 
    //echo $this->render_button('save_return','save','list','btn btn-info text-white','','create,edit');
    //echo $this->render_button('save_new','save','create','btn btn-default','','create,edit');
    //echo $this->render_button('save_edit','save','edit','btn btn-default','','create,edit');
    //echo $this->render_button('return','list','','btn btn-warning'); ?>
</div>-->
<div class="xcrud-view row">
<?php echo $mode == 'view' ? $this->render_fields_list($mode,array('tag'=>'table','class'=>'table')) : $this->render_fields_list($mode,'div','div','label','div'); ?>
</div>
<div class="xcrud-nav">
    <?php echo $this->render_benchmark(); ?>
</div>
