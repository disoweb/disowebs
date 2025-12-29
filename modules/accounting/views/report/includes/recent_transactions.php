<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head();?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="panel_s">
        <div class="panel-body">
          <h4 class="no-margin font-bold"><?php echo _l($title); ?></h4>
          <a href="<?php echo admin_url('accounting/report'); ?>"><?php echo _l('back_to_report_list'); ?></a>
          <hr />
          <div class="mbot15" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <button type="button" class="btn btn-default" data-toggle="collapse" data-target="#recent-transactions-filters" style="border: 1px solid #ccc;">
              <i class="fa fa-filter"></i> <?php echo _l('filter'); ?>
            </button>
            <div class="btn-group">
               <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border: 1px solid #ccc;"><i class="fa fa-print"></i> Export <span class="caret"></span></a>
               <ul class="dropdown-menu dropdown-menu-right">
                  <li>
                     <a href="#" onclick="printDiv(); return false;">
                     <?php echo _l('export_to_pdf'); ?>
                     </a>
                  </li>
                  <li>
                     <a href="#" onclick="printExcel(); return false;">
                     <?php echo _l('export_to_excel'); ?>
                     </a>
                  </li>
               </ul>
            </div>
          </div>
          <div class="collapse" id="recent-transactions-filters">
            <div class="row">
              <?php echo form_open(admin_url('accounting/view_report'),array('id'=>'filter-form')); ?>
                <div class="col-md-3 col-xs-6">
                  <?php echo render_date_input('from_date','from_date', _d($from_date)); ?>
                </div>
                <div class="col-md-3 col-xs-6">
                  <?php echo render_date_input('to_date','to_date', _d($to_date)); ?>
                </div>
                <div class="col-md-2 col-xs-12">
                  <?php echo form_hidden('type', 'recent_transactions'); ?>
                  <button type="submit" class="btn btn-info btn-submit mtop25"><?php echo _l('apply'); ?></button>
                </div>
              <?php echo form_close(); ?>
            </div>
          </div>
          <div class="row"> 
            <div class="col-md-12"> 
              <hr>
            </div>
          </div>
          <div class="page-size2" id="DivIdToPrint">
            
        </div>
      </div>
    </div>
  </div>
</div>
<!-- box loading -->
<div id="box-loading"></div>
<?php init_tail(); ?>
</body>
</html>
