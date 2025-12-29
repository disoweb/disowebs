<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <!-- Desktop Sidebar Tabs -->
      <div class="col-md-3 hidden-xs hidden-sm">
        <ul class="nav navbar-pills navbar-pills-flat nav-tabs nav-stacked customer-tabs" role="tablist">
          <?php
          foreach($tab as $key => $gr){
            ?>
            <li class="<?php if($key == 0){echo 'active ';} ?>transaction_tab_<?php echo html_entity_decode($key); ?>">
              <a data-group="<?php echo html_entity_decode($gr); ?>" href="<?php echo admin_url('accounting/transaction?group='.$gr); ?>">
                <?php if ($gr == 'banking') {
                    echo '<i class="fa fa-university" aria-hidden="true"></i>';
                }elseif ($gr == 'sales') {
                    echo '<i class="fa fa-line-chart" aria-hidden="true"></i>';
                }elseif ($gr == 'expenses') {
                    echo '<i class="fa fa-file-text-o" aria-hidden="true"></i>';
                } ?>
                <?php echo _l($gr); ?>
              </a>
            </li>
          <?php } ?>
        </ul>
      </div>
      
      <!-- Mobile Horizontal Tabs with Filter -->
      <div class="col-xs-12 visible-xs visible-sm mobile-transaction-tabs">
        <div class="transaction-tabs-row">
          <?php foreach($tab as $key => $gr): ?>
            <a href="<?php echo admin_url('accounting/transaction?group='.$gr); ?>" 
               class="tab-item <?php if($group == $gr) echo 'active'; ?>">
              <?php if ($gr == 'banking'): ?>
                <i class="fa fa-university"></i>
              <?php elseif ($gr == 'sales'): ?>
                <i class="fa fa-line-chart"></i>
              <?php elseif ($gr == 'expenses'): ?>
                <i class="fa fa-file-text-o"></i>
              <?php endif; ?>
              <span><?php echo _l($gr); ?></span>
            </a>
          <?php endforeach; ?>
          <button type="button" class="tab-item filter-btn" data-toggle="collapse" data-target=".mobile-filters-collapse">
            <i class="fa fa-filter"></i>
            <span><?php echo _l('filter_by'); ?></span>
          </button>
        </div>
      </div>
      
      <div class="col-md-9 col-xs-12">
        <div class="panel_s">
           <div class="panel-body">
              <div>
                 <div class="tab-content">
                    <?php $this->load->view($tabs['view']); ?>
                 </div>
              </div>
           </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php init_tail(); ?>
