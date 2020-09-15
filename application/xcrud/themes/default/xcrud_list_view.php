    <?php echo $this->render_table_name(); ?>
    <div class="xcrud-list-container">
        <table class="xcrud-list table table-striped table-hover table-bordered">
            <thead>
                <?php echo $this->render_grid_head('tr', 'th'); ?>
            </thead>
            <tbody>
                <?php echo $this->render_grid_body('tr', 'td'); ?>
                <?php if($this->table == "requisition_details") { ?>
                    <?php $total_price=0; $total_previous_price=0; foreach($this->result_list as $lst) { $total_price += intval($lst['requisition_details.price']); $total_previous_price += intval($lst['requisition_details.previous_price']);  } ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><h5><b>Total</b></h5></td>
                        <td><h5><?= $total_price; ?></h5></td>
                        <td><h5><?= $total_previous_price; ?></h5></td>
                    </tr>
                <?php } ?>
            </tbody>
            <tfoot>
                <?php echo $this->render_grid_footer('tr', 'td'); ?>
            </tfoot>
        </table>
    </div>
        <div class="xcrud-nav">
            <?php echo $this->add_button('xcrud-button xcrud-green','icon-plus');
            echo $this->csv_button('xcrud-button xcrud-purple','icon-file');
            echo $this->print_button('xcrud-button xcrud-pink','icon-print'); 
            echo $this->render_search(); ?>
            <div style="float: right;">
            <?php echo $this->render_benchmark(); ?>
            </div>
            <br />
            <?php echo $this->render_limitlist(true); ?>
            <div style="float: right;">
            <?php echo $this->render_pagination(); ?>
            </div>
            
        </div>
